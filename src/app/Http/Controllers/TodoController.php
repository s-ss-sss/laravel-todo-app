<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchTodoRequest;
use App\Http\Requests\StoreTodoRequest;
use App\Http\Requests\UpdateTodoRequest;
use App\Models\Todo;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

        $todos = $query
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate(10)
            ->withQueryString();

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
        $todo = $request->user()
            ->todos()
            ->make($request->validated());

        $todo->sort_order = (
            (int) $request->user()->todos()->max('sort_order')
        ) + 1;

        $todo->save();

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todoを登録しました。');
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
            ->route('todos.show', $todo)
            ->with('success', 'Todoを更新しました。');
    }

    /**
     * Todoを論理削除
     */
    public function destroy(Todo $todo): RedirectResponse
    {
        Gate::authorize('delete', $todo);

        $todo->delete();

        return redirect()
            ->route('todos.index')
            ->with('success', 'Todoを削除しました。');
    }

    /**
     * Todoの完了状態を切り替え
     */
    public function toggleCompletion(Todo $todo): RedirectResponse
    {
        Gate::authorize('update', $todo);

        $todo->is_completed = ! $todo->is_completed;
        $todo->save();

        $message = $todo->is_completed
            ? 'Todoを完了にしました。'
            : 'Todoを未完了に戻しました。';

        return redirect()
            ->route('todos.show', $todo)
            ->with('success', $message);
    }

    /**
     * Todoを1つ上へ移動
     */
    public function moveUp(
        Request $request,
        Todo $todo
    ): RedirectResponse {
        Gate::authorize('update', $todo);

        $previousTodo = $request->user()
            ->todos()
            ->where('sort_order', '<', $todo->sort_order)
            ->orderByDesc('sort_order')
            ->first();

        if ($previousTodo === null) {
            return redirect()
                ->route('todos.index');
        }

        DB::transaction(function () use ($todo, $previousTodo) {
            $currentOrder = $todo->sort_order;

            $todo->sort_order = $previousTodo->sort_order;
            $todo->save();

            $previousTodo->sort_order = $currentOrder;
            $previousTodo->save();
        });

        return redirect()
            ->route('todos.index');
    }

    /**
     * Todoを1つ下へ移動
     */
    public function moveDown(
        Request $request,
        Todo $todo
    ): RedirectResponse {
        Gate::authorize('update', $todo);

        $nextTodo = $request->user()
            ->todos()
            ->where('sort_order', '>', $todo->sort_order)
            ->orderBy('sort_order')
            ->first();

        if ($nextTodo === null) {
            return redirect()
                ->route('todos.index');
        }

        DB::transaction(function () use ($todo, $nextTodo) {
            $currentOrder = $todo->sort_order;

            $todo->sort_order = $nextTodo->sort_order;
            $todo->save();

            $nextTodo->sort_order = $currentOrder;
            $nextTodo->save();
        });

        return redirect()
            ->route('todos.index');
    }

    /**
     * ログインユーザーの削除済みTodo一覧を表示
     */
    public function trash(Request $request): View
    {
        $todos = $request->user()
            ->todos()
            ->onlyTrashed()
            ->latest('deleted_at')
            ->paginate(10);

        return view('todos.trash', compact('todos'));
    }

    /**
     * 削除済みTodoを通常一覧の末尾へ復元
     */
    public function restore(
        Request $request,
        int $todo
    ): RedirectResponse {
        $trashedTodo = $request->user()
            ->todos()
            ->onlyTrashed()
            ->findOrFail($todo);

        $trashedTodo->sort_order = (
            (int) $request->user()
                ->todos()
                ->max('sort_order')
        ) + 1;

        $trashedTodo->restore();

        return redirect()
            ->route('todos.trash')
            ->with('success', 'Todoを復元しました。');
    }

    /**
     * 削除済みTodoをデータベースから完全削除
     */
    public function forceDelete(
        Request $request,
        int $todo
    ): RedirectResponse {
        $trashedTodo = $request->user()
            ->todos()
            ->onlyTrashed()
            ->findOrFail($todo);

        $trashedTodo->forceDelete();

        return redirect()
            ->route('todos.trash')
            ->with('success', 'Todoを完全に削除しました。');
    }

    /**
     * ログインユーザーのゴミ箱を空にする
     */
    public function emptyTrash(Request $request): RedirectResponse
    {
        $request->user()
            ->todos()
            ->onlyTrashed()
            ->forceDelete();

        return redirect()
            ->route('todos.trash')
            ->with('success', 'ゴミ箱を空にしました。');
    }
}
