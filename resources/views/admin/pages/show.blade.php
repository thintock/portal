<x-admin-layout>
    <div class="max-w-5xl mx-auto py-10 px-6">

        {{-- ✅ ページタイトル --}}
        <h1 class="text-2xl font-bold mb-6">{{ $page->title ?? 'タイトル未設定' }}</h1>

        {{-- ✅ ステータス表示 --}}
        <div class="mb-4">
            @if($page->status === 'published')
                <span class="bg-green-100 text-green-800 text-sm px-3 py-1 rounded">公開中</span>
            @else
                <span class="bg-gray-200 text-gray-700 text-sm px-3 py-1 rounded">下書き</span>
            @endif
        </div>

        {{-- ✅ スラッグ・作成者情報 --}}
        <div class="text-sm text-gray-600 mb-8 space-y-1">
            <p><strong>スラッグ：</strong> {{ $page->slug }}</p>
            <p><strong>作成者：</strong> {{ optional($page->creator)->name ?? '不明' }}</p>
            <p><strong>更新者：</strong> {{ optional($page->updater)->name ?? '不明' }}</p>
            <p><strong>最終更新：</strong> {{ $page->updated_at?->format('Y-m-d H:i') }}</p>
        </div>

        <hr class="my-6">

        {{-- ✅ 本文表示 --}}
        <div class="prose max-w-none">
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

        <hr class="my-6">

        {{-- ✅ ボタン群 --}}
        <div class="flex justify-between items-center mt-10">
            <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:underline">← 一覧に戻る</a>

            <div class="flex space-x-4">
                <a href="{{ route('admin.pages.edit', $page->id) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-5 py-2 rounded shadow">
                    編集
                </a>

                @if($page->status === 'published')
                    <a href="{{ route('pages.show', $page->slug) }}" target="_blank"
                       class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded shadow">
                        公開ページを表示
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
