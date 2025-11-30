<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CourseController;
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

    // Batch and course creation (controllers enforce admin)
    Route::post('scholarships/{scholarship}/batches', [BatchController::class, 'store']);
    Route::post('batches/{batch}/courses', [CourseController::class, 'store']);

    // Courses nested under batches
Route::get('batches/{batch}/courses', [\App\Http\Controllers\CourseController::class, 'index']);
Route::post('batches/{batch}/courses', [\App\Http\Controllers\CourseController::class, 'store']);
Route::get('batches/{batch}/courses/{course}', [\App\Http\Controllers\CourseController::class, 'show']);
Route::put('batches/{batch}/courses/{course}', [\App\Http\Controllers\CourseController::class, 'update']);
Route::delete('batches/{batch}/courses/{course}', [\App\Http\Controllers\CourseController::class, 'destroy']);

    // Application status update (admin-only in controller)
    Route::patch('applications/{application}/status', [ApplicationController::class, 'updateStatus']);

    // Student-only actions (controller enforces student)
    Route::post('applications', [ApplicationController::class, 'store']);
});
