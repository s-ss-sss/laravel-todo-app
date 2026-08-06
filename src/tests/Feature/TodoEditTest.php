<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoEditTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodo編集画面からログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_todo_edit_to_login(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->get("/todos/{$todo->id}/edit");

        $response->assertRedirect(route('login'));
    }

    /**
     * Todoの所有者が編集画面を表示できることを確認
     */
    public function test_owner_can_view_todo_edit(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '編集前のタイトル',
                'description' => '編集前の説明',
                'due_date' => '2026-08-31',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.edit', $todo));

        $response->assertOk();
        $response->assertViewIs('todos.edit');
        $response->assertViewHas(
            'todo',
            fn (Todo $viewTodo) => $viewTodo->is($todo)
        );
        $response->assertSee('編集前のタイトル');
        $response->assertSee('編集前の説明');
    }

    /**
     * 他人のTodo編集画面を表示できないことを確認
     */
    public function test_users_cannot_view_other_users_todo_edit(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.edit', $todo));

        $response->assertForbidden();
    }

    /**
     * Todoの所有者がTodoを更新できることを確認
     */
    public function test_owner_can_update_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '編集前のタイトル',
                'description' => '編集前の説明',
                'due_date' => '2026-08-31',
            ]);

        $response = $this
            ->actingAs($user)
            ->put(route('todos.update', $todo), [
                'title' => '編集後のタイトル',
                'description' => '編集後の説明',
                'due_date' => '2026-09-30',
            ]);

        $response->assertRedirect(route('todos.show', $todo));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'user_id' => $user->id,
            'title' => '編集後のタイトル',
            'description' => '編集後の説明',
        ]);

        $todo->refresh();

        $this->assertSame(
            '2026-09-30',
            $todo->due_date->format('Y-m-d')
        );
    }

    /**
     * 未ログインユーザーはTodoを更新できないことを確認
     */
    public function test_guests_cannot_update_todo(): void
    {
        $todo = Todo::factory()->create([
            'title' => '変更前のタイトル',
        ]);

        $response = $this->put(route('todos.update', $todo), [
            'title' => '変更後のタイトル',
            'description' => null,
            'due_date' => null,
        ]);

        $response->assertRedirect(route('login'));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '変更前のタイトル',
        ]);
    }

    /**
     * 他人のTodoを更新できないことを確認
     */
    public function test_users_cannot_update_other_users_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create([
                'title' => '変更前のタイトル',
            ]);

        $response = $this
            ->actingAs($user)
            ->put(route('todos.update', $todo), [
                'title' => '不正に変更したタイトル',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertForbidden();

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'user_id' => $otherUser->id,
            'title' => '変更前のタイトル',
        ]);

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
            'title' => '不正に変更したタイトル',
        ]);
    }

    /**
     * 更新時もタイトルが必須であることを確認
     */
    public function test_title_is_required_to_update_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '変更前のタイトル',
            ]);

        $response = $this
            ->actingAs($user)
            ->from(route('todos.edit', $todo))
            ->put(route('todos.update', $todo), [
                'title' => '',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.edit', $todo));
        $response->assertSessionHasErrors('title');

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'title' => '変更前のタイトル',
        ]);
    }

    /**
     * 説明と期限日を空に更新できることを確認
     */
    public function test_optional_fields_can_be_cleared_when_updating_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Todo',
                'description' => '削除する説明',
                'due_date' => '2026-08-31',
            ]);

        $response = $this
            ->actingAs($user)
            ->put(route('todos.update', $todo), [
                'title' => 'Todo',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.show', $todo));

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'description' => null,
            'due_date' => null,
        ]);
    }
}
