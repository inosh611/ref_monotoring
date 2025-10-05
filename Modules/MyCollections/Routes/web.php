<?php

use Illuminate\Support\Facades\Route;
use Modules\MyCollections\Http\Controllers\MyCollectionsController;

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

Route::prefix('admin/my-collection')->group(function () {
    Route::post('/data-table', [MyCollectionsController::class, 'dataTable'])->name('my.collection.datatable');
    Route::get('/', [MyCollectionsController::class, 'index'])->name('my.collection.index');
    Route::get('/create', [MyCollectionsController::class, 'create'])->name('my.collection.create');
    Route::get('/edit/{id}', [MyCollectionsController::class, 'edit'])->name('my.collection.edit');
});
