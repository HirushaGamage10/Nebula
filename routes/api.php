<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CourseRegistraionController;
use App\Http\Controllers\CourseManagementController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\ProgramAdminL2DashboardController;
use App\Models\Intake;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group.
|
*/

Route::middleware('auth:sanctum')->group(function () {
    // Current authenticated user
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // ========================================================================
    // READ-ONLY DASHBOARD & LOOKUP ROUTES
    // ========================================================================
    Route::get('/revenue-data', [DashboardController::class, 'getRevenueData']);
    Route::get('/monthly-earnings', [DashboardController::class, 'getMonthlyEarnings']);
    Route::get('/registration-data', [DashboardController::class, 'getRegistrationData']);
    Route::get('/courses', [DashboardController::class, 'getCourses']);
    Route::get('/course-revenue/{courseId}', [DashboardController::class, 'getRevenueByCourse']);

    // Student and Course lookups
    Route::get('/students/{nic}', [CourseRegistraionController::class, 'getStudentByNic']);
    Route::get('/intakes/{courseId}', [CourseRegistraionController::class, 'getIntakesByCourse']);
    Route::get('/courses-by-location/{location}', [CourseRegistraionController::class, 'getCoursesByLocation']);
    Route::get('/courses/{id}', [CourseManagementController::class, 'getCourseById']);
    Route::get('/intakes-by-course/{courseId}', function ($courseId) {
        if (!$courseId) return response()->json([]);
        return Intake::where('course_id', $courseId)
            ->select('intake_id', 'batch', 'location')
            ->orderBy('batch')
            ->get();
    });

    Route::get('/debug-data', [AttendanceController::class, 'debugData'])->middleware('restrict.debug');

    // Method not allowed handler for GET course-registration
    Route::get('/course-registration', function () {
        return response()->json([
            'success' => false,
            'message' => 'This endpoint accepts POST requests only. Use POST to submit a course registration.'
        ], 405);
    });

    // ========================================================================
    // MUTATION ROUTES (Protected by Specific Roles)
    // ========================================================================
    // Course registration submission
    Route::post('/course-registration', [CourseRegistraionController::class, 'storeCourseRegistrationAPI'])
        ->middleware('role:Developer,DGM,Program Administrator (level 01),Program Administrator (level 02),Program Administrator (level 02) Trainee,Student Counselor,Student Counselor Trainee');

    // Course management mutations
    Route::post('/courses/update/{id}', [CourseManagementController::class, 'updateCourseData'])
        ->middleware('role:Developer,DGM,Program Administrator (level 01),Program Administrator (level 02)');

    Route::delete('/courses/{id}', [CourseManagementController::class, 'deleteCourse'])
        ->middleware('role:Developer,DGM,Program Administrator (level 01)');
});
