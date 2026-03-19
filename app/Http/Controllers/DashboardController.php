<?php

namespace App\Http\Controllers;
use App\Models\Item;
use App\Models\PersonnelItem;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request ){
         
        $ItemCounts = Item::count();
        $OutboundCounts = PersonnelItem::count();

        return view("dashboard", compact('ItemCounts', 'OutboundCounts'));
    }
}
