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

            <br>

            
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            
            <?php if(RoleHelper::hasPermission($role, 'student.list')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'student.list' ? 'active' : ''); ?>"
                   href="<?php echo e(route('student.list')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>
            <?php endif; ?>

            
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'students.view' ? 'active' : ''); ?>"
                   href="<?php echo e(route('students.view')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>

            
            <?php if(RoleHelper::hasPermission($role, 'course.change')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'course.change.index' ? 'active' : ''); ?>"
                   href="<?php echo e(route('course.change.index')); ?>">
                    <span><i class="ti ti-repeat"></i></span>
                    <span class="hide-menu">Course Change</span>
                </a>
            </li>
            <?php endif; ?>


            <br>

            
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            
            <?php if(RoleHelper::hasPermission($role, 'payment.dashboard')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('payment.summary') ? 'active' : ''); ?>"
                   href="<?php echo e(route('payment.summary')); ?>">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>
            <?php endif; ?>

            
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('latefee.approval.index') ? 'active' : ''); ?>"
                   href="<?php echo e(route('latefee.approval.index')); ?>">
                    <span><i class="ti ti-currency-dollar"></i></span>
                    <span class="hide-menu">Late Fee Approval</span>
                </a>
            </li>


            <br>

            
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">SPECIAL APPROVAL</span>
            </li>

            <?php if(RoleHelper::hasPermission($role, 'special.approval')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'special.approval.list' ? 'active' : ''); ?>"
                   href="<?php echo e(route('special.approval.list')); ?>">
                    <span><i class="ti ti-check"></i></span>
                    <span class="hide-menu">Special Approval</span>
                </a>
            </li>
            <?php endif; ?>


            <br><br>

            
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100"
                       style="background:#6c8cff; color:#fff;">
                        My Profile
                    </a>

                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100"
                       style="background:#ff8c7a; color:#fff;">
                        Logout
                    </a>
                </div>
            </div>

            
            <li id="teamNebulaLink" class="text-center mb-3"
                style="opacity: 0.8; font-size: 13px;">
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
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/components/sidebar/dgm_sidebar.blade.php ENDPATH**/ ?>