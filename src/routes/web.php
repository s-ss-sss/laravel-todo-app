<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::view('/dashboard', 'dashboard')
    ->middleware('auth')
    ->name('dashboard');

Route::get('/todos', [TodoController::class, 'index'])
    ->middleware('auth')
    ->name('todos.index');

Route::get('/todos/create', [TodoController::class, 'create'])
    ->middleware('auth')
    ->name('todos.create');

Route::post('/todos', [TodoController::class, 'store'])
    ->middleware('auth')
    ->name('todos.store');
