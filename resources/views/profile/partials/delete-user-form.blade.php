<section class="space-y-6">

    {{-- アコーディオン全体 --}}
    <div tabindex="0" class="collapse collapse-arrow border border-error/40 bg-base-100 rounded-xl">

        {{-- ▼ アコーディオンタイトル --}}
        <div class="collapse-title text-lg font-semibold text-gray-800 flex items-center gap-2 py-4">
            <div class="badge badge-error text-white">
                アカウント削除手続き
            </div>
        </div>

        {{-- ▼ アコーディオン内容（ここに全文） --}}
        <div class="collapse-content space-y-6">

            {{-- 説明文 --}}
            <p class="mt-2 text-sm text-gray-600 leading-relaxed">
                アカウントを削除すると、これまでの投稿・コメント・プロフィール情報などがすべて失われます。
                <br class="hidden sm:block">
                <strong class="text-error">削除後は元に戻すことができません。</strong>
            </p>

            {{-- 注意・アドバイス --}}
            <div class="alert alert-warning bg-warning/20 border border-warning/40 text-sm leading-relaxed">
                <div>
                    <h3 class="font-semibold text-warning text-base mb-1">⚠️ 削除の前にご確認ください</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li>必ず <strong>サブスクリプションがキャンセル（中止）されている</strong> ことを確認してください。</li>
                        <li>サブスクリプションが中止されていれば、<strong>会員情報を残しても費用は発生しません。</strong></li>
                        <li>将来的に再入会の可能性がある場合は、<strong>削除せず保留</strong> しておくことをおすすめします。</li>
                    </ul>
                </div>
            </div>

            {{-- 操作ボタン --}}
            <div class="flex flex-col sm:flex-row items-center sm:justify-between gap-3">
                <p class="text-sm text-gray-500">
                    アカウント削除をご希望の方は、以下のボタンをクリックしてください。
                </p>

                <x-danger-button
                    class="btn-sm sm:btn-md shadow-md"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                >
                    アカウントを削除する
                </x-danger-button>
            </div>

            {{-- 確認モーダル --}}
            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-semibold text-gray-800 mb-2">
                        本当にアカウントを削除しますか？
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        アカウント削除後は、データを復元することはできません。
                        <br class="hidden sm:block">
                        続行する場合は、確認のためにパスワードを入力してください。
                    </p>

                    {{-- パスワード入力 --}}
                    <div class="form-control">
                        <x-input-label for="password" value="{{ __('パスワードの入力') }}" />
                        <x-text-input
                            id="password"
                            name="password"
                            type="password"
                            class="input input-bordered w-full sm:w-3/4 mt-1 text-base"
                            placeholder="現在のパスワードを入力"
                        />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    {{-- ボタン --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <x-danger-button class="btn-sm sm:btn-md">
                            削除を確定する
                        </x-danger-button>
                    </div>
                </form>
            </x-modal>

        </div>
    </div>
</section>
