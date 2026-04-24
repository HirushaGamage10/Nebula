@extends('inc.app')

@section('title', 'NEBULA | Overall Attendance')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Overall Attendance</h2>
            <hr>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="overallAttendanceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree-panel" type="button" role="tab">Degree & Diploma</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate-panel" type="button" role="tab">Certificate</button>
                </li>
            </ul>

            <div class="tab-content" id="overallAttendanceTabContent">
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
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Degree Attendance Section -->
                    <div class="mt-4" id="degreeOverallAttendanceSection" style="display:none;">
                        <div class="mb-3 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button id="degreeExportPdfBtn" class="btn btn-outline-primary" type="button">
                                    <i class="ti ti-download"></i> Export to PDF
                                </button>
                                <button id="degreeExportExcelBtn" class="btn btn-outline-success" type="button">
                                    <i class="ti ti-file-spreadsheet"></i> Export to Excel
                                </button>
                            </div>
                        </div>
                        <ul class="nav nav-tabs mb-3" id="degreeResultTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="degree-summary-tab" data-bs-toggle="tab" data-bs-target="#degree-summary-panel" type="button" role="tab">Summary</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="degree-matrix-tab" data-bs-toggle="tab" data-bs-target="#degree-matrix-panel" type="button" role="tab">Date x Students</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="degreeResultTabContent">
                            <div class="tab-pane fade show active" id="degree-summary-panel" role="tabpanel">
                                <h4 class="text-center mb-3">Attendance Summary</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="degreeAttendanceTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Registration Number</th>
                                                <th>Student Name</th>
                                                <th>Total Sessions</th>
                                                <th>Attended Sessions</th>
                                                <th>Attendance (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="degreeOverallAttendanceTableBody">
                                            <!-- Rows will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="degree-matrix-panel" role="tabpanel">
                                <h4 class="text-center mb-3">Attendance Matrix (Dates x Students)</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="degreeAttendanceMatrixTable">
                                        <thead class="table-light" id="degreeMatrixHead">
                                            <!-- Dynamic head -->
                                        </thead>
                                        <tbody id="degreeMatrixBody">
                                            <!-- Dynamic body -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
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
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Certificate Attendance Section -->
                    <div class="mt-4" id="certOverallAttendanceSection" style="display:none;">
                        <div class="mb-3 text-end">
                            <div class="d-flex gap-2 justify-content-end">
                                <button id="certExportPdfBtn" class="btn btn-outline-primary" type="button">
                                    <i class="ti ti-download"></i> Export to PDF
                                </button>
                                <button id="certExportExcelBtn" class="btn btn-outline-success" type="button">
                                    <i class="ti ti-file-spreadsheet"></i> Export to Excel
                                </button>
                            </div>
                        </div>
                        <ul class="nav nav-tabs mb-3" id="certResultTabs" role="tablist">
                            <li class="nav-item" role="presentation">
                                <button class="nav-link active" id="cert-summary-tab" data-bs-toggle="tab" data-bs-target="#cert-summary-panel" type="button" role="tab">Summary</button>
                            </li>
                            <li class="nav-item" role="presentation">
                                <button class="nav-link" id="cert-matrix-tab" data-bs-toggle="tab" data-bs-target="#cert-matrix-panel" type="button" role="tab">Date x Students</button>
                            </li>
                        </ul>

                        <div class="tab-content" id="certResultTabContent">
                            <div class="tab-pane fade show active" id="cert-summary-panel" role="tabpanel">
                                <h4 class="text-center mb-3">Attendance Summary</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="certAttendanceTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Registration Number</th>
                                                <th>Student Name</th>
                                                <th>Total Sessions</th>
                                                <th>Attended Sessions</th>
                                                <th>Attendance (%)</th>
                                            </tr>
                                        </thead>
                                        <tbody id="certOverallAttendanceTableBody">
                                            <!-- Rows will be added here dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="cert-matrix-panel" role="tabpanel">
                                <h4 class="text-center mb-3">Attendance Matrix (Dates x Students)</h4>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-sm" id="certAttendanceMatrixTable">
                                        <thead class="table-light" id="certMatrixHead">
                                            <!-- Dynamic head -->
                                        </thead>
                                        <tbody id="certMatrixBody">
                                            <!-- Dynamic body -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
<script nonce="{{ $cspNonce }}">
/* HTML Escape Helper Function */
function escapeHtml(text) {
  const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
  return String(text).replace(/[&<>"']/g, m => map[m]);
}

document.addEventListener('DOMContentLoaded', function() {
    // Degree Tab Elements
    const degreeLocation = document.getElementById('degree_location');
    const degreeCourseType = document.getElementById('degree_course_type');
    const degreeCourse = document.getElementById('degree_course');
    const degreeIntake = document.getElementById('degree_intake');
    const degreeSemester = document.getElementById('degree_semester');
    const degreeModule = document.getElementById('degree_module');
    const degreeTableBody = document.getElementById('degreeOverallAttendanceTableBody');
    const degreeSection = document.getElementById('degreeOverallAttendanceSection');
    const degreeMatrixHead = document.getElementById('degreeMatrixHead');
    const degreeMatrixBody = document.getElementById('degreeMatrixBody');
    
    // Certificate Tab Elements
    const certLocation = document.getElementById('cert_location');
    const certCourse = document.getElementById('cert_course');
    const certIntake = document.getElementById('cert_intake');
    const certTableBody = document.getElementById('certOverallAttendanceTableBody');
    const certSection = document.getElementById('certOverallAttendanceSection');
    const certMatrixHead = document.getElementById('certMatrixHead');
    const certMatrixBody = document.getElementById('certMatrixBody');
    
    // Tab event listeners
    const degreeTabBtn = document.getElementById('degree-tab');
    const certTabBtn = document.getElementById('certificate-tab');
    
    degreeTabBtn.addEventListener('shown.bs.tab', function() {
        certSection.style.display = 'none';
    });
    
    certTabBtn.addEventListener('shown.bs.tab', function() {
        degreeSection.style.display = 'none';
    });

    function resetAndDisable(select, placeholder) {
        if (!select) return;
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    // Initialize disabled state
    resetAndDisable(degreeCourse, 'Select a Course');
    resetAndDisable(degreeIntake, 'Select an Intake');
    resetAndDisable(degreeSemester, 'Select a Semester');
    resetAndDisable(degreeModule, 'Select a Module');
    resetAndDisable(certCourse, 'Select a Course');
    resetAndDisable(certIntake, 'Select an Intake');

    // DEGREE TAB EVENT LISTENERS
    degreeLocation.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select a Course');
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        degreeSection.style.display = 'none';
        if (degreeLocation.value && degreeCourseType.value) {
            fetchDegreeCourses(degreeLocation.value, degreeCourseType.value);
        }
    });

    degreeCourseType.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select a Course');
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        degreeSection.style.display = 'none';
        if (degreeLocation.value && this.value) {
            fetchDegreeCourses(degreeLocation.value, this.value);
        }
    });

    degreeCourse.addEventListener('change', function() {
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeCourse.value && degreeLocation.value) {
            fetchDegreeIntakes(degreeCourse.value, degreeLocation.value);
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
            fetchDegreeModules();
        }
    });

    degreeModule.addEventListener('change', fetchDegreeOverallAttendance);

    // CERTIFICATE TAB EVENT LISTENERS
    certLocation.addEventListener('change', function() {
        resetAndDisable(certCourse, 'Select a Course');
        resetAndDisable(certIntake, 'Select an Intake');
        certSection.style.display = 'none';
        if (certLocation.value) {
            fetchCertCourses(certLocation.value);
        }
    });

    certCourse.addEventListener('change', function() {
        resetAndDisable(certIntake, 'Select an Intake');
        if (certCourse.value && certLocation.value) {
            fetchCertIntakes(certCourse.value, certLocation.value);
        }
    });

    certIntake.addEventListener('change', fetchCertOverallAttendance);

    // DEGREE EXPORT HANDLERS
    document.getElementById('degreeExportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const locationText = degreeLocation.options[degreeLocation.selectedIndex]?.text || '';
        const courseText = degreeCourse.options[degreeCourse.selectedIndex]?.text || '';
        const intakeText = degreeIntake.options[degreeIntake.selectedIndex]?.text || '';
        const semesterText = degreeSemester.options[degreeSemester.selectedIndex]?.text || '';
        const moduleText = degreeModule.options[degreeModule.selectedIndex]?.text || '';
        let y = 16;
        doc.setFontSize(16);
        doc.text('Attendance Report', 14, y);
        doc.setFontSize(12);
        y += 10;
        doc.text(`Location: ${locationText}`, 14, y);
        y += 8;
        doc.text(`Course: ${courseText}`, 14, y);
        y += 8;
        doc.text(`Intake: ${intakeText}`, 14, y);
        y += 8;
        doc.text(`Semester: ${semesterText}`, 14, y);
        y += 8;
        doc.text(`Module: ${moduleText}`, 14, y);
        y += 6;
        const tableRows = [];
        degreeTableBody.querySelectorAll('tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => row.push(td.textContent));
            if (row.length) tableRows.push(row);
        });
        const headers = [];
        document.querySelectorAll('#degreeAttendanceTable thead th').forEach(th => headers.push(th.textContent));
        doc.autoTable({
            head: [headers],
            body: tableRows,
            startY: y + 6
        });
        doc.save('degree_attendance_report.pdf');
    });

    document.getElementById('degreeExportExcelBtn').addEventListener('click', function() {
        if (!degreeLocation.value || !degreeCourse.value || !degreeIntake.value || !degreeSemester.value || !degreeModule.value) {
            alert('Please select all filters before exporting to Excel.');
            return;
        }
        const formData = new FormData();
        formData.append('location', degreeLocation.value);
        formData.append('course_id', degreeCourse.value);
        formData.append('intake_id', degreeIntake.value);
        formData.append('semester', degreeSemester.value);
        formData.append('module_id', degreeModule.value);
        formData.append('_token', '{{ csrf_token() }}');
        fetch('/download-attendance-excel', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) return response.blob();
            throw new Error('Network response was not ok.');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'degree_attendance_report.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error downloading Excel file.');
        });
    });

    // CERTIFICATE EXPORT HANDLERS
    document.getElementById('certExportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        const locationText = certLocation.options[certLocation.selectedIndex]?.text || '';
        const courseText = certCourse.options[certCourse.selectedIndex]?.text || '';
        const intakeText = certIntake.options[certIntake.selectedIndex]?.text || '';
        let y = 16;
        doc.setFontSize(16);
        doc.text('Certificate Attendance Report', 14, y);
        doc.setFontSize(12);
        y += 10;
        doc.text(`Location: ${locationText}`, 14, y);
        y += 8;
        doc.text(`Course: ${courseText}`, 14, y);
        y += 8;
        doc.text(`Intake: ${intakeText}`, 14, y);
        y += 6;
        const tableRows = [];
        certTableBody.querySelectorAll('tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => row.push(td.textContent));
            if (row.length) tableRows.push(row);
        });
        const headers = [];
        document.querySelectorAll('#certAttendanceTable thead th').forEach(th => headers.push(th.textContent));
        doc.autoTable({
            head: [headers],
            body: tableRows,
            startY: y + 6
        });
        doc.save('certificate_attendance_report.pdf');
    });

    document.getElementById('certExportExcelBtn').addEventListener('click', function() {
        if (!certLocation.value || !certCourse.value || !certIntake.value) {
            alert('Please select all filters before exporting to Excel.');
            return;
        }
        const formData = new FormData();
        formData.append('location', certLocation.value);
        formData.append('course_id', certCourse.value);
        formData.append('intake_id', certIntake.value);
        formData.append('semester', '');
        formData.append('module_id', '');
        formData.append('_token', '{{ csrf_token() }}');
        fetch('/download-attendance-excel', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) return response.blob();
            throw new Error('Network response was not ok.');
        })
        .then(blob => {
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = 'certificate_attendance_report.xlsx';
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error downloading Excel file.');
        });
    });

    // DEGREE FETCH FUNCTIONS
    function fetchDegreeCourses(location, courseType) {
        const url = `/attendance/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    populateDropdown(degreeCourse, data.courses, 'course_id', 'course_name', 'Course');
                    degreeCourse.disabled = false;
                } else {
                    resetAndDisable(degreeCourse, 'Select a Course');
                }
            })
            .catch((error) => {
                console.error('Error fetching courses:', error);
            });
    }

    function fetchDegreeIntakes(courseId, location) {
        fetch(`/attendance/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.intakes && data.intakes.length > 0) {
                    populateDropdown(degreeIntake, data.intakes, 'intake_id', 'batch', 'Intake');
                    degreeIntake.disabled = false;
                } else {
                    resetAndDisable(degreeIntake, 'Select an Intake');
                }
            });
    }

    function fetchDegreeSemesters(courseId, intakeId) {
        fetch(`/attendance/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(degreeSemester, data.semesters, 'semester_id', 'semester_name', 'Semester');
                    degreeSemester.disabled = false;
                } else {
                    resetAndDisable(degreeSemester, 'Select a Semester');
                }
            });
    }

    function fetchDegreeModules() {
        const data = {
            location: degreeLocation.value,
            course_id: degreeCourse.value,
            intake_id: degreeIntake.value,
            semester: degreeSemester.value
        };
        fetch('/get-filtered-modules', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.modules && data.modules.length > 0) {
                populateDropdown(degreeModule, data.modules, 'module_id', 'module_name', 'Module');
                degreeModule.disabled = false;
            } else {
                resetAndDisable(degreeModule, 'Select a Module');
                degreeSection.style.display = 'none';
            }
        });
    }

    function fetchDegreeOverallAttendance() {
        const data = {
            location: degreeLocation.value,
            course_id: degreeCourse.value,
            intake_id: degreeIntake.value,
            semester: degreeSemester.value,
            module_id: degreeModule.value
        };
        if (Object.values(data).some(v => !v)) {
            degreeSection.style.display = 'none';
            return;
        }
        degreeSection.style.display = '';
        fetch('/get-overall-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.attendance && data.attendance.length > 0) {
                degreeTableBody.innerHTML = '';
                data.attendance.forEach(row => {
                    degreeTableBody.insertAdjacentHTML('beforeend', `<tr>
                        <td>${escapeHtml(row.registration_number)}</td>
                        <td>${escapeHtml(row.name_with_initials)}</td>
                        <td>${escapeHtml(String(row.total_sessions))}</td>
                        <td>${escapeHtml(String(row.attended_sessions))}</td>
                        <td>${escapeHtml(String(row.percentage))}%</td>
                    </tr>`);
                });
            } else {
                degreeTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
            }

            renderAttendanceMatrix(data.matrix, degreeMatrixHead, degreeMatrixBody);
        });
    }

    // CERTIFICATE FETCH FUNCTIONS
    function fetchCertCourses(location) {
        const url = `/attendance/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=certificate`;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    populateDropdown(certCourse, data.courses, 'course_id', 'course_name', 'Course');
                    certCourse.disabled = false;
                } else {
                    resetAndDisable(certCourse, 'Select a Course');
                }
            })
            .catch((error) => {
                console.error('Error fetching courses:', error);
            });
    }

    function fetchCertIntakes(courseId, location) {
        fetch(`/attendance/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.intakes && data.intakes.length > 0) {
                    populateDropdown(certIntake, data.intakes, 'intake_id', 'batch', 'Intake');
                    certIntake.disabled = false;
                } else {
                    resetAndDisable(certIntake, 'Select an Intake');
                }
            });
    }

    function fetchCertOverallAttendance() {
        const data = {
            location: certLocation.value,
            course_id: certCourse.value,
            intake_id: certIntake.value,
            semester: null,
            module_id: null
        };
        if (!certLocation.value || !certCourse.value || !certIntake.value) {
            certSection.style.display = 'none';
            return;
        }
        certSection.style.display = '';
        fetch('/get-overall-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.attendance && data.attendance.length > 0) {
                certTableBody.innerHTML = '';
                data.attendance.forEach(row => {
                    certTableBody.insertAdjacentHTML('beforeend', `<tr>
                        <td>${escapeHtml(row.registration_number)}</td>
                        <td>${escapeHtml(row.name_with_initials)}</td>
                        <td>${escapeHtml(String(row.total_sessions))}</td>
                        <td>${escapeHtml(String(row.attended_sessions))}</td>
                        <td>${escapeHtml(String(row.percentage))}%</td>
                    </tr>`);
                });
            } else {
                certTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
            }

            renderAttendanceMatrix(data.matrix, certMatrixHead, certMatrixBody);
        });
    }

    function renderAttendanceMatrix(matrix, headEl, bodyEl) {
        if (!headEl || !bodyEl) return;

        const students = matrix && Array.isArray(matrix.students) ? matrix.students : [];
        const rows = matrix && Array.isArray(matrix.rows) ? matrix.rows : [];

        if (!students.length || !rows.length) {
            headEl.innerHTML = '<tr><th>Date</th></tr>';
            bodyEl.innerHTML = '<tr><td class="text-center">No date-wise attendance data found.</td></tr>';
            return;
        }

        let headHtml = '<tr><th>Date</th>';
        students.forEach(student => {
            const studentLabel = `${student.name_with_initials || ''} (${student.registration_number || '-'})`;
            headHtml += `<th>${escapeHtml(studentLabel)}</th>`;
        });
        headHtml += '</tr>';
        headEl.innerHTML = headHtml;

        bodyEl.innerHTML = '';
        rows.forEach(row => {
            let rowHtml = `<tr><td>${escapeHtml(row.date || '')}</td>`;
            students.forEach(student => {
                const key = String(student.student_id);
                const status = row.statuses ? row.statuses[key] : null;

                if (status === true) {
                    rowHtml += '<td class="text-center"><span class="badge bg-success">P</span></td>';
                } else if (status === false) {
                    rowHtml += '<td class="text-center"><span class="badge bg-danger">A</span></td>';
                } else {
                    rowHtml += '<td class="text-center text-muted">-</td>';
                }
            });
            rowHtml += '</tr>';
            bodyEl.insertAdjacentHTML('beforeend', rowHtml);
        });
    }

    // Populate dropdown with items
    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        if (!select) return;
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            select.add(new Option(item[textKey], item[valueKey]));
        });
    }
});
</script>
@endsection 