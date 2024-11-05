<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return ['Laravel' => app()->version()];
});

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::apiResource('tasks', TaskController::class);
    Route::put('tasks/{task}/assign', [TaskController::class, 'assign']);
    Route::post('tasks/{task}/restore', [TaskController::class, 'restore']);
});

require __DIR__.'/auth.php';

