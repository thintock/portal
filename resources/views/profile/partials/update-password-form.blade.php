<section class="space-y-6">

    {{-- アコーディオン全体 --}}
    <div tabindex="0" class="collapse collapse-arrow border border-base-300 bg-base-100 rounded-xl">

        {{-- ▼ アコーディオンタイトル（クリックで開閉） --}}
        <div class="collapse-title text-lg font-semibold text-gray-800 flex items-center gap-2 py-4">
            <div class="badge badge-primary text-white">セキュリティ設定</div>
            <span>パスワードの変更</span>
        </div>

        {{-- ▼ アコーディオンの中身（フォーム） --}}
        <div class="collapse-content space-y-6">

            {{-- 説明文 --}}
            <p class="mt-1 text-sm text-gray-600">
                アカウントを安全に保つために、定期的に
                <strong class="text-primary">強力なパスワード</strong>
                へ変更しましょう。
            </p>

            {{-- フォーム --}}
            <form method="post" action="{{ route('password.update') }}"
                class="bg-base-100 p-3 rounded-2xl shadow-inner border border-base-200 space-y-6">
                @csrf
                @method('put')

                {{-- 現在のパスワード --}}
                <div>
                    <x-input-label for="update_password_current_password" :value="__('現在のパスワード')" />
                    <x-text-input id="update_password_current_password" name="current_password" type="password"
                        class="input input-bordered w-full mt-1 text-base bg-white"
                        placeholder="現在のパスワードを入力"
                        autocomplete="current-password" />
                    <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
                </div>

                {{-- 新しいパスワード --}}
                <div class="grid sm:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="update_password_password" :value="__('新しいパスワード')" />
                        <x-text-input id="update_password_password" name="password" type="password"
                            class="input input-bordered w-full mt-1 text-base bg-white"
                            placeholder="8文字以上で設定"
                            autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="update_password_password_confirmation" :value="__('新しいパスワード（確認）')" />
                        <x-text-input id="update_password_password_confirmation" name="password_confirmation"
                            type="password" class="input input-bordered w-full mt-1 text-base bg-white"
                            placeholder="もう一度入力" autocomplete="new-password" />
                        <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
                    </div>
                </div>

                {{-- 注意メッセージ --}}
                <div class="alert alert-info text-sm mt-2 flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 16h-1v-4h-1m1-4h.01M12 20a8 8 0 100-16 8 8 0 000 16z" />
                    </svg>
                    <span>推奨：英数字・記号を組み合わせた <strong>8文字以上</strong> で設定してください。</span>
                </div>

                {{-- 保存ボタン --}}
                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="btn btn-primary shadow-md w-full sm:w-auto btn-sm sm:btn-md">
                        保存する
                    </button>

                    @if (session('status') === 'password-updated')
                        <p x-data="{ show: true }" x-show="show" x-transition
                            x-init="setTimeout(() => show = false, 2000)"
                            class="text-sm text-success">保存しました。</p>
                    @endif
                </div>
            </form>

        </div>
    </div>

</section>
