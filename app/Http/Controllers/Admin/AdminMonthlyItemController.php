<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyItem;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminMonthlyItemController extends Controller
{
    /**
     * 一覧
     * GET /admin/monthly-items
     */
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));
        $status = $request->query('status'); // draft / published / null

        $monthlyItems = MonthlyItem::query()
            ->withCount('feedbackPosts')
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . addcslashes($q, '\\%_') . '%';
                $query->where(function ($sub) use ($like) {
                    $sub->where('month', 'like', $like)
                        ->orWhere('title', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->when(in_array($status, ['draft', 'published'], true), fn ($query) => $query->where('status', $status))
            ->orderByDesc('month')
            ->orderByDesc('id')
            ->paginate(20)
            ->withQueryString();

        // ✅ view側が $monthlyItems を参照しているので、必ずこのキーで渡す
        return view('admin.monthly-items.index', [
            'monthlyItems' => $monthlyItems,
            'q' => $q,
            'status' => $status,
        ]);
    }

    /**
     * 作成フォーム
     * GET /admin/monthly-items/create
     */
    public function create()
    {
        return view('admin.monthly-items.create');
    }

    /**
     * 保存
     * POST /admin/monthly-items
     */
    public function store(Request $request)
    {
        $data = $this->validatedData($request);

        $item = MonthlyItem::create($data);

        return redirect()
            ->route('admin.monthly-items.edit', $item)
            ->with('success', '月次アイテムを作成しました。');
    }

    /**
     * 詳細
     * GET /admin/monthly-items/{monthly_item}
     * ※ 管理画面では show 不要なら edit へ寄せます
     */
    public function show(MonthlyItem $monthly_item)
    {
        return redirect()->route('admin.monthly-items.edit', $monthly_item);
    }

    /**
     * 編集フォーム
     * GET /admin/monthly-items/{monthly_item}/edit
     */
    public function edit(MonthlyItem $monthly_item)
    {
        // 画像を後工程で付けるなら、ここで eager load すると便利
        // $monthly_item->load(['mediaFiles' => fn($q) => $q->orderBy('media_relations.sort_order')]);

        return view('admin.monthly-items.edit', [
            'monthlyItem' => $monthly_item, // view側は monthlyItem で統一
        ]);
    }

    /**
     * 更新
     * PATCH /admin/monthly-items/{monthly_item}
     */
    public function update(Request $request, MonthlyItem $monthly_item)
    {
        $data = $this->validatedData($request);

        $monthly_item->fill($data)->save();

        return redirect()
            ->route('admin.monthly-items.edit', $monthly_item)
            ->with('success', '月次アイテムを更新しました。');
    }

    /**
     * 削除
     * DELETE /admin/monthly-items/{monthly_item}
     */
    public function destroy(MonthlyItem $monthly_item)
{
    // 関連メッセージ（feedbackPosts）が存在する場合は削除不可
    if ($monthly_item->feedbackPosts()->exists()) {
        return redirect()
            ->route('admin.monthly-items.index')
            ->with('error', 'メッセージ（投稿）が存在するため、この月次テーマは削除できません。先にメッセージを削除してください。');
    }

    $monthly_item->delete();

    return redirect()
        ->route('admin.monthly-items.index')
        ->with('success', '月次テーマを削除しました。');
}

    /**
     * バリデーション＆整形（store/update共通）
     */
    private function validatedData(Request $request): array
    {
        // 1) バリデーション
        $validated = $request->validate([
            'month' => ['required', 'string', 'size:7'], // YYYY-MM
            'title' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'published_at' => ['required', 'date'],
            'protein' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'ash' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'absorption' => ['nullable', 'numeric', 'min:0', 'max:200'],

            'feedback_start_at' => ['required', 'date'],
            'feedback_end_at'   => ['required', 'date', 'after:feedback_start_at'],
            
            'status' => ['required', Rule::in(['draft', 'published'])],
        ], [], [
            'month' => '月',
            'title' => 'タイトル',
            'description' => '説明',
            'published_at' => '情報公開日',
            'protein' => 'タンパク値',
            'ash' => '灰分値',
            'absorption' => '吸水率',
            'feedback_start_at' => '受付開始日時',
            'feedback_end_at' => '受付終了日時',
            'status' => 'ステータス',
        ]);

        // 2) 余分な空白整理など（必要なら）
        $validated['month'] = trim($validated['month']);
        if (isset($validated['title'])) {
            $validated['title'] = $validated['title'] !== null ? trim($validated['title']) : null;
        }
        
        // 数値カラム（空文字をnullへ）
        foreach (['protein', 'ash', 'absorption'] as $k) {
            if (array_key_exists($k, $validated) && $validated[$k] === '') {
                $validated[$k] = null;
            }
        }

        return $validated;
    }
}
