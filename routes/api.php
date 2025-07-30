<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseRegistraionController;
use App\Http\Controllers\CourseManagementController;
use App\Http\Controllers\AttendanceController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Dashboard controller / dashboard.blade api routes for the apex charts

Route::get('/revenue-data', [DashboardController::class, 'getRevenueData']); // Revenue Overview Chart
Route::get('/monthly-earnings', [DashboardController::class, 'getMonthlyEarnings']); //Monthly Earnings Chart
Route::get('/registration-data', [DashboardController::class, 'getRegistrationData']);// Student Registration data chart
Route::get('/courses', [DashboardController::class, 'getCourses']);
Route::get('/course-revenue/{courseId}', [DashboardController::class, 'getRevenueByCourse']);

// Course Registration API Routes
Route::get('/students/{nic}', [CourseRegistraionController::class, 'getStudentByNic']);
Route::get('/intakes/{courseId}', [CourseRegistraionController::class, 'getIntakesByCourse']);
Route::post('/course-registration', [CourseRegistraionController::class, 'storeCourseRegistrationAPI']);

Route::get('/courses-by-location/{location}', [CourseRegistraionController::class, 'getCoursesByLocation']);

Route::get('/courses/{id}', [CourseManagementController::class, 'getCourseById']);
Route::post('/courses/update/{id}', [CourseManagementController::class, 'updateCourseData']);
Route::delete('/courses/{id}', [CourseManagementController::class, 'deleteCourse']);

Route::get('/debug-data', [AttendanceController::class, 'debugData']);
