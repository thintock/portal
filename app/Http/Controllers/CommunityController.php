<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CommunityController extends Controller
{
    /**
     * コミュニティトップページ
     * 有料会員のみアクセス可能（ルートで subscribed ミドルウェアを適用）
     */
    public function index()
    {
        return view('community.index');
    }
}
