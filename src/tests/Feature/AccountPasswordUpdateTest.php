<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AccountPasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しい現在のパスワードで新しいパスワードへ変更できることを確認
     */
    public function test_user_can_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHas(
            'status',
            'password-updated'
        );

        $this->assertTrue(
            Hash::check(
                'new-password123',
                $user->fresh()->password
            )
        );
    }

    /**
     * 現在のパスワードが間違っている場合は変更できないことを確認
     */
    public function test_current_password_must_be_correct(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHasErrors(
            [
                'current_password' => '現在のパスワードが正しくありません。',
            ],
            null,
            'updatePassword'
        );

        $this->assertTrue(
            Hash::check(
                'password',
                $user->fresh()->password
            )
        );
    }

    /**
     * パスワード変更時の必須バリデーションを確認
     */
    public function test_current_and_new_password_are_required(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-password.update'), [
                'current_password' => '',
                'password' => '',
                'password_confirmation' => '',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHasErrors(
            [
                'current_password' => '現在のパスワードは必須です。',
                'password' => '新しいパスワードは必須です。',
            ],
            null,
            'updatePassword'
        );
    }

    /**
     * 新しいパスワードが8文字以上必要なことを確認
     */
    public function test_new_password_must_be_at_least_eight_characters(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'short',
                'password_confirmation' => 'short',
            ]);

        $response->assertSessionHasErrors(
            [
                'password' => '新しいパスワードは8文字以上で入力してください。',
            ],
            null,
            'updatePassword'
        );

        $this->assertTrue(
            Hash::check(
                'password',
                $user->fresh()->password
            )
        );
    }

    /**
     * 新しいパスワードと確認欄が一致する必要があることを確認
     */
    public function test_new_password_confirmation_must_match(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-password.update'), [
                'current_password' => 'password',
                'password' => 'new-password123',
                'password_confirmation' => 'different-password',
            ]);

        $response->assertSessionHasErrors(
            [
                'password' => '新しいパスワード確認が一致していません。',
            ],
            null,
            'updatePassword'
        );

        $this->assertTrue(
            Hash::check(
                'password',
                $user->fresh()->password
            )
        );
    }

    /**
     * 未ログインユーザーはパスワードを変更できないことを確認
     */
    public function test_guests_cannot_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this->put(
            route('user-password.update'),
            [
                'current_password' => 'password',
                'password' => 'new-password123',
                'password_confirmation' => 'new-password123',
            ]
        );

        $response->assertRedirect(route('login'));

        $this->assertTrue(
            Hash::check(
                'password',
                $user->fresh()->password
            )
        );
    }
}
