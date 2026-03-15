<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\QuizController;
use App\Http\Controllers\ReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Course Endpoints
    Route::get('/courses', [CourseController::class, 'index']);
    Route::get('/courses/enrolled', [CourseController::class, 'enrolledCourses'])->middleware('role:student');
    Route::get('/courses/{id}', [CourseController::class, 'show']);

    // Admin only
    Route::middleware('role:admin')->group(function () {
        Route::post('/courses', [CourseController::class, 'store']);
        Route::put('/courses/{id}', [CourseController::class, 'update']);
        Route::delete('/courses/{id}', [CourseController::class, 'destroy']);
    });

    // Student only
    Route::post('/courses/{id}/enroll', [CourseController::class, 'enroll'])->middleware('role:student');

    // Teacher only
    Route::get('/courses/{id}/students', [CourseController::class, 'students'])->middleware('role:teacher');

    // Teacher only Quizzes
    Route::middleware('role:teacher')->group(function () {
        Route::post('/courses/{id}/quizzes', [QuizController::class, 'storeQuiz']);
        Route::post('/quizzes/{id}/questions', [QuizController::class, 'storeQuestion']);
    });

    // Student only Quiz Submissions
    Route::post('/quizzes/{id}/submit', [QuizController::class, 'submitQuiz'])->middleware('role:student');

    // Admin Reports
    Route::get('/reports/courses', [ReportController::class, 'coursesReport'])->middleware('role:admin');
});
