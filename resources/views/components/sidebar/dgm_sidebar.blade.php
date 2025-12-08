<div>
    @php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
    @endphp

    <!-- Logo -->
    <div class="brand-logo d-flex align-items-center justify-content-center py-3 position-relative w-100">
        <a href="javascript:void(0)" aria-label="Close sidebar"
           class="nav-link sidebartoggler d-xl-none position-absolute top-0 end-0 mt-1 me-3">
            <i class="ti ti-x fs-5"></i>
        </a>

        <a href="{{ route('dashboard') }}" class="text-nowrap logo-img">
            <img src="{{ asset('images/logos/nebula.png') }}" alt="Nebula" width="180">
        </a>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
        <ul id="sidebarnav">

            {{-- HOME --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>

            {{-- Dashboard --}}
            @if(RoleHelper::hasPermission($role, 'dashboard'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}"
                   href="{{ route('dashboard') }}">
                    <span><i class="ti ti-layout-dashboard"></i></span>
                    <span class="hide-menu">Dashboard</span>
                </a>
            </li>
            @endif

            <br>

            {{-- STUDENT MANAGEMENT --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            {{-- Student Lists --}}
            @if(RoleHelper::hasPermission($role, 'student.list'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.list' ? 'active' : '' }}"
                   href="{{ route('student.list') }}">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>
            @endif

            {{-- All Students View --}}
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'students.view' ? 'active' : '' }}"
                   href="{{ route('students.view') }}">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>

            {{-- Course Change --}}
            @if(RoleHelper::hasPermission($role, 'course.change'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'course.change.index' ? 'active' : '' }}"
                   href="{{ route('course.change.index') }}">
                    <span><i class="ti ti-repeat"></i></span>
                    <span class="hide-menu">Course Change</span>
                </a>
            </li>
            @endif


            <br>

            {{-- FINANCIAL --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            {{-- Payment Dashboard --}}
            @if(RoleHelper::hasPermission($role, 'payment.dashboard'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.summary') ? 'active' : '' }}"
                   href="{{ route('payment.summary') }}">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>
            @endif

            {{-- 🔹 Late Fee Approval --}}
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('latefee.approval.index') ? 'active' : '' }}"
                   href="{{ route('latefee.approval.index') }}">
                    <span><i class="ti ti-currency-dollar"></i></span>
                    <span class="hide-menu">Late Fee Approval</span>
                </a>
            </li>


            <br>

            {{-- SPECIAL APPROVAL --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">SPECIAL APPROVAL</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'special.approval'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'special.approval.list' ? 'active' : '' }}"
                   href="{{ route('special.approval.list') }}">
                    <span><i class="ti ti-check"></i></span>
                    <span class="hide-menu">Special Approval</span>
                </a>
            </li>
            @endif


            <br><br>

            {{-- FOOTER --}}
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="{{ route('user.profile') }}" class="btn w-100"
                       style="background:#6c8cff; color:#fff;">
                        My Profile
                    </a>

                    <a href="{{ route('logout') }}" class="btn w-100"
                       style="background:#ff8c7a; color:#fff;">
                        Logout
                    </a>
                </div>
            </div>

            {{-- Team Nebula --}}
            <li id="teamNebulaLink" class="text-center mb-3"
                style="opacity: 0.8; font-size: 13px;">
                <a href="{{ route('team.phase.index') }}"
                   class="text-decoration-none d-inline-block py-1 px-2 rounded
                          {{ Route::currentRouteName() == 'team.phase.index'
                                ? 'bg-light text-primary fw-semibold shadow-sm'
                                : 'text-muted' }}"
                   style="transition: all 0.3s;">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
