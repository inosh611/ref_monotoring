<?php

use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;

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

Route::prefix('orders')->group(function() {
    Route::get('/', 'OrdersController@index');
});

Route::prefix('admin/order')->group(function () {
    Route::post('/data-table', [OrdersController::class, 'dataTable'])->name('order.datatable');
    Route::get('/', [OrdersController::class, 'index'])->name('order.index');
    Route::get('/create', [OrdersController::class, 'create'])->name('order.create');
    Route::get('/edit/{id}', [OrdersController::class, 'edit'])->name('order.edit');
});
