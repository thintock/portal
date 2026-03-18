@section('title', 'ポイント交換履歴')

<x-app-layout>

<div class="p-1 py-10 bg-base-200">

<div class="max-w-6xl mx-auto sm:px-6 lg:px-8 space-y-8">


{{-- ヘッダー --}}
<div class="flex items-center justify-between">

<h2 class="text-xl font-bold text-primary flex items-center gap-3">
    <span class="badge badge-primary text-white">ベイクル</span>
    ポイント交換履歴
</h2>

<a href="{{ route('rewards.index') }}" class="btn btn-outline btn-sm">
    景品一覧へ戻る
</a>

</div>



{{-- 履歴カード --}}
<div class="card bg-white shadow-xl border border-base-300">

<div class="card-body">

<h3 class="text-lg font-bold text-primary mb-4">
交換履歴
</h3>


@if($redemptions->isEmpty())

<div class="text-center text-gray-500 py-10">
まだポイント交換は行われていません。
</div>

@else


<div class="overflow-x-auto">

<table class="table table-zebra">

<thead>
<tr>
<th>景品</th>
<th>使用ポイント</th>
<th>ステータス</th>
<th>日時</th>
</tr>
</thead>

<tbody>

@foreach($redemptions as $r)

<tr>

<td class="font-medium">
{{ $r->reward->name }}
</td>

<td>
<span class="badge badge-secondary">
{{ number_format($r->points_used) }} pt
</span>
</td>

<td>

@if($r->status === 'requested')
<span class="badge badge-warning">
申請中
</span>

@elseif($r->status === 'approved')
<span class="badge badge-info">
承認済
</span>

@elseif($r->status === 'shipped')
<span class="badge badge-success">
発送済
</span>

@else
<span class="badge">
{{ $r->status }}
</span>
@endif

</td>

<td class="text-sm text-gray-500">
{{ $r->created_at->format('Y-m-d H:i') }}
</td>

</tr>

@endforeach

</tbody>

</table>

</div>

@endif

</div>
</div>


</div>
</div>

</x-app-layout>