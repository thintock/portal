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
        Schema::create('media_relations', function (Blueprint $table) {
            $table->id();

            // 紐づくメディアファイル
            $table->foreignId('media_file_id')
                ->constrained('media_files')
                ->cascadeOnDelete();

            // ポリモーフィック対象（Post, Comment, Room, etc...）
            $table->morphs('mediable'); 
            // 生成カラム例：
            // mediable_id (unsignedBigInteger)
            // mediable_type (string)

            $table->integer('sort_order')->default(0)->comment('並び順');
            $table->timestamps();

            $table->index(['mediable_type', 'mediable_id'], 'idx_mediable');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_relations');
    }
};
