<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AdminUserController extends Controller
{
    /**
     * ユーザー一覧
     */
    public function index()
    {
        // avatar リレーションも同時に読み込む
        $users = User::with(['mediaFiles' => function ($query) {
            $query->where('type', 'avatar')
                  ->orderBy('media_relations.sort_order', 'asc');
        }])->paginate(20);

        // 各ユーザーのアバターURLをセット
        $users->getCollection()->transform(function ($user) {
            $avatar = $user->mediaFiles->first();

            if ($avatar && $avatar->path) {
                $user->avatar_url = Storage::url($avatar->path);
            } else {
                $user->avatar_url = null;
            }

            return $user;
        });

        return view('admin.users.index', compact('users'));
    }

    /**
     * ユーザー編集フォーム
     */
    public function edit(User $user)
    {
        // ✅ アバター画像を取得（media_files.type = 'avatar'）
        $avatar = $user->mediaFiles()
            ->where('media_files.type', 'avatar')
            ->orderBy('media_relations.sort_order', 'asc')
            ->first();
    
        // ✅ Storage から URL を生成（存在しない場合は null）
        if ($avatar && $avatar->path) {
            try {
                // ディスク指定があれば使い、なければ config/filesystems.php の default
                $disk = $avatar->disk ?? config('filesystems.default', 'public');
                $avatar_url = Storage::disk($disk)->url($avatar->path);
            } catch (\Exception $e) {
                // 万一 Storage::url 失敗時も安全に null
                $avatar_url = null;
            }
        } else {
            $avatar_url = null;
        }
    
        return view('admin.users.edit', compact('user', 'avatar_url'));
    }


    public function update(ProfileUpdateRequest $request, User $user)
    {
        // バリデーション済みデータを取得
        $data = $request->validated();
    
        $user->fill($data);
        $user->save();
    
        return redirect()->route('admin.users.index')->with('success', 'ユーザーを更新しました');
    }



    /**
     * ユーザー削除処理
     */
    public function destroy(User $user)
    {
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'ユーザーを削除しました。');
    }
}
