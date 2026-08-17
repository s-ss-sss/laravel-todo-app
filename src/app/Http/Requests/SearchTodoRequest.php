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

    /**
     * バリデーションエラーメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'keyword.string' => 'キーワードは文字列で入力してください。',
            'keyword.max' => 'キーワードは255文字以内で入力してください。',
            'status.in' => '状態には完了または未完了を指定してください。',
            'due_date.date' => '期限日は正しい日付で入力してください。',
        ];
    }
}
