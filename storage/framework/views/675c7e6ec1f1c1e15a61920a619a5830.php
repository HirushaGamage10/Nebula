<div>
    <?php
        $user = auth()->user();
        $role = $user->user_role ?? '';
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

            <!-- Dashboard -->
            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('dashboard') ? 'active' : ''); ?>"
                   href="<?php echo e(route('dashboard')); ?>">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>

            <!-- STUDENT VIEW SECTION -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">STUDENT DATA</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('students.view') ? 'active' : ''); ?>"
                   href="<?php echo e(route('students.view')); ?>">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>

            <!-- PROJECT CLEARANCE -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">PROJECT MANAGEMENT</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('project.clearance.management') ? 'active' : ''); ?>"
                   href="<?php echo e(route('project.clearance.management')); ?>">
                    <span><i class="ti ti-briefcase"></i></span>
                    <span class="hide-menu">Project Clearance</span>
                </a>
            </li>

            <!-- ATTENDANCE -->
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">ATTENDANCE</span>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link <?php echo e(request()->routeIs('attendance') ? 'active' : ''); ?>"
                   href="<?php echo e(route('attendance')); ?>">
                    <span><i class="ti ti-id"></i></span>
                    <span class="hide-menu">Attendance</span>
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

            <!-- Team Nebula IT -->
            <li class="text-center mb-3" style="opacity:0.8; font-size:13px;">
                <a href="<?php echo e(route('team.phase.index')); ?>"
                   class="text-muted text-decoration-none py-1 px-2 d-inline-block rounded">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
<?php /**PATH E:\Project-Nebula\Nebula\resources\views/components/sidebar/project_tutor_sidebar.blade.php ENDPATH**/ ?>