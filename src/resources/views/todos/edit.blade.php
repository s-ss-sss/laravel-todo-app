@extends('layouts.app')

@section('title', 'Todo編集')

@section('content')
    <section class="p-todo-detail">
        <header class="p-todo-detail__header">
            <p class="p-todo-detail__eyebrow">EDIT TASK</p>
            <h1 class="p-todo-detail__title">Todo編集</h1>
        </header>

        <div class="c-card p-todo-detail__body">
            <form
                class="c-form"
                method="POST"
                action="{{ route('todos.update', $todo) }}"
            >
                @csrf
                @method('PUT')

                <div class="c-form__group">
                    <label class="c-form__label" for="title">
                        タイトル
                    </label>

                    <input
                        class="c-form__control"
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title', $todo->title) }}"
                        required
                        autofocus
                    >

                    @error('title')
                        <p class="c-form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="description">
                        説明
                    </label>

                    <textarea
                        class="c-form__control"
                        id="description"
                        name="description"
                    >{{ old('description', $todo->description) }}</textarea>

                    @error('description')
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
                        value="{{ old('due_date', $todo->due_date?->format('Y-m-d')) }}"
                    >

                    @error('due_date')
                        <p class="c-form__error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="c-form__actions p-todo-detail__form-actions">
                    <button
                        class="c-button c-button--primary"
                        type="submit"
                    >
                        更新
                    </button>

                    <a
                        class="c-button c-button--secondary"
                        href="{{ route('todos.show', $todo) }}"
                    >
                        Todo詳細へ戻る
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
