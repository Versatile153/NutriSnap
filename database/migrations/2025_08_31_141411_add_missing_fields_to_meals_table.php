<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            if (!Schema::hasColumn('meals', 'portion_size')) {
                $table->decimal('portion_size', 8, 2)->nullable()->after('calories');
            }
            if (!Schema::hasColumn('meals', 'health_condition')) {
                $table->string('health_condition')->nullable()->after('status');
            }
            if (!Schema::hasColumn('meals', 'correction_request')) {
                $table->text('correction_request')->nullable()->after('leftover_analysis');
            }
            if (!Schema::hasColumn('meals', 'corrected_calories')) {
                $table->decimal('corrected_calories', 8, 2)->nullable()->after('correction_request');
            }
            if (!Schema::hasColumn('meals', 'corrected_food')) {
                $table->string('corrected_food')->nullable()->after('corrected_calories');
            }
            if (!Schema::hasColumn('meals', 'share_proof')) {
                $table->json('share_proof')->nullable()->after('corrected_food');
            }
        });
    }

    public function down(): void
    {
        Schema::table('meals', function (Blueprint $table) {
            $table->dropColumn([
                'portion_size',
                'health_condition',
                'correction_request',
                'corrected_calories',
                'corrected_food',
                'share_proof',
            ]);
        });
    }
};
