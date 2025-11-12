<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">イベント管理</h1>
            <p class="text-sm text-gray-500">イベントを編集できます。</p>
        </div>
    @endsection

    <div class="w-full">

        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8">
            <h1 class="text-2xl font-bold mb-6">イベント編集</h1>

            <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @method('PATCH')

                {{-- タイトル --}}
                <div>
                    <label class="block font-semibold mb-1">タイトル <span class="text-red-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $event->title) }}"
                           class="input input-bordered w-full" required>
                </div>

                {{-- スラッグ（変更不可） --}}
                <div>
                    <label class="block font-semibold mb-1">スラッグ（変更できません）</label>
                    <input type="text" name="slug" value="{{ old('slug', $event->slug) }}"
                           class="input input-bordered w-full bg-gray-100 cursor-not-allowed"
                           readonly tabindex="-1">
                    <p class="text-sm text-gray-500 mt-1">
                        URLとして使用されます（例: https://portal.bakerista.jp/events/<span class="text-gray-600 italic">{{ $event->slug }}</span>）
                    </p>
                </div>

                {{-- イベント種別 --}}
                <div>
                    <label class="block font-semibold mb-1">イベント種別</label>
                    <input type="text" name="event_type" value="{{ old('event_type', $event->event_type) }}"
                           class="input input-bordered w-full">
                </div>

                {{-- 概要（body1：テキスト） --}}
                <div class="mb-6">
                  <label class="block font-semibold mb-1">概要</label>
                  <textarea 
                      name="body1" 
                      rows="5"
                      class="textarea textarea-bordered w-full font-mono"
                  >{{ old('body1', $event->body1) }}</textarea>
                </div>
                
                {{-- 詳細（body2：HTML入力 / TinyMCE対応） --}}
                <div class="mb-6">
                  <label class="block font-semibold mb-1">詳細（HTML可）</label>
                  <textarea 
                      id="body2-editor" 
                      name="body2" 
                      rows="15"
                      class="textarea textarea-bordered w-full"
                  >{{ old('body2', $event->body2) }}</textarea>
                </div>
                
                {{-- 完了報告（body3：テキスト） --}}
                <div class="mb-6">
                  <label class="block font-semibold mb-1">完了報告</label>
                  <textarea 
                      name="body3" 
                      rows="5"
                      class="textarea textarea-bordered w-full font-mono"
                  >{{ old('body3', $event->body3) }}</textarea>
                </div>


                {{-- 開催期間 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">開始日時</label>
                        <input type="datetime-local" name="start_at"
                               value="{{ old('start_at', $event->start_at ? $event->start_at->format('Y-m-d\TH:i') : '') }}"
                               class="input input-bordered w-full">
                    </div>
                    <div>
                        <label class="block font-semibold mb-1">終了日時</label>
                        <input type="datetime-local" name="end_at"
                               value="{{ old('end_at', $event->end_at ? $event->end_at->format('Y-m-d\TH:i') : '') }}"
                               class="input input-bordered w-full">
                    </div>
                </div>

                {{-- 会場／URL --}}
                <div>
                    <label class="block font-semibold mb-1">会場名またはオンラインURL</label>
                    <input type="text" name="location" value="{{ old('location', $event->location) }}"
                           class="input input-bordered w-full">
                </div>

                <div>
                    <label class="block font-semibold mb-1">参加URL</label>
                    <input type="text" name="join_url" value="{{ old('join_url', $event->join_url) }}"
                           class="input input-bordered w-full">
                </div>

                {{-- 定員・受付 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">定員</label>
                        <input type="number" name="capacity" value="{{ old('capacity', $event->capacity ?? 0) }}"
                               class="input input-bordered w-full">
                    </div>
                    <div class="flex items-center gap-3 mt-6">
                        <input type="hidden" name="recept" value="0">
                        <input type="checkbox" name="recept" value="1" class="toggle toggle-primary"
                               @checked(old('recept', $event->recept))>
                        <span class="font-medium">参加受付を有効化</span>
                    </div>
                </div>

                {{-- ステータス・公開範囲 --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div>
                        <label class="block font-semibold mb-1">ステータス</label>
                        <select name="status" class="select select-bordered w-full">
                            <option value="draft" @selected(old('status', $event->status) === 'draft')>下書き</option>
                            <option value="published" @selected(old('status', $event->status) === 'published')>公開中</option>
                            <option value="ongoing" @selected(old('status', $event->status) === 'ongoing')>開催中</option>
                            <option value="finished" @selected(old('status', $event->status) === 'finished')>終了</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">公開範囲</label>
                        <select name="visibility" class="select select-bordered w-full">
                            <option value="public" @selected(old('visibility', $event->visibility) === 'public')>一般公開</option>
                            <option value="membership" @selected(old('visibility', $event->visibility) === 'membership')>サークル向け</option>
                            <option value="hidden" @selected(old('visibility', $event->visibility) === 'hidden')>非公開</option>
                        </select>
                    </div>
                </div>

                {{-- カバー・ギャラリー画像（Livewireコンポーネント） --}}
                <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-8">
                    <livewire:admin.event-images :event="$event" />
                </div>

                {{-- ボタン --}}
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('admin.events.index') }}" class="link text-gray-500">← 一覧へ戻る</a>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>

        {{-- TinyMCE --}}
        <script src="https://cdn.jsdelivr.net/npm/tinymce@6.8.3/tinymce.min.js"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
          tinymce.init({
            selector: '#body2-editor',
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
