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
        Schema::create('monthly_items', function (Blueprint $table) {
            $table->id();
            $table->char('month', 7)->index();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('published_at')->nullable()->index();
            $table->dateTime('feedback_start_at')->index();
            $table->dateTime('feedback_end_at')->index();
            $table->decimal('protein', 4, 1)->nullable(); // 例: 10.5
            $table->decimal('ash', 4, 2)->nullable();     // 例: 1.00
            $table->decimal('absorption', 4, 1)->nullable(); // 例: 70.0
            $table->string('status', 20)->default('draft')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('monthly_items');
    }
};
