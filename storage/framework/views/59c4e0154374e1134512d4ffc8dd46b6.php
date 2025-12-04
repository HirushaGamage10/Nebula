<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title>NEBULA | Sign In</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?php echo e(asset('images/logos/nebula.png')); ?>">
    <link rel="shortcut icon" type="image/png" href="<?php echo e(asset('images/logos/nebula.png')); ?>">

    <!-- CSS -->
    <link href="<?php echo e(asset('css/styles.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/login.css')); ?>" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

    <!-- JS -->
    <script src="<?php echo e(asset('libs/jquery/dist/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('libs/bootstrap/dist/js/bootstrap.bundle.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/login.js')); ?>"></script>
</head>

<body>
    <div class="page-wrapper" id="main-wrapper">

        <div class="position-relative radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="row justify-content-center w-100">
                <div class="col-md-8 col-lg-6 col-xxl-3">

                    <div class="card mb-0">
                        <div class="card-body">

                            <a href="./" class="text-center d-block py-3 w-100">
                                <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula" class="img-fluid" loading="lazy">
                            </a>

                            <?php if(($errors ?? collect())->any()): ?>
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <li><?php echo e($error); ?></li>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </ul>
                                </div>
                            <?php endif; ?>

                            <form id="loginForm" method="POST" action="<?php echo e(route('login.authenticate')); ?>" class="pt-3">
                                <?php echo csrf_field(); ?>

                                <div class="form-group mb-3">
                                    <label for="email">Username</label>
                                    <input type="email"
                                        id="email"
                                        name="email"
                                        class="form-control form-control-lg <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        placeholder="Enter your username"
                                        value="<?php echo e(old('email')); ?>"
                                        autocomplete="email"
                                        required>

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
                                </div>

                                <div class="form-group mb-4">
                                    <label for="password">Password</label>

                                    <div class="input-group">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="form-control form-control-lg <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                               placeholder="Enter your password"
                                               autocomplete="current-password"
                                               required>

                                        <span class="input-group-text btn-password" id="togglePassword">
                                            <i id="togglePasswordIcon" class="bi bi-eye"></i>
                                        </span>
                                    </div>

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
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2 fs-4 rounded-2">
                                    Sign In
                                </button>

                            </form>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <footer class="footer bg-dark text-light text-center py-3">
        <p class="mb-0">&copy; <span id="currentYear"></span> Nebula. All rights reserved.</p>
    </footer>
</body>

</html>
<?php /**PATH D:\SLT\Welisara\Nebula\resources\views/login.blade.php ENDPATH**/ ?>