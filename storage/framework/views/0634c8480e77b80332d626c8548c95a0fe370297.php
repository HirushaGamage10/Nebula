

<?php $__env->startSection('title', 'NEBULA | Semester Creation'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Create Semester</h2>
            <hr>
            <form action="<?php echo e(route('semesters.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select name="location" id="location" class="form-select" required>
                            <option selected disabled value="">Select a Location</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course_id" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select name="course_id" id="course_id" class="form-select" required disabled>
                            <option selected disabled value="">Select Course</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intake_id" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select name="intake_id" id="intake_id" class="form-select" required disabled>
                            <option selected disabled value="">Select Intake</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="semester" class="col-sm-2 col-form-label">Semester <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select name="semester" id="semester" class="form-select" required disabled>
                            <option selected disabled value="">Select Semester</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="start_date" class="col-sm-2 col-form-label">Start Date <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" name="start_date" id="start_date" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="end_date" class="col-sm-2 col-form-label">End Date <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" name="end_date" id="end_date" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="registration_date" class="col-sm-2 col-form-label">Registration Date <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="registration_date" name="registration_date" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="status" class="col-sm-2 col-form-label">Status <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="status" name="status" required>
                            <option value="">Select Status</option>
                            <option value="open">Open</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3" id="specializationRow" style="display:none;">
                    <label for="specialization_select" class="col-sm-2 col-form-label">Specialization <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select id="specialization_select" class="form-select" name="specialization">
                            <option value="" selected disabled>Select Specialization</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label class="col-sm-2 col-form-label">Modules <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select id="module_type" class="form-select" style="max-width:150px;">
                                <option value="Core">Core</option>
                                <option value="Elective">Elective</option>
                                <option value="Special Unit Compulsory (S/U)">Special Unit Compulsory (S/U)</option>
                            </select>
                            <select id="module_select" class="form-select">
                                <option selected disabled value="">Select a module...</option>
                            </select>
                            <button type="button" id="add_module_btn" class="btn btn-primary">Add</button>
                        </div>
                        <div class="table-responsive mt-2">
                            <table class="table table-bordered" id="modules_table">
                                <thead style="background:#6c8cff;color:white;">
                                    <tr id="modulesTableHeaderRow">
                                        <th>Semester</th>
                                        <!-- Specialization column will be inserted here if needed -->
                                        <th>Module Name</th>
                                        <th>Type</th>
                                        <th>Credits</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JS will populate rows here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-grid mx-3">
                    <button type="submit" class="btn btn-success">Create Semester</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                <!-- Message will go here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
let courseSpecializations = [];
document.addEventListener('DOMContentLoaded', function() {
    const locationSelect = document.getElementById('location');
    const courseSelect = document.getElementById('course_id');
    const intakeSelect = document.getElementById('intake_id');
    const semesterSelect = document.getElementById('semester');
    const moduleTypeSelect = document.getElementById('module_type');
    const moduleSelect = document.getElementById('module_select');
    const addModuleBtn = document.getElementById('add_module_btn');
    const modulesTableBody = document.querySelector('#modules_table tbody');
    let addedModules = [];
    let allModules = [];

    // Helper to reset and disable a select
    function resetAndDisable(select, placeholder) {
        $(select).html(`<option value="" selected disabled>${placeholder}</option>`).prop('disabled', true);
        $(select).removeClass('enabled-highlight');
    }
    // Helper to enable a select
    function enableSelect(select) {
        $(select).prop('disabled', false);
        $(select).addClass('enabled-highlight');
    }

    // 1. Location -> Course
    locationSelect.addEventListener('change', function() {
        resetAndDisable(courseSelect, 'Select Course');
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        resetAndDisable(moduleSelect, 'Select a module...');
        if (locationSelect.value) {
            courseSelect.innerHTML = '<option value="" selected disabled>Loading courses...</option>';
            courseSelect.disabled = true;
            fetch(`/courses/by-location?location=${encodeURIComponent(locationSelect.value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.courses && data.courses.length > 0) {
                        let options = '<option value="" selected disabled>Select Course</option>';
                        data.courses.forEach(course => {
                            options += `<option value="${course.course_id}">${course.course_name}</option>`;
                        });
                        courseSelect.innerHTML = options;
                        enableSelect(courseSelect);
                    } else {
                        resetAndDisable(courseSelect, 'No courses available');
                    }
                })
                .catch(error => {
                    resetAndDisable(courseSelect, 'Failed to load courses');
                });
        }
    });

    // Specialization logic for semester creation
    function updateModulesTableHeader() {
        const headerRow = document.getElementById('modulesTableHeaderRow');
        // Remove any existing specialization column
        const ths = headerRow.querySelectorAll('th');
        ths.forEach(th => {
            if (th.textContent.trim() === 'Specialization') th.remove();
        });
        // Insert specialization column if needed (after Semester)
        if (courseSpecializations.length > 0) {
            const th = document.createElement('th');
            th.textContent = 'Specialization';
            headerRow.insertBefore(th, headerRow.children[1]);
        }
    }
    // Call this after fetching specializations
    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        resetAndDisable(moduleSelect, 'Select a module...');
        // Fetch specializations for the selected course
        if (courseSelect.value) {
            fetch(`/api/courses/${courseSelect.value}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Course data received:', data);
                    if (data.success && data.course) {
                        let specializations = [];
                        
                        // Handle different formats of specializations
                        if (data.course.specializations) {
                            if (typeof data.course.specializations === 'string') {
                                try {
                                    specializations = JSON.parse(data.course.specializations);
                                } catch (e) {
                                    console.error('Error parsing specializations JSON:', e);
                                    specializations = [];
                                }
                            } else if (Array.isArray(data.course.specializations)) {
                                specializations = data.course.specializations;
                            }
                        }
                        
                        // Filter out empty/null values
                        specializations = specializations.filter(spec => spec && spec.trim() !== '');
                        
                        if (specializations.length > 0) {
                            courseSpecializations = specializations;
                            let options = '<option value="" selected disabled>Select Specialization</option>';
                            courseSpecializations.forEach(spec => {
                                options += `<option value="${spec}">${spec}</option>`;
                            });
                            document.getElementById('specialization_select').innerHTML = options;
                            document.getElementById('specializationRow').style.display = '';
                            updateModulesTableHeader();
                            console.log('Specializations loaded:', courseSpecializations);
                        } else {
                            courseSpecializations = [];
                            document.getElementById('specializationRow').style.display = 'none';
                            document.getElementById('specialization_select').innerHTML = '<option value="" selected disabled>No Specialization</option>';
                            updateModulesTableHeader();
                            console.log('No specializations found for this course');
                        }
                    } else {
                        courseSpecializations = [];
                        document.getElementById('specializationRow').style.display = 'none';
                        document.getElementById('specialization_select').innerHTML = '<option value="" selected disabled>No Specialization</option>';
                        updateModulesTableHeader();
                        console.log('Course data not found or invalid');
                    }
                    // --- Always fetch intakes after handling specializations ---
                    fetch(`/get-intakes/${courseSelect.value}/${locationSelect.value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.intakes && data.intakes.length > 0) {
                                let options = '<option value="" selected disabled>Select Intake</option>';
                                data.intakes.forEach(intake => {
                                    options += `<option value="${intake.intake_id}">${intake.batch}</option>`;
                                });
                                intakeSelect.innerHTML = options;
                                enableSelect(intakeSelect);
                            } else {
                                resetAndDisable(intakeSelect, 'No intakes available');
                            }
                        })
                        .catch(error => {
                            resetAndDisable(intakeSelect, 'Failed to load intakes');
                        });
                })
                .catch(() => {
                    courseSpecializations = [];
                    document.getElementById('specializationRow').style.display = 'none';
                    updateModulesTableHeader();
                    // --- Still fetch intakes even if specializations fetch fails ---
                    fetch(`/get-intakes/${courseSelect.value}/${locationSelect.value}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.intakes && data.intakes.length > 0) {
                                let options = '<option value="" selected disabled>Select Intake</option>';
                                data.intakes.forEach(intake => {
                                    options += `<option value="${intake.intake_id}">${intake.batch}</option>`;
                                });
                                intakeSelect.innerHTML = options;
                                enableSelect(intakeSelect);
                            } else {
                                resetAndDisable(intakeSelect, 'No intakes available');
                            }
                        })
                        .catch(error => {
                            resetAndDisable(intakeSelect, 'Failed to load intakes');
                        });
                });
        } else {
            courseSpecializations = [];
            document.getElementById('specializationRow').style.display = 'none';
            updateModulesTableHeader();
            resetAndDisable(intakeSelect, 'Select Intake');
        }
    });

    intakeSelect.addEventListener('change', function() {
        resetAndDisable(semesterSelect, 'Select Semester');
        resetAndDisable(moduleSelect, 'Select a module...');
        if (courseSelect.value) {
            semesterSelect.innerHTML = '<option value="" selected disabled>Loading semesters...</option>';
            semesterSelect.disabled = true;
            fetch(`/semester-registration/get-all-semesters-for-course?course_id=${encodeURIComponent(courseSelect.value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.semesters && data.semesters.length > 0) {
                        let options = '<option value="" selected disabled>Select Semester</option>';
                        data.semesters.forEach(sem => {
                            options += `<option value="${sem.semester_id}">${sem.semester_name}</option>`;
                        });
                        semesterSelect.innerHTML = options;
                        enableSelect(semesterSelect);
                    } else {
                        resetAndDisable(semesterSelect, 'No semesters available');
                    }
                })
                .catch(error => {
                    resetAndDisable(semesterSelect, 'Failed to load semesters');
                });
        }
    });

    // 4. Semester -> Modules
    semesterSelect.addEventListener('change', function() {
        resetAndDisable(moduleSelect, 'Select a module...');
        allModules = [];
        if (semesterSelect.value && intakeSelect.value && courseSelect.value && locationSelect.value) {
            $(moduleSelect).html('<option value="" selected disabled>Loading modules...</option>').prop('disabled', true);
            const data = {
                location: locationSelect.value,
                course_id: courseSelect.value,
                intake_id: intakeSelect.value,
                semester: semesterSelect.value
            };
            fetch('/semester/get-filtered-modules', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(data => {
                if (data.modules && data.modules.length > 0) {
                    allModules = data.modules;
                    filterAndPopulateModules();
                    $(moduleSelect).prop('disabled', false);
                } else {
                    moduleSelect.innerHTML = '<option value="" selected disabled>No modules available</option>';
                    $(moduleSelect).prop('disabled', true);
                }
            })
            .catch(() => {
                moduleSelect.innerHTML = '<option value="" selected disabled>Failed to load modules</option>';
                $(moduleSelect).prop('disabled', true);
            });
        } else {
            resetAndDisable(moduleSelect, 'Select a module...');
        }
    });

    // Filter modules by type (Core/Elective) and populate dropdown
    function filterAndPopulateModules() {
        // Map UI selection to DB value
        const typeMap = {
            'Core': 'core',
            'Elective': 'elective',
            'Special Unit Compulsory (S/U)': 'special_unit_compulsory'
        };
        const selectedType = typeMap[moduleTypeSelect.value];
        let options = '<option value="" selected disabled>Select a module...</option>';
        const filtered = allModules.filter(m => m.module_type === selectedType);
        if (filtered.length > 0) {
            filtered.forEach(module => {
                options += `<option value="${module.module_id}" data-type="${module.module_type ?? ''}" data-credits="${module.credits ?? ''}">${module.module_name}</option>`;
            });
            moduleSelect.innerHTML = options;
            $(moduleSelect).prop('disabled', false);
        } else {
            moduleSelect.innerHTML = '<option value="" selected disabled>No modules available for this type</option>';
            $(moduleSelect).prop('disabled', false);
        }
    }

    // When module type changes, filter the modules
    moduleTypeSelect.addEventListener('change', function() {
        filterAndPopulateModules();
    });

    // 5. Add module to table
    addModuleBtn.addEventListener('click', function() {
        const moduleId = moduleSelect.value;
        const moduleName = moduleSelect.options[moduleSelect.selectedIndex]?.text;
        const moduleType = moduleTypeSelect.value;
        const moduleOption = moduleSelect.options[moduleSelect.selectedIndex];
        const moduleCredits = moduleOption ? moduleOption.getAttribute('data-credits') : '';
        const semester = semesterSelect.value;
        let specialization = '';
        if (document.getElementById('specializationRow').style.display !== 'none') {
            specialization = document.getElementById('specialization_select').value || '';
        }
        if (!moduleId || !moduleName || !semester) return;
        // Prevent duplicate
        if (addedModules.some(m => m.moduleId === moduleId && m.semester === semester)) return;
        addedModules.push({moduleId, moduleName, moduleType, moduleCredits, semester, specialization});
        const row = document.createElement('tr');
        let rowHtml = `<td>${semesterSelect.options[semesterSelect.selectedIndex].text}</td>`;
        if (courseSpecializations.length > 0) {
            rowHtml += `<td>${specialization ? specialization : '-'}</td>`;
        }
        rowHtml += `
            <td>${moduleSelect.options[moduleSelect.selectedIndex].text}</td>
            <td>${moduleTypeSelect.value}</td>
            <td>${moduleSelect.selectedOptions[0].getAttribute('data-credits') || ''}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-module">Remove</button></td>
        `;
        row.innerHTML = rowHtml;
        row.dataset.moduleId = moduleId;
        row.dataset.semester = semester;
        modulesTableBody.appendChild(row);
    });

    // 6. Remove module from table
    modulesTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-module')) {
            const row = e.target.closest('tr');
            const moduleId = row.dataset.moduleId;
            const semester = row.dataset.semester;
            addedModules = addedModules.filter(m => !(m.moduleId === moduleId && m.semester === semester));
            row.remove();
        }
    });
});

// Toast function
window.showToast = function(message, type = 'success') {
    const toastEl = document.getElementById('mainToast');
    const toastBody = document.getElementById('mainToastBody');
    toastBody.textContent = message;
    toastEl.className = 'toast align-items-center border-0 text-bg-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary')));
    const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
    toast.show();
};
// AJAX form submission for semester creation
const semesterForm = document.querySelector('form[action="<?php echo e(route('semesters.store')); ?>"]');
semesterForm.addEventListener('submit', function(e) {
    e.preventDefault();
    
    // Validate required fields
    const requiredFields = ['location', 'course_id', 'intake_id', 'semester', 'start_date', 'end_date', 'registration_date', 'status'];
    const missingFields = [];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field);
        if (!element || !element.value) {
            missingFields.push(field);
        }
    });
    
    if (missingFields.length > 0) {
        showToast('Please fill in all required fields: ' + missingFields.join(', '), 'danger');
        return;
    }
    
    // Gather form data as JSON
    const formData = {
        location: document.getElementById('location').value,
        course_id: document.getElementById('course_id').value,
        intake_id: document.getElementById('intake_id').value,
        semester: document.getElementById('semester').value,
        start_date: document.getElementById('start_date').value,
        end_date: document.getElementById('end_date').value,
        registration_date: document.getElementById('registration_date').value,
        status: document.getElementById('status').value,
        _token: '<?php echo e(csrf_token()); ?>'
    };
    
    // Debug: Log form data
    console.log('Form data being sent:', formData);
    
    // Gather modules with specialization
    const modules = [];
    document.querySelectorAll('#modules_table tbody tr').forEach(row => {
        const cells = row.querySelectorAll('td');
        if (cells.length >= 5) {
            const semester = cells[0].textContent.trim();
            let specialization = null;
            let moduleName, moduleType, credits;
            if (courseSpecializations.length > 0) {
                specialization = cells[1].textContent.trim();
                moduleName = cells[2].textContent.trim();
                moduleType = cells[3].textContent.trim();
                credits = cells[4].textContent.trim();
            } else {
                moduleName = cells[1].textContent.trim();
                moduleType = cells[2].textContent.trim();
                credits = cells[3].textContent.trim();
            }
            // Find module_id by name (assumes moduleSelect options have unique names)
            let moduleId = null;
            document.querySelectorAll('#module_select option').forEach(opt => {
                if (opt.textContent.trim() === moduleName) {
                    moduleId = opt.value;
                }
            });
            if (moduleId) {
                modules.push({
                    module_id: moduleId,
                    specialization: specialization && specialization !== '-' ? specialization : null
                });
            }
        }
    });
    
    // Add modules to form data
    formData.modules = modules;
    
    // Debug: Log final data
    console.log('Final data with modules:', formData);
    
    // Send AJAX request
    fetch(semesterForm.action, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => {
        console.log('Response status:', response.status);
        if (!response.ok) {
            return response.json().then(data => {
                console.error('Error response:', data);
                throw new Error(data.message || 'Network response was not ok');
            });
        }
        return response.json();
    })
    .then(data => {
        console.log('Success response:', data);
        if (data.success) {
            showToast(data.message || 'Semester created successfully!', 'success');
            // Refresh the page after a short delay to show the success message
            setTimeout(() => {
                window.location.reload();
            }, 1500);
        } else {
            showToast(data.message || 'Failed to create semester.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showToast(error.message || 'An unexpected error occurred.', 'danger');
    });
});
</script>
<?php $__env->stopSection(); ?>

<style>
    select:disabled {
        background-color: #f5f5f5 !important;
        border-color: #ddd !important;
        color: #aaa !important;
    }
    select:enabled {
        border-color: #6c8cff !important;
        box-shadow: 0 0 0 0.1rem #6c8cff33;
    }
</style>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/semester_creation.blade.php ENDPATH**/ ?>