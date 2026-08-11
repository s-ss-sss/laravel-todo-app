<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoSearchTest extends TestCase
{
    use RefreshDatabase;

    /**
     * タイトルのキーワードでTodoを検索できることを確認
     */
    public function test_users_can_search_todos_by_title(): void
    {
        $user = User::factory()->create();

        $matchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Laravelを勉強する',
            ]);

        $nonMatchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Dockerを勉強する',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'keyword' => 'Laravel',
            ]));

        $response->assertOk();
        $response->assertSeeText($matchingTodo->title);
        $response->assertDontSeeText($nonMatchingTodo->title);
    }

    /**
     * タイトルまたは説明のキーワードで自分のTodoだけを検索できることを確認
     */
    public function test_users_can_search_their_own_todos_by_keyword(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $titleMatchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Laravelを勉強する',
                'description' => null,
            ]);

        $descriptionMatchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'フレームワークを勉強する',
                'description' => 'Laravelの検索機能を実装する',
            ]);

        $nonMatchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Dockerを勉強する',
                'description' => 'コンテナについて確認する',
            ]);

        $otherUsersTodo = Todo::factory()
            ->for($otherUser)
            ->create([
                'title' => '他人のLaravel Todo',
                'description' => 'Laravelについての説明',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'keyword' => 'Laravel',
            ]));

        $response->assertOk();
        $response->assertSeeText($titleMatchingTodo->title);
        $response->assertSeeText($descriptionMatchingTodo->title);
        $response->assertDontSeeText($nonMatchingTodo->title);
        $response->assertDontSeeText($otherUsersTodo->title);
    }

    /**
     * 完了済みTodoだけを検索できることを確認
     */
    public function test_users_can_filter_completed_todos(): void
    {
        $user = User::factory()->create();

        $completedTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '完了済みTodo',
                'is_completed' => true,
            ]);

        $incompleteTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '未完了Todo',
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'status' => 'completed',
            ]));

        $response->assertSeeText($completedTodo->title);
        $response->assertDontSeeText($incompleteTodo->title);
    }

    /**
     * 未完了Todoだけを検索できることを確認
     */
    public function test_users_can_filter_incomplete_todos(): void
    {
        $user = User::factory()->create();

        $completedTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '完了済みTodo',
                'is_completed' => true,
            ]);

        $incompleteTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '未完了Todo',
                'is_completed' => false,
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'status' => 'incomplete',
            ]));

        $response->assertSeeText($incompleteTodo->title);
        $response->assertDontSeeText($completedTodo->title);
    }

    /**
     * 指定した期限日のTodoを検索できることを確認
     */
    public function test_users_can_filter_todos_by_due_date(): void
    {
        $user = User::factory()->create();

        $matchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '指定日期限のTodo',
                'due_date' => '2026-08-31',
            ]);

        $nonMatchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '別日期限のTodo',
                'due_date' => '2026-09-30',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'due_date' => '2026-08-31',
            ]));

        $response->assertSeeText($matchingTodo->title);
        $response->assertDontSeeText($nonMatchingTodo->title);
    }

    /**
     * 複数の検索条件を組み合わせられることを確認
     */
    public function test_users_can_combine_todo_search_filters(): void
    {
        $user = User::factory()->create();

        $matchingTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Laravelのテスト',
                'is_completed' => false,
                'due_date' => '2026-08-31',
            ]);

        $wrongStatusTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => 'Laravelの完了済みテスト',
                'is_completed' => true,
                'due_date' => '2026-08-31',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'keyword' => 'Laravel',
                'status' => 'incomplete',
                'due_date' => '2026-08-31',
            ]));

        $response->assertSeeText($matchingTodo->title);
        $response->assertDontSeeText($wrongStatusTodo->title);
    }
}
