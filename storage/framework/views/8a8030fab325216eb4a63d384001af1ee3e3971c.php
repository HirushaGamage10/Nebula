<?php
    use App\Helpers\RoleHelper;
    $role = auth()->user()->user_role ?? '';
    $sections = config('menu.sections');
?>

<ul id="sidebarnav">
<?php $__currentLoopData = $sections; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $section): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <?php
        // Determine if any items in this section are visible to this user
        $visible = collect($section['items'])->contains(function($item) use ($role) {
            if(isset($item['roles'])) {
                return in_array($role, $item['roles']);
            }
            if(isset($item['permission'])) {
                return RoleHelper::hasPermission($role, $item['permission']);
            }
            return true;
        });
    ?>

    <?php if($visible): ?>
        <li class="nav-small-cap">
            <span class="nav-small-cap-text"><?php echo e($section['title']); ?></span>
        </li>

        <?php $__currentLoopData = $section['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $hasAccess = true;
                if(isset($item['roles'])) {
                    $hasAccess = in_array($role, $item['roles']);
                } elseif(isset($item['permission'])) {
                    $hasAccess = RoleHelper::hasPermission($role, $item['permission']);
                }
            ?>

            <?php if($hasAccess): ?>
                <li class="sidebar-item">
                    <?php if(isset($item['is_profile']) && $item['is_profile']): ?>
                        <?php
                            $user = auth()->user();
                            $studentId = $user->student_id ?? 0;
                            $url = route('student.profile', ['studentId' => $studentId]);
                            $active = request()->routeIs('student.profile');
                        ?>
                        <a class="sidebar-link <?php echo e($active ? 'active' : ''); ?>" href="<?php echo e($url); ?>">
                            <span><i class="<?php echo e($item['icon']); ?>"></i></span>
                            <span class="hide-menu"><?php echo e($item['label']); ?></span>
                        </a>
                    <?php else: ?>
                        <?php $active = request()->routeIs($item['route']) || Route::currentRouteName() == $item['route']; ?>
                        <a class="sidebar-link <?php echo e($active ? 'active' : ''); ?>" href="<?php echo e(route($item['route'])); ?>">
                            <span><i class="<?php echo e($item['icon']); ?>"></i></span>
                            <span class="hide-menu"><?php echo e($item['label']); ?></span>
                        </a>
                    <?php endif; ?>
                </li>
            <?php endif; ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<hr class="my-2 border-gray-200 opacity-30">

<li class="px-3 pb-3">
    <div class="bg-light rounded p-3 d-flex flex-column gap-2 align-items-center">
        <a href="<?php echo e(route('user.profile')); ?>" class="btn w-100" style="background-color: #6c8cff; color: #fff; font-weight: 500;">My Profile</a>
        <a href="<?php echo e(route('logout')); ?>" class="btn w-100" style="background-color: #ff8c7a; color: #fff; font-weight: 500;">Logout</a>
    </div>
</li>

<li id="teamNebulaLink" class="text-center mb-3" style="opacity: 0.8; font-size: 13px;">
    <a href="<?php echo e(route('team.phase.index')); ?>" class="text-decoration-none d-inline-block py-1 px-2 rounded <?php echo e(Route::currentRouteName() == 'team.phase.index' ? 'bg-light text-primary fw-semibold shadow-sm' : 'text-muted'); ?>" style="transition: all 0.3s;">© Team Nebula IT</a>
</li>
<?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/components/sidebar-menu.blade.php ENDPATH**/ ?>