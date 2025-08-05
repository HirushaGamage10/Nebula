

<?php $__env->startSection('title', 'NEBULA | Student List'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Student List</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Filters -->
            <div id="student-list-filters" class="mb-4">
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location">
                            <option value="" selected disabled>Select a Location</option>
                            <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($location); ?>">Nebula Institute of Technology - <?php echo e($location); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course" name="course_id" disabled>
                            <option value="" selected disabled>Select Course</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intake" class="col-sm-2 col-form-label">Batch <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intake" name="intake_id" disabled>
                            <option value="" selected disabled>Select Batch</option>
                        </select>
                    </div>
                </div>
            </div>

            <hr class="my-4">

            <!-- Student List Table -->
            <div class="mt-4" id="studentTableSection" style="display:none;">
                <div class="d-flex justify-content-end mb-2">
                    <button id="downloadListBtn" class="btn btn-primary" type="button">
                        <i class="bi bi-download"></i> Download PDF
                    </button>
                </div>
                <h4 class="text-center mb-3" id="studentListHeader"></h4>
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Course Registration ID</th>
                                <th>Student ID</th>
                                <th>Student Name</th>
                            </tr>
                        </thead>
                        <tbody id="studentTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-end mt-2">
                    <span id="studentTotalCount" class="fw-bold"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const locationSelect = document.getElementById('location');
    const courseSelect = document.getElementById('course');
    const intakeSelect = document.getElementById('intake');
    const studentTableSection = document.getElementById('studentTableSection');
    const studentTableBody = document.getElementById('studentTableBody');
    const downloadListBtn = document.getElementById('downloadListBtn');
    let lastStudentData = [];
    
    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    locationSelect.addEventListener('change', function() {
        const selectedLocation = this.value;
        resetAndDisable(courseSelect, 'Select Course');
        resetAndDisable(intakeSelect, 'Select Batch');

        if (selectedLocation) {
            showSpinner(true);
            fetch(`/api/courses-by-location/${encodeURIComponent(selectedLocation)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.courses.length > 0) {
                        populateDropdown(courseSelect, data.courses, 'course_id', 'course_name', 'Course');
                        courseSelect.disabled = false;
                    } else {
                        showToast('Info', 'No courses found for this location.', 'bg-info');
                    }
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                    showToast('Error', 'Failed to fetch courses.', 'bg-danger');
                })
                .finally(() => showSpinner(false));
        }
    });

    courseSelect.addEventListener('change', function() {
        const selectedCourseId = this.value;
        const selectedLocation = locationSelect.value;
        resetAndDisable(intakeSelect, 'Select Batch');

        if (selectedCourseId && selectedLocation) {
            showSpinner(true);
            fetch(`/get-intakes/${encodeURIComponent(selectedCourseId)}/${encodeURIComponent(selectedLocation)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.intakes && data.intakes.length > 0) {
                        populateDropdown(intakeSelect, data.intakes, 'intake_id', 'batch', 'Batch');
                        intakeSelect.disabled = false;
                    } else {
                        showToast('Info', 'No intakes found for this course and location.', 'bg-info');
                    }
                })
                .catch(error => {
                    console.error('Error fetching intakes:', error);
                    showToast('Error', 'Failed to fetch intakes.', 'bg-danger');
                })
                .finally(() => showSpinner(false));
        }
    });

    intakeSelect.addEventListener('change', fetchStudentData);

    function fetchStudentData() {
        const location = locationSelect.value;
        const courseId = courseSelect.value;
        const intakeId = intakeSelect.value;

        if (!location || !courseId || !intakeId) {
            studentTableSection.style.display = 'none';
            return;
        }

        showSpinner(true);
        fetch('/get-student-list-data', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                location: location,
                course_id: courseId,
                intake_id: intakeId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students.length > 0) {
                const locationText = locationSelect.options[locationSelect.selectedIndex].text;
                const courseText = courseSelect.options[courseSelect.selectedIndex].text;
                const intakeText = intakeSelect.options[intakeSelect.selectedIndex].text;
                const studentListHeader = document.getElementById('studentListHeader');
                studentListHeader.innerHTML = `Student list - ${locationText}<br>${courseText} - ${intakeText}`;

                studentTableBody.innerHTML = '';
                lastStudentData = data.students;
                data.students.forEach((student, idx) => {
                    const row = `<tr>
                        <td>${idx + 1}</td>
                        <td>${student.course_registration_id ?? ''}</td>
                        <td>${student.student_id ?? ''}</td>
                        <td>${student.name_with_initials}</td>
                    </tr>`;
                    studentTableBody.insertAdjacentHTML('beforeend', row);
                });
                document.getElementById('studentTotalCount').textContent = `Total Students: ${data.students.length}`;
                studentTableSection.style.display = 'block';
            } else {
                studentTableSection.style.display = 'none';
                showToast('Info', 'No students found for the selected criteria.', 'bg-info');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('Error', 'An error occurred while fetching student data.', 'bg-danger');
            studentTableSection.style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        items.forEach(item => {
            const option = new Option(item[textKey], item[valueKey]);
            select.add(option);
        });
    }

    function showSpinner(show) {
        document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
    }

    function showToast(title, message, bgColor) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = `toast align-items-center text-white ${bgColor} border-0`;
        toast.setAttribute('role', 'alert');
        toast.setAttribute('aria-live', 'assertive');
        toast.setAttribute('aria-atomic', 'true');
        toast.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}:</strong> ${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
        container.appendChild(toast);
        const bsToast = new bootstrap.Toast(toast);
        bsToast.show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Download List Button Handler
    if (downloadListBtn) {
        downloadListBtn.addEventListener('click', function() {
            const location = locationSelect.value;
            const courseId = courseSelect.value;
            const intakeId = intakeSelect.value;
            if (!location || !courseId || !intakeId) {
                showToast('Error', 'Please select all filters before downloading.', 'bg-danger');
                return;
            }
            // Create a form and submit as POST to download endpoint
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '/download-student-list';
            form.target = '_blank';
            form.style.display = 'none';
            // CSRF
            const csrf = document.createElement('input');
            csrf.type = 'hidden';
            csrf.name = '_token';
            csrf.value = '<?php echo e(csrf_token()); ?>';
            form.appendChild(csrf);
            // Filters
            const locInput = document.createElement('input');
            locInput.type = 'hidden';
            locInput.name = 'location';
            locInput.value = location;
            form.appendChild(locInput);
            const courseInput = document.createElement('input');
            courseInput.type = 'hidden';
            courseInput.name = 'course_id';
            courseInput.value = courseId;
            form.appendChild(courseInput);
            const intakeInput = document.createElement('input');
            intakeInput.type = 'hidden';
            intakeInput.name = 'intake_id';
            intakeInput.value = intakeId;
            form.appendChild(intakeInput);
            document.body.appendChild(form);
            form.submit();
            document.body.removeChild(form);
        });
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
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/student_list.blade.php ENDPATH**/ ?>