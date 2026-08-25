<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserPasswords;

class UpdateUserPassword implements UpdatesUserPasswords
{
    use PasswordValidationRules;

    /**
     * Validate and update the user's password.
     *
     * @param  array<string, string>  $input
     *
     * @throws ValidationException
     */
    public function update(User $user, array $input): void
    {
        Validator::make(
            $input,
            [
                'current_password' => [
                    'required',
                    'string',
                    'current_password:web',
                ],
                'password' => $this->passwordRules(),
            ],
            [
                'current_password.required' => '現在のパスワードは必須です。',
                'current_password.string' => '現在のパスワードは文字列で入力してください。',
                'current_password.current_password' => '現在のパスワードが正しくありません。',

                'password.required' => '新しいパスワードは必須です。',
                'password.string' => '新しいパスワードは文字列で入力してください。',
                'password.min' => '新しいパスワードは8文字以上で入力してください。',
                'password.confirmed' => '新しいパスワード確認が一致していません。',
            ]
        )->validateWithBag('updatePassword');

        $user->forceFill([
            'password' => Hash::make($input['password']),
        ])->save();
    }
}
