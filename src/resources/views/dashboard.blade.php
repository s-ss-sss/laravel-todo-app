<h1>ダッシュボード</h1>

<p>
    ログインユーザー：
    {{ auth()->user()->name }}
</p>

<p>
    メールアドレス：
    {{ auth()->user()->email }}
</p>

<form method="POST" action="{{ route('logout') }}">
    @csrf

    <button type="submit">ログアウト</button>
</form>
