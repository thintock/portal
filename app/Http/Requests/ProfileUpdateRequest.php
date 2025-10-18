<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // プロフィール画像
            'avatar'        => ['nullable', 'image', 'max:10240'], // 10MBまで
            // 基本情報
            'name'             => ['nullable', 'string', 'max:50'],
            'first_name'       => ['nullable', 'string', 'max:50'],
            'last_name'        => ['nullable', 'string', 'max:50'],
            'first_name_kana'  => ['nullable', 'string', 'max:50'],
            'last_name_kana'   => ['nullable', 'string', 'max:50'],
            'instagram_id'     => ['nullable', 'string', 'max:100'],
            'avatar_media_id'  => ['nullable', 'string', 'max:50'],
            'company_name'     => ['nullable', 'string', 'max:50'],

            // 住所・連絡先
            'postal_code'      => ['nullable', 'string', 'max:10'],
            'prefecture'       => ['nullable', 'string', 'max:50'],
            'address1'         => ['nullable', 'string', 'max:100'],
            'address2'         => ['nullable', 'string', 'max:100'],
            'address3'         => ['nullable', 'string', 'max:100'],
            'country'          => ['nullable', 'string', 'size:2'], // ISO 2文字コード (JP, US)
            'phone'            => ['nullable', 'string', 'max:20'],

            // 管理系
            'role'             => ['nullable', 'string', 'max:20'],
            'user_type'        => ['nullable', 'string', 'max:10'],
            'user_status'      => ['nullable', 'string', 'max:20'],
            'email_notification' => ['nullable', 'boolean'],
            'remarks'          => ['nullable', 'string'],

            // メール（ユニーク制約・自分は除外）
            'email' => [
                'required',
                'string',
                'lowercase',
                'email',
                'max:100',
                Rule::unique(User::class)->ignore($this->user()->id),
            ],
        ];
    }
}
