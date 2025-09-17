<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_files', function (Blueprint $table) {
            $table->id(); 
            $table->string('owner_type')->nullable()->comment('関連モデル例: users, posts');
            $table->unsignedBigInteger('owner_id')->nullable()->comment('関連モデルのID');
            $table->string('type', 50)->nullable()->comment('用途: profile, post, thumbnail 等');
            $table->string('path', 255)->unique()->comment('画像のパス');
            $table->string('mime', 100)->nullable()->comment('MIMEタイプ: image/jpeg, video/mp4 など');
            $table->unsignedBigInteger('size')->nullable()->comment('ファイルサイズ（バイト）');
            $table->string('alt', 255)->nullable()->comment('代替テキスト');
            $table->integer('width')->nullable()->comment('画像/動画の幅');
            $table->integer('height')->nullable()->comment('画像/動画の高さ');
            $table->integer('duration')->nullable()->comment('動画の場合の長さ（秒）');
        
            // サムネイル参照（自己参照外部キー）
            $table->unsignedBigInteger('thumbnail_media_id')->nullable()->comment('動画のサムネイル参照');
            $table->foreign('thumbnail_media_id')->references('id')->on('media_files')->onDelete('set null');
        
            $table->timestamps();
        
            $table->index(['owner_type', 'owner_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_files');
    }
};
