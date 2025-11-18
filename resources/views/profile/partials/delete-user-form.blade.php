<section class="space-y-6">
    <div class="w-full" x-data="{ open: false }">

        {{-- ▼ トリガー部分 --}}
        <div>
            <button
                @click="open = !open"
                class="flex justify-between items-center w-full px-4 py-3 bg-base-100 border border-error/40 rounded-xl cursor-pointer"
            >
                <div class="flex items-center gap-2">
                    <span class="badge badge-error text-white">アカウント削除手続き</span>
                </div>

                <svg xmlns="http://www.w3.org/2000/svg"
                     class="w-4 h-4 transition-transform duration-300"
                     :class="open ? 'rotate-180' : 'rotate-0'"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        {{-- ▼ コンテンツ --}}
        <div
            x-show="open"
            x-transition
            x-cloak
            class="mt-3 bg-base-100 border border-error/30 rounded-xl p-4 space-y-6"
        >

            {{-- 説明文 --}}
            <p class="text-sm text-gray-600 leading-relaxed">
                アカウントを削除すると、これまでの投稿・コメント・プロフィール情報などがすべて消去されます。
                <br class="hidden sm:block">
                <strong class="text-error">削除後に復元することはできません。</strong>
            </p>

            {{-- 注意・アドバイス --}}
            <div class="alert alert-warning bg-warning/20 border border-warning/40 text-sm leading-relaxed">
                <div>
                    <h3 class="font-semibold text-warning text-base mb-1">⚠️ 削除前に必ずご確認ください</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-700">
                        <li>
                            <strong>サブスクリプションが中止されている</strong> ことを確認してください。
                        </li>
                        <li>
                            サブスク中止後は、<strong>会員情報を残しても費用は一切発生しません。</strong>
                        </li>
                        <li>
                            将来的に再入会の可能性がある場合は、
                            <strong>アカウント削除を行わず保留</strong> にしておくことをお勧めします。
                        </li>
                    </ul>
                </div>
            </div>

            {{-- 操作ボタン --}}
            <div class="flex flex-col sm:flex-row items-center sm:justify-between gap-3">
                <p class="text-sm text-gray-500">
                    アカウント削除をご希望の場合、以下のボタンを押してください。
                </p>

                <x-danger-button
                    class="btn-sm sm:btn-md shadow-md"
                    x-data=""
                    x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')"
                >
                    アカウントを削除する
                </x-danger-button>
            </div>

            {{-- ▼ 確認モーダル --}}
            <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
                <form method="post" action="{{ route('profile.destroy') }}" class="p-6">
                    @csrf
                    @method('delete')

                    <h2 class="text-lg font-semibold text-gray-800 mb-2">
                        本当にアカウントを削除しますか？
                    </h2>

                    <p class="text-sm text-gray-600 leading-relaxed mb-4">
                        削除後はアカウントやデータを復元することはできません。
                        <br class="hidden sm:block">
                        続行する場合は、確認のためにパスワードを入力してください。
                    </p>

                    {{-- パスワード --}}
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
