<?php

namespace App\Actions\Fortify;

use App\Models\User;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Contracts\UpdatesUserProfileInformation;

class UpdateUserProfileInformation implements UpdatesUserProfileInformation
{
    /**
     * Validate and update the given user's profile information.
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
                'name' => ['required', 'string', 'max:255'],

                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('users')->ignore($user->id),
                ],
            ],
            [
                'name.required' => 'ユーザー名は必須です。',
                'name.string' => 'ユーザー名は文字列で入力してください。',
                'name.max' => 'ユーザー名は255文字以内で入力してください。',

                'email.required' => 'メールアドレスは必須です。',
                'email.string' => 'メールアドレスは文字列で入力してください。',
                'email.email' => 'メールアドレスは正しい形式で入力してください。',
                'email.max' => 'メールアドレスは255文字以内で入力してください。',
                'email.unique' => 'このメールアドレスはすでに登録されています。',
            ]
        )->validateWithBag('updateProfileInformation');

        if ($input['email'] !== $user->email &&
            $user instanceof MustVerifyEmail) {
            $this->updateVerifiedUser($user, $input);
        } else {
            $user->forceFill([
                'name' => $input['name'],
                'email' => $input['email'],
            ])->save();
        }
    }

    /**
     * Update the given verified user's profile information.
     *
     * @param  array<string, string>  $input
     */
    protected function updateVerifiedUser(User $user, array $input): void
    {
        $user->forceFill([
            'name' => $input['name'],
            'email' => $input['email'],
            'email_verified_at' => null,
        ])->save();

        $user->sendEmailVerificationNotification();
    }
}
