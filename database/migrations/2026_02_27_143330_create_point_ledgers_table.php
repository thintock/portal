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
        Schema::create('point_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->integer('delta'); // +5 / -5
            $table->string('reason', 20); // earn / revoke / redeem

            $table->string('action_type', 50)->nullable();

            // ポリモーフィック紐づけ（Post/Comment/Orderなど）
            $table->string('subject_type')->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();

            $table->foreignId('room_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamp('expires_at')->nullable();

            $table->json('meta_json')->nullable();

            $table->timestamps();

            $table->index(['user_id', 'expires_at']);
            $table->index(['subject_type', 'subject_id']);
            $table->index('reason');

            // 二重付与・二重取消防止
            $table->unique([
                'reason',
                'subject_type',
                'subject_id',
                'action_type'
            ], 'unique_point_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_ledgers');
    }
};
