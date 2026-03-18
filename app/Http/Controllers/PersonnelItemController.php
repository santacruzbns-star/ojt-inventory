<?php

namespace App\Http\Controllers;

use App\Models\PersonnelItem;
use App\Models\Personnel;
use App\Models\Item;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PersonnelItemController extends Controller
{
    /**
     * Display a listing of borrowed items.
     */
    public function index()
    {
        // Load all outbound records
        $outbounds = PersonnelItem::with(['personnel','personnel.branch', 'item'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Load personnels and items for the form
        $personnels = Personnel::all();
        $items = Item::paginate(10);

        return view('personnel.index', compact('outbounds', 'personnels', 'items'));
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
            $item = \App\Models\Item::findOrFail($request->item_id);

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
            \App\Models\PersonnelItem::create([
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

        $branch = Branch::create([
            'branch_name' => $request->branch_name,
            'branch_department' => $request->branch_department
        ]);

        Personnel::create([
            'branch_id' => $branch->id,
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

        return redirect()->route('outbound.index')->with('success','Outbound updated successfully.');
    }
      /**
     * Separate return function
     */
  public function returnItem(Request $request, PersonnelItem $outbound)
{
    $validated = $request->validate([
        'return_quantity' => 'required|integer|min:1|max:' . $outbound->personnel_item_quantity,
        'return_condition' => 'required|string|in:Good,Damaged',
    ]);

    DB::transaction(function () use ($outbound, $validated) {

        $item = $outbound->item;

        // Subtract returned quantity from original outbound
        $outbound->personnel_item_quantity -= $validated['return_quantity'];
        $outbound->save();

        if ($validated['return_condition'] === 'Good') {
            // Good: merge into stock
            $item->item_quantity_remaining += $validated['return_quantity'];
            $item->item_quantity_status = $item->item_quantity_remaining <= 0 ? 'Out of Stock' :
                ($item->item_quantity_remaining < ($item->item_quantity * 0.2) ? 'Low Stock' : 'Available');
            $item->save();

            // Record as PersonnelItem
            PersonnelItem::create([
                'personnel_id' => $outbound->personnel_id,
                'item_id' => $item->item_id,
                'personnel_item_quantity' => $validated['return_quantity'],
                'personnel_date_receive' => now(),
                'personnel_item_remarks' => 'Returned',
                'item_remark' => 'Good',
            ]);

        } else {
            // Damaged: check if a “Damaged” record for this item already exists
            $existingDamaged = Item::where('item_name', $item->item_name)
                                    ->where('item_remark', 'Damaged')
                                    ->first();

            if ($existingDamaged) {
                // Merge quantity into existing damaged item
                $existingDamaged->item_quantity += $validated['return_quantity'];
                $existingDamaged->item_quantity_remaining += $validated['return_quantity'];
                $existingDamaged->save();

                $damagedItemId = $existingDamaged->item_id;
            } else {
                // Create new damaged item
                $damagedItem = $item->replicate();
                $damagedItem->item_quantity = $validated['return_quantity'];
                $damagedItem->item_quantity_remaining = $validated['return_quantity'];
                $damagedItem->item_quantity_status = 'Damaged';
                $damagedItem->item_remark = 'Damaged';
                $damagedItem->save();

                $damagedItemId = $damagedItem->item_id;
            }

            // Record as PersonnelItem
            PersonnelItem::create([
                'personnel_id' => $outbound->personnel_id,
                'item_id' => $damagedItemId,
                'personnel_item_quantity' => $validated['return_quantity'],
                'personnel_date_receive' => now(),
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
}