<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\ProductPriceController;
use Modules\Product\Http\Controllers\UnitController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['web','auth'])->prefix('admin/product')->group(function() {
    Route::post('/store',[ProductController::class, 'store'])->name('product.store');
    Route::post('/update',[ProductController::class, 'update'])->name('product.update');
    Route::post('/delete',[ProductController::class, 'destroy'])->name('product.delete');
    Route::get('/show/{id}',[ProductController::class, 'show'])->name('product.show');
    Route::get('/all',[ProductController::class, 'all'])->name('product.all');
    Route::get('/products/search', [ProductController::class, 'search'])->name('products.search');
});

Route::middleware(['web','auth'])->prefix('admin/unit')->group(function() {
    Route::post('/store',[UnitController::class, 'store'])->name('unit.store');
    Route::post('/update',[UnitController::class, 'update'])->name('unit.update');
    Route::post('/delete',[UnitController::class, 'destroy'])->name('unit.delete');
    Route::get('/show/{id}',[UnitController::class, 'show'])->name('unit.show');
    Route::get('/all',[UnitController::class, 'all'])->name('unit.all');
});
Route::middleware(['web','auth'])->prefix('admin/product-price')->group(function() {
    Route::post('/store',[ProductPriceController::class, 'store'])->name('product.price.store');
    Route::post('/update',[ProductPriceController::class, 'update'])->name('product.price.update');
    Route::post('/delete',[ProductPriceController::class, 'destroy'])->name('product.price.delete');
    Route::get('/show/{id}',[ProductPriceController::class, 'show'])->name('product.price.show');
    Route::get('/all',[ProductPriceController::class, 'all'])->name('product.price.all');
});