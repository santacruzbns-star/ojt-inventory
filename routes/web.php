<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ItemCategoryController;
use App\Http\Controllers\ItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::resource('outbound', PersonnelController::class);

// Route::resource('inventory', ItemCategoryController::class);
Route::resource('inventory', ItemController::class);
Route::post('/item-category/store', [ItemController::class, 'storeCategory'])->name('item-category.store');
// web.php
Route::post('/inventory/check-duplicate', [ItemController::class, 'checkDuplicate'])->name('inventory.checkDuplicate');

require __DIR__.'/auth.php';
