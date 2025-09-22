<?php

use Illuminate\Support\Facades\Route;
use Modules\Dealers\Http\Controllers\DealersController;
use Modules\MyVisiting\Http\Controllers\MyVisitingController;

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

Route::prefix('admin/my-visiting')->group(function () {
    Route::post('/data-table', [DealersController::class, 'dataTable'])->name('my.visiting.datatable');
    Route::get('/management', [MyVisitingController::class, 'index'])->name('submit.index');
    Route::get('/submit-check-out', [MyVisitingController::class, 'checkIn'])->name('submit.check.in');
    
});
