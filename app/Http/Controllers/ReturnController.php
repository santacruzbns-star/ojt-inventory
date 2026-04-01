<?php

namespace App\Http\Controllers;
use App\Models\PersonnelItem;
use App\Models\Personnel;
use App\Models\Branch;
use App\Models\Item;

use Illuminate\Http\Request;

class ReturnController extends Controller
{
    // app\Http\Controllers\ReturnController.php

    public function index(Request $request)
    {
        // 1. Define the filters (Search, etc.)
        $search = $request->get('search');

        // 2. Define $outbounds (This was missing or named differently)
        $outbounds = PersonnelItem::with(['personnel', 'item'])
            ->where('personnel_item_remarks', 'Returned')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('item', fn($q2) => $q2->where('item_name', 'like', "%{$search}%"))
                    ->orWhereHas('personnel', fn($q2) => $q2->where('personnel_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate();

        // 3. Define the other variables required by your compact()
        $personnels = Personnel::orderBy('personnel_name')->get();
        $items = Item::all();

        $departments = Branch::distinct()->pluck('branch_department');
        $branches = Branch::distinct()->pluck('branch_name');
        $item_remarks = PersonnelItem::distinct()->pluck('personnel_item_remarks');

        // 4. Calculate Chart Stats
        $itemsWithStats = Item::all()->map(function ($item) {
            $total = $item->item_quantity_total;
            $available = $item->item_quantity_remaining;
            $takenQty = $total - $available;
            $percentageTaken = ($total > 0) ? round(($takenQty / $total) * 100) : 0;

            return [
                'name' => $item->item_name,
                'available' => $available,
                'broken' => Item::where('item_name', $item->item_name)->where('item_remark', 'Damaged')->sum('item_quantity_remaining'),
                'deprecated' => Item::where('item_name', $item->item_name)->where('item_remark', 'Deprecated')->sum('item_quantity_remaining'),
                'percentage_taken' => $percentageTaken,
            ];
        });

        // 5. Pass everything to the view
        return view('return.index', compact(
            'outbounds', // <--- This matches the variable defined above
            'personnels',
            'items',
            'departments',
            'branches',
            'item_remarks',
            'itemsWithStats'
        ));
    }
}
