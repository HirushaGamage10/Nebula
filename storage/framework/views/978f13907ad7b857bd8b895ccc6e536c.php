<div>
    <?php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
    ?>

    <!-- Logo -->
    <div class="brand-logo d-flex align-items-center justify-content-center py-3 position-relative w-100">
        <a href="javascript:void(0)" aria-label="Close sidebar"
            class="nav-link sidebartoggler d-xl-none position-absolute top-0 end-0 mt-1 me-3">
            <i class="ti ti-x fs-5"></i>
        </a>
        <a href="<?php echo e(route('dashboard')); ?>" class="text-nowrap logo-img">
            <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula" width="180">
        </a>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
        <ul id="sidebarnav">

            <!-- HOME -->
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>

            <!-- Dashboard -->
            <?php if(RoleHelper::hasPermission($role, 'dashboard')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dashboard' ? 'active' : ''); ?>"
                   href="<?php echo e(route('dashboard')); ?>">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            <?php endif; ?>
            
        <li><hr class="my-2 border-gray-200 opacity-30"></li>
            <!-- STUDENT MANAGEMENT -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            <!-- Student Lists -->
            <?php if(RoleHelper::hasPermission($role, 'student.list')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.list' ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.list')); ?>">
                    <span><i class="ti ti-menu"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- All Students View -->
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'students.view' ? 'active' : ''); ?>"
                   href="<?php echo e(route('students.view')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>

            <!-- Course Change -->
            <?php if(RoleHelper::hasPermission($role, 'course.change')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.change.index' ? 'active' : ''); ?>"
                   href="<?php echo e(route('course.change.index')); ?>">
                    <span><i class="ti ti-repeat"></i></span>
                    <span class="hide-menu">Course Change</span>
                </a>
            </li>
            <?php endif; ?>

            
        <li><hr class="my-2 border-gray-200 opacity-30"></li>

            <!-- FINANCIAL -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            <!-- Payment Plans -->
            <?php if(RoleHelper::hasPermission($role, 'payment.plan')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.plan.index' ? 'active' : ''); ?>"
                   href="<?php echo e(route('payment.plan.index')); ?>">
                    <span><i class="ti ti-cash"></i></span>
                    <span class="hide-menu">Payment Plans</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.plan' ? 'active' : ''); ?>"
                   href="<?php echo e(route('payment.plan')); ?>">
                    <span><i class="ti ti-plus"></i></span>
                    <span class="hide-menu">Create Payment Plan</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Payment Dashboard -->
            <?php if(RoleHelper::hasPermission($role, 'payment.dashboard')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'payment.summary' ? 'active' : ''); ?>"
                   href="<?php echo e(route('payment.summary')); ?>">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>
            <?php endif; ?>


            <!-- Profile & Logout -->
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background:#6c8cff; color:#fff;">My Profile</a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background:#ff8c7a; color:#fff;">Logout</a>
                </div>
            </div>

            <!-- Team Nebula -->
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

        </ul>
    </nav>
</div>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/components/sidebar/marketing_sidebar.blade.php ENDPATH**/ ?>