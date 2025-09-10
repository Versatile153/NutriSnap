<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ModifyCorrectedResultsInCorrectionRequestsTable extends Migration
{
    public function up()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->text('corrected_results')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('correction_requests', function (Blueprint $table) {
            $table->json('corrected_results')->nullable()->change();
        });
    }
}
