<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route to fetch tasks
Route::get('/', [TaskController::class, 'index']);


Route::post('/add-task', [TaskController::class,'addTask'])->name('add.task');
Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('update.task');
Route::delete('/tasks-delete/{taskId}/', [TaskController::class,'delete'])->name('delete.task');
