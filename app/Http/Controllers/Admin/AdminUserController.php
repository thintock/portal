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
    public function index(Request $request)
    {
        // =========================================================
        // 0) 受け取る検索パラメータ（クエリ文字列）
        // =========================================================
        $q            = trim((string) $request->query('q', ''));          // 文字検索
        $emailVerified = $request->query('email_verified');              // '1' | '0' | null
        $subStatus     = $request->query('sub');                         // 'active' | 'inactive' | null
        $role          = $request->query('role');                        // 'admin' | 'user' | 'guest' | null
    
        // LIKE 検索のエスケープ
        $escapeLike = function (string $value): string {
            return str_replace(['\\', '%', '_'], ['\\\\', '\%', '\_'], $value);
        };
    
        // 文字検索対象カラム
        $searchableColumns = [
            'name',
            'first_name',
            'last_name',
            'first_name_kana',
            'last_name_kana',
            'instagram_id',
            'company_name',
            'postal_code',
            'prefecture',
            'address1',
            'address2',
            'address3',
            'country',
            'phone',
            'remarks',
            'email',
        ];
    
        // =========================================================
        // 1) 誕生日リスト（今月 + 来月5日まで / 最大20）
        // =========================================================
        $now = Carbon::now('Asia/Tokyo')->startOfDay();
        $end = $now->copy()->addMonthNoOverflow()->day(5)->endOfDay();
    
        // mmdd を作る（例: 1/8 => 108, 12/31 => 1231）
        $startKey = $now->month * 100 + $now->day;
        $endKey   = $end->month * 100 + $end->day;
    
        $mmddExpr = DB::raw('(birthday_month * 100 + birthday_day)');
    
        $birthdayUsersQuery = User::query()
            ->whereNotNull('birthday_month')
            ->whereNotNull('birthday_day');
    
        // （任意）誕生日リストも、role / email認証 / サブスク状態で絞り込みたい場合はここに追従させる
        // 今回は「一覧の検索」と独立でOKという前提で、誕生日リストには反映しない（必要なら追従版も出します）
    
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
    
        // =========================================================
        // 2) 一覧（検索対応）
        // =========================================================
        $usersQuery = User::query()
            ->with([
                'mediaFiles' => function ($query) {
                    $query->where('type', 'avatar')
                          ->orderBy('media_relations.sort_order', 'asc');
                },
                'subscriptions' => function ($q) {
                    // ※ あなたのDBに name が無いので入れない（既存仕様のまま）
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
            ->orderByDesc('created_at');
    
        // 2-1) 文字検索（複数カラム OR）
        if ($q !== '') {
            $kw = '%' . $escapeLike($q) . '%';
    
            $usersQuery->where(function ($query) use ($searchableColumns, $kw) {
                foreach ($searchableColumns as $col) {
                    $query->orWhere($col, 'like', $kw);
                }
            });
        }
    
        // 2-2) email認証（ボタン検索）
        // email_verified=1 -> 認証済み
        // email_verified=0 -> 未認証
        if ($emailVerified === '1') {
            $usersQuery->whereNotNull('email_verified_at');
        } elseif ($emailVerified === '0') {
            $usersQuery->whereNull('email_verified_at');
        }
    
        // 2-3) role（ボタン検索）
        if (!empty($role)) {
            $usersQuery->where('role', $role);
        }
    
        // 2-4) サブスク状態（ボタン検索）
        // Cashier / subscriptions テーブル前提：
        // - type='default' を対象
        // - stripe_status が active/trialing を有効扱い
        // - ends_at が未来 or null を有効扱い
        if ($subStatus === 'active') {
            $usersQuery->whereHas('subscriptions', function ($q) {
                $q->where('type', 'default')
                  ->whereIn('stripe_status', ['active', 'trialing'])
                  ->where(function ($q2) {
                      $q2->whereNull('ends_at')
                         ->orWhere('ends_at', '>', now());
                  });
            });
        } elseif ($subStatus === 'inactive') {
            $usersQuery->whereDoesntHave('subscriptions', function ($q) {
                $q->where('type', 'default')
                  ->whereIn('stripe_status', ['active', 'trialing'])
                  ->where(function ($q2) {
                      $q2->whereNull('ends_at')
                         ->orWhere('ends_at', '>', now());
                  });
            });
        }
    
        // 2-5) ページネーション（検索条件を維持）
        $users = $usersQuery
            ->paginate(20)
            ->withQueryString();
    
        // 2-6) 表示用整形（既存ロジック）
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
    
        return view('admin.users.index', compact(
            'users',
            'birthdayUsers',
            'now',
            'end',
            'q',
            'emailVerified',
            'subStatus',
            'role',
        ));
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
