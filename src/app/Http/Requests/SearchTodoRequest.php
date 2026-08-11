<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SearchTodoRequest extends FormRequest
{
    /**
     * Todo検索を許可
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Todo検索条件
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'keyword' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:completed,incomplete'],
            'due_date' => ['nullable', 'date'],
        ];
    }
}
