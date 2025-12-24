@section('title', 'ようこそベイクルへ 🎉')
{{-- resources/views/billing/success.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            ご登録完了
        </h2>
    </x-slot>

    <div class="py-10 relative overflow-hidden">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl p-8 text-center space-y-6">

                {{-- メインメッセージ --}}
                <h1 class="text-3xl font-extrabold text-green-600">
                    🎉 ようこそ、ベイクルへ！
                </h1>

                <p class="text-gray-700 leading-relaxed">
                    サブスクリプションのご登録が完了しました。<br>
                    今日から、パンづくりを通じた<br class="hidden sm:block">
                    <span class="font-semibold">学び・交流・つながり</span>のある時間が始まります。
                </p>

                {{-- 次にやってほしいこと --}}
                <div class="bg-base-200 rounded-xl p-6 text-left">
                    <h2 class="text-lg font-bold mb-2">
                        まずはプロフィールを完成させましょう
                    </h2>
                    <p class="text-sm text-gray-600 leading-relaxed">
                        お名前やお住まいの地域などを登録することで、<br class="hidden sm:block">
                        毎月の食材のお届けや、イベント参加やコミュニティ内での交流が、よりスムーズになります。
                    </p>
                </div>

                {{-- CTA --}}
                <div class="flex flex-col sm:flex-row justify-center gap-4 pt-2">
                    <a href="{{ route('profile.edit') }}"
                       class="btn btn-primary btn-wide">
                        プロフィールを編集する
                    </a>

                    <a href="{{ route('dashboard') }}"
                       class="btn btn-outline">
                        ダッシュボードへ
                    </a>
                </div>

                {{-- サブ導線 --}}
                <div class="pt-2">
                    <a href="{{ route('billing.portal') }}"
                       class="text-sm text-gray-500 hover:text-primary underline">
                        支払い情報を確認・変更する
                    </a>
                </div>

            </div>
        </div>
    </div>

    {{-- 紙吹雪 --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const duration = 2500;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 6,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 6,
                    angle: 120,
                    spread: 55,
                    origin: { x: 1 }
                });

                if (Date.now() < end) {
                    requestAnimationFrame(frame);
                }
            })();
        });
    </script>
</x-app-layout>
