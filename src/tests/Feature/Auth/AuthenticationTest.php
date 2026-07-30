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

        $response->assertRedirect('/dashboard');
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

        $response->assertSessionHasErrors('email');
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
     * 未ログインユーザーはダッシュボードからログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_dashboard_to_login(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * ログインユーザーがダッシュボードを表示できることを確認
     */
    public function test_authenticated_users_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('dashboard'));

        $response->assertOk();
        $response->assertViewIs('dashboard');
        $response->assertSee($user->name);
        $response->assertSee($user->email);
    }
}
