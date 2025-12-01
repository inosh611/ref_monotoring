<?php

use Illuminate\Http\Request;    
use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;
use Modules\StockOdit\Http\Controllers\StockOditController;
use Modules\Target\Http\Controllers\TargetController;

Route::middleware(['web','auth'])->prefix('admin/target')->group(function() {
    Route::post('/store',[TargetController::class, 'store'])->name('target.store');
    Route::post('/update',[TargetController::class, 'update'])->name('target.update');
    Route::post('/delete',[TargetController::class, 'destroy'])->name('target.delete');
    Route::get('/show/{id}',[TargetController::class, 'show'])->name('target.show');
    Route::get('/all',[TargetController::class, 'all'])->name('target.all');
    
    
});