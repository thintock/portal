<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">月次テーマ管理</h1>
            <p class="text-sm text-gray-500">対象月・商品名・説明・成分値・受付期間・公開状態を編集します。</p>
        </div>
    @endsection

    <div class="w-full">

        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4" >
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-bold">月次テーマ編集</h1>
                </div>

                <a href="{{ route('admin.monthly-items.index') }}" class="btn btn-sm btn-outline">
                    ← 一覧へ戻る
                </a>
            </div>

            <form method="POST" action="{{ route('admin.monthly-items.update', $monthlyItem) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- 対象月 --}}
                    <div>
                        <label class="block font-semibold mb-1">対象月</label>
                        <input
                            type="month"
                            name="month"
                            value="{{ old('month', $monthlyItem->month) }}"
                            class="input input-bordered w-full"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            例：2026-01（DBには YYYY-MM 形式で保存されます）
                        </p>
                    </div>
                    
                    {{-- 情報公開日 --}}
                    <div>
                        <label class="block font-semibold mb-1">情報公開日</label>
                        <input
                            type="datetime-local"
                            name="published_at"
                            value="{{ old('published_at', $monthlyItem->published_at ? $monthlyItem->published_at->format('Y-m-d\TH:i') : '') }}"
                            class="input input-bordered w-full"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            公開状態の場合、この日時以降にユーザーダッシュボードへ表示。
                        </p>
                    </div>
                </div>

                {{-- 商品名 --}}
                <div>
                    <label class="block font-semibold mb-1">商品名</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title', $monthlyItem->title) }}"
                        class="input input-bordered w-full"
                        placeholder="例：中川農場 ブレドゥポピュラシオン"
                    >
                </div>

                {{-- 説明 --}}
                <div>
                    <label class="block font-semibold mb-1">説明</label>
                    <textarea
                        name="description"
                        rows="5"
                        class="textarea textarea-bordered w-full"
                        placeholder="会員に伝える説明（原産、特徴、焼き方のヒント等）"
                    >{{ old('description', $monthlyItem->description) }}</textarea>
                </div>

                {{-- 成分・吸水（数値） --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">タンパク値（%）</label>
                        <div class="join w-full">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="100"
                                name="protein"
                                value="{{ old('protein', $monthlyItem->protein) }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：10.5"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">灰分値（%）</label>
                        <div class="join w-full">
                            <input
                                type="number"
                                step="0.01"
                                min="0"
                                max="100"
                                name="ash"
                                value="{{ old('ash', $monthlyItem->ash) }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：1.00"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">吸水率（%）</label>
                        <div class="join w-full">
                            <input
                                type="number"
                                step="0.1"
                                min="0"
                                max="200"
                                name="absorption"
                                value="{{ old('absorption', $monthlyItem->absorption) }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：70.0"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                    </div>
                </div>

                {{-- フィードバック受付期間 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">フィードバック開始日時</label>
                        <input
                            type="datetime-local"
                            name="feedback_start_at"
                            value="{{ old('feedback_start_at', $monthlyItem->feedback_start_at ? $monthlyItem->feedback_start_at->format('Y-m-d\TH:i') : '') }}"
                            class="input input-bordered w-full"
                            required
                        >
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">フィードバック締切日時</label>
                        <input
                            type="datetime-local"
                            name="feedback_end_at"
                            value="{{ old('feedback_end_at', $monthlyItem->feedback_end_at ? $monthlyItem->feedback_end_at->format('Y-m-d\TH:i') : '') }}"
                            class="input input-bordered w-full"
                            required
                        >
                    </div>
                </div>

                {{-- ステータス --}}
                <div>
                    <label class="block font-semibold mb-1">ステータス</label>
                    <select name="status" class="select select-bordered w-full">
                        <option value="draft" @selected(old('status', $monthlyItem->status) === 'draft')>下書き</option>
                        <option value="published" @selected(old('status', $monthlyItem->status) === 'published')>公開</option>
                    </select>

                    @php
                        $isActive =
                            ($monthlyItem->status === 'published')
                            && $monthlyItem->feedback_start_at
                            && $monthlyItem->feedback_end_at
                            && now()->between($monthlyItem->feedback_start_at, $monthlyItem->feedback_end_at);
                    @endphp

                    <div class="mt-2 text-xs text-gray-600">
                        現在：
                        @if($isActive)
                            <span class="badge badge-success badge-sm">受付中</span>
                        @else
                            <span class="badge badge-neutral badge-sm">受付外</span>
                        @endif
                    </div>
                </div>

                {{-- 操作ボタン --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3 pt-4">
                    <div class="flex gap-2">
                        <a href="{{ route('admin.monthly-items.index') }}" class="btn btn-sm btn-outline">
                            ← 一覧へ戻る
                        </a>

                        <a href="{{ route('admin.monthly-items.show', $monthlyItem) }}" class="btn btn-sm btn-ghost">
                            詳細を見る
                        </a>
                    </div>

                    <div class="flex gap-2 sm:justify-end">
                        <button type="submit" class="btn btn-sm btn-primary">
                            更新
                        </button>
                    </form>
                    <form method="POST" action="{{ route('admin.monthly-items.destroy', $monthlyItem) }}" onsubmit="return confirm('削除してよろしいですか？この操作は元に戻せません。')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-error text-white">
                            削除
                        </button>
                    </form>
                    </div>
                </div>
        </div>

        {{-- カバー・ギャラリー画像（Livewireコンポーネント） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4">
            <livewire:admin.monthly-item-images :monthlyItem="$monthlyItem" />
        </div>
        
        {{-- メッセージ一覧（FeedbackPost） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4">
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-bold">メッセージ一覧</h1>
                    <p class="text-sm text-gray-500">この月次テーマに投稿されたメッセージ（FeedbackPost）</p>
                </div>
                <div>
                    <a href="{{ route('admin.monthly-items.report', $monthlyItem) }}" class="btn btn-sm btn-outline">
                      印刷レポート
                    </a>
                </div>
            </div>
        
          <livewire:admin.monthly-item-feedback-posts
            :monthlyItemId="$monthlyItem->id"
            lazy
          />
        </div>

    </div>
</x-admin-layout>
