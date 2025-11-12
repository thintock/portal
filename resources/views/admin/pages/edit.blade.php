<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">固定ページ管理</h1>
            <p class="text-sm text-gray-500">サイト内の静的ページを作成・編集できます。</p>
        </div>
    @endsection

    <div class="w-full">
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">

            <h1 class="text-2xl font-bold mb-6">ページ編集：{{ $page->title ?? 'タイトル未設定' }}</h1>

            {{-- 成功メッセージ --}}
            @if (session('success'))
                <div class="alert alert-success mb-4">
                    {{ session('success') }}
                </div>
            @endif

            {{-- エラー表示 --}}
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <ul class="list-disc ml-6 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- フォーム --}}
            <form method="POST" action="{{ route('admin.pages.update', $page->id) }}" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- スラッグ --}}
                <div>
                    <label for="slug" class="block font-semibold mb-1">
                        スラッグ（URL識別子） <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug', $page->slug) }}"
                           placeholder="例: terms, privacy, about"
                           class="input input-bordered w-full">
                    <p class="text-sm text-gray-500 mt-1">
                        URLとして使用されます（例: https://portal.bakerista.jp/terms）
                    </p>
                </div>

                {{-- タイトル --}}
                <div>
                    <label for="title" class="block font-semibold mb-1">タイトル</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $page->title) }}"
                           placeholder="ページタイトルを入力してください"
                           class="input input-bordered w-full">
                </div>

                {{-- 本文1〜3 --}}
                @foreach (['body1' => '本文1（HTML可）', 'body2' => '本文2（HTML可）', 'body3' => '本文3（HTML可）'] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block font-semibold mb-1">{{ $label }}</label>
                        <textarea name="{{ $field }}" id="{{ $field }}" rows="10"
                                  class="textarea textarea-bordered w-full font-mono">{{ old($field, $page->$field) }}</textarea>
                    </div>
                @endforeach

                {{-- ステータス --}}
                <div>
                    <label for="status" class="block font-semibold mb-1">ステータス</label>
                    <select name="status" id="status" class="select select-bordered w-full">
                        <option value="draft" {{ old('status', $page->status) === 'draft' ? 'selected' : '' }}>下書き</option>
                        <option value="published" {{ old('status', $page->status) === 'published' ? 'selected' : '' }}>公開</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">ステータスを変更して公開・非公開を切り替えできます。</p>
                </div>

                {{-- ボタン群 --}}
                <div class="flex justify-between items-center pt-6">
                    <div class="space-x-4">
                        <a href="{{ route('admin.pages.index') }}" class="link text-gray-500">← 一覧に戻る</a>

                        @if ($page->status === 'published')
                            <a href="{{ route('admin.pages.show', $page->id) }}" target="_blank"
                               class="link link-primary">プレビュー</a>
                        @endif
                    </div>

                    <div class="flex items-center gap-4">
                        <button type="submit" class="btn btn-primary">保存</button>

            </form>

            <form action="{{ route('admin.pages.destroy', $page->id) }}" method="POST"
                  onsubmit="return confirm('本当に削除しますか？')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-error btn-outline">削除</button>
            </form>
        </div>
    </div>

    {{-- TinyMCEエディタ --}}
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
</x-admin-layout>
