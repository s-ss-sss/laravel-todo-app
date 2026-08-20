<?php

use App\Http\Controllers\TodoController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/todos')
    ->name('home');

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

Route::get('/todos/trash', [TodoController::class, 'trash'])
    ->middleware('auth')
    ->name('todos.trash');

Route::delete('/todos/trash', [TodoController::class, 'emptyTrash'])
    ->middleware('auth')
    ->name('todos.trash.empty');

Route::patch('/todos/trash/{todo}/restore', [TodoController::class, 'restore'])
    ->middleware('auth')
    ->name('todos.restore');

Route::delete('/todos/trash/{todo}', [TodoController::class, 'forceDelete'])
    ->middleware('auth')
    ->name('todos.force-delete');

Route::get('/todos/{todo}', [TodoController::class, 'show'])
    ->middleware('auth')
    ->name('todos.show');

Route::get('/todos/{todo}/edit', [TodoController::class, 'edit'])
    ->middleware('auth')
    ->name('todos.edit');

Route::put('/todos/{todo}', [TodoController::class, 'update'])
    ->middleware('auth')
    ->name('todos.update');

Route::patch('/todos/{todo}/completion', [TodoController::class, 'toggleCompletion'])
    ->middleware('auth')
    ->name('todos.toggle-completion');

Route::patch('/todos/{todo}/move-up', [TodoController::class, 'moveUp'])
    ->middleware('auth')
    ->name('todos.move-up');

Route::patch('/todos/{todo}/move-down', [TodoController::class, 'moveDown'])
    ->middleware('auth')
    ->name('todos.move-down');

Route::delete('/todos/{todo}', [TodoController::class, 'destroy'])
    ->middleware('auth')
    ->name('todos.destroy');
