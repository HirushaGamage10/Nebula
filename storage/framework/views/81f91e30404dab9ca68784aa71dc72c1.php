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

            <?php if(RoleHelper::hasPermission($role, 'dashboard')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'dashboard' ? 'active' : ''); ?>"
                   href="<?php echo e(route('dashboard')); ?>">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
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

            <!-- HOSTEL CLEARANCE -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">STUDENT CLEARANCE</span>
            </li>

            <?php if(RoleHelper::hasPermission($role, 'hostel.clearance.form.management')): ?>
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(Route::currentRouteName() == 'hostel.clearance.form.management' ? 'active' : ''); ?>"
                   href="<?php echo e(route('hostel.clearance.form.management')); ?>">
                    <span><i class="ti ti-note"></i></span>
                    <span class="hide-menu">Hostel Clearance</span>
                </a>
            </li>
            <?php endif; ?>
<br><br>
            <!-- Profile & Logout -->
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background:#6c8cff; color:#fff;">My Profile</a>
                    <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background:#ff8c7a; color:#fff;">Logout</a>
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

        </ul>
    </nav>
</div>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/components/sidebar/hostel_sidebar.blade.php ENDPATH**/ ?>