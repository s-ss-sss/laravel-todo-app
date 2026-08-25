<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーがログイン画面を表示できることを確認
     */
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get(route('login'));

        $response->assertOk();
        $response->assertViewIs('auth.login');
    }

    /**
     * 正しい認証情報でログインできることを確認
     */
    public function test_users_can_authenticate(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticatedAs($user);

        $response->assertRedirect(route('todos.index'));
    }

    /**
     * 不正なパスワードでログインできないことを確認
     */
    public function test_users_cannot_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);
    }

    /**
     * ログインユーザーがログアウトできることを確認
     */
    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('logout'));

        $this->assertGuest();

        $response->assertRedirect('/');
    }

    /**
     * ログイン情報が未入力の場合はログインできないことを確認
     */
    public function test_email_and_password_are_required_for_login(): void
    {
        $response = $this->post(route('login'), [
            'email' => '',
            'password' => '',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスは必須です。',
            'password' => 'パスワードは必須です。',
        ]);

        $this->assertGuest();
    }

    /**
     * 登録されていないメールアドレスではログインできないことを確認
     */
    public function test_users_cannot_authenticate_with_unknown_email(): void
    {
        $response = $this->post(route('login'), [
            'email' => 'unknown@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'メールアドレスまたはパスワードが正しくありません。',
        ]);

        $this->assertGuest();
    }
}
