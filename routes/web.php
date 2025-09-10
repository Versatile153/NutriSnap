<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\MealController;
use App\Http\Controllers\FoodController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\CouponController;
use App\Http\Controllers\ResultController;

use App\Http\Controllers\ContactController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\AdminEmailController;



/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/






Route::post('/language/{lang}', [LanguageController::class, 'switchLang'])->name('language.switch');

// Other routes...





Route::get('/contact', function () {
    return view('contact');
})->name('contact');
Route::post('/clear-session', [SessionController::class, 'clear'])->name('clear.session');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.submit');
Route::get('/api-docs', function () {
    return redirect()->route('l5-swagger.default.api');
});

Route::get('/docs/nutrisnap-widget', function () {
    return view('doc.nutrisnap-widget');
})->name('docs.nutrisnap-widget');


// Redirect root to dashboard
Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/privacy', function () {
    return view('privacy');
})->name('privacy');

Route::get('/terms', function () {
    return view('terms');
})->name('terms');


Route::middleware(['auth:admin'])->group(function () {
    Route::get('admin/emails', [AdminEmailController::class, 'index'])->name('admin.emails');
    Route::post('admin/emails/send', [AdminEmailController::class, 'send'])->name('admin.emails.send');
});


Route::get('/photo-analysis-widget.js', function () {
    return response()->file(public_path('js/photo-analysis-widget.js'), [
        'Content-Type' => 'application/javascript',
    ]);
})->name('photo-analysis-widget');


Route::middleware(['auth'])->group(function () {
    Route::get('/coupons', [CouponController::class, 'index'])->name('coupons.index');
    Route::post('/coupons/share', [CouponController::class, 'shareAndIssueCoupon'])->name('coupons.share');
    Route::post('/coupons/apply', [CouponController::class, 'applyCoupon'])->name('coupons.apply');
      Route::post('/coupons/share', [CouponController::class, 'share'])->name('coupons.share');
      Route::post('/coupons/apply', [App\Http\Controllers\CouponController::class, 'apply'])->name('coupons.apply')->middleware('auth');
});



Route::middleware(['web'])->group(function () {
    
    Route::get('/admin/meal/{meal}', [AdminDashboardController::class, 'showMeal'])->name('admin.meal.show');
    Route::get('/admin/user/{user}', [AdminDashboardController::class, 'showUser'])->name('admin.user.show');
    Route::get('/admin/subscription/{subscription}', [AdminDashboardController::class, 'showSubscription'])->name('admin.subscription.show');
    Route::get('/admin/coupon/{coupon}', [AdminDashboardController::class, 'showCoupon'])->name('admin.coupon.show');
    Route::get('/admin/profile/{profile}', [AdminDashboardController::class, 'showProfile'])->name('admin.profile.show');
    Route::get('/admin/contact/{contact}', [AdminDashboardController::class, 'showContact'])->name('admin.contact.show');
    Route::get('/admin/correction-request/{correctionRequest}', [AdminDashboardController::class, 'showCorrectionRequest'])->name('admin.correction-request.show');
});

Route::post('results/{uuid}/generate-image', [ResultController::class, 'generateImage'])->name('results.generate-image');
Route::get('/results/{uuid}', [ResultController::class, 'showPublic'])->name('results.public');
Route::get('/results/{uuid}/download', [ResultController::class, 'download'])->name('results.download');
Route::delete('/results/{id}', [ResultController::class, 'destroy'])->name('results.destroy')->middleware(['web', 'auth']);


Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
Route::get('/analysis', [AdminDashboardController::class, 'analysis'])->name('admin.analysis');
 Route::get('/progress/{user_id}', [AdminDashboardController::class, 'progress'])->name('progress.index');

// Override default /login to return JSON token
Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])
    ->name('login')
    ->withoutMiddleware([\App\Http\Middleware\VerifyCsrfToken::class]);


Route::middleware(['auth'])->group(function () {
    Route::get('/meals', [MealController::class, 'index'])->name('meals.index');
    Route::get('/meals/{meal}', [MealController::class, 'show'])->name('meals.show');
    Route::post('/diet/download', [MealController::class, 'download'])->name('diet.download');
    Route::post('/meals', [MealController::class, 'store'])->name('meals.store');
    Route::post('/meals/leftover', [MealController::class, 'storeLeftover'])->name('meals.leftover');
    Route::post('/meals/share-proof', [MealController::class, 'storeShareProof'])->name('meals.shareProof');
    Route::delete('/meals/{meal}', [MealController::class, 'destroy'])->name('meals.destroy');
    Route::post('/meals/{meal}/request-correction', [MealController::class, 'requestCorrection'])->name('meals.requestCorrection');
    
     Route::post('/meals/{meal}/suggest-dishes', [MealController::class, 'suggestDishes'])->name('meals.suggestDishes');
});

Route::get('/meals/public/{uuid}', [MealController::class, 'showPublic'])->name('meals.public');



Route::get('/admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::post('/admin/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware(['auth:admin', 'admin'])->group(function () {
    
     Route::get('/foods', [FoodController::class, 'index'])->name('admin.foods.index');
    Route::get('/foods/{food}', [FoodController::class, 'show'])->name('admin.foods.show');
    Route::post('/foods', [FoodController::class, 'store'])->name('admin.foods.store');
    Route::delete('/foods/{food}', [FoodController::class, 'destroy'])->name('admin.foods.destroy');
    Route::get('/dashboard', [UserController::class, 'index'])->name('admin.dashboard');
    Route::post('/users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('/users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{user}/email', [UserController::class, 'sendEmail'])->name('users.email');
    Route::post('/users/email-all', [UserController::class, 'sendEmailToAll'])->name('users.email-all');
    Route::get('/coupons', [UserController::class, 'couponsIndex'])->name('admin.coupons.index');
    Route::get('/coupons/create', [UserController::class, 'couponsCreate'])->name('admin.coupons.create');
    Route::post('/coupons', [UserController::class, 'couponsStore'])->name('admin.coupons.store');
    Route::get('/coupons/{coupon}/edit', [UserController::class, 'couponsEdit'])->name('admin.coupons.edit');
    Route::put('/coupons/{coupon}', [UserController::class, 'couponsUpdate'])->name('admin.coupons.update');
    Route::patch('/coupons/{coupon}/toggle', [UserController::class, 'couponsToggleStatus'])->name('admin.coupons.toggle');
    Route::get('/shares', [UserController::class, 'sharesIndex'])->name('admin.shares.index');
    Route::post('/shares/{meal}/approve', [UserController::class, 'approveShare'])->name('admin.shares.approve');
});


    Route::post('users/{user}/suspend', [UserController::class, 'suspend'])->name('users.suspend');
    Route::post('users/{user}/unsuspend', [UserController::class, 'unsuspend'])->name('users.unsuspend');
    Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('users/{user}/email', [UserController::class, 'sendEmail'])->name('users.email');
    
    Route::post('users/email-all', [UserController::class, 'sendEmailToAll'])->name('users.email-all');






    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/onboard', [ProfileController::class, 'onboard'])->name('profile.onboard');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    


use App\Http\Controllers\SetupController;
Route::middleware('auth')->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup.index');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
    Route::get('/settings', [SetupController::class, 'settings'])->name('settings');
    Route::post('/profile', [SetupController::class, 'store'])->name('profile.onboard');
    Route::patch('/profile', [SetupController::class, 'update'])->name('profile.update');
    Route::get('/subscription/upgrade', [SetupController::class, 'upgrade'])->name('subscription.upgrade');
    
    Route::get('/checkout/success', [SetupController::class, 'checkoutSuccess'])->name('checkout.success');
});


Route::post('/stripe/webhook', [\App\Http\Controllers\WebhookController::class, 'handleWebhook'])->name('cashier.webhook');
require __DIR__.'/auth.php';