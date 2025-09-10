<?php

namespace App\Jobs;

use App\Models\Meal;
use App\Models\Food;
use App\Models\Profile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;

class AnalyzeMealJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $meal;

    public function __construct(Meal $meal)
    {
        $this->meal = $meal;
    }

    public function handle()
    {
        Log::info('Processing meal analysis job', ['meal_id' => $this->meal->id]);

        try {
            $client = new Client();
            $apiKey = config('services.fireworks.api_key');
            $apiUrl = config('services.fireworks.url', 'https://api.fireworks.ai/inference/v1/chat/completions');

            // Prepare image for Fireworks AI (convert to base64)
            $imagePath = storage_path('app/public/' . str_replace(Storage::url(''), '', $this->meal->photo_url));
            $imageData = base64_encode(file_get_contents($imagePath));

            // Prompt Fireworks AI to identify foods and estimate portions
            $response = $client->post($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $apiKey,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'model' => 'llava-v1.5-13b', // Example Fireworks AI multimodal model
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => 'Analyze this food image and provide a JSON response with a list of foods, their estimated portion sizes (in grams), and confidence scores. Example: {"foods": [{"name": "pizza", "portion_size": 100, "confidence": 0.9}]}',
                                ],
                                [
                                    'type' => 'image_url',
                                    'image_url' => [
                                        'url' => 'data:image/jpeg;base64,' . $imageData,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'max_tokens' => 500,
                    'response_format' => ['type' => 'json_object'],
                ],
            ]);

            $result = json_decode($response->getBody(), true)['choices'][0]['message']['content'] ?? null;
            $result = json_decode($result, true);

            if (!$result || !isset($result['foods']) || empty($result['foods'])) {
                throw new \Exception('No foods recognized by Fireworks AI');
            }

            $foods = $result['foods'];
            $totalCalories = 0;
            $nutrients = ['sugar' => 0, 'sodium' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0];
            $foodNames = [];
            $recommendations = [];

            foreach ($foods as $foodItem) {
                $foodName = $foodItem['name'];
                $confidence = $foodItem['confidence'] ?? 1.0;
                $portion = $foodItem['portion_size'] ?? request('portion_size', 100);

                // Check local Food model or fetch from USDA API
                $food = Food::where('name', $foodName)->first();
                if (!$food) {
                    $nutritionResponse = $client->get('https://api.nal.usda.gov/fdc/v1/foods/search', [
                        'query' => [
                            'api_key' => config('services.usda.api_key'),
                            'query' => $foodName,
                            'pageSize' => 1,
                        ],
                    ]);
                    $nutritionData = json_decode($nutritionResponse->getBody(), true)['foods'][0] ?? null;

                    if ($nutritionData) {
                        $food = Food::create([
                            'name' => $foodName,
                            'calories_per_100g' => $nutritionData['nutrients']['Energy']['value'] ?? 0,
                            'nutrients' => [
                                'sugar' => $nutritionData['nutrients']['Sugars, total']['value'] ?? 0,
                                'sodium' => $nutritionData['nutrients']['Sodium']['value'] ?? 0,
                                'protein' => $nutritionData['nutrients']['Protein']['value'] ?? 0,
                                'carbs' => $nutritionData['nutrients']['Carbohydrate, by difference']['value'] ?? 0,
                                'fat' => $nutritionData['nutrients']['Total lipid (fat)']['value'] ?? 0,
                            ],
                        ]);
                    } else {
                        Log::warning('Food not found in database or USDA API', [
                            'meal_id' => $this->meal->id,
                            'food_name' => $foodName,
                        ]);
                        continue;
                    }
                }

                $calories = ($food->calories_per_100g / 100) * $portion;
                $totalCalories += $calories;
                $foodNames[] = $foodName;

                foreach ($nutrients as $key => &$value) {
                    $value += ($food->nutrients[$key] ?? 0) * ($portion / 100);
                }
            }

            if (empty($foodNames)) {
                throw new \Exception('No valid foods recognized');
            }

            // User-specific recommendations
            $profile = Profile::where('user_id', $this->meal->user_id)->first();
            if ($profile && $this->meal->user_id) {
                $conditions = is_array($profile->conditions) ? $profile->conditions : json_decode($profile->conditions, true) ?? [];

                if (in_array('diabetes', $conditions)) {
                    $recommendations[] = ($nutrients['sugar'] > 10)
                        ? 'Avoid: High sugar content'
                        : 'Safe for diabetes';
                }

                if (in_array('hypertension', $conditions)) {
                    $recommendations[] = ($nutrients['sodium'] > 200)
                        ? 'Warning: High sodium content'
                        : 'Low sodium';
                }

                if ($profile->goal === 'lose_weight') {
                    $recommendations[] = ($totalCalories > $profile->daily_calories * 0.2)
                        ? 'Eat less: Exceeds 20% of daily calories'
                        : 'Good portion for weight loss';
                } elseif ($profile->goal === 'gain_weight') {
                    $recommendations[] = ($totalCalories < $profile->daily_calories * 0.2)
                        ? 'Eat more: Below 20% of daily calories'
                        : 'Good portion for weight gain';
                }
            }

            $analysis = [
                'foods' => $foodNames,
                'calories' => $totalCalories,
                'nutrients' => $nutrients,
                'recommendations' => $recommendations,
                'status' => 'reviewed',
                'confidence' => $result['confidence'] ?? 1.0,
            ];

            $this->meal->update([
                'calories' => $totalCalories,
                'analysis' => json_encode($analysis),
                'status' => 'reviewed',
            ]);

            Log::info('Meal analysis job completed', [
                'meal_id' => $this->meal->id,
                'calories' => $totalCalories,
                'foods' => $foodNames,
                'confidence' => $result['confidence'] ?? 1.0,
            ]);
        } catch (\Exception $e) {
            Log::error('Meal analysis job failed', [
                'meal_id' => $this->meal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            $this->meal->update([
                'analysis' => json_encode(['error' => 'Analysis failed: ' . $e->getMessage(), 'status' => 'reviewed']),
                'status' => 'reviewed',
            ]);
        }
    }
}
