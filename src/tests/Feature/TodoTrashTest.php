<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoTrashTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはゴミ箱を表示できないことを確認
     */
    public function test_guests_cannot_view_todo_trash(): void
    {
        $response = $this->get('/todos/trash');

        $response->assertRedirect(route('login'));
    }

    /**
     * ログインユーザーがゴミ箱を表示できることを確認
     */
    public function test_authenticated_users_can_view_todo_trash(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get('/todos/trash');

        $response->assertOk();
        $response->assertViewIs('todos.trash');
        $response->assertViewHas('todos');
    }

    /**
     * ゴミ箱には削除済みTodoだけが表示されることを確認
     */
    public function test_only_deleted_todos_are_displayed_in_trash(): void
    {
        $user = User::factory()->create();

        $activeTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '通常のTodo',
            ]);

        $deletedTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '削除済みTodo',
            ]);

        $deletedTodo->delete();

        $response = $this
            ->actingAs($user)
            ->get('/todos/trash');

        $response->assertOk();
        $response->assertSee('削除済みTodo');
        $response->assertDontSee('通常のTodo');
    }

    /**
     * 他ユーザーの削除済みTodoは表示されないことを確認
     */
    public function test_users_cannot_view_other_users_deleted_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '自分の削除済みTodo',
            ]);

        $otherTodo = Todo::factory()
            ->for($otherUser)
            ->create([
                'title' => '他ユーザーの削除済みTodo',
            ]);

        $ownTodo->delete();
        $otherTodo->delete();

        $response = $this
            ->actingAs($user)
            ->get('/todos/trash');

        $response->assertSee('自分の削除済みTodo');
        $response->assertDontSee('他ユーザーの削除済みTodo');
    }

    /**
     * 所有者が削除済みTodoを通常一覧の末尾へ復元できることを確認
     */
    public function test_owner_can_restore_deleted_todo_to_end_of_list(): void
    {
        $user = User::factory()->create();

        Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 5,
            ]);

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 1,
            ]);

        $todo->delete();

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.restore', $todo->id));

        $response->assertRedirect(route('todos.trash'));

        $response->assertSessionHas(
            'success',
            'Todoを復元しました。'
        );

        $this->assertNotSoftDeleted('todos', [
            'id' => $todo->id,
        ]);

        $this->assertDatabaseHas('todos', [
            'id' => $todo->id,
            'user_id' => $user->id,
            'sort_order' => 6,
            'deleted_at' => null,
        ]);
    }

    /**
     * 他ユーザーの削除済みTodoを復元できないことを確認
     */
    public function test_users_cannot_restore_other_users_deleted_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create();

        $todo->delete();

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.restore', $todo->id));

        $response->assertNotFound();

        $this->assertSoftDeleted('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * 所有者が削除済みTodoを完全削除できることを確認
     */
    public function test_owner_can_permanently_delete_todo(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create();

        $todo->delete();

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.force-delete', $todo->id));

        $response->assertRedirect(route('todos.trash'));

        $response->assertSessionHas(
            'success',
            'Todoを完全に削除しました。'
        );

        $this->assertDatabaseMissing('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * 他ユーザーの削除済みTodoを完全削除できないことを確認
     */
    public function test_users_cannot_permanently_delete_other_users_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create();

        $todo->delete();

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.force-delete', $todo->id));

        $response->assertNotFound();

        $this->assertSoftDeleted('todos', [
            'id' => $todo->id,
        ]);
    }

    /**
     * ログインユーザーが自分のゴミ箱を空にできることを確認
     */
    public function test_user_can_empty_own_trash(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTodo1 = Todo::factory()->for($user)->create();
        $ownTodo2 = Todo::factory()->for($user)->create();
        $otherTodo = Todo::factory()->for($otherUser)->create();

        $ownTodo1->delete();
        $ownTodo2->delete();
        $otherTodo->delete();

        $response = $this
            ->actingAs($user)
            ->delete(route('todos.trash.empty'));

        $response->assertRedirect(route('todos.trash'));

        $response->assertSessionHas(
            'success',
            'ゴミ箱を空にしました。'
        );

        $this->assertDatabaseMissing('todos', [
            'id' => $ownTodo1->id,
        ]);

        $this->assertDatabaseMissing('todos', [
            'id' => $ownTodo2->id,
        ]);

        // 他ユーザーのTodoは残る
        $this->assertSoftDeleted('todos', [
            'id' => $otherTodo->id,
        ]);
    }
}
