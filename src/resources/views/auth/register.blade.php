@extends('layouts.app')

@section('title', 'ユーザー登録')

@section('content')
    <div class="p-auth">
        <section class="c-card p-auth__card">
            <header class="p-auth__header">
                <p class="p-auth__eyebrow">CREATE ACCOUNT</p>
                <h1 class="p-auth__title">ユーザー登録</h1>
            </header>

            <form class="c-form" method="POST" action="{{ route('register') }}">
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
                    <label class="c-form__label" for="name">
                        ユーザー名
                        <span class="c-form__required">必須</span>
                    </label>
                    <input
                        class="c-form__control @error('name') is-invalid @enderror"
                        id="name"
                        name="name"
                        type="text"
                        value="{{ old('name') }}"
                        autocomplete="name"
                        aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                        @error('name')
                            aria-describedby="name-error"
                        @enderror
                        required
                        autofocus
                    >

                    @error('name')
                        <p
                            class="c-form__error"
                            id="name-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="email">
                        メールアドレス
                        <span class="c-form__required">必須</span>
                    </label>
                    <input
                        class="c-form__control @error('email') is-invalid @enderror"
                        id="email"
                        name="email"
                        type="email"
                        value="{{ old('email') }}"
                        autocomplete="email"
                        aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                        @error('email')
                            aria-describedby="email-error"
                        @enderror
                        required
                    >

                    @error('email')
                        <p
                            class="c-form__error"
                            id="email-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="password">
                        パスワード
                        <span class="c-form__required">必須</span>
                    </label>
                    <input
                        class="c-form__control @error('password') is-invalid @enderror"
                        id="password"
                        name="password"
                        type="password"
                        autocomplete="new-password"
                        aria-invalid="{{ $errors->has('password') ? 'true' : 'false' }}"
                        @error('password')
                            aria-describedby="password-error"
                        @enderror
                        required
                    >

                    @error('password')
                        <p
                            class="c-form__error"
                            id="password-error"
                        >
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="c-form__group">
                    <label class="c-form__label" for="password_confirmation">
                        パスワード確認
                        <span class="c-form__required">必須</span>
                    </label>
                    <input
                        class="c-form__control"
                        id="password_confirmation"
                        name="password_confirmation"
                        type="password"
                        autocomplete="new-password"
                        required
                    >
                </div>

                <button
                    class="c-button c-button--primary"
                    type="submit"
                >
                    登録
                </button>
            </form>

            <p class="p-auth__footer">
                すでにアカウントをお持ちの方
                <a
                    class="p-auth__footer-link"
                    href="{{ route('login') }}"
                >
                    ログイン画面へ
                </a>
            </p>
        </section>
    </div>
@endsection
