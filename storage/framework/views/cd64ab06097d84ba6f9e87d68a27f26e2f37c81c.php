

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

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="attendanceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree-panel" type="button" role="tab">Degree & Diploma</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate-panel" type="button" role="tab">Certificate</button>
                </li>
            </ul>

            <div class="tab-content" id="attendanceTabContent">
                <!-- Degree & Diploma Tab -->
                <div class="tab-pane fade show active" id="degree-panel" role="tabpanel">
                    <div id="attendance-filters-degree" class="mb-4">
                        <div class="mb-3 row mx-3">
                            <label for="degree_location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select degree-filter" id="degree_location" name="location" required>
                                    <option value="" selected disabled>Select a Location</option>
                                    <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                    <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                    <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="degree_course_type" class="col-sm-2 col-form-label">Course Type <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select degree-filter" id="degree_course_type" name="course_type" required>
                                    <option value="" selected disabled>Select a Course Type</option>
                                    <option value="degree">Degree Program</option>
                                    <option value="diploma">Diploma Program</option>
                                </select>
                            </div>
                        </div>
                        <div id="degree-fields-container">
                            <div class="mb-3 row mx-3">
                                <label for="degree_course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select degree-filter" id="degree_course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="degree_intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select degree-filter" id="degree_intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="degree_semester" class="col-sm-2 col-form-label">Semester <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select degree-filter" id="degree_semester" name="semester" required>
                                        <option selected disabled value="">Select a Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="degree_module" class="col-sm-2 col-form-label">Module <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select degree-filter" id="degree_module" name="module_id" required>
                                        <option selected disabled value="">Select a Module</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="degree_date" class="col-sm-2 col-form-label">Date <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="degree_date" name="attendance_date" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Degree Bulk Import Section -->
                    <div id="degreeBulkImportSection" class="mb-4 px-3" style="display:none;">
                        <h5>Bulk Import</h5>
                        <p class="text-muted">Download a pre-populated template with student details, mark attendance, then upload it back.</p>
                        <div class="alert alert-info py-2 mb-3">
                            <strong>📝 How to fill the template:</strong>
                            <ul class="mb-0 mt-2">
                                <li>The template will be pre-filled with student registration numbers and names</li>
                                <li>In the <strong>"attendance"</strong> column, select from dropdown: <span class="badge bg-success">Present</span> or <span class="badge bg-danger">Absent</span></li>
                                <li>You must use exactly these words (not case-sensitive)</li>
                            </ul>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <a id="degreeDownloadTemplateBtn" href="#" class="btn btn-outline-primary">📥 Download Template (XLSX)</a>
                            </div>
                            <div class="col-auto">
                                <input type="file" id="degreeAttendanceFileInput" accept=".csv, .xlsx, .xls" class="form-control">
                            </div>
                            <div class="col-auto">
                                <button id="degreeUploadAttendanceFileBtn" class="btn btn-primary">📤 Upload File</button>
                            </div>
                        </div>
                    </div>

                    <!-- Degree Attendance Table -->
                    <div class="mt-4" id="degreeAttendanceTableSection" style="display:none;">
                        <h4 id="degreeAttendanceTableHeader" class="text-center mb-3" style="display: none;"></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>Present</th>
                                    </tr>
                                </thead>
                                <tbody id="degreeAttendanceTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Degree Submit Button -->
                    <div class="text-center mt-4" id="degreeSaveAttendanceBtnSection" style="display:none;">
                        <button type="button" id="degreeSaveAttendanceBtn" class="btn btn-primary w-100 py-2">Save Attendance</button>
                    </div>
                </div>

                <!-- Certificate Tab -->
                <div class="tab-pane fade" id="certificate-panel" role="tabpanel">
                    <div id="attendance-filters-cert" class="mb-4">
                        <div class="mb-3 row mx-3">
                            <label for="cert_location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select cert-filter" id="cert_location" name="location" required>
                                    <option value="" selected disabled>Select a Location</option>
                                    <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                    <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                    <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div id="cert-fields-container">
                            <div class="mb-3 row mx-3">
                                <label for="cert_course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select cert-filter" id="cert_course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="cert_intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select cert-filter" id="cert_intake" name="intake_id" required>
                                        <option selected disabled value="">Select an Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="cert_date" class="col-sm-2 col-form-label">Date <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="date" class="form-control" id="cert_date" name="attendance_date" required>
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Certificate Bulk Import Section -->
                    <div id="certBulkImportSection" class="mb-4 px-3" style="display:none;">
                        <h5>Bulk Import</h5>
                        <p class="text-muted">Download a pre-populated template with student details, mark attendance, then upload it back.</p>
                        <div class="alert alert-info py-2 mb-3">
                            <strong>📝 How to fill the template:</strong>
                            <ul class="mb-0 mt-2">
                                <li>The template will be pre-filled with student registration numbers and names</li>
                                <li>In the <strong>"attendance"</strong> column, select from dropdown: <span class="badge bg-success">Present</span> or <span class="badge bg-danger">Absent</span></li>
                                <li>You must use exactly these words (not case-sensitive)</li>
                            </ul>
                        </div>
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <a id="certDownloadTemplateBtn" href="#" class="btn btn-outline-primary">📥 Download Template (XLSX)</a>
                            </div>
                            <div class="col-auto">
                                <input type="file" id="certAttendanceFileInput" accept=".csv, .xlsx, .xls" class="form-control">
                            </div>
                            <div class="col-auto">
                                <button id="certUploadAttendanceFileBtn" class="btn btn-primary">📤 Upload File</button>
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Attendance Table -->
                    <div class="mt-4" id="certAttendanceTableSection" style="display:none;">
                        <h4 id="certAttendanceTableHeader" class="text-center mb-3" style="display: none;"></h4>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>Present</th>
                                    </tr>
                                </thead>
                                <tbody id="certAttendanceTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Certificate Submit Button -->
                    <div class="text-center mt-4" id="certSaveAttendanceBtnSection" style="display:none;">
                        <button type="button" id="certSaveAttendanceBtn" class="btn btn-primary w-100 py-2">Save Attendance</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="<?php echo e($cspNonce); ?>">
document.addEventListener('DOMContentLoaded', function() {
    let degreeStudents = [];
    let certStudents = [];
    
    // Degree Tab Elements
    const degreeLocation = document.getElementById('degree_location');
    const degreeCourseType = document.getElementById('degree_course_type');
    const degreeCourse = document.getElementById('degree_course');
    const degreeIntake = document.getElementById('degree_intake');
    const degreeSemester = document.getElementById('degree_semester');
    const degreeModule = document.getElementById('degree_module');
    const degreeDate = document.getElementById('degree_date');
    
    // Certificate Tab Elements
    const certLocation = document.getElementById('cert_location');
    const certCourse = document.getElementById('cert_course');
    const certIntake = document.getElementById('cert_intake');
    const certDate = document.getElementById('cert_date');
    
    // Degree-specific elements
    const degreeAttendanceTableBody = document.getElementById('degreeAttendanceTableBody');
    const degreeSaveAttendanceBtn = document.getElementById('degreeSaveAttendanceBtn');
    const degreeAttendanceTableHeader = document.getElementById('degreeAttendanceTableHeader');
    const degreeDownloadTemplateBtn = document.getElementById('degreeDownloadTemplateBtn');
    const degreeAttendanceFileInput = document.getElementById('degreeAttendanceFileInput');
    const degreeUploadAttendanceFileBtn = document.getElementById('degreeUploadAttendanceFileBtn');
    
    // Certificate-specific elements
    const certAttendanceTableBody = document.getElementById('certAttendanceTableBody');
    const certSaveAttendanceBtn = document.getElementById('certSaveAttendanceBtn');
    const certAttendanceTableHeader = document.getElementById('certAttendanceTableHeader');
    const certDownloadTemplateBtn = document.getElementById('certDownloadTemplateBtn');
    const certAttendanceFileInput = document.getElementById('certAttendanceFileInput');
    const certUploadAttendanceFileBtn = document.getElementById('certUploadAttendanceFileBtn');
    
    // Tab change event listeners to ensure proper section visibility
    const degreeTabBtn = document.getElementById('degree-tab');
    const certTabBtn = document.getElementById('certificate-tab');
    
    degreeTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to degree tab, hide cert sections if they're visible
        document.getElementById('certAttendanceTableSection').style.display = 'none';
        document.getElementById('certSaveAttendanceBtnSection').style.display = 'none';
        document.getElementById('certBulkImportSection').style.display = 'none';
    });
    
    certTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to cert tab, hide degree sections if they're visible
        document.getElementById('degreeAttendanceTableSection').style.display = 'none';
        document.getElementById('degreeSaveAttendanceBtnSection').style.display = 'none';
        document.getElementById('degreeBulkImportSection').style.display = 'none';
    });
    
    // Helper functions
    function getActiveTab() {
        const degreePanel = document.getElementById('degree-panel');
        return degreePanel.classList.contains('active') && degreePanel.classList.contains('show') ? 'degree' : 'certificate';
    }
    
    function getCurrentStudents() {
        return getActiveTab() === 'degree' ? degreeStudents : certStudents;
    }
    
    function setCurrentStudents(students) {
        if (getActiveTab() === 'degree') {
            degreeStudents = students;
        } else {
            certStudents = students;
        }
    }

    function resetAndDisable(select, placeholder) {
        if (!select) return;
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    // DEGREE TAB EVENT LISTENERS
    degreeLocation.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select a Course');
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeLocation.value && degreeCourseType.value) {
            fetchDegreeCourses(degreeLocation.value, degreeCourseType.value);
        }
    });

    degreeCourseType.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select a Course');
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeLocation.value && this.value) {
            fetchDegreeCourses(degreeLocation.value, this.value);
        }
    });

    degreeCourse.addEventListener('change', function() {
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeCourse.value && degreeLocation.value) {
            degreeIntake.disabled = false;
            handleDegreeIntakeFetch();
        }
    });

    degreeIntake.addEventListener('change', function() {
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeIntake.value && degreeCourse.value) {
            fetchDegreeSemesters(degreeCourse.value, degreeIntake.value);
        }
    });

    degreeSemester.addEventListener('change', function() {
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeSemester.value && degreeIntake.value && degreeCourse.value && degreeLocation.value) {
            degreeModule.disabled = false;
            handleDegreeModuleFetch();
        }
    });

    degreeModule.addEventListener('change', function() {
        if (allDegreeFilled()) {
            fetchDegreeStudentsForAttendance();
        }
    });

    degreeDate.addEventListener('change', function() {
        if (allDegreeFilled()) {
            fetchDegreeStudentsForAttendance();
        }
        updateBulkImportSection();
    });

    // CERTIFICATE TAB EVENT LISTENERS
    certLocation.addEventListener('change', function() {
        resetAndDisable(certCourse, 'Select a Course');
        resetAndDisable(certIntake, 'Select an Intake');
        if (certLocation.value) {
            fetchCertCourses(certLocation.value);
        }
    });

    certCourse.addEventListener('change', function() {
        resetAndDisable(certIntake, 'Select an Intake');
        if (certCourse.value && certLocation.value) {
            certIntake.disabled = false;
            handleCertIntakeFetch();
        }
    });

    certIntake.addEventListener('change', function() {
        if (allCertFilled()) {
            fetchCertStudentsForAttendance();
        }
    });

    certDate.addEventListener('change', function() {
        if (allCertFilled()) {
            fetchCertStudentsForAttendance();
        }
        updateBulkImportSection();
    });

    // Validation functions
    function allDegreeFilled() {
        return degreeLocation.value && degreeCourseType.value && degreeCourse.value && 
               degreeIntake.value && degreeSemester.value && degreeModule.value && degreeDate.value;
    }

    function allCertFilled() {
        return certLocation.value && certCourse.value && certIntake.value && certDate.value;
    }

    // Helper to populate dropdowns
    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        if (!select) return;
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            const value = item[valueKey];
            let displayText = item[textKey];
            if (displayText && value) {
                select.add(new Option(displayText, value));
            }
        });
    }

    // Fetch functions for Degree tab
    function fetchDegreeCourses(location, courseType) {
        showSpinner(true);
        fetch(`/attendance/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    populateDropdown(degreeCourse, data.courses, 'course_id', 'course_name', 'Course');
                    degreeCourse.disabled = false;
                } else {
                    resetAndDisable(degreeCourse, 'Select a Course');
                }
            })
            .catch(() => resetAndDisable(degreeCourse, 'Select a Course'))
            .finally(() => showSpinner(false));
    }

    function handleDegreeIntakeFetch() {
        showSpinner(true);
        fetch(`/attendance/get-intakes/${degreeCourse.value}/${degreeLocation.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resetAndDisable(degreeIntake, 'Select an Intake');
                } else {
                    populateDropdown(degreeIntake, data.intakes, 'intake_id', 'batch', 'Intake');
                    degreeIntake.disabled = false;
                }
            })
            .catch(() => resetAndDisable(degreeIntake, 'Select an Intake'))
            .finally(() => showSpinner(false));
    }

    function fetchDegreeSemesters(courseId, intakeId) {
        showSpinner(true);
        fetch(`/attendance/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(degreeSemester, data.semesters, 'semester_id', 'semester_name', 'Semester');
                    degreeSemester.disabled = false;
                } else {
                    resetAndDisable(degreeSemester, 'Select a Semester');
                }
            })
            .catch(() => resetAndDisable(degreeSemester, 'Select a Semester'))
            .finally(() => showSpinner(false));
    }

    function handleDegreeModuleFetch() {
        const data = {
            location: degreeLocation.value,
            course_id: degreeCourse.value,
            intake_id: degreeIntake.value,
            semester: degreeSemester.value
        };
        showSpinner(true);
        fetch('/get-filtered-modules', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.modules) {
                populateDropdown(degreeModule, data.modules, 'module_id', 'module_name', 'Module');
                degreeModule.disabled = false;
            } else {
                resetAndDisable(degreeModule, 'Select a Module');
            }
        })
        .catch(() => resetAndDisable(degreeModule, 'Select a Module'))
        .finally(() => showSpinner(false));
    }

    // Fetch functions for Certificate tab
    function fetchCertCourses(location) {
        showSpinner(true);
        fetch(`/attendance/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=certificate`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses) {
                    populateDropdown(certCourse, data.courses, 'course_id', 'course_name', 'Course');
                    certCourse.disabled = false;
                } else {
                    resetAndDisable(certCourse, 'Select a Course');
                }
            })
            .catch(() => resetAndDisable(certCourse, 'Select a Course'))
            .finally(() => showSpinner(false));
    }

    function handleCertIntakeFetch() {
        showSpinner(true);
        fetch(`/attendance/get-intakes/${certCourse.value}/${certLocation.value}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resetAndDisable(certIntake, 'Select an Intake');
                } else {
                    populateDropdown(certIntake, data.intakes, 'intake_id', 'batch', 'Intake');
                    certIntake.disabled = false;
                }
            })
            .catch(() => resetAndDisable(certIntake, 'Select an Intake'))
            .finally(() => showSpinner(false));
    }

    function fetchDegreeStudentsForAttendance() {
        const data = {
            location: degreeLocation.value,
            course_type: degreeCourseType.value,
            course_id: degreeCourse.value,
            intake_id: degreeIntake.value,
            semester: degreeSemester.value,
            module_id: degreeModule.value,
            date: degreeDate.value
        };
        console.log('Fetching degree students with data:', data);
        showSpinner(true);
        fetch('/get-students-for-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Backend response:', data);
            if (data.success && data.students && data.students.length > 0) {
                degreeStudents = data.students.map(s => ({ ...s, status: true }));
                renderAttendanceTable();
                document.getElementById('degreeAttendanceTableSection').style.display = '';
                document.getElementById('degreeSaveAttendanceBtnSection').style.display = '';
                updateBulkImportSection();
            } else if (data.success && data.students && data.students.length === 0) {
                console.warn('No students found in the response');
                showToast('Warning', 'No students found for these filters. Please verify the filters are correct.', 'bg-warning');
                document.getElementById('degreeAttendanceTableSection').style.display = 'none';
                document.getElementById('degreeSaveAttendanceBtnSection').style.display = 'none';
            } else {
                console.error('Error response:', data);
                showToast('Error', data.message || 'Failed to fetch students.', 'bg-danger');
                document.getElementById('degreeAttendanceTableSection').style.display = 'none';
                document.getElementById('degreeSaveAttendanceBtnSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showToast('Error', 'Failed to fetch students. Check console for details.', 'bg-danger');
            document.getElementById('degreeAttendanceTableSection').style.display = 'none';
            document.getElementById('degreeSaveAttendanceBtnSection').style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    function fetchCertStudentsForAttendance() {
        const data = {
            location: certLocation.value,
            course_type: 'certificate',
            course_id: certCourse.value,
            intake_id: certIntake.value,
            semester: null,
            module_id: null,
            date: certDate.value
        };
        console.log('Fetching certificate students with data:', data);
        showSpinner(true);
        fetch('/get-students-for-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Backend response:', data);
            if (data.success && data.students && data.students.length > 0) {
                certStudents = data.students.map(s => ({ ...s, status: true }));
                renderAttendanceTable();
                document.getElementById('certAttendanceTableSection').style.display = '';
                document.getElementById('certSaveAttendanceBtnSection').style.display = '';
                updateBulkImportSection();
            } else if (data.success && data.students && data.students.length === 0) {
                console.warn('No students found in the response');
                showToast('Warning', 'No students found for these filters. Please verify the filters are correct.', 'bg-warning');
                document.getElementById('certAttendanceTableSection').style.display = 'none';
                document.getElementById('certSaveAttendanceBtnSection').style.display = 'none';
            } else {
                console.error('Error response:', data);
                showToast('Error', data.message || 'Failed to fetch students.', 'bg-danger');
                document.getElementById('certAttendanceTableSection').style.display = 'none';
                document.getElementById('certSaveAttendanceBtnSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showToast('Error', 'Failed to fetch students. Check console for details.', 'bg-danger');
            document.getElementById('certAttendanceTableSection').style.display = 'none';
            document.getElementById('certSaveAttendanceBtnSection').style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    function renderAttendanceTable() {
        const activeTab = getActiveTab();
        const students = getCurrentStudents();
        const tableBody = activeTab === 'degree' ? degreeAttendanceTableBody : certAttendanceTableBody;
        
        tableBody.innerHTML = '';
        students.forEach((student, index) => {
            const row = `<tr>
                <td>${student.registration_number}</td>
                <td>${student.name_with_initials}</td>
                <td class="text-center">
                    <input type="checkbox" ${student.status ? 'checked' : ''} data-student-index="${index}" class="attendance-checkbox-${activeTab}">
                </td>
            </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
        
        // Add event listeners to checkboxes
        document.querySelectorAll(`.attendance-checkbox-${activeTab}`).forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const students = getCurrentStudents();
                const index = parseInt(this.getAttribute('data-student-index'));
                students[index].status = this.checked;
            });
        });
    }

    function updateBulkImportSection() {
        const activeTab = getActiveTab();
        
        let hasDate = false;
        let params = new URLSearchParams();
        
        if (activeTab === 'degree' && degreeDate.value) {
            hasDate = true;
            params.append('location', degreeLocation.value || '');
            params.append('course_id', degreeCourse.value || '');
            params.append('intake_id', degreeIntake.value || '');
            params.append('semester', degreeSemester.value || '');
            params.append('module_id', degreeModule.value || '');
            params.append('date', degreeDate.value || '');
            
            document.getElementById('degreeBulkImportSection').style.display = '';
            degreeDownloadTemplateBtn.href = '/attendance/download-template?' + params.toString();
        } else {
            document.getElementById('degreeBulkImportSection').style.display = 'none';
        }
        
        if (activeTab === 'certificate' && certDate.value) {
            hasDate = true;
            params = new URLSearchParams();
            params.append('location', certLocation.value || '');
            params.append('course_id', certCourse.value || '');
            params.append('intake_id', certIntake.value || '');
            params.append('semester', '');
            params.append('module_id', '');
            params.append('date', certDate.value || '');
            
            document.getElementById('certBulkImportSection').style.display = '';
            certDownloadTemplateBtn.href = '/attendance/download-template?' + params.toString();
        } else {
            document.getElementById('certBulkImportSection').style.display = 'none';
        }
    }

    // Degree save attendance button
    degreeSaveAttendanceBtn.addEventListener('click', function() {
        const students = degreeStudents;
        
        let data = {
            attendance_data: students.map(s => ({ student_id: s.student_id, status: s.status }))
        };
        
        data.location = degreeLocation.value;
        data.course_type = degreeCourseType.value;
        data.course_id = degreeCourse.value;
        data.intake_id = degreeIntake.value;
        data.semester = degreeSemester.value;
        data.module_id = degreeModule.value;
        data.date = degreeDate.value;
        
        if (Object.values(data).filter(v => v !== null).some(v => !v) || !data.attendance_data.length) {
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
                document.getElementById('degreeAttendanceTableSection').style.display = 'none';
                document.getElementById('degreeSaveAttendanceBtnSection').style.display = 'none';
            } else {
                showToast('Error', data.message || 'Failed to save attendance.', 'bg-danger');
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to save attendance.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    });

    // Certificate save attendance button
    certSaveAttendanceBtn.addEventListener('click', function() {
        const students = certStudents;
        
        let data = {
            attendance_data: students.map(s => ({ student_id: s.student_id, status: s.status }))
        };
        
        data.location = certLocation.value;
        data.course_type = 'certificate';
        data.course_id = certCourse.value;
        data.intake_id = certIntake.value;
        data.semester = null;
        data.module_id = null;
        data.date = certDate.value;
        
        if (Object.values(data).filter(v => v !== null).some(v => !v) || !data.attendance_data.length) {
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
                document.getElementById('certAttendanceTableSection').style.display = 'none';
                document.getElementById('certSaveAttendanceBtnSection').style.display = 'none';
            } else {
                showToast('Error', data.message || 'Failed to save attendance.', 'bg-danger');
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to save attendance.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    });

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

    // Degree bulk import upload handler
    degreeUploadAttendanceFileBtn.addEventListener('click', function() {
        const file = degreeAttendanceFileInput.files[0];
        if (!file) {
            showToast('Warning', 'Please choose a file to upload.', 'bg-warning');
            return;
        }

        const payload = new FormData();
        payload.append('attendance_file', file);
        payload.append('location', degreeLocation.value || '');
        payload.append('course_id', degreeCourse.value || '');
        payload.append('intake_id', degreeIntake.value || '');
        payload.append('semester', degreeSemester.value || '');
        payload.append('module_id', degreeModule.value || '');
        payload.append('date', degreeDate.value || '');
        payload.append('_token', '<?php echo e(csrf_token()); ?>');

        showSpinner(true);
        fetch('/attendance/import', {
            method: 'POST',
            body: payload
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message || 'Import successful', 'bg-success');
                // Use the returned student data with their attendance status
                if (data.students && data.students.length > 0) {
                    degreeStudents = data.students;
                    renderAttendanceTable();
                    document.getElementById('degreeAttendanceTableSection').style.display = '';
                    document.getElementById('degreeSaveAttendanceBtnSection').style.display = '';
                } else {
                    fetchDegreeStudentsForAttendance();
                }
            } else {
                showToast('Error', data.message || 'Import failed', 'bg-danger');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Error', 'Upload failed. Check console for details.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    });

    // Certificate bulk import upload handler
    certUploadAttendanceFileBtn.addEventListener('click', function() {
        const file = certAttendanceFileInput.files[0];
        if (!file) {
            showToast('Warning', 'Please choose a file to upload.', 'bg-warning');
            return;
        }

        const payload = new FormData();
        payload.append('attendance_file', file);
        payload.append('location', certLocation.value || '');
        payload.append('course_id', certCourse.value || '');
        payload.append('intake_id', certIntake.value || '');
        payload.append('semester', '');
        payload.append('module_id', '');
        payload.append('date', certDate.value || '');
        payload.append('_token', '<?php echo e(csrf_token()); ?>');

        showSpinner(true);
        fetch('/attendance/import', {
            method: 'POST',
            body: payload
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message || 'Import successful', 'bg-success');
                // Use the returned student data with their attendance status
                if (data.students && data.students.length > 0) {
                    certStudents = data.students;
                    renderAttendanceTable();
                    document.getElementById('certAttendanceTableSection').style.display = '';
                    document.getElementById('certSaveAttendanceBtnSection').style.display = '';
                } else {
                    fetchCertStudentsForAttendance();
                }
            } else {
                showToast('Error', data.message || 'Import failed', 'bg-danger');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('Error', 'Upload failed. Check console for details.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    });
});
</script>

<style nonce="<?php echo e($cspNonce); ?>">
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #fff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #fff transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/attendance/attendance.blade.php ENDPATH**/ ?>