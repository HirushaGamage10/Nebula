<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>"> <!-- CSRF Token -->
    <title>NEBULA | Sign In</title>
    <link href="<?php echo e(asset('css/styles.min.css')); ?>" rel="stylesheet">
    <!-- CSS -->
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.min.css')); ?>">

    <!-- JS -->
    <script src="<?php echo e(asset('js/app.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/jquery/dist/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')); ?>"></script>

    <style>
    body {
        background: url('data:image/svg+xml,%3Csvg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1 1"%3E%3C/svg%3E')
        no-repeat center center fixed;
        background-size: cover;
        width: 100%;
        height: 100vh;
    }

    body.loaded {
        background-image: url('<?php echo e(asset('images/backgrounds/nebula.jpg')); ?>');
    }

    /* Error styling */
    .invalid-field {
        border-color: #dc3545 !important;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
    }

    .error-message {
        color: #dc3545;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    .alert-danger {
        background-color: #f8d7da;
        border-color: #f5c6cb;
        color: #721c24;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
        border-radius: 0.375rem;
    }

    .form-control.is-invalid {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25);
    }

    .form-control.is-valid {
        border-color: #198754;
        box-shadow: 0 0 0 0.2rem rgba(25, 135, 84, 0.25);
    }
    </style>

    <script async defer>
        document.addEventListener('DOMContentLoaded', function() {
            const body = document.querySelector('body');
            body.classList.add('loaded');

            // Clear error styling when user starts typing
            const emailInput = document.getElementById('email');
            const passwordInput = document.getElementById('password');

            emailInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                this.classList.remove('invalid-field');
            });

            passwordInput.addEventListener('input', function() {
                this.classList.remove('is-invalid');
                this.classList.remove('invalid-field');
            });

            // Form validation
            const loginForm = document.getElementById('loginForm');
            loginForm.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Basic client-side validation
                if (!emailInput.value.trim()) {
                    emailInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!passwordInput.value.trim()) {
                    passwordInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                if (!isValid) {
                    e.preventDefault();
                }
            });
        });
    </script>
</head>

<body>
    <!-- Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-3">
                        <div class="card mb-0">
                            <div class="card-body">
                                <a href="./index.html" class="text-nowrap logo-img text-center d-block py-3 w-100">
                                    <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula"
                                        class="img-fluid" loading="lazy">
                                </a>
                                
                                <!-- Display general errors -->
                                <?php if(($errors ?? collect())->any()): ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <li><?php echo e($error); ?></li>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form class="pt-3" method="POST" action="<?php echo e(route('login.authenticate')); ?>" id="loginForm">
                                    <?php echo csrf_field(); ?>
                                    <div class="form-group mb-3">
                                        <label for="email" class="form-label">Username</label>
                                        <input type="email" 
                                               class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php echo e(($errors->any() && !$errors->has('email') && !$errors->has('password')) ? 'is-invalid' : ''); ?>" 
                                               name="email"
                                               id="email" 
                                               placeholder="Enter your username" 
                                               value="<?php echo e(old('email')); ?>"
                                               required 
                                               autocomplete="email">
                                        <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="error-message"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <?php if($errors->any() && !$errors->has('email') && !$errors->has('password')): ?>
                                            <div class="error-message"><?php echo e($errors->first()); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div class="form-group mb-4">
                                        <label for="password" class="form-label">Password</label>
                                        <input type="password" 
                                               class="form-control form-control-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?> <?php echo e(($errors->any() && !$errors->has('email') && !$errors->has('password')) ? 'is-invalid' : ''); ?>" 
                                               name="password"
                                               id="password" 
                                               placeholder="Enter your password" 
                                               required 
                                               autocomplete="current-password">
                                        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                            <div class="error-message"><?php echo e($message); ?></div>
                                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                        <?php if($errors->any() && !$errors->has('email') && !$errors->has('password')): ?>
                                            <div class="error-message"><?php echo e($errors->first()); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <button type="submit" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2"
                                        id="signInButton">Sign In</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="footer bg-dark text-light text-center py-3">
        <div class="container">
            <div class="row">
                <div class="col-lg-6 mx-auto">
                    <p id="copyright" class="mb-0">&copy; <span id="currentYear"></span> Nebula. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </footer>
</body>

</html>
<?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/login.blade.php ENDPATH**/ ?>