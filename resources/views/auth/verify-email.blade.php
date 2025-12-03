<x-guest-layout>
    <div class="min-h-screen flex flex-col justify-center items-center bg-gradient-to-br from-primary/40 via-accent/30 to-secondary/40 px-4">
        <div class="w-full max-w-md bg-white shadow-xl rounded-xl p-8">
            {{-- ロゴ --}}
            <div class="text-center mb-6">
                <img src="{{ asset('images/bakerista_logo.png') }}" alt="Bakerista" class="mx-auto w-24 mb-2">
                <h1 class="text-2xl font-bold text-primary">ご登録ありがとうございます！</h1>
                <p class="text-gray-500 text-sm mt-1">メールアドレスの確認をお願いいたします</p>
            </div>

            {{-- メッセージ --}}
            <div class="text-sm text-gray-600 mb-4 leading-relaxed">
                ご登録のメールアドレスに確認用リンクをお送りしました。<br>
                メール内のリンクをクリックして登録を完了してください。<br><br>
                メールが届いていない場合は、下のボタンから再送信できます。
            </div>

            {{-- 成功メッセージ --}}
            @if (session('status') == 'verification-link-sent')
                <div class="alert alert-success text-sm mb-4">
                    ✅ 登録時のメールアドレス宛に新しい確認リンクを送信しました。
                </div>
            @endif
            @if (session('warning'))
                <div class="alert alert-warning shadow-lg my-4">
                    <div>
                        <span>{{ session('warning') }}</span>
                    </div>
                </div>
            @endif

            {{-- ボタン群 --}}
            <div class="mt-6 space-y-4">
                {{-- 再送信ボタン --}}
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">
                        メールを再送信する
                    </button>
                </form>

                {{-- ログアウトボタン --}}
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline w-full">
                        ログアウト
                    </button>
                </form>
            </div>
        </div>

    </div>
</x-guest-layout>
