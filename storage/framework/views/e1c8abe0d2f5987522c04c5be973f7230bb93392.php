<div>
    <?php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
    ?>
    <div class="brand-logo d-flex align-items-center justify-content-center py-3 position-relative w-100">
        <!-- Mobile close button (uses the same toggler JS) -->
          <a href="javascript:void(0)" aria-label="Close sidebar"
              class="nav-link sidebartoggler d-xl-none position-absolute top-0 end-0 mt-1 me-3">
            <i class="ti ti-x fs-5"></i>
        </a>
        <a href="/dashboard" class="text-nowrap logo-img">
            <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula" width="180">
        </a>
    </div>

    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul class="metismenu" id="menu">
            
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>
            <?php if(RoleHelper::hasPermission($role, 'dashboard')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dashboard' ? 'active' : ''); ?>" href="<?php echo e(route('dashboard')); ?>">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if($role == 'Program Administrator (level 01)' || $role == 'Developer'): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">USER MANAGEMENT</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'create.user' ? 'active' : ''); ?>" href="<?php echo e(route('create.user')); ?>">
                    <span><i class="ti ti-user"></i></span>
                    <span class="hide-menu">Create User</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dgm.user.management' ? 'active' : ''); ?>" href="<?php echo e(route('dgm.user.management')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">User Management</span>
                </a>
            </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'student.registration') ||
                RoleHelper::hasPermission($role, 'student.other.information') ||
                RoleHelper::hasPermission($role, 'student.list') ||
                RoleHelper::hasPermission($role, 'student.view') ||
                RoleHelper::hasPermission($role, 'course.badge') ||
                RoleHelper::hasPermission($role, 'student.profile')
                ): ?>
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student_management.registration' ? 'active' : ''); ?>" href="<?php echo e(route('student_management.registration')); ?>">
                        <span><i class="ti ti-user"></i></span>
                        <span class="hide-menu">Student Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.other.information')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student_management.other.information' ? 'active' : ''); ?>" href="<?php echo e(route('student_management.other.information')); ?>">
                        <span><i class="ti ti-layout"></i></span>
                        <span class="hide-menu">Student Other Information</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.list')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student_management.list' ? 'active' : ''); ?>" href="<?php echo e(route('student_management.list')); ?>">
                        <span><i class="ti ti-menu"></i></span>
                        <span class="hide-menu">Student Lists</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.view')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student_management.view' ? 'active' : ''); ?>" href="<?php echo e(route('student_management.view')); ?>">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">All Students View</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.badge')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'badges.index' ? 'active' : ''); ?>" href="<?php echo e(route('badges.index')); ?>">
                        <span><i class="ti ti-id-badge"></i></span>
                        <span class="hide-menu">Badges Generation</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.profile')): ?>
                <li class="sidebar-item">
                    <?php
                        $user = auth()->user();
                        $studentProfileUrl = isset($user->student_id) && $user->student_id
                            ? route('student_management.profile', ['studentId' => $user->student_id])
                            : route('student_management.profile', ['studentId' => 0]);
                    ?>
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student_management.profile' ? 'active' : ''); ?>" href="<?php echo e($studentProfileUrl); ?>">
                        <span><i class="ti ti-id"></i></span>
                        <span class="hide-menu">Student Profile</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'course.registration') ||
                RoleHelper::hasPermission($role, 'eligibility.registration') ||
                RoleHelper::hasPermission($role, 'semester.registration') ||
                RoleHelper::hasPermission($role, 'module.management') ||
                RoleHelper::hasPermission($role, 'uh.index.page') ||
                RoleHelper::hasPermission($role, 'course.change.index')
                ): ?>
                
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">REGISTRATIONS</span>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.registration' ? 'active' : ''); ?>" href="<?php echo e(route('course.registration')); ?>">
                        <span><i class="ti ti-book"></i></span>
                        <span class="hide-menu">Course Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'eligibility.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'eligibility.registration' ? 'active' : ''); ?>" href="<?php echo e(route('eligibility.registration')); ?>">
                        <span><i class="ti ti-checks"></i></span>
                        <span class="hide-menu">Eligibility & Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'semester.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'semester.registration' ? 'active' : ''); ?>" href="<?php echo e(route('semester.registration')); ?>">
                        <span><i class="ti ti-calendar-stats"></i></span>
                        <span class="hide-menu">Semester Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'module.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'module.management' ? 'active' : ''); ?>" href="<?php echo e(route('module.management')); ?>">
                        <span><i class="ti ti-briefcase"></i></span>
                        <span class="hide-menu">Elective Module Registrations</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'uh.index.page')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'uh.index.page' ? 'active' : ''); ?>" href="<?php echo e(route('uh.index.page')); ?>">
                        <span><i class="ti ti-id-badge"></i></span>
                        <span class="hide-menu">External Institute IDs</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.change')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.change.index' ? 'active' : ''); ?>" href="<?php echo e(route('course.change.index')); ?>">
                        <span><i class="ti ti-repeat"></i></span>
                        <span class="hide-menu">Course Change</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'exam.results') || 
                RoleHelper::hasPermission($role, 'student.exam.result.management') ||
                RoleHelper::hasPermission($role, 'exam.results.view.edit') ||
                RoleHelper::hasPermission($role, 'repeat.students.management')
                ): ?>
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">EXAMS & RESULTS</span>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'exam.results')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.exam.result.management' ? 'active' : ''); ?>" href="<?php echo e(route('student.exam.result.management')); ?>">
                        <span><i class="ti ti-file"></i></span>
                        <span class="hide-menu">Add Exam Result</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'exam.results.view.edit')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'exam.results.view.edit' ? 'active' : ''); ?>" href="<?php echo e(route('exam.results.view.edit')); ?>">
                        <span><i class="ti ti-edit"></i></span>
                        <span class="hide-menu">View & Edit Results</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'repeat.students.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'repeat.students.management' ? 'active' : ''); ?>" href="<?php echo e(route('repeat.students.management')); ?>">
                        <span><i class="ti ti-refresh"></i></span>
                        <span class="hide-menu">Repeat Students</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'attendance') ||
                RoleHelper::hasPermission($role, 'overall.attendance')
                ): ?>
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">ATTENDANCE</span>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'attendance')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'attendance' ? 'active' : ''); ?>" href="<?php echo e(route('attendance')); ?>">
                        <span><i class="ti ti-id"></i></span>
                        <span class="hide-menu">Attendance</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'overall.attendance')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'overall.attendance' ? 'active' : ''); ?>" href="<?php echo e(route('overall.attendance')); ?>">
                        <span><i class="ti ti-id"></i></span>
                        <span class="hide-menu">Overall Attendance</span>
                    </a>
                </li>
            <?php endif; ?>
           
            
            <?php if(
                RoleHelper::hasPermission($role, 'all.clearance.management') ||
                RoleHelper::hasPermission($role, 'library.clearance') ||
                RoleHelper::hasPermission($role, 'hostel.clearance.form.management') ||
                RoleHelper::hasPermission($role, 'project.clearance.management') ||
                RoleHelper::hasPermission($role, 'payment.clearance')
                ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT CLEARANCE</span>
            </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'all.clearance.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'all.clearance.management' ? 'active' : ''); ?>" href="<?php echo e(route('all.clearance.management')); ?>">
                        <span><i class="ti ti-clipboard"></i></span>
                        <span class="hide-menu">All Clearance</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'library.clearance')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'library.clearance' ? 'active' : ''); ?>" href="<?php echo e(route('library.clearance')); ?>">
                        <span><i class="ti ti-clipboard"></i></span>
                        <span class="hide-menu">Library Clearance</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'hostel.clearance.form.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'hostel.clearance.form.management' ? 'active' : ''); ?>" href="<?php echo e(route('hostel.clearance.form.management')); ?>">
                        <span><i class="ti ti-note"></i></span>
                        <span class="hide-menu">Hostel Clearance</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'project.clearance.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'project.clearance.management' ? 'active' : ''); ?>" href="<?php echo e(route('project.clearance.management')); ?>">
                        <span><i class="ti ti-briefcase"></i></span>
                        <span class="hide-menu">Project Clearance</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.clearance')): ?>            
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.clearance' ? 'active' : ''); ?>" href="<?php echo e(route('payment.clearance')); ?>">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Payment Clearance</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'module.creation') ||
                RoleHelper::hasPermission($role, 'course.management') ||
                RoleHelper::hasPermission($role, 'intake.create') ||             
                RoleHelper::hasPermission($role, 'semesters.create') ||
                RoleHelper::hasPermission($role, 'semester.management') ||
                RoleHelper::hasPermission($role, 'timetable')
                ): ?>
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">COURSES & MODULES</span>
                </li>
            <?php endif; ?>
            <?php if($role == 'Developer' || $role == 'Program Administrator (level 02)' || RoleHelper::hasPermission($role, 'module.creation')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'module.creation' ? 'active' : ''); ?>" href="<?php echo e(route('module.creation')); ?>">
                        <span><i class="ti ti-plus"></i></span>
                        <span class="hide-menu">Create New Modules</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.management' ? 'active' : ''); ?>" href="<?php echo e(route('course.management')); ?>">
                        <span><i class="ti ti-notebook"></i></span>
                        <span class="hide-menu">Create New Courses</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'intake.create')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'intake.create' ? 'active' : ''); ?>" href="<?php echo e(route('intake.create')); ?>">
                        <span><i class="ti ti-pencil"></i></span>
                        <span class="hide-menu">Create New Intakes</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'module.creation') ||
                RoleHelper::hasPermission($role, 'course.management') ||
                RoleHelper::hasPermission($role, 'intake.create') ||             
                RoleHelper::hasPermission($role, 'semesters.create') ||
                RoleHelper::hasPermission($role, 'semester.management') ||
                RoleHelper::hasPermission($role, 'timetable')
                ): ?>
                <li><hr class="my-2 border-gray-200 opacity-30"></li>
                <?php endif; ?>

            
            <?php if(RoleHelper::hasPermission($role, 'semesters.create')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'semesters.create' ? 'active' : ''); ?>" href="<?php echo e(route('semesters.create')); ?>">
                        <span><i class="ti ti-calendar"></i></span>
                        <span class="hide-menu">Create New Semesters</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'semesters.index' ? 'active' : ''); ?>" href="<?php echo e(route('semesters.index')); ?>">
                        <span><i class="ti ti-list"></i></span>
                        <span class="hide-menu">Semester Management</span>
                    </a>
                </li>
            <?php endif; ?>
             <?php if(RoleHelper::hasPermission($role, 'timetable')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'timetable.show' ? 'active' : ''); ?>" href="<?php echo e(route('timetable.show')); ?>">
                        <span><i class="ti ti-calendar"></i></span>
                        <span class="hide-menu">Timetable</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'payment.dashboard') ||
                RoleHelper::hasPermission($role, 'payment.discounts') ||
                RoleHelper::hasPermission($role, 'payment.plan') ||
                RoleHelper::hasPermission($role, 'payment.plan.index') ||
                RoleHelper::hasPermission($role, 'payment') ||
                RoleHelper::hasPermission($role, 'misc.payment') ||
                RoleHelper::hasPermission($role, 'late.payment') ||
                RoleHelper::hasPermission($role, 'payment.discount.page') ||
                RoleHelper::hasPermission($role, 'repeat.students.payment')                
            ): ?>
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">FINANCIAL</span>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.dashboard')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.summary') ? 'active' : ''); ?>"href="<?php echo e(route('payment.summary')); ?>">
                        <span><i class="ti ti-chart-pie"></i></span>
                        <span class="hide-menu">Payment Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.discounts')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.discount.page') ? 'active' : ''); ?>" href="<?php echo e(route('payment.discount.page')); ?>">
                        <span><i class="ti ti-discount"></i></span>
                        <span class="hide-menu">Create Discounts</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.plan')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.plan.index') ? 'active' : ''); ?>" href="<?php echo e(route('payment.plan.index')); ?>">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Existing Payment Plans</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'payment.dashboard') ||
                RoleHelper::hasPermission($role, 'payment.discounts') ||
                RoleHelper::hasPermission($role, 'payment.plan') ||
                RoleHelper::hasPermission($role, 'payment.plan.index') ||
                RoleHelper::hasPermission($role, 'payment') ||
                RoleHelper::hasPermission($role, 'misc.payment') ||
                RoleHelper::hasPermission($role, 'late.payment') ||
                RoleHelper::hasPermission($role, 'payment.discount.page') ||
                RoleHelper::hasPermission($role, 'repeat.students.payment')                
            ): ?>
            <li><hr class="my-2 border-gray-200 opacity-30"></li>
            <?php endif; ?>

            <?php if(RoleHelper::hasPermission($role, 'payment.plan.index')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.plan') ? 'active' : ''); ?>" href="<?php echo e(route('payment.plan')); ?>">
                        <span><i class="ti ti-plus"></i></span>
                        <span class="hide-menu">Intake Payment Plan</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.index') ? 'active' : ''); ?>" href="<?php echo e(route('payment.index')); ?>">
                        <span><i class="ti ti-credit-card"></i></span>
                        <span class="hide-menu">Student Payment Plan</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'misc.payment')): ?>        
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('misc.payment.index') ? 'active' : ''); ?>" href="<?php echo e(route('misc.payment.index')); ?>">
                        <span><i class="ti ti-wallet"></i></span>
                        <span class="hide-menu">Miscellaneous Payment</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'late.payment')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('late.payment.index') ? 'active' : ''); ?>" href="<?php echo e(route('late.payment.index')); ?>">
                        <span><i class="ti ti-clock"></i></span>
                        <span class="hide-menu">Late Payment</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.showDownloadPage')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(request()->routeIs('payment.showDownloadPage') ? 'active' : ''); ?>"
                    href="<?php echo e(route('payment.showDownloadPage')); ?>">
                        <span><i class="ti ti-file-download"></i></span>
                        <span class="hide-menu">Payment Statements</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'repeat.students.payment')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'repeat.payment.index' ? 'active' : ''); ?>" href="<?php echo e(route('repeat.payment.index')); ?>">
                        <span><i class="ti ti-currency-dollar"></i></span>
                        <span class="hide-menu">Repeat Payment Plan</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'special.approval') ||
                RoleHelper::hasPermission($role, 'latefee.approval.index')
                ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">APPROVALS</span>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'special.approval.list' ? 'active' : ''); ?>" href="<?php echo e(route('special.approval.list')); ?>">
                    <span><i class="ti ti-check"></i></span>
                    <span class="hide-menu">Special Approval</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('latefee.approval.index') ? 'active' : ''); ?>" href="<?php echo e(route('latefee.approval.index')); ?>">
                    <span><i class="ti ti-currency-dollar"></i></span>
                    <span class="hide-menu">Late Fee Approval</span>
                </a>
            </li>
            <?php endif; ?>

            
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background-color: #6c8cff; color: #fff; font-weight: 500;">My Profile</a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background-color: #ff8c7a; color: #fff; font-weight: 500;">Logout</a>
                </div>
            </div>

            
            <li id="teamNebulaLink" class="text-center mb-3" style="opacity: 0.8; font-size: 13px;">
                <a href="<?php echo e(route('team.phase.index')); ?>"
                class="text-decoration-none d-inline-block py-1 px-2 rounded
                        <?php echo e(Route::currentRouteName() == 'team.phase.index'
                                ? 'bg-light text-primary fw-semibold shadow-sm' 
                                : 'text-muted'); ?>"
                style="transition: all 0.3s;">
                    © Team Nebula IT
                </a>
            </li>

            
            <?php if(Route::currentRouteName() == 'team.phase.index'): ?>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const link = document.getElementById('teamNebulaLink');
                        if (link) {
                            // Find the SimpleBar content element and scroll to the footer link
                            const sidebar = document.querySelector('.scroll-sidebar [data-simplebar=""]') || document.querySelector('.scroll-sidebar');
                            link.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        }
                    });
                </script>
            <?php endif; ?>

        </ul>
    </nav>
</div><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/components/sidebar.blade.php ENDPATH**/ ?>