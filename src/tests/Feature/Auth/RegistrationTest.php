<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーが登録画面を表示できることを確認
     */
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get(route('register'));

        $response->assertOk();
        $response->assertViewIs('auth.register');
    }

    /**
     * 正しい入力でユーザー登録できログイン状態になることを確認
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $this->assertAuthenticated();

        $response->assertRedirect('/dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
        ]);

        // 保存されたハッシュが入力パスワードと一致することを確認
        $user = User::where('email', 'test@example.com')->firstOrFail();

        $this->assertTrue(
            Hash::check('password123', $user->password)
        );
    }

    /**
     * パスワード確認が一致しない場合は登録できないことを確認
     */
    public function test_registration_fails_when_password_confirmation_does_not_match(): void
    {
        $response = $this->post(route('register'), [
            'name' => 'テストユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'different-password',
        ]);

        $response->assertSessionHasErrors('password');

        $this->assertGuest();

        $this->assertDatabaseMissing('users', [
            'email' => 'test@example.com',
        ]);
    }

    /**
     * 登録済みメールアドレスではユーザー登録できないことを確認
     */
    public function test_registration_fails_when_email_is_already_registered(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post(route('register'), [
            'name' => '別のユーザー',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertSessionHasErrors('email');

        $this->assertGuest();

        $this->assertDatabaseCount('users', 1);
    }
}
