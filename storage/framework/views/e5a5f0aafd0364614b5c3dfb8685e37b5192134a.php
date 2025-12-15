

<?php $__env->startSection('title', 'NEBULA | Student Registration'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
        <h2 class="text-center mb-4">Student Registration</h2>
            <hr>

            <div id="spinner-overlay" style="display:none;">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>

            <form id="registrationForm" action="<?php echo e(route('student.register')); ?>" method="POST" enctype="multipart/form-data" novalidate>
                <?php echo csrf_field(); ?>
                <div id="formErrorSummary" class="alert alert-danger d-none" role="alert"></div>
                
                
                <h5 class="mb-3">Personal Information</h5>
                
                <div class="row mb-3">
                    <label for="title" class="col-sm-2 col-form-label">Title<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="title" name="title" required>
                            <option selected disabled value="#">Select a Title</option>
                            <?php $__currentLoopData = $titles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $title): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($title['TitleID']); ?>"><?php echo e($title['TitleName']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="titleOtherContainer" class="mt-2" style="display: none;">
                            <input type="text" class="form-control" id="titleOther" name="titleOther" placeholder="Please specify your title">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="nameWithInitials" class="col-sm-2 col-form-label">Name with Initials<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nameWithInitials" name="nameWithInitials" placeholder="J. A. Smith" required>
                            <div id="nameError" class="text-danger" style="display: none;">Please enter a name using letters, periods (.) and spaces only.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="fullName" class="col-sm-2 col-form-label">Full Name<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="fullName" name="fullName" placeholder="John Adam Smith" required>
                            <div id="fullNameError" class="text-danger" style="display: none;">Please enter the full name using letters and spaces only.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="birthday" class="col-sm-2 col-form-label">Birthday<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="birthday" name="birthday" required>
                            <div id="birthdayError" class="text-danger" style="display: none;">Please choose a valid birth date (year should be between 1890 and the current year).</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="gender" class="col-sm-2 col-form-label">Gender<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="gender" name="gender" required>
                            <option selected disabled value="#">Select a Gender</option>
                            <?php $__currentLoopData = $genders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gender): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($gender['id']); ?>"><?php echo e($gender['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="idValue" class="col-sm-2 col-form-label">ID Value<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select class="form-select bg-primary text-white" id="identificationType" name="identificationType" style="flex: 0 0 150px;" required>
                                <?php $__currentLoopData = $idTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $idType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($idType['id']); ?>"><?php echo e($idType['id_type']); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <input type="text" class="form-control" id="idValue" name="idValue" placeholder="Enter ID value" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="address" class="col-sm-2 col-form-label">Address<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="address" name="address" placeholder="123 Main Street, City, Country" rows="3" required></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="email" class="col-sm-2 col-form-label">Email<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@example.com" required>
                            <div id="emailError" class="text-danger" style="display: none;">Please enter a valid email address (e.g., example@example.com).</div>
                    </div>
                </div>

                <!-- More fields omitted for brevity (copied from original) -->

                <div class="mb-4 text-center">
                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/registration/student_registration.blade.php ENDPATH**/ ?>