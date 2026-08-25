<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class DeleteAccountRequest extends FormRequest
{
    /**
     * 使用するエラーバッグ
     *
     * @var string
     */
    protected $errorBag = 'deleteAccount';

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'current_password' => [
                'required',
                'string',
                'current_password:web',
            ],
        ];
    }

    /**
     * バリデーションメッセージ
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'current_password.required' => '現在のパスワードは必須です。',
            'current_password.string' => '現在のパスワードは文字列で入力してください。',
            'current_password.current_password' => '現在のパスワードが正しくありません。',
        ];
    }
}
