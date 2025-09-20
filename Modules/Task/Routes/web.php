<?php

use Illuminate\Support\Facades\Route;
use Modules\Task\Http\Controllers\TaskController;

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

Route::prefix('task')->group(function() {
    Route::get('/', 'OrdersController@index');
});

Route::prefix('admin/task')->group(function () {
    Route::post('/data-table', [TaskController::class, 'dataTable'])->name('task.datatable');
    Route::get('/', [TaskController::class, 'index'])->name('task.index');
    Route::get('/create', [TaskController::class, 'create'])->name('task.create');
    Route::get('/edit/{id}', [TaskController::class, 'edit'])->name('task.edit');
});
