<div>
    <?php
        $user = auth()->user();
        $role = $user->user_role ?? '';
        $studentId = $user->student_id ?? 0;
    ?>

    <!-- Brand Logo -->
    <div class="brand-logo d-flex align-items-center justify-content-center py-3 position-relative w-100">
        <a href="javascript:void(0)" class="nav-link sidebartoggler d-xl-none position-absolute top-0 end-0 mt-1 me-3">
            <i class="ti ti-x fs-5"></i>
        </a>
        <a href="<?php echo e(route('dashboard')); ?>" class="text-nowrap logo-img">
            <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" width="180">
        </a>
    </div>

    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
        <ul id="sidebarnav">

            <!-- HOME -->
            <li class="nav-small-cap"><span class="nav-small-cap-text">HOME</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('dashboard')); ?>">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>



            <!-- STUDENT MANAGEMENT -->
            <li class="nav-small-cap mt-3"><span class="nav-small-cap-text">STUDENT MANAGEMENT</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('badges.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('badges.index')); ?>">
                    <span><i class="ti ti-award"></i></span>
                    <span class="hide-menu">Course and Badges</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('student.other.information') ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.other.information')); ?>">
                    <span><i class="ti ti-layout"></i></span>
                    <span class="hide-menu">Student Other Information</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('student.list') ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.list')); ?>">
                    <span><i class="ti ti-menu"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('student.profile') ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.profile', ['studentId' => $studentId])); ?>">
                    <span><i class="ti ti-id"></i></span>
                    <span class="hide-menu">Student Profile</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('students.view') ? 'active' : ''); ?>"
                   href="<?php echo e(route('students.view')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>



            <!-- EXAMS -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">EXAMS</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('student.exam.result.management') ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.exam.result.management')); ?>">
                    <span><i class="ti ti-file"></i></span>
                    <span class="hide-menu">Add Exam Results</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('exam.results.view.edit') ? 'active' : ''); ?>"
                   href="<?php echo e(route('exam.results.view.edit')); ?>">
                    <span><i class="ti ti-edit"></i></span>
                    <span class="hide-menu">View and Edit Results</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('repeat.students.management') ? 'active' : ''); ?>"
                   href="<?php echo e(route('repeat.students.management')); ?>">
                    <span><i class="ti ti-refresh"></i></span>
                    <span class="hide-menu">Repeat Students</span>
                </a>
            </li>



            <!-- ATTENDANCE -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">ATTENDANCE</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('attendance') ? 'active' : ''); ?>"
                   href="<?php echo e(route('attendance')); ?>">
                    <span><i class="ti ti-id"></i></span>
                    <span class="hide-menu">Attendance</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('overall.attendance') ? 'active' : ''); ?>"
                   href="<?php echo e(route('overall.attendance')); ?>">
                    <span><i class="ti ti-id"></i></span>
                    <span class="hide-menu">Overall Attendance</span>
                </a>
            </li>



            <!-- DEVELOPER ONLY ITEMS (BUT YOU WANT THEM FOR ADMIN L2) -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">EXTERNAL DATA</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('uh.index.page') ? 'active' : ''); ?>"
                   href="<?php echo e(route('uh.index.page')); ?>">
                    <span><i class="ti ti-list-numbers"></i></span>
                    <span class="hide-menu">External Institute IDs</span>
                </a>
            </li>



            <!-- CLEARANCE -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">CLEARANCE</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('all.clearance.management') ? 'active' : ''); ?>"
                   href="<?php echo e(route('all.clearance.management')); ?>">
                    <span><i class="ti ti-clipboard"></i></span>
                    <span class="hide-menu">All Clearance</span>
                </a>
            </li>



            <!-- ACADEMIC MANAGEMENT -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">ACADEMIC</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('module.creation') ? 'active' : ''); ?>"
                   href="<?php echo e(route('module.creation')); ?>">
                    <span><i class="ti ti-plus"></i></span>
                    <span class="hide-menu">Module Creation</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('intake.create') ? 'active' : ''); ?>"
                   href="<?php echo e(route('intake.create')); ?>">
                    <span><i class="ti ti-pencil"></i></span>
                    <span class="hide-menu">Intake Creation</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('semesters.create') ? 'active' : ''); ?>"
                   href="<?php echo e(route('semesters.create')); ?>">
                    <span><i class="ti ti-calendar"></i></span>
                    <span class="hide-menu">Semester Creation</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('semesters.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('semesters.index')); ?>">
                    <span><i class="ti ti-list"></i></span>
                    <span class="hide-menu">Semester Management</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('semester.registration') ? 'active' : ''); ?>"
                   href="<?php echo e(route('semester.registration')); ?>">
                    <span><i class="ti ti-user-check"></i></span>
                    <span class="hide-menu">Semester Registration</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('module.management') ? 'active' : ''); ?>"
                   href="<?php echo e(route('module.management')); ?>">
                    <span><i class="ti ti-briefcase"></i></span>
                    <span class="hide-menu">Module Management</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('timetable.show') ? 'active' : ''); ?>"
                   href="<?php echo e(route('timetable.show')); ?>">
                    <span><i class="ti ti-calendar"></i></span>
                    <span class="hide-menu">Time table</span>
                </a>
            </li>



            <!-- FINANCIAL -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">FINANCIAL</span></li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('payment.summary') ? 'active' : ''); ?>"
                   href="<?php echo e(route('payment.summary')); ?>">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>



            <!-- FOOTER -->
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background:#6c8cff; color:#fff;">
                        My Profile
                    </a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background:#ff8c7a; color:#fff;">
                        Logout
                    </a>
                </div>
            </div>

            <li class="text-center mb-3" style="opacity:0.8; font-size:13px;">
                <a href="<?php echo e(route('team.phase.index')); ?>" class="text-muted text-decoration-none py-1 px-2 d-inline-block rounded">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/components/sidebar/admin_l2_sidebar.blade.php ENDPATH**/ ?>