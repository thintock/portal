<x-admin-layout>
    @section('admin-header')
        <div>
            <h1 class="text-lg font-bold text-gray-800">イベント管理</h1>
            <p class="text-sm text-gray-500">イベントを編集できます。</p>
        </div>
    @endsection

    <div class="w-full">

        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-4">
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

                {{-- スラッグ（通常は編集不可 / 必要なときのみ編集） --}}
                <div x-data="{ editingSlug: false }">
                    <label class="block font-semibold mb-1">URL</label>

                    <input type="text"
                        name="slug"
                        id="slug"
                        value="{{ old('slug', $event->slug) }}"
                        class="input input-bordered w-full"
                        :readonly="!editingSlug"
                        :class="editingSlug ? 'bg-white' : 'bg-gray-100 cursor-not-allowed'"
                    >

                    <p class="text-sm text-gray-500 mt-1">
                        URL 例： https://portal.bakerista.jp/events/XXX
                        <span class="italic text-gray-600">{{ $event->slug }}</span>
                    </p>

                    {{-- 編集スイッチ --}}
                    <label class="mt-3 flex items-center gap-2 cursor-pointer select-none text-sm">
                        <input type="checkbox"
                            x-model="editingSlug"
                            class="checkbox checkbox-sm checkbox-primary">
                        <span class="text-gray-700">URL（スラッグ）を編集する</span>
                    </label>

                    <p class="text-xs text-gray-500 mt-1" x-show="editingSlug">
                        ※ スラッグを変更すると既存リンクが無効になる可能性があります。
                    </p>
                </div>

                {{-- イベント種別 --}}
                <div>
                    <label class="block font-semibold mb-1">イベント種別</label>
                    <select name="event_type" class="select select-bordered w-full">
                      <option value="">選択してください</option>
                      <option value="online" @selected(old('event_type', $event->event_type)==='online')>オンラインイベント</option>
                      <option value="sns" @selected(old('event_type', $event->event_type)==='sns')>SNSイベント</option>
                      <option value="real" @selected(old('event_type', $event->event_type)==='real')>リアルイベント</option>
                      <option value="distribution" @selected(old('event_type', $event->event_type)==='distribution')>配布型イベント</option>
                    </select>
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

                {{-- 会場名 --}}
                <div>
                  <label class="block font-semibold mb-1">会場名</label>
                  <select name="location" class="select select-bordered w-full">
                      <option value="">選択してください</option>
                      <option value="zoom" @selected(old('location', $event->location)==='zoom')>ZOOM会場</option>
                      <option value="insta" @selected(old('location', $event->location)==='insta')>Instagram会場</option>
                      <option value="real" @selected(old('location', $event->location)==='real')>リアル会場</option>
                  </select>
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
                            <option value="published" @selected(old('status', $event->status) === 'published')>公開</option>
                            <option value="cancelled" @selected(old('status', $event->status) === 'cancelled')>中止</option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">公開範囲</label>
                        <select name="visibility" class="select select-bordered w-full">
                            <option value="membership" @selected(old('visibility', $event->visibility) === 'membership')>サークル向け</option>
                            <option value="public" @selected(old('visibility', $event->visibility) === 'public')>一般公開</option>
                            <option value="hidden" @selected(old('visibility', $event->visibility) === 'hidden')>非公開</option>
                        </select>
                    </div>
                </div>

                {{-- ボタン --}}
                <div class="flex justify-between items-center pt-4">
                    <a href="{{ route('admin.events.index') }}" class="link text-gray-500">← 一覧へ戻る</a>
                    <button type="submit" class="btn btn-primary">更新</button>
                </div>
            </form>
        </div>

        {{-- カバー・ギャラリー画像（Livewireコンポーネント） --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-8">
            <livewire:admin.event-images :event="$event" />
        </div>
        
        {{-- 参加者リスト --}}
        <div class="card bg-white shadow p-4 sm:p-6 lg:p-8 mb-8">
            <div class="flex items-center justify-between gap-3 mb-4">
                <div>
                    <h2 class="text-lg font-bold text-gray-800">参加者</h2>
                    <p class="text-sm text-gray-500">参加予定（going）のユーザー一覧です。</p>
                </div>
        
                <div class="text-sm text-gray-600">
                    合計：<span class="font-semibold">{{ $participants->count() }}</span>名
                </div>
            </div>
        
            @if($participants->isEmpty())
                <div class="p-6 bg-base-200 rounded-lg text-center text-sm text-gray-500">
                    現在、参加者はまだいません。
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-zebra w-full text-sm">
                        <thead class="bg-base-200">
                            <tr>
                                <th class="w-16">ID</th>
                                <th>ユーザー</th>
                                <th class="w-56">参加登録日時</th>
                                <th class="w-24">ステータス</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($participants as $p)
                                @php
                                    $u = $p->user;
                                @endphp
        
                                <tr>
                                    <td>{{ $u?->id ?? '—' }}</td>
        
                                    <td class="font-semibold">
                                        @if($u)
                                            <a href="{{ route('admin.users.edit', $u->id) }}" class="link link-primary">
                                                {{ $u->name }}
                                            </a>
                                            <div class="text-xs text-gray-500 mt-1">
                                                {{ $u->email ?? '' }}
                                            </div>
                                        @else
                                            <span class="text-gray-500">（ユーザーが見つかりません）</span>
                                        @endif
                                    </td>
        
                                    <td>
                                        {{ $p->created_at?->format('Y/m/d H:i') ?? '—' }}
                                    </td>
        
                                    <td>
                                        <span class="badge badge-outline">{{ $p->status }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
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
