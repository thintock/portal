<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('post_type', 20)->default('post'); // post / event
            $table->text('body')->nullable();
            $table->string('visibility', 20)->default('public');
            $table->string('status', 20)->default('published'); // published / hidden / archived
            $table->string('external_url', 500)->nullable();
            $table->json('link_preview_json')->nullable();
            $table->integer('reaction_count')->default(0);
            $table->integer('comment_count')->default(0);
            $table->timestamp('last_activity_at')->nullable()->index()->comment('投稿またはコメントの最終アクティビティ日時');
            $table->timestamp('pinned_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->index('room_id', 'idx_posts_room');
            $table->index('user_id', 'idx_posts_user');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
