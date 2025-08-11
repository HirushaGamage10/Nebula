<div>
    <?php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
    ?>
    <div class="brand-logo d-flex align-items-center justify-content-center py-3">
        <a href="/" class="text-nowrap logo-img">
            <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula" width="180">
        </a>
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">
            
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
                RoleHelper::hasPermission($role, 'course.registration') ||
                RoleHelper::hasPermission($role, 'eligibility.registration') ||
                RoleHelper::hasPermission($role, 'student.other.information') ||
                RoleHelper::hasPermission($role, 'exam.results') ||
                RoleHelper::hasPermission($role, 'student.exam.result.management') ||
                RoleHelper::hasPermission($role, 'attendance') ||
                RoleHelper::hasPermission($role, 'overall.attendance') ||
                RoleHelper::hasPermission($role, 'student.list') ||
                RoleHelper::hasPermission($role, 'student.profile')
            ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.registration' ? 'active' : ''); ?>" href="<?php echo e(route('student.registration')); ?>">
                        <span><i class="ti ti-user"></i></span>
                        <span class="hide-menu">Student Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.registration' ? 'active' : ''); ?>" href="<?php echo e(route('course.registration')); ?>">
                        <span><i class="ti ti-notebook"></i></span>
                        <span class="hide-menu">Course Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'eligibility.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'eligibility.registration' ? 'active' : ''); ?>" href="<?php echo e(route('eligibility.registration')); ?>">
                        <span><i class="ti ti-cards"></i></span>
                        <span class="hide-menu">Eligibility & Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.other.information')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.other.information' ? 'active' : ''); ?>" href="<?php echo e(route('student.other.information')); ?>">
                        <span><i class="ti ti-layout"></i></span>
                        <span class="hide-menu">Student Other Information</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'exam.results') || RoleHelper::hasPermission($role, 'student.exam.result.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.exam.result.management' ? 'active' : ''); ?>" href="<?php echo e(route('student.exam.result.management')); ?>">
                        <span><i class="ti ti-file"></i></span>
                        <span class="hide-menu">Add Exam Result</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'exam.results') || RoleHelper::hasPermission($role, 'exam.results.view.edit')): ?>
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
            <?php if(RoleHelper::hasPermission($role, 'student.list')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.list' ? 'active' : ''); ?>" href="<?php echo e(route('student.list')); ?>">
                        <span><i class="ti ti-menu"></i></span>
                        <span class="hide-menu">Student Lists</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'student.profile')): ?>
                <li class="sidebar-item">
                    <?php
                        $user = auth()->user();
                        $studentProfileUrl = isset($user->student_id) && $user->student_id ? route('student.profile', ['studentId' => $user->student_id]) : route('student.profile', ['studentId' => 0]);
                    ?>
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.profile' ? 'active' : ''); ?>" href="<?php echo e($studentProfileUrl); ?>">
                        <span><i class="ti ti-id"></i></span>
                        <span class="hide-menu">Student Profile</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user() && (auth()->user()->role === 'Developer' || (isset(
                auth()->user()->user_role) && auth()->user()->user_role === 'Developer'))): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'uh.index.page' ? 'active' : ''); ?>" href="<?php echo e(route('uh.index.page')); ?>">
                        <span><i class="ti ti-list-numbers"></i></span>
                        <span class="hide-menu">External Institute IDs</span>
                    </a>
                </li>
            <?php endif; ?>
            

            
            <?php if(
                RoleHelper::hasPermission($role, 'all.clearance') ||
                RoleHelper::hasPermission($role, 'library.clearance') ||
                RoleHelper::hasPermission($role, 'hostel.clearance.form.management') ||
                RoleHelper::hasPermission($role, 'project.clearance.management')
            ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT CLEARANCE</span>
            </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'all.clearance')): ?>
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
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">FINANCIAL MANAGEMENT</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.clearance' ? 'active' : ''); ?>" href="<?php echo e(route('payment.clearance')); ?>">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Payment Clearance</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'module.management') ||
                RoleHelper::hasPermission($role, 'module.creation') ||
                RoleHelper::hasPermission($role, 'course.management') ||
                RoleHelper::hasPermission($role, 'intake.create') ||
                RoleHelper::hasPermission($role, 'semester.create') ||
                RoleHelper::hasPermission($role, 'semester.registration') ||
                RoleHelper::hasPermission($role, 'timetable')
            ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">ACADEMIC MANAGEMENT</span>
            </li>
            <?php endif; ?>            
            <?php if($role == 'Developer' || $role == 'Program Administrator (level 02)' || RoleHelper::hasPermission($role, 'module.creation')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'module.creation' ? 'active' : ''); ?>" href="<?php echo e(route('module.creation')); ?>">
                        <span><i class="ti ti-plus"></i></span>
                        <span class="hide-menu">Module Creation</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'course.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.management' ? 'active' : ''); ?>" href="<?php echo e(route('course.management')); ?>">
                        <span><i class="ti ti-notebook"></i></span>
                        <span class="hide-menu">Course Management</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'intake.create')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'intake.create' ? 'active' : ''); ?>" href="<?php echo e(route('intake.create')); ?>">
                        <span><i class="ti ti-pencil"></i></span>
                        <span class="hide-menu">Intake Creation</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'semester.create')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'semesters.create' ? 'active' : ''); ?>" href="<?php echo e(route('semesters.create')); ?>">
                        <span><i class="ti ti-calendar"></i></span>
                        <span class="hide-menu">Semester Creation</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'semester.registration')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'semester.registration' ? 'active' : ''); ?>" href="<?php echo e(route('semester.registration')); ?>">
                        <span><i class="ti ti-user-check"></i></span>
                        <span class="hide-menu">Semester Registration</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'module.management')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'module.management' ? 'active' : ''); ?>" href="<?php echo e(route('module.management')); ?>">
                        <span><i class="ti ti-briefcase"></i></span>
                        <span class="hide-menu">Module Management</span>
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
            
            <?php if($role === 'Developer'): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'reporting.dashboard' ? 'active' : ''); ?>" href="<?php echo e(route('reporting.dashboard')); ?>">
                        <span><i class="ti ti-chart-bar"></i></span>
                        <span class="hide-menu">Reporting</span>
                    </a>
                </li>

                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'data.export.import' ? 'active' : ''); ?>" href="<?php echo e(route('data.export.import')); ?>">
                        <span><i class="ti ti-download"></i></span>
                        <span class="hide-menu">Data Export/Import</span>
                    </a>
                </li>
            <?php endif; ?>

            
            <?php if(
                RoleHelper::hasPermission($role, 'payment') ||
                RoleHelper::hasPermission($role, 'late.payment') ||
                RoleHelper::hasPermission($role, 'payment.discounts') ||
                RoleHelper::hasPermission($role, 'payment.plan')
            ): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment.plan')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.plan' ? 'active' : ''); ?>" href="<?php echo e(route('payment.plan')); ?>">
                        <span><i class="ti ti-currency-dollar"></i></span>
                        <span class="hide-menu">Payment Plan</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(auth()->user() && (auth()->user()->role === 'Developer' || (isset(auth()->user()->user_role) && auth()->user()->user_role === 'Developer'))): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.discount.page' ? 'active' : ''); ?>" href="<?php echo e(route('payment.discount.page')); ?>">
                        <span><i class="ti ti-discount"></i></span>
                        <span class="hide-menu">Payment Discount</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'payment')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.index' ? 'active' : ''); ?>" href="<?php echo e(route('payment.index')); ?>">
                        <span><i class="ti ti-credit-card"></i></span>
                        <span class="hide-menu">Payment</span>
                    </a>
                </li>
            <?php endif; ?>
            <?php if(RoleHelper::hasPermission($role, 'late.payment')): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'late.payment.index' ? 'active' : ''); ?>" href="<?php echo e(route('late.payment.index')); ?>">
                        <span><i class="ti ti-clock"></i></span>
                        <span class="hide-menu">Late Payment</span>
                    </a>
                </li>
            <?php endif; ?>

            

            
            <?php if(RoleHelper::hasPermission($role, 'special.approval')): ?>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">SPECIAL APPROVAL</span>
            </li>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'special.approval.list' ? 'active' : ''); ?>" href="<?php echo e(route('special.approval.list')); ?>">
                        <span><i class="ti ti-check"></i></span>
                        <span class="hide-menu">Special Approval</span>
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
        </ul>
    </nav>
</div>
<?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/components/sidebar.blade.php ENDPATH**/ ?>