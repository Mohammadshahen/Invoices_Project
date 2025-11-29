<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\Invoices\InvoiceAttachmentsController;
use App\Http\Controllers\Invoices\InvoiceController;
use App\Http\Controllers\Invoices\InvoiceDetailController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SectionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AdminController::class,'index'])->middleware(['auth', 'verified'])->name('dashboard');



Route::resource('/sections',SectionController::class)->middleware(['auth', 'verified']);


Route::resource('/products',ProductController::class)->middleware(['auth', 'verified']);



Route::resource('/invoice_detail',InvoiceDetailController::class)->middleware(['auth', 'verified']);
Route::resource('/invoice_attachment',InvoiceAttachmentsController::class)->middleware(['auth', 'verified']);



Route::resource('/invoices',InvoiceController::class)->middleware(['auth', 'verified']);
Route::get('/status/{invoice}',[InvoiceController::class,'Status_show'])->name('status');
Route::put('/status/{invoice}',[InvoiceController::class,'Status_update'])->name('status_update');
Route::get('/get_products/{section_id}',[InvoiceController::class,'getProducts']);
Route::get('/Print_invoice/{invoice}',[InvoiceController::class,'printInvoice'])->name('Print_invoice');


Route::get('archive',[InvoiceController::class,'archive'])->name('archive');
Route::patch('restore/{invoice}',[InvoiceController::class,'restore'])->name('restore');
Route::post('force_destroy/{invoice}',[InvoiceController::class,'force_destroy'])->name('force_destroy');


Route::get('/download/{id}',[InvoiceAttachmentsController::class,'download']);

require __DIR__.'/auth.php';







Route::get("/s",function(){
    return view("Invoices.update_invoice");
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
