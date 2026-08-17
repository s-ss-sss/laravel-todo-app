@extends('layouts.app')

@section('title', 'Todo登録')

@section('content')
    <section class="p-todo-detail">
        <header class="p-todo-detail__header">
            <p class="p-todo-detail__eyebrow">NEW TASK</p>
            <h1 class="p-todo-detail__title">Todo登録</h1>
        </header>

        <div class="c-card p-todo-detail__body">
            <form
                class="c-form"
                method="POST"
                action="{{ route('todos.store') }}"
            >
                @csrf
                @if ($errors->any())
                    <div
                        class="c-form-errors"
                        role="alert"
                        aria-labelledby="form-errors-title"
                    >
                        <p
                            class="c-form-errors__title"
                            id="form-errors-title"
                        >
                            入力内容を確認してください。
                        </p>

                        <ul class="c-form-errors__list">
                            @foreach ($errors->all() as $error)
                                <li class="c-form-errors__item">
                                    {{ $error }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="c-form__group">
                    <label class="c-form__label" for="title">
                        タイトル
                        <span class="c-form__required">必須</span>
                    </label>

                    <input
                        class="c-form__control @error('title') is-invalid @enderror"
                        id="title"
                        name="title"
                        type="text"
                        value="{{ old('title') }}"
                        aria-invalid="{{ $errors->has('title') ? 'true' : 'false' }}"
                        @error('title')
                            aria-describedby="title-error"
                        @enderror
                        required
                        autofocus
                    >

                    @error('title')
                        <p
                            class="c-form__error"
                            id="title-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="description">
                        説明
                        <span class="c-form__optional">任意</span>
                    </label>

                    <textarea
                        class="c-form__control @error('description') is-invalid @enderror"
                        id="description"
                        name="description"
                        aria-invalid="{{ $errors->has('description') ? 'true' : 'false' }}"
                        @error('description')
                            aria-describedby="description-error"
                        @enderror
                    >{{ old('description') }}</textarea>

                    @error('description')
                        <p
                            class="c-form__error"
                            id="description-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="due_date">
                        期限日
                        <span class="c-form__optional">任意</span>
                    </label>

                    <input
                        class="c-form__control @error('due_date') is-invalid @enderror"
                        id="due_date"
                        name="due_date"
                        type="date"
                        value="{{ old('due_date') }}"
                        aria-invalid="{{ $errors->has('due_date') ? 'true' : 'false' }}"
                        @error('due_date')
                            aria-describedby="due-date-error"
                        @enderror
                    >

                    @error('due_date')
                        <p
                            class="c-form__error"
                            id="due-date-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__actions p-todo-detail__form-actions">
                    <button
                        class="c-button c-button--primary"
                        type="submit"
                    >
                        登録
                    </button>

                    <a
                        class="c-button c-button--secondary"
                        href="{{ route('todos.index') }}"
                    >
                        Todo一覧へ戻る
                    </a>
                </div>
            </form>
        </div>
    </section>
@endsection
