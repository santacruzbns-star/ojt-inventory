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

        // 2. Define $outbounds (History of items that have been 'Returned')
        $outbounds = PersonnelItem::with(['personnel', 'item'])
            ->where('personnel_item_remarks', 'Returned')
            ->when($search, function ($q) use ($search) {
                $q->whereHas('item', fn($q2) => $q2->where('item_name', 'like', "%{$search}%"))
                    ->orWhereHas('personnel', fn($q2) => $q2->where('personnel_name', 'like', "%{$search}%"));
            })
            ->latest()
            ->paginate();

        // 3. Metadata for filters/dropdowns
        $personnels = Personnel::orderBy('personnel_name')->get();
        $items = Item::all();
        $departments = Branch::distinct()->pluck('branch_department');
        $branches = Branch::distinct()->pluck('branch_name');
        $item_remarks = PersonnelItem::distinct()->pluck('personnel_item_remarks');

        // 4. Calculate Chart Stats based on "Received" AND "Not Receive" statuses
        $itemsWithStats = Item::all()->map(function ($item) {
            $total = $item->item_quantity_total;
            $available = $item->item_quantity_remaining;

            /**
             * UPDATED LOGIC:
             * Your database shows Row 90 has quantity 1 but status "Not Receive".
             * Rows 87 & 89 have status "Received" but quantity 0.
             * To get an accurate 'Taken' count, we sum both active statuses.
             */
            $takenQty = PersonnelItem::where('item_id', $item->id)
                ->whereIn('personnel_item_remarks', ['Received', 'Not Receive'])
                ->where('personnel_item_quantity', '>', 0)
                ->sum('personnel_item_quantity');

            // Fallback: If your status records are inconsistent, 
            // standard inventory math is: Total - Available
            if ($takenQty <= 0 && $total > $available) {
                $takenQty = $total - $available;
            }

            $percentageTaken = ($total > 0) ? round(($takenQty / $total) * 100) : 0;

            return [
                'name' => $item->item_name,
                'available' => $available,
                'broken' => Item::where('item_name', $item->item_name)
                    ->where('item_remark', 'Damaged')
                    ->sum('item_quantity_remaining'),
                'deprecated' => Item::where('item_name', $item->item_name)
                    ->where('item_remark', 'Deprecated')
                    ->sum('item_quantity_remaining'),
                'percentage_taken' => $percentageTaken,
            ];
        });

        // 5. Return View
        return view('return.index', compact(
            'outbounds',
            'personnels',
            'items',
            'departments',
            'branches',
            'item_remarks',
            'itemsWithStats'
        ));
    }
}
