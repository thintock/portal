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
        Schema::create('member_number_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade'); // ユーザー削除時に履歴も削除
            $table->unsignedBigInteger('number'); // 採番された番号
            $table->timestamp('assigned_at')->useCurrent(); // 採番された日時
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('member_number_histories');
    }
};
