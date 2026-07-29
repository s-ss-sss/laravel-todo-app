<form method="POST" action="{{ route('login') }}">
    @csrf

    <div>
        <label for="email">メールアドレス</label>
        <input
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            required
            autofocus
        >

        @error('email')
        <p>{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label for="password">パスワード</label>
        <input
            id="password"
            type="password"
            name="password"
            required
        >
    </div>

    <div>
        <label>
            <input type="checkbox" name="remember">
            ログイン状態を保持する
        </label>
    </div>

    <button type="submit">ログイン</button>
</form>

<a href="{{ route('register') }}">ユーザー登録へ</a>
