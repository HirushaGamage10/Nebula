@extends('inc.app')

@section('title', 'NEBULA | View & Edit Exam Results')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">View & Edit Exam Results</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 9px; right: 10px; z-index: 1000;"></div>

            <!-- Tabs -->
            <ul class="nav nav-tabs mb-4" id="examResultsTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree-panel" type="button" role="tab">Degree & Diploma</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate-panel" type="button" role="tab">Certificate</button>
                </li>
            </ul>

            <div class="tab-content" id="examResultsTabContent">
                <!-- Degree & Diploma Tab -->
                <div class="tab-pane fade show active" id="degree-panel" role="tabpanel">
                    <!-- Filters -->
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
                                <option value="degree">Degree</option>
                                <option value="diploma">Diploma</option>
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
                        <div class="mb-3 row mx-3" id="degree_specialization_row" style="display:none;">
                            <label for="degree_specialization" class="col-sm-2 col-form-label">Specialization <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select degree-filter" id="degree_specialization" name="specialization" disabled>
                                    <option selected disabled value="">Select a Specialization</option>
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

                    <hr class="my-4">

                    <!-- Degree Results Table -->
                    <div class="mt-4" id="degreeResultsTableSection" style="display:none;">
                        <h4 id="degreeResultsTableHeader" class="text-center mb-3" style="display: none;"></h4>

                        <!-- Results Status Alert -->
                        <div id="degreeResultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                            <i class="ti ti-info-circle"></i>
                            <strong>Exam Results Status:</strong>
                            <span id="degreeResultsStatusText"></span>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4" id="degreeStatisticsCards" style="display: none;">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Students</h5>
                                        <h3 id="degreeTotalStudents">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Average Marks</h5>
                                        <h3 id="degreeAverageMarks">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Pass Rate</h5>
                                        <h3 id="degreePassRate">0%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Last Updated</h5>
                                        <h6 id="degreeLastUpdated">-</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="degreeResultsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>Marks</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody id="degreeResultsTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Degree Update Button -->
                    <div class="text-center mt-4" id="degreeUpdateAllBtnSection" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="degreeUpdateAllBtn" class="btn btn-primary w-100 py-2 mb-2">Update All Results</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificate Tab -->
                <div class="tab-pane fade" id="certificate-panel" role="tabpanel">
                    <!-- Filters -->
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

                    <hr class="my-4">

                    <!-- Certificate Results Table -->
                    <div class="mt-4" id="certResultsTableSection" style="display:none;">
                        <h4 id="certResultsTableHeader" class="text-center mb-3" style="display: none;"></h4>

                        <!-- Results Status Alert -->
                        <div id="certResultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                            <i class="ti ti-info-circle"></i>
                            <strong>Exam Results Status:</strong>
                            <span id="certResultsStatusText"></span>
                        </div>

                        <!-- Statistics Cards -->
                        <div class="row mb-4" id="certStatisticsCards" style="display: none;">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Total Students</h5>
                                        <h3 id="certTotalStudents">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Average Marks</h5>
                                        <h3 id="certAverageMarks">0</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Pass Rate</h5>
                                        <h3 id="certPassRate">0%</h3>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h5 class="card-title">Last Updated</h5>
                                        <h6 id="certLastUpdated">-</h6>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-bordered" id="certResultsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>Marks</th>
                                        <th>Grade</th>
                                        <th>Remarks</th>
                                        <th>Last Updated</th>
                                    </tr>
                                </thead>
                                <tbody id="certResultsTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Certificate Update Button -->
                    <div class="text-center mt-4" id="certUpdateAllBtnSection" style="display:none;">
                        <div class="row">
                            <div class="col-md-6">
                            </div>
                            <div class="col-md-6">
                                <button type="button" id="certUpdateAllBtn" class="btn btn-primary w-100 py-2 mb-2">Update All Results</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    let degreeResults = [];
    let certResults = [];
    let degreeSpecializationsLoaded = false;

    // Degree Tab Elements
    const degreeLocation = document.getElementById('degree_location');
    const degreeCourseType = document.getElementById('degree_course_type');
    const degreeCourse = document.getElementById('degree_course');
    const degreeIntake = document.getElementById('degree_intake');
    const degreeSpecialization = document.getElementById('degree_specialization');
    const degreeSpecializationRow = document.getElementById('degree_specialization_row');
    const degreeSemester = document.getElementById('degree_semester');
    const degreeModule = document.getElementById('degree_module');
    const degreeFieldsContainer = document.getElementById('degree-fields-container');

    // Certificate Tab Elements
    const certLocation = document.getElementById('cert_location');
    const certCourse = document.getElementById('cert_course');
    const certIntake = document.getElementById('cert_intake');
    const certFieldsContainer = document.getElementById('cert-fields-container');

    // Degree-specific elements
    const degreeUpdateAllBtn = document.getElementById('degreeUpdateAllBtn');
    const degreeResultsTableBody = document.getElementById('degreeResultsTableBody');
    const degreeResultsTableHeader = document.getElementById('degreeResultsTableHeader');
    const degreeStatisticsCards = document.getElementById('degreeStatisticsCards');

    // Certificate-specific elements
    const certUpdateAllBtn = document.getElementById('certUpdateAllBtn');
    const certResultsTableBody = document.getElementById('certResultsTableBody');
    const certResultsTableHeader = document.getElementById('certResultsTableHeader');
    const certStatisticsCards = document.getElementById('certStatisticsCards');

    // Tab event listeners to ensure proper section visibility
    const degreeTabBtn = document.getElementById('degree-tab');
    const certTabBtn = document.getElementById('certificate-tab');

    degreeTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to degree tab, hide cert sections if they're visible
        document.getElementById('certResultsTableSection').style.display = 'none';
        document.getElementById('certUpdateAllBtnSection').style.display = 'none';
    });

    certTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to cert tab, hide degree sections if they're visible
        document.getElementById('degreeResultsTableSection').style.display = 'none';
        document.getElementById('degreeUpdateAllBtnSection').style.display = 'none';
    });

    // Helper functions
    function getActiveTab() {
        const degreePanel = document.getElementById('degree-panel');
        return degreePanel.classList.contains('active') && degreePanel.classList.contains('show') ? 'degree' : 'certificate';
    }

    function getCurrentResults() {
        return getActiveTab() === 'degree' ? degreeResults : certResults;
    }

    function setCurrentResults(results) {
        if (getActiveTab() === 'degree') {
            degreeResults = results;
        } else {
            certResults = results;
        }
    }

    // Helper to reset and disable dropdowns
    function resetAndDisable(select, placeholder) {
        if (!select) return;
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    function resetSpecialization() {
        if (!degreeSpecialization || !degreeSpecializationRow) {
            return;
        }

        degreeSpecializationsLoaded = false;
        degreeSpecialization.innerHTML = '<option selected disabled value="">Select a Specialization</option>';
        degreeSpecialization.disabled = true;
        degreeSpecializationRow.style.display = 'none';
    }

    function hasDegreeSpecializationSelection() {
        return degreeSpecializationsLoaded && (!degreeSpecializationRow || degreeSpecializationRow.style.display === 'none' || !!degreeSpecialization.value);
    }

    function fetchDegreeSpecializations() {
        if (!degreeCourse.value) {
            resetSpecialization();
            return;
        }

        showSpinner(true);
        fetch(`/api/course/${degreeCourse.value}/specializations`)
            .then(response => response.json())
            .then(data => {
                const specializations = data.success && Array.isArray(data.specializations) ? data.specializations.filter(Boolean) : [];

                if (specializations.length > 0) {
                    degreeSpecialization.innerHTML = '<option selected disabled value="">Select a Specialization</option>';
                    specializations.forEach(spec => {
                        degreeSpecialization.add(new Option(spec, spec));
                    });
                    degreeSpecialization.disabled = false;
                    degreeSpecializationRow.style.display = '';
                    degreeSpecializationsLoaded = true;
                } else {
                    resetSpecialization();
                    degreeSpecializationsLoaded = true;
                }
            })
            .catch(() => {
                resetSpecialization();
                degreeSpecializationsLoaded = true;
            })
            .finally(() => showSpinner(false));
    }

    // On load, reset and disable all except location
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
        resetSpecialization();
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeLocation.value && degreeCourseType.value) {
            fetchDegreeCourses(degreeLocation.value, degreeCourseType.value);
        }
    });

    degreeCourseType.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select a Course');
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetSpecialization();
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeLocation.value && this.value) {
            fetchDegreeCourses(degreeLocation.value, this.value);
        }
    });

    degreeCourse.addEventListener('change', function() {
        resetAndDisable(degreeIntake, 'Select an Intake');
        resetSpecialization();
        resetAndDisable(degreeSemester, 'Select a Semester');
        resetAndDisable(degreeModule, 'Select a Module');
        if (degreeCourse.value && degreeLocation.value) {
            degreeIntake.disabled = false;
            fetchDegreeSpecializations();
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
            fetchExistingExamResults();
        }
        updateResultsHeader();
    });

    degreeSpecialization.addEventListener('change', function() {
        if (allDegreeFilled()) {
            fetchExistingExamResults();
        }
        updateResultsHeader();
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
            fetchExistingExamResults();
        }
        updateResultsHeader();
    });

    // Validation functions
    function allDegreeFilled() {
        return degreeLocation.value && degreeCourseType.value && degreeCourse.value &&
               degreeIntake.value && hasDegreeSpecializationSelection() && degreeSemester.value && degreeModule.value;
    }

    function allCertFilled() {
        return certLocation.value && certCourse.value && certIntake.value;
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

    degreeUpdateAllBtn.addEventListener('click', handleUpdateAll);
    certUpdateAllBtn.addEventListener('click', handleUpdateAll);

    function handleUpdateAll() {
        const activeTab = getActiveTab();
        const resultsTableBody = activeTab === 'degree' ? degreeResultsTableBody : certResultsTableBody;
        const filterData = getFilterData();
        if (!filterData || getCurrentResults().length === 0) {
            showToast('Warning', 'Please select all filters and ensure results are loaded.', 'bg-warning');
            return;
        }

        // Collect all updated results
        const updatedResults = [];
        const rows = resultsTableBody.querySelectorAll('tr');
        rows.forEach(row => {
            const resultId = row.getAttribute('data-result-id');
            const marksInput = row.querySelector('input[name="marks"]');
            const gradeInput = row.querySelector('input[name="grade"]');
            const remarksInput = row.querySelector('input[name="remarks"]');

            if (resultId && marksInput && gradeInput) {
                updatedResults.push({
                    id: parseInt(resultId),
                    marks: parseFloat(marksInput.value) || 0,
                    grade: gradeInput.value.trim(),
                    remarks: remarksInput ? remarksInput.value.trim() : ''
                });
            }
        });

        if (updatedResults.length === 0) {
            showToast('Warning', 'No results to update.', 'bg-warning');
            return;
        }

        const payload = { ...filterData, results: updatedResults };

        showSpinner(true);
        fetch('{{ route("update.result") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message, '#ccffcc');
                setTimeout(() => {
                    fetchExistingExamResults();
                }, 1500);
            } else {
                let errorMsg = data.message || 'An error occurred.';
                if(data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                showToast('Error', errorMsg, 'bg-danger');
            }
        })
        .catch(() => showToast('Error', 'An error occurred while updating results.', 'bg-danger'))
        .finally(() => showSpinner(false));
    }

    function getFilterData() {
        const activeTab = getActiveTab();
        if (activeTab === 'degree') {
            const data = {
                location: degreeLocation.value,
                course_type: degreeCourseType.value,
                course_id: degreeCourse.value,
                intake_id: degreeIntake.value,
                specialization: degreeSpecialization.value,
                semester: degreeSemester.value,
                module_id: degreeModule.value
            };
            return degreeLocation.value && degreeCourseType.value && degreeCourse.value && degreeIntake.value && degreeSemester.value && degreeModule.value && hasDegreeSpecializationSelection()
                ? data
                : null;
        } else {
            const data = {
                location: certLocation.value,
                course_type: 'certificate',
                course_id: certCourse.value,
                intake_id: certIntake.value,
                semester: null,
                module_id: null
            };
            return Object.values(data).filter(v => v !== null).some(v => !v) ? null : data;
        }
    }

    function renderTable() {
        const activeTab = getActiveTab();
        const resultsTableBody = activeTab === 'degree' ? degreeResultsTableBody : certResultsTableBody;
        const results = getCurrentResults();
        resultsTableBody.innerHTML = '';
        results.forEach((result, index) => {
            const row = `<tr data-result-id="${result.id}">
                <td>${result.registration_id}</td>
                <td>${result.student_name}</td>
                <td><input type="number" class="form-control" name="marks" min="0" max="100" step="0.01" value="${result.marks || ''}" onchange="updateResultMark(${index}, this.value)"></td>
                <td><input type="text" class="form-control" name="grade" maxlength="5" value="${result.grade || ''}" onchange="updateResultGrade(${index}, this.value)"></td>
                <td><input type="text" class="form-control" name="remarks" maxlength="255" value="${result.remarks || ''}" onchange="updateResultRemarks(${index}, this.value)" placeholder="Enter remarks"></td>
                <td>${result.updated_at}</td>
            </tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function updateResultsHeader() {
        const activeTab = getActiveTab();
        if (activeTab === 'degree') {
            if (degreeCourse.value && degreeSemester.value && degreeModule.value && hasDegreeSpecializationSelection()) {
                const courseName = degreeCourse.options[degreeCourse.selectedIndex].text;
                const specializationName = degreeSpecializationRow && degreeSpecializationRow.style.display !== 'none'
                    ? ` - ${degreeSpecialization.options[degreeSpecialization.selectedIndex]?.text || ''}`
                    : '';
                const semesterName = degreeSemester.options[degreeSemester.selectedIndex].text;
                const moduleName = degreeModule.options[degreeModule.selectedIndex].text;
                degreeResultsTableHeader.innerHTML = `Exam Results for: ${courseName}${specializationName} - ${semesterName} (${moduleName})`;
                degreeResultsTableHeader.style.display = 'block';
            }
        } else {
            if (certCourse.value && certIntake.value) {
                const courseName = certCourse.options[certCourse.selectedIndex].text;
                const intakeName = certIntake.options[certIntake.selectedIndex].text;
                certResultsTableHeader.innerHTML = `Exam Results for: ${courseName} - ${intakeName}`;
                certResultsTableHeader.style.display = 'block';
            }
        }
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

    function toDateSafe(dateText) {
        if (!dateText) {
            return null;
        }

        const normalized = String(dateText).replace(' ', 'T');
        const date = new Date(normalized);
        return Number.isNaN(date.getTime()) ? null : date;
    }

    function isResultPassed(result) {
        const grade = String(result?.grade ?? '').trim().toUpperCase();
        if (grade) {
            return !['F', 'FAIL', 'AB', 'ABSENT'].includes(grade);
        }

        const marks = Number(result?.marks);
        return Number.isFinite(marks) && marks >= 40;
    }

    function updateStatistics(activeTab, results) {
        const totalStudentsId = activeTab === 'degree' ? 'degreeTotalStudents' : 'certTotalStudents';
        const averageMarksId = activeTab === 'degree' ? 'degreeAverageMarks' : 'certAverageMarks';
        const passRateId = activeTab === 'degree' ? 'degreePassRate' : 'certPassRate';
        const lastUpdatedId = activeTab === 'degree' ? 'degreeLastUpdated' : 'certLastUpdated';

        const totalStudents = results.length;
        const marksList = results
            .map(r => Number(r?.marks))
            .filter(m => Number.isFinite(m));
        const marksTotal = marksList.reduce((sum, value) => sum + value, 0);
        const averageMarks = marksList.length > 0 ? (marksTotal / marksList.length) : 0;

        const passedCount = results.filter(isResultPassed).length;
        const passRate = totalStudents > 0 ? ((passedCount / totalStudents) * 100) : 0;

        const latestDate = results
            .map(r => toDateSafe(r?.updated_at))
            .filter(Boolean)
            .sort((a, b) => b - a)[0] || null;

        document.getElementById(totalStudentsId).textContent = String(totalStudents);
        document.getElementById(averageMarksId).textContent = averageMarks.toFixed(1);
        document.getElementById(passRateId).textContent = `${passRate.toFixed(0)}%`;
        document.getElementById(lastUpdatedId).textContent = latestDate
            ? latestDate.toLocaleString()
            : '-';
    }

    window.updateResultMark = function(index, value) {
        const results = getCurrentResults();
        if (results[index]) {
            results[index].marks = value === '' ? '' : parseFloat(value);
        }
    }

    window.updateResultGrade = function(index, value) {
        const results = getCurrentResults();
        if (results[index]) {
            results[index].grade = value;
        }
    }

    window.updateResultRemarks = function(index, value) {
        const results = getCurrentResults();
        if (results[index]) {
            results[index].remarks = value;
        }
    }

    // Fetch functions for Degree tab
    function fetchDegreeCourses(location, courseType) {
        showSpinner(true);
        fetch(`/exam-results/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`)
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
        fetch(`/exam-results/get-intakes/${degreeCourse.value}/${degreeLocation.value}`)
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
        fetch(`/exam-results/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(degreeSemester, data.semesters, 'id', 'display_name', 'Semester');
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
        fetch('{{ route('exam.results.get.filtered.modules') }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
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
        fetch(`/exam-results/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=certificate`)
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
        fetch(`/exam-results/get-intakes/${certCourse.value}/${certLocation.value}`)
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

    function fetchExistingExamResults() {
        const activeTab = getActiveTab();
        let data;

        if (activeTab === 'degree') {
            data = {
                location: degreeLocation.value,
                course_type: degreeCourseType.value,
                course_id: degreeCourse.value,
                intake_id: degreeIntake.value,
                specialization: degreeSpecialization.value,
                semester: degreeSemester.value,
                module_id: degreeModule.value
            };
        } else {
            data = {
                location: certLocation.value,
                course_type: 'certificate',
                course_id: certCourse.value,
                intake_id: certIntake.value,
                semester: null,
                module_id: null
            };
        }

        showSpinner(true);
        fetch('{{ route("get.existing.exam.results") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            const activeTab = getActiveTab();
            const statusAlert = document.getElementById(activeTab === 'degree' ? 'degreeResultsStatusAlert' : 'certResultsStatusAlert');
            const statusText = document.getElementById(activeTab === 'degree' ? 'degreeResultsStatusText' : 'certResultsStatusText');
            const statisticsCards = activeTab === 'degree' ? degreeStatisticsCards : certStatisticsCards;
            const resultsTableBody = activeTab === 'degree' ? degreeResultsTableBody : certResultsTableBody;
            const resultsTableSection = activeTab === 'degree' ? 'degreeResultsTableSection' : 'certResultsTableSection';
            const updateAllBtnSection = activeTab === 'degree' ? 'degreeUpdateAllBtnSection' : 'certUpdateAllBtnSection';

            if (data.success && data.results && data.results.length > 0) {
                // Show results status
                statusText.textContent = `Found ${data.results.length} existing result(s)`;
                statusAlert.style.display = 'block';

                // Update statistics
                updateStatistics(activeTab, data.results);

                statisticsCards.style.display = 'flex';

                setCurrentResults(data.results);
                renderTable();
                document.getElementById(resultsTableSection).style.display = '';
                document.getElementById(updateAllBtnSection).style.display = '';
                updateResultsHeader();
            } else {
                    resultsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No existing results found for these filters. If you have entered results, please check that all filters (Course, Intake, Semester, Module, Location) match the saved records.</td></tr>';
                document.getElementById(resultsTableSection).style.display = '';
                document.getElementById(updateAllBtnSection).style.display = 'none';
                statisticsCards.style.display = 'none';

                    // Log filter data for debugging
                    console.log('No results found for filters:', {
                        location: activeTab === 'degree' ? degreeLocation.value : certLocation.value,
                        course_type: activeTab === 'degree' ? degreeCourseType.value : 'certificate',
                        course_id: activeTab === 'degree' ? degreeCourse.value : certCourse.value,
                        intake_id: activeTab === 'degree' ? degreeIntake.value : certIntake.value,
                        specialization: activeTab === 'degree' ? degreeSpecialization.value : null,
                        semester: activeTab === 'degree' ? degreeSemester.value : null,
                        module_id: activeTab === 'degree' ? degreeModule.value : null
                    });
            }
        })
            .catch(error => {
                console.error('Error fetching results:', error);
                showToast('Error', 'Failed to fetch existing results. Check console for details.', 'bg-danger');
            })
        .finally(() => showSpinner(false));
    }
});
</script>

<style nonce="{{ $cspNonce }}">
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #fff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #fff transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    #spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
    #exam-filters-bootstrap .form-label {
        font-size: 1rem;
        font-weight: 500;
        color: #222;
        margin-bottom: 0;
        letter-spacing: 0.01em;
        text-align: left;
    }
    #exam-filters-bootstrap .form-select, #exam-filters-bootstrap .form-control {
        border-radius: 8px;
        border: 1px solid #e0e0e0;
        background: #fff;
        font-size: 0.875rem;
        padding: 0.35rem 0.75rem;
        box-shadow: none;
        transition: border-color 0.2s;
        min-height: 32px;
        width: 100%;
    }
    #exam-filters-bootstrap .form-select:focus, #exam-filters-bootstrap .form-control:focus {
        border-color: #a3a3ff;
        outline: none;
        box-shadow: 0 0 0 2px #e0e7ff;
    }
    .table input[type="number"], .table input[type="text"] {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }
    .table input[type="number"]:focus, .table input[type="text"]:focus {
        border-color: #a3a3ff;
        outline: none;
        box-shadow: 0 0 0 2px #e0e7ff;
    }
</style>
@endsection
