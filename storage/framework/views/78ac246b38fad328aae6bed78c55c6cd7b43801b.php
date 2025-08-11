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
                    <div class="mb-3 row mx-3" id="specialization-row" style="display:none;">
                        <label for="specialization" class="col-sm-2 col-form-label">Specialization</label>
                        <div class="col-sm-10">
                            <select class="form-select filter-param" id="specialization" name="specialization">
                                <option selected disabled value="">Select Specialization</option>
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
                <div id="resultsStatusAlert" class="alert alert-info mb-3" style="display: none;">
                    <i class="ti ti-info-circle"></i>
                    <strong>Exam Results Status:</strong> 
                    <span id="resultsStatusText"></span>
                </div>
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
                        <tbody id="resultsTableBody"></tbody>
                    </table>
                </div>
            </div>
            <div class="text-center mt-4" id="saveAllBtnSection" style="display:none;">
                <button type="button" id="saveAllBtn" class="btn btn-primary w-100 py-2">Save All Results</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let results = [];
    let courseSpecializations = [];
    const courseSelect = document.getElementById('course');
    const moduleSelect = document.getElementById('module');
    const semesterSelect = document.getElementById('semester');
    const specializationSelect = document.getElementById('specialization');
    const specializationRow = document.getElementById('specialization-row');
    const addStudentBtn = document.getElementById('addStudentBtn');
    const resultsTableBody = document.getElementById('resultsTableBody');
    const saveAllBtn = document.getElementById('saveAllBtn');
    const resultsTableHeader = document.getElementById('resultsTableHeader');
    const locationSelect = document.getElementById('location');
    const intakeSelect = document.getElementById('intake');
    const courseTypeSelect = document.getElementById('course_type');
    const fieldsContainer = document.getElementById('fields-container');
    const semesterRow = document.getElementById('semester-row');
    const addMarksColumnBtn = document.getElementById('addMarksColumnBtn');
    const addGradeColumnBtn = document.getElementById('addGradeColumnBtn');
    const removeMarksColumnBtn = document.getElementById('removeMarksColumnBtn');
    const removeGradeColumnBtn = document.getElementById('removeGradeColumnBtn');

    resultsTableBody.innerHTML = '';

    function resetTableStructure() {
        resultsTableBody.innerHTML = '';
        const existingMarksHeader = document.getElementById('marksColumnHeader');
        const existingGradeHeader = document.getElementById('gradeColumnHeader');
        if (existingMarksHeader) existingMarksHeader.remove();
        if (existingGradeHeader) existingGradeHeader.remove();
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
    }
    resetTableStructure();

    function ensureTwoColumns() {
        resultsTableBody.innerHTML = '';
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const headers = tableHeader.querySelectorAll('th');
        if (headers.length > 2) {
            for (let i = 2; i < headers.length; i++) {
                headers[i].remove();
            }
        }
        addMarksColumnBtn.style.display = 'inline-block';
        addGradeColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'none';
    }
    ensureTwoColumns();

    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    resetAndDisable(courseSelect, 'Select a Course');
    resetAndDisable(intakeSelect, 'Select an Intake');
    resetAndDisable(semesterSelect, 'Select a Semester');
    resetAndDisable(moduleSelect, 'Select a Module');
    resetAndDisable(specializationSelect, 'Select Specialization');
    specializationRow.style.display = 'none';

    if (locationSelect.value) {
        fetchCoursesByLocation(locationSelect.value, courseTypeSelect.value);
    }

    locationSelect.addEventListener('change', function() {
        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        resetAndDisable(specializationSelect, 'Select Specialization');
        specializationRow.style.display = 'none';
        courseSelect.value = '';
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        specializationSelect.value = '';
        if (locationSelect.value && courseTypeSelect.value) {
            fetchCoursesByLocation(locationSelect.value, courseTypeSelect.value);
        }
    });

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
        resetAndDisable(courseSelect, 'Select a Course');
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        resetAndDisable(specializationSelect, 'Select Specialization');
        specializationRow.style.display = 'none';
        if (locationSelect.value && courseTypeSelect.value) {
            fetchCoursesByLocation(locationSelect.value, this.value);
        }
    });

    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select an Intake');
        resetAndDisable(semesterSelect, 'Select a Semester');
        resetAndDisable(moduleSelect, 'Select a Module');
        resetAndDisable(specializationSelect, 'Select Specialization');
        specializationRow.style.display = 'none';
        intakeSelect.value = '';
        semesterSelect.value = '';
        moduleSelect.value = '';
        specializationSelect.value = '';
        if (courseSelect.value && locationSelect.value) {
            intakeSelect.disabled = false;
            handleIntakeFetch();
            fetchSpecializations(courseSelect.value);
        }
        handleCourseChange();
    });

    function fetchSpecializations(courseId) {
        fetch(`/api/courses/${courseId}`)
            .then(res => res.json())
            .then(data => {
                let specs = [];
                if (data.success && data.course && data.course.specializations) {
                    if (typeof data.course.specializations === 'string') {
                        try { specs = JSON.parse(data.course.specializations); } catch (e) { specs = []; }
                    } else if (Array.isArray(data.course.specializations)) {
                        specs = data.course.specializations;
                    }
                    specs = specs.filter(s => s && s.trim() !== '');
                }
                if (specs.length > 0) {
                    courseSpecializations = specs;
                    let options = '<option selected disabled value="">Select Specialization</option>';
                    specs.forEach(s => { options += `<option value="${s}">${s}</option>`; });
                    specializationSelect.innerHTML = options;
                    specializationSelect.disabled = false;
                    specializationRow.style.display = '';
                } else {
                    courseSpecializations = [];
                    specializationSelect.innerHTML = '<option selected disabled value="">No Specialization</option>';
                    specializationSelect.disabled = true;
                    specializationRow.style.display = 'none';
                }
            })
            .catch(() => {
                courseSpecializations = [];
                specializationSelect.innerHTML = '<option selected disabled value="">No Specialization</option>';
                specializationSelect.disabled = true;
                specializationRow.style.display = 'none';
            });
    }

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

    specializationSelect.addEventListener('change', function() {
        resetAndDisable(moduleSelect, 'Select a Module');
        moduleSelect.value = '';
        if (specializationSelect.value && semesterSelect.value && intakeSelect.value && courseSelect.value && locationSelect.value) {
            moduleSelect.disabled = false;
            handleModuleFetch();
        }
    });

    moduleSelect.addEventListener('change', function() {
        ensureTwoColumns();
        saveAllBtnSection.style.display = 'none';
        if (allFiltersFilled()) {
            fetchStudentsForResultEntry();
        }
        updateResultsHeader();
    });

    fieldsContainer.style.display = 'none';
    semesterRow.style.display = 'none';

    addMarksColumnBtn.addEventListener('click', function() {
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const marksHeader = document.createElement('th');
        marksHeader.id = 'marksColumnHeader';
        marksHeader.textContent = 'Marks';
        tableHeader.appendChild(marksHeader);
        addMarksColumnBtn.style.display = 'none';
        removeMarksColumnBtn.style.display = 'inline-block';
        updateExistingRows();
    });

    removeMarksColumnBtn.addEventListener('click', function() {
        const marksHeader = document.getElementById('marksColumnHeader');
        if (marksHeader) marksHeader.remove();
        addMarksColumnBtn.style.display = 'inline-block';
        removeMarksColumnBtn.style.display = 'none';
        updateExistingRows();
    });

    addGradeColumnBtn.addEventListener('click', function() {
        const tableHeader = document.querySelector('#resultsTable thead tr');
        const gradeHeader = document.createElement('th');
        gradeHeader.id = 'gradeColumnHeader';
        gradeHeader.textContent = 'Grade';
        tableHeader.appendChild(gradeHeader);
        addGradeColumnBtn.style.display = 'none';
        removeGradeColumnBtn.style.display = 'inline-block';
        updateExistingRows();
    });

    removeGradeColumnBtn.addEventListener('click', function() {
        const gradeHeader = document.getElementById('gradeColumnHeader');
        if (gradeHeader) gradeHeader.remove();
        addGradeColumnBtn.style.display = 'inline-block';
        removeGradeColumnBtn.style.display = 'none';
        updateExistingRows();
    });

    function updateExistingRows() {
        const rows = resultsTableBody.querySelectorAll('tr');
        rows.forEach((row, rowIndex) => {
            const cells = row.querySelectorAll('td');
            if (cells.length > 2) {
                for (let i = 2; i < cells.length; i++) {
                    cells[i].remove();
                }
            }
            const marksVisible = document.getElementById('marksColumnHeader') !== null;
            if (marksVisible) {
                const marksCell = document.createElement('td');
                marksCell.className = 'marks-cell';
                marksCell.innerHTML = `<input type="number" class="form-control" min="0" max="100" placeholder="Marks" onchange="updateResultMark(${rowIndex}, this.value)">`;
                row.appendChild(marksCell);
            }
            const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
            if (gradeVisible) {
                const gradeCell = document.createElement('td');
                gradeCell.className = 'grade-cell';
                gradeCell.innerHTML = `<input type="text" class="form-control" maxlength="5" placeholder="Grade" onchange="updateResultGrade(${rowIndex}, this.value)">`;
                row.appendChild(gradeCell);
            }
        });
    }

    function handleIntakeFetch() {
        const courseId = courseSelect.value;
        const location = locationSelect.value;
        if (!courseId || !location) {
            resetAndDisable(intakeSelect, 'Select an Intake');
            return;
        }
        fetch(`/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    resetAndDisable(intakeSelect, 'Select an Intake');
                } else {
                    populateDropdown(intakeSelect, data.intakes, 'intake_id', 'batch', 'Intake');
                    intakeSelect.disabled = false;
                }
            })
            .catch(() => {
                resetAndDisable(intakeSelect, 'Select an Intake');
            });
    }

    function fetchSemesters(courseId, intakeId) {
        fetch(`/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    populateDropdown(semesterSelect, data.semesters, 'id', 'name', 'Semester');
                    semesterSelect.disabled = false;
                } else {
                    resetAndDisable(semesterSelect, 'Select a Semester');
                }
            })
            .catch(() => {
                resetAndDisable(semesterSelect, 'Select a Semester');
            });
    }

    function handleCourseChange() {
        const courseId = courseSelect.value;
        if (!courseId) {
            resetAndDisable(semesterSelect, 'Select a Semester');
            resetAndDisable(moduleSelect, 'Select a Module');
            resetAndDisable(specializationSelect, 'Select Specialization');
            specializationRow.style.display = 'none';
            return;
        }
    }

    function handleModuleFetch() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value,
            specialization: specializationSelect.value
        };
        if (Object.values(data).some(v => !v && v !== '')) {
            resetAndDisable(moduleSelect, 'Select a Module');
            return;
        }
        fetch('/exam-results/get-filtered-modules', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                resetAndDisable(moduleSelect, 'Select a Module');
            } else {
                populateDropdown(moduleSelect, data.modules, 'module_id', 'module_name', 'Module');
                moduleSelect.disabled = false;
            }
        })
        .catch(() => {
            resetAndDisable(moduleSelect, 'Select a Module');
        });
    }

    function populateDropdown(select, items, valueKey, textKey, defaultText) {
        select.innerHTML = `<option selected disabled value="">Select ${defaultText}</option>`;
        (items || []).forEach(item => {
            let displayText = item[textKey];
            let value = item[valueKey];
            if (displayText && value) {
                select.add(new Option(displayText, value));
            }
        });
    }

    addStudentBtn.addEventListener('click', handleAddStudent);
    saveAllBtn.addEventListener('click', handleSaveAll);

    function handleAddStudent() {
        const studentId = document.getElementById('new_student_id').value.trim();
        if (!studentId) return;
        if (results.some(r => r.student_id === studentId)) return;
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
                document.getElementById('new_student_name').value = data.name;
            }
        });
    }

    function handleSaveAll() {
        const filterData = getFilterData();
        if (!filterData || results.length === 0) return;
        const filteredResults = results.map(result => {
            const filtered = { student_id: result.student_id };
            if (result.marks !== '' && result.marks !== null) filtered.marks = result.marks;
            if (result.grade !== '' && result.grade !== null) filtered.grade = result.grade;
            return filtered;
        }).filter(result => result.marks !== undefined || result.grade !== undefined);
        if (filteredResults.length === 0) return;
        const payload = { ...filterData, results: filteredResults };
        fetch('<?php echo e(route("store.result")); ?>', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                setTimeout(function() { location.reload(); }, 1500);
                results = [];
                renderTable();
                resetAndDisable(courseSelect, 'Select a Course');
                resetAndDisable(intakeSelect, 'Select an Intake');
                resetAndDisable(semesterSelect, 'Select a Semester');
                resetAndDisable(moduleSelect, 'Select a Module');
                resetAndDisable(specializationSelect, 'Select Specialization');
                specializationRow.style.display = 'none';
                updateResultsHeader();
            }
        });
    }

    function getFilterData() {
        const data = {
            location: locationSelect.value,
            course_id: courseSelect.value,
            intake_id: intakeSelect.value,
            semester: semesterSelect.value,
            specialization: specializationSelect.value,
            module_id: moduleSelect.value,
        };
        return Object.values(data).some(v => !v && v !== '') ? null : data;
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

    window.updateResultMark = function(index, value) {
        if (results[index]) results[index].marks = value;
    }
    window.updateResultGrade = function(index, value) {
        if (results[index]) results[index].grade = value;
    }

    function allFiltersFilled() {
        if (courseSpecializations.length > 0) {
            return locationSelect.value && courseTypeSelect.value && courseSelect.value && intakeSelect.value && semesterSelect.value && specializationSelect.value && moduleSelect.value;
        } else if (courseTypeSelect.value === 'degree') {
            return locationSelect.value && courseTypeSelect.value && courseSelect.value && intakeSelect.value && semesterSelect.value && moduleSelect.value;
        } else if (courseTypeSelect.value === 'certificate') {
            return locationSelect.value && courseTypeSelect.value && courseSelect.value && intakeSelect.value && moduleSelect.value;
        }
        return false;
    }

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
            specialization: specializationSelect.value,
            module_id: moduleSelect.value
        };
        fetch('/exam-results/get-students-for-exam-result', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success && data.students && data.students.length > 0) {
                renderEditableResultsTable(data.students);
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('saveAllBtnSection').style.display = '';
            } else {
                resultsTableBody.innerHTML = '<tr><td colspan="2" class="text-center">No students found for these filters.</td></tr>';
                document.getElementById('resultsTableSection').style.display = '';
                document.getElementById('saveAllBtnSection').style.display = 'none';
            }
        });
    }

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
        const marksVisible = document.getElementById('marksColumnHeader') !== null;
        const gradeVisible = document.getElementById('gradeColumnHeader') !== null;
        if (marksVisible || gradeVisible) {
            updateExistingRows();
        }
    }

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
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/exam_results.blade.php ENDPATH**/ ?>