<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            有料会員プラン
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                @if (session('status'))
                    <div class="alert alert-success mb-4">{{ session('status') }}</div>
                @endif

                <form method="POST" action="{{ route('billing.subscribe') }}" class="space-y-4">
                    @csrf
                    <label class="block">
                        <span class="font-semibold">プランを選択</span>
                        <select name="price" class="select select-bordered w-full mt-2" required>
                            @if (config('services.stripe.prices.basic'))
                                <option value="{{ config('services.stripe.prices.basic') }}">ベーシック（月額）</option>
                            @endif
                            @if (config('services.stripe.prices.premium'))
                                <option value="{{ config('services.stripe.prices.premium') }}">プレミアム（月額）</option>
                            @endif
                        </select>
                    </label>

                    <button type="submit" class="btn btn-primary w-full">申し込む（Stripeへ）</button>
                </form>

                <div class="mt-6 text-center">
                    <a class="link" href="{{ route('billing.portal') }}">支払い情報の確認・変更（Billing Portal）</a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
