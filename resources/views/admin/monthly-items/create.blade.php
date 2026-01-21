<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">月次テーマ管理</h1>
            <p class="text-sm text-gray-500">
                対象月・商品名・説明・成分値・受付期間・公開状態を登録します。
            </p>
        </div>
    @endsection

    <div class="w-full">

        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">
            
            <div class="flex items-center justify-between gap-3 mb-6">
                <div>
                    <h1 class="text-2xl font-bold">月次テーマを作成</h1>
                </div>

                <a href="{{ route('admin.monthly-items.index') }}" class="btn btn-sm btn-outline">
                    ← 一覧へ戻る
                </a>
            </div>

            <form
                method="POST"
                action="{{ route('admin.monthly-items.store') }}"
                class="space-y-6"
            >
                @csrf
                
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    {{-- 月（YYYY-MM） --}}
                    <div>
                        <label class="block font-semibold mb-1">
                            対象月 <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="month"
                            name="month"
                            value="{{ old('month') }}"
                            class="input input-bordered w-full"
                            required
                        >
                        @error('month')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    {{-- 情報公開日 --}}
                    <div>
                        <label class="block font-semibold mb-1">
                            情報公開日 <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="published_at"
                            value="{{ old('published_at') }}"
                            class="input input-bordered w-full"
                            required
                        >
                        <p class="text-xs text-gray-500 mt-1">
                            公開状態（published）の場合、この日時以降にユーザーダッシュボード等へ表示する想定です。
                        </p>
                        @error('published_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                {{-- 商品名 --}}
                <div>
                    <label class="block font-semibold mb-1">商品名</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        class="input input-bordered w-full"
                        placeholder="例：中川農場 ブレドゥ・ポピュラシオン"
                    >
                    @error('title')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 説明 --}}
                <div>
                    <label class="block font-semibold mb-1">説明</label>
                    <textarea
                        name="description"
                        rows="4"
                        class="textarea textarea-bordered w-full"
                        placeholder="会員向けの説明文。何を届けたか、どう楽しんでほしいか等"
                    >{{ old('description') }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
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
                                value="{{ old('protein') }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：10.5"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                        @error('protein')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                                value="{{ old('ash') }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：1.00"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                        @error('ash')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
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
                                value="{{ old('absorption') }}"
                                class="input input-bordered join-item w-full"
                                placeholder="例：70.0"
                            >
                            <span class="btn join-item btn-ghost pointer-events-none">%</span>
                        </div>
                        @error('absorption')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- フィードバック受付期間 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">
                            フィードバック受付開始 <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="feedback_start_at"
                            value="{{ old('feedback_start_at') }}"
                            class="input input-bordered w-full"
                            required
                        >
                        @error('feedback_start_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">
                            フィードバック受付終了 <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="datetime-local"
                            name="feedback_end_at"
                            value="{{ old('feedback_end_at') }}"
                            class="input input-bordered w-full"
                            required
                        >
                        @error('feedback_end_at')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- ステータス --}}
                <div>
                    <label class="block font-semibold mb-1">
                        ステータス <span class="text-red-500">*</span>
                    </label>
                    <select
                        name="status"
                        class="select select-bordered w-full"
                        required
                    >
                        <option value="draft" @selected(old('status', 'draft') === 'draft')>
                            下書き（非公開）
                        </option>
                        <option value="published" @selected(old('status') === 'published')>
                            公開
                        </option>
                    </select>
                    @error('status')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- 注意文 --}}
                <div class="p-4 bg-base-200 rounded text-sm text-gray-600">
                    ・この画面では基本情報のみを登録します。<br>
                    ・画像（小麦写真など）は作成後、編集画面で追加できます。<br>
                    ・公開状態（published）でも、情報公開日（published_at）以前は表示されない想定です。
                </div>

                {{-- ボタン --}}
                <div class="flex justify-between items-center pt-4">
                    <a
                        href="{{ route('admin.monthly-items.index') }}"
                        class="link text-gray-500"
                    >
                        ← 一覧へ戻る
                    </a>

                    <button type="submit" class="btn btn-primary">
                        作成
                    </button>
                </div>
            </form>
        </div>

    </div>
</x-admin-layout>
