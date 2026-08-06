<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTodoRequest;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * ログインユーザーのTodo一覧を表示
     */
    public function index(Request $request): View
    {
        $todos = $request->user()->todos()->get();

        return view('todos.index', compact('todos'));
    }

    /**
     * Todo登録画面を表示
     */
    public function create(): View
    {
        return view('todos.create');
    }

    /**
     * Todoを登録
     */
    public function store(StoreTodoRequest $request): RedirectResponse
    {
        $request->user()
            ->todos()
            ->create($request->validated());

        return redirect()
            ->route('todos.index');
    }

    /**
     * Todo詳細画面を表示
     */
    public function show(Todo $todo): View
    {
        Gate::authorize('view', $todo);

        return view('todos.show', compact('todo'));
    }
}
