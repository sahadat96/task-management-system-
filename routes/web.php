<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;    

// Route::get('/', function () {
//     return view('welcome');
// });`


Route::get('/', [TaskController::class, 'index'])->name('tasks.index');