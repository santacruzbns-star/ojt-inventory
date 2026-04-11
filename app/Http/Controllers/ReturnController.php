<?php

namespace App\Http\Controllers;
use App\Models\PersonnelItem;
use App\Models\Personnel;
use App\Models\Branch;
use App\Models\Item;

use Illuminate\Http\Request;

class ReturnController extends Controller
{

    public function index(Request $request)
    {
        $items = PersonnelItem::with("personnel_item")->get();

        return view("return.index", compact('items'));
    }

    public function create(Request $request)
    {

    }

    public function store(Request $request)
    {

    }

    public function show(PersonnelItem $item)
    {

    }

    public function edit(PersonnelItem $item)
    {

    }

    public function update(Request $request, PersonnelItem $item)
    {

    }

    public function destroy(PersonnelItem $item)
    {

    }
}
