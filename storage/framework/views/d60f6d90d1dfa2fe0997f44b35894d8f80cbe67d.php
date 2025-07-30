

<?php $__env->startSection('title', 'NEBULA | Exam Results'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Exam Result Management</h2>
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

                <!-- Add New Student Section -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0">Add New Student</h6>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <label for="new_student_id" class="form-label">Registration Number</label>
                                <input type="text" class="form-control" id="new_student_id" placeholder="Enter Registration Number">
                            </div>
                            <div class="col-md-4">
                                <label for="new_student_name" class="form-label">Student Name</label>
                                <input type="text" class="form-control" id="new_student_name" placeholder="Student Name" readonly>
                            </div>
                            <div class="col-md-4 d-flex align-items-end">
                                <button type="button" class="btn btn-success" id="addStudentBtn">
                                    <i class="ti ti-plus"></i> Add Student
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Column Management Buttons -->
                <div class="mb-3 d-flex gap-2 justify-content-center">
                    <button type="button" class="btn btn-outline-primary" id="addMarksColumnBtn">
                        <i class="ti ti-plus"></i> Add Marks Column
                    </button>
                    <button type="button" class="btn btn-outline-success" id="addGradeColumnBtn">
                        <i class="ti ti-plus"></i> Add Grade Column
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="removeMarksColumnBtn" style="display: none;">
                        <i class="ti ti-minus"></i> Remove Marks Column
                    </button>
                    <button type="button" class="btn btn-outline-danger" id="removeGradeColumnBtn" style="display: none;">
                        <i class="ti ti-minus"></i> Remove Grade Column
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-bordered" id="resultsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Student data rows will be added here dynamically -->
                        </tbody>
                        <tbody id="resultsTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="text-center mt-4" id="saveAllBtnSection" style="display:none;">
                <button type="button" id="saveAllBtn" class="btn btn-primary w-100 py-2">Save All Results</button>
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
    const addStudentBtn = document.getElementById('addStudentBtn');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const saveAllBtn = document.getElementById('saveAllBtn');
    const resultsTableHeader = document.getElementById('resultsTableHeader');
    const locationSelect = document.getElementById('location');
    const intakeSelect = document.getElementById('intake');
    const courseTypeSelect = document.getElementById('course_type');
    const fieldsContainer = document.getElementById('fields-container');
    const semesterRow = document.getElementById('semester-row');
    
    // Column management elements
    const addMarksColumnBtn = document.getElementById('addMarksColumnBtn');
    const addGradeColumnBtn = document.getElementById('addGradeColumnBtn');
    const removeMarksColumnBtn = document.getElementById('removeMarksColumnBtn');
    const removeGradeColumnBtn = document.getElementById('removeGradeColumnBtn');
    
    // Clear any existing data rows on page load to ensure clean state
    resultsTableBody.innerHTML = '';
    
    // Function to reset table to base structure (only two columns)
    function resetTableStructure() {
        // Clear existing data
        resultsTableBody.innerHTML = '';
        
        // Remove any existing dynamic columns
        const existingMarksHeader = document.getElementById('marksColumnHeader');
        const existingGradeHeader = document.getElementById('gradeColumnHeader');
        const existingMarksInput = document.getElementById('marksInputCell');
        const existingGradeInput = document.getElementById('gradeInputCell');
        
        if (existingMarksHeader) existingMarksHeader.remove();
        if (existingGradeHeader) existingGradeHeader.remove();
        if (existingMarksInput) existingMarksInput.remove();
        if (existingGradeInput) existingGradeInput.remove();
        
        // Reset button states
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
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
        // Clear all existing data rows
        resultsTableBody.innerHTML = '';
        
        // Remove any dynamic columns from header
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const headers = tableHeader.querySelectorAll('th');
        if (headers.length > 2) {
            for (let i = 2; i < headers.length; i++) {
                headers[i].remove();
            }
        }
        
        // Reset button states
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
    }
    
    // Ensure clean two-column structure on page load
    ensureTwoColumns();

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

    // When module changes, fetch students if all filters are filled
    moduleSelect.addEventListener('change', function() {
        // Ensure clean two-column structure
        ensureTwoColumns();
        saveAllBtnSection.style.display = 'none';
        
        if (allFiltersFilled()) {
            fetchStudentsForResultEntry();
        }
        updateResultsHeader();
    });

    // Hide fields initially
    fieldsContainer.style.display = 'none';
    semesterRow.style.display = 'none';
    
    // Column management event listeners
    addMarksColumnBtn.addEventListener('click', function() {
        // Add Marks header
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const marksHeader = document.createElement('th');
        marksHeader.id = 'marksColumnHeader';
        marksHeader.textContent = 'Marks';
        tableHeader.appendChild(marksHeader);
        
        addMarksColumnBtn.style.display = 'none';
        removeMarksColumnBtn.style.display = 'inline-block';
        
        // Update existing rows to include editable marks column
        updateExistingRows();
    });
    
    removeMarksColumnBtn.addEventListener('click', function() {
        // Remove Marks header
        const marksHeader = document.getElementById('marksColumnHeader');
        if (marksHeader) {
            marksHeader.remove();
        }
        
        // Remove Marks input cell
        const marksInputCell = document.getElementById('marksInputCell');
        if (marksInputCell) {
            marksInputCell.remove();
        }
        
        addMarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        
        // Update existing rows to remove marks column
        updateExistingRows();
    });
    
    addGradeColumnBtn.addEventListener('click', function() {
        // Add Grade header
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const gradeHeader = document.createElement('th');
        gradeHeader.id = 'gradeColumnHeader';
        gradeHeader.textContent = 'Grade';
        tableHeader.appendChild(gradeHeader);
        
        addGradeColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'inline-block';
        
        // Update existing rows to include editable grade column
        updateExistingRows();
    });
    
    removeGradeColumnBtn.addEventListener('click', function() {
        // Remove Grade header
        const gradeHeader = document.getElementById('gradeColumnHeader');
        if (gradeHeader) {
            gradeHeader.remove();
        }
        
        // Remove Grade input cell
        const gradeInputCell = document.getElementById('gradeInputCell');
        if (gradeInputCell) {
            gradeInputCell.remove();
        }
        
        addGradeColumnBtn.style.display = 'inline-block';
        removeGradeColumnBtn.style.display = 'none';
        
        // Update existing rows to remove grade column
        updateExistingRows();
    });
    
    // Function to update existing rows when columns are added/removed
    function updateExistingRows() {
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
        });
    }

    // Debug: If we have pre-selected values, test the semester fetching
    if (courseSelect.value && intakeSelect.value) {
        setTimeout(() => {
            fetchSemesters(courseSelect.value, intakeSelect.value);
        }, 1000);
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
        const url = '<?php echo e(route('exam.results.get.filtered.modules')); ?>';
        console.log('DEBUG: Calling modules API with URL:', url);
        console.log('DEBUG: Request data:', data);
        fetch(url, {
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
        .catch((error) => {
            console.error('DEBUG: Modules API error:', error);
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

    // Event listeners
    addStudentBtn.addEventListener('click', handleAddStudent);
    saveAllBtn.addEventListener('click', handleSaveAll);

    function handleAddStudent() {
        const studentId = document.getElementById('new_student_id').value.trim();
        
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
        fetch('<?php echo e(route("get.student.name")); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify({ student_id: studentId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const studentData = { student_id: studentId, name: data.name };
                results.push(studentData);
                renderTable();
                clearInputFields();
                // Update the student name field
                document.getElementById('new_student_name').value = data.name;
            } else {
                showToast('Error', data.message || 'Could not find student.', 'bg-danger');
            }
        })
        .catch(() => showToast('Error', 'An error occurred while fetching student details.', 'bg-danger'))
        .finally(() => showSpinner(false));
    }

    function handleSaveAll() {
        const filterData = getFilterData();
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
            return filtered;
        }).filter(result => result.marks !== undefined || result.grade !== undefined);
        
        if (filteredResults.length === 0) {
            showToast('Warning', 'Please enter at least marks or grade for at least one student.', 'bg-warning');
            return;
        }
        
        const payload = { ...filterData, results: filteredResults };
        
        showSpinner(true);
        fetch('<?php echo e(route("store.result")); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Success', data.message, '#ccffcc');
                setTimeout(function() {
                    location.reload();
                }, 1500);
                results = [];
                renderTable();
                document.getElementById('student_id').form.reset();
                resetAndDisable(courseSelect, 'Select a Course');
                resetAndDisable(intakeSelect, 'Select an Intake');
                resetAndDisable(semesterSelect, 'Select a Semester');
                resetAndDisable(moduleSelect, 'Select a Module');
                updateResultsHeader();
            } else {
                let errorMsg = data.message || 'An error occurred.';
                if(data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                }
                showToast('Error', errorMsg, 'bg-danger');
            }
        })
        .catch(() => showToast('Error', 'An error occurred while saving results.', 'bg-danger'))
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
            const marksVisible = document.getElementById('marksColumnHeader') !== null;
            const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
            
            let row = `<tr>
                <td>${result.student_id}</td>
                <td>${result.name}</td>`;
            
            if (marksVisible) {
                row += `<td class="marks-cell"><input type="number" class="form-control" min="0" max="100" placeholder="Marks" value="${result.marks || ''}" onchange="updateResultMark(${index}, this.value)"></td>`;
            }
            
            if (gradeVisible) {
                row += `<td class="grade-cell"><input type="text" class="form-control" maxlength="5" placeholder="Grade" value="${result.grade || ''}" onchange="updateResultGrade(${index}, this.value)"></td>`;
            }
            
            row += `</tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    function clearInputFields() {
        document.getElementById('new_student_id').value = '';
        document.getElementById('new_student_name').value = '';
        document.getElementById('new_student_id').focus();
    }

    function updateResultsHeader() {
        const data = getFilterData();
        if(data) {
            const courseName = courseSelect.options[courseSelect.selectedIndex].text;
            const moduleName = moduleSelect.options[moduleSelect.selectedIndex].text;
            resultsTableHeader.innerHTML = `Results for: ${courseName} - Semester ${data.semester} (${moduleName})`;
            resultsTableHeader.style.display = 'block';
        } else {
            resultsTableHeader.style.display = 'none';
        }
        results = [];
        renderTable();
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

    // Fetch and display students when all filters are filled
    document.querySelectorAll('.filter-param').forEach(el => el.addEventListener('change', function() {
        if (allFiltersFilled()) {
            fetchStudentsForResultEntry();
        } else {
            document.getElementById('resultsTableSection').style.display = 'none';
            document.getElementById('saveAllBtnSection').style.display = 'none';
        }
    }));

    function fetchStudentsForResultEntry() {
        const data = {
            location: locationSelect.value,
            course_type: courseTypeSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: courseTypeSelect.value === 'degree' ? semesterSelect.value : null,
            module_id: moduleSelect.value
        };
        showSpinner(true);
        fetch('/get-students-for-exam-result', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students && data.students.length > 0) {
                // Show results status
                const statusAlert = document.getElementById('resultsStatusAlert');
                const statusText = document.getElementById('resultsStatusText');
                const addStudentSection = document.querySelector('.card.mb-3'); // The Add New Student card
                
                if (data.results_exist) {
                    statusAlert.className = 'alert alert-warning mb-3';
                    statusText.innerHTML = 'Exam results for this module have already been entered. You can view and edit existing results.';
                    statusAlert.style.display = 'block';
                    
                    // Hide the Add New Student section when results exist
                    if (addStudentSection) {
                        addStudentSection.style.display = 'none';
                    }
                    
                    // Hide the column management buttons when results exist
                    document.getElementById('addMarksColumnBtn').style.display = 'none';
                    document.getElementById('addGradeColumnBtn').style.display = 'none';
                    document.getElementById('removeMarksColumnBtn').style.display = 'none';
                    document.getElementById('removeGradeColumnBtn').style.display = 'none';
                } else {
                    statusAlert.className = 'alert alert-info mb-3';
                    statusText.innerHTML = 'No exam results have been entered for this module yet. You can add new results.';
                    statusAlert.style.display = 'block';
                    
                    // Show the Add New Student section when no results exist
                    if (addStudentSection) {
                        addStudentSection.style.display = 'block';
                    }
                    
                                            // Show column management buttons when no results exist (let users choose which columns they want)
                    document.getElementById('addMarksColumnBtn').style.display = 'inline-block';
                    document.getElementById('addGradeColumnBtn').style.display = 'inline-block';
                    document.getElementById('removeMarksColumnBtn').style.display = 'none';
                    document.getElementById('removeGradeColumnBtn').style.display = 'none';
                }
                
                renderEditableResultsTable(data.students);
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('saveAllBtnSection').style.display = '';
            } else {
                resultsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No students found for these filters.</td></tr>';
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('saveAllBtnSection').style.display = 'none';
                
                // Hide status alert and show Add New Student section
                document.getElementById('resultsStatusAlert').style.display = 'none';
                const addStudentSection = document.querySelector('.card.mb-3');
                if (addStudentSection) {
                    addStudentSection.style.display = 'block';
                }
                
                // Show column management buttons when no students found (let users choose which columns they want)
                document.getElementById('addMarksColumnBtn').style.display = 'inline-block';
                document.getElementById('addGradeColumnBtn').style.display = 'inline-block';
                document.getElementById('removeMarksColumnBtn').style.display = 'none';
                document.getElementById('removeGradeColumnBtn').style.display = 'none';
            }
        })
        .catch(() => {
            showToast('Error', 'Failed to fetch students.', 'bg-danger');
            document.getElementById('resultsTableSection').style.display = 'none';
            document.getElementById('saveAllBtnSection').style.display = 'none';
        })
        .finally(() => showSpinner(false));
    }

    // Render table with only two columns
    function renderEditableResultsTable(students) {
        results = students.map(s => ({ 
            registration_id: s.registration_id, 
            student_id: s.student_id, 
            name: s.name, 
            marks: s.marks || '', 
            grade: s.grade || '' 
        }));
        resultsTableBody.innerHTML = '';
        results.forEach((result, index) => {
            const row = `<tr>
                <td>${result.registration_id}</td>
                <td>${result.name}</td>
            </tr>`;
            resultsTableBody.insertAdjacentHTML('beforeend', row);
        });
        
        // If Marks or Grade columns are visible, update them with existing data
        const marksVisible = document.getElementById('marksColumnHeader') !== null;
        const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
        
        if (marksVisible || gradeVisible) {
            updateExistingRows();
        }
    }

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
</style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\JOB\Projects\Nebula\resources\views/exam_results.blade.php ENDPATH**/ ?>