<div>
    @php
        use App\Helpers\RoleHelper;
        $role = auth()->user()->user_role ?? '';

        $canPaymentPlan   = RoleHelper::hasPermission($role, 'payment.plan');
        $canPayment       = RoleHelper::hasPermission($role, 'payment');
        $canLatePayment   = RoleHelper::hasPermission($role, 'late.payment');
        $isDev             = $role === 'Developer';
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

            {{-- FINANCIAL --}}
            <li class="nav-small-cap">
                <span class="nav-small-cap-text">FINANCIAL</span>
            </li>

            {{-- Payment Clearance --}}
            @if(RoleHelper::hasPermission($role, 'payment.clearance'))
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('payment.clearance') ? 'active' : '' }}"
                       href="{{ route('payment.clearance') }}">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Payment Clearance</span>
                    </a>
                </li>
            @endif

            {{-- Payment Plans --}}
            @if($canPaymentPlan)
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('payment.plan.index') ? 'active' : '' }}"
                       href="{{ route('payment.plan.index') }}">
                        <span><i class="ti ti-cash"></i></span>
                        <span class="hide-menu">Payment Plans</span>
                    </a>
                </li>
            @endif

            <li><hr class="my-2 opacity-30"></li>

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

            {{-- Payments --}}
            @if($canPayment)

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

            {{-- Developer Only --}}
            @if($isDev)
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('payment.discount.page') ? 'active' : '' }}"
                       href="{{ route('payment.discount.page') }}">
                        <span><i class="ti ti-discount"></i></span>
                        <span class="hide-menu">Payment Discount</span>
                    </a>
                </li>
            @endif

            <li><hr class="my-2 opacity-30"></li>

            {{-- Late Payments --}}
            @if($canLatePayment)
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('late.payment.index') ? 'active' : '' }}"
                       href="{{ route('late.payment.index') }}">
                        <span><i class="ti ti-clock"></i></span>
                        <span class="hide-menu">Late Payment</span>
                    </a>
                </li>
            @endif

            <br><br>

            <hr>

            {{-- Footer Buttons --}}
            <div class="px-3 pb-3">
                <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
                    <a href="{{ route('user.profile') }}" class="btn w-100 text-white" style="background:#6c8cff;">
                        My Profile
                    </a>
                    <a href="{{ route('logout') }}" class="btn w-100 text-white" style="background:#ff8c7a;">
                        Logout
                    </a>
                </div>
            </div>

            {{-- Team Nebula --}}
            <li class="text-center mb-3" style="opacity:0.8;font-size:13px;">
                <a href="{{ route('team.phase.index') }}"
                   class="text-decoration-none d-inline-block py-1 px-2 rounded
                   {{ request()->routeIs('team.phase.index') ? 'bg-light text-primary fw-semibold shadow-sm' : 'text-muted' }}">
                    © Team Nebula IT
                </a>
            </li>

        </ul>
    </nav>
</div>
