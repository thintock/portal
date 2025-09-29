<x-app-layout>
    <div class="max-w-3xl mx-auto py-8">
        <h1 class="text-2xl font-bold mb-6">新しいルームを作成</h1>

        <form method="POST" action="{{ route('admin.rooms.store') }}" enctype="multipart/form-data" class="space-y-6">
            @csrf

            {{-- ルーム名 --}}
            <div>
                <label class="block font-semibold mb-1">ルーム名 <span class="text-red-500">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" 
                       class="input input-bordered w-full">
                @error('name') <p class="text-red-500 text-sm">{{ $message }}</p> @enderror
            </div>

            {{-- 説明 --}}
            <div>
                <label class="block font-semibold mb-1">説明</label>
                <textarea name="description" rows="3" 
                          class="textarea textarea-bordered w-full">{{ old('description') }}</textarea>
            </div>

            {{-- アイコン画像 --}}
            <div>
                <label class="block font-semibold mb-1">アイコン画像</label>
                <input type="file" name="icon" class="file-input file-input-bordered w-full">
                <p class="text-xs text-gray-500 mt-1">推奨: 正方形 200x200px</p>
            </div>

            {{-- カバー画像 --}}
            <div>
                <label class="block font-semibold mb-1">カバー画像</label>
                <input type="file" name="cover_image" class="file-input file-input-bordered w-full">
                <p class="text-xs text-gray-500 mt-1">推奨: 横長 1200x400px</p>
            </div>

            {{-- 公開範囲 --}}
            <div>
                <label class="block font-semibold mb-1">公開範囲</label>
                <select name="visibility" class="select select-bordered w-full">
                    <option value="public" {{ old('visibility')==='public'?'selected':'' }}>公開</option>
                    <option value="members" {{ old('visibility')==='members'?'selected':'' }}>メンバーのみ</option>
                    <option value="private" {{ old('visibility')==='private'?'selected':'' }}>招待制</option>
                </select>
            </div>

            {{-- 投稿権限 --}}
            <div>
                <label class="block font-semibold mb-1">投稿権限</label>
                <select name="post_policy" class="select select-bordered w-full">
                    <option value="admins_only" {{ old('post_policy')==='admins_only'?'selected':'' }}>管理者のみ</option>
                    <option value="members" {{ old('post_policy')==='members'?'selected':'' }}>誰でもOK</option>
                </select>
            </div>



            {{-- 作成ボタン --}}
            <div>
                <button class="btn btn-primary">作成</button>
            </div>
        </form>
    </div>
</x-app-layout>
