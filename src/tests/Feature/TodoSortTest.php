<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoSortTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Todoがsort_order順に表示されることを確認
     */
    public function test_todos_are_displayed_in_sort_order(): void
    {
        $user = User::factory()->create();

        $thirdTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '3番目のTodo',
                'sort_order' => 3,
            ]);

        $firstTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '1番目のTodo',
                'sort_order' => 1,
            ]);

        $secondTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '2番目のTodo',
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertSeeInOrder([
            $firstTodo->title,
            $secondTodo->title,
            $thirdTodo->title,
        ]);
    }

    /**
     * 新規Todoが既存Todoの末尾へ追加されることを確認
     */
    public function test_new_todo_is_added_to_end_of_sort_order(): void
    {
        $user = User::factory()->create();

        Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 1,
            ]);

        Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->post(route('todos.store'), [
                'title' => '新しく追加したTodo',
                'description' => null,
                'due_date' => null,
            ]);

        $response->assertRedirect(route('todos.index'));

        $this->assertDatabaseHas('todos', [
            'user_id' => $user->id,
            'title' => '新しく追加したTodo',
            'sort_order' => 3,
        ]);
    }

    /**
     * Todoの所有者がTodoを1つ上へ移動できることを確認
     */
    public function test_owner_can_move_todo_up(): void
    {
        $user = User::factory()->create();

        $firstTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '1番目のTodo',
                'sort_order' => 1,
            ]);

        $secondTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '2番目のTodo',
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.move-up', $secondTodo));

        $response->assertRedirect(route('todos.index'));

        $this->assertSame(
            2,
            $firstTodo->refresh()->sort_order
        );

        $this->assertSame(
            1,
            $secondTodo->refresh()->sort_order
        );
    }

    /**
     * Todoの所有者がTodoを1つ下へ移動できることを確認
     */
    public function test_owner_can_move_todo_down(): void
    {
        $user = User::factory()->create();

        $firstTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '1番目のTodo',
                'sort_order' => 1,
            ]);

        $secondTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '2番目のTodo',
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.move-down', $firstTodo));

        $response->assertRedirect(route('todos.index'));

        $this->assertSame(
            2,
            $firstTodo->refresh()->sort_order
        );

        $this->assertSame(
            1,
            $secondTodo->refresh()->sort_order
        );
    }

    /**
     * 先頭のTodoを上へ移動しても順番が変わらないことを確認
     */
    public function test_first_todo_cannot_move_up(): void
    {
        $user = User::factory()->create();

        $firstTodo = Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 1,
            ]);

        $secondTodo = Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.move-up', $firstTodo));

        $response->assertRedirect(route('todos.index'));

        $this->assertSame(1, $firstTodo->refresh()->sort_order);
        $this->assertSame(2, $secondTodo->refresh()->sort_order);
    }

    /**
     * 末尾のTodoを下へ移動しても順番が変わらないことを確認
     */
    public function test_last_todo_cannot_move_down(): void
    {
        $user = User::factory()->create();

        $firstTodo = Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 1,
            ]);

        $lastTodo = Todo::factory()
            ->for($user)
            ->create([
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.move-down', $lastTodo));

        $response->assertRedirect(route('todos.index'));

        $this->assertSame(1, $firstTodo->refresh()->sort_order);
        $this->assertSame(2, $lastTodo->refresh()->sort_order);
    }

    /**
     * 他人のTodoを移動できないことを確認
     */
    public function test_users_cannot_move_other_users_todo(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $todo = Todo::factory()
            ->for($otherUser)
            ->create([
                'sort_order' => 2,
            ]);

        $response = $this
            ->actingAs($user)
            ->patch(route('todos.move-up', $todo));

        $response->assertForbidden();

        $this->assertSame(
            2,
            $todo->refresh()->sort_order
        );
    }

    /**
     * 未ログインユーザーはTodoを移動できないことを確認
     */
    public function test_guests_cannot_move_todo(): void
    {
        $todo = Todo::factory()->create([
            'sort_order' => 1,
        ]);

        $response = $this->patch(
            route('todos.move-up', $todo)
        );

        $response->assertRedirect(route('login'));
        $this->assertSame(1, $todo->refresh()->sort_order);
    }

    /**
     * 検索中は並び替えボタンが表示されないことを確認
     */
    public function test_sort_buttons_are_hidden_while_searching(): void
    {
        $user = User::factory()->create();

        $todo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Laravelを勉強する',
                'sort_order' => 1,
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'keyword' => 'Laravel',
            ]));

        $response->assertOk();
        $response->assertDontSee(
            route('todos.move-up', $todo),
            false
        );
        $response->assertDontSee(
            route('todos.move-down', $todo),
            false
        );
    }
}
