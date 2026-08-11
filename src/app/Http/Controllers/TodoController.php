<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * ログインユーザーのTodo一覧を表示
     */
    public function index(SearchTodoRequest $request): View
    {
        $filters = $request->validated();
        $query = $request->user()->todos();

        if (! empty($filters['keyword'])) {
            $keyword = $filters['keyword'];

            $query->where(function ($query) use ($keyword) {
                $query
                    ->where('title', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            });
        }

        if (($filters['status'] ?? null) === 'completed') {
            $query->where('is_completed', true);
        }

        if (($filters['status'] ?? null) === 'incomplete') {
            $query->where('is_completed', false);
        }

        if (! empty($filters['due_date'])) {
            $query->whereDate('due_date', $filters['due_date']);
        }

        $todos = $query->get();

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

    /**
     * Todo編集画面を表示
     */
    public function edit(Todo $todo): View
    {
        Gate::authorize('update', $todo);

        return view('todos.edit', compact('todo'));
    }

    /**
     * Todoを更新
     */
    public function update(
        UpdateTodoRequest $request,
        Todo $todo
    ): RedirectResponse {
        $todo->update($request->validated());

        return redirect()
            ->route('todos.show', $todo);
    }

    /**
     * Todoを論理削除
     */
    public function destroy(Todo $todo): RedirectResponse
    {
        Gate::authorize('delete', $todo);

        $todo->delete();

        return redirect()
            ->route('todos.index');
    }

    /**
     * Todoの完了状態を切り替え
     */
    public function toggleCompletion(Todo $todo): RedirectResponse
    {
        Gate::authorize('update', $todo);

        $todo->is_completed = ! $todo->is_completed;
        $todo->save();

        return redirect()
            ->route('todos.show', $todo);
    }
}
