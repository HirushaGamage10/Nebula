<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AllClearanceController,
    AttendanceController,
    UserProfileController,
    CourseManagementController,
    CourseRegistraionController,
    DashboardController,
    DataExportImportController,
    EligibilityCheckingAndRegistrationController,
    FileManagementController,
    HomeHostalController,
    HomeLibraryController,
    HomeProjectController,
    HostelClearanceController,
    IntakeCreationController,
    LoginController,
    LogoutController,
    LibraryClearanceController,
    ModuleManagementController,
    ReportingController,
    RepeatStudentsController,
    SpreadsheetController,
    StudentClearanceFormManagementController,
    ExamResultController,
    StudentListController,
    StudentOtherInformationController,
    StudentProfileController,
    StudentRegistraionController,
    TimetableController,
    ModuleCreditsController,
    ProjectClearanceController,
    AcademicDetailsController,
    ModuleCreationController,
    SemesterCreationController,
    SpecialApprovalController,
    UhIndexController,
    PaymentDiscountController,
    PaymentController,
    LatePaymentController,
};

// Default
Route::redirect('/', 'login');

// Route for showing the login page
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');

// Route for handling the login form submission
Route::post('/login', [LoginController::class, 'authenticate'])->name('login.authenticate');

// Route for the spreadsheet section, accessible without authentication
Route::get('/spreadsheet', [SpreadSheetController::class, 'showSpreadsheet'])->name('spreadsheet.section');
Route::post('/store-attendance', [SpreadSheetController::class, 'storeAttendance'])->name('store.attendance');


Route::middleware(['auth', 'prevent-back-history'])->group(function () {

     // Dashboard Page - All authenticated users can access
     Route::get('/dashboard', [DashboardController::class, 'showDashboard'])->name('dashboard');
     
     // Student Registration Routes - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
     Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
         Route::get('/student/register', [StudentRegistraionController::class, 'showStudentRegistration'])->name('student.registration');
         Route::post('/student/register', [StudentRegistraionController::class, 'register'])->name('student.register');
         Route::get('/student/subjects/{examTypeId}', [StudentRegistraionController::class, 'getSubjectsByExamType']);
         Route::get('/student/streams/{examTypeId}', [StudentRegistraionController::class, 'getStreamsByExamType']);
     });

     // Dashboard controller / dashboard.blade api routes for the apex charts
     Route::get('/yearly-revenue', [DashboardController::class, 'getYearlyRevenue']);// Yearly Revenue Breakup Chart
     Route::get('/monthly-earnings', [DashboardController::class, 'getMonthlyEarnings']); //Monthly Earnings Chart
     Route::get('/students-per-course', [DashboardController::class, 'getStudentsPerCourse']); // Student Per Course Chart
     Route::get('/marketing-survey-country-reg', [DashboardController::class, 'getCountrySurveyData']);// Marketing Survey Chart
     Route::get('/dropdown-options', [DashboardController::class, 'getDropdownOptions']);// Student Registration data chart drop down menus
     Route::get('/registration-data', [DashboardController::class, 'getRegistrationData']);// Student Registration data chart
     Route::get('/courses', [DashboardController::class, 'getCourses']);
     Route::get('/course-revenue/{courseId}', [DashboardController::class, 'getRevenueByCourse']);
     Route::get('/revenue-data', [DashboardController::class, 'getRevenueData']); // Revenue Overview Chart


    // Logout
    Route::get('/logout', [LogoutController::class, 'logout'])->name('logout');

    // User Page - All authenticated users can access
    Route::get('/user', [UserProfileController::class, 'showUserProfile'])->name('user.profile');
    Route::post('/user/change-password', [UserProfileController::class, 'changePassword'])->name('user.changePassword');
    
    // User management - Only DGM, Program Administrator (level 01), and Developer can create/update/delete users
    Route::middleware(['role:DGM,Program Administrator (level 01),Developer'])->group(function () {
        Route::get('/user/create', [UserProfileController::class, 'showCreateUserForm'])->name('create.user');
        Route::post('/user/create', [UserProfileController::class, 'createUser'])->name('user.create');
        Route::post('/user/update-status', [UserProfileController::class, 'updateUserStatus'])->name('user.updateStatus');
        Route::post('/user/delete', [UserProfileController::class, 'deleteUser'])->name('user.delete');
        Route::post('/user/get-details', [UserProfileController::class, 'getUserDetails'])->name('user.getDetails');
        Route::post('/user/reset-password', [UserProfileController::class, 'resetPassword'])->name('user.resetPassword');
    });
   
    // Remove DGM user management create user routes (user.create) from the DGM user management context
    // Only keep user listing, edit, and delete routes for this page

    // User Management - Only Program Administrator (level 01) and Developer can access
    Route::middleware(['role:Program Administrator (level 01),Developer'])->group(function () {
        Route::get('/dgm-user-management', [UserProfileController::class, 'showDGMUserManagement'])->name('dgm.user.management');
    });

    // Intake Creation Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        Route::get('/intake-creation', [IntakeCreationController::class, 'create'])->name('intake.create');
        Route::post('/intake-creation', [IntakeCreationController::class, 'store'])->name('intake.store');
    });

    // Course Management Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        Route::get('/course-management', [CourseManagementController::class, 'showCourseManagement'])->name('course.management');
        Route::post('/store-course-data', [CourseManagementController::class, 'storeCourseData'])->name('course.store');
    });

    // Course Registration Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/course-registration', [CourseRegistraionController::class, 'showCourseRegistration'])->name('course.registration');
        Route::post('/check-student-exists', [CourseRegistraionController::class, 'checkStudentExists'])->name('check.student.exists');
        Route::post('/store-course-registration', [CourseRegistraionController::class, 'storeCourseRegistration'])->name('store.course.registration');
        Route::post('/check-students', [CourseRegistraionController::class, 'checkStudents'])->name('check.students');
        Route::post('/batch-dropdown-options', [CourseRegistraionController::class, 'batchDropdownOptions'])->name('batch.dropdown.options');
        Route::get('/check-blacklist-status', [CourseRegistraionController::class, 'checkBlacklistStatus']);
    });


    // Eligibility Checking & Registration Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/eligibility-registration', [EligibilityCheckingAndRegistrationController::class, 'showEligibilityRegistration'])->name('eligibility.registration');
        Route::get('/get-courses-by-location', [EligibilityCheckingAndRegistrationController::class, 'getCoursesByLocation']);
        Route::get('/get-intakes/{courseId}/{location}', [EligibilityCheckingAndRegistrationController::class, 'getIntakesForCourseAndLocation']);
        Route::post('/get-eligible-students', [EligibilityCheckingAndRegistrationController::class, 'getEligibleStudents']);
        Route::post('/get-student-data', [EligibilityCheckingAndRegistrationController::class, 'getStudentData'])->name('student.data');
        Route::post('/verify-eligibility', [EligibilityCheckingAndRegistrationController::class, 'verifyEligibility'])->name('verify.eligibility');
        Route::post('/check-approval', [EligibilityCheckingAndRegistrationController::class, 'checkApproval'])->name('check.approval');
        Route::get('/get-registered-courses-by-nic', [EligibilityCheckingAndRegistrationController::class, 'getRegisteredCoursesByNic']);
        Route::post('/get-eligible-students-by-nic', [EligibilityCheckingAndRegistrationController::class, 'getEligibleStudentsByNic']);
        Route::post('/get-student-exam-details-by-nic-course', [EligibilityCheckingAndRegistrationController::class, 'getStudentExamDetailsByNicCourse']);
        Route::get('/get-course-entry-qualification', [
            App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
            'getCourseEntryQualification',
        ]);
        Route::get('/get-next-course-registration-id', [EligibilityCheckingAndRegistrationController::class, 'getNextCourseRegistrationId'])->name('get.next.course.registration.id');
    });

    // Module Management Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        Route::get('/module-management', [ModuleManagementController::class, 'showModuleManagement'])->name('module.management');
        
        // Module Management API routes
        Route::post('/module-management/get-intakes', [ModuleManagementController::class, 'getIntakes'])->name('module.management.getIntakes');
        Route::post('/module-management/get-students', [ModuleManagementController::class, 'getStudents'])->name('module.management.getStudents');
        Route::post('/module-management/get-modules', [ModuleManagementController::class, 'getModules'])->name('module.management.getModules');
        Route::post('/module-management/get-assignments', [ModuleManagementController::class, 'getModuleAssignments'])->name('module.management.getAssignments');
        Route::post('/module-management/assign-modules', [ModuleManagementController::class, 'assignModules'])->name('module.management.assignModules');
        Route::post('/module-management/remove-assignment', [ModuleManagementController::class, 'removeAssignment'])->name('module.management.removeAssignment');
        Route::post('/module-management/get-statistics', [ModuleManagementController::class, 'getModuleStatistics'])->name('module.management.getStatistics');

        // Elective Module Registration routes
        Route::post('/module-management/get-ongoing-semesters', [ModuleManagementController::class, 'getOngoingSemesters'])->name('module.management.getOngoingSemesters');
        Route::post('/module-management/get-elective-modules', [ModuleManagementController::class, 'getElectiveModules'])->name('module.management.getElectiveModules');
        Route::post('/module-management/get-elective-students', [ModuleManagementController::class, 'getElectiveStudents'])->name('module.management.getElectiveStudents');
        Route::post('/module-management/register-elective-modules', [ModuleManagementController::class, 'registerElectiveModules'])->name('module.management.registerElectiveModules');
        Route::post('/module-management/get-elective-registrations', [ModuleManagementController::class, 'getElectiveRegistrations'])->name('module.management.getElectiveRegistrations');

        // Module Creation page
        Route::get('/module-creation', [ModuleCreationController::class, 'create'])->name('module.creation');
        Route::post('/module-store', [ModuleCreationController::class, 'store'])->name('module.store');
        Route::patch('/modules/{id}', [ModuleCreationController::class, 'update']);
    });

    // File Management - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        // File upload routes
        Route::post('/file/upload', [FileManagementController::class, 'uploadFile'])->name('file.upload');
        Route::post('/file/upload-multiple', [FileManagementController::class, 'uploadMultipleFiles'])->name('file.uploadMultiple');
        Route::get('/file/download', [FileManagementController::class, 'downloadFile'])->name('file.download');
        
        // File management routes
        Route::delete('/file/delete', [FileManagementController::class, 'deleteFile'])->name('file.delete');
        Route::delete('/file/delete-multiple', [FileManagementController::class, 'deleteMultipleFiles'])->name('file.deleteMultiple');
        Route::get('/file/info', [FileManagementController::class, 'getFileInfo'])->name('file.info');
        Route::get('/file/list', [FileManagementController::class, 'listFiles'])->name('file.list');
        
        // Storage management routes
        Route::get('/file/storage-stats', [FileManagementController::class, 'getStorageStats'])->name('file.storageStats');
        Route::post('/file/cleanup', [FileManagementController::class, 'cleanupOrphanedFiles'])->name('file.cleanup');
    });

    // Reporting System - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        // Reporting dashboard
        Route::get('/reporting', [ReportingController::class, 'showReportingDashboard'])->name('reporting.dashboard');
        
        // Report generation routes
        Route::post('/reporting/enrollment', [ReportingController::class, 'generateStudentEnrollmentReport'])->name('reporting.enrollment');
        Route::post('/reporting/performance', [ReportingController::class, 'generateCoursePerformanceReport'])->name('reporting.performance');
        Route::post('/reporting/attendance', [ReportingController::class, 'generateAttendanceReport'])->name('reporting.attendance');
        Route::post('/reporting/financial', [ReportingController::class, 'generateFinancialReport'])->name('reporting.financial');
        Route::post('/reporting/module', [ReportingController::class, 'generateModuleAssignmentReport'])->name('reporting.module');
        
        // Report export routes
        Route::post('/reporting/export', [ReportingController::class, 'exportReport'])->name('reporting.export');
    });



    // Data Export/Import - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
        // Dashboard
        Route::get('/data-export-import', [DataExportImportController::class, 'showDashboard'])->name('data.export.import');
        
        // Export routes
        Route::post('/data-export/students', [DataExportImportController::class, 'exportStudents'])->name('data.export.students');
        Route::post('/data-export/courses', [DataExportImportController::class, 'exportCourses'])->name('data.export.courses');
        Route::post('/data-export/attendance', [DataExportImportController::class, 'exportAttendance'])->name('data.export.attendance');
        Route::post('/data-export/exam-results', [DataExportImportController::class, 'exportExamResults'])->name('data.export.examResults');
        
        // Import routes
        Route::post('/data-import/students', [DataExportImportController::class, 'importStudents'])->name('data.import.students');
        
        // Template routes
        Route::get('/data-import/template', [DataExportImportController::class, 'getImportTemplate'])->name('data.import.template');
        
        // Statistics
        Route::get('/data-export/stats', [DataExportImportController::class, 'getExportStats'])->name('data.export.stats');
    });

    // Attendance Management Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Bursar, Project Tutor, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Bursar,Project Tutor,Marketing Manager,Developer'])->group(function () {
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
        Route::get('/get-courses-by-location', [AttendanceController::class, 'getCoursesByLocation'])->name('get.courses.by.location');
        Route::get('/get-intakes/{courseId}/{location}', [AttendanceController::class, 'getIntakesForCourseAndLocation'])->name('get.intakes.for.course.location');
        Route::get('/attendance/get-semesters', [AttendanceController::class, 'getSemesters'])->name('attendance.get.semesters');
        Route::post('/get-filtered-modules', [AttendanceController::class, 'getFilteredModules'])->name('get.filtered.modules');
        Route::post('/semester/get-filtered-modules', [SemesterCreationController::class, 'getFilteredModules'])->name('semester.get.filtered.modules');
        Route::post('/get-students-for-attendance', [AttendanceController::class, 'getStudentsForAttendance'])->name('get.students.for.attendance');
        Route::post('/store-attendance', [AttendanceController::class, 'storeAttendance'])->name('store.attendance');
        Route::post('/get-attendance-history', [AttendanceController::class, 'getAttendanceHistory'])->name('get.attendance.history');
        Route::get('/debug-attendance-data', [AttendanceController::class, 'debugData'])->name('debug.attendance.data'); // Debug route

        // Redirect old attendance management URL to new one
        Route::redirect('/student-attendance-management', '/attendance');
    });

     //clearance start
    //library clearance - Librarian, DGM, Program Administrator (level 01), Developer
    Route::middleware(['role:Librarian,DGM,Program Administrator (level 01),Developer'])->group(function () {
        Route::get('/student-clearance-form-management', [StudentClearanceFormManagementController::class, 'showStudentClearanceFormManagement'])->name('student.clearance.form.management');
        Route::post('/student-clearance-form-management', [StudentClearanceFormManagementController::class, 'store'])->name('library.store');
        Route::get('/student-clearance/search', [StudentClearanceFormManagementController::class, 'search'])->name('library.search');
        Route::get('/get-student-details', [StudentClearanceFormManagementController::class, 'getStudentDetails'])->name('getStudentDetails');
        Route::post('/library/update-received-date', [StudentClearanceFormManagementController::class, 'updateReceivedDate'])->name('library.updateReceivedDate');

        //library clearance
        // Route::get('/library-clearance', [HomeLibraryController::class, 'showLibraryClearance'])->name('library.clearance');
        Route::get('/library-clearance', [LibraryClearanceController::class, 'index'])->name('library.clearance');
        Route::get('/library-clearance/{id}/details', [LibraryClearanceController::class, 'details'])->name('library.clearance.details');
        Route::post('/library/approve-clearance', [LibraryClearanceController::class, 'approveClearance'])->name('library.approve.clearance');
        Route::post('/library/reject-clearance', [LibraryClearanceController::class, 'rejectClearance'])->name('library.reject.clearance');
    });

    //hostel clearance - Hostel Manager, DGM, Program Administrator (level 01), Developer
    Route::middleware(['role:Hostel Manager,DGM,Program Administrator (level 01),Developer'])->group(function () {
        Route::get('/hostel-clearance', [HostelClearanceController::class, 'showHostelClearanceFormManagement'])->name('hostel.clearance.form.management');
        Route::post('/hostel-clearance', [HostelClearanceController::class, 'store'])->name('hostel.store');
        Route::post('/hostel/update-clearance', [HostelClearanceController::class, 'updateClearance'])->name('hostel.update');
        Route::get('/search/hostel-clearance', [HostelClearanceController::class, 'search'])->name('hostel.search');
        Route::get('/get-student-details', [HostelClearanceController::class, 'getStudentDetails'])->name('getStudentDetails');
        Route::post('/hostel/approve-clearance', [HostelClearanceController::class, 'approveClearance'])->name('hostel.approve.clearance');
        Route::post('/hostel/reject-clearance', [HostelClearanceController::class, 'rejectClearance'])->name('hostel.reject.clearance');
    });

    //project clearance - Project Tutor, DGM, Program Administrator (level 01), Developer
    Route::middleware(['role:Project Tutor,DGM,Program Administrator (level 01),Developer'])->group(function () {
        Route::get('/project-clearance-form-management', [ProjectClearanceController::class, 'showProjectClearanceFormManagement'])->name('project.clearance.management');
        Route::post('/project-clearance-form-management', [ProjectClearanceController::class, 'store'])->name('project.store');
        Route::post('/project/update-clearance', [ProjectClearanceController::class, 'updateClearance'])->name('project.update');
        Route::get('/search/project-clearance', [ProjectClearanceController::class, 'search'])->name('project.search');
        Route::get('/get-student-details', [ProjectClearanceController::class, 'getStudentDetails'])->name('getStudentDetails');
        Route::post('/project/approve-clearance', [ProjectClearanceController::class, 'approveClearance'])->name('project.approve.clearance');
        Route::post('/project/reject-clearance', [ProjectClearanceController::class, 'rejectClearance'])->name('project.reject.clearance');
    });




    //all clearance - DGM, Program Administrator (level 01), Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Developer'])->group(function () {
        // Main page route
        //Route::get('/clearance', [AllClearanceController::class, 'showAllClearance'])->name('clearance.index');

        // Route to handle all search together (you can modify this logic to be shared)
        Route::get('/clearance/search', [AllClearanceController::class, 'showAllClearance'])->name('clearance.search');
        Route::get('/all-clearance', [AllClearanceController::class, 'showAllClearance'])->name('all.clearance.management');
        Route::get('/library-clearance/search', [AllClearanceController::class, 'librarysearch'])->name('library.search');
        Route::get('/hostel-clearance/search', [AllClearanceController::class, 'hostelsearch'])->name('hostel.search');
        Route::get('/project-clearance/search', [AllClearanceController::class, 'projectsearch'])->name('project.search');
    });

    // exam results - DGM, Program Administrator (level 01), Program Administrator (level 02), Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/student-exam-result-management', [ExamResultController::class, 'showStudentExamResultManagement'])->name('student.exam.result.management');
        Route::get('/exam-results-view-edit', [ExamResultController::class, 'showExamResultsViewEdit'])->name('exam.results.view.edit');
        Route::get('/get-course-data/{courseID}', [ExamResultController::class, 'getCourseData']);
        Route::post('/store/result', [ExamResultController::class, 'storeResult'])->name('store.result');
        Route::post('/update/result', [ExamResultController::class, 'updateResult'])->name('update.result');
        Route::post('/get-student-name', [ExamResultController::class, 'getStudentName'])->name('get.student.name');
        Route::get('/get-intakes/{courseID}/{location}', [ExamResultController::class, 'getIntakesForCourseAndLocation'])->name('get.intakes.for.course.location');
        Route::post('/exam-results/get-modules', [ExamResultController::class, 'getFilteredModules'])->name('exam.results.get.filtered.modules');
        Route::get('/get-semesters', [ExamResultController::class, 'getSemesters'])->name('get.semesters');
        Route::post('/get-students-for-exam-result', [ExamResultController::class, 'getStudentsForExamResult'])->name('get.students.for.exam.result');
        Route::post('/get-existing-exam-results', [ExamResultController::class, 'getExistingExamResults'])->name('get.existing.exam.results');
        Route::post('/auto-calculate-grades', [ExamResultController::class, 'autoCalculateGrades'])->name('auto.calculate.grades');
    });

    // Repeat Students Management - DGM, Program Administrator (level 01), Program Administrator (level 02), Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/repeat-students', [RepeatStudentsController::class, 'showRepeatStudentsManagement'])->name('repeat.students.management');
        Route::get('/repeat-students/get-course-data/{courseID}', [RepeatStudentsController::class, 'getCourseData']);
        Route::post('/repeat-students/get-student-name', [RepeatStudentsController::class, 'getStudentName'])->name('repeat.students.get.student.name');
        Route::post('/repeat-students/get-exam-results', [RepeatStudentsController::class, 'getRepeatStudentsForExamResults'])->name('repeat.students.get.exam.results');
        Route::post('/repeat-students/get-payments', [RepeatStudentsController::class, 'getRepeatStudentsForPayments'])->name('repeat.students.get.payments');
        Route::post('/repeat-students/update-exam-results', [RepeatStudentsController::class, 'updateExamResults'])->name('repeat.students.update.exam.results');
        Route::post('/repeat-students/update-payments', [RepeatStudentsController::class, 'updatePaymentDetails'])->name('repeat.students.update.payments');
        Route::get('/repeat-students/get-intakes/{courseID}/{location}', [RepeatStudentsController::class, 'getIntakesForCourseAndLocation'])->name('repeat.students.get.intakes.for.course.location');
        Route::post('/repeat-students/get-modules', [RepeatStudentsController::class, 'getFilteredModules'])->name('repeat.students.get.filtered.modules');
        Route::get('/repeat-students/get-semesters', [RepeatStudentsController::class, 'getSemesters'])->name('repeat.students.get.semesters');
    });


    // Student List Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/student-list', [StudentListController::class, 'showStudentList'])->name('student.list');
        Route::post('/get-student-list-data', [StudentListController::class, 'getStudentListData'])->name('student.getListData');
        Route::post('/download-student-list', [StudentListController::class, 'downloadStudentList'])->name('student.downloadList');

        // Temporary test route for semester creation debugging
        Route::post('/test-semester-creation', function(Request $request) {
            \Log::info('Test semester creation data:', $request->all());
            return response()->json(['success' => true, 'message' => 'Test route working']);
        });
    });

    // Student Other Information page - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/student-other-information', [StudentOtherInformationController::class, 'showStudentOtherInformation'])->name('student.other.information');
        Route::post('/retrieve-student-details', [StudentOtherInformationController::class, 'getStudentDetails'])->name('retrieve.student.details');
        Route::post('/store-other-informations', [StudentOtherInformationController::class, 'storeOtherInformations'])->name('store.other.informations');
    });

    // Student Profile Page - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Marketing Manager, Developer
    Route::middleware(['role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Marketing Manager,Developer'])->group(function () {
        Route::get('/student-profile/{studentId}', [StudentProfileController::class, 'showStudentProfile'])->name('student.profile');
        Route::post('/get-student-details', [StudentProfileController::class, 'getStudentDetails'])->name('student.details');
        Route::put('/student/update/{studentId}', [StudentProfileController::class, 'updatePersonalInfo'])->name('student.updatePersonalInfo');
        Route::post('/student/update-personal-info', [StudentProfileController::class, 'updatePersonalInfoAjax'])->name('student.update.personal.info');
        Route::post('/student/update-parent-info', [StudentProfileController::class, 'updateParentInfoAjax'])->name('student.update.parent.info');
        Route::get('/student/academic', [StudentProfileController::class, 'getAcademicDetails'])->name('student.academic');
        Route::get('/student/exam-results', [StudentProfileController::class, 'getExamResults'])->name('student.examResults');
        Route::post('/student/attendance', [StudentProfileController::class, 'getAttendanceDetails'])->name('student.attendance');
        Route::post('/student/clearance', [StudentProfileController::class, 'getClearanceDetails'])->name('student.clearance');
        Route::post('/student/certificates', [StudentProfileController::class, 'getCertificates'])->name('student.certificates');
        Route::get('/student/certificate/download/{id}', [StudentProfileController::class, 'downloadCertificate'])->name('student.certificate.download');
        Route::post('/student/certificate-upload', [StudentProfileController::class, 'uploadCertificate'])->name('student.certificate.upload');
        Route::get('/get-academic-details', [AcademicDetailsController::class, 'getAcademicDetails'])->name('academic.details');
        
        // Payment API routes
        Route::get('/api/student/{studentId}/course/{courseId}/intakes', [StudentProfileController::class, 'getIntakesForCourse'])->name('student.intakes.for.course');
        Route::get('/api/student/{studentId}/course/{courseId}/intake/{intake}/payment-details', [StudentProfileController::class, 'getPaymentDetails'])->name('student.payment.details');
        Route::get('/api/student/{studentId}/course/{courseId}/intake/{intake}/payment-history', [StudentProfileController::class, 'getPaymentHistory'])->name('student.payment.history');
        Route::get('/api/student/{studentId}/course/{courseId}/intake/{intake}/payment-schedule', [StudentProfileController::class, 'getPaymentSchedule'])->name('student.payment.schedule');
    });
    

    
    // Timetable Management - Program Administrator (level 02) and Developer only
    Route::get('/timetable', [TimetableController::class, 'showTimetable'])->name('timetable.show');
    Route::post('/timetable', [TimetableController::class, 'store'])->name('timetable.store');
    Route::get('/get-intakes/{courseId}/{location}', [App\Http\Controllers\TimetableController::class, 'getIntakesForCourseAndLocation']);
    Route::get('/get-courses-by-location', [TimetableController::class, 'getCoursesByLocation'])->name('timetable.courses.by.location');
    Route::get('/timetable/get-semesters', [TimetableController::class, 'getSemesters'])->name('timetable.semesters');
    Route::get('/get-weeks', [TimetableController::class, 'getWeeks'])->name('timetable.weeks');
    Route::get('/get-modules-by-semester', [TimetableController::class, 'getModulesBySemester'])->name('timetable.modules.by.semester');
    Route::post('/get-existing-timetable', [TimetableController::class, 'getExistingTimetable'])->name('timetable.get.existing');
    Route::get('/download-timetable-pdf', [TimetableController::class, 'downloadTimetablePDF'])->name('timetable.download.pdf');
});

// API routes - DGM, Program Administrator (level 01), Program Administrator (level 02), Student Counselor, Bursar, Developer
Route::middleware(['auth', 'role:DGM,Program Administrator (level 01),Program Administrator (level 02),Student Counselor,Bursar,Developer'])->group(function () {
    Route::post('/intakes/get', [CourseRegistraionController::class, 'getIntakes'])->name('intakes.get');
    Route::post('/students/find', [CourseRegistraionController::class, 'findStudent'])->name('students.find');
    Route::post('/api/course-registration', [CourseRegistraionController::class, 'storeCourseRegistrationAPI'])->name('register.course.api');
    Route::post('/api/course-registration-eligibility', [CourseRegistraionController::class, 'storeCourseRegistrationForEligibilityAPI'])->name('register.course.eligibility.api');
});

// Overall Attendance - DGM, Program Administrator (level 01), Program Administrator (level 02), Bursar, Marketing Manager, Developer
Route::middleware(['auth', 'role:DGM,Program Administrator (level 01),Program Administrator (level 02),Bursar,Marketing Manager,Developer'])->group(function () {
    Route::get('/overall-attendance', function() {
        $courses = \App\Models\Course::all(['course_id', 'course_name']);
        $intakes = \App\Models\Intake::all(['intake_id', 'batch']);
        return view('overall_attendance', compact('courses', 'intakes'));
    })->name('overall.attendance');
    Route::post('/get-overall-attendance', [\App\Http\Controllers\AttendanceController::class, 'getOverallAttendance'])->name('get.overall.attendance');
});

// Special Approval List - DGM and Developer only
Route::middleware(['auth', 'role:DGM,Developer'])->group(function () {
    Route::get('/special-approval-list', function() {
        return view('Special_approval_list');
    })->name('special.approval.list');

    Route::get('/get-special-approval-list', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'getSpecialApprovalList',
    ]);

    Route::post('/register-eligible-student', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'registerEligibleStudent',
    ]);

    Route::post('/update-dgm-comment', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'updateDgmComment',
    ]);

    Route::get('/get-course-details/{courseId}', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'getCourseDetails',
    ]);

    Route::post('/get-next-course-registration-id', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'getNextCourseRegistrationId',
    ]);

    // Temporary debug route
    Route::get('/debug-special-approval', function() {
        $reg = App\Models\CourseRegistration::where('status', 'Special approval required')->with('student')->first();
        return response()->json([
            'student_id' => $reg->student->student_id,
            'nic' => $reg->student->id_value,
            'name' => $reg->student->full_name,
        ]);
    });
});

// Special Approval Request - DGM, Student Counselor, and Developer
Route::middleware(['auth', 'role:DGM,Student Counselor,Developer,Program Administrator (level 01),Program Administrator (level 02)'])->group(function () {
    Route::post('/send-special-approval-request', [
        App\Http\Controllers\EligibilityCheckingAndRegistrationController::class,
        'sendSpecialApprovalRequest',
    ]);
    
    // Test route for debugging
    Route::post('/test-special-approval', function(Request $request) {
        return response()->json([
            'success' => true,
            'message' => 'Test route working',
            'user_role' => auth()->user()->user_role ?? 'unknown',
            'request_data' => $request->all()
        ]);
    });
});

// Semester Creation - DGM, Program Administrator (level 01), Program Administrator (level 02), Developer
Route::middleware(['auth', 'role:DGM,Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
    Route::get('semesters/create', [App\Http\Controllers\SemesterCreationController::class, 'create'])->name('semesters.create');
    Route::post('semesters', [App\Http\Controllers\SemesterCreationController::class, 'store'])->name('semesters.store');
});

// Semester Registration - Program Administrator (level 01), Program Administrator (level 02), Developer
Route::middleware(['auth', 'role:Program Administrator (level 01),Program Administrator (level 02),Developer'])->group(function () {
    Route::get('/semester-registration', [App\Http\Controllers\SemesterRegistrationController::class, 'index'])->name('semester.registration');
    Route::post('/semester-registration/store', [App\Http\Controllers\SemesterRegistrationController::class, 'store'])->name('semester.registration.store');
    Route::get('/semester-registration/get-courses-by-location', [App\Http\Controllers\SemesterRegistrationController::class, 'getCoursesByLocation'])->name('semester.registration.getCoursesByLocation');
    Route::get('/semester-registration/get-ongoing-intakes', [App\Http\Controllers\SemesterRegistrationController::class, 'getOngoingIntakes'])->name('semester.registration.getOngoingIntakes');
    Route::get('/semester-registration/get-open-semesters', [App\Http\Controllers\SemesterRegistrationController::class, 'getOpenSemesters'])->name('semester.registration.getOpenSemesters');
    Route::get('/semester-registration/get-eligible-students', [App\Http\Controllers\SemesterRegistrationController::class, 'getEligibleStudents'])->name('semester.registration.getEligibleStudents');
    Route::get('/semester-registration/get-all-semesters-for-course', [App\Http\Controllers\SemesterRegistrationController::class, 'getAllSemestersForCourse'])->name('semester.registration.getAllSemestersForCourse');
    
    // Test route for debugging semester registration
    Route::post('/test-semester-registration', function(Request $request) {
        \Log::info('Test semester registration data:', $request->all());
        return response()->json([
            'success' => true,
            'message' => 'Test route working',
            'data' => $request->all()
        ]);
    });
});

// Payment Plan - Marketing Manager and Developer only
Route::middleware(['auth', 'role:Marketing Manager,Developer'])->group(function () {
    Route::get('/payment-plan', [App\Http\Controllers\PaymentPlanController::class, 'index'])->name('payment.plan');
    Route::post('/payment-plan/store', [App\Http\Controllers\PaymentPlanController::class, 'store'])->name('payment.plan.store');
});

// Intake autofill for payment plan
Route::post('/get-payment-plan-details', [App\Http\Controllers\IntakeCreationController::class, 'getPaymentPlanDetails'])->name('get.payment.plan.details');

// Payment plan autofill from intake
Route::post('/get-intake-fees', [App\Http\Controllers\PaymentPlanController::class, 'getIntakeFees'])->name('get.intake.fees');

Route::post('/special-approval-register', [SpecialApprovalController::class, 'register']);
// Special approval document download route
Route::get('/special-approval-document/{filename}', [SpecialApprovalController::class, 'downloadDocument'])->name('special.approval.document.download');
// Removed duplicate route - using EligibilityCheckingAndRegistrationController@getSpecialApprovalList instead

// Payment Clearance - Bursar and Developer only
Route::middleware(['auth', 'role:Bursar,Developer'])->group(function () {
    Route::get('/payment-clearance', [App\Http\Controllers\PaymentClearanceController::class, 'index'])->name('payment.clearance');
    Route::post('/payment/approve-clearance', [App\Http\Controllers\PaymentClearanceController::class, 'approveClearance'])->name('payment.approve.clearance');
    Route::post('/payment/reject-clearance', [App\Http\Controllers\PaymentClearanceController::class, 'rejectClearance'])->name('payment.reject.clearance');
});

// API: Get course registration history for a student
Route::get('/api/student/{studentId}/history', [App\Http\Controllers\StudentProfileController::class, 'getCourseRegistrationHistory']);
// API: Get student details by NIC (for AJAX search)
Route::get('/api/student-details-by-nic', [App\Http\Controllers\StudentProfileController::class, 'getStudentDetailsByNic']);
// API: Get course details by ID (for specialization handling)
Route::get('/api/courses/{courseId}', [App\Http\Controllers\CourseManagementController::class, 'getCourseById']);

// All Clearance AJAX routes
Route::post('/clearance/send-request', [AllClearanceController::class, 'sendClearanceRequest'])->name('clearance.sendRequest');
Route::post('/clearance/get-students-for-intake', [AllClearanceController::class, 'getStudentsForIntake'])->name('clearance.getStudentsForIntake');
Route::post('/clearance/get-intake-details', [AllClearanceController::class, 'getIntakeDetails'])->name('clearance.getIntakeDetails');

Route::get('/uh-index-numbers', [App\Http\Controllers\UhIndexController::class, 'showPage'])->name('uh.index.page');
Route::post('/uh-index/courses', [App\Http\Controllers\UhIndexController::class, 'getCoursesByLocation'])->name('uh.index.courses');
Route::post('/uh-index/intakes', [App\Http\Controllers\UhIndexController::class, 'getIntakesByCourse'])->name('uh.index.intakes');
Route::post('/uh-index/students', [App\Http\Controllers\UhIndexController::class, 'getStudentsByIntake'])->name('uh.index.students');
Route::post('/uh-index/save', [App\Http\Controllers\UhIndexController::class, 'saveUhIndexNumbers'])->name('uh.index.save');

Route::get('/payment-discount', [App\Http\Controllers\PaymentDiscountController::class, 'showPage'])->name('payment.discount.page');
Route::post('/payment-discount/courses', [App\Http\Controllers\PaymentDiscountController::class, 'getCoursesByLocation'])->name('payment.discount.courses');
Route::post('/payment-discount/intakes', [App\Http\Controllers\PaymentDiscountController::class, 'getIntakesByCourse'])->name('payment.discount.intakes');
Route::post('/payment-discount/payment-plan', [App\Http\Controllers\PaymentDiscountController::class, 'getPaymentPlan'])->name('payment.discount.paymentplan');
Route::post('/payment-discount/save-slt-loan', [App\Http\Controllers\PaymentDiscountController::class, 'saveSltLoan'])->name('payment.discount.save.sltloan');
Route::post('/payment-discount/save-discount', [App\Http\Controllers\PaymentDiscountController::class, 'saveDiscount'])->name('payment.discount.save.discount');
Route::get('/payment-discount/get-discounts', [App\Http\Controllers\PaymentDiscountController::class, 'getDiscounts'])->name('payment.discount.get.discounts');
Route::post('/payment-discount/update-discount', [App\Http\Controllers\PaymentDiscountController::class, 'updateDiscount'])->name('payment.discount.update.discount');
Route::post('/payment-discount/delete-discount', [App\Http\Controllers\PaymentDiscountController::class, 'deleteDiscount'])->name('payment.discount.delete.discount');

Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
Route::post('/payment/get-plans', [PaymentController::class, 'getPaymentPlans'])->name('payment.get.plans');
Route::post('/payment/get-student-courses', [PaymentController::class, 'getStudentCourses'])->name('payment.get.student.courses');
Route::post('/payment/create-payment-plan', [PaymentController::class, 'createPaymentPlan'])->name('payment.create.payment.plan');
Route::get('/payment/get-discounts', [PaymentController::class, 'getDiscounts'])->name('payment.get.discounts');
Route::post('/payment/get-installments', [PaymentController::class, 'getPaymentPlanInstallments'])->name('payment.get.installments');
Route::post('/payment/get-payment-details', [PaymentController::class, 'getPaymentDetails'])->name('payment.get.payment.details');
Route::post('/payment/save-plans', [PaymentController::class, 'savePaymentPlans'])->name('payment.save.plans');
Route::post('/payment/generate-slip', [PaymentController::class, 'generatePaymentSlip'])->name('payment.generate.slip');
Route::post('/payment/download-slip-pdf', [PaymentController::class, 'downloadPaymentSlipPDF'])->name('payment.download.slip.pdf');
Route::post('/payment/save-record', [PaymentController::class, 'savePaymentRecord'])->name('payment.save.record');
Route::post('/payment/get-records', [PaymentController::class, 'getPaymentRecords'])->name('payment.get.records');
Route::post('/payment/update-record', [PaymentController::class, 'updatePaymentRecord'])->name('payment.update.record');
Route::post('/payment/delete-record', [PaymentController::class, 'deletePaymentRecord'])->name('payment.delete.record');
Route::post('/payment/get-summary', [PaymentController::class, 'getPaymentSummary'])->name('payment.get.summary');
Route::post('/payment/export-summary', [PaymentController::class, 'exportPaymentSummary'])->name('payment.export.summary');
Route::get('/payment/get-intakes/{courseID}/{location}', [PaymentController::class, 'getIntakesForCourseAndLocation'])->name('payment.get.intakes.for.course.location');

// Late Payment Routes - Bursar and Developer only
Route::middleware(['auth', 'role:Bursar,Developer'])->group(function () {
    Route::get('/late-payment', [LatePaymentController::class, 'index'])->name('late.payment.index');
    Route::post('/late-payment/get-payment-plan', [LatePaymentController::class, 'getPaymentPlan'])->name('late.payment.get.payment.plan');
    Route::post('/late-payment/get-paid-payments', [LatePaymentController::class, 'getPaidPaymentDetails'])->name('late.payment.get.paid.payments');
    Route::post('/late-payment/get-student-courses', [LatePaymentController::class, 'getStudentCourses'])->name('late.payment.get.student.courses');
});

Route::get('/courses/by-location', [App\Http\Controllers\SemesterCreationController::class, 'getCoursesByLocation'])->name('courses.byLocation');
