<?php

namespace App\Http\Controllers;

use App\Models\Meal;
use App\Services\AIAnalysisService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Laravel\Facades\Image;
use Barryvdh\DomPDF\Facade\Pdf as PDF;
use Illuminate\Support\Facades\Validator;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
class MealController extends Controller
{
    protected $aiAnalysisService;

    public function __construct(AIAnalysisService $aiAnalysisService)
    {
        $this->middleware('auth')->except(['showPublic']);
        $this->aiAnalysisService = $aiAnalysisService;
        Log::info('MealController initialized');
    }

  public function index(Request $request)
    {
        try {
            $user = auth()->user();
            if (!$user) {
                return redirect()->guest('login');
            }

            $period = $request->query('period', '1month');
            $days = match ($period) {
                '1week' => 7,
                '3months' => 90,
                default => 30,
            };

            $meals = $user->meals()
                ->select('id', 'photo_url', 'leftover_photo_url', 'meal_type', 'calories', 'analysis', 'leftover_analysis', 'feedback', 'uuid', 'status', 'created_at')
                ->where('created_at', '>=', now()->subDays($days))
                ->latest()
                ->take(12)
                ->get()
                ->map(function ($meal) {
                    // Process image URLs
                    $meal->photo_url = $meal->photo_url ? Storage::disk('public')->url($meal->photo_url) : null;
                    $meal->photo_exists = $meal->photo_url && Storage::disk('public')->exists(str_replace(config('app.url') . '/storage/', '', $meal->photo_url));
                    $meal->leftover_photo_url = $meal->leftover_photo_url ? Storage::disk('public')->url($meal->leftover_photo_url) : null;
                    $meal->leftover_photo_exists = $meal->leftover_photo_url && Storage::disk('public')->exists(str_replace(config('app.url') . '/storage/', '', $meal->leftover_photo_url));

                    // Use temporary variables for analysis processing
                    $analysis = $meal->analysis ?? [];
                    $leftoverAnalysis = $meal->leftover_analysis ?? [];

                    $defaultAnalysis = [
                        'is_non_food' => false,
                        'non_food_items' => [],
                        'food_items' => [],
                        'ingredients' => [],
                        'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                        'macronutrients' => [
                            'protein' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                            'carbs' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                            'fat' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                        ],
                        'micronutrients' => [
                            'vitamin_c' => ['value' => 0, 'unit' => 'mg', 'percentage' => 0],
                            'calcium' => ['value' => 0, 'unit' => 'mg', 'percentage' => 0],
                            'iron' => ['value' => 0, 'unit' => 'mg', 'percentage' => 0],
                        ],
                        'calories' => 0,
                        'source' => 'huggingface',
                        'was_cropped' => false,
                    ];

                    // Calculate percentages for analysis
                    $totalCalories = $analysis['calories'] ?? 0;
                    $macroPercentages = [
                        'protein' => $totalCalories > 0 ? ($analysis['nutrients']['protein'] ?? 0) * 4 / $totalCalories * 100 : 0,
                        'carbs' => $totalCalories > 0 ? ($analysis['nutrients']['carbs'] ?? 0) * 4 / $totalCalories * 100 : 0,
                        'fat' => $totalCalories > 0 ? ($analysis['nutrients']['fat'] ?? 0) * 9 / $totalCalories * 100 : 0,
                    ];
                    $microPercentages = [
                        'vitamin_c' => ($analysis['micronutrients']['vitamin_c'] ?? 0) / 90 * 100,
                        'calcium' => ($analysis['micronutrients']['calcium'] ?? 0) / 1300 * 100,
                        'iron' => ($analysis['micronutrients']['iron'] ?? 0) / 18 * 100,
                    ];

                    // Merge and update analysis
                    $analysis = array_merge($defaultAnalysis, $analysis);
                    $analysis['macronutrients']['protein']['percentage'] = round($macroPercentages['protein'], 1);
                    $analysis['macronutrients']['carbs']['percentage'] = round($macroPercentages['carbs'], 1);
                    $analysis['macronutrients']['fat']['percentage'] = round($macroPercentages['fat'], 1);
                    $analysis['micronutrients']['vitamin_c'] = [
                        'value' => is_array($analysis['micronutrients']['vitamin_c'] ?? []) ? ($analysis['micronutrients']['vitamin_c']['value'] ?? 0) : ($analysis['micronutrients']['vitamin_c'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($microPercentages['vitamin_c'], 1),
                    ];
                    $analysis['micronutrients']['calcium'] = [
                        'value' => is_array($analysis['micronutrients']['calcium'] ?? []) ? ($analysis['micronutrients']['calcium']['value'] ?? 0) : ($analysis['micronutrients']['calcium'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($microPercentages['calcium'], 1),
                    ];
                    $analysis['micronutrients']['iron'] = [
                        'value' => is_array($analysis['micronutrients']['iron'] ?? []) ? ($analysis['micronutrients']['iron']['value'] ?? 0) : ($analysis['micronutrients']['iron'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($microPercentages['iron'], 1),
                    ];

                    // Deduplicate non_food_items
                    if (!empty($analysis['non_food_items']) && is_array($analysis['non_food_items'])) {
                        $analysis['non_food_items'] = array_unique($analysis['non_food_items']);
                    }

                    // Calculate percentages for leftover analysis
                    $totalLeftoverCalories = $leftoverAnalysis['calories'] ?? 0;
                    $leftoverMacroPercentages = [
                        'protein' => $totalLeftoverCalories > 0 ? ($leftoverAnalysis['nutrients']['protein'] ?? 0) * 4 / $totalLeftoverCalories * 100 : 0,
                        'carbs' => $totalLeftoverCalories > 0 ? ($leftoverAnalysis['nutrients']['carbs'] ?? 0) * 4 / $totalLeftoverCalories * 100 : 0,
                        'fat' => $totalLeftoverCalories > 0 ? ($leftoverAnalysis['nutrients']['fat'] ?? 0) * 9 / $totalLeftoverCalories * 100 : 0,
                    ];
                    $leftoverMicroPercentages = [
                        'vitamin_c' => ($leftoverAnalysis['micronutrients']['vitamin_c'] ?? 0) / 90 * 100,
                        'calcium' => ($leftoverAnalysis['micronutrients']['calcium'] ?? 0) / 1300 * 100,
                        'iron' => ($leftoverAnalysis['micronutrients']['iron'] ?? 0) / 18 * 100,
                    ];

                    // Merge and update leftover analysis
                    $leftoverAnalysis = array_merge($defaultAnalysis, $leftoverAnalysis);
                    $leftoverAnalysis['macronutrients']['protein']['percentage'] = round($leftoverMacroPercentages['protein'], 1);
                    $leftoverAnalysis['macronutrients']['carbs']['percentage'] = round($leftoverMacroPercentages['carbs'], 1);
                    $leftoverAnalysis['macronutrients']['fat']['percentage'] = round($leftoverMacroPercentages['fat'], 1);
                    $leftoverAnalysis['micronutrients']['vitamin_c'] = [
                        'value' => is_array($leftoverAnalysis['micronutrients']['vitamin_c'] ?? []) ? ($leftoverAnalysis['micronutrients']['vitamin_c']['value'] ?? 0) : ($leftoverAnalysis['micronutrients']['vitamin_c'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($leftoverMicroPercentages['vitamin_c'], 1),
                    ];
                    $leftoverAnalysis['micronutrients']['calcium'] = [
                        'value' => is_array($leftoverAnalysis['micronutrients']['calcium'] ?? []) ? ($leftoverAnalysis['micronutrients']['calcium']['value'] ?? 0) : ($leftoverAnalysis['micronutrients']['calcium'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($leftoverMicroPercentages['calcium'], 1),
                    ];
                    $leftoverAnalysis['micronutrients']['iron'] = [
                        'value' => is_array($leftoverAnalysis['micronutrients']['iron'] ?? []) ? ($leftoverAnalysis['micronutrients']['iron']['value'] ?? 0) : ($leftoverAnalysis['micronutrients']['iron'] ?? 0),
                        'unit' => 'mg',
                        'percentage' => round($leftoverMicroPercentages['iron'], 1),
                    ];

                    if (!empty($leftoverAnalysis['non_food_items']) && is_array($leftoverAnalysis['non_food_items'])) {
                        $leftoverAnalysis['non_food_items'] = array_unique($leftoverAnalysis['non_food_items']);
                    }

                    // Assign processed data back to model
                    $meal->setAttribute('analysis', $analysis);
                    $meal->setAttribute('leftover_analysis', $leftoverAnalysis);

                    $meal->share_link = $meal->share_link ?? [];
                    $meal->share_proof = $meal->share_proof ?? [];
                    $meal->created_at = $meal->created_at ? $meal->created_at->setTimezone('Africa/Lagos') : null;

                    return $meal;
                });

            $startDate = now()->subDays($days)->startOfDay()->setTimezone('Africa/Lagos');
            $endDate = now()->endOfDay()->setTimezone('Africa/Lagos');
            $chartData = DB::table('meals')
                ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(calories) as calories'))
                ->where('user_id', $user->id)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->groupBy('date')
                ->get()
                ->map(fn($item) => ['date' => $item->date, 'calories' => $item->calories])
                ->toArray();

            return view('meals.index', compact('meals', 'period', 'chartData'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch meals', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('meals.index')->with('error', 'Failed to load meals. Please try again.');
        }
    }
public function store(Request $request)
{
    try {
        $validator = Validator::make($request->all(), [
            'photo' => 'required|image|mimes:jpeg,png|max:5120',
            'meal_type' => 'required|in:breakfast,lunch,dinner,snack',
            'portion_size' => 'required|numeric|min:1',
            'health_condition' => 'nullable|in:none,diabetes,hypertension,heart_disease,celiac',
            'platforms' => 'nullable|array',
            'platforms.*' => 'in:facebook,instagram,youtube',
        ]);

        if ($validator->fails()) {
            Log::warning('Validation failed for meal upload', ['errors' => $validator->errors()]);
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $profile = auth()->user()->profile ?? null;
        if (!$profile) {
            Log::warning('User profile not found', ['user_id' => auth()->id()]);
            $profile = (object)['daily_calories' => 2000, 'health_conditions' => [], 'goal' => ''];
        } else {
            $profile->height = $profile->height > 300 || $profile->height < 50 ? 170 : $profile->height;
            $profile->weight = $profile->weight > 500 || $profile->weight < 20 ? 70 : $profile->weight;
            $profile->daily_calories = $profile->daily_calories > 5000 || $profile->daily_calories < 500 ? 2000 : $profile->daily_calories;
        }

        $image = Image::read($request->file('photo')->getRealPath())->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $photoPath = 'meals/' . uniqid() . '.jpg';
        Storage::disk('public')->put($photoPath, $image->toJpeg(80));
        Log::info('Photo stored', ['photo_path' => $photoPath]);

        $portionSize = $request->input('portion_size');
        $analysis = $this->aiAnalysisService->analyzeMealPhoto($photoPath, $portionSize, $profile);

        if (isset($analysis['errors'])) {
            Log::warning('AI analysis failed', ['errors' => $analysis['errors']]);
            Storage::disk('public')->delete($photoPath);
            return redirect()->back()->with('error', 'Failed to analyze image: ' . $analysis['errors']['photo'])->withInput();
        }

        if ($analysis['is_non_food'] && empty($analysis['food'])) {
            Log::warning('Non-food items detected', [
                'non_food_items' => $analysis['non_food_items'],
                'photo_path' => $photoPath
            ]);
            Storage::disk('public')->delete($photoPath);
            return redirect()->back()->with('error', 'No food detected in the image. Detected items: ' . implode(', ', $analysis['non_food_items']) . '. Please upload a clear photo of a meal (e.g., pizza, salad, or pasta).')->withInput();
        }

        $macronutrients = $analysis['macronutrients'] ?? [];
        $totalGrams = array_sum(array_map(fn($macro) => $macro['value'] ?? 0, $macronutrients));
        if ($totalGrams > 0) {
            foreach ($macronutrients as $key => $macro) {
                $macronutrients[$key]['percentage'] = round(($macro['value'] ?? 0) / $totalGrams * 100, 2);
            }
        }
        Log::info('Macronutrients normalized', ['macronutrients' => $macronutrients]);
        $analysis['macronutrients'] = $macronutrients;

        $dailyGoal = $profile->daily_calories ?? 2000;
        $mealCalorieTarget = $dailyGoal / 3;
        $calories = $analysis['calories'] ?? 0;
        $feedback = [sprintf('Approx %.2f kcal, %s goal.', $calories, $calories > $mealCalorieTarget * 1.2 ? 'above' : ($calories < $mealCalorieTarget * 0.8 ? 'below' : 'within'))];
        if (!empty($analysis['recommendations'])) {
            $feedback = array_merge($feedback, $analysis['recommendations']);
        }
        Log::info('Feedback generated', ['feedback' => $feedback]);

        $uuid = (string) Str::uuid();
        $platforms = $request->input('platforms', []);
        $shareLinks = $this->generateShareLink($photoPath, $analysis, $platforms, $request->meal_type, $uuid);
        Log::info('Share links generated', ['share_links' => $shareLinks]);

        try {
            $meal = new Meal();
            $meal->user_id = auth()->id();
            $meal->photo_url = $photoPath;
            $meal->meal_type = $request->meal_type;
            $meal->calories = $analysis['calories'] ?? 0;
            $meal->feedback = implode(' ', $feedback);
            $meal->analysis = $analysis;
            $meal->uuid = $uuid;
            $meal->share_link = $shareLinks;
            $meal->health_condition = $request->health_condition ?? 'none';
            $meal->status = 'analyzed';
            $meal->created_at = now()->setTimezone('Africa/Lagos');
            $meal->save();

            Log::info('Meal created', [
                'meal_id' => $meal->id,
                'photo_url' => $meal->photo_url,
                'meal_type' => $meal->meal_type,
                'calories' => $meal->calories,
                'health_condition' => $meal->health_condition,
                'platforms' => $platforms,
                'share_links' => $shareLinks
            ]);

            $mealData = [
                'id' => $meal->id,
                'photo_url' => Storage::disk('public')->url($photoPath),
                'meal_type' => $meal->meal_type,
                'calories' => $meal->calories,
                'feedback' => $meal->feedback,
                'analysis' => $analysis,
                'share_link' => $shareLinks,
                'status' => $meal->status,
                'created_at' => $meal->created_at->setTimezone('Africa/Lagos')->format('M d, Y H:i'),
                'health_condition' => $meal->health_condition,
                'is_non_food' => $analysis['is_non_food'] ?? false,
            ];

            return redirect()->route('meals.index')->with('success', 'Meal analyzed successfully.')->with('meal', $mealData);
        } catch (QueryException $e) {
            Log::error('Failed to save meal to database', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            Storage::disk('public')->delete($photoPath);
            return redirect()->back()->with('error', 'Failed to save meal to database. Please try again.')->withInput();
        }
    } catch (\Exception $e) {
        Log::error('Meal creation failed', [
            'error' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        if (isset($photoPath) && Storage::disk('public')->exists($photoPath)) {
            Storage::disk('public')->delete($photoPath);
        }
        return redirect()->back()->with('error', 'An error occurred during analysis: ' . $e->getMessage())->withInput();
    }
}

    protected function normalizeMacronutrients($analysis)
    {
        if (!isset($analysis['macronutrients'])) {
            Log::warning('No macronutrients in analysis', ['analysis' => $analysis]);
            return $analysis;
        }

        $totalCalories = $analysis['calories'] ?? 0;
        $caloriesPerGram = [
            'protein' => 4,
            'carbs' => 4,
            'fat' => 9,
        ];

        $normalizedMacronutrients = [];
        foreach ($analysis['macronutrients'] as $key => $data) {
            $grams = isset($data['value']) ? $data['value'] : (isset($data['grams']) ? $data['grams'] : 0);
            $calories = $grams * ($caloriesPerGram[$key] ?? 4);
            $percentage = $totalCalories > 0 ? round(($calories / $totalCalories) * 100, 2) : 0;

            $normalizedMacronutrients[$key] = [
                'value' => $grams,
                'unit' => $data['unit'] ?? 'g',
                'percentage' => $percentage,
            ];
        }

        $analysis['macronutrients'] = $normalizedMacronutrients;
        Log::info('Macronutrients normalized', ['macronutrients' => $normalizedMacronutrients]);
        return $analysis;
    }

    protected function generateShareLink($photoUrl, $analysis, $platforms, $mealType, $uuid)
    {
        $baseUrl = config('app.url');
        $shareLinks = [];

        $caption = sprintf(
            "My %s: %d kcal. %s #CalAI",
            ucfirst($mealType),
            $analysis['calories'] ?? 0,
            implode(' ', $analysis['recommendations'] ?? [])
        );

        foreach ($platforms as $platform) {
            $shareLinks[$platform] = match ($platform) {
                'facebook' => "https://www.facebook.com/sharer/sharer.php?u={$baseUrl}/results/public/{$uuid}&quote={$caption}",
                'instagram' => "https://www.instagram.com/?url={$baseUrl}/results/public/{$uuid}&caption={$caption}",
                'youtube' => "https://www.youtube.com/upload?url={$baseUrl}/results/public/{$uuid}",
                default => null,
            };
        }

        Log::info('Share links generated', ['share_links' => $shareLinks]);
        return $shareLinks;
    }

    protected function generateFeedback($analysis, $profile)
    {
        $calories = $analysis['calories'] ?? 0;
        $dailyGoal = $profile->daily_calories ?? 2000;
        $mealCalorieTarget = $dailyGoal / 3;

        $feedback = [
            "Approx {$calories} kcal, " . ($calories > $mealCalorieTarget ? 'above' : 'within') . " goal.",
        ];

        if (!empty($analysis['health_warnings'])) {
            $feedback = array_merge($feedback, $analysis['health_warnings']);
        }

        Log::info('Feedback generated', ['feedback' => $feedback]);
        return implode(' ', $feedback);
    }

   public function show($id)
{
    try {
        Log::info('Fetching meal', ['meal_id' => $id, 'user_id' => auth()->id()]);
        $user = auth()->user();
        if (!$user) {
            Log::warning('Unauthorized access attempt to meal show', ['meal_id' => $id]);
            return redirect()->route('login');
        }

        $meal = $user->meals()->findOrFail($id);

        // Handle image existence checks
        $rawPhotoUrl = $meal->photo_url;
        $rawLeftoverPhotoUrl = $meal->leftover_photo_url;
        $photoExists = $meal->photo_url ? Storage::disk('public')->exists($meal->photo_url) : false;
        $leftoverPhotoExists = $meal->leftover_photo_url ? Storage::disk('public')->exists($meal->leftover_photo_url) : false;
        $photoPath = $meal->photo_url ? storage_path('app/public/' . $meal->photo_url) : null;
        $photoFileExists = $meal->photo_url ? file_exists($photoPath) : false;

        // Store existence results in model
        $meal->photo_exists = $photoExists;
        $meal->leftover_photo_exists = $leftoverPhotoExists;
        $meal->photo_url = $meal->photo_url ? Storage::disk('public')->url($meal->photo_url) : null;
        $meal->leftover_photo_url = $meal->leftover_photo_url ? Storage::disk('public')->url($meal->leftover_photo_url) : null;

        Log::debug('Meal image details', [
            'meal_id' => $meal->id,
            'raw_photo_url' => $rawPhotoUrl,
            'photo_url' => $meal->photo_url,
            'photo_exists' => $photoExists,
            'photo_file_exists' => $photoFileExists,
            'photo_path' => $photoPath,
            'raw_leftover_photo_url' => $rawLeftoverPhotoUrl,
            'leftover_photo_url' => $meal->leftover_photo_url,
            'leftover_photo_exists' => $leftoverPhotoExists,
            'app_url' => config('app.url'),
        ]);

        // Handle analysis
        if (empty($meal->analysis)) {
            Log::warning('Meal analysis not available', ['meal_id' => $id]);
            $meal->analysis = ['error' => 'Analysis data not available'];
        } else {
            $analysis = is_string($meal->analysis) ? json_decode($meal->analysis, true) ?? [] : ($meal->analysis ?? []);
            $meal->analysis = array_merge([
                'food' => 'Unknown food',
                'calories' => 0,
                'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                'macronutrients' => [
                    'protein' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                    'carbs' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                    'fat' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                ],
                'micronutrients' => ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0],
                'ingredients' => [],
                'non_food_items' => [],
                'preparation' => [],
                'serving_details' => ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'],
                'suitability' => [],
                'recommendations' => [],
                'health_warnings' => [],
                'source' => 'huggingface',
                'is_non_food' => false,
                'was_cropped' => false,
            ], $analysis);
        }

        // Handle leftover analysis
        if (!empty($meal->leftover_analysis)) {
            $leftoverAnalysis = is_string($meal->leftover_analysis) ? json_decode($meal->leftover_analysis, true) ?? [] : ($meal->leftover_analysis ?? []);
            $meal->leftover_analysis = array_merge([
                'calories' => 0,
                'nutrients' => ['protein' => 0, 'carbs' => 0, 'fat' => 0, 'fiber' => 0, 'sodium' => 0],
                'macronutrients' => [
                    'protein' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                    'carbs' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                    'fat' => ['value' => 0, 'unit' => 'g', 'percentage' => 0],
                ],
                'micronutrients' => ['vitamin_c' => 0, 'calcium' => 0, 'iron' => 0],
                'ingredients' => [],
                'non_food_items' => [],
                'preparation' => [],
                'serving_details' => ['method' => 'plate', 'utensils' => ['fork', 'knife'], 'setting' => 'table'],
                'suitability' => [],
                'recommendations' => [],
                'health_warnings' => [],
                'source' => 'huggingface',
                'is_non_food' => false,
                'was_cropped' => false,
            ], $leftoverAnalysis);
        }

        // Handle share data
        $meal->share_link = is_string($meal->share_link) ? json_decode($meal->share_link, true) ?? [] : ($meal->share_link ?? []);
        $meal->share_proof = is_string($meal->share_proof) ? json_decode($meal->share_proof, true) ?? [] : ($meal->share_proof ?? []);

        // Set timezone for created_at
        if ($meal->created_at) {
            $meal->created_at = $meal->created_at->setTimezone('Africa/Lagos');
        }

        Log::info('Meal retrieved', ['meal_id' => $id]);
        return view('meals.show', compact('meal'));
    } catch (\Exception $e) {
        Log::error('Failed to fetch meal', ['meal_id' => $id, 'error' => $e->getMessage(), 'stack' => $e->getTraceAsString()]);
        return redirect()->route('meals.index')->with('error', 'Failed to load meal details. Please try again.');
    }
}

    public function storeLeftover(Request $request)
{
    Log::info('Leftover store request received', [
        'user_id' => auth()->id(),
        'input' => $request->all()
    ]);
    $validated = $request->validate([
        'meal_id' => 'required|exists:meals,id,user_id,' . auth()->id(),
        'leftover_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
    ]);
    Log::info('Leftover request validated', ['validated' => $validated]);

    try {
        $meal = auth()->user()->meals()->findOrFail($validated['meal_id']);

        $image = Image::read($request->file('leftover_photo')->getRealPath())->resize(800, 800, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $leftoverPhotoPath = 'meals/leftovers/' . uniqid() . '.jpg';
        Storage::disk('public')->put($leftoverPhotoPath, $image->toJpeg(80));
        $leftoverPhotoUrl = Storage::disk('public')->url($leftoverPhotoPath);
        Log::info('Leftover image saved', [
            'leftover_photo_path' => $leftoverPhotoPath,
            'leftover_photo_url' => $leftoverPhotoUrl
        ]);

        $profile = auth()->user()->profile ?? (object)[
            'height' => 170,
            'weight' => 120,
            'target_weight' => 50,
            'goal_period' => 90,
            'health_conditions' => [$meal->health_condition],
            'daily_calories' => 2000,
        ];

        if (isset($profile->height) && ($profile->height > 300 || $profile->height < 50)) {
            Log::warning('Invalid height in profile', ['height' => $profile->height, 'user_id' => auth()->id()]);
            $profile->height = 170;
        }
        if (isset($profile->weight) && ($profile->weight > 500 || $profile->weight < 20)) {
            Log::warning('Invalid weight in profile', ['weight' => $profile->weight, 'user_id' => auth()->id()]);
            $profile->weight = 70;
        }

        $analysis = $this->aiAnalysisService->analyzeLeftoverPhoto($leftoverPhotoPath, $meal, $profile);
        Log::info('Leftover analysis completed', ['analysis' => $analysis]);

        if (isset($analysis['errors'])) {
            Log::warning('AI analysis failed for leftover', ['errors' => $analysis['errors']]);
            Storage::disk('public')->delete($leftoverPhotoPath);
            return redirect()->back()->with('error', 'Failed to analyze leftover image: ' . $analysis['errors']['photo'])->withInput();
        }

        if ($analysis['is_non_food'] && empty($analysis['food_items'])) {
            Log::warning('Non-food items detected in leftover', [
                'non_food_items' => $analysis['non_food_items'],
                'leftover_photo_path' => $leftoverPhotoPath
            ]);
            Storage::disk('public')->delete($leftoverPhotoPath);
            return redirect()->back()->with('error', 'No food detected in the leftover image. Detected items: ' . implode(', ', $analysis['non_food_items']) . '. Please upload a clear photo of leftover food.')->withInput();
        }

        $macronutrients = $analysis['macronutrients'] ?? [];
        $totalGrams = array_sum(array_map(fn($macro) => $macro['value'] ?? 0, $macronutrients));
        if ($totalGrams > 0) {
            foreach ($macronutrients as $key => $macro) {
                $macronutrients[$key]['percentage'] = round(($macro['value'] ?? 0) / $totalGrams * 100, 2);
            }
        }
        $analysis['macronutrients'] = $macronutrients;

        $originalCalories = $meal->calories ?? 0;
        $leftoverCalories = $analysis['calories'] ?? 0;
        $consumedCalories = max(0, $originalCalories - $leftoverCalories);
        $analysis['consumed_calories'] = $consumedCalories;

        $dailyGoal = $profile->daily_calories ?? 2000;
        $mealCalorieTarget = $dailyGoal / 3;
        $feedback = [sprintf('Consumed approx %.2f kcal, %s goal.', $consumedCalories, $consumedCalories > $mealCalorieTarget * 1.2 ? 'above' : ($consumedCalories < $mealCalorieTarget * 0.8 ? 'below' : 'within'))];
        if (!empty($analysis['recommendations'])) {
            $feedback = array_merge($feedback, $analysis['recommendations']);
        }
        Log::info('Leftover feedback generated', ['feedback' => $feedback]);

        try {
            $meal->leftover_photo_url = $leftoverPhotoPath;
            $meal->leftover_analysis = $analysis;
            $meal->calories = $consumedCalories;
            $meal->feedback = implode(' ', $feedback);
            $meal->status = 'leftover_analyzed';
            $meal->updated_at = now()->setTimezone('Africa/Lagos');
            $meal->save();

            Log::info('Leftover meal updated', [
                'meal_id' => $meal->id,
                'leftover_photo_url' => $leftoverPhotoUrl,
                'calories' => $meal->calories,
                'status' => $meal->status
            ]);

            $leftoverMealData = [
                'id' => $meal->id,
                'photo_url' => $meal->photo_url ? Storage::disk('public')->url($meal->photo_url) : null,
                'leftover_photo_url' => $leftoverPhotoUrl,
                'meal_type' => $meal->meal_type,
                'calories' => $meal->calories,
                'feedback' => $meal->feedback,
                'analysis' => $analysis,
                'status' => $meal->status,
                'created_at' => $meal->created_at->setTimezone('Africa/Lagos')->format('M d, Y H:i'),
                'health_condition' => $meal->health_condition,
                'is_non_food' => $analysis['is_non_food'] ?? false,
            ];

            return redirect()->route('meals.index')->with('success', 'Leftover analyzed successfully.')->with('leftover_meal', $leftoverMealData);
        } catch (QueryException $e) {
            Log::error('Failed to save leftover meal to database', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            Storage::disk('public')->delete($leftoverPhotoPath);
            return redirect()->back()->with('error', 'Failed to save leftover analysis to database. Please try again.')->withInput();
        }
    } catch (\Exception $e) {
        Log::error('Leftover analysis failed', [
            'error' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);
        if (isset($leftoverPhotoPath) && Storage::disk('public')->exists($leftoverPhotoPath)) {
            Storage::disk('public')->delete($leftoverPhotoPath);
        }
        return redirect()->back()->with('error', 'An error occurred during leftover analysis: ' . $e->getMessage())->withInput();
    }
}
    public function storeShareProof(Request $request)
    {
        Log::info('Share proof submission received', [
            'user_id' => auth()->id(),
            'input' => $request->all()
        ]);

        $validated = $request->validate([
            'meal_id' => 'required|exists:meals,id,user_id,' . auth()->id(),
            'share_proof' => 'required|url',
            'platform' => 'required|in:facebook,instagram,youtube',
        ]);
        Log::info('Share proof validated', ['validated' => $validated]);

        try {
            $meal = auth()->user()->meals()->findOrFail($validated['meal_id']);
            $shareProof = is_string($meal->share_proof) ? json_decode($meal->share_proof, true) ?? [] : $meal->share_proof ?? [];

            if (isset($shareProof[$validated['platform']])) {
                Log::warning('Share proof already submitted for platform', [
                    'meal_id' => $meal->id,
                    'platform' => $validated['platform']
                ]);
                return redirect()->back()->with('error', 'Share proof already submitted for this platform.');
            }

            $shareProof[$validated['platform']] = $validated['share_proof'];
            $meal->share_proof = $shareProof;
            $meal->updated_at = now()->setTimezone('Africa/Lagos');
            $meal->save();

            Log::info('Share proof saved', [
                'meal_id' => $meal->id,
                'platform' => $validated['platform'],
                'share_proof' => $validated['share_proof']
            ]);

            return redirect()->route('meals.index')->with('success', 'Share proof submitted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to save share proof', [
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->back()->with('error', 'Failed to submit share proof: ' . $e->getMessage())->withInput();
        }
    }

    public function showPublic($uuid)
    {
        try {
            $meal = Meal::where('uuid', $uuid)->firstOrFail();
            $meal->photo_url = $meal->photo_url ? Storage::disk('public')->url($meal->photo_url) : null;
            $meal->analysis = is_string($meal->analysis) ? json_decode($meal->analysis, true) ?? [] : $meal->analysis ?? [];
            Log::info('Public meal retrieved', ['uuid' => $uuid]);
            return view('meals.public', compact('meal'));
        } catch (\Exception $e) {
            Log::error('Failed to fetch public meal', [
                'uuid' => $uuid,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            return redirect()->route('home')->with('error', 'Public meal not found.');
        }
    }

    public function destroy($id)
    {
        try {
            $meal = auth()->user()->meals()->findOrFail($id);
            if ($meal->photo_url) {
                Storage::disk('public')->delete($meal->photo_url);
            }
            if ($meal->leftover_photo_url) {
                Storage::disk('public')->delete($meal->leftover_photo_url);
            }
            $meal->delete();
            Log::info('Meal deleted', ['meal_id' => $id, 'user_id' => auth()->id()]);
            return redirect()->route('meals.index')->with('success', 'Meal deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Failed to delete meal', [
                'meal_id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            return redirect()->route('meals.index')->with('error', 'Failed to delete meal: ' . $e->getMessage());
        }
    }

    public function downloadReport($id)
    {
        try {
            $meal = auth()->user()->meals()->findOrFail($id);
            $meal->photo_url = $meal->photo_url ? Storage::disk('public')->url($meal->photo_url) : null;
            $meal->leftover_photo_url = $meal->leftover_photo_url ? Storage::disk('public')->url($meal->leftover_photo_url) : null;
            $meal->analysis = is_string($meal->analysis) ? json_decode($meal->analysis, true) ?? [] : $meal->analysis ?? [];
            $meal->leftover_analysis = is_string($meal->leftover_analysis) ? json_decode($meal->leftover_analysis, true) ?? [] : $meal->leftover_analysis ?? [];

            $pdf = PDF::loadView('meals.report', compact('meal'));
            $filename = 'meal_report_' . $meal->id . '_' . now()->format('Ymd_His') . '.pdf';
            Log::info('Generating PDF report', ['meal_id' => $id, 'filename' => $filename]);
            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('Failed to generate PDF report', [
                'meal_id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString()
            ]);
            return redirect()->route('meals.index')->with('error', 'Failed to generate report: ' . $e->getMessage());
        }
    }
}