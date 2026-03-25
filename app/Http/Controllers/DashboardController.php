<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\PersonnelItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $itemCount = Item::where('item_remark', '!=', 'Damaged')
            ->sum('item_quantity');

        // Total outbound quantity (Received)
        $outboundCount = PersonnelItem::where('personnel_item_remarks', 'Received')
            ->sum('personnel_item_quantity');

        // Total damaged items
        $damagedItem = Item::where('item_remark', 'Damaged')
            ->sum('item_quantity');

        // Remaining items 
        $itemRemaining = Item::where('item_remark', '!=', 'Damaged')
            ->sum('item_quantity_remaining');

        // Available items 
        $goodItemTotal = Item::where('item_remark', 'Good')
            ->sum('item_quantity');

        $availableItem = $goodItemTotal - $outboundCount;

        // Recent 5 activities
        $recentActivities = PersonnelItem::latest()
            ->take(5)
            ->get();

        return view("dashboard", compact(
            'itemCount',
            'outboundCount',
            'damagedItem',
            'availableItem',
            'recentActivities',
            'itemRemaining'
        ));
    }

}
