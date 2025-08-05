

<?php $__env->startSection('title', 'NEBULA | Elective Module Registration'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Elective Module Registration</h2>
            <hr>
            <!-- Filter Section for Elective Registration -->
            <div class="mt-4">
                <div class="mb-3 row mx-3">
                    <label for="elective_location" class="col-sm-2 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_location" name="elective_location">
                            <option selected disabled value="">Select a location</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="elective_course" class="col-sm-2 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_course" name="elective_course">
                            <option selected disabled value="">Select a course</option>
                            <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="elective_intake" class="col-sm-2 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_intake" name="elective_intake" disabled>
                            <option selected disabled value="">Select an Intake</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="elective_semester" class="col-sm-2 col-form-label fw-bold">Ongoing Semester<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_semester" name="elective_semester" disabled>
                            <option selected disabled value="">Select an ongoing semester</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="elective_module" class="col-sm-2 col-form-label fw-bold">Module<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_module" name="elective_module" disabled>
                            <option selected disabled value="">Select a module</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3" id="elective_specialization_row" style="display:none;">
                    <label for="elective_specialization" class="col-sm-2 col-form-label fw-bold">Specialization</label>
                    <div class="col-sm-10">
                        <select class="form-select cursor-pointer bg-white" id="elective_specialization" name="elective_specialization">
                            <option selected disabled value="">Select Specialization</option>
                        </select>
                    </div>
                </div>
                <!-- Removed buttons row -->
            </div>
            <!-- Elective Registration Section -->
            <div id="electiveRegistrationSection" style="display: none;">
                <hr>
                <h4 class="mb-3">Elective Module Registration</h4>
                <form method="POST" action="<?php echo e(route('module.management.registerElectiveModules')); ?>" id="electiveRegistrationForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="semester_id" id="elective_semester_hidden">
                    <input type="hidden" name="course_id" id="elective_course_hidden">
                    <input type="hidden" name="intake_id" id="elective_intake_hidden">
                    <input type="hidden" name="location" id="elective_location_hidden">
                    <input type="hidden" name="module_id" id="elective_module_hidden">
                    <input type="hidden" name="specialization" id="elective_specialization_hidden">
                    
                    <!-- Students List for Elective -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Available Students</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>NIC</th>
                                            <th>Register</th>
                                        </tr>
                                    </thead>
                                    <tbody id="electiveStudentsTable">
                                        <!-- Students will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid mx-3 mt-3">
                        <button type="submit" class="btn btn-primary">Register Elective Modules</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Enhanced toast function for colorful messages
function showToast(message, type = 'info') {
    // Remove existing toasts
    $('.toast').remove();
    
    let bgClass = '';
    let icon = '';
    
    switch(type) {
        case 'success':
            bgClass = 'bg-success text-white';
            icon = '🎉';
            break;
        case 'error':
            bgClass = 'bg-danger text-white';
            icon = '❌';
            break;
        case 'warning':
            bgClass = 'bg-warning text-dark';
            icon = '⚠️';
            break;
        case 'info':
        default:
            bgClass = 'bg-info text-white';
            icon = 'ℹ️';
            break;
    }
    
    let toastHtml = `
        <div class="toast show ${bgClass}" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header ${bgClass}">
                <strong class="me-auto">${icon} ${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    $('.toast-container').append(toastHtml);
    
    // Auto-hide after 5 seconds
    setTimeout(function() {
        $('.toast').fadeOut(500, function() {
            $(this).remove();
        });
    }, 5000);
}
$(document).ready(function() {
    // Update hidden fields when dropdowns change
    $('#elective_location').on('change', function() {
        $('#elective_location_hidden').val($(this).val());
    });
    
    $('#elective_course').on('change', function() {
        $('#elective_course_hidden').val($(this).val());
    });
    
    $('#elective_intake').on('change', function() {
        $('#elective_intake_hidden').val($(this).val());
    });
    
    $('#elective_semester').on('change', function() {
        $('#elective_semester_hidden').val($(this).val());
    });
    
    $('#elective_module').on('change', function() {
        $('#elective_module_hidden').val($(this).val());
    });

    // Handle form submission
    $('#electiveRegistrationForm').on('submit', function(e) {
        e.preventDefault();
        
        // Get selected students
        let selectedStudents = [];
        $('input[name="register_students[]"]:checked').each(function() {
            selectedStudents.push($(this).val());
        });
        
        console.log('Selected students:', selectedStudents);
        
        if (selectedStudents.length === 0) {
            showToast('⚠️ Please select at least one student to register.', 'warning');
            return;
        }
        
        // Log form data before submission
        let formData = {
            semester_id: $('#elective_semester_hidden').val(),
            course_id: $('#elective_course_hidden').val(),
            intake_id: $('#elective_intake_hidden').val(),
            location: $('#elective_location_hidden').val(),
            module_id: $('#elective_module_hidden').val(),
            specialization: $('#elective_specialization_hidden').val(),
            register_students: selectedStudents
        };
        
        console.log('Form data being sent:', formData);
        
        // Show loading state
        let submitBtn = $(this).find('button[type="submit"]');
        let originalText = submitBtn.text();
        submitBtn.prop('disabled', true).text('🔄 Registering...');
        
        // Submit via AJAX
        $.ajax({
            url: $(this).attr('action'),
            method: 'POST',
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    // Show colorful success message
                    showToast(response.message, 'success');
                    
                    // Reset form after successful registration
                    setTimeout(function() {
                        $('#electiveRegistrationForm')[0].reset();
                        $('#electiveRegistrationSection').hide();
                        // Uncheck all checkboxes
                        $('input[name="register_students[]"]').prop('checked', false);
                    }, 2000);
                } else {
                    showToast(response.message, 'error');
                }
            },
            error: function(xhr) {
                let errorMessage = '❌ An error occurred while registering elective modules.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage, 'error');
            },
            complete: function() {
                // Reset button state
                submitBtn.prop('disabled', false).text(originalText);
            }
        });
    });

    // Helper to check if all fields are selected
    function allFieldsSelected() {
        let basicFields = $('#elective_location').val() && $('#elective_course').val() && $('#elective_intake').val() && $('#elective_semester').val() && $('#elective_module').val();
        let specializationVisible = $('#elective_specialization_row').is(':visible');
        let specializationValue = $('#elective_specialization').val();
        
        console.log('allFieldsSelected check:', {
            basicFields,
            specializationVisible,
            specializationValue,
            result: specializationVisible ? (basicFields && specializationValue) : basicFields
        });
        
        // If specialization field is visible, it must be selected
        if (specializationVisible) {
            return basicFields && specializationValue;
        }
        
        return basicFields;
    }

    // Event handlers for field changes
    $('#elective_location, #elective_course').on('change', function() {
        $('#elective_intake').val('').prop('disabled', true);
        $('#elective_semester').val('').prop('disabled', true);
        $('#elective_module').val('').prop('disabled', true);
        $('#electiveRegistrationSection').hide();
        $('#elective_specialization').empty().append('<option selected disabled value="">Select Specialization</option>');
        $('#elective_specialization_row').hide();
        $('#elective_specialization_hidden').val('');
    });
    
    $('#elective_course').on('change', function() {
        // Reset specialization field
        $('#elective_specialization').empty().append('<option selected disabled value="">Select Specialization</option>');
        $('#elective_specialization_row').hide();
        $('#elective_specialization_hidden').val('');
        
        if ($('#elective_location').val() && $('#elective_course').val()) {
            // Check if the course has specializations
            fetch(`/api/courses/${$('#elective_course').val()}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.course && data.course.specializations) {
                        let specializations = [];
                        if (typeof data.course.specializations === 'string') {
                            try {
                                specializations = JSON.parse(data.course.specializations);
                            } catch (e) {
                                console.error('Error parsing specializations JSON:', e);
                                specializations = [];
                            }
                        } else if (Array.isArray(data.course.specializations)) {
                            specializations = data.course.specializations;
                        }
                        
                        // Filter out empty/null values
                        specializations = specializations.filter(spec => spec && spec.trim() !== '');
                        
                        if (specializations.length > 0) {
                            let options = '<option selected disabled value="">Select Specialization</option>';
                            specializations.forEach(spec => {
                                options += `<option value="${spec}">${spec}</option>`;
                            });
                            $('#elective_specialization').html(options);
                            $('#elective_specialization_row').show();
                        } else {
                            $('#elective_specialization_row').hide();
                        }
                    } else {
                        $('#elective_specialization_row').hide();
                    }
                })
                .catch(error => {
                    console.error('Error fetching course details:', error);
                    $('#elective_specialization_row').hide();
                });
            
            loadElectiveIntakes();
        }
    });
    
    $('#elective_location').on('change', function() {
        if ($('#elective_location').val() && $('#elective_course').val()) {
            loadElectiveIntakes();
        }
    });
    
    $('#elective_intake').on('change', function() {
        if ($(this).val()) {
            loadOngoingSemesters();
        } else {
            $('#elective_semester').prop('disabled', true);
            $('#elective_module').prop('disabled', true);
        }
    });
    
    $('#elective_semester').on('change', function() {
        if ($(this).val()) {
            // Enable module dropdown and load elective modules for this semester
            $('#elective_module').prop('disabled', false);
            loadElectiveModulesForSemester();
        } else {
            $('#elective_module').prop('disabled', true);
            $('#electiveRegistrationSection').hide();
        }
    });
    
    $('#elective_module').on('change', function() {
        console.log('Module changed, checking if all fields are selected...');
        console.log('Location:', $('#elective_location').val());
        console.log('Course:', $('#elective_course').val());
        console.log('Intake:', $('#elective_intake').val());
        console.log('Semester:', $('#elective_semester').val());
        console.log('Module:', $('#elective_module').val());
        console.log('Specialization visible:', $('#elective_specialization_row').is(':visible'));
        console.log('Specialization value:', $('#elective_specialization').val());
        
        if (allFieldsSelected()) {
            console.log('All fields selected, loading students...');
            // All fields selected, load students and show section
            loadElectiveStudents();
            $('#electiveRegistrationSection').show();
        } else {
            console.log('Not all fields selected, hiding section...');
            $('#electiveRegistrationSection').hide();
        }
    });

    // Add event listener for specialization dropdown
    $('#elective_specialization').on('change', function() {
        $('#elective_specialization_hidden').val($(this).val());
        // Trigger module change to check if all fields are now selected
        $('#elective_module').trigger('change');
    });

    function loadElectiveIntakes() {
        $.ajax({
            url: '<?php echo e(route("module.management.getIntakes")); ?>',
            method: 'POST',
            data: {
                course_id: $('#elective_course').val(),
                location: $('#elective_location').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#elective_intake').empty().append('<option selected disabled value="">Select an Intake</option>');
                    response.data.forEach(function(intake) {
                        $('#elective_intake').append(`<option value="${intake.intake_id}">${intake.intake_name}</option>`);
                    });
                    $('#elective_intake').prop('disabled', false);
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('❌ Error loading intakes. Please try again.', 'error');
            }
        });
    }

    function loadOngoingSemesters() {
        $.ajax({
            url: '<?php echo e(route("module.management.getOngoingSemesters")); ?>',
            method: 'POST',
            data: {
                course_id: $('#elective_course').val(),
                intake_id: $('#elective_intake').val(),
                location: $('#elective_location').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#elective_semester').empty().append('<option selected disabled value="">Select an ongoing semester</option>');
                    response.data.forEach(function(semester) {
                        let electiveModulesText = '';
                        if (semester.elective_modules && semester.elective_modules.length > 0) {
                            electiveModulesText = ' - Elective Modules: ' + semester.elective_modules.map(m => m.module_name).join(', ');
                        }
                        let statusText = semester.status === 'active' ? ' (Active)' : ' (Upcoming)';
                        $('#elective_semester').append(`<option value="${semester.id}">${semester.name}${statusText}${electiveModulesText}</option>`);
                    });
                    $('#elective_semester').prop('disabled', false);
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('❌ Error loading ongoing semesters. Please try again.', 'error');
            }
        });
    }

    function loadElectiveModulesForSemester() {
        $.ajax({
            url: '<?php echo e(route("module.management.getElectiveModules")); ?>',
            method: 'POST',
            data: {
                semester_id: $('#elective_semester').val(),
                course_id: $('#elective_course').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#elective_module').empty().append('<option selected disabled value="">Select a module</option>');
                    response.data.forEach(function(module) {
                        $('#elective_module').append(`<option value="${module.module_id}">${module.module_name}</option>`);
                    });
                    $('#elective_module').prop('disabled', false);
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('❌ Error loading elective modules. Please try again.', 'error');
            }
        });
    }

    function loadModules() {
        $.ajax({
            url: '<?php echo e(route("module.management.getModules")); ?>',
            method: 'POST',
            data: {
                course_id: $('#elective_course').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#elective_module').empty().append('<option selected disabled value="">Select a module</option>');
                    // Filter to show only elective modules
                    const electiveModules = response.data.filter(module => module.module_type === 'elective');
                    electiveModules.forEach(function(module) {
                        $('#elective_module').append(`<option value="${module.module_id}">${module.module_name}</option>`);
                    });
                    $('#elective_module').prop('disabled', false);
                } else {
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: function() {
                showToast('❌ Error loading modules. Please try again.', 'error');
            }
        });
    }

    function loadElectiveStudents() {
        console.log('loadElectiveStudents called with data:', {
            course_id: $('#elective_course').val(),
            intake_id: $('#elective_intake').val(),
            semester_id: $('#elective_semester').val(),
            location: $('#elective_location').val()
        });
        
        $.ajax({
            url: '<?php echo e(route("module.management.getElectiveStudents")); ?>',
            method: 'POST',
            data: {
                course_id: $('#elective_course').val(),
                intake_id: $('#elective_intake').val(),
                semester_id: $('#elective_semester').val(),
                location: $('#elective_location').val(),
                _token: '<?php echo e(csrf_token()); ?>'
            },
            success: function(response) {
                console.log('loadElectiveStudents response:', response);
                if (response.success) {
                    console.log('Students found:', response.students.length);
                    let rows = '';
                    response.students.forEach(student => {
                        rows += `<tr>
                            <td>${student.student_id}</td>
                            <td>${student.name}</td>
                            <td>${student.email}</td>
                            <td>${student.nic}</td>
                            <td><input type="checkbox" name="register_students[]" value="${student.student_id}"></td>
                        </tr>`;
                    });
                    $('#electiveStudentsTable').html(rows);
                    console.log('Student table updated with', response.students.length, 'students');
                } else {
                    console.log('Error in response:', response.message);
                    showToast('❌ ' + response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                console.log('loadElectiveStudents error:', {xhr, status, error});
                showToast('❌ Error loading students. Please try again.', 'error');
            }
        });
    }
    
    // Check if all fields are already selected when page loads
    $(document).ready(function() {
        console.log('Document ready, checking if all fields are selected...');
        if (allFieldsSelected()) {
            console.log('All fields already selected on page load, loading students...');
            loadElectiveStudents();
            $('#electiveRegistrationSection').show();
        }
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/module_management.blade.php ENDPATH**/ ?>