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
        Schema::create('room_point_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained()->cascadeOnDelete();
            $table->string('action_type', 50);
            $table->integer('points_override')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('note', 300)->nullable();
            $table->timestamps();

            $table->unique(['room_id', 'action_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('room_point_rules');
    }
};
