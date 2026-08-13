<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class TodoPaginationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Todo一覧が10件ずつページ分割されることを確認
     */
    public function test_todos_are_paginated_by_ten_items(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 12) as $number) {
            Todo::factory()
                ->for($user)
                ->create([
                    'title' => "Todo {$number}",
                    'sort_order' => $number,
                ]);
        }

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();

        $response->assertViewHas('todos', function ($todos) {
            return $todos instanceof LengthAwarePaginator
                && $todos->currentPage() === 1
                && $todos->count() === 10
                && $todos->total() === 12
                && $todos->lastPage() === 2
                && $todos->pluck('title')->all() === [
                    'Todo 1',
                    'Todo 2',
                    'Todo 3',
                    'Todo 4',
                    'Todo 5',
                    'Todo 6',
                    'Todo 7',
                    'Todo 8',
                    'Todo 9',
                    'Todo 10',
                ];
        });

        $response->assertDontSeeText('Todo 11');
        $response->assertDontSeeText('Todo 12');
    }

    /**
     * Todo一覧の2ページ目に残りのTodoが表示されることを確認
     */
    public function test_remaining_todos_are_displayed_on_second_page(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 12) as $number) {
            Todo::factory()
                ->for($user)
                ->create([
                    'title' => "ページ確認Todo {$number}",
                    'sort_order' => $number,
                ]);
        }

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', ['page' => 2]));

        $response->assertOk();

        $response->assertViewHas('todos', function ($todos) {
            return $todos instanceof LengthAwarePaginator
                && $todos->currentPage() === 2
                && $todos->count() === 2
                && $todos->pluck('title')->all() === [
                    'ページ確認Todo 11',
                    'ページ確認Todo 12',
                ];
        });

        $response->assertSeeText('ページ確認Todo 11');
        $response->assertSeeText('ページ確認Todo 12');
    }

    /**
     * ページ移動時に検索条件が引き継がれることを確認
     */
    public function test_pagination_links_preserve_search_conditions(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 12) as $number) {
            Todo::factory()
                ->for($user)
                ->create([
                    'title' => "Laravel Todo {$number}",
                    'sort_order' => $number,
                ]);
        }

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index', [
                'keyword' => 'Laravel',
            ]));

        $response->assertOk();

        $response->assertViewHas('todos', function ($todos) {
            $nextPageUrl = $todos->nextPageUrl();

            return $nextPageUrl !== null
                && str_contains($nextPageUrl, 'keyword=Laravel')
                && str_contains($nextPageUrl, 'page=2');
        });
    }
}
