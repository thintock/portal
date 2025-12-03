@section('title', '決済画面から戻りました')

<x-app-layout>

    @php
        $isActive = $subscription && $subscription->stripe_status === 'active' && is_null($subscription->ends_at);
        $isCanceling = $subscription && $subscription->stripe_status === 'active' && !is_null($subscription->ends_at);
        $isNone = !$subscription;
    @endphp

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            決済画面から戻りました
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm sm:rounded-lg p-6 text-center">

                {{-- ==================================================
                     ケース①：契約中のユーザー（active）
                =================================================== --}}
                @if($isActive)
                    <h1 class="text-2xl font-bold mb-4 text-primary">
                        ベイクルをご利用いただきありがとうございます！
                    </h1>
                    <p class="mb-6 text-gray-700 leading-relaxed">
                        Stripeの画面から戻られました。<br>
                        あなたのサブスクリプションは引き続き有効です。
                    </p>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('billing.show') }}" class="btn btn-primary">
                            サブスクリプション管理へ戻る
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline">
                            ダッシュボードへ
                        </a>
                    </div>

                {{-- ==================================================
                     ケース②：解約予定のユーザー（canceling）
                =================================================== --}}
                @elseif($isCanceling)
                    <h1 class="text-2xl font-bold mb-4 text-yellow-600">
                        解約手続きは完了済みです
                    </h1>
                    <p class="mb-6 text-gray-700 leading-relaxed">
                        Stripeの画面から戻られました。<br>
                        現在のサブスクリプションは解約予定です。
                    </p>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('billing.show') }}" class="btn btn-primary">
                            サブスクリプション状況を確認する
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline">
                            ダッシュボードへ
                        </a>
                    </div>

                {{-- ==================================================
                     ケース③：未契約（決済中断）
                =================================================== --}}
                @else
                    <h1 class="text-2xl font-bold mb-4 text-red-600">
                        決済は完了しませんでした
                    </h1>
                    <p class="mb-6 text-gray-700 leading-relaxed">
                        Stripeの決済ページから戻られました。<br>
                        サブスクリプションはまだ開始されていません。
                    </p>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('billing.show') }}" class="btn btn-primary">
                            プラン選択ページへ
                        </a>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline">
                            ダッシュボードへ
                        </a>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
