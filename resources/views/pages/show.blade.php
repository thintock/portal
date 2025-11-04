<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-6">

        {{-- ✅ ページタイトル --}}
        <h1 class="text-3xl font-bold mb-8">{{ $page->title ?? 'タイトル未設定' }}</h1>

        {{-- ✅ 本文表示 --}}
        <div class="prose max-w-none text-gray-800">
            @if($page->body1)
                <div class="mb-10">{!! $page->body1 !!}</div>
            @endif

            @if($page->body2)
                <div class="mb-10">{!! $page->body2 !!}</div>
            @endif

            @if($page->body3)
                <div class="mb-10">{!! $page->body3 !!}</div>
            @endif
        </div>
        
    </div>
</x-app-layout>
