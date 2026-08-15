@extends('layouts.app')

@section('title', 'Todo一覧')

@section('content')
    <div class="p-todo-list">
        <div class="p-todo-list__header">
            <div>
                <p class="p-todo-list__eyebrow">
                    TASKS
                </p>

                <h1 class="p-todo-list__title">
                    Todo一覧
                </h1>
            </div>

            <a
                class="c-button c-button--primary"
                href="{{ route('todos.create') }}"
            >
                新しいTodoを登録
            </a>
        </div>

        <div class="c-card c-card--flat p-todo-list__search">
            <form
                class="c-form p-todo-search"
                method="GET"
                action="{{ route('todos.index') }}"
            >
                <div class="p-todo-search__fields">
                    <div class="c-form__group">
                        <label class="c-form__label" for="keyword">
                            キーワード
                        </label>

                        <input
                            class="c-form__control"
                            id="keyword"
                            name="keyword"
                            type="search"
                            value="{{ request('keyword') }}"
                            placeholder="タイトル・説明から検索"
                        >

                        @error('keyword')
                            <p class="c-form__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="c-form__group">
                        <label class="c-form__label" for="status">
                            状態
                        </label>

                        <select
                            class="c-form__control"
                            id="status"
                            name="status"
                        >
                            <option value="">すべて</option>

                            <option
                                value="completed"
                                @selected(request('status') === 'completed')
                            >
                                完了
                            </option>

                            <option
                                value="incomplete"
                                @selected(request('status') === 'incomplete')
                            >
                                未完了
                            </option>
                        </select>

                        @error('status')
                            <p class="c-form__error">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="c-form__group">
                        <label class="c-form__label" for="due_date">
                            期限日
                        </label>

                        <input
                            class="c-form__control"
                            id="due_date"
                            name="due_date"
                            type="date"
                            value="{{ request('due_date') }}"
                        >

                        @error('due_date')
                            <p class="c-form__error">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="c-form__actions p-todo-search__actions">
                    <button
                        class="c-button c-button--primary"
                        type="submit"
                    >
                        検索
                    </button>

                    <a
                        class="c-button c-button--secondary"
                        href="{{ route('todos.index') }}"
                    >
                        条件をクリア
                    </a>
                </div>
            </form>
        </div>

        @if ($todos->isEmpty())
            @if (
                request()->filled('keyword')
                || request()->filled('status')
                || request()->filled('due_date')
            )
                <p class="c-empty-state">
                    検索条件に一致するTodoはありません。
                </p>
            @else
                <p class="c-empty-state">
                    Todoはまだありません。
                </p>
            @endif
        @else
            <ul class="p-todo-list__items">
                @foreach ($todos as $todo)
                    <li
                        class="p-todo-list__item {{ $todo->is_completed ? 'is-completed' : '' }}"
                    >
                        <div class="p-todo-list__content">
                            <div class="p-todo-list__item-header">
                                <a
                                    class="p-todo-list__item-title"
                                    href="{{ route('todos.show', $todo) }}"
                                >
                                    {{ $todo->title }}
                                </a>

                                <span
                                    class="c-status {{ $todo->is_completed ? 'c-status--completed' : 'c-status--incomplete' }}"
                                >
                                    {{ $todo->is_completed ? '完了' : '未完了' }}
                                </span>
                            </div>

                            <p class="p-todo-list__meta">
                                期限：{{ $todo->due_date?->format('Y/m/d') ?? '設定なし' }}
                            </p>
                        </div>

                        @if (
                            ! request()->filled('keyword')
                            && ! request()->filled('status')
                            && ! request()->filled('due_date')
                        )
                            <div class="p-todo-list__actions">
                                <form
                                    method="POST"
                                    action="{{ route('todos.move-up', $todo) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        class="c-button c-button--icon"
                                        type="submit"
                                        aria-label="{{ $todo->title }}を上へ移動"
                                        title="上へ移動"
                                    >
                                        ↑
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route('todos.move-down', $todo) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        class="c-button c-button--icon"
                                        type="submit"
                                        aria-label="{{ $todo->title }}を下へ移動"
                                        title="下へ移動"
                                    >
                                        ↓
                                    </button>
                                </form>
                            </div>
                        @endif
                    </li>
                @endforeach
            </ul>

            {{ $todos->links('vendor.pagination.default') }}
        @endif
    </div>
@endsection
