<?php

// use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ApplicationController;
// use App\Http\Controllers\ScholarshipController;
// use App\Http\Controllers\DashboardController;

// Authentication
// Route::post('/register', [AuthController::class, 'register']);
// Route::post('/login', [AuthController::class, 'login']);

// // Protected Routes (Need Token)
// Route::middleware('auth:sanctum')->group(function () {
//     Route::post('/logout', [AuthController::class, 'logout']);
//     Route::get('/dashboard', [DashboardController::class, 'index']);
//     Route::apiResource('applications', ApplicationController::class);
//     Route::apiResource('scholarships', ScholarshipController::class);

//     Route::get('/user', [AuthController::class, 'user']);
//     Route::post('/logout', [AuthController::class, 'logout']);

//     Route::post('/login', [AuthController::class, 'login']);
//     Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);
//     Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

//     // Applications
//     Route::get('/applications', [ApplicationController::class, 'index']);
//     Route::post('/apply', [ApplicationController::class, 'store']);
//     Route::post('/applications/{id}/approve', [ApplicationController::class, 'approve']);
//     Route::post('/applications/{id}/reject', [ApplicationController::class, 'reject']);

//     // Scholarships
//     Route::get('/scholarships', [ScholarshipController::class, 'index']);

//     // Dashboard stats
//     Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
// });




use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ApplicationController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Protected Routes (Login Required)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // User
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/upload-profile-image', [AuthController::class, 'uploadProfileImage']);

    /*
    |--------------------------------------------------------------------------
    | Scholarships
    |--------------------------------------------------------------------------
    */
    Route::get('/scholarships', [ScholarshipController::class, 'index']);
    Route::post('/scholarships', [ScholarshipController::class, 'store']);
    Route::put('/scholarships/{id}', [ScholarshipController::class, 'update']);
    Route::delete('/scholarships/{id}', [ScholarshipController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Batches (Inside a Scholarship)
    |--------------------------------------------------------------------------
    */
    Route::get('/scholarships/{scholarshipId}/batches', [BatchController::class, 'indexByScholarship']);
    Route::post('/scholarships/{scholarshipId}/batches', [BatchController::class, 'store']);
    Route::put('/batches/{id}', [BatchController::class, 'update']);
    Route::delete('/batches/{id}', [BatchController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Courses (Inside a Batch)
    |--------------------------------------------------------------------------
    */
    Route::get('/batches/{batchId}/courses', [CourseController::class, 'indexByBatch']);
    Route::post('/batches/{batchId}/courses', [CourseController::class, 'store']);
    Route::put('/courses/{id}', [CourseController::class, 'update']);
    Route::delete('/courses/{id}', [CourseController::class, 'destroy']);

    /*
    |--------------------------------------------------------------------------
    | Applications (Student Applies)
    |--------------------------------------------------------------------------
    */
    Route::post('/apply', [ApplicationController::class, 'store']);

    /*
    |--------------------------------------------------------------------------
    | Admin View Applications per Course
    |--------------------------------------------------------------------------
    */
    Route::get('/courses/{course}/applications', [ApplicationController::class, 'indexByCourse']);

    /*
    |--------------------------------------------------------------------------
    | Approve / Reject Applications
    |--------------------------------------------------------------------------
    */
    Route::put('/applications/{application}/status', [ApplicationController::class, 'updateStatus']);

    /*
    |--------------------------------------------------------------------------
    | Student's Own Applications
    |--------------------------------------------------------------------------
    */
    Route::get('/my-applications', [ApplicationController::class, 'myApplications']);
});
