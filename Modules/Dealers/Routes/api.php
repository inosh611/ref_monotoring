<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Dealers\Http\Controllers\DealersController;

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

Route::middleware('auth:api')->get('/dealer', function (Request $request) {
    return $request->user();
});
Route::middleware(['web','auth'])->prefix('admin/dealer')->group(function() {
    Route::post('/store',[DealersController::class, 'store'])->name('dealer.store');
    Route::post('/update',[DealersController::class, 'update'])->name('dealer.update');
    Route::post('/delete',[DealersController::class, 'destroy'])->name('dealer.delete');
    Route::get('/show/{id}',[DealersController::class, 'show'])->name('dealer.show');
    Route::get('/all',[DealersController::class, 'all'])->name('dealer.all');

});