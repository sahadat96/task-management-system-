<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;


Route::post('/tasks', [TaskController::class, 'store']);
Route::get('/get-tasks', [TaskController::class, 'getTasks']);