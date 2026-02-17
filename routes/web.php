<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\BillingController;
use App\Http\Controllers\RoomMemberController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\EventParticipantController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRoomController;
use App\Http\Controllers\Admin\AdminPageController;
use App\Http\Controllers\Admin\AdminEventController;
use App\Http\Controllers\Admin\AdminAnnouncementController;
use App\Http\Controllers\Admin\AdminMonthlyItemController;
use App\Http\Controllers\Admin\CsvUserConvertController;
use App\Http\Controllers\Admin\AdminMonthlyItemReportController;
use App\Http\Controllers\StripeWebhookController;
use App\Livewire\Room;
use App\Livewire\PostShow;
use App\Livewire\Posts\Index as PostsIndex;
use App\Livewire\Events\Show;
use App\Livewire\Announcements\Show as AnnouncementShow;
use App\Livewire\Members\MemberIndex;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\MonthlyItems\Index as MonthlyItemsIndex;
use App\Livewire\MonthlyItems\Show as MonthlyItemsShow;
use App\Livewire\MonthlyItems\Feedback\Create as MonthlyFeedbackCreate;
use App\Livewire\MonthlyItems\Feedback\Edit as MonthlyFeedbackEdit;
use App\Livewire\SavedPosts\Index as SavedPostsIndex;

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
    // お知らせ（ユーザー側）
    Route::get('/announcements/{slug}', AnnouncementShow::class)->name('announcements.show');
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
    Route::post('rooms/{room}/join', [RoomMemberController::class, 'join'])->name('rooms.join');
    Route::delete('rooms/{room}/leave', [RoomMemberController::class, 'leave'])->name('rooms.leave');
    Route::patch('room-members/{member}/role', [RoomMemberController::class, 'updateRole'])->name('room_members.update_role');
    Route::get('/rooms/{room}', Room::class)->name('rooms.show');
    Route::get('/posts/{post}', PostShow::class)->name('posts.show');
    Route::get('/posts', PostsIndex::class)->name('posts.index');
    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::get('/events/{slug}', Show::class)->name('events.show');
    Route::post('/events/{event}/join', [EventParticipantController::class, 'store'])->name('events.join');
    Route::delete('/events/{event}/cancel', [EventParticipantController::class, 'destroy'])->name('events.cancel');
    Route::get('/monthly-items', MonthlyItemsIndex::class)->name('monthly-items.index');
    Route::get('/monthly-items/{monthlyItem}', MonthlyItemsShow::class)->name('monthly-items.show');
    Route::get('/monthly-items/{monthlyItem}/feedback/create', MonthlyFeedbackCreate::class)->name('monthly-items.feedback.create');
    Route::get('/monthly-items/{monthlyItem}/feedback/edit', MonthlyFeedbackEdit::class)->name('monthly-items.feedback.edit');
    Route::get('/saved-posts', SavedPostsIndex::class)->name('saved-posts.index');
});

// 管理者専用
Route::middleware(['auth','verified','is_admin'])->prefix('admin')->name('admin.')->group(function () {
    
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // 今後の管理ページ用のルート（未実装でもOK）
    Route::get('/posts', fn() => '投稿管理ページ')->name('admin.posts');
    Route::get('/events', fn() => 'イベント管理ページ')->name('admin.events');
    Route::post('/rooms/sort', [AdminRoomController::class, 'updateSortOrder'])->name('rooms.sort');
    Route::resource('users', AdminUserController::class)->names('users')->except('show');
    Route::resource('rooms', AdminRoomController::class)->except('show');
    Route::resource('pages', AdminPageController::class);
    Route::resource('events', AdminEventController::class);
    Route::resource('announcements', AdminAnnouncementController::class);
    Route::resource('monthly-items', AdminMonthlyItemController::class);
    // CSV
    Route::get('/csv/users/download', [CsvUserConvertController::class, 'download'])->name('csv.users.download');
    // レポート
    Route::get('/monthly-items/{monthly_item}/report', [AdminMonthlyItemReportController::class, 'show'])->name('monthly-items.report');
});


// Stripe Webhook
Route::post('/stripe/webhook', [StripeWebhookController::class, 'handleWebhook']);
// Route::post('/stripe/webhook', [WebhookController::class, 'handleWebhook']);

require __DIR__.'/auth.php';