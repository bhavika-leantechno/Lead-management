<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\OtpVerificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Api\LeadController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Authenticated User Info
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// User Authentication Routes
Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->post('/change-password', [UserController::class, 'changePassword']);

// Password Recovery Routes
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpVerificationController::class, 'verifyOtp']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Lead Management API Routes

Route::prefix('leads')->group(function () {
    // Step 1: Lead info (Level 1)
    Route::post('/level-1', [LeadController::class, 'levelOne']);

    // Step 2: Additional details (Level 2)
    Route::post('/level-2', [LeadController::class, 'levelTwo']);

    // Step 3: File uploads and finalization (Level 3)
    Route::post('/level-3', [LeadController::class, 'levelThree']);

    // Get all leads (Admin access)
    Route::get('/', [LeadController::class, 'getLeads']);

    // Get leads by level
    Route::get('/level/{level}', [LeadController::class, 'getLeadsByLevel']);
});
