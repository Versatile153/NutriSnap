<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->float('height')->nullable();
            $table->float('weight')->nullable();
            $table->string('goal')->nullable();
            $table->integer('daily_calories')->nullable();
            $table->json('conditions')->nullable();
            $table->timestamps();
        });
    }
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
};
