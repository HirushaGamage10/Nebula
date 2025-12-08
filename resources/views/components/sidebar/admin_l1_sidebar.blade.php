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
            <img src="{{ asset('images/logos/nebula.png') }}" width="180" alt="Nebula">
        </a>
    </div>

    <!-- Sidebar -->
    <nav class="sidebar-nav scroll-sidebar" data-simplebar>
        <ul id="sidebarnav">

            {{-- HOME --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>

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

            {{-- USER MANAGEMENT --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">USER MANAGEMENT</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'create.user'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'create.user' ? 'active' : '' }}"
                   href="{{ route('create.user') }}">
                    <span><i class="ti ti-user"></i></span>
                    <span class="hide-menu">Create User</span>
                </a>
            </li>
            @endif

            @if(RoleHelper::hasPermission($role, 'user.management'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'dgm.user.management' ? 'active' : '' }}"
                   href="{{ route('dgm.user.management') }}">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">User Management</span>
                </a>
            </li>
            @endif


            <br>

            {{-- STUDENT MANAGEMENT --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'student.list'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.list' ? 'active' : '' }}"
                   href="{{ route('student.list') }}">
                    <span><i class="ti ti-menu"></i></span>
                    <span class="hide-menu">Student Lists</span>
                </a>
            </li>
            @endif

            {{-- Always visible --}}
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'students.view' ? 'active' : '' }}"
                   href="{{ route('students.view') }}">
                    <span><i class="ti ti-users"></i></span>
                    <span class="hide-menu">All Students View</span>
                </a>
            </li>

            @if(RoleHelper::hasPermission($role, 'student.other.information'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.other.information' ? 'active' : '' }}"
                   href="{{ route('student.other.information') }}">
                    <span><i class="ti ti-info-circle"></i></span>
                    <span class="hide-menu">Student Other Information</span>
                </a>
            </li>
            @endif

            @if(RoleHelper::hasPermission($role, 'student.profile'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'student.profile' ? 'active' : '' }}"
                   href="{{ route('student.profile', ['studentId' => auth()->user()->student_id ?? 0]) }}">
                    <span><i class="ti ti-id"></i></span>
                    <span class="hide-menu">Student Profile</span>
                </a>
            </li>
            @endif

            @if(RoleHelper::hasPermission($role, 'repeat.students.management'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'repeat.students.management' ? 'active' : '' }}"
                   href="{{ route('repeat.students.management') }}">
                    <span><i class="ti ti-refresh"></i></span>
                    <span class="hide-menu">Repeat Students</span>
                </a>
            </li>
            @endif

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

            {{-- CLEARANCE --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">CLEARANCE</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'all.clearance'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'all.clearance.management' ? 'active' : '' }}"
                   href="{{ route('all.clearance.management') }}">
                    <span><i class="ti ti-clipboard"></i></span>
                    <span class="hide-menu">All Clearance</span>
                </a>
            </li>
            @endif


            <br>

            {{-- ACADEMIC MANAGEMENT --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">ACADEMIC MANAGEMENT</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'module.management'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'module.management' ? 'active' : '' }}"
                   href="{{ route('module.management') }}">
                    <span><i class="ti ti-briefcase"></i></span>
                    <span class="hide-menu">Module Management</span>
                </a>
            </li>
            @endif

            @if(RoleHelper::hasPermission($role, 'course.management'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'course.management' ? 'active' : '' }}"
                   href="{{ route('course.management') }}">
                    <span><i class="ti ti-notebook"></i></span>
                    <span class="hide-menu">Course Management</span>
                </a>
            </li>
            @endif

            @if(RoleHelper::hasPermission($role, 'overall.attendance'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'overall.attendance' ? 'active' : '' }}"
                   href="{{ route('overall.attendance') }}">
                    <span><i class="ti ti-calendar"></i></span>
                    <span class="hide-menu">Overall Attendance</span>
                </a>
            </li>
            @endif


            <br>

            {{-- FINANCIAL --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            @if(RoleHelper::hasPermission($role, 'payment.dashboard'))
            <li class="sidebar-item">
                <a class="sidebar-link {{ Route::currentRouteName() == 'payment.summary' ? 'active' : '' }}"
                   href="{{ route('payment.summary') }}">
                    <span><i class="ti ti-chart-pie"></i></span>
                    <span class="hide-menu">Payment Dashboard</span>
                </a>
            </li>
            @endif


            <br><br>

            {{-- FOOTER --}}
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
