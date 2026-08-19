@extends('layouts.app')

@section('title', 'ログイン')

@section('content')
    <div class="p-auth">
        <section class="c-card p-auth__card">
            <header class="p-auth__header">
                <p class="p-auth__eyebrow">WELCOME BACK</p>
                <h1 class="p-auth__title">ログイン</h1>
            </header>

            <form class="c-form" method="POST" action="{{ route('login') }}">
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
                        autofocus
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
                        autocomplete="current-password"
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

                <label class="c-form__check">
                    <input type="checkbox" name="remember">
                    ログイン状態を保持する
                </label>

                <button
                    class="c-button c-button--primary"
                    type="submit"
                >
                    ログイン
                </button>
            </form>

            <p class="p-auth__footer">
                アカウントをお持ちでない方
                <a
                    class="p-auth__footer-link"
                    href="{{ route('register') }}"
                >
                    ユーザー登録へ
                </a>
            </p>
        </section>
    </div>
@endsection
