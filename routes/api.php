<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForgotPasswordController;
use App\Http\Controllers\OtpVerificationController;
use App\Http\Controllers\ResetPasswordController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\ImageController;


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

Route::post('upload-multiple-images', [ImageController::class, 'uploadMultipleImages']);

// User Authentication Routes
Route::post('/register', [UserController::class, 'create']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:api')->post('/change-password', [UserController::class, 'changePassword']);
Route::post('/signup', [AuthController::class, 'signup']);

// Password Recovery Routes
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendOtp']);
Route::post('/verify-otp', [OtpVerificationController::class, 'verifyOtp']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

// Lead Management API Routes
// Lead Management API Routes
Route::middleware('auth:api')->prefix('leads')->group(function () {
    // Step 1: Lead info (Level 1)
    Route::post('/level-1', [LeadController::class, 'levelOne']);

    // Step 2: Additional details (Level 2)
    Route::post('/level-2', [LeadController::class, 'levelTwo']);

    // Step 3: File uploads and finalization (Level 3)
    Route::post('/level-3', [LeadController::class, 'levelThree']);

    // Get leads by level
    Route::post('/level', [LeadController::class, 'getLeadsByLevel']);

    // Create a new lead
    Route::post('/create', [LeadController::class, 'createLead']);

    // Get all leads (Admin access)
    Route::get('/', [LeadController::class, 'getLeads']);

    Route::get('/{id}', [LeadController::class, 'getLeadsById']);

    Route::put('/{id}/visit-update', [LeadController::class, 'updateVisit']);

    Route::put('/{id}/follow-up-update', [LeadController::class, 'updateFollowUp']);

    Route::put('/{id}/change-status', [LeadController::class, 'updateChangeStatus']);

});

// Freelancer Management API Routes
Route::middleware('auth:api')->prefix('admin')->group(function () {
    // Get all freelancers
    Route::get('/freelancers', [AdminController::class, 'getFreelancers']);

    // Approve a freelancer
    Route::put('/freelancers-approve', [AdminController::class, 'getFreelancersApprove']);

    // Get mobile services leads
    Route::get('/leads/mobile-services', [AdminController::class, 'getMobileServicesLeads']);

    // Get outsourcing leads
    Route::get('/leads/outsourcing', [AdminController::class, 'getOutsourcingLeads']);

    // Get lead details by ID
    Route::get('/leads/{id}', [AdminController::class, 'getLeadDetails']);

    // Update lead status
    Route::put('/leads/{id}/update-status', [AdminController::class, 'updateLeadStatus']);

    Route::put('/leads/activity-log', [AdminController::class, 'getActivityLog']);

});

// Agent Management API Routes
Route::middleware('auth:api')->prefix('admin/agents')->group(function () {
    // Create a new agent
    Route::post('/create-agent', [AdminController::class, 'createAgent']);

    // List all agents
    Route::get('/', [AdminController::class, 'getAgents']);

    // View agent details by ID
    Route::get('/{id}', [AdminController::class, 'viewAgent']);

    // Edit agent details by ID
    Route::put('/{id}', [AdminController::class, 'editAgent']);

    // Delete agent by ID
    Route::delete('/{id}', [AdminController::class, 'deleteAgent']);
});


Route::middleware('auth:api')->prefix('admin/plans')->group(function () {
    // Create a new plans
    Route::post('/create', [PlanController::class, 'createPlans']);

    // List all plans
    Route::get('/', [PlanController::class, 'getPlans']);

    // View plans details by ID
    Route::get('/{id}', [PlanController::class, 'viewPlans']);

    // Edit plans details by ID
    Route::put('/{id}', [PlanController::class, 'editPlans']);

    // Delete plans by ID
    Route::delete('/{id}', [PlanController::class, 'deletePlans']);
});
