<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;
use Modules\StockOdit\Http\Controllers\StockOditController;
use Modules\Target\Http\Controllers\TargetController;

Route::prefix('admin/target')->group(function () {
    Route::post('/data-table', [StockOditController::class, 'dataTable'])->name('target.datatable');
    Route::get('/', [TargetController::class, 'index'])->name('target.index');
    Route::get('/create', [TargetController::class, 'create'])->name('target.create');
    Route::get('/edit/{id}', [StockOditController::class, 'edit'])->name('stock.odit.edit');
});
