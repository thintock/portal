<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reactions', function (Blueprint $table) {
            $table->id();
            // リアクションの対象（ポリモーフィックリレーション）
            $table->morphs('reactionable'); // reactionable_id, reactionable_type
            // リアクションしたユーザー
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            // リアクションの種類（例：like, love, clapなど）
            $table->string('type', 50);
            $table->timestamps();

            // 同一ユーザーが同じ対象に同一タイプのリアクションを重複してできないようにする
            $table->unique(['reactionable_id', 'reactionable_type', 'user_id', 'type'], 'unique_reaction');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reactions');
    }
};
