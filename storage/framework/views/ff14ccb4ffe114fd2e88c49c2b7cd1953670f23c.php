<?php $__env->startSection('title', 'NEBULA | View & Edit Exam Results'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">View & Edit Exam Results</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Filters -->
            <div id="exam-filters-bootstrap" class="mb-4">
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
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="intake" name="intake_id" required>
                                <option selected disabled value="">Select an Intake</option>
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

            <!-- Results Table -->
            <div class="mt-4" id="resultsTableSection" style="display:none;">
                <h4 id="resultsTableHeader" class="text-center mb-3" style="display: none;"></h4>
                
                <!-- Results Status Alert -->
                <div id="resultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                    <i class="ti ti-info-circle"></i>
                    <strong>Exam Results Status:</strong> 
                    <span id="resultsStatusText"></span>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4" id="statisticsCards" style="display: none;">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Total Students</h5>
                                <h3 id="totalStudents">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Average Marks</h5>
                                <h3 id="averageMarks">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Pass Rate</h5>
                                <h3 id="passRate">0%</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5 class="card-title">Last Updated</h5>
                                <h6 id="lastUpdated">-</h6>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="resultsTable">
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
                        <tbody id="resultsTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4" id="updateAllBtnSection" style="display:none;">
                <div class="row">
                    <div class="col-md-6">
                        <button type="button" id="autoCalculateGradesBtn" class="btn btn-success w-100 py-2 mb-2">
                            <i class="ti ti-calculator me-2"></i>Auto-Calculate Grades from Marks
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button type="button" id="updateAllBtn" class="btn btn-primary w-100 py-2 mb-2">Update All Results</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let results = [];
    const courseSelect = document.getElementById('course');
    const moduleSelect = document.getElementById('module');
    const semesterSelect = document.getElementById('semester');
    const updateAllBtn = document.getElementById('updateAllBtn');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const resultsTableHeader = document.getElementById('resultsTableHeader');
    const locationSelect = document.getElementById('location');
    const intakeSelect = document.getElementById('intake');
    const courseTypeSelect = document.getElementById('course_type');
    const fieldsContainer = document.getElementById('fields-container');
    const semesterRow = document.getElementById('semester-row');
    const statisticsCards = document.getElementById('statisticsCards');
    
    // Clear any existing data rows on page load to ensure clean state
    resultsTableBody.innerHTML = '';
    
    // Helper to reset and disable dropdowns
    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    // On load, reset and disable all except location
    resetAndDisable(courseSelect, 'Select a Course');
    resetAndDisable(intakeSelect, 'Select an Intake');
    resetAndDisable(semesterSelect, 'Select a Semester');
    resetAndDisable(moduleSelect, 'Select a Module');

    // Enable course if location is pre-selected
    if (locationSelect.value) {
        fetchCoursesByLocation(locationSelect.value, courseTypeSelect.value);
    }

    // When location changes
    locationSelect.addEventListener('change', function() {
        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        courseSelect.value = '';
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        // Only fetch courses if both location and course type are selected
        if (locationSelect.value && courseTypeSelect.value) {
            fetchCoursesByLocation(locationSelect.value, courseTypeSelect.value);
        }
    });

    // When course type changes
    courseTypeSelect.addEventListener('change', function() {
        if (this.value === 'degree') {
            fieldsContainer.style.display = 'block';
            semesterRow.style.display = 'flex';
        } else if (this.value === 'certificate') {
            fieldsContainer.style.display = 'block';
            semesterRow.style.display = 'none';
        } else {
            fieldsContainer.style.display = 'none';
            semesterRow.style.display = 'none';
        }
        // Only fetch courses if both location and course type are selected
        if (locationSelect.value && courseTypeSelect.value) {
            fetchCoursesByLocation(locationSelect.value, this.value);
        }
    });

    // When course changes
    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        // Enable intake
        if (courseSelect.value && locationSelect.value) {
            intakeSelect.disabled = false;
            handleIntakeFetch();
        }
        // Fetch semesters and modules for course
        handleCourseChange();
    });

    // When intake changes
    intakeSelect.addEventListener('change', function() {
        console.log('Intake changed to:', this.value);
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        semesterSelect.value = '';
        moduleSelect.value = '';
        // Enable and fetch semesters if both intake and course are selected
        if (intakeSelect.value && courseSelect.value) {
            console.log('Fetching semesters for course:', courseSelect.value, 'intake:', intakeSelect.value);
            fetchSemesters(courseSelect.value, intakeSelect.value);
        } else {
            console.log('Not fetching semesters - missing course or intake');
        }
    });

    // When semester changes
    semesterSelect.addEventListener('change', function() {
        resetAndDisable(moduleSelect, 'Select a Module');
        moduleSelect.value = '';
        // Enable module
        if (semesterSelect.value && intakeSelect.value && courseSelect.value && locationSelect.value) {
            moduleSelect.disabled = false;
            handleModuleFetch();
        }
    });

    // When module changes, fetch existing results if all filters are filled
    moduleSelect.addEventListener('change', function() {
        if (allFiltersFilled()) {
            fetchExistingExamResults();
        }
        updateResultsHeader();
    });

    // Hide fields initially
    fieldsContainer.style.display = 'none';
    semesterRow.style.display = 'none';
    
    // Event listeners
    updateAllBtn.addEventListener('click', handleUpdateAll);

    function handleUpdateAll() {
        const filterData = getFilterData();
        if (!filterData || results.length === 0) {
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
                    marks: parseInt(marksInput.value) || 0,
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
        fetch('<?php echo e(route("update.result")); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message, '#ccffcc');
                // Refresh the results
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
        const data = {
            location: document.getElementById('location').value,
            course_id: courseSelect.value,
            intake_id: document.getElementById('intake').value,
            semester: semesterSelect.value,
            module_id: moduleSelect.value,
        };
        return Object.values(data).some(v => !v) ? null : data;
    }
    
    function renderTable() {
        resultsTableBody.innerHTML = '';
        results.forEach((result, index) => {
            const row = `<tr data-result-id="${result.id}">
                <td>${result.registration_id}</td>
                <td>${result.student_name}</td>
                <td><input type="number" class="form-control" name="marks" min="0" max="100" value="${result.marks || ''}" onchange="updateResultMark(${index}, this.value)"></td>
                <td><input type="text" class="form-control" name="grade" maxlength="5" value="${result.grade || ''}" onchange="updateResultGrade(${index}, this.value)"></td>
                <td><input type="text" class="form-control" name="remarks" maxlength="255" value="${result.remarks || ''}" onchange="updateResultRemarks(${index}, this.value)" placeholder="Enter remarks"></td>
                <td>${result.updated_at}</td>
            </tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function updateResultsHeader() {
        const data = getFilterData();
        if(data) {
            const courseName = courseSelect.options[courseSelect.selectedIndex].text;
            const moduleName = moduleSelect.options[moduleSelect.selectedIndex].text;
            resultsTableHeader.innerHTML = `Exam Results for: ${courseName} - Semester ${data.semester} (${moduleName})`;
            resultsTableHeader.style.display = 'block';
        } else {
            resultsTableHeader.style.display = 'none';
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

    // Helper to check if all filters are filled
    function allFiltersFilled() {
        if (courseTypeSelect.value === 'degree') {
            return locationSelect.value && courseTypeSelect.value && courseSelect.value && intakeSelect.value && semesterSelect.value && moduleSelect.value;
        } else if (courseTypeSelect.value === 'certificate') {
            return locationSelect.value && courseTypeSelect.value && courseSelect.value && intakeSelect.value && moduleSelect.value;
        }
        return false;
    }

    // Fetch and display existing exam results when all filters are filled
    document.querySelectorAll('.filter-param').forEach(el => el.addEventListener('change', function() {
        if (allFiltersFilled()) {
            fetchExistingExamResults();
        } else {
            document.getElementById('resultsTableSection').style.display = 'none';
            document.getElementById('updateAllBtnSection').style.display = 'none';
            statisticsCards.style.display = 'none';
        }
    }));

    function fetchExistingExamResults() {
        const data = {
            location: locationSelect.value,
            course_type: courseTypeSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: courseTypeSelect.value === 'degree' ? semesterSelect.value : null,
            module_id: moduleSelect.value
        };
        showSpinner(true);
        fetch('<?php echo e(route("get.existing.exam.results")); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.results && data.results.length > 0) {
                // Show results status
                const statusAlert = document.getElementById('resultsStatusAlert');
                const statusText = document.getElementById('resultsStatusText');
                
                statusAlert.className = 'alert alert-success mb-3';
                statusText.innerHTML = `Found ${data.total_count} existing exam result(s) for this module. You can view and edit them below.`;
                statusAlert.style.display = 'block';
                
                results = data.results;
                renderTable();
                updateStatistics(data.results);
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('updateAllBtnSection').style.display = '';
                statisticsCards.style.display = '';
            } else {
                resultsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No exam results found for these filters.</td></tr>';
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('updateAllBtnSection').style.display = 'none';
                statisticsCards.style.display = 'none';
                
                // Show status alert
                const statusAlert = document.getElementById('resultsStatusAlert');
                const statusText = document.getElementById('resultsStatusText');
                statusAlert.className = 'alert alert-warning mb-3';
                statusText.innerHTML = 'No exam results found for the selected criteria.';
                statusAlert.style.display = 'block';
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to fetch exam results.', 'bg-danger');
            document.getElementById('resultsTableSection').style.display = 'none';
            document.getElementById('updateAllBtnSection').style.display = 'none';
            statisticsCards.style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    function updateStatistics(results) {
        const totalStudents = results.length;
        const totalMarks = results.reduce((sum, result) => sum + (parseInt(result.marks) || 0), 0);
        const averageMarks = totalStudents > 0 ? Math.round(totalMarks / totalStudents) : 0;
        
        // Calculate pass rate using grades if available, otherwise use marks
        const passedStudents = results.filter(result => {
            // If grade is available, use grade-based logic
            if (result.grade && result.grade.trim() !== '') {
                return ['A', 'B', 'C', 'D'].includes(result.grade);
            }
            // If only marks are available, use marks-based logic
            if (result.marks !== null && result.marks !== undefined && result.marks !== '') {
                const marks = parseInt(result.marks);
                return marks >= 50; // 50% is passing threshold
            }
            return false; // No grade or marks available
        }).length;
        
        const passRate = totalStudents > 0 ? Math.round((passedStudents / totalStudents) * 100) : 0;
        const lastUpdated = results.length > 0 ? results[0].updated_at : '-';

        document.getElementById('totalStudents').textContent = totalStudents;
        document.getElementById('averageMarks').textContent = averageMarks;
        document.getElementById('passRate').textContent = passRate + '%';
        document.getElementById('lastUpdated').textContent = lastUpdated;
    }

    // Function to automatically calculate grade from marks
    function calculateGradeFromMarks(marks) {
        if (marks === null || marks === undefined || marks === '') {
            return '';
        }
        
        const marksNum = parseInt(marks);
        if (isNaN(marksNum)) {
            return '';
        }
        
        if (marksNum >= 80) return 'A';
        if (marksNum >= 70) return 'B';
        if (marksNum >= 60) return 'C';
        if (marksNum >= 50) return 'D';
        return 'F';
    }

    // Function to auto-fill grades when marks are entered
    function autoFillGrades() {
        const gradeInputs = document.querySelectorAll('input[name*="grade"]');
        const marksInputs = document.querySelectorAll('input[name*="marks"]');
        
        marksInputs.forEach((marksInput, index) => {
            if (gradeInputs[index] && marksInput.value && !gradeInputs[index].value) {
                const grade = calculateGradeFromMarks(marksInput.value);
                gradeInputs[index].value = grade;
                
                // Update the results array if it exists
                if (window.results && window.results[index]) {
                    window.results[index].grade = grade;
                }
            }
        });
        
        // Update statistics after auto-filling grades
        if (window.results) {
            updateStatistics(window.results);
        }
    }

    // Add event listener for auto-fill functionality
    document.addEventListener('DOMContentLoaded', function() {
        // Add event listeners to marks inputs for auto-grade calculation
        document.addEventListener('input', function(e) {
            if (e.target.name && e.target.name.includes('marks')) {
                // Auto-fill grade after a short delay
                setTimeout(autoFillGrades, 500);
            }
        });
        
        // Add event listener for auto-calculate grades button
        const autoCalculateGradesBtn = document.getElementById('autoCalculateGradesBtn');
        if (autoCalculateGradesBtn) {
            autoCalculateGradesBtn.addEventListener('click', function() {
                if (results && results.length > 0) {
                    // Auto-calculate grades for all results with marks but no grades
                    let updatedCount = 0;
                    results.forEach((result, index) => {
                        if (result.marks && (!result.grade || result.grade.trim() === '')) {
                            const grade = calculateGradeFromMarks(result.marks);
                            if (grade) {
                                result.grade = grade;
                                updatedCount++;
                                
                                // Update the input field if it exists
                                const gradeInput = document.querySelector(`input[name="grade_${index}"]`);
                                if (gradeInput) {
                                    gradeInput.value = grade;
                                }
                            }
                        }
                    });
                    
                    // Update statistics
                    updateStatistics(results);
                    
                    if (updatedCount > 0) {
                        showToast('Success', `Auto-calculated grades for ${updatedCount} student(s).`, 'bg-success');
                    } else {
                        showToast('Info', 'No students found with marks but no grades.', 'bg-info');
                    }
                } else {
                    showToast('Warning', 'No results available to calculate grades for.', 'bg-warning');
                }
            });
        }
    });

    window.updateResultMark = function(index, value) {
        if (results[index]) {
            results[index].marks = value;
        }
    }
    
    window.updateResultGrade = function(index, value) {
        if (results[index]) {
            results[index].grade = value;
        }
    }
    
    window.updateResultRemarks = function(index, value) {
        if (results[index]) {
            results[index].remarks = value;
        }
    }

    // Fetch intakes
    function handleIntakeFetch() {
        const courseId = courseSelect.value;
        const location = locationSelect.value;
        if (!courseId || !location) {
            resetAndDisable(intakeSelect, 'Select an Intake');
            return;
        }
        showSpinner(true);
        fetch(`/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showToast('Error', data.error, 'bg-danger');
                    resetAndDisable(intakeSelect, 'Select an Intake');
                } else {
                    populateDropdown(intakeSelect, data.intakes, 'intake_id', 'batch', 'Intake');
                    intakeSelect.disabled = false;
                }
            })
            .catch(() => {
                showToast('Error', 'Failed to fetch intakes.', 'bg-danger');
                resetAndDisable(intakeSelect, 'Select an Intake');
            })
            .finally(() => showSpinner(false));
    }

    function fetchSemesters(courseId, intakeId) {
        console.log('fetchSemesters called with courseId:', courseId, 'intakeId:', intakeId);
        showSpinner(true);
        fetch(`/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => {
                console.log('API response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('API response data:', data);
                console.log('Semesters array:', data.semesters);
                if (data.semesters && data.semesters.length > 0) {
                    console.log('First semester object:', data.semesters[0]);
                    console.log('Available keys in first semester:', Object.keys(data.semesters[0]));
                    console.log('Populating semester dropdown with', data.semesters.length, 'semesters');
                    // Use 'id' and 'name' since that's what the TimetableController returns
                    populateDropdown(semesterSelect, data.semesters, 'id', 'name', 'Semester');
                    semesterSelect.disabled = false;
                } else {
                    console.log('No semesters found in response');
                    resetAndDisable(semesterSelect, 'Select a Semester');
                    showToast('Error', 'No semesters found for this course and intake.', 'bg-danger');
                }
            })
            .catch((error) => {
                console.error('Error fetching semesters:', error);
                resetAndDisable(semesterSelect, 'Select a Semester');
                showToast('Error', 'Failed to fetch semesters.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    // Fetch course data (semesters, modules)
    function handleCourseChange() {
        const courseId = courseSelect.value;
        if (!courseId) {
            resetAndDisable(semesterSelect, 'Select a Semester');
            resetAndDisable(moduleSelect, 'Select a Module');
            return;
        }
        // No longer fetch semesters here; only fetch modules if needed
        // Optionally, you can fetch modules here if you want
    }

    // Fetch modules for all filters
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
        fetch('<?php echo e(route('exam.results.get.filtered.modules')); ?>', {
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

    // Helper to populate dropdown
    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        console.log('populateDropdown called with:', { select, items, valueKey, textKey, defaultText });
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            let displayText = item[textKey];
            let value = item[valueKey];
            
            // Handle different property name formats
            if (defaultText === 'Semester') {
                // Try semester_id/semester_name first, then fall back to id/name
                if (displayText === undefined && item.semester_name !== undefined) {
                    displayText = item.semester_name;
                }
                if (value === undefined && item.semester_id !== undefined) {
                    value = item.semester_id;
                }
                
                // Format semester names for display
                if (displayText && !displayText.toLowerCase().includes('semester')) {
                    displayText = `Semester ${displayText}`;
                }
            }
            
            console.log('Adding option:', displayText, 'with value:', value);
            if (displayText && value) {
                select.add(new Option(displayText, value));
            }
        });
        console.log('Dropdown populated with', items.length, 'options');
    }

    // Update fetchCoursesByLocation to accept both params
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
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/exam_results_view_edit.blade.php ENDPATH**/ ?>