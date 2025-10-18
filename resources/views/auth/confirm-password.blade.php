<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
            {{-- ロゴ --}}
            <div class="text-center mb-6">
                <img src="{{ asset('images/bakerista_logo.png') }}" alt="Bakerista" class="mx-auto w-24 mb-2">
                <h1 class="text-2xl font-bold text-primary">パスワードの確認</h1>
                <p class="text-gray-500 text-sm mt-1">続行する前に、パスワードを再入力してください</p>
            </div>

            {{-- 説明文 --}}
            <div class="text-sm text-gray-600 mb-4 leading-relaxed">
                この操作はセキュアエリアでの確認を必要とします。<br>
                ご本人確認のため、パスワードを入力してください。
            </div>

            {{-- フォーム --}}
            <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
                @csrf

                {{-- パスワード --}}
                <div>
                    <x-input-label for="password" :value="__('パスワード')" />
                    <x-text-input id="password" type="password" name="password"
                        class="input input-bordered w-full mt-1"
                        required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- 確認ボタン --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('確認して続行') }}
                    </button>
                </div>
            </form>

            {{-- 戻る導線 --}}
            <div class="text-center mt-4">
                <a href="{{ url()->previous() }}" class="link link-secondary text-sm">
                    ← 前のページに戻る
                </a>
            </div>
        </div>

    </div>
</x-guest-layout>
