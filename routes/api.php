<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ScholarshipController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\ApplicationController;



Route::post('register',[AuthController::class,'register']);
Route::post('login',[AuthController::class,'login']);
Route::middleware('auth:sanctum')->post('logout',[AuthController::class,'logout']);

Route::get('scholarships',[ScholarshipController::class,'index']);
Route::get('scholarships/{scholarship}',[ScholarshipController::class,'show']);

Route::middleware(['auth:sanctum','role:admin'])->group(function(){
    Route::post('scholarships',[ScholarshipController::class,'store']);
    Route::post('/batches', [BatchController::class,'store']);
    Route::put('scholarships/{scholarship}',[ScholarshipController::class,'update']);
    Route::delete('scholarships/{scholarship}',[ScholarshipController::class,'destroy']);
    Route::post('scholarships/{scholarship}/batches',[BatchController::class,'store']);
    Route::post('batches/{batch}/courses',[CourseController::class,'store']);
    Route::patch('applications/{application}/status',[ApplicationController::class,'updateStatus']);
});

Route::middleware(['auth:sanctum','role:student'])->group(function(){
    Route::post('applications',[ApplicationController::class,'store']);
});
