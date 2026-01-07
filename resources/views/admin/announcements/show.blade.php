{{-- resources/views/admin/announcements/show.blade.php --}}

<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">お知らせ管理</h1>
            <p class="text-sm text-gray-500">お知らせの詳細を確認できます。</p>
        </div>
    @endsection

    <div class="w-full space-y-6">

        {{-- 本体 --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">
            <div class="flex items-start justify-between gap-4">
                <div class="min-w-0">
                    <h1 class="text-2xl font-bold text-gray-800 break-words">
                        {{ $announcement->title }}
                    </h1>

                    <div class="mt-3 flex flex-wrap items-center gap-2 text-sm text-gray-600">
                        {{-- visibility --}}
                        @php
                            $v = $announcement->visibility;
                        @endphp

                        @if($v === 'membership')
                            <span class="badge badge-primary">ベイクル限定</span>
                        @elseif($v === 'public')
                            <span class="badge badge-ghost">一般公開</span>
                        @elseif($v === 'admin')
                            <span class="badge badge-neutral">管理者限定</span>
                        @else
                            <span class="badge">未設定</span>
                        @endif

                        {{-- 公開期間 --}}
                        <span class="text-gray-500">
                            公開：
                            {{ $announcement->publish_start_at?->format('Y/m/d H:i') ?? '即時' }}
                            〜
                            {{ $announcement->publish_end_at?->format('Y/m/d H:i') ?? '無期限' }}
                        </span>

                        {{-- 更新日時 --}}
                        <span class="text-gray-400">
                            更新：{{ $announcement->updated_at?->format('Y/m/d H:i') }}
                        </span>
                    </div>

                    <div class="mt-2 text-xs text-gray-500">
                        URL: <span class="font-mono">{{ $announcement->slug }}</span>
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-sm btn-outline">
                        編集
                    </a>
                </div>
            </div>

            {{-- 本文 --}}
            <div class="mt-6">
                <div class="prose max-w-none">
                    {!! $announcement->body !!}
                </div>

                @if(empty($announcement->body))
                    <div class="p-4 bg-base-200 rounded-lg text-sm text-base-content/60 mt-4">
                        本文は未入力です。
                    </div>
                @endif
            </div>
        </div>

        {{-- 画像（ユーザー側と同じ表示） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">画像</h2>
                    <p class="text-sm text-gray-500">カバー + ギャラリー（MediaFile / MediaRelation）</p>
                </div>
        
                <a href="{{ route('admin.announcements.edit', $announcement) }}#images"
                   class="link link-primary text-sm">
                    編集画面で追加/並び替え
                </a>
            </div>
        
            {{-- カバー --}}
            @if($cover)
                @php
                    $coverUrl = $cover->url ?? Storage::url($cover->path);
                @endphp
        
                <div class="mb-4">
                    <div class="text-sm font-semibold text-gray-700 mb-2">カバー画像</div>
        
                    <button
                        type="button"
                        class="block w-full rounded-lg overflow-hidden border border-base-300 hover:opacity-95 transition"
                        onclick="
                          window.dispatchEvent(new CustomEvent('open-modal', { detail: 'image-viewer' }));
                          window.dispatchEvent(new CustomEvent('set-image', { detail: { src: '{{ $coverUrl }}' } }));
                        "
                    >
                        <img src="{{ $coverUrl }}"
                             alt="{{ $announcement->title }}"
                             class="w-full h-56 sm:h-72 object-cover">
                    </button>
                </div>
            @endif
        
            {{-- ギャラリー --}}
            @if(($gallery ?? collect())->isNotEmpty())
                <div>
                    <div class="text-sm font-semibold text-gray-700 mb-2">ギャラリー画像</div>
        
                    <div class="flex gap-2 overflow-x-auto pb-2">
                        @foreach($gallery as $img)
                            @php
                                $url = $img->url ?? Storage::url($img->path);
                            @endphp
        
                            <button
                                type="button"
                                class="block flex-shrink-0"
                                onclick="
                                  window.dispatchEvent(new CustomEvent('open-modal', { detail: 'image-viewer' }));
                                  window.dispatchEvent(new CustomEvent('set-image', { detail: { src: '{{ $url }}' } }));
                                "
                            >
                                <img
                                    src="{{ $url }}"
                                    alt="announcement gallery image"
                                    class="w-24 h-24 object-cover rounded-md border border-base-300 hover:opacity-90 transition"
                                />
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif
        
            {{-- 画像が1枚もない場合 --}}
            @if(!$cover && (($gallery ?? collect())->isEmpty()))
                <div class="p-6 bg-base-200 rounded-lg text-center text-sm text-gray-500">
                    画像はまだ登録されていません。
                </div>
            @endif
        </div>

        {{-- フッター導線 --}}
        <div class="flex items-center justify-between">
            <a href="{{ route('admin.announcements.index') }}" class="link text-gray-500">← 一覧へ戻る</a>
            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="btn btn-primary btn-sm">編集する</a>
        </div>

    </div>
</x-admin-layout>
