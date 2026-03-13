<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemUom;
use App\Models\ItemBrand;
use App\Models\ItemCategory;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $item_categories = ItemCategory::all();
        $item_brands = ItemBrand::all();
        $item_uoms = ItemUom::all();

        $items = Item::orderBy('created_at', 'desc')->paginate(10);

        return view('inventory.index', compact('item_categories', 'item_brands', 'item_uoms', 'items'));
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
        Item::where('item_id', $item_id)->delete();

        return redirect()->back()->with('success','Item deleted successfully');
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