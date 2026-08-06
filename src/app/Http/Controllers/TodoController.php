<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;

class TodoController extends Controller
{
    /**
     * ログインユーザーのTodo一覧を表示
     */
    public function index(Request $request): View
    {
        $todos = $request->user()->todos()->get();

        return view('todos.index', compact('todos'));
    }
}
