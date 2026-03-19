<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUom;
use App\Models\ItemBrand;
use App\Models\PersonnelItem;
use App\Models\ItemCategory;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ItemsExport;
use Illuminate\Http\Request;

use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = $request->get('search', '');
        $remarkFilter = $request->get('remark');
        $categoryId = $request->get('category');
        $brandId = $request->get('brand');

        $item_categories = ItemCategory::all();
        $item_brands = ItemBrand::all();
        $item_uoms = ItemUom::all();

        // Base query for items
        $itemsQuery = Item::query();

        if ($query) {
            $itemsQuery->where(function ($q) use ($query) {
                $q->where('item_name', 'like', "%$query%")
                    ->orWhere('item_serialno', 'like', "%$query%");
            });
        }

        if ($remarkFilter) {
            $itemsQuery->where('item_remark', $remarkFilter);
        }

        if ($categoryId) {
            $itemsQuery->where('item_category_id', $categoryId);
        }

        if ($brandId) {
            $itemsQuery->where('item_brand_id', $brandId);
        }

        // Get regular items
        $items = $itemsQuery->orderBy('created_at', 'desc')->get();

        // Get returned items and integrate fully
        $returnedItems = PersonnelItem::where('personnel_item_remarks', 'Returned')
            ->with(['item', 'item.uom', 'item.category', 'item.brand'])
            ->get()
            ->map(function ($pi) {
                return (object) [
                    'item_id' => 'return-' . $pi->personnel_item_id,
                    'item_name' => $pi->item->item_name ?? 'N/A',
                    'item_category_id' => $pi->item->item_category_id ?? null,
                    'category' => $pi->item->category ?? null,
                    'brand' => $pi->item->brand ?? null,
                    'item_serialno' => $pi->item->item_serialno ?? '-',
                    'uom' => $pi->item->uom ?? null,
                    'item_quantity' => $pi->personnel_item_quantity,
                    'item_quantity_remaining' => $pi->personnel_item_quantity,
                    'item_remark' => $pi->personnel_item_remarks ?? 'Returned',
                    'created_at' => $pi->created_at,
                    'updated_at' => $pi->updated_at,
                ];
            })
            ->filter(function ($pi) use ($query, $remarkFilter, $categoryId, $brandId) {
                // Search filter
                if ($query) {
                    $match = str_contains(strtolower($pi->item_name), strtolower($query)) ||
                        str_contains(strtolower($pi->item_serialno), strtolower($query));
                    if (!$match)
                        return false;
                }

                // Remark filter (works for Returned and others)
                if ($remarkFilter && $pi->item_remark != $remarkFilter)
                    return false;

                // Category filter
                if ($categoryId && $pi->item_category_id != $categoryId)
                    return false;

                // Brand filter
                if ($brandId && $pi->brand?->item_brand_id != $brandId)
                    return false;

                return true;
            });

        // Merge regular items + returned items
        $allItems = $items->concat($returnedItems)->sortByDesc('created_at');

        // Paginate manually
        $perPage = 10;
        $page = $request->get('page', 1);
        $itemsPaginated = new \Illuminate\Pagination\LengthAwarePaginator(
            $allItems->forPage($page, $perPage),
            $allItems->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $itemsPaginated->appends([
            'search' => $query,
            'remark' => $remarkFilter,
            'category' => $categoryId,
            'brand' => $brandId
        ]);

        // Get unique remarks for dropdown
        $item_remarks = Item::select('item_remark')->distinct()->orderBy('item_remark')->pluck('item_remark')->toArray();

        $returned_remarks = PersonnelItem::where('personnel_item_remarks', 'Returned')
            ->pluck('personnel_item_remarks')
            ->unique()
            ->toArray();

        $item_remarks = array_unique(array_merge($item_remarks, $returned_remarks));

        // EXPORT SINGLE ITEM PDF
        if ($request->get('export') == 'pdf' && $request->has('item_id')) {
            $itemId = $request->get('item_id');
            if (str_starts_with($itemId, 'return-')) {
                $personnelItemId = str_replace('return-', '', $itemId);
                $pi = PersonnelItem::findOrFail($personnelItemId);
                $pdf = Pdf::loadView('inventory.pdf-individual-returned', compact('pi'));
                return $pdf->stream("returned_item_{$pi->personnel_item_id}.pdf");
            } else {
                $item = Item::findOrFail($itemId);
                $pdf = Pdf::loadView('inventory.pdf-individual', compact('item'));
                return $pdf->stream("item_{$item->item_id}.pdf");
            }
        }

        // EXPORT ALL ITEMS PDF
            if ($request->get('export') == 'pdf' && !$request->has('item_id')) {
            $pdf = Pdf::loadView('inventory.pdf-forall', ['allItems' => $allItems]);
            return $pdf->stream('inventory.pdf');
        }

        // AJAX Table Update
        if ($request->ajax() || $request->has('ajax')) {
            return view('inventory.inventory-table', [
                'items' => $itemsPaginated,
                'item_categories' => $item_categories,
                'item_brands' => $item_brands,
                'item_uoms' => $item_uoms
            ])->render();
        }

        // EXPORT EXCEL
        if ($request->get('export') == 'excel') {
            $ids = $request->get('ids');
            $dbQuery = Item::query();

            if ($ids) {
                $idArray = explode(',', $ids);
                $dbQuery->whereIn('item_id', $idArray);
            } else {
                if ($query) {
                    $dbQuery->where(function ($q) use ($query) {
                        $q->where('item_name', 'like', "%$query%")
                            ->orWhere('item_serialno', 'like', "%$query%");
                    });
                }
                if ($remarkFilter) {
                    $dbQuery->where('item_remark', $remarkFilter);
                }
                if ($categoryId) {
                    $dbQuery->where('item_category_id', $categoryId);
                }
                if ($brandId) {
                    $dbQuery->where('item_brand_id', $brandId);
                }
            }

            $filteredItems = $dbQuery->orderBy('created_at', 'desc')->get();
            return Excel::download(new ItemsExport($filteredItems), 'inventory.xlsx');
        }

        return view('inventory.index', [
            'items' => $itemsPaginated,
            'item_categories' => $item_categories,
            'item_brands' => $item_brands,
            'item_uoms' => $item_uoms,
            'item_remarks' => $item_remarks
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_serialno' => 'nullable|string|max:255',
            'item_quantity' => 'required|integer|max:999999',
            'item_remark' => 'nullable|string|max:255',
            'item_uom_name' => 'required|string|max:255',
            'item_brand_name' => 'required|string|max:255',
            'item_category_id' => 'required|exists:item_categories,item_category_id'
        ]);

        // First or create UOM and Brand
        $brand = ItemBrand::firstOrCreate(['item_brand_name' => $request->item_brand_name]);
        $uom = ItemUom::firstOrCreate(['item_uom_name' => $request->item_uom_name]);

        // Check for existing item with same combination including item_remark
        $existingItem = Item::where('item_name', $request->item_name)
            ->where('item_brand_id', $brand->item_brand_id)
            ->where('item_uom_id', $uom->item_uom_id)
            ->where('item_remark', $request->item_remark)
            ->first();

        if ($existingItem) {
            return redirect()->back()->withErrors(['duplicate' => 'This item already exists with the same name, brand, UOM, and remark.']);
        }

        // Determine initial quantity status
        $quantityStatus = $request->item_quantity > 0 ? 'Available' : 'Out of Stock';

        // Create Item
        Item::create([
            'item_name' => $request->item_name,
            'item_serialno' => $request->item_serialno,
            'item_quantity' => $request->item_quantity,
            'item_quantity_remaining' => $request->item_quantity,
            'item_quantity_status' => $quantityStatus,
            'item_remark' => $request->item_remark,
            'item_category_id' => $request->item_category_id,
            'item_uom_id' => $uom->item_uom_id,
            'item_brand_id' => $brand->item_brand_id,
        ]);

        return redirect()->back()->with('success', 'Item added successfully');
    }
    public function storeCategory(Request $request)
    {
        $request->validate([
            'item_category_name' => 'required|string|max:255|unique:item_categories,item_category_name',
        ]);

        ItemCategory::create([
            'item_category_name' => $request->item_category_name
        ]);

        return redirect()->back()->with('success', 'Category added successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Item $item)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $item_id)
    {
        $item = Item::findOrFail($item_id);

        // Validate input
        $request->validate([
            'item_name' => 'required|string|max:255',
            'item_serialno' => 'nullable|string|max:255',
            'item_quantity' => 'required|integer|min:0',
            'item_remark' => 'nullable|string|max:500',
            'item_brand_name' => 'nullable|string|max:255',
            'item_uom_name' => 'nullable|string|max:255',
        ]);

        // Prevent lowering total quantity below current total
        if ($request->item_quantity < $item->item_quantity) {
            return redirect()->back()->with('error', "Cannot decrease total quantity below current total ({$item->item_quantity}).");
        }

        // Calculate how much the total increased
        $difference = $request->item_quantity - $item->item_quantity;

        // Increment remaining stock by the difference
        $item->item_quantity_remaining += $difference;

        // Update total quantity
        $item->item_quantity = $request->item_quantity;

        // Update other fields
        $item->item_name = $request->item_name;
        $item->item_serialno = $request->item_serialno;
        $item->item_remark = $request->item_remark;

        // Update Brand if provided
        if ($request->item_brand_name) {
            $brand = ItemBrand::firstOrCreate([
                'item_brand_name' => $request->item_brand_name
            ]);
            $item->item_brand_id = $brand->item_brand_id;
        }

        // Update Unit of Measure if provided
        if ($request->item_uom_name) {
            $uom = ItemUom::firstOrCreate([
                'item_uom_name' => $request->item_uom_name
            ]);
            $item->item_uom_id = $uom->item_uom_id;
        }

        // Update quantity status
        if ($item->item_quantity_remaining == 0) {
            $item->item_quantity_status = 'Out of Stock';
        } elseif ($item->item_quantity_remaining < ($item->item_quantity * 0.2)) {
            $item->item_quantity_status = 'Low Stock';
        } else {
            $item->item_quantity_status = 'Available';
        }

        $item->save();

        return redirect()->back()->with('success', 'Item updated successfully.');
    }
    /**
     * Remove the specified resource from storage.
     */
    public function destroy($item_id)
    {
        Item::findOrFail($item_id)->delete();

        return redirect()->back()->with('success', 'Item deleted successfully');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Item::whereIn('item_id', $ids)->delete();
            return response()->json(['success' => true, 'message' => 'Selected items deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'No items selected.'], 400);
    }
    // ItemController.php

    public function checkDuplicate(Request $request)
    {
        $brand = ItemBrand::where('item_brand_name', $request->item_brand_name)->first();
        $uom = ItemUom::where('item_uom_name', $request->item_uom_name)->first();

        $exists = Item::where('item_name', $request->item_name)
            ->where('item_brand_id', $brand?->item_brand_id)
            ->where('item_uom_id', $uom?->item_uom_id)
            ->where('item_remark', $request->item_remark)
            ->exists();

        return response()->json(['exists' => $exists]);
    }
}