{{-- resources/views/billing/success.blade.php --}}
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済完了
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                <h1 class="text-2xl font-bold mb-4 text-green-600">ご登録ありがとうございます！</h1>
                <p class="mb-6 text-gray-700">
                    Stripeでの決済が正常に完了しました。<br>
                    あなたのサブスクリプションは有効化されています。
                </p>

                <div class="flex justify-center space-x-4">
                    <a href="{{ route('dashboard') }}" class="btn btn-primary">
                        ダッシュボードへ戻る
                    </a>
                    <a href="" class="btn btn-outline">
                        コミュニティページへ
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
