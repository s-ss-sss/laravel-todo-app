<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * トップページからTodo一覧へ転送されることを確認
     */
    public function test_home_redirects_to_todo_index(): void
    {
        $response = $this->get(route('home'));

        $response->assertRedirect(route('todos.index'));
    }
}
