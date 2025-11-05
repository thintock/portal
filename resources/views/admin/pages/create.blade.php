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
            <h1 class="text-2xl font-bold mb-6">新規固定ページの作成</h1>
    
            {{-- ✅ バリデーションエラー表示 --}}
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
            <form method="POST" action="{{ route('admin.pages.store') }}">
                @csrf
    
                {{-- スラッグ --}}
                <div class="mb-6">
                    <label for="slug" class="block font-semibold mb-1">
                        スラッグ（URL識別子）<span class="text-red-500">*</span>
                    </label>
                
                    <input type="text" name="slug" id="slug"
                           value="{{ old('slug', $presetSlug ?? '') }}"
                           class="w-full border rounded p-2 {{ isset($presetSlug) ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                           placeholder="例: terms, privacy, about"
                           {{ isset($presetSlug) ? 'readonly' : '' }}>
                
                    <p class="text-sm text-gray-500 mt-1">
                        URLとして使用されます（例: https://portal.bakerista.jp/terms）
                    </p>
                </div>
    
                {{-- タイトル --}}
                <div class="mb-6">
                    <label for="title" class="block font-semibold mb-1">タイトル</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title', $presetTitle ?? '') }}"
                           class="w-full border rounded p-2 {{ isset($presetTitle) && $presetTitle ? 'bg-gray-100 cursor-not-allowed' : '' }}"
                           placeholder="ページタイトルを入力してください"
                           {{ isset($presetTitle) && $presetTitle ? 'readonly' : '' }}>
                </div>
    
                {{-- 本文1 --}}
                <div class="mb-6">
                    <label for="body1" class="block font-semibold mb-1">本文1（HTML可）</label>
                    <textarea name="body1" id="body1" rows="10"
                              class="w-full border rounded p-2 font-mono"
                              placeholder="<h2>会社概要</h2> ...">{{ old('body1') }}</textarea>
                </div>
    
                {{-- 本文2 --}}
                <div class="mb-6">
                    <label for="body2" class="block font-semibold mb-1">本文2（HTML可）</label>
                    <textarea name="body2" id="body2" rows="10"
                              class="w-full border rounded p-2 font-mono"
                              placeholder="<h2>沿革</h2> ...">{{ old('body2') }}</textarea>
                </div>
    
                {{-- 本文3 --}}
                <div class="mb-6">
                    <label for="body3" class="block font-semibold mb-1">本文3（HTML可）</label>
                    <textarea name="body3" id="body3" rows="10"
                              class="w-full border rounded p-2 font-mono"
                              placeholder="<h2>お問い合わせ先</h2> ...">{{ old('body3') }}</textarea>
                </div>
    
                {{-- ステータス --}}
                <div class="mb-6">
                    <label class="block font-semibold mb-1">ステータス</label>
                    <select name="status" id="status" class="border rounded p-2 bg-gray-100 cursor-not-allowed" disabled>
                        <option value="draft" selected>下書き</option>
                    </select>
                    <p class="text-sm text-gray-500 mt-1">※ 作成時は常に下書きになります。公開するには編集画面で変更してください。</p>
                </div>
    
                {{-- ボタン群 --}}
                <div class="flex justify-between items-center mt-8">
                    <a href="{{ route('admin.pages.index') }}"
                       class="text-gray-600 hover:underline">← 一覧に戻る</a>
    
                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-5 py-2 rounded shadow">
                        下書きとして保存
                    </button>
                </div>
            </form>
        </div>
        {{-- ✅ 簡易HTMLエディタ（TinyMCE or Trixを利用可能） --}}
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
