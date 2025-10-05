<?php

use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;

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

Route::middleware(['web','auth'])->prefix('admin/my-collection')->group(function() {
    Route::post('/store',[MyCollectionsController::class, 'store'])->name('my.collection.store');
    Route::post('/update',[MyCollectionsController::class, 'update'])->name('my.collection.update');
    Route::post('/delete',[MyCollectionsController::class, 'destroy'])->name('my.collection.delete');
    Route::get('/show/{id}',[MyCollectionsController::class, 'show'])->name('my.collection.show');
    Route::get('/all',[MyCollectionsController::class, 'all'])->name('my.collection.all');
    
});