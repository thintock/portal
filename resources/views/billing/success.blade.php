@section('title', 'ようこそベイクルへ 🎉')
{{-- resources/views/billing/success.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済完了
        </h2>
    </x-slot>

    <div class="py-8 relative overflow-hidden">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl p-8 text-center">
                <h1 class="text-3xl font-extrabold mb-4 text-green-600">
                    🎉 ようこそ、ベイクルへ！
                </h1>

                <p class="mb-6 text-gray-700 leading-relaxed">
                    決済が正常に完了しました。<br>
                    今日からあなたのパン作りが、<br>
                    繋がりを生み、ストーリーを紡ぎ始めます。
                </p>

                <div class="flex justify-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        ダッシュボードへ
                    </a>

                    <a class="btn btn-outline" href="{{ route('billing.portal') }}">
                        支払い情報の確認
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- 紙吹雪 --}}
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const duration = 3000;
            const end = Date.now() + duration;

            (function frame() {
                confetti({
                    particleCount: 5,
                    angle: 60,
                    spread: 55,
                    origin: { x: 0 }
                });
                confetti({
                    particleCount: 5,
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
