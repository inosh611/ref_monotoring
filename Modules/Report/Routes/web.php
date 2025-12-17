<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\UnitController;
use Modules\Report\Http\Controllers\ReportController;

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
});


