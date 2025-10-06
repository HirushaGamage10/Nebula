<div>
    @php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';
    @endphp
    <div class="brand-logo d-flex align-items-center justify-content-center py-3">
        <a href="/" class="text-nowrap logo-img">
            <img src="{{ asset('images/logos/nebula.png') }}" alt="Nebula" width="180">
        </a>
    </div>
    <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
        <ul id="sidebarnav">
            {{-- HOME --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">HOME</span>
            </li>
            @if(RoleHelper::hasPermission($role, 'dashboard'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'dashboard' ? 'active' : '' }}" href="{{ route('dashboard') }}">
                        <span><i class="ti ti-layout-dashboard"></i></span>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>
            @endif

            {{-- USER MANAGEMENT --}}
            @if($role == 'Program Administrator (level 01)' || $role == 'Developer')
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">USER MANAGEMENT</span>
            </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'create.user' ? 'active' : '' }}" href="{{ route('create.user') }}">
                        <span><i class="ti ti-user"></i></span>
                        <span class="hide-menu">Create User</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'dgm.user.management' ? 'active' : '' }}" href="{{ route('dgm.user.management') }}">
                        <span><i class="ti ti-users"></i></span>
                        <span class="hide-menu">User Management</span>
                    </a>
                </li>
            @endif

            {{-- ======================= STUDENT MANAGEMENT ======================= --}}
@if(
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
)
    <li class="nav-small-cap">
        <span class="nav-small-cap-text">STUDENT MANAGEMENT</span>
    </li>
@endif

{{-- 🔹 Registrations --}}
@if(RoleHelper::hasPermission($role, 'student.registration'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'student.registration' ? 'active' : '' }}"
           href="{{ route('student.registration') }}">
            <span><i class="ti ti-user"></i></span>
            <span class="hide-menu">Student Registration</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'course.registration'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'course.registration' ? 'active' : '' }}"
           href="{{ route('course.registration') }}">
            <span><i class="ti ti-notebook"></i></span>
            <span class="hide-menu">Course Registration</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'eligibility.registration'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'eligibility.registration' ? 'active' : '' }}"
           href="{{ route('eligibility.registration') }}">
            <span><i class="ti ti-cards"></i></span>
            <span class="hide-menu">Eligibility & Registration</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Student Information --}}
@if(RoleHelper::hasPermission($role, 'student.other.information'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'student.other.information' ? 'active' : '' }}"
           href="{{ route('student.other.information') }}">
            <span><i class="ti ti-layout"></i></span>
            <span class="hide-menu">Student Other Information</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'student.list'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'student.list' ? 'active' : '' }}"
           href="{{ route('student.list') }}">
            <span><i class="ti ti-menu"></i></span>
            <span class="hide-menu">Student Lists</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'student.profile'))
    <li class="sidebar-item">
        @php
            $user = auth()->user();
            $studentProfileUrl = isset($user->student_id) && $user->student_id
                ? route('student.profile', ['studentId' => $user->student_id])
                : route('student.profile', ['studentId' => 0]);
        @endphp
        <a class="sidebar-link {{ Route::currentRouteName() == 'student.profile' ? 'active' : '' }}"
           href="{{ $studentProfileUrl }}">
            <span><i class="ti ti-id"></i></span>
            <span class="hide-menu">Student Profile</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Exams & Results --}}
@if(RoleHelper::hasPermission($role, 'exam.results') || RoleHelper::hasPermission($role, 'student.exam.result.management'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'student.exam.result.management' ? 'active' : '' }}"
           href="{{ route('student.exam.result.management') }}">
            <span><i class="ti ti-file"></i></span>
            <span class="hide-menu">Add Exam Result</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'exam.results') || RoleHelper::hasPermission($role, 'exam.results.view.edit'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'exam.results.view.edit' ? 'active' : '' }}"
           href="{{ route('exam.results.view.edit') }}">
            <span><i class="ti ti-edit"></i></span>
            <span class="hide-menu">View & Edit Results</span>
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

@if(RoleHelper::hasPermission($role, 'repeat.students.payment'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'repeat.payment.index' ? 'active' : '' }}"
           href="{{ route('repeat.payment.index') }}">
            <span><i class="ti ti-currency-dollar"></i></span>
            <span class="hide-menu">Repeat Payment Plan</span>
        </a>
    </li>
@endif


{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Attendance --}}
@if(RoleHelper::hasPermission($role, 'attendance'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'attendance' ? 'active' : '' }}"
           href="{{ route('attendance') }}">
            <span><i class="ti ti-id"></i></span>
            <span class="hide-menu">Attendance</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'overall.attendance'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'overall.attendance' ? 'active' : '' }}"
           href="{{ route('overall.attendance') }}">
            <span><i class="ti ti-id"></i></span>
            <span class="hide-menu">Overall Attendance</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Developer Only --}}
@if(auth()->user() && (auth()->user()->role === 'Developer' || (isset(auth()->user()->user_role) && auth()->user()->user_role === 'Developer')))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'uh.index.page' ? 'active' : '' }}"
           href="{{ route('uh.index.page') }}">
            <span><i class="ti ti-list-numbers"></i></span>
            <span class="hide-menu">External Institute IDs</span>
        </a>
    </li>
@endif


            {{-- STUDENT CLEARANCE --}}
            @if(
                RoleHelper::hasPermission($role, 'all.clearance') ||
                RoleHelper::hasPermission($role, 'library.clearance') ||
                RoleHelper::hasPermission($role, 'hostel.clearance.form.management') ||
                RoleHelper::hasPermission($role, 'project.clearance.management')
            )
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">STUDENT CLEARANCE</span>
            </li>
            @endif
            @if(RoleHelper::hasPermission($role, 'all.clearance'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'all.clearance.management' ? 'active' : '' }}" href="{{ route('all.clearance.management') }}">
                        <span><i class="ti ti-clipboard"></i></span>
                        <span class="hide-menu">All Clearance</span>
                    </a>
                </li>
            @endif
            @if(RoleHelper::hasPermission($role, 'library.clearance'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'library.clearance' ? 'active' : '' }}" href="{{ route('library.clearance') }}">
                        <span><i class="ti ti-clipboard"></i></span>
                        <span class="hide-menu">Library Clearance</span>
                    </a>
                </li>
            @endif
            @if(RoleHelper::hasPermission($role, 'hostel.clearance.form.management'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'hostel.clearance.form.management' ? 'active' : '' }}" href="{{ route('hostel.clearance.form.management') }}">
                        <span><i class="ti ti-note"></i></span>
                        <span class="hide-menu">Hostel Clearance</span>
                    </a>
                </li>
            @endif
            @if(RoleHelper::hasPermission($role, 'project.clearance.management'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'project.clearance.management' ? 'active' : '' }}" href="{{ route('project.clearance.management') }}">
                        <span><i class="ti ti-briefcase"></i></span>
                        <span class="hide-menu">Project Clearance</span>
                    </a>
                </li>
            @endif

             @if(RoleHelper::hasPermission($role, 'payment.clearance'))
                <li class="nav-small-cap">
                    <span class="nav-small-cap-text">FINANCIAL MANAGEMENT</span>
                </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'payment.clearance' ? 'active' : '' }}" href="{{ route('payment.clearance') }}">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Payment Clearance</span>
                    </a>
                </li>
            @endif

            {{-- ======================= ACADEMIC MANAGEMENT ======================= --}}
@if(
    RoleHelper::hasPermission($role, 'module.management') ||
    RoleHelper::hasPermission($role, 'module.creation') ||
    RoleHelper::hasPermission($role, 'course.management') ||
    RoleHelper::hasPermission($role, 'intake.create') ||
    RoleHelper::hasPermission($role, 'semester.create') ||
    RoleHelper::hasPermission($role, 'semester.registration') ||
    RoleHelper::hasPermission($role, 'timetable')
)
    <li class="nav-small-cap">
        <span class="nav-small-cap-text">ACADEMIC MANAGEMENT</span>
    </li>
@endif

{{-- 🔹 Setup & Creation --}}
@if($role == 'Developer' || $role == 'Program Administrator (level 02)' || RoleHelper::hasPermission($role, 'module.creation'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'module.creation' ? 'active' : '' }}"
           href="{{ route('module.creation') }}">
            <span><i class="ti ti-plus"></i></span>
            <span class="hide-menu">Module Creation</span>
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

@if(RoleHelper::hasPermission($role, 'intake.create'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'intake.create' ? 'active' : '' }}"
           href="{{ route('intake.create') }}">
            <span><i class="ti ti-pencil"></i></span>
            <span class="hide-menu">Intake Creation</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Semester Lifecycle --}}
@if(RoleHelper::hasPermission($role, 'semester.create'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'semesters.create' ? 'active' : '' }}"
           href="{{ route('semesters.create') }}">
            <span><i class="ti ti-calendar"></i></span>
            <span class="hide-menu">Semester Creation</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'semesters.index' ? 'active' : '' }}"
           href="{{ route('semesters.index') }}">
            <span><i class="ti ti-list"></i></span>
            <span class="hide-menu">Semester Management</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'semester.registration'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'semester.registration' ? 'active' : '' }}"
           href="{{ route('semester.registration') }}">
            <span><i class="ti ti-user-check"></i></span>
            <span class="hide-menu">Semester Registration</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Operations --}}
@if(RoleHelper::hasPermission($role, 'module.management'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'module.management' ? 'active' : '' }}"
           href="{{ route('module.management') }}">
            <span><i class="ti ti-briefcase"></i></span>
            <span class="hide-menu">Module Management</span>
        </a>
    </li>
@endif

@if(RoleHelper::hasPermission($role, 'timetable'))
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'timetable.show' ? 'active' : '' }}"
           href="{{ route('timetable.show') }}">
            <span><i class="ti ti-calendar"></i></span>
            <span class="hide-menu">Timetable</span>
        </a>
    </li>
@endif

{{-- Divider (light) --}}
<li><hr class="my-2 border-gray-200 opacity-30"></li>

{{-- 🔹 Developer Only --}}
@if($role === 'Developer')
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'reporting.dashboard' ? 'active' : '' }}"
           href="{{ route('reporting.dashboard') }}">
            <span><i class="ti ti-chart-bar"></i></span>
            <span class="hide-menu">Reporting</span>
        </a>
    </li>
    <li class="sidebar-item">
        <a class="sidebar-link {{ Route::currentRouteName() == 'data.export.import' ? 'active' : '' }}"
           href="{{ route('data.export.import') }}">
            <span><i class="ti ti-download"></i></span>
            <span class="hide-menu">Data Export/Import</span>
        </a>
    </li>
@endif


            @php
            // Guard against nulls and keep conditions readable
            $canPaymentPlan   = RoleHelper::hasPermission($role ?? null, 'payment.plan');
            $canPayment       = RoleHelper::hasPermission($role ?? null, 'payment');
            $canLatePayment   = RoleHelper::hasPermission($role ?? null, 'late.payment');
            $canPaymentDisc   = RoleHelper::hasPermission($role ?? null, 'payment.discounts');

            // Developer flag (supports both role fields)
            $user = auth()->user();
            $isDev = $user && (
                ($user->role ?? null) === 'Developer' ||
                ($user->user_role ?? null) === 'Developer'
            );

            $showFinancialCap = $canPaymentPlan || $canPayment || $canLatePayment || $canPaymentDisc || $isDev;
        @endphp

        {{-- ======================= FINANCIAL ======================= --}}
        @if($showFinancialCap)
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>
        @endif

        {{-- 🔹 Payment Plans (View + Create) --}}
        @if($canPaymentPlan)
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.plan.index') ? 'active' : '' }}"
                href="{{ route('payment.plan.index') }}">
                    <span><i class="ti ti-cash"></i></span>
                    <span class="hide-menu">Payment Plans</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.plan') ? 'active' : '' }}"
                href="{{ route('payment.plan') }}">
                    <span><i class="ti ti-plus"></i></span>
                    <span class="hide-menu">Create Payment Plan</span>
                </a>
            </li>
        @endif

        {{-- Divider (light) --}}
        <li><hr class="my-2 border-gray-200 opacity-30"></li>
        {{-- 🔹 Payment Dashboard / Summary --}}
        <li class="sidebar-item">
            <a class="sidebar-link {{ request()->routeIs('payment.summary') ? 'active' : '' }}"
            href="{{ route('payment.summary') }}">
                <span><i class="ti ti-chart-pie"></i></span>
                <span class="hide-menu">Payment Dashboard</span>
            </a>
        </li>
        {{-- 🔹 Payments & Discounts --}}
        @if($canPayment)
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.index') ? 'active' : '' }}"
                href="{{ route('payment.index') }}">
                    <span><i class="ti ti-credit-card"></i></span>
                    <span class="hide-menu">Payments</span>
                </a>
            </li>

            {{-- 🔹 Miscellaneous Payments --}}
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('misc.payment.index') ? 'active' : '' }}"
                href="{{ route('misc.payment.index') }}">
                    <span><i class="ti ti-wallet"></i></span>
                    <span class="hide-menu">Miscellaneous Payment</span>
                </a>
            </li>

            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.showDownloadPage') ? 'active' : '' }}"
                href="{{ route('payment.showDownloadPage') }}">
                    <span><i class="ti ti-file-download"></i></span>
                    <span class="hide-menu">Payment Statement</span>
                </a>
            </li>
        @endif

        @if($isDev)
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('payment.discount.page') ? 'active' : '' }}"
                href="{{ route('payment.discount.page') }}">
                    <span><i class="ti ti-discount"></i></span>
                    <span class="hide-menu">Payment Discount</span>
                </a>
            </li>
        @endif

        {{-- Divider (light) --}}
        <li><hr class="my-2 border-gray-200 opacity-30"></li>

        {{-- 🔹 Late Payments --}}
        @if($canLatePayment)
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('late.payment.index') ? 'active' : '' }}"
                href="{{ route('late.payment.index') }}">
                    <span><i class="ti ti-clock"></i></span>
                    <span class="hide-menu">Late Payment</span>
                </a>
            </li>
            <li class="sidebar-item">
                <a class="sidebar-link {{ request()->routeIs('latefee.approval.index') ? 'active' : '' }}"
                href="{{ route('latefee.approval.index') }}">
                    <span><i class="ti ti-currency-dollar"></i></span>
                    <span class="hide-menu">Late Fee Approval</span>
                </a>
            </li>
        @endif

            {{-- SPECIAL APPROVAL --}}
            @if(RoleHelper::hasPermission($role, 'special.approval'))
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">SPECIAL APPROVAL</span>
            </li>
                <li class="sidebar-item">
                    <a class="sidebar-link {{ Route::currentRouteName() == 'special.approval.list' ? 'active' : '' }}" href="{{ route('special.approval.list') }}">
                        <span><i class="ti ti-check"></i></span>
                        <span class="hide-menu">Special Approval</span>
                    </a>
                </li>
            @endif

            <hr>
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="{{ route('user.profile') }}" class="btn w-100" style="background-color: #6c8cff; color: #fff; font-weight: 500;">My Profile</a>
                    <a href="{{ route('logout') }}" class="btn w-100" style="background-color: #ff8c7a; color: #fff; font-weight: 500;">Logout</a>
                </div>
            </div>
        </ul>
    </nav>
</div>
