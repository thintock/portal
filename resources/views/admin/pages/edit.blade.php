<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">固定ページ管理</h1>
            <p class="text-sm text-gray-500">サイト内の静的ページを作成・編集できます。</p>
        </div>
    @endsection
    
    <div class="w-full">
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">
    
            {{-- ✅ ページタイトル --}}
            <h1 class="text-2xl font-bold mb-6">ページ編集：{{ $page->title ?? 'タイトル未設定' }}</h1>
    
            {{-- ✅ 成功メッセージ --}}
            @if (session('success'))
                <div class="bg-green-100 border border-green-300 text-green-700 px-4 py-2 rounded mb-4">
                    {{ session('success') }}
                </div>
            @endif
    
            {{-- ✅ エラー表示 --}}
            @if ($errors->any())
                <div class="bg-red-100 border border-red-300 text-red-700 px-4 py-3 rounded mb-6">
                    <ul class="list-disc ml-6">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
    
            {{-- ✅ フォーム --}}
            <form method="POST" action="{{ route('admin.pages.update', $page->id) }}">
                @csrf
                @method('PUT')
    
                {{-- スラッグ --}}
                <div class="mb-6">
                    <label for="slug" class="block font-semibold mb-1">スラッグ（URL識別子）<span class="text-red-500">*</span></label>
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug', $page->slug) }}"
                           class="w-full border rounded p-2"
                           placeholder="例: terms, privacy, about">
                    <p class="text-sm text-gray-500 mt-1">URLとして使用されます（例: https://portal.bakerista.jp/terms）</p>
                </div>
    
                {{-- タイトル --}}
                <div class="mb-6">
                    <label for="title" class="block font-semibold mb-1">タイトル</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $page->title) }}"
                           class="w-full border rounded p-2"
                           placeholder="ページタイトルを入力してください">
                </div>
    
                {{-- 本文1 --}}
                <div class="mb-6">
                    <label for="body1" class="block font-semibold mb-1">本文1（HTML可）</label>
                    <textarea name="body1" id="body1" rows="10"
                              class="w-full border rounded p-2 font-mono">{{ old('body1', $page->body1) }}</textarea>
                </div>
    
                {{-- 本文2 --}}
                <div class="mb-6">
                    <label for="body2" class="block font-semibold mb-1">本文2（HTML可）</label>
                    <textarea name="body2" id="body2" rows="10"
                              class="w-full border rounded p-2 font-mono">{{ old('body2', $page->body2) }}</textarea>
                </div>
    
                {{-- 本文3 --}}
                <div class="mb-6">
                    <label for="body3" class="block font-semibold mb-1">本文3（HTML可）</label>
                    <textarea name="body3" id="body3" rows="10"
                              class="w-full border rounded p-2 font-mono">{{ old('body3', $page->body3) }}</textarea>
                </div>
    
                {{-- ステータス --}}
                <div class="mb-6">
                    <label for="status" class="block font-semibold mb-1">ステータス</label>
                    <select name="status" id="status" class="border rounded p-2">
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>下書き</option>
                        <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>公開</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">ステータスを変更して公開・非公開を切り替えできます。</p>
                </div>
    
                {{-- ボタン群 --}}
                <div class="flex justify-between items-center mt-10">
                    <div class="space-x-4">
                        <a href="{{ route('admin.pages.index') }}" class="text-gray-600 hover:underline">← 一覧に戻る</a>
    
                        {{-- プレビューリンク（published のみ有効） --}}
                        @if ($page->status === 'published')
                            <a href="{{ route('admin.pages.show', $page->id) }}" target="_blank"
                               class="text-blue-600 hover:underline">プレビュー</a>
                        @endif
                    </div>
    
                    <div class="flex space-x-4 items-center">
                        {{-- 保存ボタン --}}
                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded shadow">
                            保存
                        </button>
                      
                    </form>  
                    {{-- 削除ボタン --}}
                    <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST" 
                          onsubmit="return confirm('本当に削除しますか？')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="text-red-600 hover:underline">削除</button>
                    </form>
                </div>
            </div>
        </div>
        {{-- ✅ HTMLエディタ --}}
        <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector: 'textarea[name^="body"]',
                height: 400,
                menubar: false,
                plugins: 'link lists code table',
                toolbar: 'undo redo | bold italic underline | alignleft aligncenter alignright | bullist numlist | link table | code',
                content_css: false,
            });
        </script>

    </div>
</x-admin-layout>
