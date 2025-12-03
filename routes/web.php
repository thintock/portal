<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomMemberController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventParticipantController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\StripeWebhookController;
use App\Livewire\Room;
use App\Livewire\PostShow;
use App\Livewire\Events\Show;
use App\Livewire\Members\MemberIndex;
use App\Livewire\Dashboard\Index as DashboardIndex;

// 誰でもOK
Route::get('/', function () {
    if (Auth::check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('register');
});
Route::get('/pages/{slug}', [PageController::class, 'show'])->name('pages.show');

// 無料会員（認証済み）
Route::middleware(['auth','verified'])->group(function () {
    Route::get('/dashboard', DashboardIndex::class)->name('dashboard');

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
    Route::get('/members', MemberIndex::class)->name('members.member_index');
});

// 有料会員専用
Route::middleware(['auth','verified','subscribed'])->group(function () {
    // RoomMembers（参加/退出/役割変更）
    Route::post('rooms/{room}/join', [RoomMemberController::class, 'join'])->name('rooms.join');
    Route::delete('rooms/{room}/leave', [RoomMemberController::class, 'leave'])->name('rooms.leave');
    Route::patch('room-members/{member}/role', [RoomMemberController::class, 'updateRole'])->name('room_members.update_role');
    Route::get('/rooms/{room}', Room::class)->name('rooms.show');
    Route::get('/posts/{post}', PostShow::class)->name('posts.show');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', Show::class)->name('events.show');
    Route::post('/events/{event}/join', [EventParticipantController::class, 'store'])->name('events.join');
    Route::delete('/events/{event}/cancel', [EventParticipantController::class, 'destroy'])->name('events.cancel');
});

// 管理者専用
Route::middleware(['auth','verified','is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 今後の管理ページ用のルート（未実装でもOK）
    Route::get('/posts', fn() => '投稿管理ページ')->name('admin.posts');
    Route::get('/events', fn() => 'イベント管理ページ')->name('admin.events');
    Route::resource('users', AdminUserController::class)->names('users')->except('show');
    Route::resource('rooms', RoomController::class)->except('show');
    Route::resource('pages', AdminPageController::class);
    Route::resource('events', AdminEventController::class);
});


// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
// Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

require __DIR__.'/auth.php';