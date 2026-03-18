@section('title', 'ポイント交換')

<x-app-layout>

<div class="p-1 py-10 bg-base-200">

<div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">


{{-- ヘッダー --}}
<div class="flex items-center justify-between">
    <h2 class="text-xl font-bold text-primary flex items-center gap-3">
        <span class="badge badge-primary text-white">ベイクル</span>
        ポイント交換
    </h2>

    <a href="{{ route('rewards.history') }}" class="btn btn-outline btn-sm">
        交換履歴
    </a>
</div>


{{-- ポイント残高 --}}
<div class="card bg-white shadow-xl border border-base-300">
<div class="card-body">

<h3 class="text-lg font-bold text-primary mb-2">
現在のポイント
</h3>

<div class="text-3xl font-bold text-secondary">
{{ number_format($balance) }} pt
</div>

<p class="text-sm text-gray-500 mt-2">
投稿やコメントでポイントが貯まります。  
貯まったポイントはベーカリスタオリジナルグッズと交換できます。
</p>

</div>
</div>



{{-- メッセージ --}}
@if(session('success'))
<div class="alert alert-success shadow-lg">
{{ session('success') }}
</div>
@endif

@if(session('error'))
<div class="alert alert-error shadow-lg">
{{ session('error') }}
</div>
@endif



{{-- 景品一覧 --}}
<div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">

@forelse($rewards as $reward)

<div class="card bg-white shadow-xl border border-base-300">

<div class="card-body">

<h3 class="card-title text-primary">
{{ $reward->name }}
</h3>

@if($reward->description)
<p class="text-sm text-gray-600">
{{ $reward->description }}
</p>
@endif


{{-- 必要ポイント --}}
<div class="mt-3 flex items-center justify-between">

<span class="badge badge-secondary badge-lg">
{{ number_format($reward->points_cost) }} pt
</span>

@if($reward->stock !== null)
<span class="text-xs text-gray-500">
在庫 {{ $reward->stock }}
</span>
@endif

</div>


{{-- ボタン --}}
<div class="card-actions justify-end mt-4">

@if($balance < $reward->points_cost)

<button class="btn btn-disabled btn-sm">
ポイント不足
</button>

@else

<form method="POST" action="{{ route('rewards.redeem',$reward) }}">
@csrf

<button class="btn btn-primary btn-sm">
交換する
</button>

</form>

@endif

</div>

</div>
</div>

@empty

<div class="col-span-full text-center text-gray-500">
現在交換できる景品はありません。
</div>

@endforelse

</div>


</div>
</div>

</x-app-layout>