<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use Modules\Dealers\Http\Controllers\DealersController;
use Modules\MyVisiting\Http\Controllers\MyVisitingController;

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

Route::middleware(['web','auth'])->prefix('admin/my-visiting')->group(function() {
    Route::post('/store',[MyVisitingController::class, 'store'])->name('my.visiting.store');
    Route::post('/store-check-out',[MyVisitingController::class, 'checkOut'])->name('my.checkout.store');
});