<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonnelItem;
use App\Models\Personnel;
use Illuminate\Support\Facades\DB;
use App\Models\Item;

class ReturnController extends Controller
{
    public function index(Request $request)
    {
        $itemCount = Item::sum('item_quantity');

        // Total outbound quantity (Received/Not Receive)
        $outboundCount = PersonnelItem::whereIn('personnel_item_remarks', ['Received', 'Not Receive'])
            ->sum('personnel_item_quantity');

        // Total damaged items
        $damagedItem = Item::where('item_remark', 'Damaged')
            ->sum('item_quantity_remaining');

        // Remaining items 
        $itemRemaining = Item::where('item_remark', '!=', 'Damaged')
            ->sum('item_quantity_remaining');

        // Available items 
        $goodItemTotal = Item::where('item_remark', 'Good')
            ->where('item_remark', '!=', 'Damaged')
            ->sum('item_quantity');

        $availableItem = $goodItemTotal - $outboundCount;

        // Fetch ONLY returned items for recent activities by excluding outbound remarks
        $recentActivities = PersonnelItem::where('personnel_item_quantity', '>', 0)
            ->whereNotIn('personnel_item_remarks', ['Received', 'Not Receive'])
            ->latest()
            ->take(5)
            ->paginate(5);
        $categoriesWithStats = [];
        $categories = DB::table('item_categories')->get();

        foreach ($categories as $category) {
            $itemsInCategory = Item::where('item_category_id', $category->item_category_id)->get();
            $categoryTotal = $itemsInCategory->sum('item_quantity');

            $categoryOutbound = PersonnelItem::whereIn('item_id', $itemsInCategory->pluck('id'))
                ->whereIn('personnel_item_remarks', ['Received', 'Not Receive'])
                ->sum('personnel_item_quantity');

            $categoryAvailable = max(0, $itemsInCategory->where('item_remark', 'Good')->sum('item_quantity') - $categoryOutbound);

            $categoriesWithStats[] = [
                'name' => $category->item_category_name,
                'icon' => $category->item_category_icon,
                'total' => $categoryTotal,
                'available' => $categoryAvailable,
                'outboundCount' => $categoryOutbound,
                'broken' => $itemsInCategory->where('item_remark', 'Damaged')->sum('item_quantity'),
                'deprecated' => $itemsInCategory->where('item_remark', 'Deprecated')->sum('item_quantity'),
            ];
        }

        return view("return.index", compact(
            'itemCount',
            'outboundCount',
            'damagedItem',
            'availableItem',
            'recentActivities',
            'itemRemaining',
            'goodItemTotal','categoriesWithStats'
        ));
    }
}