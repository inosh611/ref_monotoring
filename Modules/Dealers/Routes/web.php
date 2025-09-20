<?php

use Illuminate\Support\Facades\Route;
use Modules\Dealers\Http\Controllers\DealersController;

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

Route::prefix('admin/dealer')->group(function () {
    Route::post('/data-table', [DealersController::class, 'dataTable'])->name('dealer.datatable');
    Route::get('/', [DealersController::class, 'index'])->name('dealer.index');
    Route::get('/create', [DealersController::class, 'create'])->name('dealer.create');
    Route::get('/edit/{id}', [DealersController::class, 'edit'])->name('dealer.edit');
});
