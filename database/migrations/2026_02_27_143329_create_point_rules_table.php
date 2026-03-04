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
        Schema::create('point_rules', function (Blueprint $table) {
            $table->id();
            $table->string('action_type', 50); // post.created 等
            $table->integer('base_points');
            $table->boolean('is_active')->default(true);
            $table->string('note', 300)->nullable();
            $table->timestamps();

            $table->unique('action_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('point_rules');
    }
};
