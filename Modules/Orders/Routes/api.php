<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Orders\Http\Controllers\OrdersController;
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

Route::middleware(['web','auth'])->prefix('admin/order')->group(function() {
    Route::post('/store',[OrdersController::class, 'store'])->name('order.store');
    Route::post('/update',[OrdersController::class, 'update'])->name('order.update');
    Route::post('/delete',[OrdersController::class, 'destroy'])->name('order.delete');
    Route::get('/show/{id}',[OrdersController::class, 'show'])->name('order.show');
    Route::get('/all',[OrdersController::class, 'all'])->name('order.all');
    
});