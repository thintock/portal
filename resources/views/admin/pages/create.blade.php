<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">固定ページ管理</h1>
            <p class="text-sm text-gray-500">サイト内の静的ページを作成・編集できます。</p>
        </div>
    @endsection

    <div class="w-full">
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">

            <h1 class="text-2xl font-bold mb-6">新規固定ページの作成</h1>

            {{-- バリデーションエラー --}}
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
            <form method="POST" action="{{ route('admin.pages.store') }}" class="space-y-6">
                @csrf

                {{-- タイトル --}}
                <div>
                    <label for="title" class="block font-semibold mb-1">タイトル</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $presetTitle ?? '') }}"
                           placeholder="ページタイトルを入力してください"
                           {{ isset($presetTitle) && $presetTitle ? 'readonly' : '' }}
                           class="input input-bordered w-full {{ isset($presetTitle) && $presetTitle ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                </div>

                {{-- スラッグ --}}
                <div>
                    <label for="slug" class="block font-semibold mb-1">
                        URL <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug', $presetSlug ?? '') }}"
                           placeholder="例: terms, privacy, about"
                           {{ isset($presetSlug) ? 'readonly' : '' }}
                           class="input input-bordered w-full {{ isset($presetSlug) ? 'bg-gray-100 cursor-not-allowed' : '' }}">
                    <p class="text-sm text-gray-500 mt-1">
                        URLとして使用されます（例: https://portal.bakerista.jp/terms）
                    </p>
                </div>

                {{-- 本文1〜3 --}}
                @foreach (['body1' => '本文1（HTML可）', 'body2' => '本文2（HTML可）', 'body3' => '本文3（HTML可）'] as $field => $label)
                    <div>
                        <label for="{{ $field }}" class="block font-semibold mb-1">{{ $label }}</label>
                        <textarea name="{{ $field }}" id="{{ $field }}" rows="10"
                                  class="textarea textarea-bordered w-full font-mono"
                                  placeholder="<h2>セクションタイトル</h2> ...">{{ old($field) }}</textarea>
                    </div>
                @endforeach

                {{-- ステータス --}}
                <div>
                    <label class="block font-semibold mb-1">ステータス</label>
                    <select name="status" class="select select-bordered w-full bg-gray-100 cursor-not-allowed" disabled>
                        <option value="draft" selected>下書き</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">※作成時は常に下書きになります。公開するには編集画面で変更してください。</p>
                </div>

                {{-- ボタン群 --}}
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('admin.pages.index') }}" class="link text-gray-500">← 一覧へ戻る</a>
                    <button type="submit" class="btn btn-primary">下書きとして保存</button>
                </div>
            </form>
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

    </div>
</x-admin-layout>
