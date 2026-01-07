<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">お知らせ管理</h1>
            <p class="text-sm text-gray-500">お知らせを編集できます。</p>
        </div>
    @endsection

    <div class="w-full">

        {{-- 本体フォーム --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4">
            <h1 class="text-2xl font-bold mb-6">お知らせ編集</h1>

            <form method="POST" action="{{ route('admin.announcements.update', $announcement) }}" class="space-y-6" >
                @csrf
                @method('PATCH')

                {{-- タイトル --}}
                <div>
                    <label class="block font-semibold mb-1">
                        タイトル <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="title" value="{{ old('title', $announcement->title) }}" class="input input-bordered w-full" required>
                </div>

                {{-- スラッグ（通常は編集不可 / 必要なときのみ編集） --}}
                <div x-data="{ editingSlug: false }">
                    <label class="block font-semibold mb-1">URL</label>

                    <input type="text" name="slug" id="slug" value="{{ old('slug', $announcement->slug) }}" class="input input-bordered w-full" :readonly="!editingSlug" :class="editingSlug ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'">

                    <p class="text-sm text-gray-500 mt-1">
                        URL 例： https://portal.bakerista.jp/announcements/XXX
                        <span class="italic text-gray-600">{{ $announcement->slug }}</span>
                    </p>

                    {{-- 編集スイッチ --}}
                    <label class="mt-3 flex items-center gap-2 cursor-pointer select-none text-sm">
                        <input type="checkbox" x-model="editingSlug" class="checkbox checkbox-sm checkbox-primary">
                        <span class="text-gray-700">URL（スラッグ）を編集する</span>
                    </label>

                    <p class="text-xs text-gray-500 mt-1" x-show="editingSlug">
                        ※ スラッグを変更すると既存リンクが無効になる可能性があります。
                    </p>
                </div>

                {{-- 公開範囲 --}}
                <div>
                    <label class="block font-semibold mb-1">公開範囲 <span class="text-red-500">*</span></label>
                    <select name="visibility" class="select select-bordered w-full" required>
                        <option value="membership" @selected(old('visibility', $announcement->visibility) === 'membership')>
                            ベイクル限定
                        </option>
                        <option value="public" @selected(old('visibility', $announcement->visibility) === 'public')>
                            一般公開
                        </option>
                        <option value="admin" @selected(old('visibility', $announcement->visibility) === 'admin')>
                            管理者限定
                        </option>
                    </select>
                </div>

                {{-- 公開期間（開始 / 終了） --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">公開開始日時</label>
                        <input type="datetime-local" name="publish_start_at" value="{{ old('publish_start_at', $announcement->publish_start_at ? $announcement->publish_start_at->format('Y-m-d\TH:i') : '') }}" class="input input-bordered w-full">
                        <p class="text-xs text-gray-500 mt-1">
                            未設定の場合は「即時公開扱い（開始制限なし）」にできます。
                        </p>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">公開終了日時</label>
                        <input type="datetime-local" name="publish_end_at" value="{{ old('publish_end_at', $announcement->publish_end_at ? $announcement->publish_end_at->format('Y-m-d\TH:i') : '') }}" class="input input-bordered w-full">
                        <p class="text-xs text-gray-500 mt-1">
                            未設定の場合は「終了なし（常時公開）」にできます。
                        </p>
                    </div>
                </div>

                {{-- 本文（HTML可 / TinyMCE） --}}
                <div class="mb-6">
                    <label class="block font-semibold mb-1">本文（HTML可）</label>
                    <textarea id="body-editor" name="body" rows="15" class="textarea textarea-bordered w-full" >{{ old('body', $announcement->body) }}</textarea>
                </div>

                {{-- ボタン --}}
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('admin.announcements.index') }}" class="link text-gray-500">← 一覧へ戻る</a>

                    <div class="flex items-center gap-2">
                        <button type="submit" class="btn btn-primary">更新</button>
            </form>
                        <form method="POST" action="{{ route('admin.announcements.destroy', $announcement) }}" onsubmit="return confirm('本当にこのお知らせを削除しますか？');" class="btn btn-error">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-error">
                                削除する
                            </button>
                        </form>
                    </div>
                </div>
        </div>
        
        {{-- 画像（Livewire） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-8">
            <livewire:admin.announcement-images :announcement="$announcement" />
        </div>

        {{-- TinyMCE --}}
        <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          tinymce.init({
            selector: '#body-editor',
            height: 420,
            menubar: false,
            plugins: 'link image code lists',
            toolbar: 'undo redo | formatselect | bold italic underline | bullist numlist | link image | code',
            content_style: "body { font-family: sans-serif; font-size:14px }",
          });
        });
        </script>

    </div>
</x-admin-layout>
