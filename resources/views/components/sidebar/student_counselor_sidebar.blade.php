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

            {{-- =====================================
                STUDENT MANAGEMENT
            ====================================== --}}
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            {{-- Student Registration --}}
            @if(RoleHelper::hasPermission($role, 'student.registration'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.registration' ? 'active' : '' }}"
                   href="{{ route('student.registration') }}">
                    <span><i class="ti ti-user-plus"></i></span>
                    <span class="hide-menu">Student Registration</span>
                </a>
            </li>
            @endif

            {{-- Course Registration --}}
            @if(RoleHelper::hasPermission($role, 'course.registration'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'course.registration' ? 'active' : '' }}"
                   href="{{ route('course.registration') }}">
                    <span><i class="ti ti-book"></i></span>
                    <span class="hide-menu">Course Registration</span>
                </a>
            </li>
            @endif

            {{-- Eligibility Registration --}}
            @if(RoleHelper::hasPermission($role, 'eligibility.registration'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'eligibility.registration' ? 'active' : '' }}"
                   href="{{ route('eligibility.registration') }}">
                    <span><i class="ti ti-check"></i></span>
                    <span class="hide-menu">Eligibility Registration</span>
                </a>
            </li>
            @endif

            {{-- Student List --}}
            @if(RoleHelper::hasPermission($role, 'student.list'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.list' ? 'active' : '' }}"
                   href="{{ route('student.list') }}">
                    <span><i class="ti ti-list"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>
            @endif

            {{-- All Students View (Everyone gets this) --}}
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

            {{-- =====================================
                FINANCIAL
            ====================================== --}}
            <li class="nav-small-cap mt-3">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            {{-- Payment Dashboard --}}
            @if(RoleHelper::hasPermission($role, 'payment.dashboard'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'payment.summary' ? 'active' : '' }}"
                   href="{{ route('payment.summary') }}">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>
            @endif

            {{-- Payments --}}
            @if(RoleHelper::hasPermission($role, 'payment'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'payment.index' ? 'active' : '' }}"
                   href="{{ route('payment.index') }}">
                    <span><i class="ti ti-credit-card"></i></span>
                    <span class="hide-menu">Payments</span>
                </a>
            </li>
            @endif

            {{-- Payment Discounts --}}
            @if(RoleHelper::hasPermission($role, 'payment.discounts'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'payment.discount.page' ? 'active' : '' }}"
                   href="{{ route('payment.discount.page') }}">
                    <span><i class="ti ti-discount"></i></span>
                    <span class="hide-menu">Payment Discounts</span>
                </a>
            </li>
            @endif

            {{-- Late Payments --}}
            @if(RoleHelper::hasPermission($role, 'late.payment'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'late.payment.index' ? 'active' : '' }}"
                   href="{{ route('late.payment.index') }}">
                    <span><i class="ti ti-clock"></i></span>
                    <span class="hide-menu">Late Payments</span>
                </a>
            </li>
            @endif

            {{-- Late Fee Approval --}}
            @if(RoleHelper::hasPermission($role, 'latefee.approval'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('latefee.approval.index') ? 'active' : '' }}"
                href="{{ route('latefee.approval.index') }}">
                    <span><i class="ti ti-currency-dollar"></i></span>
                    <span class="hide-menu">Late Fee Approval</span>
                </a>
            </li>
            @endif


            <br><br>

            {{-- =====================================
                FOOTER
            ====================================== --}}
            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="{{ route('user.profile') }}" class="btn w-100" style="background:#6c8cff; color:#fff;">
                        My Profile
                    </a>
                    <a href="{{ route('logout') }}" class="btn w-100" style="background:#ff8c7a; color:#fff;">
                        Logout
                    </a>
                </div>
            </div>

            {{-- Team Nebula --}}
            <li id="teamNebulaLink" class="text-center mb-3" style="opacity: 0.8; font-size: 13px;">
                <a href="{{ route('team.phase.index') }}"
                   class="text-decoration-none d-inline-block py-1 px-2 rounded
                          {{ Route::currentRouteName() == 'team.phase.index' ? 'bg-light text-primary fw-semibold shadow-sm' : 'text-muted' }}"
                   style="transition: all 0.3s;">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
