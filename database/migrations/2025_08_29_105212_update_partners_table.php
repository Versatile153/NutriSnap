<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade')->after('id');
            $table->string('website')->nullable()->after('user_id');
            $table->string('api_key')->unique()->after('website');
            $table->boolean('is_active')->default(true)->after('api_key');
        });
    }

    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn(['user_id', 'website', 'api_key', 'is_active']);
        });
    }
};