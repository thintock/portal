<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // 名前関連
            $table->string('first_name', 50)->nullable()->after('name');
            $table->string('last_name', 50)->nullable()->after('first_name');
            $table->string('first_name_kana', 50)->nullable()->after('last_name');
            $table->string('last_name_kana', 50)->nullable()->after('first_name_kana');

            // プロフィール関連
            $table->string('instagram_id', 100)->nullable()->after('last_name_kana');
            $table->string('company_name', 50)->nullable()->after('instagram_id');

            // 住所関連
            $table->string('postal_code', 10)->nullable()->after('company_name');
            $table->string('prefecture', 50)->nullable()->after('postal_code');
            $table->string('address1', 200)->nullable()->after('prefecture');
            $table->string('address2', 300)->nullable()->after('address1');
            $table->string('address3', 400)->nullable()->after('address2');
            $table->string('country', 2)->nullable()->after('address3'); // ISO 2文字コード (JP, US, etc.)
            $table->string('phone', 20)->nullable()->after('country');

            // 権限・属性
            $table->string('role', 20)->nullable()->after('phone');
            $table->string('user_type', 10)->nullable()->after('role');
            $table->string('user_status', 20)->nullable()->after('user_type');
            $table->boolean('email_notification')->default(true)->after('user_status');
            $table->text('remarks')->nullable()->after('email_notification');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'last_name',
                'first_name_kana',
                'last_name_kana',
                'display_name',
                'instagram_id',
                'company_name',
                'postal_code',
                'prefecture',
                'address1',
                'address2',
                'address3',
                'country',
                'phone',
                'role',
                'user_type',
                'user_status',
                'email_notification',
                'remarks',
            ]);
        });
    }
};
