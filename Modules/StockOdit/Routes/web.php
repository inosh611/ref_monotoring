<?php


use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;
use Modules\StockOdit\Http\Controllers\StockOditController;

Route::middleware('auth')->group(function () {
    Route::prefix('admin/stockodit')->group(function () {
        Route::post('/data-table', [StockOditController::class, 'dataTable'])->name('stock.odit.datatable');
        Route::get('/', [StockOditController::class, 'index'])->name('stock.odit.index');
        Route::get('/create', [StockOditController::class, 'create'])->name('stock.odit.create');
        Route::get('/edit/{id}', [StockOditController::class, 'edit'])->name('stock.odit.edit');
    });
});
