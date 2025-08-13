<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
});

Route::group(['middleware' => 'auth:api', 'prefix' => 'dashboard'], function () {
    Route::get('gender-distribution', [DashboardController::class, 'getGenderDistribution']);
    Route::get('age-distribution', [DashboardController::class, 'getAgeDistribution']);
    Route::get('city-distribution', [DashboardController::class, 'getCityDistribution']);
    Route::get('education-distribution', [DashboardController::class, 'getEducationDistribution']);
    Route::get('experience-pay-correlation', [DashboardController::class, 'getExperiencePayCorrelation']);
    Route::get('benched-history', [DashboardController::class, 'getBenchedHistory']);
    Route::get('leave-prediction', [DashboardController::class, 'getLeaveOrNotPrediction']);
    Route::get('search-profiles', [DashboardController::class, 'searchProfiles']);
    Route::post('export-report', [DashboardController::class, 'exportReport']);
    Route::get('employees', [DashboardController::class, 'getEmployees']);
    Route::get('filter-options', [DashboardController::class, 'getFilterOptions']);
    Route::post('employees', [DashboardController::class, 'storeEmployee']);    
});
