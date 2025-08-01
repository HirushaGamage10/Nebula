<?php $__env->startSection('title', 'NEBULA | Repeat Students Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Repeat Students Management</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="repeatStudentsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="exam-results-tab" data-bs-toggle="tab" data-bs-target="#exam-results" type="button" role="tab" aria-controls="exam-results" aria-selected="true">
                        <i class="ti ti-file-text me-2"></i>Exam Results
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payment-processes-tab" data-bs-toggle="tab" data-bs-target="#payment-processes" type="button" role="tab" aria-controls="payment-processes" aria-selected="false">
                        <i class="ti ti-credit-card me-2"></i>Payment Processes
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="clearance-status-tab" data-bs-toggle="tab" data-bs-target="#clearance-status" type="button" role="tab" aria-controls="clearance-status" aria-selected="false">
                        <i class="ti ti-check-circle me-2"></i>Clearance Status
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="attendance-tracking-tab" data-bs-toggle="tab" data-bs-target="#attendance-tracking" type="button" role="tab" aria-controls="attendance-tracking" aria-selected="false">
                        <i class="ti ti-calendar me-2"></i>Attendance Tracking
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="repeatStudentsTabContent">
                <!-- Exam Results Tab -->
                <div class="tab-pane fade show active" id="exam-results" role="tabpanel" aria-labelledby="exam-results-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Location <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="exam-location" name="location" required>
                                        <option value="" selected disabled>Select a Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="exam-course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="exam-intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Semester <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="exam-semester" name="semester" required>
                                        <option selected disabled value="">Select a Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Module <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="exam-module" name="module_id" required>
                                        <option selected disabled value="">Select a Module</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="fetchRepeatStudentsForExamResults()">
                                        <i class="ti ti-search me-2"></i>Find Repeat Students
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Results Table -->
                        <div class="mt-4" id="examResultsTableSection" style="display:none;">
                            <h4 class="text-center mb-3">Repeat Students - Exam Results</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Registration Number</th>
                                            <th>Student Name</th>
                                            <th>Previous Marks</th>
                                            <th>Previous Grade</th>
                                            <th>Repeat Count</th>
                                            <th>New Marks</th>
                                            <th>New Grade</th>
                                        </tr>
                                    </thead>
                                    <tbody id="examResultsTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3" id="examSaveBtnSection" style="display:none;">
                                <button type="button" class="btn btn-success" onclick="updateExamResults()">
                                    <i class="ti ti-device-floppy me-2"></i>Update Exam Results
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Processes Tab -->
                <div class="tab-pane fade" id="payment-processes" role="tabpanel" aria-labelledby="payment-processes-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Location <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="payment-location" name="location" required>
                                        <option value="" selected disabled>Select a Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="payment-course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="payment-intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="fetchRepeatStudentsForPayments()">
                                        <i class="ti ti-search me-2"></i>Find Repeat Students
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Table -->
                        <div class="mt-4" id="paymentTableSection" style="display:none;">
                            <h4 class="text-center mb-3">Repeat Students - Payment Processes</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Registration Number</th>
                                            <th>Student Name</th>
                                            <th>Course Fee</th>
                                            <th>Paid Amount</th>
                                            <th>Outstanding Amount</th>
                                            <th>Payment Status</th>
                                            <th>Payment Amount</th>
                                            <th>Payment Method</th>
                                            <th>Payment Date</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3" id="paymentSaveBtnSection" style="display:none;">
                                <button type="button" class="btn btn-success" onclick="updatePaymentDetails()">
                                    <i class="ti ti-credit-card me-2"></i>Update Payment Details
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Clearance Status Tab -->
                <div class="tab-pane fade" id="clearance-status" role="tabpanel" aria-labelledby="clearance-status-tab">
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h5><i class="ti ti-info-circle me-2"></i>Clearance Status Management</h5>
                            <p class="mb-0">This section will allow you to manage clearance status for repeat students including library, hostel, project, and payment clearances.</p>
                        </div>
                        
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Location <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="clearance-location" name="location" required>
                                        <option value="" selected disabled>Select a Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="clearance-course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="clearance-intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="fetchClearanceStatus()">
                                        <i class="ti ti-search me-2"></i>Find Clearance Status
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Clearance Table -->
                        <div class="mt-4" id="clearanceTableSection" style="display:none;">
                            <h4 class="text-center mb-3">Repeat Students - Clearance Status</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Registration Number</th>
                                            <th>Student Name</th>
                                            <th>Library Clearance</th>
                                            <th>Hostel Clearance</th>
                                            <th>Project Clearance</th>
                                            <th>Payment Clearance</th>
                                            <th>Overall Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="clearanceTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance Tracking Tab -->
                <div class="tab-pane fade" id="attendance-tracking" role="tabpanel" aria-labelledby="attendance-tracking-tab">
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <h5><i class="ti ti-calendar me-2"></i>Attendance Tracking for Repeat Students</h5>
                            <p class="mb-0">This section will allow you to track attendance for repeat students and manage their attendance records.</p>
                        </div>
                        
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Location <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="attendance-location" name="location" required>
                                        <option value="" selected disabled>Select a Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="attendance-course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="attendance-intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Module <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="attendance-module" name="module_id" required>
                                        <option selected disabled value="">Select a Module</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Date <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control filter-param" id="attendance-date" name="date" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="fetchAttendanceData()">
                                        <i class="ti ti-search me-2"></i>Find Attendance Data
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Attendance Table -->
                        <div class="mt-4" id="attendanceTableSection" style="display:none;">
                            <h4 class="text-center mb-3">Repeat Students - Attendance Tracking</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Registration Number</th>
                                            <th>Student Name</th>
                                            <th>Previous Attendance %</th>
                                            <th>Current Attendance</th>
                                            <th>In Time</th>
                                            <th>Out Time</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody id="attendanceTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3" id="attendanceSaveBtnSection" style="display:none;">
                                <button type="button" class="btn btn-success" onclick="updateAttendanceData()">
                                    <i class="ti ti-calendar me-2"></i>Update Attendance
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.lds-ring {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}
.lds-ring div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 64px;
    height: 64px;
    margin: 8px;
    border: 8px solid #007bff;
    border-radius: 50%;
    animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-color: #007bff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
    animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
    animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
    animation-delay: -0.15s;
}
@keyframes lds-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
#spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>

<script>
let examResults = [];
let paymentData = [];
let clearanceData = [];
let attendanceData = [];

// Toast notification function
function showToast(title, message, bgClass = 'bg-info') {
    const toastContainer = document.getElementById('toastContainer');
    const toastId = 'toast-' + Date.now();
    
    const toast = `
        <div class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true" id="${toastId}">
            <div class="d-flex">
                <div class="toast-body">
                    <strong>${title}</strong><br>${message}
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toast);
    const toastElement = document.getElementById(toastId);
    const bsToast = new bootstrap.Toast(toastElement);
    bsToast.show();
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toastElement.parentNode) {
            toastElement.parentNode.removeChild(toastElement);
        }
    }, 5000);
}

// Spinner functions
function showSpinner(show) {
    document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
}

// Filter parameter change handlers
document.addEventListener('DOMContentLoaded', function() {
    // Exam Results Tab
    document.getElementById('exam-location').addEventListener('change', function() {
        const courseSelect = document.getElementById('exam-course');
        if (this.value && courseSelect.value) {
            fetchIntakesForCourseAndLocation(courseSelect.value, this.value, 'exam-intake');
        }
    });
    
    document.getElementById('exam-course').addEventListener('change', function() {
        const locationSelect = document.getElementById('exam-location');
        if (this.value && locationSelect.value) {
            fetchIntakesForCourseAndLocation(this.value, locationSelect.value, 'exam-intake');
            fetchModulesForCourse(this.value, 'exam-module');
            fetchSemestersForCourse(this.value, 'exam-semester');
        }
    });

    // Payment Processes Tab
    document.getElementById('payment-location').addEventListener('change', function() {
        const courseSelect = document.getElementById('payment-course');
        if (this.value && courseSelect.value) {
            fetchIntakesForCourseAndLocation(courseSelect.value, this.value, 'payment-intake');
        }
    });
    
    document.getElementById('payment-course').addEventListener('change', function() {
        const locationSelect = document.getElementById('payment-location');
        if (this.value && locationSelect.value) {
            fetchIntakesForCourseAndLocation(this.value, locationSelect.value, 'payment-intake');
        }
    });

    // Clearance Status Tab
    document.getElementById('clearance-location').addEventListener('change', function() {
        const courseSelect = document.getElementById('clearance-course');
        if (this.value && courseSelect.value) {
            fetchIntakesForCourseAndLocation(courseSelect.value, this.value, 'clearance-intake');
        }
    });
    
    document.getElementById('clearance-course').addEventListener('change', function() {
        const locationSelect = document.getElementById('clearance-location');
        if (this.value && locationSelect.value) {
            fetchIntakesForCourseAndLocation(this.value, locationSelect.value, 'clearance-intake');
        }
    });

    // Attendance Tracking Tab
    document.getElementById('attendance-location').addEventListener('change', function() {
        const courseSelect = document.getElementById('attendance-course');
        if (this.value && courseSelect.value) {
            fetchIntakesForCourseAndLocation(courseSelect.value, this.value, 'attendance-intake');
        }
    });
    
    document.getElementById('attendance-course').addEventListener('change', function() {
        const locationSelect = document.getElementById('attendance-location');
        if (this.value && locationSelect.value) {
            fetchIntakesForCourseAndLocation(this.value, locationSelect.value, 'attendance-intake');
            fetchModulesForCourse(this.value, 'attendance-module');
        }
    });
});

// Fetch intakes for course and location
function fetchIntakesForCourseAndLocation(courseId, location, intakeSelectId) {
    showSpinner(true);
    fetch(`/repeat-students/get-intakes/${courseId}/${location}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const intakeSelect = document.getElementById(intakeSelectId);
                intakeSelect.innerHTML = '<option selected disabled value="">Select an Intake</option>';
                data.intakes.forEach(intake => {
                    intakeSelect.innerHTML += `<option value="${intake.intake_id}">${intake.batch}</option>`;
                });
            }
        })
        .catch(() => showToast('Error', 'Failed to fetch intakes.', 'bg-danger'))
        .finally(() => showSpinner(false));
}

// Fetch modules for course
function fetchModulesForCourse(courseId, moduleSelectId) {
    showSpinner(true);
    fetch('/repeat-students/get-modules', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({ course_id: courseId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const moduleSelect = document.getElementById(moduleSelectId);
            moduleSelect.innerHTML = '<option selected disabled value="">Select a Module</option>';
            data.modules.forEach(module => {
                moduleSelect.innerHTML += `<option value="${module.module_id}">${module.module_name}</option>`;
            });
        }
    })
    .catch(() => showToast('Error', 'Failed to fetch modules.', 'bg-danger'))
    .finally(() => showSpinner(false));
}

// Fetch semesters for course
function fetchSemestersForCourse(courseId, semesterSelectId) {
    showSpinner(true);
    fetch('/repeat-students/get-semesters')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const semesterSelect = document.getElementById(semesterSelectId);
                semesterSelect.innerHTML = '<option selected disabled value="">Select a Semester</option>';
                data.semesters.forEach(semester => {
                    semesterSelect.innerHTML += `<option value="${semester}">Semester ${semester}</option>`;
                });
            }
        })
        .catch(() => showToast('Error', 'Failed to fetch semesters.', 'bg-danger'))
        .finally(() => showSpinner(false));
}

// Fetch repeat students for exam results
function fetchRepeatStudentsForExamResults() {
    const data = {
        location: document.getElementById('exam-location').value,
        course_id: document.getElementById('exam-course').value,
        intake_id: document.getElementById('exam-intake').value,
        semester: document.getElementById('exam-semester').value,
        module_id: document.getElementById('exam-module').value
    };

    if (!data.location || !data.course_id || !data.intake_id || !data.semester || !data.module_id) {
        showToast('Warning', 'Please select all required fields.', 'bg-warning');
        return;
    }

    showSpinner(true);
    fetch('/repeat-students/get-exam-results', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.students && data.students.length > 0) {
            renderExamResultsTable(data.students);
            document.getElementById('examResultsTableSection').style.display = '';
            document.getElementById('examSaveBtnSection').style.display = '';
        } else {
            document.getElementById('examResultsTableBody').innerHTML = '<tr><td colspan="7" class="text-center">No repeat students found for these filters.</td></tr>';
            document.getElementById('examResultsTableSection').style.display = '';
            document.getElementById('examSaveBtnSection').style.display = 'none';
        }
    })
    .catch(() => {
        showToast('Error', 'Failed to fetch repeat students.', 'bg-danger');
        document.getElementById('examResultsTableSection').style.display = 'none';
        document.getElementById('examSaveBtnSection').style.display = 'none';
    })
    .finally(() => showSpinner(false));
}

// Render exam results table
function renderExamResultsTable(students) {
    examResults = students.map(s => ({
        registration_id: s.registration_id,
        student_id: s.student_id,
        name: s.name,
        previous_marks: s.previous_marks,
        previous_grade: s.previous_grade,
        repeat_count: s.repeat_count,
        new_marks: '',
        new_grade: ''
    }));

    const tbody = document.getElementById('examResultsTableBody');
    tbody.innerHTML = '';
    
    examResults.forEach((result, index) => {
        const row = `<tr>
            <td>${result.registration_id}</td>
            <td>${result.name}</td>
            <td>${result.previous_marks}</td>
            <td>${result.previous_grade}</td>
            <td>${result.repeat_count}</td>
            <td><input type="number" class="form-control" min="0" max="100" value="${result.new_marks}" onchange="updateExamResultMark(${index}, this.value)"></td>
            <td><input type="text" class="form-control" maxlength="5" value="${result.new_grade}" onchange="updateExamResultGrade(${index}, this.value)"></td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update exam result mark and grade
window.updateExamResultMark = function(index, value) {
    examResults[index].new_marks = value;
}

window.updateExamResultGrade = function(index, value) {
    examResults[index].new_grade = value;
}

// Update exam results
function updateExamResults() {
    const filterData = {
        location: document.getElementById('exam-location').value,
        course_id: document.getElementById('exam-course').value,
        intake_id: document.getElementById('exam-intake').value,
        semester: document.getElementById('exam-semester').value,
        module_id: document.getElementById('exam-module').value
    };

    const results = examResults.filter(r => r.new_marks && r.new_grade).map(r => ({
        student_id: r.student_id,
        marks: r.new_marks,
        grade: r.new_grade
    }));

    if (results.length === 0) {
        showToast('Warning', 'Please enter marks and grades for at least one student.', 'bg-warning');
        return;
    }

    const payload = { ...filterData, results: results };
    
    showSpinner(true);
    fetch('/repeat-students/update-exam-results', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success', data.message, 'bg-success');
            setTimeout(() => location.reload(), 1500);
        } else {
            let errorMsg = data.message || 'An error occurred.';
            if(data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            }
            showToast('Error', errorMsg, 'bg-danger');
        }
    })
    .catch(() => showToast('Error', 'An error occurred while updating exam results.', 'bg-danger'))
    .finally(() => showSpinner(false));
}

// Fetch repeat students for payments
function fetchRepeatStudentsForPayments() {
    const data = {
        location: document.getElementById('payment-location').value,
        course_id: document.getElementById('payment-course').value,
        intake_id: document.getElementById('payment-intake').value
    };

    if (!data.location || !data.course_id || !data.intake_id) {
        showToast('Warning', 'Please select all required fields.', 'bg-warning');
        return;
    }

    showSpinner(true);
    fetch('/repeat-students/get-payments', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success && data.students && data.students.length > 0) {
            renderPaymentTable(data.students);
            document.getElementById('paymentTableSection').style.display = '';
            document.getElementById('paymentSaveBtnSection').style.display = '';
        } else {
            document.getElementById('paymentTableBody').innerHTML = '<tr><td colspan="9" class="text-center">No repeat students with payment issues found.</td></tr>';
            document.getElementById('paymentTableSection').style.display = '';
            document.getElementById('paymentSaveBtnSection').style.display = 'none';
        }
    })
    .catch(() => {
        showToast('Error', 'Failed to fetch payment data.', 'bg-danger');
        document.getElementById('paymentTableSection').style.display = 'none';
        document.getElementById('paymentSaveBtnSection').style.display = 'none';
    })
    .finally(() => showSpinner(false));
}

// Render payment table
function renderPaymentTable(students) {
    paymentData = students.map(s => ({
        registration_id: s.registration_id,
        student_id: s.student_id,
        name: s.name,
        course_fee: s.course_fee,
        paid_amount: s.paid_amount,
        outstanding_amount: s.outstanding_amount,
        payment_status: s.payment_status,
        payment_amount: '',
        payment_method: 'Cash',
        payment_date: new Date().toISOString().split('T')[0]
    }));

    const tbody = document.getElementById('paymentTableBody');
    tbody.innerHTML = '';
    
    paymentData.forEach((payment, index) => {
        const row = `<tr>
            <td>${payment.registration_id}</td>
            <td>${payment.name}</td>
            <td>Rs. ${payment.course_fee.toLocaleString()}</td>
            <td>Rs. ${payment.paid_amount.toLocaleString()}</td>
            <td>Rs. ${payment.outstanding_amount.toLocaleString()}</td>
            <td><span class="badge bg-${payment.payment_status === 'Paid' ? 'success' : 'danger'}">${payment.payment_status}</span></td>
            <td><input type="number" class="form-control" min="0" value="${payment.payment_amount}" onchange="updatePaymentAmount(${index}, this.value)"></td>
            <td>
                <select class="form-select" onchange="updatePaymentMethod(${index}, this.value)">
                    <option value="Cash" ${payment.payment_method === 'Cash' ? 'selected' : ''}>Cash</option>
                    <option value="Bank Transfer" ${payment.payment_method === 'Bank Transfer' ? 'selected' : ''}>Bank Transfer</option>
                    <option value="Cheque" ${payment.payment_method === 'Cheque' ? 'selected' : ''}>Cheque</option>
                    <option value="Card" ${payment.payment_method === 'Card' ? 'selected' : ''}>Card</option>
                </select>
            </td>
            <td><input type="date" class="form-control" value="${payment.payment_date}" onchange="updatePaymentDate(${index}, this.value)"></td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update payment data
window.updatePaymentAmount = function(index, value) {
    paymentData[index].payment_amount = value;
}

window.updatePaymentMethod = function(index, value) {
    paymentData[index].payment_method = value;
}

window.updatePaymentDate = function(index, value) {
    paymentData[index].payment_date = value;
}

// Update payment details
function updatePaymentDetails() {
    const payments = paymentData.filter(p => p.payment_amount > 0).map(p => ({
        student_id: p.student_id,
        payment_amount: p.payment_amount,
        payment_method: p.payment_method,
        payment_date: p.payment_date,
        payment_reference: '',
        remarks: 'Repeat student payment'
    }));

    if (payments.length === 0) {
        showToast('Warning', 'Please enter payment amounts for at least one student.', 'bg-warning');
        return;
    }

    const payload = { payments: payments };
    
    showSpinner(true);
    fetch('/repeat-students/update-payments', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success', data.message, 'bg-success');
            setTimeout(() => location.reload(), 1500);
        } else {
            let errorMsg = data.message || 'An error occurred.';
            if(data.errors) {
                errorMsg = Object.values(data.errors).flat().join('<br>');
            }
            showToast('Error', errorMsg, 'bg-danger');
        }
    })
    .catch(() => showToast('Error', 'An error occurred while updating payment details.', 'bg-danger'))
    .finally(() => showSpinner(false));
}

// Placeholder functions for other tabs
function fetchClearanceStatus() {
    showToast('Info', 'Clearance status functionality will be implemented soon.', 'bg-info');
}

function fetchAttendanceData() {
    showToast('Info', 'Attendance tracking functionality will be implemented soon.', 'bg-info');
}

function updateAttendanceData() {
    showToast('Info', 'Attendance update functionality will be implemented soon.', 'bg-info');
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/repeat_students.blade.php ENDPATH**/ ?>