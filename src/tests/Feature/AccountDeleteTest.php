<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 正しい現在のパスワードでアカウントを論理削除できることを確認
     */
    public function test_user_can_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->delete(route('account.destroy'), [
                'current_password' => 'password',
            ]);

        $response->assertRedirect(route('login'));

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        $this->assertGuest();
    }

    /**
     * 間違ったパスワードでアカウントを論理削除できないことを確認
     */
    public function test_account_is_not_deleted_when_current_password_is_incorrect(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->delete(route('account.destroy'), [
                'current_password' => 'wrong-password',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHasErrors(
            [
                'current_password' => '現在のパスワードが正しくありません。',
            ],
            null,
            'deleteAccount'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);

        $this->assertAuthenticatedAs($user);
    }

    /**
     * パスワード未入力でアカウントを論理削除できないことを確認
     */
    public function test_current_password_is_required_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->delete(route('account.destroy'), [
                'current_password' => '',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHasErrors(
            [
                'current_password' => '現在のパスワードは必須です。',
            ],
            null,
            'deleteAccount'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * 未ログインユーザーでアカウントを論理削除できないことを確認
     */
    public function test_guests_cannot_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this->delete(route('account.destroy'), [
            'current_password' => 'password',
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    /**
     * アカウントを論理削除してもTodo情報は保持されることを確認
     */
    public function test_todos_are_preserved_when_account_is_deleted(): void
    {
        $user = User::factory()
            ->hasTodos(2)
            ->create();

        $response = $this
            ->actingAs($user)
            ->delete(route('account.destroy'), [
                'current_password' => 'password',
            ]);

        $response->assertRedirect(route('login'));

        $this->assertSoftDeleted('users', [
            'id' => $user->id,
        ]);

        $this->assertDatabaseCount('todos', 2);
    }
}
