<?php

use App\Http\Controllers\API\PhotoAnalysisController;
use Illuminate\Support\Facades\Route;

Route::post('/seller/register', [PhotoAnalysisController::class, 'register'])->name('seller.register');
Route::post('/seller/login', [PhotoAnalysisController::class, 'login'])->name('seller.login');

Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    Route::post('/seller/analyze-photo', [PhotoAnalysisController::class, 'analyzePhoto'])->name('seller.analyze-photo');
    Route::post('/seller/analyze-leftover', [PhotoAnalysisController::class, 'analyzeLeftoverPhoto'])->name('seller.analyze-leftover');
    Route::get('/seller/analysis-results/{id}', [PhotoAnalysisController::class, 'getResults'])->name('seller.analysis-results');
});