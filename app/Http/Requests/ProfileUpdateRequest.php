<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーション前に値を整形
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            // 郵便番号と電話番号のハイフンを削除
            'postal_code' => $this->postal_code
                ? str_replace(['-', 'ー', '−'], '', $this->postal_code)
                : null,

            'phone' => $this->phone
                ? str_replace(['-', 'ー', '−', ' '], '', $this->phone)
                : null,

            // Instagram IDの@を削除
            'instagram_id' => $this->instagram_id
                ? ltrim($this->instagram_id, '@')
                : null,
        ]);
    }

    public function rules(): array
    {
        $userId = $this->route('user')?->id ?? $this->user()?->id;

        return [
            'avatar'           => ['nullable', 'image', 'max:10240'], // 10MBまで
            'name'             => ['nullable', 'string', 'max:50'],
            'first_name'       => ['nullable', 'string', 'max:50'],
            'last_name'        => ['nullable', 'string', 'max:50'],
            'first_name_kana'  => ['nullable', 'string', 'max:50'],
            'last_name_kana'   => ['nullable', 'string', 'max:50'],
            'instagram_id'     => ['nullable', 'string', 'max:100'],
            'avatar_media_id'  => ['nullable', 'string', 'max:50'],
            'company_name'     => ['nullable', 'string', 'max:50'],

            'postal_code'      => ['nullable', 'string', 'max:10'],
            'prefecture'       => ['nullable', 'string', 'max:50'],
            'address1'         => ['nullable', 'string', 'max:100'],
            'address2'         => ['nullable', 'string', 'max:100'],
            'address3'         => ['nullable', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'size:2'],
            'phone'            => ['nullable', 'string', 'max:20'],

            'role'             => ['nullable', 'string', 'max:20'],
            'user_type'        => ['nullable', 'string', 'max:10'],
            'user_status'      => ['nullable', 'string', 'max:20'],
            'email_notification' => ['nullable', 'boolean'],
            'remarks'          => ['nullable', 'string'],
            'birthday_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'birthday_day'   => ['nullable', 'integer', 'min:1', 'max:31'],
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                Rule::unique(User::class)->ignore($userId),
            ],
        ];
    }
}
