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
        Schema::create('comments', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('post_id')->constrained('posts')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->foreignId('root_id')->nullable()->constrained('comments')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->text('body');
            $table->string('status', 20)->default('published'); // published/hidden
            $table->integer('reaction_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->tinyInteger('depth')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // インデックス
            $table->index(['post_id', 'parent_id', 'root_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
