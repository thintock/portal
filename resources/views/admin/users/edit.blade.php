<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">ユーザー管理</h1>
            <p class="text-sm text-gray-500">ユーザーを確認・編集できます。</p>
        </div>
    @endsection
    
    <div class="w-full">
        <form method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf @method('PATCH')

            {{-- 基本情報 --}}
            <div class="card bg-white shadow p-4 mb-4">
                <h3 class="font-bold mb-2">基本情報</h3>
                {{-- アバター表示 --}}
                @if($avatar_url)
                  <div class="avatar mb-4">
                    <div class="w-24 rounded-full">
                      <img src="{{ $avatar_url }}" alt="プロフィール画像" class="rounded-full object-cover">
                    </div>
                  </div>
                @else
                  <div class="avatar placeholder mb-4">
                    <div class="bg-neutral text-neutral-content rounded-full w-24">
                      <span class="text-2xl">{{ mb_substr($user->name ?? '？', 0, 1) }}</span>
                    </div>
                  </div>
                @endif
                <div class="grid grid-cols-2 gap-4">
                    <x-input name="last_name" label="姓" :value="$user->last_name"/>
                    <x-input name="first_name" label="名" :value="$user->first_name"/>
                    <x-input name="last_name_kana" label="姓（かな）" :value="$user->last_name_kana"/>
                    <x-input name="first_name_kana" label="名（かな）" :value="$user->first_name_kana"/>
                    <x-input name="name" label="ニックネーム" :value="$user->name"/>
                    <x-input name="instagram_id" label="Instagram ID" :value="$user->instagram_id"/>
                    <x-input name="company_name" label="会社名" :value="$user->company_name"/>
                    <x-input name="email" type="email" label="メール" :value="$user->email"/>
                    <x-input name="phone" label="電話番号" :value="$user->phone"/>
                    {{-- 🎂 誕生日（月・日） --}}
                    <div class="col-span-2">
                        <label class="block font-bold mb-1">誕生日</label>
                    
                        <div class="flex gap-3">
                            {{-- 月 --}}
                            <div class="flex-1">
                                <select name="birthday_month"
                                    class="select select-bordered w-full bg-white rounded-md">
                                    <option value="">月を選択</option>
                                    @foreach(range(1,12) as $m)
                                        <option value="{{ $m }}"
                                            @selected(old('birthday_month', $user->birthday_month) == $m)>
                                            {{ $m }} 月
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                    
                            {{-- 日 --}}
                            <div class="flex-1">
                                <select name="birthday_day"
                                    class="select select-bordered w-full bg-white rounded-md">
                                    <option value="">日を選択</option>
                                    @foreach(range(1,31) as $d)
                                        <option value="{{ $d }}"
                                            @selected(old('birthday_day', $user->birthday_day) == $d)>
                                            {{ $d }} 日
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            {{-- 住所 --}}
            <div class="card bg-white shadow p-4 mb-4">
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
            <div class="card bg-white shadow p-4 mb-4">
                <h3 class="font-bold mb-2">管理情報</h3>
                <div class="grid grid-cols-2 gap-4">
                    {{-- 権限 --}}
                    <x-select 
                        name="role" 
                        label="権限" 
                        :options="[
                            'admin' => '管理者',
                            'user' => '一般',
                            'guest' => 'ゲスト',
                        ]" 
                        :value="$user->role ?? ''" />
            
                    {{-- ユーザー種別 --}}
                    <x-select 
                        name="user_type" 
                        label="ユーザー種別"
                        :options="[
                            'home' => '一般会員',
                            'bakery' => 'ベーカリー事業者',
                            'admin' => '管理者',
                            'partner' => 'パートナー（専用コンソール利用可）',
                        ]"
                        :value="$user->user_type ?? 'home'" />
            
                    {{-- ステータス --}}
                    <x-select 
                        name="user_status" 
                        label="ユーザーステータス"
                        :options="[
                            'active' => '有効',
                            'withdrawn' => '退会済み',
                        ]"
                        :value="$user->user_status ?? 'active'" />
                    
                    <label class="flex items-center space-x-2">
                        <input type="hidden" name="email_notification" value="0">
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
            <div class="card bg-white shadow p-4 mb-4">
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
</x-admin-layout>
