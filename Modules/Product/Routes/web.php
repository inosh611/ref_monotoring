<?php

use Illuminate\Support\Facades\Route;
use Modules\Product\Http\Controllers\ProductController;
use Modules\Product\Http\Controllers\UnitController;

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

Route::prefix('product')->group(function() {
    Route::get('/', 'OrdersController@index');
});

Route::prefix('admin/product')->group(function () {
    Route::post('/data-table', [ProductController::class, 'dataTable'])->name('product.datatable');
    Route::get('/', [ProductController::class, 'index'])->name('product.index');
    Route::get('/create', [ProductController::class, 'create'])->name('product.create');
    Route::get('/edit/{id}', [ProductController::class, 'edit'])->name('product.edit');
});

Route::prefix('admin/unit')->group(function () {
    Route::post('/data-table', [UnitController::class, 'dataTable'])->name('unit.datatable');
    Route::get('/', [UnitController::class, 'index'])->name('unit.index');
    Route::get('/create', [UnitController::class, 'create'])->name('unit.create');
    Route::get('/edit/{id}', [UnitController::class, 'edit'])->name('unit.edit');
});