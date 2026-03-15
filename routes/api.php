<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Course Endpoints
    Route::get('/courses', [\App\Http\Controllers\CourseController::class, 'index']);
    Route::get('/courses/enrolled', [\App\Http\Controllers\CourseController::class, 'enrolledCourses'])->middleware('role:student');
    Route::get('/courses/{id}', [\App\Http\Controllers\CourseController::class, 'show']);
    
    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/courses', [\App\Http\Controllers\CourseController::class, 'store']);
        Route::put('/courses/{id}', [\App\Http\Controllers\CourseController::class, 'update']);
        Route::delete('/courses/{id}', [\App\Http\Controllers\CourseController::class, 'destroy']);
    });

    // Student only
    Route::post('/courses/{id}/enroll', [\App\Http\Controllers\CourseController::class, 'enroll'])->middleware('role:student');

    // Teacher only
    Route::get('/courses/{id}/students', [\App\Http\Controllers\CourseController::class, 'students'])->middleware('role:teacher');

    // Teacher only Quizzes
    Route::middleware('role:teacher')->group(function () {
        Route::post('/courses/{id}/quizzes', [\App\Http\Controllers\QuizController::class, 'storeQuiz']);
        Route::post('/quizzes/{id}/questions', [\App\Http\Controllers\QuizController::class, 'storeQuestion']);
    });

    // Student only Quiz Submissions
    Route::post('/quizzes/{id}/submit', [\App\Http\Controllers\QuizController::class, 'submitQuiz'])->middleware('role:student');

    // Admin Reports
    Route::get('/reports/courses', [\App\Http\Controllers\ReportController::class, 'coursesReport'])->middleware('role:admin');
});
