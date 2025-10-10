<?php

use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;
use Modules\StockOdit\Http\Controllers\StockOditController;

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

Route::middleware(['web','auth'])->prefix('admin/stock-odit')->group(function() {
    Route::post('/store',[StockOditController::class, 'store'])->name('stock-odit.store');
    Route::post('/update',[StockOditController::class, 'update'])->name('stock-odit.update');
    Route::post('/delete',[StockOditController::class, 'destroy'])->name('stock-odit.delete');
    Route::get('/show/{id}',[StockOditController::class, 'show'])->name('stock-odit.show');
    Route::get('/all',[StockOditController::class, 'all'])->name('stock-odit.all');
    // Route::get('/search-order',[StockOditController::class, 'searchOrder'])->name('my.collection.search.order');
    
});