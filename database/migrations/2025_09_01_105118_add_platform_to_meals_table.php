<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPlatformToMealsTable extends Migration
{
    public function up()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->string('platform')->nullable()->after('share_proof');
        });
    }

    public function down()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn('platform');
        });
    }
}