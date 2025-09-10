<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl">ダッシュボード</h2></x-slot>

    <div class="p-6">
        @auth
            @if(auth()->user()->subscribed('default'))
                <div class="alert alert-success mb-4">現在：<b>有料会員</b></div>
                <a href="{{ route('billing.portal') }}" class="btn btn-outline">支払い情報の確認・変更</a>
                <a href="{{ route('community.index') }}" class="btn btn-primary ml-2">コミュニティへ</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ml-2">管理者画面へ</a>
            @else
                <div class="alert alert-info mb-4">現在：<b>無料会員</b></div>
                <a href="{{ route('billing.show') }}" class="btn btn-primary">有料会員へアップグレード</a>
                <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary ml-2">管理者画面へ</a>
            @endif
        @endauth
    </div>
</x-app-layout>
