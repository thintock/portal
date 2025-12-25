<x-app-layout>

    {{-- ===========================
         現在サブスクしていない（未契約 or 解約済み）
    ============================ --}}
    @if(!$subscription || $subscription->stripe_status !== 'active')
        @include('billing.partials.landing', [
            'prices' => $prices,
            'isRecruiting' => $isRecruiting,
            'nextRecruitingAt' => $nextRecruitingAt,
        ])

    {{-- ===========================
         契約中（解約予定なし）
    ============================ --}}
    @elseif($subscription && $subscription->stripe_status === 'active' && is_null($subscription->ends_at))
        @include('billing.partials.active', [
            'subscription' => $subscription,
            'prices' => $prices
        ])

    {{-- ===========================
         契約中（解約予定あり）
    ============================ --}}
    @elseif($subscription && $subscription->stripe_status === 'active' && !is_null($subscription->ends_at))
        @include('billing.partials.canceling', [
            'subscription' => $subscription,
            'prices' => $prices
        ])

    @endif

</x-app-layout>
