<?php

namespace App\Http\Controllers;

use App\Models\PersonnelItem;
use App\Models\Personnel;
use App\Models\Item;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PersonnelItemsExport;

class PersonnelItemController extends Controller
{
    /**
     * Display a listing of  items.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $personnelFilter = $request->get('personnel');
        $departmentFilter = $request->get('department');
        $branchFilter = $request->get('branch');
        $remarksFilter = $request->get('remarks');

        // 1. Main Table Query: Only show rows where quantity > 0
        $outbounds = PersonnelItem::with(['personnel', 'personnel.branch', 'item'])
            ->where('personnel_item_quantity', '>', 0)
            ->when($search, function ($q) use ($search) {
                $q->where(function ($sub) use ($search) {
                    $sub->whereHas('item', fn($q2) => $q2->where('item_name', 'like', "%{$search}%"))
                        ->orWhereHas('personnel', fn($q2) => $q2->where('personnel_name', 'like', "%{$search}%"));
                });
            })
            ->when($personnelFilter, fn($q) => $q->where('personnel_id', $personnelFilter))
            ->when($departmentFilter, function ($q) use ($departmentFilter) {
                $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_department', $departmentFilter));
            })
            ->when($branchFilter, function ($q) use ($branchFilter) {
                $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_name', $branchFilter));
            })
            ->when($remarksFilter, fn($q) => $q->where('personnel_item_remarks', $remarksFilter))
            // 🔥 ORDERING FOR WEB VIEW:
            ->orderBy('updated_at', 'desc')           // 1. Most recently touched
            ->orderBy('personnel_item_id', 'desc')    // 2. Highest ID wins (Tie-breaker for Returns)
            ->paginate(5)
            ->withQueryString();

        // 2. Dropdown Data: Shows ALL personnel regardless of quantity
        // This allows you to still select them to create new records
        $personnels = Personnel::orderBy('personnel_name')->get();

        // 3. Departments column
        $departments = Branch::select('branch_department')
            ->distinct()
            ->pluck('branch_department');

        // 4. Branches
        $branches = Branch::select('branch_name')
            ->distinct()
            ->orderBy('branch_name')
            ->pluck('branch_name');

        $item_remarks = PersonnelItem::select('personnel_item_remarks')
            ->distinct()
            ->pluck('personnel_item_remarks');

        $items = Item::all();

        // EXPORT PDF
        if ($request->get('export') == 'pdf') {
            $ids = $request->get('ids');
            $dbQuery = PersonnelItem::with(['personnel', 'personnel.branch', 'item'])
                ->where('personnel_item_quantity', '>', 0);

            if ($ids) {
                $idArray = array_filter(explode(',', $ids));
                $dbQuery->whereIn('personnel_item_id', $idArray);
            } else {
                $dbQuery
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->whereHas('item', fn($q2) => $q2->where('item_name', 'like', "%{$search}%"))
                                ->orWhereHas('personnel', fn($q2) => $q2->where('personnel_name', 'like', "%{$search}%"));
                        });
                    })
                    ->when($personnelFilter, fn($q) => $q->where('personnel_id', $personnelFilter))
                    ->when($departmentFilter, function ($q) use ($departmentFilter) {
                        $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_department', $departmentFilter));
                    })
                    ->when($branchFilter, function ($q) use ($branchFilter) {
                        $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_name', $branchFilter));
                    })
                    ->when($remarksFilter, fn($q) => $q->where('personnel_item_remarks', $remarksFilter));
            }

            // 🔥 SORT FOR PDF: Group by Branch -> Dept -> Updated At
            $outbounds = $dbQuery->get()->sortBy([
                ['personnel.branch.branch_name', 'asc'],
                ['personnel.branch.branch_department', 'asc'],
                ['updated_at', 'desc']
            ])->values();

            $pdf = Pdf::loadView('personnel.pdf-outbound', compact('outbounds'));

            // 1. Render the PDF first so the canvas is fully initialized
            $pdf->render();

            // 2. Get the canvas instance from DomPDF
            $canvas = $pdf->getDomPDF()->getCanvas();

            // 3. Pass the code as a string. DomPDF natively exposes $pdf, $PAGE_NUM, $PAGE_COUNT, and $fontMetrics here.
            $script = <<<'EOT'
        $font = $fontMetrics->get_font("Helvetica", "normal");
        $size = 10;

        if ($PAGE_NUM === $PAGE_COUNT) {
            $footerText = "Generated by Goldtown Inventory System " . date('F d, Y');
            $footerWidth = $fontMetrics->getTextWidth($footerText, $font, $size);
            $xFooter = ($pdf->get_width() - $footerWidth) / 2;
            $yFooter = $pdf->get_height() - (2.5 * 12);
            $pdf->text($xFooter, $yFooter, $footerText, $font, $size);
        }

        // Page number at bottom-right
        $pageText = "Page " . $PAGE_NUM;
        $pageWidth = $fontMetrics->getTextWidth($pageText, $font, $size);
        $xPage = $pdf->get_width() - $pageWidth - (2 * 12);
        $yPage = $pdf->get_height() - (2.5 * 12);
        $pdf->text($xPage, $yPage, $pageText, $font, $size);
EOT;

            $canvas->page_script($script);

            return $pdf->stream('outbound.pdf');
        }

        // ⚡ AJAX TABLE REFRESH
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'table' => view('personnel.outbound-table', compact('outbounds', 'personnels'))->render()
            ]);
        }

        // EXPORT EXCEL
        if ($request->get('export') == 'excel') {
            $ids = $request->get('ids');
            $dbQuery = PersonnelItem::with(['personnel', 'personnel.branch', 'item'])
                ->where('personnel_item_quantity', '>', 0);

            if ($ids) {
                $idArray = explode(',', $ids);
                $dbQuery->whereIn('personnel_item_id', $idArray);
            } else {
                $dbQuery
                    ->when($search, function ($q) use ($search) {
                        $q->where(function ($sub) use ($search) {
                            $sub->whereHas('item', fn($q2) => $q2->where('item_name', 'like', "%{$search}%"))
                                ->orWhereHas('personnel', fn($q2) => $q2->where('personnel_name', 'like', "%{$search}%"));
                        });
                    })
                    ->when($personnelFilter, fn($q) => $q->where('personnel_id', $personnelFilter))
                    ->when($departmentFilter, function ($q) use ($departmentFilter) {
                        $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_department', $departmentFilter));
                    })
                    ->when($branchFilter, function ($q) use ($branchFilter) {
                        $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_name', $branchFilter));
                    })
                    ->when($remarksFilter, fn($q) => $q->where('personnel_item_remarks', $remarksFilter));
            }

            // 🔥 SORT FOR EXCEL: Group by Branch -> Dept -> Updated At
            $filteredOutbounds = $dbQuery->get()->sortBy([
                ['personnel.branch.branch_name', 'asc'],
                ['personnel.branch.branch_department', 'asc'],
                ['updated_at', 'desc']
            ])->values();

            return Excel::download(new PersonnelItemsExport($filteredOutbounds), 'outbound.xlsx');
        }

        return view('personnel.index', compact(
            'outbounds',
            'personnels',
            'items',
            'departments',
            'branches',
            'item_remarks'
        ));
    }
    /**
     * Show the form for creating a new borrow record.
     */
    public function create()
    {

    }

    /**
     * Store a newly created borrow record.
     */
    public function store(Request $request)
    {
        $request->validate([
            'personnel_id' => 'required|exists:personnels,personnel_id',
            'item_id' => 'required|exists:items,item_id',
            'personnel_item_quantity' => 'required|integer|min:1',
            'personnel_date_receive' => 'nullable|date',
            'personnel_date_issued' => 'nullable|date',
            'personnel_item_remarks' => 'required|string|max:500',
        ]);

        // Use a transaction to ensure stock consistency
        \DB::transaction(function () use ($request) {
            $item = Item::findOrFail($request->item_id);

            // Check if enough remaining stock exists
            if ($item->item_quantity_remaining < $request->personnel_item_quantity) {
                throw new \Exception('Not enough remaining stock available.');
            }

            // Decrement remaining quantity
            $item->item_quantity_remaining -= $request->personnel_item_quantity;

            // Update quantity status
            if ($item->item_quantity_remaining == 0) {
                $item->item_quantity_status = 'Out of Stock';
            } elseif ($item->item_quantity_remaining < ($item->item_quantity_total * 0.2)) {
                $item->item_quantity_status = 'Low Stock';
            } else {
                $item->item_quantity_status = 'Available';
            }

            $item->save();

            // Record outbound
            PersonnelItem::create([
                'personnel_id' => $request->personnel_id,
                'item_id' => $request->item_id,
                'personnel_item_quantity' => $request->personnel_item_quantity,
                'personnel_date_receive' => $request->personnel_date_receive,
                'personnel_date_issued' => $request->personnel_date_issued,
                'personnel_item_remarks' => $request->personnel_item_remarks,
            ]);
        });

        return redirect()->route('outbound.index')
            ->with('success', 'Item outbound recorded successfully!');
    }

    public function storePersonnel(Request $request)
    {
        $request->validate([
            'branch_name' => 'required|string|max:255',
            'branch_department' => 'required|string|max:255',
            'personnel_name' => 'required|string|max:255',
        ]);

        // 1. Reuse or create branch
        $branch = Branch::firstOrCreate([
            'branch_name' => $request->branch_name,
            'branch_department' => $request->branch_department
        ]);

        // 2. Create the personnel
        $personnel = Personnel::create([
            'branch_id' => $branch->branch_id,
            'personnel_name' => $request->personnel_name,
        ]);

        // 3. Load the branch relationship for the AJAX response
        $personnel->load('branch');

        // 4. Check if the request is AJAX
        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'personnel' => $personnel,
                'message' => 'Personnel added successfully'
            ]);
        }

        // Fallback for standard form submission
        return redirect()->back()->with('success', 'Personnel added successfully');
    }
    /**
     * Display the specified borrow record.
     */
    public function show(PersonnelItem $personnelItem)
    {
        $personnelItem->load(['personnel', 'item']);
        return view('personnel_items.show', compact('personnelItem'));
    }

    /**
     * Show the form for editing the specified borrow record.
     */
    public function edit(PersonnelItem $personnelItem)
    {
        // $personnelItem->load(['personnel', 'item']);
        // $personnels = Personnel::all();
        // $items = Item::all();

        // return view('personnel_items.edit', compact('personnelItem', 'personnels', 'items'));
    }

    /**
     * Update the specified borrow record.
     */
    public function update(Request $request, PersonnelItem $outbound)
    {
        // 1. Calculate the absolute max they can request:
        // What they already hold + what is currently left in stock
        $item = $outbound->item;
        $remainingStock = $item->item_quantity_remaining ?? 0;
        $maxAllowed = $outbound->personnel_item_quantity + $remainingStock;

        // 2. Validate with the dynamic max limit
        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,personnel_id',
            'personnel_item_quantity' => 'required|integer|min:1|max:' . $maxAllowed,
            'personnel_date_issued' => 'required|date',
            'personnel_item_receive' => 'required_if:personnel_item_remarks,Received|nullable|date',
            'personnel_item_remarks' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $outbound, $item) {

            // 3. Handle Inventory Stock Adjustment
            $newQuantity = $validated['personnel_item_quantity'];
            $oldQuantity = $outbound->personnel_item_quantity;

            if ($newQuantity != $oldQuantity) {
                // Calculate the difference. 
                // If they increased the amount, subtract from main inventory.
                // If they decreased the amount, add back to main inventory.
                $qtyDifference = $newQuantity - $oldQuantity;

                $item->item_quantity_remaining -= $qtyDifference;

                // Keep the stock status accurate
                $item->item_quantity_status = $item->item_quantity_remaining <= 0 ? 'Out of Stock' :
                    ($item->item_quantity_remaining < ($item->item_quantity * 0.2) ? 'Low Stock' : 'Available');

                $item->save();
            }

            // 4. Update the outbound record
            $outbound->update([
                'personnel_id' => $validated['personnel_id'],
                'personnel_item_quantity' => $newQuantity,
                'personnel_date_issued' => $validated['personnel_date_issued'],
                // Use null if it wasn't provided (not received yet)
                'personnel_date_receive' => $validated['personnel_item_receive'] ?? $outbound->personnel_date_receive,
                'personnel_item_remarks' => $validated['personnel_item_remarks'],
            ]);
        });

        return response()->json([
            'success' => true,
            'message' => 'Outbound record updated successfully!'
        ]);
    }
    /**
     * Separate return function
     */
    public function returnItem(Request $request, PersonnelItem $outbound)
    {
        // 1. Validation Check (for non-AJAX fallback)
        if ($outbound->personnel_item_remarks !== 'Received') {
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Only RECEIVED items can be returned.'], 403);
            }
            return redirect()->back()->with('error', 'Only RECEIVED items can be returned.');
        }

        $goodReasonPresets = ['no_longer_needed', 'end_of_assignment', 'replaced_upgraded', 'transfer_reassign', 'other'];
        $damagedReasonPresets = [
            'physical_damage',
            'malfunction',
            'wear_unusable',
            'missing_accessories',
            'other',
        ];
        $allReasonPresets = array_merge($goodReasonPresets, $damagedReasonPresets);

        $validated = $request->validate([
            'return_quantity' => 'required|integer|min:1|max:' . $outbound->personnel_item_quantity,
            'return_condition' => 'required|string|in:Good,Damaged',
            'return_date' => 'required|date',
            'issued_date' => 'nullable|date',
            'return_reason_preset' => ['required', 'string', Rule::in($allReasonPresets)],
            'return_reason_detail' => 'nullable|string|max:2000',
        ]);

        $allowedForCondition = $validated['return_condition'] === 'Damaged' ? $damagedReasonPresets : $goodReasonPresets;
        if (!in_array($validated['return_reason_preset'], $allowedForCondition, true)) {
            throw ValidationException::withMessages([
                'return_reason_preset' => 'Pick a reason that matches Good or Damaged.',
            ]);
        }

        if (
            $validated['return_reason_preset'] === 'other'
            && trim((string) ($validated['return_reason_detail'] ?? '')) === ''
        ) {
            throw ValidationException::withMessages([
                'return_reason_detail' => 'Please describe the reason when you choose Other.',
            ]);
        }

        try {
            DB::transaction(function () use ($outbound, $validated) {
                $item = $outbound->item;

                // Reduce borrowed quantity on the original record
                $outbound->personnel_item_quantity -= $validated['return_quantity'];
                $outbound->save();

                if ($validated['return_condition'] === 'Good') {
                    // Return to stock
                    $item->item_quantity_remaining += $validated['return_quantity'];
                    $item->item_quantity_status =
                        $item->item_quantity_remaining <= 0 ? 'Out of Stock' :
                        ($item->item_quantity_remaining < ($item->item_quantity * 0.2) ? 'Low Stock' : 'Available');
                    $item->save();

                    $targetItemId = $item->item_id;
                    $remark = 'Good';
                } else {
                    // Handle Damaged Logic
                    $existingDamaged = Item::where('item_name', $item->item_name)
                        ->where('item_remark', 'Damaged')
                        ->first();

                    if ($existingDamaged) {
                        $existingDamaged->item_quantity_remaining += $validated['return_quantity'];
                        $existingDamaged->save();
                        $targetItemId = $existingDamaged->item_id;
                    } else {
                        $damagedItem = $item->replicate();
                        $damagedItem->item_quantity = 0;
                        $damagedItem->item_quantity_remaining = $validated['return_quantity'];
                        $damagedItem->item_quantity_status = 'Damaged';
                        $damagedItem->item_remark = 'Damaged';
                        $damagedItem->save();
                        $targetItemId = $damagedItem->item_id;
                    }
                    $remark = 'Damaged';
                }

                // Create the "Returned" record
                PersonnelItem::create([
                    'personnel_id' => $outbound->personnel_id,
                    'item_id' => $targetItemId,
                    'personnel_item_quantity' => $validated['return_quantity'],
                    'personnel_date_receive' => $validated['return_date'],

                    // 🔥 Check if issued_date is in the request; if null or missing, use the old one.
                    'personnel_date_issued' => $validated['issued_date'] ?? $outbound->personnel_date_issued,

                    'personnel_item_remarks' => 'Returned',
                    'item_remark' => $remark,
                    'return_reason_preset' => $validated['return_reason_preset'],
                    'return_reason_detail' => $validated['return_reason_detail'] ?? null,
                ]);
            });

            // 🔥 JSON Response for AJAX
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item returned successfully!'
                ]);
            }

            return redirect()->back()->with('success', 'Item returned successfully!');

        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Server Error: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'An error occurred during return.');
        }
    }
    /**
     * Remove the specified borrow record.
     */
    public function destroy($personnel_item_id)
    {
        try {
            $item = PersonnelItem::findOrFail($personnel_item_id);
            $item->delete();

            // Check if the request is an AJAX/Fetch request
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Item deleted successfully.'
                ]);
            }

            // Fallback for standard form submissions
            return redirect()->back()->with('success', 'Item deleted successfully');

        } catch (\Exception $e) {
            if (request()->ajax() || request()->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete item: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->with('error', 'Failed to delete item.');
        }
    }

    public function bulkPersonnelDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            PersonnelItem::whereIn('personnel_item_id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
    }
    public function destroyPersonnel($id)
    {
        $personnel = Personnel::find($id);

        if (!$personnel) {
            return response()->json([
                'success' => false,
                'message' => 'Personnel not found.'
            ]);
        }

        // ❗ prevent delete if has assigned items
        if (PersonnelItem::where('personnel_id', $id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete. Personnel has assigned items.'
            ]);
        }

        $personnel->delete();

        return response()->json([
            'success' => true
        ]);
    }

    public function getItems($id)
    {
        $items = PersonnelItem::with('item')
            ->where('personnel_id', $id)
            ->latest()
            ->get();

        return view('personnel.assigned-items', compact('items'));
    }
}

