<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                  ->constrained()
                  ->onDelete('cascade'); // deletes coupons if user is deleted
            $table->string('code')->unique(); // unique coupon code
            $table->decimal('discount_percentage', 5, 2); // e.g. 15.00 means 15% off
            $table->timestamp('expires_at')->nullable(); // nullable expiration date
            $table->boolean('is_used')->default(false); // whether coupon is already used
            $table->timestamps(); // created_at & updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
