<?php

namespace App\Http\Controllers;

use App\Models\PersonnelItem;
use App\Models\Personnel;
use App\Models\Item;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
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

        $outbounds = PersonnelItem::with(['personnel', 'personnel.branch', 'item'])
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
                $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_id', $branchFilter));
            })
            ->when($remarksFilter, fn($q) => $q->where('personnel_item_remarks', $remarksFilter))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        // 📋 Dropdown Data
        $personnels = Personnel::orderBy('personnel_name')->get();

        // departments column
        $departments = Branch::select('branch_department')
            ->distinct()
            ->pluck('branch_department');

        // If you have branches table
        $branches = Branch::orderBy('branch_name')->get();


        $item_remarks = PersonnelItem::select('personnel_item_remarks')
            ->distinct()
            ->pluck('personnel_item_remarks');

        $items = Item::all();

        // ⚡ AJAX TABLE REFRESH
        if ($request->ajax()) {
            return view('personnel.outbound-table', compact(
                'outbounds',
                'personnels'
            ))->render();
        }

        // EXPORT SINGLE ITEM PDF
        if ($request->get('export') == 'pdf' && $request->has('personnel_item_id')) {
            $itemId = $request->get('personnel_item_id');
            $outbound = PersonnelItem::with(['personnel', 'personnel.branch', 'item'])->findOrFail($itemId);
            $pdf = Pdf::loadView('personnel.pdf-individual', compact('outbound'));
            return $pdf->stream("outbound_{$outbound->personnel_item_id}.pdf");
        }

        // EXPORT EXCEL
        if ($request->get('export') == 'excel') {
            $ids = $request->get('ids');
            $dbQuery = PersonnelItem::with(['personnel', 'personnel.branch', 'item']);

            if ($ids) {
                $idArray = explode(',', $ids);
                $dbQuery->whereIn('personnel_item_id', $idArray);
            } else {
                $dbQuery->when($search, function ($q) use ($search) {
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
                        $q->whereHas('personnel.branch', fn($q2) => $q2->where('branch_id', $branchFilter));
                    })
                    ->when($remarksFilter, fn($q) => $q->where('personnel_item_remarks', $remarksFilter));
            }

            $filteredOutbounds = $dbQuery->latest()->get();
            return Excel::download(new PersonnelItemsExport($filteredOutbounds), 'outbound.xlsx');
        }

        // for the entire pdf
        if ($request->query('pdf')) {
            $pdf = Pdf::loadView('personnel.all-outbound', compact(
                'outbounds',
                'personnels',
                'items',
                'departments',
                'branches',
                'item_remarks'
            ))->setPaper('a4', 'portrait');

            // $pdf->output();
            // $domPdf = $pdf->getDomPDF();
            // $canvas = $domPdf->getCanvas();

            // $canvas->page_script(function ($pageNumber, $pageCount, $canvas, $fontMetrics) {
            //     $font = $fontMetrics->getFont("Helvetica", "normal");
            //     $size = 10;

            //     if ($pageNumber === $pageCount) {
            //         $footerText = "Generated by Malnutrition Profiling and Monitoring System " . date('F d, Y');
            //         $footerWidth = $fontMetrics->getTextWidth($footerText, $font, $size);
            //         $xFooter = ($canvas->get_width() - $footerWidth) / 2;
            //         $yFooter = $canvas->get_height() - (2.5 * 12);
            //         $canvas->text($xFooter, $yFooter, $footerText, $font, $size);
            //     }

            //     // Page number at bottom-right
            //     $pageText = "Page $pageNumber";
            //     $pageWidth = $fontMetrics->getTextWidth($pageText, $font, $size);
            //     $xPage = $canvas->get_width() - $pageWidth - (2 * 12);
            //     $yPage = $canvas->get_height() - (2.5 * 12); // same vertical as footer
            //     $canvas->text($xPage, $yPage, $pageText, $font, $size);
            // });

            if ($request->query('action') === 'download') {
                return $pdf->download('personnel.all-outbound');
            }

            return $pdf->stream('personnel.all-outbound');
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
            'personnel_name' => 'required|string|max:255', // ✅ allow duplicates if you want
        ]);

        // ✅ Reuse or create branch
        $branch = Branch::firstOrCreate([
            'branch_name' => $request->branch_name,
            'branch_department' => $request->branch_department
        ]);

        // ✅ ALWAYS create new personnel (reuse branch_id only)
        Personnel::create([
            'branch_id' => $branch->branch_id, // ⚠️ IMPORTANT (not ->id)
            'personnel_name' => $request->personnel_name,
        ]);

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
        $validated = $request->validate([
            'personnel_id' => 'required|exists:personnels,personnel_id',
            'personnel_item_quantity' => 'required|integer|min:1',
            'personnel_item_receive' => 'required|date',
            'personnel_item_remarks' => 'required|string',
        ]);

        DB::transaction(function () use ($validated, $outbound) {
            $outbound->update([
                'personnel_id' => $validated['personnel_id'],
                'personnel_item_quantity' => $validated['personnel_item_quantity'],
                'personnel_date_receive' => $validated['personnel_item_receive'],
                'personnel_item_remarks' => $validated['personnel_item_remarks'],
            ]);
        });

        return redirect()->route('outbound.index')->with('success', 'Outbound updated successfully.');
    }
    /**
     * Separate return function
     */
    public function returnItem(Request $request, PersonnelItem $outbound)
    {
        $validated = $request->validate([
            'return_quantity' => 'required|integer|min:1|max:' . $outbound->personnel_item_quantity,
            'return_condition' => 'required|string|in:Good,Damaged',
            'return_date' => 'required|date',
        ]);

        DB::transaction(function () use ($outbound, $validated) {

            $item = $outbound->item;

            // Reduce borrowed quantity
            $outbound->personnel_item_quantity -= $validated['return_quantity'];
            $outbound->save();

            if ($validated['return_condition'] === 'Good') {

                // ✅ Add back to remaining only
                $item->item_quantity_remaining += $validated['return_quantity'];

                $item->item_quantity_status =
                    $item->item_quantity_remaining <= 0 ? 'Out of Stock' :
                    ($item->item_quantity_remaining < ($item->item_quantity * 0.2) ? 'Low Stock' : 'Available');

                $item->save();

                // Record return
                PersonnelItem::create([
                    'personnel_id' => $outbound->personnel_id,
                    'item_id' => $item->item_id,
                    'personnel_item_quantity' => $validated['return_quantity'],
                    'personnel_date_receive' => $validated['return_date'],
                    'personnel_item_remarks' => 'Returned',
                    'item_remark' => 'Good',
                ]);

            } else {

                $item->save();

                // ✅ Find existing damaged item (MERGE)
                $existingDamaged = Item::where('item_name', $item->item_name)
                    ->where('item_remark', 'Damaged')
                    ->first();

                if ($existingDamaged) {

                    // ✅ ALWAYS ZERO total quantity
                    $existingDamaged->item_quantity = 0;

                    // ✅ Accumulate damaged count in remaining
                    $existingDamaged->item_quantity_remaining += $validated['return_quantity'];

                    $existingDamaged->item_quantity_status = 'Damaged';
                    $existingDamaged->save();

                    $damagedItemId = $existingDamaged->item_id;

                } else {

                    // ✅ Create damaged item once
                    $damagedItem = $item->replicate();

                    $damagedItem->item_quantity = 0; // 🔥 always zero
                    $damagedItem->item_quantity_remaining = $validated['return_quantity'];
                    $damagedItem->item_quantity_status = 'Damaged';
                    $damagedItem->item_remark = 'Damaged';

                    $damagedItem->save();

                    $damagedItemId = $damagedItem->item_id;
                }

                // Record damaged return
                PersonnelItem::create([
                    'personnel_id' => $outbound->personnel_id,
                    'item_id' => $damagedItemId,
                    'personnel_item_quantity' => $validated['return_quantity'],
                    'personnel_date_receive' => $validated['return_date'],
                    'personnel_item_remarks' => 'Returned',
                    'item_remark' => 'Damaged',
                ]);
            }
        });

        return redirect()->route('outbound.index')->with('success', 'Item returned successfully.');
    }
    /**
     * Remove the specified borrow record.
     */
    public function destroy($personnel_item_id)
    {
        PersonnelItem::findOrFail($personnel_item_id)->delete();

        return redirect()->back()->with('success', 'Item deleted successfully');
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
}