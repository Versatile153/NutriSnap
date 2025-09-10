<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->string('status', 50)->default('pending')->change();
        });
    }

    public function down()
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->boolean('status')->default(0)->change(); // adjust to original type
        });
    }
};
