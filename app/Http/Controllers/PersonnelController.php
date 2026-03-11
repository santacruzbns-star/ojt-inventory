<?php

namespace App\Http\Controllers;

use App\Models\Personnel;
use App\Models\Branch;
use Illuminate\Http\Request;

class PersonnelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $personnels = Personnel::All();
       $branches   = Branch::All();

       return view('personnel.index', compact('personnels','branches'));
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
            'branch_name' => '|string|max:255',
            'branch_department' => 'string|max:255',
            'personnel_name' => '|string|max:255',
           
           
        ]);

        // Create branch
        $branch = Branch::create([
            'branch_name' => $request->branch_name,
            'branch_department' => $request->branch_department
        ]);

        // Create personnel linked to branch
        Personnel::create([
            'branch_id' => $branch->id,
            'personnel_name' => $request->personnel_name,
           
        ]);

        return redirect()->back()->with('success','Personnel added successfully');
    }
    /**
     * Display the specified resource.
     */
    public function show(Personnel $personnel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Personnel $personnel)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Personnel $personnel)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Personnel $personnel)
    {
        //
    }
}
