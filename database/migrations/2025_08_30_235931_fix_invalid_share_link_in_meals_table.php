<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        // Convert string share_link values to JSON
        DB::table('meals')->whereNotNull('share_link')->get()->each(function ($meal) {
            $shareLink = $meal->share_link;
            if (!is_array(json_decode($shareLink, true)) && !empty($shareLink)) {
                // Wrap string URLs in a JSON array with 'legacy' key
                $newShareLink = json_encode(['legacy' => $shareLink]);
                DB::table('meals')->where('id', $meal->id)->update(['share_link' => $newShareLink]);
            }
        });
    }

    public function down()
    {
        // Revert JSON to string (take first value)
        DB::table('meals')->whereNotNull('share_link')->get()->each(function ($meal) {
            $shareLinks = json_decode($meal->share_link, true);
            if (is_array($shareLinks)) {
                $firstLink = reset($shareLinks) ?: null;
                DB::table('meals')->where('id', $meal->id)->update(['share_link' => $firstLink]);
            }
        });
    }
};