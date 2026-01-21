{{-- resources/views/admin/monthly-items/report.blade.php --}}
@php
  $cover = $monthlyItem->mediaFiles->first();

  // month が "2026-01" 形式想定
  try {
    $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $monthlyItem->month)->format('Y年n月');
  } catch (\Throwable $e) {
    $monthLabel = $monthlyItem->month;
  }
@endphp

<!doctype html>
<html lang="ja">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>月次テーマ レポート（{{ $monthLabel }}）</title>

  {{-- Tailwind/DaisyUI をレイアウト経由で読み込んでいた場合、ここでは使えません。
       その場合でも崩れないように最低限CSSだけで整えます。 --}}
  <style>
    /* Base */
    html, body { margin: 0; padding: 0; }
    body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Hiragino Sans", "Noto Sans JP", "Yu Gothic", "Helvetica Neue", Arial, sans-serif; color: #111; background: #fff; }
    a { color: inherit; text-decoration: none; }
    .container { max-width: 980px; margin: 0 auto; padding: 16px; }
    .muted { color: #666; font-size: 12px; }
    .h1 { font-size: 20px; font-weight: 700; margin: 0; }
    .h2 { font-size: 16px; font-weight: 700; margin: 0; }
    .btnbar { display: flex; justify-content: space-between; gap: 8px; margin-bottom: 12px; }
    .btn { display: inline-block; border: 1px solid #111; padding: 8px 10px; border-radius: 8px; font-size: 12px; background: #fff; cursor: pointer; }
    .btn.primary { background: #111; color: #fff; }
    .card { border: 0px solid #e5e5e5; border-radius: 12px; padding: 14px; }
    .stack { display: grid; gap: 12px; }
    .divider { height: 1px; background: #eee; margin: 10px 0; }

    /* Header */
    .headerTop { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .titleBlock { min-width: 0; }
    .month { font-size: 12px; color: #666; margin-bottom: 2px; }
    .mainTitle { font-size: 18px; font-weight: 800; margin: 0; word-break: break-word; }
    .desc { font-size: 13px; line-height: 1.65; color: #222; white-space: pre-wrap; word-break: break-word; }
    .cover { width: 100%; max-height: 360px; object-fit: cover; border-radius: 10px; border: 1px solid #e5e5e5; }

    /* Post */
    .postHeader { display: flex; justify-content: space-between; gap: 12px; align-items: flex-start; }
    .who { display: flex; gap: 10px; align-items: center; min-width: 0; }
    .avatar { width: 40px; height: 40px; border-radius: 999px; border: 1px solid #e5e5e5; overflow: hidden; background: #fafafa; display: flex; align-items: center; justify-content: center; font-size: 12px; color: #666; flex: 0 0 auto; }
    .avatar img { width: 100%; height: 100%; object-fit: cover; }
    .whoName { font-weight: 700; word-break: break-word; }
    .postTitle { font-size: 14px; font-weight: 800; margin: 0; word-break: break-word; }
    .postBody { font-size: 13px; line-height: 1.7; color: #222; white-space: pre-wrap; word-break: break-word; }
    .gridImgs { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 6px; }
    .gridImgs img { width: 100%; height: 140px; object-fit: cover; border-radius: 10px; border: 1px solid #e5e5e5; }

    /* Print */
    @media print {
      @page { margin: 12mm; }
      .no-print { display: none !important; }
      body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
      .avoid-break { break-inside: avoid; page-break-inside: avoid; }
      .btn { display: none; }
      .container { padding: 0; }
      .gridImgs img { height: 120px; }
    }

    /* Mobile */
    @media (max-width: 640px) {
      .container { padding: 12px; }
      .gridImgs { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .gridImgs img { height: 120px; }
    }
  </style>
</head>

<body>
  <div class="container">

    {{-- 画面操作（印刷時は消える） --}}
    <div class="btnbar no-print">
      <div style="display:flex; gap:8px; flex-wrap:wrap;">
        <a class="btn" href="{{ route('admin.monthly-items.edit', $monthlyItem) }}">← 編集へ戻る</a>
      </div>
      <button type="button" class="btn primary" onclick="window.print()">印刷する</button>
    </div>

    {{-- レポートヘッダー --}}
    <div class="card stack">
      <div class="headerTop">
        <div class="titleBlock">
          <div class="month">{{ $monthLabel }}</div>
          <h1 class="mainTitle">{{ $monthlyItem->title }}</h1>
        </div>

        <div class="muted" style="white-space:nowrap; text-align:right;">
          公開日：{{ optional($monthlyItem->published_at)->format('Y/m/d H:i') }}<br>
          メッセージ件数：{{ $posts->count() }} 件
        </div>
      </div>

      @if(!empty($monthlyItem->description))
        <div class="desc">{{ strip_tags($monthlyItem->description) }}</div>
      @endif

      @if($cover)
        <img class="cover" src="{{ $cover->url }}" alt="monthly cover">
      @endif

      <div class="muted">
        受付期間：
        {{ optional($monthlyItem->feedback_start_at)->format('Y/m/d H:i') }}
        〜
        {{ optional($monthlyItem->feedback_end_at)->format('Y/m/d H:i') }}
      </div>
    </div>

    <div class="divider"></div>

    {{-- 投稿一覧（古い順） --}}
    <div class="stack">
      @forelse($posts as $p)
        @php
          $images = $p->mediaFiles ?? collect();

          // Avatar: プロジェクト仕様に合わせて調整
          $avatarUrl = null;
          if (isset($p->user) && property_exists($p->user, 'profile_photo_url')) {
            $avatarUrl = $p->user->profile_photo_url;
          }
        @endphp

        <div class="card stack">
          <div class="postHeader">
            <div class="who">
              <div class="avatar">
                @if($avatarUrl)
                  <img src="{{ $avatarUrl }}" alt="avatar">
                @else
                  {{ mb_substr($p->user?->name ?? 'U', 0, 1) }}
                @endif
              </div>

              <div style="min-width:0;">
                <div class="whoName">{{ $p->user?->name ?? '（不明）' }}</div>
                <div class="muted">{{ optional($p->created_at)->format('Y/m/d H:i') }}</div>
              </div>
            </div>

            <div class="muted" style="font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace;">
              #{{ $p->id }}
            </div>
          </div>

          @if(!empty($p->title))
            <h2 class="postTitle">{{ $p->title }}</h2>
          @endif

          @if(!empty($p->body))
            <div class="postBody">{{ strip_tags($p->body) }}</div>
          @endif

          @if($images->count() > 0)
            <div class="gridImgs">
              @foreach($images as $img)
                <img src="{{ $img->url }}" alt="image">
              @endforeach
            </div>
          @endif
        </div>
        
        <div class="divider"></div>
      @empty
        <div class="muted">メッセージはまだ投稿されていません。</div>
      @endforelse
    </div>

  </div>
</body>
</html>
