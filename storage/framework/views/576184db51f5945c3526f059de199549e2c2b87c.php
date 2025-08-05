

<?php $__env->startSection('title', 'NEBULA | Course Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
              <h2 class="text-center mb-4">Course Management</h2>
            <hr>
            <form id="courseForm">
                <?php echo csrf_field(); ?>
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location" required>
                            <option selected disabled value="">Choose a location...</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
              
                <div class="mb-3 row mx-3">
                    <label for="course_type" class="col-sm-2 col-form-label">Course Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course_type" name="course_type" required>
                            <option selected disabled value="">Choose course type...</option>
                            <option value="degree">Degree Program</option>
                            <option value="certificate">Certificate Program</option>
                        </select>
                    </div>
                </div>
              
                <!-- Degree Program Fields -->
                <div id="degree_program_fields" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="course_name" class="col-sm-2 col-form-label">Course Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="course_name" name="course_name" placeholder="Enter the course name" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="course_medium" class="col-sm-2 col-form-label">Course Medium <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="course_medium" name="course_medium" required>
                                <option selected disabled value="">Choose a medium...</option>
                                <option value="Sinhala">Sinhala</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                    </div>
                    <!-- Specialization Field (Degree Only) -->
                    <div class="mb-3 row mx-3 align-items-center">
                        <label class="col-sm-2 col-form-label">Specialization</label>
                        <div class="col-sm-10 d-flex align-items-center gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_specialization" id="specializationYes" value="yes">
                                <label class="form-check-label" for="specializationYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_specialization" id="specializationNo" value="no" checked>
                                <label class="form-check-label" for="specializationNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div id="specializationFields" style="display: none;">
                        <div class="mb-3 row mx-3 align-items-center">
                            <label class="col-sm-2 col-form-label">Specialization Name(s)</label>
                            <div class="col-sm-10" id="specializationInputs">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">
                                    <button type="button" class="btn btn-outline-secondary remove-specialization" style="display:none;">Remove</button>
                                </div>
                            </div>
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-sm btn-success" id="addSpecializationBtn">Add Another Specialization</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="conducted_by" class="col-sm-2 col-form-label">Conducted by <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="conducted_by" name="conducted_by" required>
                                <option selected disabled value="">Select who conducts</option>
                                <option value="SLT-MOBITEL Nebula Institute of Technology">SLT-MOBITEL Nebula Institute of Technology</option>
                                <option value="Pearson">Pearson</option>
                                <option value="University of Hertfordshire">University of Hertfordshire</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="other_conducted_by" name="other_conducted_by" placeholder="Please specify" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Duration <span class="text-danger">*</span></label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="duration_years" name="duration_years" placeholder="Years" min="0" required>
                            <input type="number" class="form-control" id="duration_months" name="duration_months" placeholder="Months" min="0" max="11" required>
                            <input type="number" class="form-control" id="duration_days" name="duration_days" placeholder="Days" min="0" max="30" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="no_of_semesters" class="col-sm-2 col-form-label">Semesters</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="no_of_semesters" name="no_of_semesters" placeholder="Enter the number of total semesters">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Training Period</label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="training_years" name="training_years" placeholder="Years" min="0">
                            <input type="number" class="form-control" id="training_months" name="training_months" placeholder="Months" min="0" max="11">
                            <input type="number" class="form-control" id="training_days" name="training_days" placeholder="Days" min="0" max="30">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="min_credits" class="col-sm-2 col-form-label">Minimum Credits</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="min_credits" name="min_credits" placeholder="Enter the minimum credits">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="entry_qualification" class="col-sm-2 col-form-label">Entry Qualification <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="entry_qualification" name="entry_qualification" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
              
                <!-- Certificate Program Fields -->
                <div id="certificate_program_fields" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="cert_course_name" class="col-sm-2 col-form-label">Course Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="cert_course_name" name="course_name" placeholder="Enter the course name" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_course_medium" class="col-sm-2 col-form-label">Course Medium <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="cert_course_medium" name="course_medium" required>
                                <option selected disabled value="">Choose a medium...</option>
                                <option value="Sinhala">Sinhala</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_conducted_by" class="col-sm-2 col-form-label">Conducted by <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="cert_conducted_by" name="conducted_by" required>
                                <option selected disabled value="">Select who conducts</option>
                                <option value="SLT-MOBITEL Nebula Institute of Technology">SLT-MOBITEL Nebula Institute of Technology</option>
                                <option value="Pearson">Pearson</option>
                                <option value="University of Hertfordshire">University of Hertfordshire</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="cert_other_conducted_by" name="other_conducted_by" placeholder="Please specify" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Duration <span class="text-danger">*</span></label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="cert_duration_years" name="duration_years" placeholder="Years" min="0" required>
                            <input type="number" class="form-control" id="cert_duration_months" name="duration_months" placeholder="Months" min="0" max="11" required>
                            <input type="number" class="form-control" id="cert_duration_days" name="duration_days" placeholder="Days" min="0" max="30" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Training Period</label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="cert_training_years" name="training_years" placeholder="Years" min="0">
                            <input type="number" class="form-control" id="cert_training_months" name="training_months" placeholder="Months" min="0" max="11">
                            <input type="number" class="form-control" id="cert_training_days" name="training_days" placeholder="Days" min="0" max="30">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="course_content" class="col-sm-2 col-form-label">Course Content <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="course_content" name="course_content" rows="4" placeholder="Enter the course content" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_entry_qualification" class="col-sm-2 col-form-label">Entry Qualification <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="cert_entry_qualification" name="entry_qualification" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h2 class="text-center mb-4">Existing Courses</h2>
            <hr>
            <div class="table-responsive" style="max-height: 400px; overflow-y: auto; overflow-x: auto; width: 100%;">
                <table class="table table-striped table-bordered" style="table-layout: fixed; width: max-content; min-width: 900px;">
                    <thead style="position: sticky; top: 0; background: #fff; z-index: 2;">
                        <tr>
                            <th style="position: sticky; top: 0; background: #fff;">Course Name</th>
                            <th style="position: sticky; top: 0; background: #fff;">Course Type</th>
                            <th style="position: sticky; top: 0; background: #fff;">Location</th>
                            <th style="position: sticky; top: 0; background: #fff;">Duration</th>
                            <th style="position: sticky; top: 0; background: #fff;">Medium</th>
                            <th style="position: sticky; top: 0; background: #fff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="existingCoursesTableBody">
                        <?php $__empty_1 = true; $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-course-id="<?php echo e($course->course_id); ?>">
                            <td><?php echo e($course->course_name); ?></td>
                            <td>
                                <?php if($course->course_type == 'degree'): ?>
                                    Degree Program
                                <?php elseif($course->course_type == 'certificate'): ?>
                                    Certificate Program
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($course->location); ?></td>
                            <td><?php echo e($course->duration); ?></td>
                            <td><?php echo e($course->course_medium); ?></td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-course-btn" data-course-id="<?php echo e($course->course_id); ?>">Edit</button>
                                <button class="btn btn-sm btn-danger delete-course-btn" data-course-id="<?php echo e($course->course_id); ?>">Delete</button>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr class="no-courses-found">
                            <td colspan="6" class="text-center">No courses found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteCourseModalLabel">Delete Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this course?</p>
            <input type="hidden" id="delete_course_id">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteCourseBtn">Delete</button>
        </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
    $(document).ready(function() {
        // Course type change handler
        $('#course_type').on('change', function() {
            const selectedType = $(this).val();
            
            if (selectedType === 'degree') {
                // Show and enable degree fields
                $('#degree_program_fields').show();
                $('#degree_program_fields').find('input, select, textarea').prop('disabled', false);

                // Hide and disable certificate fields
                $('#certificate_program_fields').hide();
                $('#certificate_program_fields').find('input, select, textarea').prop('disabled', true);

            } else if (selectedType === 'certificate') {
                // Hide and disable degree fields
                $('#degree_program_fields').hide();
                $('#degree_program_fields').find('input, select, textarea').prop('disabled', true);

                // Show and enable certificate fields
                $('#certificate_program_fields').show();
                $('#certificate_program_fields').find('input, select, textarea').prop('disabled', false);
            } else {
                 // Nothing selected, hide and disable both
                 $('#degree_program_fields').hide();
                 $('#degree_program_fields').find('input, select, textarea').prop('disabled', true);
                 $('#certificate_program_fields').hide();
                 $('#certificate_program_fields').find('input, select, textarea').prop('disabled', true);
            }
        });

        // Initially disable all fields in both sections
        $('#degree_program_fields, #certificate_program_fields').find('input, select, textarea').prop('disabled', true);

        $('#courseForm').on('submit', function(e) {
            e.preventDefault();
            
            const courseType = $('#course_type').val();
            console.log('Selected course type:', courseType);
            if (!courseType) {
                showToast('Please select a course type.', 'warning');
                return;
            }
            
            const formData = new FormData(this);
            
            // Debug: Log form data
            console.log('Form data being sent:');
            for (let [key, value] of formData.entries()) {
                console.log(key + ': ' + value);
            }
            
            // Debug: Check specialization fields
            console.log('Specialization fields found:', $('input[name="specializations[]"]').length);
            $('input[name="specializations[]"]').each(function(index) {
                console.log('Specialization ' + index + ':', $(this).val());
            });
            
            if (courseType === 'degree') {
                if ($('#conducted_by').val() === 'Other') {
                    formData.set('conducted_by', $('#other_conducted_by').val());
                }
            } else if (courseType === 'certificate') {
                if ($('#cert_conducted_by').val() === 'Other') {
                    formData.set('conducted_by', $('#cert_other_conducted_by').val());
                }
            }
            formData.delete('other_conducted_by');

            // Determine if we're in edit mode
            const editCourseId = getQueryParam('course_id');
            const isEditMode = editCourseId !== null;
            
            const url = isEditMode ? `/api/courses/update/${editCourseId}` : '<?php echo e(route("course.store")); ?>';
            const method = 'POST'; // Always use POST

            $.ajax({
                url: url,
                type: method,
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        
                        if (!isEditMode) {
                            // Only reset form and hide fields for new course creation
                            $('#courseForm')[0].reset();
                            // Hide all field sections after successful submission
                            $('#degree_program_fields, #certificate_program_fields').hide();
                            // Add new course to the table
                            if (response.course) {
                                const course = response.course;
                                let courseTypeText = 'N/A';
                                if (course.course_type === 'degree') courseTypeText = 'Degree Program';
                                else if (course.course_type === 'certificate') courseTypeText = 'Certificate Program';
                                const newRow = `<tr data-course-id="${course.course_id}">
                                    <td>${course.course_name}</td>
                                    <td>${courseTypeText}</td>
                                    <td>${course.location}</td>
                                    <td>${course.duration}</td>
                                    <td>${course.course_medium}</td>
                                    <td>
                                        <button class="btn btn-sm btn-primary edit-course-btn" data-course-id="${course.course_id}">Edit</button>
                                        <button class="btn btn-sm btn-danger delete-course-btn" data-course-id="${course.course_id}">Delete</button>
                                    </td>
                                </tr>`;
                                $('#existingCoursesTableBody').prepend(newRow);
                                // Remove 'No courses found.' row if present
                                $('#existingCoursesTableBody .no-courses-found').remove();
                            }
                        } else {
                            // For edit mode, clear the URL parameter and reset button text
                            const url = new URL(window.location.href);
                            url.searchParams.delete('course_id');
                            window.history.replaceState({}, document.title, url.toString());
                            $('#submitBtn').text('Submit');
                        }
                    }
                },
                error: function(xhr) {
                    let message = 'An error occurred.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        message = xhr.responseJSON.message;
                    }
                    showToast(message, 'danger');
                }
            });
        });

        function showToast(message, type) {
            const toastHtml = `
                <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>`;
            $('.toast-container').append(toastHtml);
            const toastEl = $('.toast-container .toast').last();
            const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
            toast.show();
        }

        // Edit button click: redirect to course management with course_id param
        $(document).on('click', '.edit-course-btn', function() {
            const courseId = $(this).data('course-id');
            window.location.href = '/course-management?course_id=' + courseId;
        });

        // On page load, check for course_id param and prefill form
        function getQueryParam(name) {
            const url = new URL(window.location.href);
            return url.searchParams.get(name);
        }
        const editCourseId = getQueryParam('course_id');
        if (editCourseId) {
            // Update button text for edit mode
            $('#submitBtn').text('Update Course');
            
            $.ajax({
                url: '/api/courses/' + editCourseId,
                type: 'GET',
                success: function(response) {
                    if (response.success && response.course) {
                        const course = response.course;
                        
                        // Set location and course type first
                        setSelectValue('#location', course.location);
                        setSelectValue('#course_type', course.course_type);
                        
                        // Trigger change to show correct fields
                        $('#course_type').trigger('change');

                        // Populate the visible fields
                        if (course.course_type === 'degree') {
                            $('#course_name').val(course.course_name);
                            $('#duration_years').val(course.duration.years);
                            $('#duration_months').val(course.duration.months);
                            $('#duration_days').val(course.duration.days);
                            setSelectValue('#course_medium', course.course_medium);

                            const conductedBy = course.conducted_by;
                            const conductedBySelect = $('#conducted_by');
                            const otherConductedByInput = $('#other_conducted_by');
                            if (conductedBySelect.find(`option[value="${conductedBy}"]`).length > 0) {
                                conductedBySelect.val(conductedBy);
                            } else {
                                conductedBySelect.val('Other');
                                otherConductedByInput.val(conductedBy).show().prop('required', true);
                            }

                            $('#no_of_semesters').val(course.no_of_semesters).trigger('input');
                            $('#training_years').val(course.training_period.years);
                            $('#training_months').val(course.training_period.months);
                            $('#training_days').val(course.training_period.days);
                            $('#min_credits').val(course.min_credits);
                            $('#entry_qualification').val(course.entry_qualification);
                        } else if (course.course_type === 'certificate') {
                            // Note: certificate fields have different IDs
                            $('#cert_course_name').val(course.course_name);
                            $('#cert_duration_years').val(course.duration.years);
                            $('#cert_duration_months').val(course.duration.months);
                            $('#cert_duration_days').val(course.duration.days);
                            setSelectValue('#cert_course_medium', course.course_medium);
                            
                            const conductedBy = course.conducted_by;
                            const conductedBySelect = $('#cert_conducted_by');
                            const otherConductedByInput = $('#cert_other_conducted_by');
                            if (conductedBySelect.find(`option[value="${conductedBy}"]`).length > 0) {
                                conductedBySelect.val(conductedBy);
                            } else {
                                conductedBySelect.val('Other');
                                otherConductedByInput.val(conductedBy).show().prop('required', true);
                            }
                            
                            $('#cert_training_years').val(course.training_period.years);
                            $('#cert_training_months').val(course.training_period.months);
                            $('#cert_training_days').val(course.training_period.days);
                            $('#course_content').val(course.course_content);
                            $('#cert_entry_qualification').val(course.entry_qualification);
                        }
                    } else {
                        showToast('Failed to fetch course details for editing', 'danger');
                    }
                },
                error: function(xhr, status, error) {
                    showToast('Error fetching course details for editing', 'danger');
                }
            });
        }

        $('#conducted_by, #cert_conducted_by').on('change', function() {
            const $otherInput = $(this).parent().find('input[name="other_conducted_by"]');
            if ($(this).val() === 'Other') {
                $otherInput.show().prop('required', true);
            } else {
                $otherInput.hide().prop('required', false).val('');
            }
        });

        // Delete button click
        $(document).on('click', '.delete-course-btn', function() {
            const courseId = $(this).data('course-id');
            $('#delete_course_id').val(courseId);
            $('#deleteCourseModal').modal('show');
        });

        // Confirm delete
        $('#confirmDeleteCourseBtn').on('click', function() {
            const courseId = $('#delete_course_id').val();
            $.ajax({
                url: '/api/courses/' + courseId,
                type: 'DELETE',
                data: {_token: '<?php echo e(csrf_token()); ?>'},
                success: function(response) {
                    if (response.success) {
                        showToast('Course deleted successfully', 'success');
                        location.reload();
                    } else {
                        showToast('Failed to delete course', 'danger');
                    }
                },
                error: function() {
                    showToast('Error deleting course', 'danger');
                }
            });
        });

        // Defensive: Only set if value exists in the select options
        function setSelectValue(selectId, value) {
            const $select = $(selectId);
            if ($select.find(`option[value='${value}']`).length > 0) {
                $select.val(value);
            } else {
                // Try to match by display text (for legacy data)
                $select.find('option').each(function() {
                    if ($(this).text().trim() === value.trim()) {
                        $select.val($(this).val());
                    }
                });
            }
        }

        // Specialization logic
        $(document).ready(function() {
            $('input[name="has_specialization"]').on('change', function() {
                if ($(this).val() === 'yes') {
                    $('#specializationFields').show();
                } else {
                    $('#specializationFields').hide();
                    $('#specializationInputs').html('<div class="input-group mb-2">\
                        <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">\
                        <button type="button" class="btn btn-outline-secondary remove-specialization" style="display:none;">Remove</button>\
                    </div>');
                }
            });
            // Add specialization
            $('#addSpecializationBtn').on('click', function() {
                $('#specializationInputs').append('<div class="input-group mb-2">\
                    <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">\
                    <button type="button" class="btn btn-outline-secondary remove-specialization">Remove</button>\
                </div>');
                updateRemoveButtons();
            });
            // Remove specialization
            $('#specializationInputs').on('click', '.remove-specialization', function() {
                $(this).closest('.input-group').remove();
                updateRemoveButtons();
            });
            function updateRemoveButtons() {
                var count = $('#specializationInputs .input-group').length;
                $('#specializationInputs .remove-specialization').each(function(i, btn) {
                    $(btn).toggle(count > 1);
                });
            }
            updateRemoveButtons();
        });
    });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/course_management.blade.php ENDPATH**/ ?>