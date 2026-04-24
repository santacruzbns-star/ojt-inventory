<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PersonnelItem;
use App\Models\Item;
use Illuminate\Support\Facades\DB;


class ReturnController extends Controller
{
    public function index(Request $request)
    {
        /*
        |--------------------------------------------------------------------------
        | OVERALL INVENTORY COUNTS
        |--------------------------------------------------------------------------
        */

        // Total physical stock of all items
        $itemCount = Item::sum('item_quantity');

        // Total damaged items
        $damagedItem = Item::where('item_remark', 'Damaged')
            ->sum('item_quantity_remaining');

        // Total deprecated items
        $deprecatedItem = Item::where('item_remark', 'Deprecated')
            ->sum('item_quantity_remaining');

        // Total usable / good stock
        $goodItemTotal = Item::where('item_remark', 'Good')
            ->sum('item_quantity_remaining');

        // Total outbound items currently issued
        $outboundCount = PersonnelItem::whereIn(
            'personnel_item_remarks',
            ['Received', 'Not Receive']
        )->sum('personnel_item_quantity');

        // Remaining stock excluding damaged / deprecated
        $itemRemaining = Item::whereNotIn(
            'item_remark',
            ['Damaged', 'Deprecated']
        )->sum('item_quantity_remaining');

        // Available stock
        $availableItem = max(0, $itemRemaining);

        /*
        |--------------------------------------------------------------------------
        | RECENT ACTIVITIES (RETURNS ONLY)
        |--------------------------------------------------------------------------
        | Added item relation so serial number can display
        |--------------------------------------------------------------------------
        */

        $recentActivities = PersonnelItem::with([
            'personnel',
            'item' => function ($query) {
                $query->select(
                    'item_id',
                    'item_name',
                    'item_serialno',
                    'item_remark',
                    'item_category_id' // REQUIRED
                );
            },
            'item.category'
        ])
            ->where('personnel_item_quantity', '>', 0)
            ->whereNotIn(
                'personnel_item_remarks',
                ['Received', 'Not Receive']
            )
            ->latest()
            ->paginate(5);

        /*
        |--------------------------------------------------------------------------
        | CATEGORY STATISTICS
        |--------------------------------------------------------------------------
        */

        $categoriesWithStats = [];

        $categories = DB::table('item_categories')->get();

        foreach ($categories as $category) {

            $itemsInCategory = Item::where(
                'item_category_id',
                $category->item_category_id
            )->get();

            $itemIds = $itemsInCategory->pluck('item_id');

            // Total stock in category
            $categoryTotal = $itemsInCategory->sum('item_quantity');

            // Total available good stock
            $categoryAvailable = $itemsInCategory
                ->where('item_remark', 'Good')
                ->sum('item_quantity_remaining');

            // Total outbound issued
            $categoryOutbound = PersonnelItem::whereIn('item_id', $itemIds)
                ->whereIn(
                    'personnel_item_remarks',
                    ['Received', 'Not Receive']
                )
                ->sum('personnel_item_quantity');

            // Damaged stock
            $categoryBroken = $itemsInCategory
                ->where('item_remark', 'Damaged')
                ->sum('item_quantity_remaining');

            // Deprecated stock
            $categoryDeprecated = $itemsInCategory
                ->where('item_remark', 'Deprecated')
                ->sum('item_quantity_remaining');

            $categoriesWithStats[] = [
                'name' => $category->item_category_name,
                'icon' => $category->item_category_icon,
                'total' => $categoryTotal,
                'available' => $categoryAvailable,
                'taken' => $categoryOutbound,
                'broken' => $categoryBroken,
                'deprecated' => $categoryDeprecated,
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | RETURN VIEW
        |--------------------------------------------------------------------------
        */

        return view('return.index', compact(
            'itemCount',
            'outboundCount',
            'damagedItem',
            'deprecatedItem',
            'availableItem',
            'itemRemaining',
            'goodItemTotal',
            'recentActivities',
            'categoriesWithStats'
        ));
    }
}