

<?php $__env->startSection('title', 'NEBULA | Add External Institute Student ID'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Success Message Styles */
.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.success-message.show {
    transform: translateX(0);
}

.success-message .success-icon {
    margin-right: 10px;
    font-size: 18px;
}

/* Error Message Styles */
.error-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.error-message.show {
    transform: translateX(0);
}

.error-message .error-icon {
    margin-right: 10px;
    font-size: 18px;
}
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Add External Institute Student ID</h2>
            <hr>
            <form id="uh-index-form">
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-3 col-form-label fw-bold">Location</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="locationSelect" name="location" required>
                            <option value="">Select Location</option>
                            <!-- Options populated by JS -->
                        </select>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-3 col-form-label fw-bold">Course</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="courseSelect" name="course" required disabled>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-3 col-form-label fw-bold">Intake</label>
                    <div class="col-sm-9">
                        <select class="form-select" id="intakeSelect" name="intake" required disabled>
                            <option value="">Select Intake</option>
                        </select>
                    </div>
                </div>
            </form>
            <div id="studentsSection" style="display:none;">
                <h4 class="mt-4">Students - External Institute ID</h4>
                <form id="uh-index-save-form">
                    <table class="table table-bordered mt-3">
                        <thead class="table-light">
                            <tr>
                                <th>Name</th>
                                <th>Student ID</th>
                                <th>External Institute Student ID</th>
                            </tr>
                        </thead>
                        <tbody id="studentsTableBody">
                            <!-- Populated by JS -->
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-primary mt-3">Save External Institute IDs</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// Success and Error Message Functions
function showSuccessMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `
        <i class="ti ti-check-circle success-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(successDiv);
    
    // Show the message
    setTimeout(() => successDiv.classList.add('show'), 100);
    
    // Hide after 4 seconds
    setTimeout(() => {
        successDiv.classList.remove('show');
        setTimeout(() => successDiv.remove(), 300);
    }, 4000);
}

function showErrorMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `
        <i class="ti ti-alert-circle error-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(errorDiv);
    
    // Show the message
    setTimeout(() => errorDiv.classList.add('show'), 100);
    
    // Hide after 5 seconds
    setTimeout(() => {
        errorDiv.classList.remove('show');
        setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
}

$(document).ready(function() {
    // 1. Populate locations (static for now, can be dynamic if needed)
    const locations = [
        { id: 'Welisara', name: 'Nebula Institute of Technology - Welisara' },
        { id: 'Moratuwa', name: 'Nebula Institute of Technology - Moratuwa' },
        { id: 'Peradeniya', name: 'Nebula Institute of Technology - Peradeniya' }
    ];
    locations.forEach(loc => {
        $('#locationSelect').append(`<option value="${loc.id}">${loc.name}</option>`);
    });

    // 2. On location change, fetch courses
    $('#locationSelect').on('change', function() {
        const location = $(this).val();
        $('#courseSelect').prop('disabled', true).html('<option value="">Select Course</option>');
        $('#intakeSelect').prop('disabled', true).html('<option value="">Select Intake</option>');
        $('#studentsSection').hide();
        if (!location) return;
        $.post("<?php echo e(route('uh.index.courses')); ?>", { location: location, _token: '<?php echo e(csrf_token()); ?>' }, function(res) {
            if (res.courses && res.courses.length) {
                res.courses.forEach(course => {
                    $('#courseSelect').append(`<option value="${course.course_id}">${course.course_name}</option>`);
                });
                $('#courseSelect').prop('disabled', false);
            }
        });
    });

    // 3. On course change, fetch intakes
    $('#courseSelect').on('change', function() {
        const courseId = $(this).val();
        $('#intakeSelect').prop('disabled', true).html('<option value="">Select Intake</option>');
        $('#studentsSection').hide();
        if (!courseId) return;
        $.post("<?php echo e(route('uh.index.intakes')); ?>", { course_id: courseId, _token: '<?php echo e(csrf_token()); ?>' }, function(res) {
            if (res.intakes && res.intakes.length) {
                res.intakes.forEach(intake => {
                    $('#intakeSelect').append(`<option value="${intake.intake_id}">${intake.batch}</option>`);
                });
                $('#intakeSelect').prop('disabled', false);
            }
        });
    });

    // 4. On intake change, fetch students
    $('#intakeSelect').on('change', function() {
        const intakeId = $(this).val();
        $('#studentsSection').hide();
        if (!intakeId) return;
        $.post("<?php echo e(route('uh.index.students')); ?>", { intake_id: intakeId, _token: '<?php echo e(csrf_token()); ?>' }, function(res) {
            const $tbody = $('#studentsTableBody');
            $tbody.empty();
            if (res.students && res.students.length) {
                res.students.forEach(stu => {
                    $tbody.append(`
                        <tr>
                            <td>${stu.name}</td>
                            <td>${stu.student_id}</td>
                            <td><input type="text" class="form-control" name="external_institute_id[${stu.student_id}]" value="${stu.uh_index_number || ''}" placeholder="Enter Pearson/UH/Other Institute ID"></td>
                        </tr>
                    `);
                });
                $('#studentsSection').show();
            } else {
                $tbody.append('<tr><td colspan="3" class="text-center">No students found for this intake.</td></tr>');
                $('#studentsSection').show();
            }
        });
    });

    // 5. Handle save
    $('#uh-index-save-form').on('submit', function(e) {
        e.preventDefault();
        const students = [];
        $('#studentsTableBody tr').each(function() {
            const studentId = $(this).find('td').eq(1).text();
            const externalInstituteId = $(this).find('input').val();
            if (studentId) {
                students.push({ student_id: studentId, uh_index_number: externalInstituteId });
            }
        });
        $.post("<?php echo e(route('uh.index.save')); ?>", { students: students, _token: '<?php echo e(csrf_token()); ?>' }, function(res) {
            if (res.success) {
                showSuccessMessage(res.message || 'External institute student IDs saved successfully!');
            } else {
                showErrorMessage(res.message || 'Failed to save.');
            }
        }).fail(function(xhr) {
            let errorMessage = 'Error saving external institute student IDs.';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showErrorMessage(errorMessage);
        });
    });
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/uh_index_numbers.blade.php ENDPATH**/ ?>