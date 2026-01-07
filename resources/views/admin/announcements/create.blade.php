{{-- resources/views/admin/announcements/create.blade.php --}}

<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">お知らせ管理</h1>
            <p class="text-sm text-gray-500">運営からのお知らせを作成できます。</p>
        </div>
    @endsection

    <div class="w-full">

        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4">
            <h1 class="text-2xl font-bold mb-6">お知らせ作成</h1>

            {{-- バリデーションエラー --}}
            @if ($errors->any())
                <div class="alert alert-error mb-6">
                    <div class="space-y-1">
                        <div class="font-semibold">入力内容に不備があります。</div>
                        <ul class="list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <form method="POST" action="{{ route('admin.announcements.store') }}" class="space-y-6">
                @csrf

                {{-- タイトル --}}
                <div>
                    <label class="block font-semibold mb-1">
                        タイトル <span class="text-red-500">*</span>
                    </label>
                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           class="input input-bordered w-full"
                           required>
                </div>

                {{-- スラッグ：初期表示で自動生成して表示 --}}
                <div x-data="{ editingSlug: false }">
                    <label class="block font-semibold mb-1">URL</label>

                    <input type="text"
                           name="slug"
                           id="slug"
                           value="{{ old('slug') }}"
                           class="input input-bordered w-full"
                           :readonly="!editingSlug"
                           :class="editingSlug ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'"
                           placeholder="未入力の場合は自動生成されます">

                    <p class="text-sm text-gray-500 mt-1">
                        URL 例： https://portal.bakerista.jp/announcements/XXX
                        <span class="italic text-gray-600">（この画面で生成されます）</span>
                    </p>

                    {{-- 編集スイッチ --}}
                    <label class="mt-3 flex items-center gap-2 cursor-pointer select-none text-sm">
                        <input type="checkbox"
                               x-model="editingSlug"
                               class="checkbox checkbox-sm checkbox-primary">
                        <span class="text-gray-700">URL（スラッグ）を編集する</span>
                    </label>

                    <p class="text-xs text-gray-500 mt-1" x-show="editingSlug">
                        ※ スラッグはURLになります。変更するとリンクが変わるため注意してください。
                    </p>
                </div>

                {{-- 本文（HTML可：TinyMCE対応） --}}
                <div class="mb-6">
                    <label class="block font-semibold mb-1">本文</label>
                    <textarea id="body-editor"
                              name="body"
                              rows="15"
                              class="textarea textarea-bordered w-full">{{ old('body') }}</textarea>
                </div>

                {{-- 公開範囲 --}}
                <div>
                    <label class="block font-semibold mb-1">
                        公開範囲 <span class="text-red-500">*</span>
                    </label>
                    <select name="visibility" class="select select-bordered w-full" required>
                        <option value="membership" @selected(old('visibility', 'membership') === 'membership')>ベイクル限定</option>
                        <option value="public" @selected(old('visibility') === 'public')>一般公開</option>
                        <option value="admin" @selected(old('visibility') === 'admin')>管理者限定</option>
                    </select>
                    <p class="text-xs text-gray-500 mt-2">
                        ※ 一般公開を選択した場合、ベイクルユーザーを含む全てのユーザーに表示します。
                    </p>
                </div>

                {{-- 公開期間（表示制御） --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">公開開始日時</label>
                        <input type="datetime-local"
                               name="publish_start_at"
                               value="{{ old('publish_start_at') }}"
                               class="input input-bordered w-full">
                        <p class="text-xs text-gray-500 mt-2">
                            ※ 未設定の場合は「即時公開扱い」にできます（ユーザー側のクエリ次第）。
                        </p>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">公開終了日時</label>
                        <input type="datetime-local"
                               name="publish_end_at"
                               value="{{ old('publish_end_at') }}"
                               class="input input-bordered w-full">
                        <p class="text-xs text-gray-500 mt-2">
                            ※ 未設定の場合は「終了なし」にできます（ユーザー側のクエリ次第）。
                        </p>
                    </div>
                </div>

                {{-- ボタン --}}
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('admin.announcements.index') }}" class="link text-gray-500">← 一覧へ戻る</a>
                    <button type="submit" class="btn btn-primary">作成</button>
                </div>
            </form>
        </div>

        {{-- 画像は作成後に紐付ける想定（MediaFile/MediaRelation） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-8">
            <h2 class="text-lg font-bold text-gray-800 mb-2">画像</h2>
            <p class="text-sm text-gray-500">
                画像は作成後に編集画面で追加してください。
            </p>
        </div>

        {{-- slug自動生成（初回のみ） --}}
        <script>
        document.addEventListener('DOMContentLoaded', () => {
            const slug = document.querySelector('#slug');
            if (!slug) return;

            // ランダム slug 生成（衝突しにくい長さ）
            function generateRandomSlug() {
                return 'a-' + Math.random().toString(36).substring(2, 10);
            }

            // old() や バリデーション失敗で slug が戻ってきている場合は生成しない
            if (!slug.value) {
                slug.value = generateRandomSlug();
                slug.dataset.generated = "true";
            }

            // 手動入力が発生したらフラグを manual にする（保険）
            slug.addEventListener('input', () => {
                slug.dataset.generated = "manual";
            });
        });
        </script>

        {{-- TinyMCE --}}
        <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          tinymce.init({
            selector: '#body-editor',
            height: 400,
            menubar: false,
            plugins: 'link image code lists',
            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | code',
            content_style: "body { font-family: sans-serif; font-size:14px }",
          });
        });
        </script>

    </div>
</x-admin-layout>
