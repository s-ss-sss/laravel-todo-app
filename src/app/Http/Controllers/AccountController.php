<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteAccountRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    /**
     * ログインユーザーのアカウントを論理削除
     */
    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->delete();

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('success', 'アカウントを削除しました。');
    }
}
