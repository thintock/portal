<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests\ProfileUpdateRequest;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminUserController extends Controller
{
    /**
     * ユーザー一覧
     */
    public function index()
    {
        // ===== ① 誕生日リスト（今月 + 来月5日まで / 最大20） =====
        $now = Carbon::now('Asia/Tokyo')->startOfDay();
        $end = $now->copy()->addMonthNoOverflow()->day(5)->endOfDay();
    
        // mmdd を作る（例: 1/8 => 108, 12/31 => 1231）
        $startKey = $now->month * 100 + $now->day;
        $endKey   = $end->month * 100 + $end->day;
    
        $mmddExpr = DB::raw('(birthday_month * 100 + birthday_day)');
    
        $birthdayUsersQuery = User::query()
            ->whereNotNull('birthday_month')
            ->whereNotNull('birthday_day');
    
        // 期間条件（年跨ぎも対応）
        if ($startKey <= $endKey) {
            $birthdayUsersQuery->whereBetween($mmddExpr, [$startKey, $endKey])
                ->orderBy($mmddExpr, 'asc');
        } else {
            // 例: 12/20(1220) -> 1/5(105) のように跨ぐ場合
            $birthdayUsersQuery->where(function ($q) use ($mmddExpr, $startKey, $endKey) {
                    $q->where($mmddExpr, '>=', $startKey)
                      ->orWhere($mmddExpr, '<=', $endKey);
                })
                ->orderByRaw(
                    "CASE WHEN (birthday_month * 100 + birthday_day) >= ? THEN 0 ELSE 1 END ASC,
                     (birthday_month * 100 + birthday_day) ASC",
                    [$startKey]
                );
        }
    
        // avatar も一緒に（表示用）
        $birthdayUsers = $birthdayUsersQuery
            ->with(['mediaFiles' => function ($query) {
                $query->where('type', 'avatar')
                      ->orderBy('media_relations.sort_order', 'asc');
            }])
            ->limit(20)
            ->get();
    
        // avatar_url 付与
        $birthdayUsers->transform(function ($user) {
            $avatar = $user->mediaFiles->first();
            $user->avatar_url = ($avatar && $avatar->path) ? Storage::url($avatar->path) : null;
            return $user;
        });
    
    
        // ===== ② 一覧（既存処理） =====
        $users = User::with([
            'mediaFiles' => function ($query) {
                $query->where('type', 'avatar')
                      ->orderBy('media_relations.sort_order', 'asc');
            },
            'subscriptions' => function ($q) {
                // ※ あなたのDBに name が無いので入れない
                $q->select([
                    'id',
                    'user_id',
                    'type',
                    'stripe_status',
                    'created_at',
                    'ends_at',
                ])->orderByDesc('created_at');
            },
        ])
        ->orderByDesc('created_at')
        ->paginate(20);
    
        $users->getCollection()->transform(function ($user) {
            // avatar_url
            $avatar = $user->mediaFiles->first();
            $user->avatar_url = ($avatar && $avatar->path) ? Storage::url($avatar->path) : null;
    
            // サブスク情報（default 優先、環境差分吸収）
            $subs = $user->subscriptions ?? collect();
            $defaultSub = $subs->firstWhere('name', 'default')
                ?? $subs->firstWhere('type', 'default')
                ?? $subs->first();
    
            $user->subscription_started_at = $defaultSub?->created_at;
            $user->subscription_cancel_scheduled_at = $defaultSub?->ends_at;
    
            return $user;
        });
    
        return view('admin.users.index', compact('users', 'birthdayUsers', 'now', 'end'));
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
    
        return redirect()->route('admin.users.edit', $user->id)->with('success', 'ユーザーを更新しました');
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
