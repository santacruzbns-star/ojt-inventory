<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PersonnelController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\PersonnelItemController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

  

    // Additional inventory routes
   
   
});
    // Inventory
    Route::resource('inventory', ItemController::class);
    Route::post('/item-category/store', [ItemController::class, 'storeCategory'])->name('item-category.store');
    Route::post('/inventory/check-duplicate', [ItemController::class, 'checkDuplicate'])->name('inventory.checkDuplicate');
    Route::post('/inventory/bulk-delete', [ItemController::class, 'bulkDestroy'])
    ->name('inventory.bulkDelete');
    Route::get('/inventory/{item}/pdf', [ItemController::class, 'exportIndividualPDF'])
     ->name('inventory.export.individual.pdf');

    // Outbound

    // Route::put('outbound/update', PersonnelItemController::class)->name('outbound.update');
    Route::resource('outbound', PersonnelItemController::class);
    Route::post('/personnels/store', [PersonnelItemController::class, 'storePersonnel'])->name('personnels.store');

   Route::put('/outbound/{outbound}/return', [PersonnelItemController::class, 'returnItem'])
    ->name('outbound.return')
    ->whereNumber('outbound');
    
require __DIR__.'/auth.php';