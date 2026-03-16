<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUom;
use App\Models\ItemBrand;
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
        $categoryId = $request->get('category'); // get selected category
        $brandId = $request->get('brand');

        $item_categories = ItemCategory::all();
        $item_brands = ItemBrand::all();
        $item_uoms = ItemUom::all();

        // Build query with optional search, remark, and category filter
        $itemsQuery = Item::query();

        if ($query) {
            $itemsQuery->where(function($q) use ($query) {
                $q->where('item_name', 'like', "%$query%")
                ->orWhere('item_serialno', 'like', "%$query%");
            });
        }

        if ($remarkFilter) {
            $itemsQuery->where('item_remark', $remarkFilter);
        }

        if ($categoryId) {
            $itemsQuery->where('item_category_id', $categoryId); // <-- apply filter here
        }

         if ($brandId) {
            $itemsQuery->where('item_brand_id', $brandId); // <-- apply filter here
        }

        // Paginate results
        $items = $itemsQuery->orderBy('created_at', 'desc')->paginate(10);
        $items->appends([
            'search' => $query,
            'remark' => $remarkFilter,
            'category' => $categoryId,
            'brand' => $brandId
        ]);

        // Get unique item remarks for dropdown
        $item_remarks = Item::select('item_remark')
                            ->distinct()
                            ->orderBy('item_remark')
                            ->pluck('item_remark');

        // EXPORT SINGLE ITEM PDF
        if ($request->get('export') == 'pdf' && $request->has('item_id')) {
            $item = Item::findOrFail($request->get('item_id'));
            $pdf = Pdf::loadView('inventory.pdf-individual', compact('item'));
            return $pdf->stream("item_{$item->item_id}.pdf");
        }

        // EXPORT ALL ITEMS PDF
        if ($request->get('export') == 'pdf' && !$request->has('item_id')) {
            $allItems = $itemsQuery->get(); // respect search, remark & category
            $pdf = Pdf::loadView('inventory.pdf-forall', compact('allItems'));
            return $pdf->stream('inventory.pdf');
        }

        // AJAX Table Update
        if ($request->ajax() || $request->has('ajax')) {
            return view('inventory.inventory-table', compact(
                'items', 'item_categories', 'item_brands', 'item_uoms'
            ))->render();
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
                    $dbQuery->where(function($q) use ($query) {
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

        return view('inventory.index', compact(
            'item_categories', 
            'item_brands', 
            'item_uoms', 
            'items', 
            'item_remarks'
        ));
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
            'item_name'        => 'required|string|max:255',
            'item_serialno'    => 'nullable|string|max:255',
            'item_quantity'    => 'required|integer|max:999999',
            'item_remark'      => 'nullable|string|max:255',
            'item_uom_name'    => 'required|string|max:255',
            'item_brand_name'  => 'required|string|max:255',
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

        // Create Item
        Item::create([
            'item_name'        => $request->item_name,
            'item_serialno'    => $request->item_serialno,
            'item_quantity'    => $request->item_quantity,
            'item_remark'      => $request->item_remark,
            'item_category_id' => $request->item_category_id,
            'item_uom_id'      => $uom->item_uom_id,
            'item_brand_id'    => $brand->item_brand_id,
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

    $item->item_name = $request->item_name;
    $item->item_serialno = $request->item_serialno; // added
    $item->item_quantity = $request->item_quantity;
    $item->item_remark = $request->item_remark;

    // Brand
    if ($request->item_brand_name) {
        $brand = ItemBrand::firstOrCreate([
            'item_brand_name' => $request->item_brand_name
        ]);
        $item->item_brand_id = $brand->item_brand_id;
    }

    // Unit of Measure
    if ($request->item_uom_name) {
        $uom = ItemUom::firstOrCreate([
            'item_uom_name' => $request->item_uom_name
        ]);
        $item->item_uom_id = $uom->item_uom_id;
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