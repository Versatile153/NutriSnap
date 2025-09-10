<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
    {
        DB::table('meals')->whereNotNull('share_link')->get()->each(function ($meal) {
            $shareLink = $meal->share_link;
            // Check if share_link is a string (not JSON)
            if (!is_array(json_decode($shareLink, true)) && !empty($shareLink)) {
                // Replace localhost with production URL
                $shareLink = str_replace('http://localhost', 'https://bincone.apexjets.org', $shareLink);
                // Wrap in JSON with 'legacy' key
                $newShareLink = json_encode(['legacy' => $shareLink]);
                DB::table('meals')->where('id', $meal->id)->update(['share_link' => $newShareLink]);
            } elseif (is_array(json_decode($shareLink, true))) {
                // Update existing JSON to replace localhost
                $shareLinks = json_decode($shareLink, true);
                foreach ($shareLinks as $platform => $url) {
                    $shareLinks[$platform] = str_replace('http://localhost', 'https://bincone.apexjets.org', $url);
                }
                DB::table('meals')->where('id', $meal->id)->update(['share_link' => json_encode($shareLinks)]);
            }
        });
    }

    public function down()
    {
        DB::table('meals')->whereNotNull('share_link')->get()->each(function ($meal) {
            $shareLinks = json_decode($meal->share_link, true);
            if (is_array($shareLinks)) {
                $firstLink = reset($shareLinks) ?: null;
                DB::table('meals')->where('id', $meal->id)->update(['share_link' => $firstLink]);
            }
        });
    }
};
