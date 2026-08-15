@extends('layouts.app')

@section('title', 'Todo詳細')

@section('content')
    <section class="p-todo-detail">
        <header class="p-todo-detail__header">
            <p class="p-todo-detail__eyebrow">TASK DETAIL</p>
            <h1 class="p-todo-detail__title">Todo詳細</h1>
        </header>

        <div class="c-card p-todo-detail__body">
            <dl class="p-todo-detail__list">
                <dt class="p-todo-detail__term">タイトル</dt>
                <dd class="p-todo-detail__description">
                    {{ $todo->title }}
                </dd>

                <dt class="p-todo-detail__term">説明</dt>
                <dd class="p-todo-detail__description">
                    {{ $todo->description ?? '説明はありません。' }}
                </dd>

                <dt class="p-todo-detail__term">期限日</dt>
                <dd class="p-todo-detail__description">
                    {{ $todo->due_date?->format('Y/m/d') ?? '期限はありません。' }}
                </dd>

                <dt class="p-todo-detail__term">状態</dt>
                <dd class="p-todo-detail__description">
                    <span
                        class="c-status {{ $todo->is_completed ? 'c-status--completed' : 'c-status--incomplete' }}"
                    >
                        {{ $todo->is_completed ? '完了' : '未完了' }}
                    </span>
                </dd>
            </dl>

            <div class="p-todo-detail__actions">
                <form
                    method="POST"
                    action="{{ route('todos.toggle-completion', $todo) }}"
                >
                    @csrf
                    @method('PATCH')

                    <button
                        class="c-button c-button--primary"
                        type="submit"
                    >
                        {{ $todo->is_completed ? '未完了に戻す' : '完了にする' }}
                    </button>
                </form>

                <a
                    class="c-button c-button--secondary"
                    href="{{ route('todos.edit', $todo) }}"
                >
                    このTodoを編集
                </a>

                <a
                    class="c-button c-button--ghost"
                    href="{{ route('todos.index') }}"
                >
                    Todo一覧へ戻る
                </a>

                <form
                    class="p-todo-detail__danger-action"
                    method="POST"
                    action="{{ route('todos.destroy', $todo) }}"
                    onsubmit="return confirm('このTodoを削除しますか？')"
                >
                    @csrf
                    @method('DELETE')

                    <button
                        class="c-button c-button--danger"
                        type="submit"
                    >
                        このTodoを削除
                    </button>
                </form>
            </div>
        </div>
    </section>
@endsection
