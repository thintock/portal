<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // 通知の受信者
            $table->morphs('notifiable'); // notifiable_id, notifiable_type
            $table->string('type', 50); // 通知タイプ（comment, reply, likeなど）
            $table->text('message');
            $table->foreignId('sender_id')->nullable()->constrained('users')->nullOnDelete();
            // どのルームに属する通知か（投稿を特定するため）
            $table->foreignId('room_id')->nullable()->constrained()->onDelete('cascade');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            // 検索最適化
            $table->index(['user_id', 'read_at']);
            $table->index(['room_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
