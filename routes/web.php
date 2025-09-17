<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use Laravel\Cashier\Http\Controllers\WebhookController;

// 誰でもOK
Route::get('/', function () {
    return view('welcome');
});

// 無料会員（認証済み）
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');

    // プロフィール
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // 課金
    Route::get('/billing', [BillingController::class, 'show'])->name('billing.show');
    Route::post('/billing/subscribe', [BillingController::class, 'subscribe'])->name('billing.subscribe');
    Route::get('/billing/success', [BillingController::class, 'success'])->name('billing.success');
    Route::get('/billing/cancel',  [BillingController::class, 'cancel'])->name('billing.cancel');
    Route::get('/billing/portal',  [BillingController::class, 'portal'])->name('billing.portal');
});

// 有料会員専用
Route::middleware(['auth','verified','subscribed'])->group(function () {
    Route::get('/community', [CommunityController::class, 'index'])->name('community.index');
});

// 管理者専用
Route::middleware(['auth','verified','is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 今後の管理ページ用のルート（未実装でもOK）
    Route::resource('users', AdminUserController::class)->names('users');
    Route::get('/posts', fn() => '投稿管理ページ')->name('admin.posts');
    Route::get('/events', fn() => 'イベント管理ページ')->name('admin.events');
});


// Stripe Webhook
Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

require __DIR__.'/auth.php';