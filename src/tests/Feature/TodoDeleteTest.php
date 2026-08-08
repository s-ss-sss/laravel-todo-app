<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoDeleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodoを削除できないことを確認
     */
    public function test_guests_cannot_delete_todo(): void
    {
        $todo = Todo::factory()->create();

        $response = $this->delete("/todos/{$todo->id}");

        $response->assertRedirect(route('login'));

        $this->assertNotSoftDeleted('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * Todoの所有者がTodoを論理削除できることを確認
     */
    public function test_owner_can_delete_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '削除するTodo',
            ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.destroy', $todo));

        $response->assertRedirect(route('todos.index'));

        $this->assertSoftDeleted('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * 他人のTodoを削除できないことを確認
     */
    public function test_users_cannot_delete_other_users_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create([
                'title' => '他人のTodo',
            ]);

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.destroy', $todo));

        $response->assertForbidden();

        $this->assertNotSoftDeleted('todos', [
            'id' => $todo->id,
        ]);
    }
}
