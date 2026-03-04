<?php

namespace App\Providers;

use App\Models\Post;
use App\Models\Comment;
use App\Observers\PostObserver;
use App\Observers\CommentObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        URL::forceScheme('https');
        Post::observe(PostObserver::class);
        Comment::observe(CommentObserver::class);
    }
}
