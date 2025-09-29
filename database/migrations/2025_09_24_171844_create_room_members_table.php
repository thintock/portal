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
        Schema::create('room_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role', 20)->default('member'); // owner/admin/moderator/member
            $table->timestamp('joined_at')->nullable();
            $table->boolean('muted')->default(false);
            $table->json('notifications')->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'user_id']); // 同じユーザーが重複参加できない
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_members');
    }

};
