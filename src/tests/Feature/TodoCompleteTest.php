<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoCompleteTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodoの完了状態を変更できないことを確認
     */
    public function test_guests_cannot_toggle_todo_completion(): void
    {
        $todo = Todo::factory()->create([
            'is_completed' => false,
        ]);

        $response = $this->patch(
            "/todos/{$todo->id}/completion"
        );

        $response->assertRedirect(route('login'));

        $this->assertFalse(
            $todo->refresh()->is_completed
        );
    }

    /**
     * Todoの所有者が未完了のTodoを完了にできることを確認
     */
    public function test_owner_can_mark_todo_as_completed(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.toggle-completion', $todo));

        $response->assertRedirect(route('todos.show', $todo));

        $response->assertSessionHas(
            'success',
            'Todoを完了にしました。'
        );

        $this->assertTrue(
            $todo->refresh()->is_completed
        );
    }

    /**
     * Todoの所有者が完了済みTodoを未完了に戻せることを確認
     */
    public function test_owner_can_mark_completed_todo_as_incomplete(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'is_completed' => true,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.toggle-completion', $todo));

        $response->assertRedirect(route('todos.show', $todo));

        $response->assertSessionHas(
            'success',
            'Todoを未完了に戻しました。'
        );

        $this->assertFalse(
            $todo->refresh()->is_completed
        );
    }

    /**
     * 他人のTodoの完了状態を変更できないことを確認
     */
    public function test_users_cannot_toggle_other_users_todo_completion(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create([
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.toggle-completion', $todo));

        $response->assertForbidden();

        $this->assertFalse(
            $todo->refresh()->is_completed
        );
    }
}
