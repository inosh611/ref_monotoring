<?php

use Illuminate\Support\Facades\Route;
use Modules\Chat\Http\Controllers\ChatController;

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


Route::prefix('chat')->group(function() {
    Route::get('/', 'OrdersController@index');
});

Route::prefix('admin/chat')->group(function () {
    Route::post('/data-table', [ChatController::class, 'dataTable'])->name('chat.datatable');
    Route::get('/', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/create', [ChatController::class, 'create'])->name('chat.create');
    Route::get('/edit/{id}', [ChatController::class, 'edit'])->name('chat.edit');
});
