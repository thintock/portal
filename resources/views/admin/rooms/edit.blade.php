<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">ルームを編集</h1>

        <form method="POST" action="{{ route('admin.rooms.update', $room) }}" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            {{-- ルーム名 --}}
            <div>
                <label class="block font-semibold mb-1">ルーム名 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name', $room->name) }}" 
                       class="input input-bordered w-full">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- 説明 --}}
            <div>
                <label class="block font-semibold mb-1">説明</label>
                <textarea name="description" rows="3" 
                          class="textarea textarea-bordered w-full">{{ old('description', $room->description) }}</textarea>
            </div>

            {{-- アイコン画像 --}}
            <div>
                <label class="block font-semibold mb-1">アイコン画像</label>
                @php
                    $iconMedia = $room->mediaFiles()
                        ->where('media_files.type', 'room_icon')
                        ->first();
                @endphp

                @if($iconMedia)
                    <div class="mb-2">
                        <img src="{{ Storage::url($iconMedia->path) }}" alt="icon" class="w-16 h-16 rounded-full object-cover border">
                    </div>
                @endif

                <input type="file" name="icon" class="file-input file-input-bordered w-full">
                <p class="text-xs text-gray-500 mt-1">新しい画像をアップロードすると置き換わります</p>
            </div>

            {{-- カバー画像 --}}
            <div>
                <label class="block font-semibold mb-1">カバー画像</label>
                @php
                    $coverMedia = $room->mediaFiles()
                        ->where('media_files.type', 'room_cover')
                        ->first();
                @endphp

                @if($coverMedia)
                    <div class="mb-2">
                        <img src="{{ Storage::url($coverMedia->path) }}" alt="cover" class="w-full max-h-40 object-cover border rounded">
                    </div>
                @endif

                <input type="file" name="cover_image" class="file-input file-input-bordered w-full">
                <p class="text-xs text-gray-500 mt-1">新しい画像をアップロードすると置き換わります</p>
            </div>

            {{-- 公開範囲 --}}
            <div>
                <label class="block font-semibold mb-1">公開範囲</label>
                <select name="visibility" class="select select-bordered w-full">
                    <option value="public" {{ old('visibility', $room->visibility)==='public'?'selected':'' }}>一般公開</option>
                    <option value="members" {{ old('visibility', $room->visibility)==='members'?'selected':'' }}>メンバーのみ</option>
                    <option value="private" {{ old('visibility', $room->visibility)==='private'?'selected':'' }}>招待制</option>
                </select>
            </div>

            {{-- 投稿権限 --}}
            <div>
                <label class="block font-semibold mb-1">投稿権限</label>
                <select name="post_policy" class="select select-bordered w-full">
                    <option value="admins_only" {{ old('post_policy', $room->post_policy)==='admins_only'?'selected':'' }}>管理者のみ</option>
                    <option value="members" {{ old('post_policy', $room->post_policy)==='members'?'selected':'' }}>誰でもOK</option>
                </select>
            </div>
            
            {{-- 公開 / 非公開スイッチ --}}
            <div class="form-control">
                <label class="label cursor-pointer">
                    <span class="label-text font-semibold">公開する</span>
                    <input type="hidden" name="is_active" value="0">
                    <input type="checkbox" name="is_active" value="1" 
                           class="toggle toggle-primary"
                           {{ old('is_active', $room->is_active) ? 'checked' : '' }}>
                </label>
            </div>
            
            {{-- 作成ボタン --}}
            <div>
                <button class="btn btn-primary">更新</button>
            </div>
        </form>
    </div>
</x-app-layout>
