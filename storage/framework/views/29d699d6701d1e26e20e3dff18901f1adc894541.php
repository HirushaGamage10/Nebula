

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

                <!-- 🔹 Mobile Phone -->
                <div class="row mb-3">
                    <label for="mobilePhone" class="col-sm-2 col-form-label">Mobile Phone No<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select class="form-select country-code-select" name="mobileCountryCode" style="max-width: 180px;">
                                <!-- Will auto-populate via JS -->
                            </select>
                            <input type="tel" class="form-control" id="mobilePhone" name="mobilePhone" placeholder="Enter phone number" required>
                        </div>
                            <div id="mobilePhoneError" class="text-danger" style="display:none;">Please enter a valid mobile number (7–15 digits). Include the country code if available, e.g. +94XXXXXXXXX.</div>
                    </div>
                </div>

                <!-- 🔹 Home Phone -->
                <div class="row mb-3">
                    <label for="homePhone" class="col-sm-2 col-form-label">Home Phone No</label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select class="form-select country-code-select" name="homeCountryCode" style="max-width: 180px;">
                                <!-- Will auto-populate via JS -->
                            </select>
                            <input type="tel" class="form-control" id="homePhone" name="homePhone" placeholder="Enter home phone number">
                        </div>
                            <div id="homePhoneError" class="text-danger" style="display:none;">Please enter a valid home phone number or leave it blank.</div>
                    </div>
                </div>

                <!-- 🔹 WhatsApp Number -->
                <div class="row mb-3">
                    <label for="whatsappPhone" class="col-sm-2 col-form-label">WhatsApp Number<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select class="form-select country-code-select" name="whatsappCountryCode" style="max-width: 180px;">
                                <!-- Will auto-populate via JS -->
                            </select>
                            <input type="tel" class="form-control" id="whatsappPhone" name="whatsappPhone" placeholder="Enter WhatsApp number" required>
                        </div>
                            <div id="whatsappNumberError" class="text-danger" style="display:none;">Please enter a valid WhatsApp number (7–15 digits). Include +country code if applicable.</div>
                    </div>
                </div>

                <hr class="my-4">

                
                <h5 class="mb-3">Academic Qualifications</h5>
                <div class="row mb-3">
                    <label for="pending_result" class="col-sm-2 col-form-label">O/L Result Pending?<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select id="pending_result" name="pending_result" class="form-select" required>
                            <option value="" selected disabled>Select an Option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>

                <div id="ol_details_container" style="display: none;">
                    <div class="accordion" id="olAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="olHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOL" aria-expanded="true" aria-controls="collapseOL">
                                    O/L Exam Details
                                </button>
                            </h2>
                            <div id="collapseOL" class="accordion-collapse collapse show" aria-labelledby="olHeading" data-bs-parent="#olAccordion">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <label for="ol_index_no" class="col-sm-2 col-form-label">Index No.<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="ol_index_no" name="ol_index_no" placeholder="XXXXXXXXXX">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="ol_exam_type" class="col-sm-2 col-form-label">Exam Type<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_exam_type" name="ol_exam_type">
                                                <option selected disabled>Select an Exam Type</option>
                                                <?php $__currentLoopData = $examTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($examType); ?>"><?php echo e($examType); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <div id="olExamTypeOtherContainer" class="mt-2" style="display: none;">
                                                <input type="text" class="form-control" id="olExamTypeOther" name="olExamTypeOther" placeholder="Please specify the exam type">
                                            </div>
                                        </div>
                                        <label for="ol_exam_year" class="col-sm-2 col-form-label text-end">Exam Year<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" id="ol_exam_year" name="ol_exam_year" placeholder="eg. 2000">
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-end">
                                        <label class="col-sm-2 col-form-label">Result<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_subject_select">
                                                <option selected disabled>Select a Subject</option>
                                                
                                            </select>
                                            <input type="text" class="form-control mt-2" id="ol_subject_other_input" name="ol_subject_other" placeholder="Enter subject name" style="display:none;">
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_result_select">
                                                <option selected disabled>Select a Result</option>
                                                <option>A</option>
                                                <option>B</option>
                                                <option>C</option>
                                                <option>S</option>
                                                <option>F</option>
                                            </select>
                                            <div id="olResultError" class="text-danger mt-1" style="display:none;">
                                                This subject is already added.
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary w-100" id="ol_add_btn">Add</button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-10 offset-sm-2">
                                            <table class="table table-bordered">
                                                <thead class="bg-primary text-white">
                                                    <tr>
                                                        <th>O/L Subject</th>
                                                        <th>Result</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="ol_certificate" class="col-sm-2 col-form-label">O/L Certificate<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control" id="ol_certificate" name="ol_certificate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="al_pending_question_container" style="display: none;" class="row mb-3">
                    <label for="al_pending_result" class="col-sm-2 col-form-label">A/L Results Pending?<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select id="al_pending_result" name="al_pending_result" class="form-select">
                            <option value="" selected disabled>Select an Option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                
                <div id="al_details_container" style="display: none;">
                     <div class="accordion" id="alAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="alHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAL" aria-expanded="true" aria-controls="collapseAL">
                                    A/L Exam Details
                                </button>
                            </h2>
                            <div id="collapseAL" class="accordion-collapse collapse show" aria-labelledby="alHeading" data-bs-parent="#alAccordion">
                                <div class="accordion-body">
                                     <div class="row mb-3">
                                        <label for="al_index_no" class="col-sm-2 col-form-label">Index No.<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="al_index_no" name="al_index_no" placeholder="XXXXXXXXXX">
                                        </div>
                                    </div>
                                     <div class="row mb-3">
                                        <label for="al_exam_type" class="col-sm-2 col-form-label">Exam Type<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="al_exam_type" name="al_exam_type">
                                                <option selected disabled>Select an Exam Type</option>
                                                <?php $__currentLoopData = $examTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $examType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                <option value="<?php echo e($examType); ?>"><?php echo e($examType); ?></option>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </select>
                                            <div id="alExamTypeOtherContainer" class="mt-2" style="display: none;">
                                                <input type="text" class="form-control" id="alExamTypeOther" name="alExamTypeOther" placeholder="Please specify the exam type">
                                            </div>
                                        </div>
                                        <label for="al_exam_year" class="col-sm-2 col-form-label text-end">Exam Year<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" id="al_exam_year" name="al_exam_year" placeholder="eg. 2000">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="al_stream" class="col-sm-2 col-form-label">A/L Stream<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                             <select class="form-select" id="al_stream" name="al_stream">
                                                <option selected disabled>Select an A/L Stream</option>
                                                <option value="Physical Science">Physical Science</option>
                                                <option value="Biological Science">Biological Science</option>
                                                <option value="Commerce">Commerce</option>
                                                <option value="Arts">Arts</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>

                
                <h5 class="mb-3 mt-4">Program & Intake</h5>
                <div class="row mb-3">
                    <label for="course_id" class="col-sm-2 col-form-label">Course<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course_id" name="course_id" required>
                            <option value="" selected disabled>Select a Course</option>
                            <?php $__currentLoopData = $btecCourses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course['course_id']); ?>"><?php echo e($course['course_name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="intake_id" class="col-sm-2 col-form-label">Intake<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intake_id" name="intake_id" required>
                            <option value="" selected disabled>Select an Intake</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="mode" class="col-sm-2 col-form-label">Mode<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="mode" name="mode" required>
                            <option value="" selected disabled>Select Mode</option>
                            <option value="Onsite">Onsite</option>
                            <option value="Online">Online</option>
                        </select>
                    </div>
                </div>

                <hr class="my-4">

                
                <div class="row mb-3">
                    <label for="campus" class="col-sm-2 col-form-label">Campus<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="campus" name="campus" required>
                            <option value="" selected disabled>Select Campus</option>
                            <?php $__currentLoopData = $campuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $campus): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($campus['id']); ?>"><?php echo e($campus['campus_name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="preferred_contact" class="col-sm-2 col-form-label">Preferred Contact Method</label>
                    <div class="col-sm-10">
                        <select class="form-select" id="preferred_contact" name="preferred_contact">
                            <option value="email">Email</option>
                            <option value="phone">Phone</option>
                        </select>
                    </div>
                </div>

                <div class="mb-4 text-center">
                    <button type="submit" class="btn btn-primary">Submit Registration</button>
                </div>

            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/student_management/student_registration.blade.php ENDPATH**/ ?>