<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
            {{-- ロゴ --}}
            <div class="text-center mb-6">
                <img src="{{ asset('images/bakerista_logo.png') }}" alt="Bakerista" class="mx-auto w-24 mb-2">
                <h1 class="text-2xl font-bold text-primary">パスワードをお忘れですか？</h1>
                <p class="text-gray-500 text-sm mt-1">登録されたメールアドレスを入力してください</p>
            </div>

            {{-- 説明文 --}}
            <div class="text-sm text-gray-600 mb-4 leading-relaxed">
                ご登録のメールアドレスを入力すると、<br>
                パスワード再設定用のリンクをお送りします。
            </div>

            {{-- ステータスメッセージ --}}
            <x-auth-session-status class="mb-4" :status="session('status')" />

            {{-- フォーム --}}
            <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
                @csrf

                {{-- メールアドレス --}}
                <div>
                    <x-input-label for="email" :value="__('Eメール')" />
                    <x-text-input id="email" type="email" name="email"
                        class="input input-bordered w-full mt-1"
                        :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- 送信ボタン --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('パスワード再設定メールを送信') }}
                    </button>
                </div>
            </form>

            {{-- 戻るリンク --}}
            <div class="text-center mt-4">
                <a href="{{ route('login') }}" class="link link-secondary text-sm">
                    ログインページに戻る
                </a>
            </div>
        </div>

    </div>
</x-guest-layout>
