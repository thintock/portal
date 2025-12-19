<x-app-layout>
    @if(!$subscription)
        {{-- ===========================
             サブスク未契約
        ============================ --}}
        @include('billing.partials.landing', [
            'prices' => $prices,
            'isRecruiting' => $isRecruiting,
            'nextRecruitingAt' => $nextRecruitingAt,
        ])
        
    @elseif($subscription->stripe_status === 'active' && is_null($subscription->ends_at))
        {{-- ===========================
             サブスク契約中
        ============================ --}}
        @include('billing.partials.active', ['subscription' => $subscription, 'prices' => $prices])
        
    @elseif($subscription->stripe_status === 'active' && !is_null($subscription->ends_at))
        {{-- ===========================
             サブスク解約予定
        ============================ --}}
        @include('billing.partials.canceling', ['subscription' => $subscription, 'prices' => $prices])
        
    @endif
</x-app-layout>
