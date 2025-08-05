<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<!-- ϥϙϜϞϧϰαα -->

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo $__env->yieldContent('title', 'NEBULA'); ?></title>
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> <!-- CSRF Token -->
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/logos/favicon.jpg')); ?>" />

    <!-- Tabler Icons CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/icons/tabler-icons/tabler-icons.css')); ?>">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">

    <!-- CSS -->
    <link href="<?php echo e(asset('css/styles.min.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.min.css')); ?>">

    <!-- JS -->
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- <script src="<?php echo e(asset('js/app.js')); ?>"></script> -->
    <script src="<?php echo e(asset('libs/jquery/dist/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/sidebarmenu.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/simplebar/dist/simplebar.js')); ?>"></script>
    <style>
        body {
            background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E') no-repeat center center fixed;
            background-size: cover;
            width: 100%;
            height: 100vh;
        }

        body.loaded {
            background-image: url('<?php echo e(asset('images/backgrounds/nebula.jpg')); ?>');
        }

        .navbar {
            box-shadow: 0 8px 8px -8px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu-outline-shadow {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        /* Apply the class to your dropdown menu */
        .dropdown-menu.dropdown-menu-end.dropdown-menu-animate-up.bg-light-primary.outline-shadow {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }
    </style>

    <script async defer>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.querySelector('body');
            body.classList.add('loaded');
        });
    </script>
</head>

<body class="d-flex flex-column">
    

    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <?php echo $__env->make('components.sidebar', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!-- End Sidebar scroll-->
        </aside>
        <div x-data x-init="
    $nextTick(() => {
        const sidebar = document.querySelector('.scroll-sidebar');
        const activeLink = sidebar?.querySelector('.sidebar-link.active');
        if (activeLink && sidebar) {
            activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    })
"><script>
    document.addEventListener('DOMContentLoaded', () => {
        const sidebar = document.querySelector('.scroll-sidebar');
        const activeLink = sidebar?.querySelector('.sidebar-link.active');
        if (activeLink && sidebar) {
            activeLink.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    });
</script>


        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper d-flex flex-column min-vh-100">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block d-xl-none">
                            <a class="nav-link sidebartoggler nav-icon-hover" id="headerCollapse"
                                href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="navbar-collapse justify-content-end px-0" id="navbarNav">
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <div class="user-name">
                                <li class="nav-item mr-10" id="greeting"></li>
                            </div>
                            <li class="nav-item">
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up"
                                    aria-labelledby="drop1">
                                    <div class="message-body">
                                        <a href="javascript:void(0)"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="./authentication-login.html"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover" href="javascript:void(0)" id="drop2"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                    <img src="<?php echo e(asset('images/profile/user-1.jpg')); ?>" alt=""
                                        width="35" height="35" class="rounded-circle">
                                </a>
                                <div class="dropdown-menu dropdown-menu-end dropdown-menu-animate-up outline-shadow"
                                    aria-labelledby="drop2">
                                    <div class="message-body">
                                        <a href="<?php echo e(route('user.profile')); ?>"
                                            class="d-flex align-items-center gap-2 dropdown-item">
                                            <i class="ti ti-user fs-6"></i>
                                            <p class="mb-0 fs-3">My Profile</p>
                                        </a>
                                        <a href="<?php echo e(route('logout')); ?>"
                                            class="btn btn-outline-primary mx-3 mt-2 d-block">Logout</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid flex-grow-1">
                <?php echo $__env->yieldContent('content'); ?>
            </div>
            <div class="footer-wrapper mt-auto">
                <footer class="footer bg-dark text-light text-center py-3">
                    <div class="container">
                        <div class="row">
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <!-- First Column - Left Blank -->
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4 mb-3 mb-md-0 align-items-center">
                                <!-- Second Column - Text -->
                                <p id="footer-year" class="mb-0">&copy; <span id="current-year">2024</span> Nebula. All
                                    rights reserved.</p>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <!-- Third Column - Left Blank -->
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Get the current time
            var currentTime = new Date();
            var currentHour = currentTime.getHours();
            var greeting;

            // Define the greeting based on the current time
            if (currentHour >= 5 && currentHour < 12) {
                greeting = 'Good morning';
            } else if (currentHour >= 12 && currentHour < 18) {
                greeting = 'Good afternoon';
            } else {
                greeting = 'Good evening ';
            }

            // Get the user's name
            var userName = "<?php echo e(auth()->check() ? auth()->user()->name : ''); ?>";

            // Display the greeting and user's name
            if (userName) {
                document.getElementById("greeting").innerHTML = greeting + ", <b>" + userName + "</b>";
            }
        });
    </script>

    <script>
        // Global AJAX setup for CSRF token
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>

    <?php echo $__env->yieldContent('scripts'); ?>
    <?php echo $__env->yieldPushContent('scripts'); ?>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const courseManagementLink = document.getElementById('course-management-link');
            if (courseManagementLink && window.location.pathname.startsWith('/course-management')) {
                courseManagementLink.classList.add('active');
            }
        });
    </script>
    <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
</body>
</html>

<?php /**PATH E:\JOB\Projects\Nebula\resources\views/inc/app.blade.php ENDPATH**/ ?>