<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">ユーザー編集</h2></x-slot>

    <div class="p-6">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
            @csrf @method('PATCH')

            {{-- 基本情報 --}}
            <div class="card bg-base-100 shadow p-4">
                <h3 class="font-bold mb-2">基本情報</h3>
                @if($user->avatar)
                    <div class="avatar">
                        <div class="w-10 rounded-full">
                            <img src="{{ $user->avatar->url }}" alt="avatar" class="w-12 h-12 rounded-full object-cover">
                        </div>
                    </div>
                @else
                    <span class="badge badge-ghost">なし</span>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <x-input name="last_name" label="姓" :value="$user->last_name"/>
                    <x-input name="name" label="名" :value="$user->name"/>
                    <x-input name="last_name_kana" label="姓（カナ）" :value="$user->last_name_kana"/>
                    <x-input name="first_name_kana" label="名（カナ）" :value="$user->first_name_kana"/>
                    <x-input name="display_name" label="表示名" :value="$user->display_name"/>
                    <x-input name="instagram_id" label="Instagram ID" :value="$user->instagram_id"/>
                    <x-input name="company_name" label="会社名" :value="$user->company_name"/>
                    <x-input name="email" type="email" label="メール" :value="$user->email"/>
                    <x-input name="phone" label="電話番号" :value="$user->phone"/>
                </div>
            </div>

            {{-- 住所 --}}
            <div class="card bg-base-100 shadow p-4">
                <h3 class="font-bold mb-2">住所情報</h3>
                <div class="grid grid-cols-2 gap-4">
                    <x-input name="postal_code" label="郵便番号" :value="$user->postal_code"/>
                    <x-input name="prefecture" label="都道府県" :value="$user->prefecture"/>
                    <x-input name="address1" label="住所1" :value="$user->address1"/>
                    <x-input name="address2" label="住所2" :value="$user->address2"/>
                    <x-input name="address3" label="住所3" :value="$user->address3"/>
                    <x-input name="country" label="国コード" :value="$user->country"/>
                </div>
            </div>

            {{-- 管理用 --}}
            <div class="card bg-base-100 shadow p-4">
                <h3 class="font-bold mb-2">管理情報</h3>
                <div class="grid grid-cols-2 gap-4">
                    <x-select name="role" label="権限" :options="['admin'=>'管理者','guest'=>'ゲスト','user'=>'一般']" :value="$user->role"/>
                    <x-input name="user_type" label="ユーザー種別" :value="$user->user_type"/>
                    <x-input name="user_status" label="ステータス" :value="$user->user_status"/>
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="email_notification" value="1" @checked($user->email_notification) class="checkbox" />
                        <span>メール通知を有効化</span>
                    </label>
                    <div class="col-span-2">
                        <label class="label"><span class="label-text">備考</span></label>
                        <textarea name="remarks" class="textarea textarea-bordered w-full">{{ old('remarks', $user->remarks) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Stripe --}}
            <div class="card bg-base-100 shadow p-4">
                <h3 class="font-bold mb-2">Stripe情報</h3>
                <ul class="text-sm">
                    <li><b>stripe_id:</b> {{ $user->stripe_id ?? '-' }}</li>
                    <li><b>pm_type:</b> {{ $user->pm_type ?? '-' }}</li>
                    <li><b>pm_last_four:</b> {{ $user->pm_last_four ?? '-' }}</li>
                    <li><b>trial_ends_at:</b> {{ $user->trial_ends_at ?? '-' }}</li>
                </ul>
            </div>

            <div class="flex space-x-2">
                <button type="submit" class="btn btn-primary">更新</button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">戻る</a>
            </div>
        </form>
    </div>
</x-app-layout>
