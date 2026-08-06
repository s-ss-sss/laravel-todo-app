<?php

namespace App\Http\Requests;

use App\Models\Todo;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateTodoRequest extends FormRequest
{
    /**
     * ログインユーザーがTodoを更新できるか確認
     */
    public function authorize(): bool
    {
        $todo = $this->route('todo');

        return $todo instanceof Todo
            && ($this->user()?->can('update', $todo) ?? false);
    }

    /**
     * Todo更新時の入力ルール
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
