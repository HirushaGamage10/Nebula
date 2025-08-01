<?php $__env->startSection('title', 'NEBULA | Attendance'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Attendance Management</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Filters -->
            <div id="attendance-filters-bootstrap" class="mb-4">
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select filter-param" id="location" name="location" required>
                            <option value="" selected disabled>Select a Location</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course_type" class="col-sm-2 col-form-label">Course Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select filter-param" id="course_type" name="course_type" required>
                            <option value="" selected disabled>Select a Course Type</option>
                            <option value="degree">Degree Program</option>
                            <option value="certificate">Certificate Program</option>
                        </select>
                    </div>
                </div>

                <div id="fields-container" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="course" name="course_id" required>
                                <option selected disabled value="">Select a Course</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="intake" name="intake_id" required>
                                <option selected disabled value="">Select an Intake</option>
                                <?php $__currentLoopData = $intakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($intake->intake_id); ?>"><?php echo e($intake->batch); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3" id="semester-row">
                        <label for="semester" class="col-sm-2 col-form-label">Semester <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="semester" name="semester" required>
                                <option selected disabled value="">Select a Semester</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="module" class="col-sm-2 col-form-label">Module <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="module" name="module_id" required>
                                <option selected disabled value="">Select a Module</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="attendance_date" class="col-sm-2 col-form-label">Date <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="date" class="form-control" id="attendance_date" name="attendance_date" required>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Attendance Table -->
            <div class="mt-4" id="attendanceTableSection" style="display:none;">
                <h4 id="attendanceTableHeader" class="text-center mb-3" style="display: none;"></h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Present</th>
                            </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4" id="saveAttendanceBtnSection" style="display:none;">
                <button type="button" id="saveAttendanceBtn" class="btn btn-primary w-100 py-2">Save Attendance</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let students = [];
    const courseSelect = document.getElementById('course');
    const courseTypeSelect = document.getElementById('course_type');
    const fieldsContainer = document.getElementById('fields-container');
    const semesterRow = document.getElementById('semester-row');
    const semesterSelect = document.getElementById('semester');
    const moduleSelect = document.getElementById('module');
    const attendanceTableBody = document.getElementById('attendanceTableBody');
    const saveAttendanceBtn = document.getElementById('saveAttendanceBtn');
    const attendanceTableHeader = document.getElementById('attendanceTableHeader');
    const locationSelect = document.getElementById('location');
    const intakeSelect = document.getElementById('intake');
    const attendanceDateInput = document.getElementById('attendance_date');

    function resetAndDisable(select, placeholder) {
        if (select) {
            select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
            select.disabled = true;
        }
    }

    resetAndDisable(courseSelect, 'Select a Course');
    resetAndDisable(intakeSelect, 'Select an Intake');
    resetAndDisable(semesterSelect, 'Select a Semester');
    resetAndDisable(moduleSelect, 'Select a Module');
    courseTypeSelect.disabled = true;

    if (locationSelect.value) {
        courseTypeSelect.disabled = false;
    }

    locationSelect.addEventListener('change', function() {
        courseTypeSelect.value = '';
        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        courseSelect.value = '';
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        courseTypeSelect.disabled = !locationSelect.value;
        fieldsContainer.style.display = 'none';
        semesterRow.style.display = 'none';
    });

    courseTypeSelect.addEventListener('change', function() {
        if (this.value) {
            fieldsContainer.style.display = 'block';
            semesterRow.style.display = this.value === 'degree' ? 'flex' : 'none';
            fetchCoursesByLocation(locationSelect.value, this.value);
        } else {
            fieldsContainer.style.display = 'none';
        }

        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        courseSelect.value = '';
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
    });

    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        if (courseSelect.value && locationSelect.value) {
            intakeSelect.disabled = false;
            handleIntakeFetch();
        }
    });

    intakeSelect.addEventListener('change', function() {
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        semesterSelect.value = '';
        moduleSelect.value = '';
        if (intakeSelect.value && courseSelect.value) {
            fetchSemesters(courseSelect.value, intakeSelect.value);
        }
    });

    semesterSelect.addEventListener('change', function() {
        resetAndDisable(moduleSelect, 'Select a Module');
        moduleSelect.value = '';
        if (semesterSelect.value && intakeSelect.value && courseSelect.value && locationSelect.value) {
            moduleSelect.disabled = false;
            handleModuleFetch();
        }
    });

    moduleSelect.addEventListener('change', fetchStudentsForAttendance);
    attendanceDateInput.addEventListener('change', fetchStudentsForAttendance);

    function handleIntakeFetch() {
        const courseId = courseSelect.value;
        const location = locationSelect.value;
        if (!courseId || !location) {
            resetAndDisable(intakeSelect, 'Select an Intake');
            return;
        }
        showSpinner(true);
        
        // Debug: Log the request
        console.log('Fetching intakes for:', { courseId, location });
        
        fetch(`/get-intakes/${courseId}/${location}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Response data:', data);
                if (data.error) {
                    showToast('Error', data.error, 'bg-danger');
                    resetAndDisable(intakeSelect, 'Select an Intake');
                } else if (data.intakes && data.intakes.length > 0) {
                    populateDropdown(intakeSelect, data.intakes, 'intake_id', 'batch', 'Intake');
                    intakeSelect.disabled = false;
                } else {
                    showToast('Error', 'No intakes found for the selected course and location', 'bg-danger');
                    resetAndDisable(intakeSelect, 'Select an Intake');
                }
            })
            .catch((error) => {
                console.error('Fetch error:', error);
                showToast('Error', 'Failed to fetch intakes. Check console for details.', 'bg-danger');
                resetAndDisable(intakeSelect, 'Select an Intake');
            })
            .finally(() => showSpinner(false));
    }

    function fetchCoursesByLocation(location, courseType) {
        showSpinner(true);
        fetch(`/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    populateDropdown(courseSelect, data.courses, 'course_id', 'course_name', 'Course');
                    courseSelect.disabled = false;
                } else {
                    resetAndDisable(courseSelect, 'Select a Course');
                    showToast('Error', data.message || 'No courses found for this location and type.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(courseSelect, 'Select a Course');
                showToast('Error', 'Failed to fetch courses for this location and type.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function fetchSemesters(courseId, intakeId) {
        showSpinner(true);
        fetch(`/attendance/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(semesterSelect, data.semesters, 'semester_id', 'semester_name', 'Semester');
                    semesterSelect.disabled = false;
                } else {
                    resetAndDisable(semesterSelect, 'Select a Semester');
                    showToast('Error', 'No semesters found for this course and intake.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(semesterSelect, 'Select a Semester');
                showToast('Error', 'Failed to fetch semesters.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function handleModuleFetch() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value
        };
        if (Object.values(data).some(v => !v)) {
            resetAndDisable(moduleSelect, 'Select a Module');
            return;
        }
        showSpinner(true);
        fetch('/get-filtered-modules', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                showToast('Error', data.error, 'bg-danger');
                resetAndDisable(moduleSelect, 'Select a Module');
            } else {
                populateDropdown(moduleSelect, data.modules, 'module_id', 'module_name', 'Module');
                moduleSelect.disabled = false;
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to fetch modules.', 'bg-danger');
            resetAndDisable(moduleSelect, 'Select a Module');
        })
        .finally(() => showSpinner(false));
    }

    function fetchStudentsForAttendance() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value,
            module_id: moduleSelect.value
        };
        const date = attendanceDateInput.value;
        if (Object.values(data).some(v => !v) || !date) {
            document.getElementById('attendanceTableSection').style.display = 'none';
            document.getElementById('saveAttendanceBtnSection').style.display = 'none';
            return;
        }
        showSpinner(true);
        fetch('/get-students-for-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students && data.students.length > 0) {
                students = data.students.map(s => ({ ...s, status: true }));
                renderAttendanceTable();
                document.getElementById('attendanceTableSection').style.display = '';
                document.getElementById('saveAttendanceBtnSection').style.display = '';
            } else {
                showToast('Error', data.message || 'No students found for these filters.', 'bg-danger');
                document.getElementById('attendanceTableSection').style.display = 'none';
                document.getElementById('saveAttendanceBtnSection').style.display = 'none';
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to fetch students.', 'bg-danger');
            document.getElementById('attendanceTableSection').style.display = 'none';
            document.getElementById('saveAttendanceBtnSection').style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    function renderAttendanceTable() {
        attendanceTableBody.innerHTML = '';
        students.forEach((student, index) => {
            const row = `<tr>
                <td>${student.registration_number}</td>
                <td>${student.name_with_initials}</td>
                <td class="text-center">
                    <input type="checkbox" ${student.status ? 'checked' : ''} onchange="window.toggleAttendanceStatus(${index}, this.checked)">
                </td>
            </tr>`;
            attendanceTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    window.toggleAttendanceStatus = function(index, checked) {
        students[index].status = checked;
    }

    saveAttendanceBtn.addEventListener('click', function() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value,
            module_id: moduleSelect.value,
            date: attendanceDateInput.value,
            attendance_data: students.map(s => ({ student_id: s.student_id, status: s.status }))
        };
        if (Object.values(data).some(v => !v) || !data.attendance_data.length) {
            showToast('Warning', 'Please select all filters and mark attendance for at least one student.', 'bg-warning');
            return;
        }
        showSpinner(true);
        fetch('/store-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', 'Attendance saved successfully!', 'bg-success');
                document.getElementById('attendanceTableSection').style.display = 'none';
                document.getElementById('saveAttendanceBtnSection').style.display = 'none';
            } else {
                showToast('Error', data.message || 'Failed to save attendance.', 'bg-danger');
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to save attendance.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    });

    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            select.add(new Option(item[textKey], item[valueKey]));
        });
    }

    function showSpinner(show) {
        document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
    }

    function showToast(title, message, bgColor) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.style.backgroundColor = bgColor;
        toast.innerHTML = `
            <div class="toast-header"><strong class="me-auto">${title}</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
            <div class="toast-body">${message}</div>
        `;
        container.appendChild(toast);
        new bootstrap.Toast(toast).show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }
});
</script>

<style>
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #fff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #fff transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/attendance.blade.php ENDPATH**/ ?>