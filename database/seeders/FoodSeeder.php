<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Food; // ✅ this is the correct import

class FoodSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Food::create([
            'name' => 'Apple',
            'calories_per_100g' => 52,
            'nutrients' => json_encode([
                'sodium' => 1,
                'sugar' => 10,
                'protein' => 0.3
            ])
        ]);

        Food::create([
            'name' => 'Pizza',
            'calories_per_100g' => 266,
            'nutrients' => json_encode([
                'sodium' => 600,
                'sugar' => 5,
                'protein' => 11
            ])
        ]);
    }
}
