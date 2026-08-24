<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class TodoOverdueTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 期限切れの未完了Todoに期限切れラベルが表示されることを確認
     */
    public function test_overdue_label_is_displayed_for_incomplete_past_due_todo(): void
    {
        $this->travelTo(Carbon::parse('2026-08-24 12:00:00'));

        $user = User::factory()->create();

        $todo = Todo::factory()->for($user)->create([
            'title' => '期限切れのTodo',
            'due_date' => '2026-08-23',
            'is_completed' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.show', $todo));

        $response->assertOk();
        $response->assertSeeText('期限切れ');
    }

    /**
     * 期限日が今日のTodoには期限切れラベルが表示されないことを確認
     */
    public function test_overdue_label_is_not_displayed_when_due_date_is_today(): void
    {
        $this->travelTo(Carbon::parse('2026-08-24 12:00:00'));

        $user = User::factory()->create();

        Todo::factory()->for($user)->create([
            'title' => '今日が期限のTodo',
            'due_date' => '2026-08-24',
            'is_completed' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertDontSeeText('期限切れ');
    }

    /**
     * 期限日を過ぎていても完了済みなら期限切れラベルが表示されないことを確認
     */
    public function test_overdue_label_is_not_displayed_for_completed_todo(): void
    {
        $this->travelTo(Carbon::parse('2026-08-24 12:00:00'));

        $user = User::factory()->create();

        Todo::factory()->for($user)->create([
            'title' => '完了済みのTodo',
            'due_date' => '2026-08-23',
            'is_completed' => true,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertDontSeeText('期限切れ');
    }

    /**
     * 期限日のないTodoには期限切れラベルが表示されないことを確認
     */
    public function test_overdue_label_is_not_displayed_when_due_date_is_null(): void
    {
        $this->travelTo(Carbon::parse('2026-08-24 12:00:00'));

        $user = User::factory()->create();

        Todo::factory()->for($user)->create([
            'title' => '期限設定なしのTodo',
            'due_date' => null,
            'is_completed' => false,
        ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertDontSeeText('期限切れ');
    }
}
