<div>
    <?php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
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
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>

            <?php if(RoleHelper::hasPermission($role, 'dashboard')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dashboard' ? 'active' : ''); ?>"
                   href="<?php echo e(route('dashboard')); ?>">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <?php endif; ?>


            <!-- USER MANAGEMENT -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">USER MANAGEMENT</span>
            </li>

            <?php if(RoleHelper::hasPermission($role, 'create.user')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'create.user' ? 'active' : ''); ?>"
                   href="<?php echo e(route('create.user')); ?>">
                    <span><i class="ti ti-user-plus"></i></span>
                    <span class="hide-menu">Create User</span>
                </a>
            </li>
            <?php endif; ?>

            <?php if(RoleHelper::hasPermission($role, 'user.management')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dgm.user.management' ? 'active' : ''); ?>"
                   href="<?php echo e(route('dgm.user.management')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">User Management</span>
                </a>
            </li>
            <?php endif; ?>



            <!-- ===================== STUDENT MANAGEMENT ===================== -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            <?php
                $studentMenu = [
                    'student.registration'       => ['student.registration',        'ti ti-user-plus',     'Student Registration'],
                    'course.registration'        => ['course.registration',         'ti ti-notebook',      'Course Registration'],
                    'eligibility.registration'   => ['eligibility.registration',    'ti ti-cards',         'Eligibility & Registration'],
                    'course.badge'               => ['badges.index',                'ti ti-award',         'Course & Badges'],
                    'student.other.information'  => ['student.other.information',   'ti ti-layout',        'Other Information'],
                    'student.list'               => ['student.list',                'ti ti-menu',          'Student Lists'],

                    /* FIXED: Student Profile route requires studentId */
                    'student.profile'            => [null,                          'ti ti-id',            'Student Profile'],

                    'course.change'              => ['course.change.index',         'ti ti-repeat',        'Course Change'],
                ];
            ?>

            <?php $__currentLoopData = $studentMenu; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>

                    <?php if($perm === 'student.profile'): ?>
                        <?php
                            $user = auth()->user();
                            $studentId = $user->student_id ?? 0;
                            $profileRoute = route('student.profile', ['studentId' => $studentId]);
                        ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php echo e(request()->routeIs('student.profile') ? 'active' : ''); ?>"
                               href="<?php echo e($profileRoute); ?>">
                                <span><i class="<?php echo e($icon); ?>"></i></span>
                                <span class="hide-menu"><?php echo e($label); ?></span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?php echo e(request()->routeIs($route) ? 'active' : ''); ?>"
                               href="<?php echo e(route($route)); ?>">
                                <span><i class="<?php echo e($icon); ?>"></i></span>
                                <span class="hide-menu"><?php echo e($label); ?></span>
                            </a>
                        </li>
                    <?php endif; ?>

                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            <!-- Always visible -->
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'students.view' ? 'active' : ''); ?>"
                   href="<?php echo e(route('students.view')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>



            <!-- EXAMS & RESULTS -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">EXAMS</span>
            </li>

            <?php $__currentLoopData = [
                'exam.results' => ['student.exam.result.management', 'ti ti-file', 'Add Exam Results'],
                'exam.results.view.edit' => ['exam.results.view.edit', 'ti ti-edit', 'View & Edit Results'],
                'repeat.students.management' => ['repeat.students.management', 'ti ti-refresh', 'Repeat Students'],
                'repeat.students.payment' => ['repeat.payment.index', 'ti ti-currency-dollar', 'Repeat Payment Plan'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == $route ? 'active' : ''); ?>"
                       href="<?php echo e(route($route)); ?>">
                        <span><i class="<?php echo e($icon); ?>"></i></span>
                        <span class="hide-menu"><?php echo e($label); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            <!-- ATTENDANCE -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">ATTENDANCE</span></li>

            <?php $__currentLoopData = [
                'attendance' => ['attendance', 'ti ti-id', 'Attendance'],
                'overall.attendance' => ['overall.attendance', 'ti ti-id', 'Overall Attendance'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == $route ? 'active' : ''); ?>"
                       href="<?php echo e(route($route)); ?>">
                        <span><i class="<?php echo e($icon); ?>"></i></span>
                        <span class="hide-menu"><?php echo e($label); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            <!-- CLEARANCE -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">CLEARANCE</span>
            </li>

            <?php $__currentLoopData = [
                'all.clearance.management' => ['all.clearance.management', 'ti ti-clipboard', 'All Clearance'],
                'hostel.clearance.form.management' => ['hostel.clearance.form.management', 'ti ti-note', 'Hostel Clearance'],
                'library.clearance' => ['library.clearance', 'ti ti-clipboard', 'Library Clearance'],
                'project.clearance.management' => ['project.clearance.management', 'ti ti-briefcase', 'Project Clearance'],
                'payment.clearance' => ['payment.clearance', 'ti ti-cash', 'Payment Clearance'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == $route ? 'active' : ''); ?>"
                       href="<?php echo e(route($route)); ?>">
                        <span><i class="<?php echo e($icon); ?>"></i></span>
                        <span class="hide-menu"><?php echo e($label); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            <!-- ACADEMIC MANAGEMENT -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">ACADEMIC MANAGEMENT</span></li>

            <?php $__currentLoopData = [
                'module.creation' => ['module.creation', 'ti ti-plus', 'Module Creation'],
                'module.management' => ['module.management', 'ti ti-briefcase', 'Module Management'],
                'course.management' => ['course.management', 'ti ti-notebook', 'Course Management'],
                'intake.create' => ['intake.create', 'ti ti-pencil', 'Create Intake'],
                'semester.create' => ['semesters.create', 'ti ti-calendar', 'Semester Creation'],
                'semester.registration' => ['semester.registration', 'ti ti-user-check', 'Semester Registration'],
                'timetable' => ['timetable.show', 'ti ti-calendar', 'Timetable'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == $route ? 'active' : ''); ?>"
                       href="<?php echo e(route($route)); ?>">
                        <span><i class="<?php echo e($icon); ?>"></i></span>
                        <span class="hide-menu"><?php echo e($label); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>



            <!-- FINANCIAL -->
            <li><hr class="my-2 opacity-30"></li>
            <li class="nav-small-cap"><span class="nav-small-cap-text">FINANCIAL</span></li>

            <?php $__currentLoopData = [
                'payment.plan' => ['payment.plan.index', 'ti ti-cash', 'Payment Plans'],
                'payment' => ['payment.index', 'ti ti-credit-card', 'Payments'],
                'payment.discounts' => ['payment.discount.page', 'ti ti-discount', 'Payment Discount'],
                'late.payment' => ['late.payment.index', 'ti ti-clock', 'Late Payment'],
                'latefee.approval.index' => ['latefee.approval.index', 'ti ti-currency-dollar', 'Late Fee Approval'],
                'payment.dashboard' => ['payment.summary', 'ti ti-chart-pie', 'Payment Dashboard'],
                'misc.payment' => ['misc.payment.index', 'ti ti-wallet', 'Misc Payments'],
                'payment.showDownloadPage' => ['payment.showDownloadPage', 'ti ti-file-download', 'Payment Statement'],
            ]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $perm => [$route, $icon, $label]): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php if(RoleHelper::hasPermission($role, $perm)): ?>
                <li class="sidebar-item">
                    <a class="sidebar-link <?php echo e(Route::currentRouteName() == $route ? 'active' : ''); ?>"
                       href="<?php echo e(route($route)); ?>">
                        <span><i class="<?php echo e($icon); ?>"></i></span>
                        <span class="hide-menu"><?php echo e($label); ?></span>
                    </a>
                </li>
                <?php endif; ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>


            <!-- FOOTER -->
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background:#6c8cff; color:#fff;">My Profile</a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background:#ff8c7a; color:#fff;">Logout</a>
                </div>
            </div>

            <li class="text-center mb-3" style="opacity:0.8; font-size:13px;">
                <a href="<?php echo e(route('team.phase.index')); ?>"
                   class="text-muted text-decoration-none py-1 px-2 d-inline-block rounded">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/components/sidebar/admin_l1_sidebar.blade.php ENDPATH**/ ?>