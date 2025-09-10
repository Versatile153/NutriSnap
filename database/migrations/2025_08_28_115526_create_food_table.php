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
    Schema::create('foods', function (Blueprint $table) {
        $table->id();
        $table->string('name')->unique();
        $table->integer('calories_per_100g'); // Base calories
        $table->json('nutrients'); // e.g., {"sodium": 500, "sugar": 20, "protein": 10}
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food');
    }
};
