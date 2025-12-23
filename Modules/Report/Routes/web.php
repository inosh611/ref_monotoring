<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\UnitController;
use Modules\Report\Http\Controllers\ReportController;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Report\Http\Controllers\ReportGenarateController;

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


Route::prefix('admin/report')->group(function () {
    Route::post('/data-table', [ProductController::class, 'dataTable'])->name('product.datatable');
    Route::get('/', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/visiting', [ReportController::class, 'customerWisedVisitsReport'])->name('visiting.report.filter');
    Route::post('/order', [ReportController::class, 'orderReport'])->name('order.report.filter');
     Route::post('/collection', [ReportController::class, 'collectionReport'])->name('collectioin.report.filter');


    //Excel Export Route
    Route::get('/visiting/export', [ReportGenarateController::class, 'exportCustomerWisedVisits'])
        ->name('admin.report.visiting.export');
    Route::get('/order/export', [ReportGenarateController::class, 'exportOrder'])
        ->name('admin.report.order.export');
    Route::get('/stock/export', [ReportGenarateController::class, 'exportStock'])
        ->name('admin.report.stock.export');
        Route::get('/collection/export', [ReportGenarateController::class, 'exportCollectionReport'])
        ->name('admin.report.collection.export');
});


