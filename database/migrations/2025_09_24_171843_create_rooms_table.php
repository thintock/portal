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
        Schema::create('rooms', function (Blueprint $table) {
            $table->id();
            $table->string('name', 80);
            $table->string('slug', 80)->unique();
            $table->string('description', 200)->nullable();
            $table->string('visibility', 20)->default('public'); // public/members/private
            $table->string('post_policy', 20)->default('members'); // admins_only/moderators/members
            $table->foreignId('owner_id')->constrained('users');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('posts_count')->default(0);
            $table->integer('members_count')->default(0);
            $table->timestamp('last_posted_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rooms');
    }
};
