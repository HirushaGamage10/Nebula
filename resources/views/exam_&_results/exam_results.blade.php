@extends('inc.app')

@section('title', 'NEBULA | Exam Results')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Exam Result Management</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

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
                    <div id="exam-filters-bootstrap-degree" class="mb-4">
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

                    <!-- Degree Bulk Upload Section -->
                    <div class="card mb-4" id="degreeBulkUploadSection" style="display:none;">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="mb-0">
                                <i class="ti ti-upload me-2"></i>Bulk Upload Exam Results
                            </h6>
                            <button type="button" class="btn btn-outline-primary btn-sm" id="degreeDownloadTemplateBtn">
                                <i class="ti ti-download me-1"></i>Download Template
                            </button>
                        </div>
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <input type="file" class="form-control" id="degreeBulkUploadFile" accept=".csv,.xlsx,.xls">
                                    <small class="text-muted">Select a CSV file with exam results data. Maximum file size: 10MB</small>
                                </div>
                                <div class="col-md-4">
                                    <button type="button" class="btn btn-success w-100" id="degreeUploadResultsBtn">
                                        <i class="ti ti-upload me-1"></i>Upload Results
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Degree Results Table -->
                    <div class="mt-4" id="degreeResultsTableSection" style="display:none;">
                        <h4 id="degreeResultsTableHeader" class="text-center mb-3" style="display: none;"></h4>
                        
                        <!-- Results Status Alert -->
                        <div id="degreeResultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                            <i class="ti ti-info-circle"></i>
                            <strong>Exam Results Status:</strong> 
                            <span id="degreeResultsStatusText"></span>
                        </div>

                        <!-- Add New Student Section -->
                        <div class="card mb-3">
                            <div class="card-header">
                                <h6 class="mb-0">Add New Student</h6>
                            </div>
                            <div class="card-body">
                                <div class="row align-items-center">
                                    <div class="col-md-4">
                                        <label for="degree_new_student_id" class="form-label">Registration Number</label>
                                        <input type="text" class="form-control" id="degree_new_student_id" placeholder="Enter Registration Number">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="degree_new_student_name" class="form-label">Student Name</label>
                                        <input type="text" class="form-control" id="degree_new_student_name" placeholder="Student Name" readonly>
                                    </div>
                                    <div class="col-md-4 d-flex align-items-end">
                                        <button type="button" class="btn btn-success" id="degreeAddStudentBtn">
                                            <i class="ti ti-plus"></i> Add Student
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Column Management Buttons -->
                        <div class="mb-3 d-flex gap-2 justify-content-center">
                            <button type="button" class="btn btn-outline-primary" id="degreeAddMarksColumnBtn">
                                <i class="ti ti-plus"></i> Add Marks Column
                            </button>
                            <button type="button" class="btn btn-outline-success" id="degreeAddGradeColumnBtn">
                                <i class="ti ti-plus"></i> Add Grade Column
                            </button>
                            <button type="button" class="btn btn-outline-info" id="degreeAddRemarksColumnBtn">
                                <i class="ti ti-plus"></i> Add Remarks Column
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="degreeRemoveMarksColumnBtn" style="display: none;">
                                <i class="ti ti-minus"></i> Remove Marks Column
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="degreeRemoveGradeColumnBtn" style="display: none;">
                                <i class="ti ti-minus"></i> Remove Grade Column
                            </button>
                            <button type="button" class="btn btn-outline-danger" id="degreeRemoveRemarksColumnBtn" style="display: none;">
                                <i class="ti ti-minus"></i> Remove Remarks Column
                            </button>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered" id="degreeResultsTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                    </tr>
                                </thead>
                                <tbody id="degreeResultsTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Degree Submit Button -->
                    <div class="text-center mt-4" id="degreeSaveAllBtnSection" style="display:none;">
                        <button type="button" id="degreeSaveAllBtn" class="btn btn-primary w-100 py-2">Save All Results</button>
                    </div>
                </div>

                <!-- Certificate Tab -->
                <div class="tab-pane fade" id="certificate-panel" role="tabpanel">
                    <div id="exam-filters-bootstrap-cert" class="mb-4">
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

                    <!-- Certificate Bulk Upload Section -->
                    <div class="card mb-4" id="certBulkUploadSection" style="display:none;">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="mb-0">
                        <i class="ti ti-upload me-2"></i>Bulk Upload Exam Results
                    </h6>
                    <button type="button" class="btn btn-outline-primary btn-sm" id="certDownloadTemplateBtn">
                        <i class="ti ti-download me-1"></i>Download Template
                    </button>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <input type="file" class="form-control" id="certBulkUploadFile" accept=".csv,.xlsx,.xls">
                            <small class="text-muted">Select a CSV file with exam results data. Maximum file size: 10MB</small>
                        </div>
                        <div class="col-md-4">
                            <button type="button" class="btn btn-success w-100" id="certUploadResultsBtn">
                                <i class="ti ti-upload me-1"></i>Upload Results
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Certificate Results Table -->
            <div class="mt-4" id="certResultsTableSection" style="display:none;">
                <h4 id="certResultsTableHeader" class="text-center mb-3" style="display: none;"></h4>
                
                <!-- Results Status Alert -->
                <div id="certResultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                    <i class="ti ti-info-circle"></i>
                    <strong>Exam Results Status:</strong> 
                    <span id="certResultsStatusText"></span>
                </div>

                <!-- Add New Student Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Add New Student</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="cert_new_student_id" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" id="cert_new_student_id" placeholder="Enter Registration Number">
                            </div>
                            <div class="col-md-4">
                                <label for="cert_new_student_name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="cert_new_student_name" placeholder="Student Name" readonly>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-success" id="certAddStudentBtn">
                                    <i class="ti ti-plus"></i> Add Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column Management Buttons -->
                <div class="mb-3 d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-primary" id="certAddMarksColumnBtn">
                        <i class="ti ti-plus"></i> Add Marks Column
                    </button>
                    <button type="button" class="btn btn-outline-success" id="certAddGradeColumnBtn">
                        <i class="ti ti-plus"></i> Add Grade Column
                    </button>
                    <button type="button" class="btn btn-outline-info" id="certAddRemarksColumnBtn">
                        <i class="ti ti-plus"></i> Add Remarks Column
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="certRemoveMarksColumnBtn" style="display: none;">
                        <i class="ti ti-minus"></i> Remove Marks Column
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="certRemoveGradeColumnBtn" style="display: none;">
                        <i class="ti ti-minus"></i> Remove Grade Column
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="certRemoveRemarksColumnBtn" style="display: none;">
                        <i class="ti ti-minus"></i> Remove Remarks Column
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="certResultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                            </tr>
                        </thead>
                        <tbody id="certResultsTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Certificate Submit Button -->
            <div class="text-center mt-4" id="certSaveAllBtnSection" style="display:none;">
                <button type="button" id="certSaveAllBtn" class="btn btn-primary w-100 py-2">Save All Results</button>
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
    
    // Degree Tab Elements
    const degreeLocation = document.getElementById('degree_location');
    const degreeCourseType = document.getElementById('degree_course_type');
    const degreeCourse = document.getElementById('degree_course');
    const degreeIntake = document.getElementById('degree_intake');
    const degreeSemester = document.getElementById('degree_semester');
    const degreeModule = document.getElementById('degree_module');
    
    // Certificate Tab Elements
    const certLocation = document.getElementById('cert_location');
    const certCourse = document.getElementById('cert_course');
    const certIntake = document.getElementById('cert_intake');
    
    // Degree-specific elements
    const degreeAddStudentBtn = document.getElementById('degreeAddStudentBtn');
    const degreeResultsTableBody = document.getElementById('degreeResultsTableBody');
    const degreeSaveAllBtn = document.getElementById('degreeSaveAllBtn');
    const degreeResultsTableHeader = document.getElementById('degreeResultsTableHeader');
    const degreeSaveAllBtnSection = document.getElementById('degreeSaveAllBtnSection');
    const degreeAddMarksColumnBtn = document.getElementById('degreeAddMarksColumnBtn');
    const degreeAddGradeColumnBtn = document.getElementById('degreeAddGradeColumnBtn');
    const degreeAddRemarksColumnBtn = document.getElementById('degreeAddRemarksColumnBtn');
    const degreeRemoveMarksColumnBtn = document.getElementById('degreeRemoveMarksColumnBtn');
    const degreeRemoveGradeColumnBtn = document.getElementById('degreeRemoveGradeColumnBtn');
    const degreeRemoveRemarksColumnBtn = document.getElementById('degreeRemoveRemarksColumnBtn');
    
    // Certificate-specific elements
    const certAddStudentBtn = document.getElementById('certAddStudentBtn');
    const certResultsTableBody = document.getElementById('certResultsTableBody');
    const certSaveAllBtn = document.getElementById('certSaveAllBtn');
    const certResultsTableHeader = document.getElementById('certResultsTableHeader');
    const certSaveAllBtnSection = document.getElementById('certSaveAllBtnSection');
    const certAddMarksColumnBtn = document.getElementById('certAddMarksColumnBtn');
    const certAddGradeColumnBtn = document.getElementById('certAddGradeColumnBtn');
    const certAddRemarksColumnBtn = document.getElementById('certAddRemarksColumnBtn');
    const certRemoveMarksColumnBtn = document.getElementById('certRemoveMarksColumnBtn');
    const certRemoveGradeColumnBtn = document.getElementById('certRemoveGradeColumnBtn');
    const certRemoveRemarksColumnBtn = document.getElementById('certRemoveRemarksColumnBtn');
    
    // Tab event listeners to ensure proper section visibility
    const degreeTabBtn = document.getElementById('degree-tab');
    const certTabBtn = document.getElementById('certificate-tab');
    
    degreeTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to degree tab, hide cert sections if they're visible
        document.getElementById('certBulkUploadSection').style.display = 'none';
        document.getElementById('certResultsTableSection').style.display = 'none';
        document.getElementById('certSaveAllBtnSection').style.display = 'none';
    });
    
    certTabBtn.addEventListener('shown.bs.tab', function() {
        // When switching to cert tab, hide degree sections if they're visible
        document.getElementById('degreeBulkUploadSection').style.display = 'none';
        document.getElementById('degreeResultsTableSection').style.display = 'none';
        document.getElementById('degreeSaveAllBtnSection').style.display = 'none';
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
    
    function getTabElements() {
        const activeTab = getActiveTab();
        if (activeTab === 'degree') {
            return {
                resultsTableBody: degreeResultsTableBody,
                addMarksColumnBtn: degreeAddMarksColumnBtn,
                addGradeColumnBtn: degreeAddGradeColumnBtn,
                addRemarksColumnBtn: degreeAddRemarksColumnBtn,
                removeMarksColumnBtn: degreeRemoveMarksColumnBtn,
                removeGradeColumnBtn: degreeRemoveGradeColumnBtn,
                removeRemarksColumnBtn: degreeRemoveRemarksColumnBtn,
                resultsTable: document.getElementById('degreeResultsTable'),
                addStudentBtn: degreeAddStudentBtn,
                saveAllBtn: degreeSaveAllBtn,
                resultsTableHeader: degreeResultsTableHeader
            };
        } else {
            return {
                resultsTableBody: certResultsTableBody,
                addMarksColumnBtn: certAddMarksColumnBtn,
                addGradeColumnBtn: certAddGradeColumnBtn,
                addRemarksColumnBtn: certAddRemarksColumnBtn,
                removeMarksColumnBtn: certRemoveMarksColumnBtn,
                removeGradeColumnBtn: certRemoveGradeColumnBtn,
                removeRemarksColumnBtn: certRemoveRemarksColumnBtn,
                resultsTable: document.getElementById('certResultsTable'),
                addStudentBtn: certAddStudentBtn,
                saveAllBtn: certSaveAllBtn,
                resultsTableHeader: certResultsTableHeader
            };
        }
    }
    
    // Function to reset table to base structure (only two columns)
    function resetTableStructure() {
        const { resultsTableBody, addMarksColumnBtn, addGradeColumnBtn, addRemarksColumnBtn,
                removeMarksColumnBtn, removeGradeColumnBtn, removeRemarksColumnBtn, resultsTable } = getTabElements();
        
        // Clear existing data
        resultsTableBody.innerHTML = '';
        
        // Remove any existing dynamic columns
        const existingMarksHeader = document.getElementById('marksColumnHeader');
        const existingGradeHeader = document.getElementById('gradeColumnHeader');
        const existingMarksInput = document.getElementById('marksInputCell');
        const existingGradeInput = document.getElementById('gradeInputCell');
        const existingRemarksHeader = document.getElementById('remarksHeaderCell');
        const existingRemarksInput = document.getElementById('remarksInputCell');
        
        if (existingMarksHeader) existingMarksHeader.remove();
        if (existingGradeHeader) existingGradeHeader.remove();
        if (existingRemarksHeader) existingRemarksHeader.remove();
        if (existingMarksInput) existingMarksInput.remove();
        if (existingGradeInput) existingGradeInput.remove();
        if (existingRemarksInput) existingRemarksInput.remove();
        
        // Reset button states
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        addRemarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
        removeRemarksColumnBtn.style.display = 'none';
    }
    
    // Call reset function on page load
    resetTableStructure();
    
    // Show Add New Student section by default
    const addStudentSection = document.querySelector('.card.mb-3');
    if (addStudentSection) {
        addStudentSection.style.display = 'block';
    }
    
    // Function to ensure table has only two columns
    function ensureTwoColumns() {
        const { resultsTableBody, addMarksColumnBtn, addGradeColumnBtn, addRemarksColumnBtn,
                removeMarksColumnBtn, removeGradeColumnBtn, removeRemarksColumnBtn, resultsTable } = getTabElements();
        
        // Clear all existing data rows
        resultsTableBody.innerHTML = '';
        
        // Remove any dynamic columns from header
        const tableHeader = resultsTable.querySelector('thead tr');
        if (tableHeader) {
            const headers = tableHeader.querySelectorAll('th');
            if (headers.length > 2) {
                for (let i = 2; i < headers.length; i++) {
                    headers[i].remove();
                }
            }
        }
        
        // Reset button states
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        addRemarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
        removeRemarksColumnBtn.style.display = 'none';
    }
    
    // Ensure clean two-column structure on page load
    ensureTwoColumns();

    // Helper to reset and disable dropdowns
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
        ensureTwoColumns();
        if (degreeSaveAllBtnSection) degreeSaveAllBtnSection.style.display = 'none';
        if (allDegreeFilled()) {
            fetchDegreeStudentsForResultEntry();
        }
        updateDegreeResultsHeader();
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
        ensureTwoColumns();
        if (certSaveAllBtnSection) certSaveAllBtnSection.style.display = 'none';
        if (allCertFilled()) {
            fetchCertStudentsForResultEntry();
        }
        updateCertResultsHeader();
    });

    // Validation functions
    function allDegreeFilled() {
        return degreeLocation.value && degreeCourseType.value && degreeCourse.value && 
               degreeIntake.value && degreeSemester.value && degreeModule.value;
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
        fetch('{{ route("exam.results.get.filtered.modules") }}', {
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

    // Result header functions
    function updateDegreeResultsHeader() {
        const courseName = degreeCourse.options[degreeCourse.selectedIndex].text;
        const moduleName = degreeModule.options[degreeModule.selectedIndex].text;
        const semesterName = degreeSemester.options[degreeSemester.selectedIndex].text;
        degreeResultsTableHeader.innerHTML = `Exam Results for: ${courseName} - ${semesterName} (${moduleName})`;
        degreeResultsTableHeader.style.display = 'block';
    }

    function updateCertResultsHeader() {
        const courseName = certCourse.options[certCourse.selectedIndex].text;
        const intakeName = certIntake.options[certIntake.selectedIndex].text;
        certResultsTableHeader.innerHTML = `Exam Results for: ${courseName} - ${intakeName}`;
        certResultsTableHeader.style.display = 'block';
    }


    
    // Column management event listeners
    degreeAddMarksColumnBtn.addEventListener('click', function() {
        const { resultsTable, addMarksColumnBtn, removeMarksColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const marksHeader = document.createElement('th');
        marksHeader.id = 'marksColumnHeader';
        marksHeader.textContent = 'Marks';
        tableHeader.appendChild(marksHeader);
        
        addMarksColumnBtn.style.display = 'none';
        removeMarksColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    certAddMarksColumnBtn.addEventListener('click', function() {
        const { resultsTable, addMarksColumnBtn, removeMarksColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const marksHeader = document.createElement('th');
        marksHeader.id = 'marksColumnHeader';
        marksHeader.textContent = 'Marks';
        tableHeader.appendChild(marksHeader);
        
        addMarksColumnBtn.style.display = 'none';
        removeMarksColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    degreeRemoveMarksColumnBtn.addEventListener('click', function() {
        const { addMarksColumnBtn, removeMarksColumnBtn } = getTabElements();
        const marksHeader = document.getElementById('marksColumnHeader');
        if (marksHeader) marksHeader.remove();
        
        addMarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    certRemoveMarksColumnBtn.addEventListener('click', function() {
        const { addMarksColumnBtn, removeMarksColumnBtn } = getTabElements();
        const marksHeader = document.getElementById('marksColumnHeader');
        if (marksHeader) marksHeader.remove();
        
        addMarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    degreeAddGradeColumnBtn.addEventListener('click', function() {
        const { resultsTable, addGradeColumnBtn, removeGradeColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const gradeHeader = document.createElement('th');
        gradeHeader.id = 'gradeColumnHeader';
        gradeHeader.textContent = 'Grade';
        tableHeader.appendChild(gradeHeader);
        
        addGradeColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    certAddGradeColumnBtn.addEventListener('click', function() {
        const { resultsTable, addGradeColumnBtn, removeGradeColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const gradeHeader = document.createElement('th');
        gradeHeader.id = 'gradeColumnHeader';
        gradeHeader.textContent = 'Grade';
        tableHeader.appendChild(gradeHeader);
        
        addGradeColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    degreeRemoveGradeColumnBtn.addEventListener('click', function() {
        const { addGradeColumnBtn, removeGradeColumnBtn } = getTabElements();
        const gradeHeader = document.getElementById('gradeColumnHeader');
        if (gradeHeader) gradeHeader.remove();
        
        addGradeColumnBtn.style.display = 'inline-block';
        removeGradeColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    certRemoveGradeColumnBtn.addEventListener('click', function() {
        const { addGradeColumnBtn, removeGradeColumnBtn } = getTabElements();
        const gradeHeader = document.getElementById('gradeColumnHeader');
        if (gradeHeader) gradeHeader.remove();
        
        addGradeColumnBtn.style.display = 'inline-block';
        removeGradeColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    // Add Remarks Column Event Handler
    degreeAddRemarksColumnBtn.addEventListener('click', function() {
        const { resultsTable, addRemarksColumnBtn, removeRemarksColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const remarksHeader = document.createElement('th');
        remarksHeader.id = 'remarksColumnHeader';
        remarksHeader.textContent = 'Remarks';
        tableHeader.appendChild(remarksHeader);
        
        addRemarksColumnBtn.style.display = 'none';
        removeRemarksColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    certAddRemarksColumnBtn.addEventListener('click', function() {
        const { resultsTable, addRemarksColumnBtn, removeRemarksColumnBtn } = getTabElements();
        const tableHeader = resultsTable.querySelector('thead tr');
        const remarksHeader = document.createElement('th');
        remarksHeader.id = 'remarksColumnHeader';
        remarksHeader.textContent = 'Remarks';
        tableHeader.appendChild(remarksHeader);
        
        addRemarksColumnBtn.style.display = 'none';
        removeRemarksColumnBtn.style.display = 'inline-block';
        
        updateExistingRows();
    });
    
    // Remove Remarks Column Event Handler
    degreeRemoveRemarksColumnBtn.addEventListener('click', function() {
        const { addRemarksColumnBtn, removeRemarksColumnBtn } = getTabElements();
        const remarksHeader = document.getElementById('remarksColumnHeader');
        if (remarksHeader) remarksHeader.remove();
        
        addRemarksColumnBtn.style.display = 'inline-block';
        removeRemarksColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    certRemoveRemarksColumnBtn.addEventListener('click', function() {
        const { addRemarksColumnBtn, removeRemarksColumnBtn } = getTabElements();
        const remarksHeader = document.getElementById('remarksColumnHeader');
        if (remarksHeader) remarksHeader.remove();
        
        addRemarksColumnBtn.style.display = 'inline-block';
        removeRemarksColumnBtn.style.display = 'none';
        
        updateExistingRows();
    });
    
    // Function to update existing rows when columns are added/removed
    function updateExistingRows() {
        const { resultsTableBody } = getTabElements();
        const rows = resultsTableBody.querySelectorAll('tr');
        rows.forEach((row, rowIndex) => {
            // Remove any extra cells beyond the first two
            const cells = row.querySelectorAll('td');
            if (cells.length > 2) {
                for (let i = 2; i < cells.length; i++) {
                    cells[i].remove();
                }
            }
            
            // Add Marks column if it exists
            const marksVisible = document.getElementById('marksColumnHeader') !== null;
            if (marksVisible) {
                const marksCell = document.createElement('td');
                marksCell.className = 'marks-cell';
                marksCell.innerHTML = `<input type="number" class="form-control" min="0" max="100" placeholder="Marks" onchange="updateResultMark(${rowIndex}, this.value)">`;
                row.appendChild(marksCell);
            }
            
            // Add Grade column if it exists
            const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
            if (gradeVisible) {
                const gradeCell = document.createElement('td');
                gradeCell.className = 'grade-cell';
                gradeCell.innerHTML = `<input type="text" class="form-control" maxlength="5" placeholder="Grade" onchange="updateResultGrade(${rowIndex}, this.value)">`;
                row.appendChild(gradeCell);
            }
            
            // Add Remarks column if it exists
            const remarksVisible = document.getElementById('remarksColumnHeader') !== null;
            if (remarksVisible) {
                const remarksCell = document.createElement('td');
                remarksCell.className = 'remarks-cell';
                remarksCell.innerHTML = `<input type="text" class="form-control" maxlength="255" placeholder="Remarks" data-field="remarks" onchange="updateResultRemarks(${rowIndex}, this.value)">`;
                row.appendChild(remarksCell);
            }
        });
    }







    // Event listeners
    degreeAddStudentBtn.addEventListener('click', handleAddStudent);
    certAddStudentBtn.addEventListener('click', handleAddStudent);
    degreeSaveAllBtn.addEventListener('click', handleSaveAll);
    certSaveAllBtn.addEventListener('click', handleSaveAll);
    
    // Bulk upload elements
    const degreeDownloadTemplateBtn = document.getElementById('degreeDownloadTemplateBtn');
    const degreeUploadResultsBtn = document.getElementById('degreeUploadResultsBtn');
    const degreeBulkUploadFile = document.getElementById('degreeBulkUploadFile');
    
    const certDownloadTemplateBtn = document.getElementById('certDownloadTemplateBtn');
    const certUploadResultsBtn = document.getElementById('certUploadResultsBtn');
    const certBulkUploadFile = document.getElementById('certBulkUploadFile');
    
    // Bulk upload event listeners
    degreeDownloadTemplateBtn.addEventListener('click', handleDownloadTemplate);
    degreeUploadResultsBtn.addEventListener('click', handleBulkUpload);
    certDownloadTemplateBtn.addEventListener('click', handleDownloadTemplate);
    certUploadResultsBtn.addEventListener('click', handleBulkUpload);

    function handleAddStudent() {
        const activeTab = getActiveTab();
        const studentIdField = activeTab === 'degree' ? 'degree_new_student_id' : 'cert_new_student_id';
        const studentNameField = activeTab === 'degree' ? 'degree_new_student_name' : 'cert_new_student_name';
        
        const studentId = document.getElementById(studentIdField).value.trim();
        const results = getCurrentResults();
        
        // Validate required fields
        if (!studentId) {
            showToast('Warning', 'Please enter Student ID.', 'bg-warning');
            return;
        }

        if (results.some(r => r.student_id === studentId)) {
            showToast('Warning', 'This student has already been added.', 'bg-warning');
            return;
        }

        showSpinner(true);
        fetch('{{ route("get.student.name") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({ student_id: studentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const results = getCurrentResults();
                const studentData = { student_id: studentId, name: data.name };
                results.push(studentData);
                setCurrentResults(results);
                renderTable();
                clearInputFields();
                // Update the student name field
                document.getElementById(studentNameField).value = data.name;
            } else {
                showToast('Error', data.message || 'Could not find student.', 'bg-danger');
            }
        })
        .catch(() => showToast('Error', 'An error occurred while fetching student details.', 'bg-danger'))
        .finally(() => showSpinner(false));
    }

    function handleSaveAll() {
        const filterData = getFilterData();
        const results = getCurrentResults();
        if (!filterData || results.length === 0) {
            showToast('Warning', 'Please select all filters and add at least one student result.', 'bg-warning');
            return;
        }

        // Filter out empty values and ensure at least one field is filled
        const filteredResults = results.map(result => {
            const filtered = { student_id: result.student_id };
            if (result.marks !== '' && result.marks !== null) {
                filtered.marks = result.marks;
            }
            if (result.grade !== '' && result.grade !== null) {
                filtered.grade = result.grade;
            }
            if (result.remarks !== '' && result.remarks !== null) {
                filtered.remarks = result.remarks;
            }
            return filtered;
        }).filter(result => result.marks !== undefined || result.grade !== undefined || result.remarks !== undefined);
        
        if (filteredResults.length === 0) {
            showToast('Warning', 'Please enter at least marks, grade, or remarks for at least one student.', 'bg-warning');
            return;
        }
        
        const payload = { ...filterData, results: filteredResults };
        
        showSpinner(true);
        fetch('{{ route("store.result") }}', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(payload)
        })
        .then(async response => {
            const data = await response.json().catch(() => null);

            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            console.log('Response data:', data);
            
            if (!response.ok) {
                let errorMsg = data?.message || `HTTP error! status: ${response.status}`;
                if (data?.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                throw new Error(errorMsg);
            }
            
            return data;
        })
        .then(data => {
            if (data.success) {
                showToast('Success', data.message, '#ccffcc');
                setTimeout(function() {
                    location.reload();
                }, 1500);
                setCurrentResults([]);
                renderTable();
                // Clear the form fields
                const activeTab = getActiveTab();
                const studentIdField = activeTab === 'degree' ? 'degree_new_student_id' : 'cert_new_student_id';
                const studentNameField = activeTab === 'degree' ? 'degree_new_student_name' : 'cert_new_student_name';
                document.getElementById(studentIdField).value = '';
                document.getElementById(studentNameField).value = '';
                updateResultsHeader();
            } else {
                let errorMsg = data.message || 'An error occurred.';
                if(data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                showToast('Error', errorMsg, 'bg-danger');
            }
        })
        .catch(error => {
            console.error('Save error:', error);
            console.error('Error details:', error.message);
            showToast('Error', error.message || 'An error occurred while saving results.', 'bg-danger');
        })
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
                semester: degreeSemester.value,
                module_id: degreeModule.value
            };
            return Object.values(data).some(v => !v) ? null : data;
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
        const { resultsTableBody } = getTabElements();
        const results = getCurrentResults();
        resultsTableBody.innerHTML = '';
        results.forEach((result, index) => {
            const marksVisible = document.getElementById('marksColumnHeader') !== null;
            const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
            const remarksVisible = document.getElementById('remarksColumnHeader') !== null;
            
            let row = `<tr>
                <td>${result.student_id}</td>
                <td>${result.name}</td>`;
            
            if (marksVisible) {
                row += `<td class="marks-cell"><input type="number" class="form-control" min="0" max="100" placeholder="Marks" value="${result.marks || ''}" onchange="updateResultMark(${index}, this.value)"></td>`;
            }
            
            if (gradeVisible) {
                row += `<td class="grade-cell"><input type="text" class="form-control" maxlength="5" placeholder="Grade" value="${result.grade || ''}" onchange="updateResultGrade(${index}, this.value)"></td>`;
            }
            
            if (remarksVisible) {
                row += `<td class="remarks-cell"><input type="text" class="form-control" maxlength="255" placeholder="Remarks" value="${result.remarks || ''}" onchange="updateResultRemarks(${index}, this.value)"></td>`;
            }
            
            row += `</tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function clearInputFields() {
        const activeTab = getActiveTab();
        const studentIdField = activeTab === 'degree' ? 'degree_new_student_id' : 'cert_new_student_id';
        const studentNameField = activeTab === 'degree' ? 'degree_new_student_name' : 'cert_new_student_name';
        
        document.getElementById(studentIdField).value = '';
        document.getElementById(studentNameField).value = '';
        document.getElementById(studentIdField).focus();
    }

    function updateResultsHeader() {
        const activeTab = getActiveTab();
        if (activeTab === 'degree') {
            updateDegreeResultsHeader();
        } else {
            updateCertResultsHeader();
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



    function fetchDegreeStudentsForResultEntry() {
        const data = {
            location: degreeLocation.value,
            course_type: degreeCourseType.value,
            course_id: degreeCourse.value,
            intake_id: degreeIntake.value,
            semester: degreeSemester.value,
            module_id: degreeModule.value
        };
        showSpinner(true);
        fetch('/get-students-for-exam-result', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Degree students response:', data);
            
            // Show bulk upload section when all filters are filled
            document.getElementById('degreeBulkUploadSection').style.display = 'block';
            
            if (data.success && data.students && data.students.length > 0) {
                // Map students to include all necessary fields
                degreeResults = data.students.map(s => ({
                    registration_id: s.registration_id || s.registration_number,
                    student_id: s.student_id,
                    name: s.name || s.name_with_initials,
                    marks: s.marks || '',
                    grade: s.grade || '',
                    remarks: s.remarks || ''
                }));
                
                renderEditableResultsTable(data.students);
                document.getElementById('degreeResultsTableSection').style.display = '';
                document.getElementById('degreeSaveAllBtnSection').style.display = '';
            } else {
                degreeResults = [];
                degreeResultsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No students found for these filters.</td></tr>';
                document.getElementById('degreeResultsTableSection').style.display = '';
                document.getElementById('degreeSaveAllBtnSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching degree students:', error);
            showToast('Error', 'Failed to fetch students.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    }

    function fetchCertStudentsForResultEntry() {
        const data = {
            location: certLocation.value,
            course_type: 'certificate',
            course_id: certCourse.value,
            intake_id: certIntake.value,
            semester: null,
            module_id: null
        };
        showSpinner(true);
        fetch('/get-students-for-exam-result', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            console.log('Certificate students response:', data);
            
            // Show bulk upload section when all filters are filled
            document.getElementById('certBulkUploadSection').style.display = 'block';
            
            if (data.success && data.students && data.students.length > 0) {
                // Map students to include all necessary fields
                certResults = data.students.map(s => ({
                    registration_id: s.registration_id || s.registration_number,
                    student_id: s.student_id,
                    name: s.name || s.name_with_initials,
                    marks: s.marks || '',
                    grade: s.grade || '',
                    remarks: s.remarks || ''
                }));
                
                renderEditableResultsTable(data.students);
                document.getElementById('certResultsTableSection').style.display = '';
                document.getElementById('certSaveAllBtnSection').style.display = '';
            } else {
                certResults = [];
                certResultsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No students found for these filters.</td></tr>';
                document.getElementById('certResultsTableSection').style.display = '';
                document.getElementById('certSaveAllBtnSection').style.display = 'none';
            }
        })
        .catch(error => {
            console.error('Error fetching certificate students:', error);
            showToast('Error', 'Failed to fetch students.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
    }



    // Render table with only two columns
    function renderEditableResultsTable(students) {
        const activeTab = getActiveTab();
        const resultsTableBody = activeTab === 'degree' ? degreeResultsTableBody : certResultsTableBody;
        
        const results = students.map(s => ({ 
            registration_id: s.registration_id || s.registration_number,
            student_id: s.student_id, 
            name: s.name || s.name_with_initials, 
            marks: s.marks || '', 
            grade: s.grade || '',
            remarks: s.remarks || ''
        }));
        setCurrentResults(results);
        resultsTableBody.innerHTML = '';
        results.forEach((result, index) => {
            const row = `<tr>
                <td>${result.registration_id}</td>
                <td>${result.name}</td>
            </tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
        
        // If Marks, Grade, or Remarks columns are visible, update them with existing data
        const marksVisible = document.getElementById('marksColumnHeader') !== null;
        const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
        const remarksVisible = document.getElementById('remarksColumnHeader') !== null;
        
        if (marksVisible || gradeVisible || remarksVisible) {
            updateExistingRows();
        }
    }

    window.updateResultMark = function(index, value) {
        const results = getCurrentResults();
        if (results[index]) {
            results[index].marks = value;
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



    // Bulk upload functions
    function handleDownloadTemplate() {
        const filterData = getFilterData();
        if (!filterData) {
            showToast('Warning', 'Please select all filters first to download the template.', 'bg-warning');
            return;
        }

        showSpinner(true);
        
        // Call the backend to download template with actual student data
        fetch('{{ route("download.exam.results.template") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(filterData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.blob();
        })
        .then(blob => {
            // Get the selected values for the filename
            const activeTab = getActiveTab();
            let courseName, moduleName, intakeName;
            
            if (activeTab === 'degree') {
                courseName = degreeCourse.options[degreeCourse.selectedIndex].text;
                moduleName = degreeModule.options[degreeModule.selectedIndex].text;
                intakeName = degreeIntake.options[degreeIntake.selectedIndex].text;
            } else {
                courseName = certCourse.options[certCourse.selectedIndex].text;
                moduleName = 'Certificate';
                intakeName = certIntake.options[certIntake.selectedIndex].text;
            }
            
            // Create and download file
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.href = url;
            a.download = `exam_results_template_${courseName.replace(/\s+/g, '_')}_${moduleName.replace(/\s+/g, '_')}_${intakeName.replace(/\s+/g, '_')}.csv`;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            window.URL.revokeObjectURL(url);
            
            showToast('Success', 'Template downloaded successfully with actual student data!', '#ccffcc');
        })
        .catch(error => {
            console.error('Error downloading template:', error);
            showToast('Error', 'Failed to download template. Please try again.', 'bg-danger');
        })
        .finally(() => {
            showSpinner(false);
        });
    }

    function handleBulkUpload() {
        const activeTab = getActiveTab();
        const file = activeTab === 'degree' ? degreeBulkUploadFile.files[0] : certBulkUploadFile.files[0];
        if (!file) {
            showToast('Warning', 'Please select a file to upload.', 'bg-warning');
            return;
        }

        // Validate file type
        const allowedTypes = ['text/csv', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'];
        if (!allowedTypes.includes(file.type) && !file.name.toLowerCase().endsWith('.csv')) {
            showToast('Error', 'Please select a valid CSV file.', 'bg-danger');
            return;
        }

        // Validate file size (10MB limit)
        if (file.size > 10 * 1024 * 1024) {
            showToast('Error', 'File size must be less than 10MB.', 'bg-danger');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('format', 'csv');

        showSpinner(true);
        fetch('/data-import/exam-results', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const importedCount = data.imported_count || 0;
                const failedCount = data.failed_count || 0;
                
                let message = `Bulk upload completed successfully! ${importedCount} exam results imported.`;
                if (failedCount > 0) {
                    message += ` ${failedCount} records failed to import.`;
                }
                
                showToast('Success', message, '#ccffcc');
                
                // Clear the file input
                const activeTab = getActiveTab();
                if (activeTab === 'degree') {
                    degreeBulkUploadFile.value = '';
                } else {
                    certBulkUploadFile.value = '';
                }
                
                // Refresh the current view to show updated results
                const allFilled = activeTab === 'degree' ? allDegreeFilled() : allCertFilled();
                if (allFilled) {
                    if (activeTab === 'degree') {
                        fetchDegreeStudentsForResultEntry();
                    } else {
                        fetchCertStudentsForResultEntry();
                    }
                }
            } else {
                showToast('Error', 'Upload failed: ' + data.message, 'bg-danger');
            }
        })
        .catch(error => {
            console.error('Upload error:', error);
            showToast('Error', 'An error occurred during upload. Please try again.', 'bg-danger');
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
</style>
@endsection