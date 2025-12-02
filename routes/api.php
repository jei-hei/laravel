<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\ApplicationController;

// Authentication
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::middleware('auth:sanctum')->post('logout', [AuthController::class, 'logout']);

// Public Scholarship Access
Route::get('scholarships', [ScholarshipController::class, 'index']);
Route::get('scholarships/{scholarship}', [ScholarshipController::class, 'show']);

// Protected routes (auth only). Controllers enforce role checks.
Route::middleware(['auth:sanctum'])->group(function () {
    // Scholarship management (admin-only enforced in controller)
    Route::post('scholarships', [ScholarshipController::class, 'store']);
    Route::put('scholarships/{scholarship}', [ScholarshipController::class, 'update']);
    Route::delete('scholarships/{scholarship}', [ScholarshipController::class, 'destroy']);

    // Application status update (admin-only in controller)
    Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);

    // Student-only actions (controller enforces student)
    Route::post('applications', [ApplicationController::class, 'store']);

    // Student: view my applications
    Route::get('applications/me', [ApplicationController::class, 'myApplications']);

    // Admin: list applicants for a scholarship
    Route::get('scholarships/{scholarship}/applications', [ApplicationController::class, 'listApplicants']);
});
