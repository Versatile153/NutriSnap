<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->string('leftover_photo_url')->nullable()->after('photo_url');
            $table->json('leftover_analysis')->nullable()->after('analysis');
        });
    }

    public function down()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn(['leftover_photo_url', 'leftover_analysis']);
        });
    }
};
