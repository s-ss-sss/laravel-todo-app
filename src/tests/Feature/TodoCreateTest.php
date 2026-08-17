<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCreateTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodo登録画面からログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_todo_create_to_login(): void
    {
        $response = $this->get(route('todos.create'));

        $response->assertRedirect(route('login'));
    }

    /**
     * ログインユーザーがTodo登録画面を表示できることを確認
     */
    public function test_authenticated_users_can_view_todo_create(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.create'));

        $response->assertOk();
        $response->assertViewIs('todos.create');
    }

    /**
     * 未ログインユーザーはTodoを登録できないことを確認
     */
    public function test_guests_cannot_create_todo(): void
    {
        $response = $this->post(route('todos.store'), [
            'title' => '未ログインユーザーのTodo',
            'description' => null,
            'due_date' => null,
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseCount('todos', 0);
    }

    /**
     * ログインユーザーがTodoを登録できることを確認
     */
    public function test_authenticated_users_can_create_todo(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('todos.store'), [
                'title' => 'Laravelのテストを勉強する',
                'description' => 'Featureテストについて確認する',
                'due_date' => '2026-08-31',
            ]);

        $response->assertRedirect(route('todos.index'));

        $response->assertSessionHas(
            'success',
            'Todoを登録しました。'
        );

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'Laravelのテストを勉強する',
            'description' => 'Featureテストについて確認する',
        ]);

        $todo = Todo::where('user_id', $user->id)
            ->where('title', 'Laravelのテストを勉強する')
            ->firstOrFail();

        $this->assertSame(
            '2026-08-31',
            $todo->due_date->format('Y-m-d')
        );
    }

    /**
     * タイトルが空の場合はTodoを登録できないことを確認
     */
    public function test_title_is_required_to_create_todo(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('todos.create'))
            ->post(route('todos.store'), [
                'title' => '',
                'description' => '説明',
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.create'));

        $response->assertSessionHasErrors([
            'title' => 'タイトルは必須です。',
        ]);

        $this->assertDatabaseCount('todos', 0);
    }

    /**
     * タイトルが255文字を超える場合はTodoを登録できないことを確認
     */
    public function test_title_must_not_exceed_255_characters(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('todos.create'))
            ->post(route('todos.store'), [
                'title' => str_repeat('a', 256),
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.create'));

        $response->assertSessionHasErrors([
            'title' => 'タイトルは255文字以内で入力してください。',
        ]);

        $this->assertDatabaseCount('todos', 0);
    }

    /**
     * 期限日が不正な日付の場合はTodoを登録できないことを確認
     */
    public function test_due_date_must_be_a_valid_date(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from(route('todos.create'))
            ->post(route('todos.store'), [
                'title' => '期限日のテスト',
                'description' => null,
                'due_date' => 'invalid-date',
            ]);

        $response->assertRedirect(route('todos.create'));

        $response->assertSessionHasErrors([
            'due_date' => '期限日は正しい日付で入力してください。',
        ]);

        $this->assertDatabaseCount('todos', 0);
    }

    /**
     * 任意項目が空でもTodoを登録できることを確認
     */
    public function test_todo_can_be_created_without_optional_fields(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('todos.store'), [
                'title' => 'タイトルだけのTodo',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'タイトルだけのTodo',
            'description' => null,
            'due_date' => null,
        ]);
    }

    /**
     * Todoはリクエストで指定されたユーザーではなくログインユーザーに紐づくことを確認
     */
    public function test_todo_is_assigned_to_authenticated_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->post(route('todos.store'), [
                'title' => 'ログインユーザーのTodo',
                'description' => null,
                'due_date' => null,
                'user_id' => $otherUser->id,
            ]);

        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => 'ログインユーザーのTodo',
        ]);

        $this->assertDatabaseMissing('todos', [
            'user_id' => $otherUser->id,
            'title' => 'ログインユーザーのTodo',
        ]);
    }

    /**
     * Todo登録後に成功メッセージが表示されることを確認
     */
    public function test_success_message_is_displayed_after_creating_todo(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->followingRedirects()
            ->post(route('todos.store'), [
                'title' => 'フラッシュメッセージを確認する',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertOk();
        $response->assertSee('Todoを登録しました。');
    }
}
