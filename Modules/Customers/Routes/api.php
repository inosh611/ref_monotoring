<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Modules\Customers\Http\Controllers\CustomersController;

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

Route::middleware('auth:api')->get('/customer', function (Request $request) {
    return $request->user();
});
Route::middleware(['web','auth'])->prefix('admin/customer')->group(function() {
    Route::post('/store',[CustomersController::class, 'store'])->name('customer.store');
    Route::post('/update',[CustomersController::class, 'update'])->name('customer.update');
     Route::post('/delete',[CustomersController::class, 'destroy'])->name('customer.delete');
});