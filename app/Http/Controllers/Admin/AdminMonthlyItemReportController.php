<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MonthlyItem;
use App\Models\FeedbackPost;

class AdminMonthlyItemReportController extends Controller
{
    /**
     * 印刷レポート
     * GET /admin/monthly-items/{monthly_item}/report
     */
    public function show(MonthlyItem $monthly_item)
    {
        // 月次テーマ（必要なら画像も）
        $monthly_item->load([
            'mediaFiles' => fn ($q) => $q
                ->where('media_files.type', 'monthly_item_cover')
                ->orderBy('media_relations.sort_order'),
        ]);

        // メッセージ（古い順） + ユーザー + 画像（feedback_image）
        $posts = FeedbackPost::query()
            ->where('monthly_item_id', $monthly_item->id)
            ->with([
                // user avatar はプロジェクトに合わせて調整してください（例: profile_photo_url / userImages 等）
                'user:id,name',
                'mediaFiles' => fn ($q) => $q
                    ->where('media_files.type', 'feedback_image')
                    ->orderBy('media_relations.sort_order'),
            ])
            ->orderBy('created_at', 'asc')
            ->get([
                'id',
                'monthly_item_id',
                'user_id',
                'title',
                'body',
                'created_at',
            ]);

        return view('admin.monthly-items.report', [
            'monthlyItem' => $monthly_item,
            'posts' => $posts,
        ]);
    }
}
