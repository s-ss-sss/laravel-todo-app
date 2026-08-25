<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountProfileUpdateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーがログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_account_to_login(): void
    {
        $response = $this->get(route('account.show'));

        $response->assertRedirect(route('login'));
    }

    /**
     * ログインユーザーがアカウント画面を表示できることを確認
     */
    public function test_authenticated_users_can_view_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('account.show'));

        $response->assertOk();
        $response->assertViewIs('account.show');
    }

    /**
     * プロフィール情報が更新できることを確認
     */
    public function test_user_can_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => '変更前',
            'email' => 'before@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-profile-information.update'), [
                'name' => '変更後',
                'email' => 'after@example.com',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHas(
            'status',
            'profile-information-updated'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '変更後',
            'email' => 'after@example.com',
        ]);
    }

    /**
     * プロフィール情報の必須バリデーションを確認
     */
    public function test_name_and_email_are_required_for_profile_update(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-profile-information.update'), [
                'name' => '',
                'email' => '',
            ]);

        $response->assertRedirect(route('account.show'));

        $response->assertSessionHasErrors(
            [
                'name' => 'ユーザー名は必須です。',
                'email' => 'メールアドレスは必須です。',
            ],
            null,
            'updateProfileInformation'
        );
    }

    /**
     * メールアドレスの重複バリデーションを確認
     */
    public function test_email_must_be_unique_for_profile_update(): void
    {
        $otherUser = User::factory()->create([
            'email' => 'used@example.com',
        ]);

        $user = User::factory()->create([
            'email' => 'current@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-profile-information.update'), [
                'name' => $user->name,
                'email' => $otherUser->email,
            ]);

        $response->assertSessionHasErrors(
            [
                'email' => 'このメールアドレスはすでに登録されています。',
            ],
            null,
            'updateProfileInformation'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => 'current@example.com',
        ]);
    }

    /**
     * プロフィール更新時にメールアドレスが正しい形式であることを確認
     */
    public function test_email_must_be_valid_for_profile_update(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-profile-information.update'), [
                'name' => $user->name,
                'email' => 'invalid-email',
            ]);

        $response->assertSessionHasErrors(
            [
                'email' => 'メールアドレスは正しい形式で入力してください。',
            ],
            null,
            'updateProfileInformation'
        );

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * 現在と同じメールアドレスでもプロフィールを更新できることを確認
     */
    public function test_user_can_update_profile_with_current_email(): void
    {
        $user = User::factory()->create([
            'name' => '変更前',
            'email' => 'user@example.com',
        ]);

        $response = $this
            ->actingAs($user)
            ->from(route('account.show'))
            ->put(route('user-profile-information.update'), [
                'name' => '変更後',
                'email' => 'user@example.com',
            ]);

        $response->assertRedirect(route('account.show'));
        $response->assertSessionDoesntHaveErrors();

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '変更後',
            'email' => 'user@example.com',
        ]);
    }

    /**
     * 未ログインユーザーはプロフィール情報を更新できないことを確認
     */
    public function test_guests_cannot_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => '変更前',
            'email' => 'before@example.com',
        ]);

        $response = $this->put(
            route('user-profile-information.update'),
            [
                'name' => '変更後',
                'email' => 'after@example.com',
            ]
        );

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => '変更前',
            'email' => 'before@example.com',
        ]);
    }
}
