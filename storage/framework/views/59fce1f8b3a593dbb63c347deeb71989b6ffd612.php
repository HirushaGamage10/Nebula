

<?php $__env->startSection('title', 'NEBULA | Repeat Students Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-body">
            <h2 class="text-center mb-4 text-primary">Repeat Students Management</h2>
            <hr>
            <!-- NIC Search Section -->
            <div class="row mb-4 justify-content-center">
                <div class="col-md-8">
                    <div class="p-3 rounded bg-light">
                        <form id="nicSearchForm" autocomplete="off">
                            <div class="input-group">
                                <input type="text" class="form-control" id="nicInput" name="nic" placeholder="Enter NIC number" required>
                                <button class="btn btn-primary" type="submit" style="min-width:120px;">Search</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- Student Profile Section (hidden by default) -->
            <div class="container mt-4 rounded border shadow p-4 bg-white" id="profileSection" style="display:none;">
                <input type="hidden" id="studentIdHidden" value="">
                <!-- Tabs -->
                <ul class="nav nav-tabs mb-3" id="studentTabs">
                    <li class="nav-item">
                        <a class="nav-link active bg-primary text-white" id="profile-tab" data-bs-toggle="tab" href="#profile">Profile Info</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="register-tab" data-bs-toggle="tab" href="#register">Re-Register Intake</a>
                    </li>
                </ul>
                <div class="tab-content mt-2">
                    <!-- PROFILE INFO TAB -->
                    <div class="tab-pane fade show active" id="profile">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-primary mb-3"><i class="ti ti-user"></i> Student Profile</h5>
                                        <div class="mb-2"><strong>Name:</strong> <span id="studentName"></span></div>
                                        <div class="mb-2"><strong>Email:</strong> <span id="studentEmail"></span></div>
                                        <div class="mb-2"><strong>Mobile:</strong> <span id="studentMobile"></span></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-light mb-3">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-secondary mb-3"><i class="ti ti-school"></i> Academic Info</h5>
                                        <div class="mb-2"><strong>Institute:</strong> <span id="studentInstitute"></span></div>
                                        <div class="mb-2"><strong>Date of Birth:</strong> <span id="studentDOB"></span></div>
                                        <div class="mb-2"><strong>Gender:</strong> <span id="studentGender"></span></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-md-12">
                                <div class="card border-0 bg-light">
                                    <div class="card-body">
                                        <h5 class="fw-bold text-primary mb-3"><i class="ti ti-list-details"></i> Holding Courses</h5>
                                        <table class="table table-bordered table-striped table-hover shadow-sm">
                                            <thead class="bg-primary text-white">
                                                <tr>
                                                    <th>Course</th>
                                                    <th>Intake</th>
                                                    <th>Specialization</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody id="holdingTableBody">
                                                <!-- Populated by JS -->
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- RE-REGISTER INTAKE TAB -->
                    <div class="tab-pane fade" id="register">
                        <h5 class="fw-bold mb-3 mt-3 text-primary">Re-Register Intake</h5>
                        <form id="reRegisterForm">
                            <?php echo csrf_field(); ?>
                            <input type="hidden" name="registration_id" id="registration_id">
                            <div class="mb-3 row mx-3">
                                <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="location" name="location" required>
                                        <option value="">Select a Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="course_id" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="course_id" name="course_id" required>
                                        <option value="">Select Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="intake_id" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="intake_id" name="intake_id" required>
                                        <option value="">Select Intake</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <label for="semester_id" class="col-sm-2 col-form-label">Semester <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="semester_id" name="semester_id" required>
                                        <option value="">Select Semester</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3" id="specialization_row" style="display:none;">
                                <label for="specialization" class="col-sm-2 col-form-label">Specialization</label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="specialization" name="specialization">
                                        <option value="">Select Specialization</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row mx-3">
                                <div class="col-sm-10 offset-sm-2">
                                    <button type="submit" class="btn btn-success px-4" id="updateBtn">Update</button>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!-- PAYMENT PLAn TAB -->
                    
                </div>
            </div>
            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>
        </div>
    </div>
</div>

<style>
.lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
.lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #007bff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #007bff transparent transparent transparent; }
.lds-ring div:nth-child(1) { animation-delay: -0.45s; }
.lds-ring div:nth-child(2) { animation-delay: -0.3s; }
.lds-ring div:nth-child(3) { animation-delay: -0.15s; }
@keyframes lds-ring { 0% { transform: rotate(0deg);} 100% { transform: rotate(360deg);} }
#spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }
</style>

<script>
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
    setTimeout(() => {
        if (toastElement.parentNode) {
            toastElement.parentNode.removeChild(toastElement);
        }
    }, 5000);
}

function showSpinner(show) {
    document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
}

function formatDate(dateStr) {
    if (!dateStr) return '';
    const d = new Date(dateStr);
    if (isNaN(d)) return dateStr.split('T')[0];
    return d.toISOString().slice(0, 10);
}

// Fill student profile info
function fillStudentProfile(student){
    document.getElementById('studentName').textContent = student.full_name || student.name_with_initials || '';
    document.getElementById('studentEmail').textContent = student.email || '';
    document.getElementById('studentMobile').textContent = student.mobile_phone || '';
    document.getElementById('studentInstitute').textContent = student.institute_location || '';
    document.getElementById('studentDOB').textContent = formatDate(student.birthday);
    document.getElementById('studentGender').textContent = student.gender || '';
}

// Fill holding semester registrations table
function fillHoldingTable(holding_history){
    const tbody = document.getElementById('holdingTableBody');
    tbody.innerHTML = '';
    if(!holding_history.length){
        tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No holding semester registrations found.</td></tr>`;
        return;
    }
    holding_history.forEach(h => {
        tbody.innerHTML += `
            <tr>
                <td>${h.course_name || ''}</td>
                <td>${h.intake || ''}</td>
                <td>${h.specialization || ''}</td>
                <td><span class="badge bg-warning">${h.status || ''}</span></td>
            </tr>
        `;
    });
}

// Fill the re-register form with registration data
function fillReRegisterForm(reg) {
    document.getElementById('registration_id').value = reg.id || '';
    // Normalize stored location (some records store a long name like "Nebula Institute of Technology - Welisara")
    const locationSelect = document.getElementById('location');
    const rawLoc = (reg.location || '').toString();
    let normalizedLoc = '';
    if (/welisara/i.test(rawLoc)) normalizedLoc = 'Welisara';
    else if (/moratuwa/i.test(rawLoc)) normalizedLoc = 'Moratuwa';
    else if (/peradeniya/i.test(rawLoc)) normalizedLoc = 'Peradeniya';
    else normalizedLoc = rawLoc; // fallback if unknown
    if (locationSelect) locationSelect.value = normalizedLoc;

    // Populate course dropdown with only the repeated course (avoid listing every course)
    const courseSelect = document.getElementById('course_id');
    courseSelect.innerHTML = '<option value="">Select Course</option>';
    if (reg.course_id) {
        courseSelect.innerHTML += `<option value="${reg.course_id}" selected>${reg.course_name || reg.course_id}</option>`;
    }

    // Replace select node to remove previous listeners and attach a fresh one
    const newCourseSelect = courseSelect.cloneNode(true);
    courseSelect.parentNode.replaceChild(newCourseSelect, courseSelect);
    newCourseSelect.addEventListener('change', function() {
        populateIntakes(this.value, null, null, document.getElementById('location').value || normalizedLoc);
    });

    // Populate intakes for this course and normalized location, preselect intake & semester
    populateIntakes(reg.course_id, reg.intake_id || null, reg.semester_id || null, normalizedLoc);

    // Show specialization if available; keep existing value if not changed
    const specRow = document.getElementById('specialization_row');
    const specSelect = document.getElementById('specialization');
    if (reg.specialization && reg.specialization !== '') {
        specRow.style.display = '';
        specSelect.innerHTML = `<option value="">Select Specialization</option><option value="${reg.specialization}" selected>${reg.specialization}</option>`;
    } else {
        specRow.style.display = 'none';
        specSelect.innerHTML = '<option value="">Select Specialization</option>';
    }
}



// Helper function to get ordinal numbers (1st, 2nd, 3rd, etc.)
function getOrdinalNumber(num) {
    const suffixes = ['th', 'st', 'nd', 'rd'];
    const value = num % 100;
    return num + (suffixes[(value - 20) % 10] || suffixes[value] || suffixes[0]);
}

// Format currency
function formatCurrency(amount) {
    return 'LKR ' + parseFloat(amount || 0).toLocaleString('en-US', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
}

function formatDateForInput(dateStr) {
    if (!dateStr || dateStr === '-') return '';
    const date = new Date(dateStr);
    return date.toISOString().split('T')[0];
}

// Function to populate intakes based on course
function populateIntakes(courseId, selectedIntakeId = null, selectedSemesterId = null, location = null) {
    const intakeSelect = document.getElementById('intake_id');
    if (!courseId) {
        intakeSelect.innerHTML = '<option value="">Select Intake</option>';
        document.getElementById('semester_id').innerHTML = '<option value="">Select Semester</option>';
        return;
    }

    // default to currently selected location if not provided
    if (!location) {
        location = document.getElementById('location') ? document.getElementById('location').value : '';
    }

    const q = `?course_id=${encodeURIComponent(courseId)}${location ? '&location=' + encodeURIComponent(location) : ''}`;
    fetch(`/api/intakes${q}`)
        .then(r => r.json())
        .then(data => {
            intakeSelect.innerHTML = '<option value="">Select Intake</option>';
            const nextId = data.next_intake_id || null;
            (data.intakes || []).forEach(i => {
                const isSelected = selectedIntakeId && (i.intake_id == selectedIntakeId);
                let label = i.batch || i.intake_no || i.intake_display_name || '';
                if (nextId && (i.intake_id == nextId)) label += ' — next';
                const selectedAttr = isSelected ? 'selected' : '';
                intakeSelect.innerHTML += `<option value="${i.intake_id}" ${selectedAttr}>${label}</option>`;
            });

            // Replace node to clear listeners then attach one
            const newIntakeSelect = intakeSelect.cloneNode(true);
            intakeSelect.parentNode.replaceChild(newIntakeSelect, intakeSelect);
            newIntakeSelect.addEventListener('change', function() {
                populateSemesters(courseId, this.value);
            });

            // If a selected intake was provided, populate semesters and preselect
            if (selectedIntakeId) {
                populateSemesters(courseId, selectedIntakeId, selectedSemesterId || null);
            }
        })
        .catch(() => {
            intakeSelect.innerHTML = '<option value="">Select Intake</option>';
        });
}

// Function to populate semesters based on course and intake
function populateSemesters(courseId, intakeId, selectedSemesterId = null) {
    const semesterSelect = document.getElementById('semester_id');
    semesterSelect.innerHTML = '<option value="">Select Semester</option>';

    // If no course or intake is selected, stop here
    if (!courseId || !intakeId) {
        return;
    }

    // Fetch semesters filtered by course & intake
    fetch(`/api/semesters?course_id=${courseId}&intake_id=${intakeId}`)
        .then(response => response.json())
        .then(data => {
            if (!data.success || !Array.isArray(data.semesters)) {
                semesterSelect.innerHTML = '<option value="">No semesters found</option>';
                return;
            }

            // Populate the dropdown
            data.semesters.forEach(s => {
                const selected = selectedSemesterId && (s.id == selectedSemesterId) ? 'selected' : '';
                const label = s.display_name || s.name || `Semester ${s.id}`;
                semesterSelect.innerHTML += `<option value="${s.id}" ${selected}>${label}</option>`;
            });

            // If no semesters exist at all
            if (data.semesters.length === 0) {
                semesterSelect.innerHTML = '<option value="">No semesters available for this intake</option>';
            }
        })
        .catch(error => {
            console.error('Error loading semesters:', error);
            semesterSelect.innerHTML = '<option value="">Error loading semesters</option>';
        });
}


// Store current student data globally
let currentStudentData = null;

// Function to perform student search
function performStudentSearch() {
    const nic = document.getElementById('nicInput').value.trim();
    if(!nic) {
        showToast('Error', 'Please enter a NIC number to search.', 'bg-warning');
        return;
    }
    
    console.log('Searching for student with NIC:', nic);
    showSpinner(true);
    
    fetch(`/api/repeat-student-by-nic?nic=${encodeURIComponent(nic)}`)
        .then(response => {
            console.log('Search response status:', response.status);
            return response.json();
        })
        .then(res => {
            console.log('Search response data:', res);
            if(res.success && res.student){
                currentStudentData = res; // Store for later use
                fillStudentProfile(res.student);
                fillHoldingTable(res.holding_history || []);
                document.getElementById('profileSection').style.display = '';
                document.getElementById('studentIdHidden').value = res.student.student_id || '';
                document.querySelector('#studentTabs .nav-link.active').click();
                // Fill re-register form with latest holding registration when present
                if(res.holding_history && res.holding_history.length){
                    fillReRegisterForm(res.holding_history[0]);
                }

                // Decide which registration to use for payment plan display:
                // Prefer the first holding registration (student being re-registered), otherwise use the active/current registration returned by the API.
                let planCourseId = null;
                if (res.holding_history && res.holding_history.length && res.holding_history[0].course_id) {
                    planCourseId = res.holding_history[0].course_id;
                } else if (res.current_registration && res.current_registration.course_id) {
                    planCourseId = res.current_registration.course_id;
                }

                if (planCourseId) {
                    console.log('Loading payment plan for student:', res.student.student_id, 'course:', planCourseId);
                    // loadPaymentPlanData(res.student.student_id, planCourseId);
                } else {
                    console.log('No course registration found to load payment plan');
                }
                if(res.student.academic_status === 'holding'){
                    showToast('Notice', 'This student is currently on hold.', 'bg-warning');
                }
                showToast('Success', 'Student found and data loaded successfully.', 'bg-success');
            }else{
                document.getElementById('profileSection').style.display = 'none';
                showToast('Error', res.message || 'Student not found!', 'bg-danger');
            }
        })
        .catch(error => {
            console.error('Search error:', error);
            document.getElementById('profileSection').style.display = 'none';
            showToast('Error', 'Error fetching student details.', 'bg-danger');
        })
        .finally(() => showSpinner(false));
}

// NIC search and load profile + registration
document.getElementById('nicSearchForm').addEventListener('submit', function(e){
    e.preventDefault();
    performStudentSearch();
});

// Also add click event to search button for better UX
document.addEventListener('DOMContentLoaded', function() {
    const searchButton = document.querySelector('#nicSearchForm button[type="submit"]');
    const nicInput = document.getElementById('nicInput');
    
    if (searchButton) {
        searchButton.addEventListener('click', function(e) {
            e.preventDefault();
            performStudentSearch();
        });
    }
    
    // Add Enter key support for search
    if (nicInput) {
        nicInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                performStudentSearch();
            }
        });
    }
});

// Handle update form submit
document.getElementById('reRegisterForm').addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(this);
    const updateBtn = document.getElementById('updateBtn');
    updateBtn.disabled = true;
    updateBtn.textContent = 'Updating...';
    fetch('/repeat-students/update-semester-registration', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        }
    })
    .then(r => r.json())
    .then(res => {
        if(res.success){
            showToast('Success', res.message, 'bg-success');
            // If backend returned an updated course registration, reload its payment plan
            if (res.updated_course_registration && res.updated_course_registration.course_id) {
                // Update the currentStudentData.holding_history first (replace the updated one)
                performStudentSearch(); // refresh student data so UI shows updated state
                // loadPaymentPlanData(document.getElementById('studentIdHidden').value, res.updated_course_registration.course_id);
            } else {
                // fallback: refresh student data
                performStudentSearch();
            }
        }else{
            showToast('Error', res.message || 'Update failed.', 'bg-danger');
        }
    })
    .catch(() => showToast('Error', 'Error updating registration.', 'bg-danger'))
        .finally(() => {
            updateBtn.disabled = false;
            updateBtn.textContent = 'Update';
        });
});

// Handle tab clicks to load payment plan data
document.addEventListener('DOMContentLoaded', function() {
    // Add click event listeners to tabs
    const paymentTab = document.getElementById('payment-tab');
    if (paymentTab) {
        paymentTab.addEventListener('click', function() {
            console.log('Payment tab clicked');
            console.log('Current student data:', currentStudentData);
            
            if (currentStudentData && currentStudentData.student) {
                if (currentStudentData.holding_history && currentStudentData.holding_history.length > 0) {
                    const latestRegistration = currentStudentData.holding_history[0];
                    console.log('Loading payment plan for student:', currentStudentData.student.student_id, 'course:', latestRegistration.course_id);
                    // loadPaymentPlanData(currentStudentData.student.student_id, latestRegistration.course_id);
                } else {
                    console.log('No holding history found for student');
                    document.getElementById('paymentPlanLoading').style.display = 'none';
                    document.getElementById('noPaymentPlanMessage').innerHTML = `
                        <i class="ti ti-info-circle fs-4 mb-2"></i>
                        <h6 class="mb-2">No Registration Data</h6>
                        <p class="mb-0">No course registration found for this student. Payment plan cannot be displayed.</p>
                    `;
                    document.getElementById('noPaymentPlanMessage').style.display = 'block';
                }
            } else {
                console.log('No student data available');
                // Show message that student data needs to be loaded first
                document.getElementById('paymentPlanLoading').style.display = 'none';
                document.getElementById('noPaymentPlanMessage').innerHTML = `
                    <i class="ti ti-info-circle fs-4 mb-2"></i>
                    <h6 class="mb-2">No Student Data</h6>
                    <p class="mb-0">Please search for a student first to view payment plan details.</p>
                `;
                document.getElementById('noPaymentPlanMessage').style.display = 'block';
            }
        });
    }
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/repeat_students.blade.php ENDPATH**/ ?>