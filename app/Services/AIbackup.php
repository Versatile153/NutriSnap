<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class AIAnalysisService
{
 protected $huggingfaceApiKey;

    public function __construct()
    {
        // Hardcode the API key
        $this->huggingfaceApiKey = 'SECRET_REMOVED';

        // Validate API key
        if (empty($this->huggingfaceApiKey)) {
            Log::error('Hugging Face API key is missing');
            throw new \Exception('Hugging Face API key is not configured');
        }
    }
    public function analyzeMealPhoto($photoPath, $portionSize = null, $profile)
    {
        try {
            Log::info('Starting meal photo analysis', [
                'photo_path' => $photoPath,
                'portion_size' => $portionSize,
                'profile' => (array)$profile
            ]);

            // Validate inputs
            if (!is_null($portionSize) && (!is_numeric($portionSize) || $portionSize <= 0)) {
                Log::warning('Invalid portion size', ['portion_size' => $portionSize]);
                throw new \Exception('Portion size must be a positive number if provided');
            }

            // Set default portion size if null
            $portionSize = $portionSize ?? 100;

            // Validate and prepare image
            if (!Storage::disk('public')->exists($photoPath)) {
                Log::error('Image file does not exist', ['photo_path' => $photoPath]);
                throw new \Exception('Image file does not exist');
            }

            $imageContent = Storage::disk('public')->get($photoPath);
            if (!$imageContent) {
                Log::error('Failed to read image file', ['photo_path' => $photoPath]);
                throw new \Exception('Failed to read image file');
            }

            $imageSize = strlen($imageContent) / 1024;
            $imageInfo = getimagesizefromstring($imageContent);
            Log::info('Image details', ['size_kb' => $imageSize, 'mime' => $imageInfo['mime'] ?? 'unknown']);
            if ($imageInfo === false || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png'])) {
                Log::error('Invalid image format', ['mime' => $imageInfo['mime'] ?? 'unknown']);
                throw new \Exception('Invalid image format. Please upload JPEG or PNG.');
            }

            if ($imageSize > 1024) {
                Log::info('Resizing image', ['original_size_kb' => $imageSize]);
                $image = Image::read($imageContent)->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $imageContent = $image->toJpeg(75);
                $imageSize = strlen($imageContent) / 1024;
                Log::info('Image resized', ['new_size_kb' => $imageSize]);
                if ($imageSize > 1024) {
                    Log::error('Image exceeds size limit after compression', ['size_kb' => $imageSize]);
                    throw new \Exception('Image exceeds size limit (1MB) after compression');
                }
            }

            $base64Image = base64_encode($imageContent);
            Log::info('Base64 image prepared', [
                'size' => strlen($base64Image),
                'sample' => substr($base64Image, 0, 50)
            ]);

            // Validate base64 string
            if (strlen($base64Image) % 4 != 0) {
                Log::error('Invalid base64 string length', ['length' => strlen($base64Image)]);
                throw new \Exception('Base64 string length is not a multiple of 4');
            }
            if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $base64Image)) {
                Log::error('Base64 string contains invalid characters', ['sample' => substr($base64Image, 0, 50)]);
                throw new \Exception('Base64 string contains invalid characters');
            }
            try {
                base64_decode($base64Image, true);
            } catch (\Exception $e) {
                Log::error('Base64 decode failed', ['error' => $e->getMessage(), 'sample' => substr($base64Image, 0, 50)]);
                throw new \Exception('Invalid base64 string: ' . $e->getMessage());
            }

            // Initialize analysis variables
            $source = 'huggingface';
            $foodItems = [];
            $ingredients = [];
            $nonFoodItems = [];
            $nutrients = ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0];
            $macronutrients = [];
            $micronutrients = [];
            $calories = 0;
            $recommendations = [];
            $preparation = [];
            $servingDetails = [];
            $suitability = [];
            $isNonFood = false;

            // Stage 1: Item Detection with Hugging Face
            try {
                $detection = $this->detectItems($base64Image);
                Log::info('Hugging Face detection result', ['detection' => $detection]);

                $foodItems = $detection['foodItems'] ?? [];
                $ingredients = $detection['ingredients'] ?? [];
                $nonFoodItems = $detection['nonFoodItems'] ?? [];
                $nutrients = $detection['nutrients'] ?? $this->calculateNutrientsFallback($foodItems, $portionSize);
                $micronutrients = $detection['micronutrients'] ?? ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0];
                $calories = $detection['calories'] ?? $this->calculateCalories($nutrients);
                $isNonFood = $detection['isNonFood'] ?? false;

                // If no food items are detected and isNonFood is true, return early
                if ($isNonFood && empty($foodItems)) {
                    Log::warning('Non-food items detected, no food items found', [
                        'non_food_items' => $nonFoodItems,
                        'user_id' => $profile->user_id ?? 'unknown',
                        'photo_path' => $photoPath
                    ]);
                    return [
                        'food' => 'Unknown',
                        'calories' => 0,
                        'nutrients' => $nutrients,
                        'macronutrients' => $this->formatMacronutrients($nutrients),
                        'micronutrients' => $micronutrients,
                        'ingredients' => [],
                        'non_food_items' => $nonFoodItems,
                        'preparation' => [],
                        'serving_details' => ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'],
                        'suitability' => [],
                        'recommendations' => [],
                        'health_warnings' => [],
                        'source' => 'huggingface',
                        'is_non_food' => true,
                        'was_cropped' => $detection['wasCropped'] ?? false
                    ];
                }
            } catch (\Exception $e) {
                Log::error('Hugging Face detection failed', [
                    'error' => $e->getMessage(),
                    'stack' => $e->getTraceAsString()
                ]);
                return [
                    'food' => 'Unknown',
                    'calories' => 0,
                    'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                    'macronutrients' => [],
                    'micronutrients' => ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0],
                    'ingredients' => [],
                    'non_food_items' => [],
                    'preparation' => [],
                    'serving_details' => [],
                    'suitability' => [],
                    'recommendations' => [],
                    'health_warnings' => [],
                    'source' => 'huggingface',
                    'is_non_food' => false,
                    'was_cropped' => false,
                    'errors' => ['photo' => 'Failed to analyze image: ' . $e->getMessage()]
                ];
            }

            // Stage 2: Nutrient Analysis and Recommendations
            try {
                $macronutrients = $this->formatMacronutrients($nutrients);
                $recommendations = $this->generateRecommendations($nutrients, $calories, $profile);
                $preparation = $this->inferPreparation($foodItems);
                $servingDetails = ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'];
                $suitability = $this->checkDietarySuitability($foodItems, $nutrients);
            } catch (\Exception $e) {
                Log::error('Nutrient analysis failed', ['error' => $e->getMessage()]);
                return [
                    'food' => 'Unknown',
                    'calories' => 0,
                    'nutrients' => $nutrients,
                    'macronutrients' => $this->formatMacronutrients($nutrients),
                    'micronutrients' => $micronutrients,
                    'ingredients' => $ingredients,
                    'non_food_items' => $nonFoodItems,
                    'preparation' => [],
                    'serving_details' => ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'],
                    'suitability' => [],
                    'recommendations' => [],
                    'health_warnings' => [],
                    'source' => 'huggingface',
                    'is_non_food' => false,
                    'was_cropped' => $detection['wasCropped'] ?? false,
                    'errors' => ['photo' => 'Failed to analyze nutrients: ' . $e->getMessage()]
                ];
            }

            $analysis = [
                'food' => implode(', ', array_keys($foodItems)) ?: 'Unknown food',
                'calories' => $calories,
                'nutrients' => $nutrients,
                'macronutrients' => $macronutrients,
                'micronutrients' => $micronutrients,
                'ingredients' => $ingredients,
                'non_food_items' => $nonFoodItems,
                'preparation' => $preparation,
                'serving_details' => $servingDetails,
                'suitability' => $suitability,
                'recommendations' => $recommendations,
                'health_warnings' => $this->checkHealthConditions($foodItems, $nutrients, $profile),
                'source' => $source,
                'is_non_food' => $isNonFood,
                'was_cropped' => $detection['wasCropped'] ?? false
            ];

            Log::info('Meal photo analyzed successfully', ['photo_path' => $photoPath, 'analysis' => $analysis]);
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Meal analysis failed', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'photo_path' => $photoPath
            ]);
            return [
                'food' => 'Unknown',
                'calories' => 0,
                'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                'macronutrients' => [],
                'micronutrients' => ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0],
                'ingredients' => [],
                'non_food_items' => [],
                'preparation' => [],
                'serving_details' => [],
                'suitability' => [],
                'recommendations' => [],
                'health_warnings' => [],
                'source' => 'huggingface',
                'is_non_food' => false,
                'was_cropped' => false,
                'errors' => ['photo' => 'Failed to analyze meal: ' . $e->getMessage()]
            ];
        }
    }

 protected function detectItems($base64Image)
    {
        $maxRetries = 3;
        $pythonPath = env('PYTHON_PATH', '/home/apexgnoa/miniconda3/bin/python3.8');
        $pythonScript = '/home/apexgnoa/bincone.apexjets.org/huggingface_predict.py'; // Corrected path

        // Validate Python environment
        if (!file_exists($pythonPath)) {
            Log::error('Python executable not found', ['python_path' => $pythonPath]);
            throw new \Exception('Python executable not found at ' . $pythonPath);
        }
        if (!is_executable($pythonPath)) {
            Log::error('Python executable not executable', ['python_path' => $pythonPath]);
            throw new \Exception('Python executable is not executable at ' . $pythonPath);
        }

        // Debug script path
        Log::info('Checking Hugging Face script path', [
            'script_path' => $pythonScript,
            'exists' => file_exists($pythonScript),
            'readable' => is_readable($pythonScript),
            'permissions' => file_exists($pythonScript) ? sprintf('%o', fileperms($pythonScript) & 0777) : 'N/A'
        ]);

        if (!file_exists($pythonScript)) {
            Log::error('Hugging Face script not found', ['script_path' => $pythonScript]);
            throw new \Exception('Hugging Face script not found at ' . $pythonScript);
        }
        if (!is_readable($pythonScript)) {
            Log::error('Hugging Face script not readable', ['script_path' => $pythonScript]);
            throw new \Exception('Hugging Face script is not readable at ' . $pythonScript);
        }

        $tempDir = sys_get_temp_dir();
        Log::info('Checking temp directory', ['temp_dir' => $tempDir, 'writable' => is_writable($tempDir)]);
        if (!is_writable($tempDir)) {
            Log::error('Temporary directory not writable', ['temp_dir' => $tempDir]);
            throw new \Exception("Temporary directory {$tempDir} is not writable");
        }

        // Write base64 to a temp file
        $tempImageFile = tempnam($tempDir, 'img_') . '.txt';
        file_put_contents($tempImageFile, $base64Image);
        $escapedTempImageFile = escapeshellarg($tempImageFile);
        $escapedToken = escapeshellarg($this->huggingfaceApiKey);

        for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
            try {
                // Execute Python script with base64 file path and API token
                $command = "{$pythonPath} {$pythonScript} {$escapedTempImageFile} {$escapedToken} 2>&1";
                Log::info('Executing Hugging Face command', ['command' => $command, 'attempt' => $attempt]);
                $output = shell_exec($command);
                Log::info('Hugging Face API raw output', ['output' => $output]);

                // Clean up temp file
                if (file_exists($tempImageFile)) {
                    unlink($tempImageFile);
                }

                if ($output === null) {
                    Log::error('shell_exec returned null', ['command' => $command]);
                    throw new \Exception('shell_exec failed to execute command');
                }

                $jsonStart = strpos($output, '{');
                $jsonEnd = strrpos($output, '}');
                if ($jsonStart === false || $jsonEnd === false) {
                    Log::warning('No valid JSON found in Hugging Face output', ['output' => $output]);
                    throw new \Exception('Invalid JSON response from Hugging Face API');
                }

                $jsonString = substr($output, $jsonStart, $jsonEnd - $jsonStart + 1);
                $result = json_decode($jsonString, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    Log::warning('JSON decoding failed', ['error' => json_last_error_msg(), 'json_string' => substr($jsonString, 0, 100)]);
                    throw new \Exception('Failed to decode Hugging Face API response: ' . json_last_error_msg());
                }

                if (!isset($result['success']) || !$result['success']) {
                    Log::warning('Hugging Face API returned unsuccessful response', ['result' => $result]);
                    throw new \Exception('Hugging Face API failed: ' . ($result['error'] ?? 'Unknown error'));
                }

                $data = $result['result'];
                $foodItems = $data['food_items'] ?? [];
                $detection = [
                    'isNonFood' => $data['is_non_food'] ?? false,
                    'foodItems' => $foodItems,
                    'nonFoodItems' => $data['non_food_items'] ?? [],
                    'ingredients' => $data['ingredients'] ?? [],
                    'nutrients' => $data['nutrients'] ?? null,
                    'micronutrients' => $data['micronutrients'] ?? null,
                    'calories' => $data['calories'] ?? null,
                    'confidence' => !empty($foodItems) ? max(array_values($foodItems)) : 0,
                    'wasCropped' => $data['was_cropped'] ?? false
                ];
                Log::info('Hugging Face detection processed', ['detection' => $detection]);
                return $detection;
            } catch (\Exception $e) {
                Log::warning('Hugging Face detection attempt failed', [
                    'error' => $e->getMessage(),
                    'attempt' => $attempt,
                    'command' => $command,
                    'output' => $output ?? ''
                ]);
                if (file_exists($tempImageFile)) {
                    unlink($tempImageFile);
                }
                if ($attempt == $maxRetries) {
                    throw new \Exception('Hugging Face API failed after retries: ' . $e->getMessage());
                }
                sleep(pow(2, $attempt));
            }
        }
    }

    protected function calculateNutrientsFallback($foodItems, $volume)
    {
        Log::info('Calculating nutrients fallback', ['food_items' => $foodItems, 'volume' => $volume]);
        $nutrients = [
            'protein' => 0,
            'carbs' => 0,
            'fat' => 0,
            'fiber' => 0,
            'sodium' => 0
        ];

        $topFood = collect($foodItems)->sortByDesc(fn($prob) => $prob)->keys()->first();
        if (!$topFood) {
            Log::warning('No top food item identified', ['food_items' => $foodItems]);
            return $nutrients;
        }

        $defaultNutrients = [
            'salad' => ['protein' => 2, 'carbs' => 5, 'fat' => 1, 'fiber' => 3, 'sodium' => 100],
            'pizza' => ['protein' => 12, 'carbs' => 35, 'fat' => 10, 'fiber' => 2, 'sodium' => 600],
            'chicken_curry' => ['protein' => 20, 'carbs' => 15, 'fat' => 8, 'fiber' => 2, 'sodium' => 500],
            'lasagna' => ['protein' => 15, 'carbs' => 30, 'fat' => 12, 'fiber' => 3, 'sodium' => 700],
            'risotto' => ['protein' => 8, 'carbs' => 40, 'fat' => 5, 'fiber' => 1, 'sodium' => 400]
        ];

        $nutrients = $defaultNutrients[strtolower($topFood)] ?? ['protein' => 2, 'carbs' => 5, 'fat' => 1, 'fiber' => 2, 'sodium' => 100];
        foreach ($nutrients as $nutrient => $value) {
            $nutrients[$nutrient] = $value * ($volume / 100);
        }
        Log::info('Nutrients calculated', ['nutrients' => $nutrients]);
        return $nutrients;
    }

    protected function calculateCalories($nutrients)
    {
        $calories = ($nutrients['protein'] * 4) + ($nutrients['carbs'] * 4) + ($nutrients['fat'] * 9);
        Log::info('Calories calculated', ['calories' => $calories, 'nutrients' => $nutrients]);
        return round($calories, 2);
    }

    protected function formatMacronutrients($nutrients)
    {
        $macronutrients = [
            'protein' => ['value' => $nutrients['protein'] ?? 0, 'unit' => 'g'],
            'carbs' => ['value' => $nutrients['carbs'] ?? 0, 'unit' => 'g'],
            'fat' => ['value' => $nutrients['fat'] ?? 0, 'unit' => 'g']
        ];
        return $macronutrients;
    }

    protected function generateRecommendations($nutrients, $calories, $profile)
    {
        $recommendations = [];
        $dailyGoal = $profile->daily_calories ?? 2000;
        $mealCalorieTarget = $dailyGoal / 3;

        if ($calories > $mealCalorieTarget * 1.2) {
            $recommendations[] = 'Consider smaller portion sizes to reduce calorie intake.';
        } elseif ($calories < $mealCalorieTarget * 0.8) {
            $recommendations[] = 'Consider increasing portion size to meet calorie goals.';
        }

        if ($nutrients['sodium'] > 1000 && in_array('hypertension', $profile->health_conditions ?? [])) {
            $recommendations[] = 'High sodium content detected. Consider low-sodium alternatives.';
        } elseif ($nutrients['sodium'] <= 1000 && in_array('hypertension', $profile->health_conditions ?? [])) {
            $recommendations[] = 'Sodium content is suitable for hypertension management.';
        }

        if ($profile->goal === 'weight_gain' && $calories < $mealCalorieTarget) {
            $recommendations[] = 'Add nutrient-dense foods (e.g., nuts, avocado) to support weight gain goals.';
        }

        return $recommendations;
    }

    protected function inferPreparation($foodItems)
    {
        $preparation = [];
        $topFood = collect($foodItems)->sortByDesc(fn($prob) => $prob)->keys()->first();
        if ($topFood) {
            $preparation = match (strtolower($topFood)) {
                'salad' => ['raw', 'chopped'],
                'pizza' => ['baked'],
                'chicken_curry' => ['cooked', 'simmered'],
                'lasagna' => ['baked', 'layered'],
                'risotto' => ['cooked', 'stirred'],
                default => ['cooked']
            };
        }
        return $preparation;
    }

    protected function checkDietarySuitability($foodItems, $nutrients)
    {
        $suitability = [];
        $topFood = collect($foodItems)->sortByDesc(fn($prob) => $prob)->keys()->first();
        if ($topFood) {
            if (strtolower($topFood) === 'salad') {
                $suitability[] = 'Vegetarian';
                $suitability[] = 'Vegan';
            }
            if ($nutrients['sodium'] < 400) {
                $suitability[] = 'Low Sodium';
            }
        }
        return $suitability;
    }

    protected function checkHealthConditions($foodItems, $nutrients, $profile)
    {
        $warnings = [];
        $healthConditions = $profile->health_conditions ?? [];
        if (in_array('diabetes', $healthConditions) && ($nutrients['carbs'] ?? 0) > 50) {
            $warnings[] = 'High carbohydrate content may affect blood sugar levels.';
        }
        if (in_array('hypertension', $healthConditions) && ($nutrients['sodium'] ?? 0) > 1000) {
            $warnings[] = 'High sodium content may impact blood pressure.';
        }
        if (in_array('celiac', $healthConditions) && collect($foodItems)->keys()->contains(fn($food) => in_array(strtolower($food), ['pizza', 'lasagna']))) {
            $warnings[] = 'May contain gluten, unsuitable for celiac disease.';
        }
        return $warnings;
    }
    
    
    public function analyzeLeftoverPhoto($photoPath, $meal, $profile)
    {
        try {
            Log::info('Starting leftover photo analysis', [
                'photo_path' => $photoPath,
                'meal_id' => $meal->id ?? 'unknown',
                'profile' => (array)$profile
            ]);

            // Validate inputs
            if (!Storage::disk('public')->exists($photoPath)) {
                Log::error('Image file does not exist', ['photo_path' => $photoPath]);
                throw new \Exception('Image file does not exist');
            }

            // Reuse image preparation logic from analyzeMealPhoto
            $imageContent = Storage::disk('public')->get($photoPath);
            if (!$imageContent) {
                Log::error('Failed to read image file', ['photo_path' => $photoPath]);
                throw new \Exception('Failed to read image file');
            }

            $imageSize = strlen($imageContent) / 1024;
            $imageInfo = getimagesizefromstring($imageContent);
            if ($imageInfo === false || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png'])) {
                Log::error('Invalid image format', ['mime' => $imageInfo['mime'] ?? 'unknown']);
                throw new \Exception('Invalid image format. Please upload JPEG or PNG.');
            }

            if ($imageSize > 1024) {
                Log::info('Resizing image', ['original_size_kb' => $imageSize]);
                $image = Image::read($imageContent)->resize(800, 800, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                });
                $imageContent = $image->toJpeg(75);
                $imageSize = strlen($imageContent) / 1024;
                if ($imageSize > 1024) {
                    Log::error('Image exceeds size limit after compression', ['size_kb' => $imageSize]);
                    throw new \Exception('Image exceeds size limit (1MB) after compression');
                }
            }

            $base64Image = base64_encode($imageContent);
            Log::info('Base64 image prepared for leftover', [
                'size' => strlen($base64Image),
                'sample' => substr($base64Image, 0, 50)
            ]);

            // Validate base64 string (same as analyzeMealPhoto)
            if (strlen($base64Image) % 4 != 0) {
                Log::error('Invalid base64 string length', ['length' => strlen($base64Image)]);
                throw new \Exception('Base64 string length is not a multiple of 4');
            }
            if (!preg_match('/^[A-Za-z0-9+\/=]+$/', $base64Image)) {
                Log::error('Base64 string contains invalid characters', ['sample' => substr($base64Image, 0, 50)]);
                throw new \Exception('Base64 string contains invalid characters');
            }
            base64_decode($base64Image, true);

            // Detect items using Hugging Face
            $detection = $this->detectItems($base64Image);
            Log::info('Hugging Face detection result for leftover', ['detection' => $detection]);

            $foodItems = $detection['foodItems'] ?? [];
            $ingredients = $detection['ingredients'] ?? [];
            $nonFoodItems = $detection['nonFoodItems'] ?? [];
            $nutrients = $detection['nutrients'] ?? $this->calculateNutrientsFallback($foodItems, $meal->portion_size ?? 100);
            $micronutrients = $detection['micronutrients'] ?? ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0];
            $calories = $detection['calories'] ?? $this->calculateCalories($nutrients);
            $isNonFood = $detection['isNonFood'] ?? false;

            if ($isNonFood && empty($foodItems)) {
                Log::warning('Non-food items detected in leftover', [
                    'non_food_items' => $nonFoodItems,
                    'meal_id' => $meal->id ?? 'unknown',
                    'photo_path' => $photoPath
                ]);
                return [
                    'food' => 'Unknown',
                    'calories' => 0,
                    'nutrients' => $nutrients,
                    'macronutrients' => $this->formatMacronutrients($nutrients),
                    'micronutrients' => $micronutrients,
                    'ingredients' => [],
                    'non_food_items' => $nonFoodItems,
                    'preparation' => [],
                    'serving_details' => ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'],
                    'suitability' => [],
                    'recommendations' => [],
                    'health_warnings' => [],
                    'source' => 'huggingface',
                    'is_non_food' => true,
                    'was_cropped' => $detection['wasCropped'] ?? false
                ];
            }

            // Additional leftover-specific logic (e.g., adjust for portion size based on meal)
            if ($meal->portion_size && $meal->portion_size > 0) {
                $portionFactor = ($meal->portion_size ?? 100) / 100;
                foreach ($nutrients as $nutrient => $value) {
                    $nutrients[$nutrient] = $value * $portionFactor;
                }
                $calories = $this->calculateCalories($nutrients);
                Log::info('Adjusted nutrients for leftover portion size', ['nutrients' => $nutrients, 'calories' => $calories]);
            }

            $macronutrients = $this->formatMacronutrients($nutrients);
            $recommendations = $this->generateRecommendations($nutrients, $calories, $profile);
            $preparation = $this->inferPreparation($foodItems);
            $servingDetails = ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'];
            $suitability = $this->checkDietarySuitability($foodItems, $nutrients);

            $analysis = [
                'food' => implode(', ', array_keys($foodItems)) ?: 'Unknown food',
                'calories' => $calories,
                'nutrients' => $nutrients,
                'macronutrients' => $macronutrients,
                'micronutrients' => $micronutrients,
                'ingredients' => $ingredients,
                'non_food_items' => $nonFoodItems,
                'preparation' => $preparation,
                'serving_details' => $servingDetails,
                'suitability' => $suitability,
                'recommendations' => $recommendations,
                'health_warnings' => $this->checkHealthConditions($foodItems, $nutrients, $profile),
                'source' => 'huggingface',
                'is_non_food' => $isNonFood,
                'was_cropped' => $detection['wasCropped'] ?? false
            ];

            Log::info('Leftover photo analyzed successfully', ['photo_path' => $photoPath, 'analysis' => $analysis]);
            return $analysis;
        } catch (\Exception $e) {
            Log::error('Leftover analysis failed', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'photo_path' => $photoPath
            ]);
            return [
                'food' => 'Unknown',
                'calories' => 0,
                'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                'macronutrients' => [],
                'micronutrients' => ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0],
                'ingredients' => [],
                'non_food_items' => [],
                'preparation' => [],
                'serving_details' => [],
                'suitability' => [],
                'recommendations' => [],
                'health_warnings' => [],
                'source' => 'huggingface',
                'is_non_food' => false,
                'was_cropped' => false,
                'errors' => ['photo' => 'Failed to analyze leftover: ' . $e->getMessage()]
            ];
        }
    }
}