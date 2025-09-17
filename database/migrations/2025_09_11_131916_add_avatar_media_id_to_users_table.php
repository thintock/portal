<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('avatar_media_id')
                  ->nullable()
                  ->after('instagram_id');

            $table->foreign('avatar_media_id')
                  ->references('id')->on('media_files')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['avatar_media_id']);
            $table->dropColumn('avatar_media_id');
        });
    }
};
