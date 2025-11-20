<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Invoices\InvoiceController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');



Route::resource('/sections',SectionController::class)->middleware(['auth', 'verified']);

Route::resource('/products',ProductController::class)->middleware(['auth', 'verified']);

Route::resource('/invoices',InvoiceController::class)->middleware(['auth', 'verified']);

Route::get('/get_products/{section_id}',[InvoiceController::class,'getProducts']);



require __DIR__.'/auth.php';







Route::get("/s",function(){
    return view("AAA");
});



// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });



// Route::get('/{page}', [AdminController::class,'index']);
