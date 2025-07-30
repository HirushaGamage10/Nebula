@extends('inc.app')

@section('title', 'NEBULA | Overall Attendance')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Overall Attendance Report</h2>
            <hr>
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
                <div id="fields-container" style="display:none;">
                    <div class="mb-3 row mx-3">
                        <label for="course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="course" name="course_id" required>
                                <option selected disabled value="">Select a Course</option>
                                @foreach ($courses as $course)
                                    <option value="{{ $course->course_id }}">{{ $course->course_name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="intake" name="intake_id" required>
                                <option selected disabled value="">Select an Intake</option>
                                @foreach ($intakes as $intake)
                                    <option value="{{ $intake->intake_id }}">{{ $intake->batch }}</option>
                                @endforeach
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
                </div>
            </div>
            <hr class="my-4">
            <div class="mt-4" id="overallAttendanceSection" style="display:none;">
                <div class="mb-3 text-end">
                    <button id="exportPdfBtn" class="btn btn-outline-primary" type="button">
                        <i class="ti ti-download"></i> Export to PDF
                    </button>
                </div>
                <h4 class="text-center mb-3">Attendance Summary</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Total Sessions</th>
                                <th>Attended Sessions</th>
                                <th>Attendance (%)</th>
                            </tr>
                        </thead>
                        <tbody id="overallAttendanceTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.7.0/jspdf.plugin.autotable.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const courseSelect = document.getElementById('course');
    const intakeSelect = document.getElementById('intake');
    const semesterSelect = document.getElementById('semester');
    const locationSelect = document.getElementById('location');
    const courseTypeSelect = document.getElementById('course_type');
    const fieldsContainer = document.getElementById('fields-container');
    const semesterRow = document.getElementById('semester-row');
    const tableBody = document.getElementById('overallAttendanceTableBody');
    const section = document.getElementById('overallAttendanceSection');
    const moduleSelect = document.getElementById('module');

    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    resetAndDisable(courseSelect, 'Select a Course');
    resetAndDisable(intakeSelect, 'Select an Intake');
    resetAndDisable(semesterSelect, 'Select a Semester');
    resetAndDisable(moduleSelect, 'Select a Module');
    courseTypeSelect.disabled = true;

    locationSelect.addEventListener('change', function() {
        courseTypeSelect.value = ''; // Reset to placeholder
        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        courseTypeSelect.disabled = !this.value;
        fieldsContainer.style.display = 'none';
        section.style.display = 'none';
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
        section.style.display = 'none';
    });

    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        if (courseSelect.value && locationSelect.value) {
            fetchIntakes(courseSelect.value, locationSelect.value);
        }
    });

    intakeSelect.addEventListener('change', function() {
        resetAndDisable(semesterSelect, 'Select a Semester');
        if (intakeSelect.value && courseSelect.value) {
            fetchSemesters(courseSelect.value, intakeSelect.value);
        }
    });

    semesterSelect.addEventListener('change', function() {
        resetAndDisable(moduleSelect, 'Select a Module');
        if (semesterSelect.value && intakeSelect.value && courseSelect.value && locationSelect.value) {
            fetchModules();
        }
    });

    moduleSelect.addEventListener('change', fetchOverallAttendance);

    document.getElementById('exportPdfBtn').addEventListener('click', function() {
        const { jsPDF } = window.jspdf;
        const doc = new jsPDF();
        // Gather selected filter values for the title
        const locationText = locationSelect.options[locationSelect.selectedIndex]?.text || '';
        const courseText = courseSelect.options[courseSelect.selectedIndex]?.text || '';
        const intakeText = intakeSelect.options[intakeSelect.selectedIndex]?.text || '';
        const semesterText = semesterSelect.options[semesterSelect.selectedIndex]?.text || '';
        const moduleText = moduleSelect.options[moduleSelect.selectedIndex]?.text || '';
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
        // Prepare table data
        const tableRows = [];
        tableBody.querySelectorAll('tr').forEach(tr => {
            const row = [];
            tr.querySelectorAll('td').forEach(td => row.push(td.textContent));
            if (row.length) tableRows.push(row);
        });
        // Prepare table headers
        const headers = [];
        document.querySelectorAll('#overallAttendanceSection thead th').forEach(th => headers.push(th.textContent));
        // Add table
        doc.autoTable({
            head: [headers],
            body: tableRows,
            startY: y + 6
        });
        doc.save('attendance_report.pdf');
    });

    function fetchCoursesByLocation(location, courseType) {
        fetch(`/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    populateDropdown(courseSelect, data.courses, 'course_id', 'course_name', 'Course');
                    courseSelect.disabled = false;
                } else {
                    resetAndDisable(courseSelect, 'Select a Course');
                }
            });
    }
    // Fetch intakes for a course and location
    function fetchIntakes(courseId, location) {
        fetch(`/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.intakes && data.intakes.length > 0) {
                    populateDropdown(intakeSelect, data.intakes, 'intake_id', 'batch', 'Intake');
                    intakeSelect.disabled = false;
                } else {
                    resetAndDisable(intakeSelect, 'Select an Intake');
                }
            });
    }
    function fetchSemesters(courseId, intakeId) {
        fetch(`/attendance/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(semesterSelect, data.semesters, 'semester_id', 'semester_name', 'Semester');
                    semesterSelect.disabled = false;
                } else {
                    resetAndDisable(semesterSelect, 'Select a Semester');
                }
            });
    }
    // Fetch modules for a course, intake, and semester
        function fetchModules() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value
        };
        fetch('/get-filtered-modules', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.modules && data.modules.length > 0) {
                populateDropdown(moduleSelect, data.modules, 'module_id', 'module_name', 'Module');
                moduleSelect.disabled = false;
            } else {
                resetAndDisable(moduleSelect, 'Select a Module');
                section.style.display = 'none';
            }
        });
    }
    // Fetch overall attendance for a course, intake, semester, and module
        function fetchOverallAttendance() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value,
            module_id: moduleSelect.value
        };
        if (Object.values(data).some(v => !v)) {
            section.style.display = 'none';
            return;
        }
        section.style.display = '';
        fetch('/get-overall-attendance', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.attendance && data.attendance.length > 0) {
                tableBody.innerHTML = '';
                data.attendance.forEach(row => {
                    tableBody.insertAdjacentHTML('beforeend', `<tr>
                        <td>${row.registration_number}</td>
                        <td>${row.name_with_initials}</td>
                        <td>${row.total_sessions}</td>
                        <td>${row.attended_sessions}</td>
                        <td>${row.percentage}%</td>
                    </tr>`);
                });
            } else {
                tableBody.innerHTML = '<tr><td colspan="5" class="text-center">No data found.</td></tr>';
            }
        });
    }
    // Populate dropdown with items
            function populateDropdown(select, items, valueKey, textKey, defaultText) {
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            select.add(new Option(item[textKey], item[valueKey]));
        });
    }
    // Removed general event listener to prevent auto-selection
    // Each dropdown has its own specific event listener
});
</script>
@endsection 