<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
            {{-- ロゴ --}}
            <div class="text-center mb-6">
                <img src="{{ asset('images/bakerista_logo.png') }}" alt="Bakerista" class="mx-auto w-24 mb-2">
                <h1 class="text-2xl font-bold text-primary">新しいパスワードを設定</h1>
                <p class="text-gray-500 text-sm mt-1">ご登録のメールアドレスに届いたリンクからアクセスしています</p>
            </div>

            {{-- フォーム --}}
            <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
                @csrf

                {{-- トークン --}}
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                {{-- メールアドレス --}}
                <div>
                    <x-input-label for="email" :value="__('Eメール')" />
                    <x-text-input id="email" type="email" name="email"
                        class="input input-bordered w-full mt-1"
                        :value="old('email', $request->email)" required autofocus autocomplete="username" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- 新しいパスワード --}}
                <div>
                    <x-input-label for="password" :value="__('新しいパスワード')" />
                    <x-text-input id="password" type="password" name="password"
                        class="input input-bordered w-full mt-1"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- パスワード確認 --}}
                <div>
                    <x-input-label for="password_confirmation" :value="__('パスワード（確認）')" />
                    <x-text-input id="password_confirmation" type="password" name="password_confirmation"
                        class="input input-bordered w-full mt-1"
                        required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                {{-- 送信ボタン --}}
                <div class="pt-4">
                    <button type="submit" class="btn btn-primary w-full">
                        {{ __('パスワードを再設定する') }}
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
