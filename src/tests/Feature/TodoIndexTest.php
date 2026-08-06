<?php

namespace Tests\Feature;

use App\Models\Todo;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TodoIndexTest extends TestCase
{
    use RefreshDatabase;

    /**
     * 未ログインユーザーはTodo一覧からログイン画面へ転送されることを確認
     */
    public function test_guests_are_redirected_from_todo_index_to_login(): void
    {
        $response = $this->get(route('todos.index'));

        $response->assertRedirect(route('login'));
    }

    /**
     * ログインユーザーがTodo一覧画面を表示できることを確認
     */
    public function test_authenticated_users_can_view_todo_index(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertViewIs('todos.index');
        $response->assertViewHas('todos');
    }

    /**
     * ログインユーザー自身のTodoだけが表示されることを確認
     */
    public function test_users_can_only_see_their_own_todos(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $ownTodo = Todo::factory()
            ->for($user)
            ->create([
                'title' => '自分のTodo',
            ]);

        $otherTodo = Todo::factory()
            ->for($otherUser)
            ->create([
                'title' => '他人のTodo',
            ]);

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertSeeText($ownTodo->title);
        $response->assertDontSeeText($otherTodo->title);
    }

    /**
     * Todoがない場合に空メッセージが表示されることを確認
     */
    public function test_empty_message_is_displayed_when_user_has_no_todos(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->get(route('todos.index'));

        $response->assertOk();
        $response->assertSeeText('Todoはまだありません。');
    }
}
