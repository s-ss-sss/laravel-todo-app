<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoShowTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodo詳細画面からログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_todo_show_to_login(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->get("/todos/{$todo->id}");

        $response->assertRedirect(route('login'));
    }

    /**
     * Todoの所有者が詳細画面を表示できることを確認
     */
    public function test_owner_can_view_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '詳細表示するTodo',
                'description' => 'Todoの詳しい説明',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.show', $todo));

        $response->assertOk();
        $response->assertViewIs('todos.show');
        $response->assertViewHas(
            'todo',
            fn (Todo $viewTodo) => $viewTodo->is($todo)
        );
        $response->assertSeeText('詳細表示するTodo');
        $response->assertSeeText('Todoの詳しい説明');
    }

    /**
     * 他人のTodo詳細画面を表示できないことを確認
     */
    public function test_users_cannot_view_other_users_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.show', $todo));

        $response->assertForbidden();
    }

    /**
     * 論理削除されたTodoは詳細画面を表示できないことを確認
     */
    public function test_deleted_todo_cannot_be_viewed(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create();

        $todo->delete();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.show', $todo));

        $response->assertNotFound();
    }
}
