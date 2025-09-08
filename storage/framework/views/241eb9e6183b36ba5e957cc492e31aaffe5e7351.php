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
                    <li class="nav-item">
                        <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment">Payment Plan</a>
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
                    <!-- PAYMENT PLAN TAB -->
                    <div class="tab-pane fade" id="payment">
                        <div class="alert alert-info mb-3" id="paymentPlanAlert" style="display: none;">
                            <i class="ti ti-info-circle"></i>
                            <strong>Payment Plan for:</strong> <span id="currentStudentDisplay">-</span>
                        </div>
                        <h5 class="fw-bold mb-3 mt-3 text-primary">Payment Plan Details</h5>
                        
                        <!-- Student Information Card -->
                        <div class="card border-0 bg-light mb-4" id="paymentStudentInfo" style="display: none;">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-user"></i> Student Information</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-2"><strong>Name:</strong> <span id="pp_student_name">-</span></div>
                                        <div class="mb-2"><strong>Student ID:</strong> <span id="pp_student_id">-</span></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-2"><strong>Course:</strong> <span id="pp_course">-</span></div>
                                        <div class="mb-2"><strong>Intake:</strong> <span id="pp_intake">-</span></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Plan Summary Card -->
                        <div class="card border-0 bg-light mb-4" id="paymentPlanSummary" style="display: none;">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-credit-card"></i> Payment Plan Summary</h6>
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-white rounded shadow-sm">
                                            <h6 class="text-muted mb-1">Plan Type</h6>
                                            <h5 class="text-primary mb-0" id="pp_plan_type_display">-</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-white rounded shadow-sm">
                                            <h6 class="text-muted mb-1">Total Amount</h6>
                                            <h5 class="text-success mb-0" id="pp_total_amount">-</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-white rounded shadow-sm">
                                            <h6 class="text-muted mb-1">Final Amount</h6>
                                            <h5 class="text-info mb-0" id="pp_final_amount_display">-</h5>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center p-3 bg-white rounded shadow-sm">
                                            <h6 class="text-muted mb-1">SLT Loan</h6>
                                            <h5 class="text-warning mb-0" id="pp_slt_loan">-</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Discounts Information -->
                        <div class="card border-0 bg-light mb-4" id="discountsInfo" style="display: none;">
                            <div class="card-body">
                                <h6 class="fw-bold text-primary mb-3"><i class="ti ti-discount"></i> Applied Discounts</h6>
                                <div id="discountsList">
                                    <!-- Discounts will be populated here -->
                                </div>
                            </div>
                        </div>

                        <!-- Installments Table -->
                        <div class="card border-0 shadow-sm" id="installmentsCard" style="display: none;">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <h6 class="mb-0"><i class="ti ti-calendar"></i> Payment Installments</h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-light me-2" id="editInstallmentsBtn" onclick="toggleEditMode()">
                                        <i class="ti ti-edit"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-success" id="saveInstallmentsBtn" onclick="saveInstallments()" style="display: none;">
                                        <i class="ti ti-check"></i> Save
                                    </button>
                                    <button type="button" class="btn btn-sm btn-secondary" id="cancelEditBtn" onclick="cancelEdit()" style="display: none;">
                                        <i class="ti ti-x"></i> Cancel
                                    </button>
                                </div>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover mb-0">
                                        <thead class="bg-light">
                                            <tr>
                                                <th class="text-center">Installment #</th>
                                                <th>Description</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Base Amount</th>
                                                <th class="text-end">Discount</th>
                                                <th class="text-end">SLT Loan</th>
                                                <th class="text-end">Final Amount</th>
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Paid Date</th>
                                                <th class="text-center">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody id="installmentsTableBody">
                                            <!-- Installments will be populated here -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <!-- No Payment Plan Message -->
                        <div class="alert alert-info text-center" id="noPaymentPlanMessage" style="display: none;">
                            <i class="ti ti-info-circle fs-4 mb-2"></i>
                            <h6 class="mb-2">No Payment Plan Found</h6>
                            <p class="mb-0">This student doesn't have a payment plan created yet.</p>
                        </div>

                        <!-- Loading Message -->
                        <div class="text-center py-5" id="paymentPlanLoading">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Loading payment plan details...</p>
                        </div>
                    </div>
                    
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

// Load payment plan data for the student
function loadPaymentPlanData(studentId, courseId) {
    console.log('Loading payment plan data for student:', studentId, 'course:', courseId);
    
    // Show loading state
    // Reset UI
    document.getElementById('paymentPlanLoading').style.display = 'block';
    document.getElementById('paymentStudentInfo').style.display = 'none';
    document.getElementById('paymentPlanSummary').style.display = 'none';
    document.getElementById('discountsInfo').style.display = 'none';
    document.getElementById('installmentsCard').style.display = 'none';
    document.getElementById('noPaymentPlanMessage').style.display = 'none';
    document.getElementById('paymentPlanAlert').style.display = 'none';

    const url = `/api/payment-plan/${encodeURIComponent(studentId)}/${encodeURIComponent(courseId)}`;
    console.log('Fetching from URL:', url);

    fetch(url)
        .then(async response => {
            let data = null;
            try { data = await response.json(); } catch (e) { /* ignore parse errors */ }
            if (!response.ok) {
                // Backend returned 4xx/5xx. Show no-plan message with backend message when available.
                console.warn('Payment plan fetch returned non-OK status', response.status, data);
                document.getElementById('paymentPlanLoading').style.display = 'none';
                document.getElementById('noPaymentPlanMessage').innerHTML = `\n                    <i class="ti ti-info-circle fs-4 mb-2"></i>\n                    <h6 class="mb-2">No Payment Plan Found</h6>\n                    <p class="mb-0">${(data && data.message) ? data.message : 'This student does not have a payment plan created yet.'}</p>\n                `;
                document.getElementById('noPaymentPlanMessage').style.display = 'block';
                return null;
            }
            return data;
        })
        .then(data => {
            if (!data) return; // already handled above
            console.log('Payment plan data received:', data);
            document.getElementById('paymentPlanLoading').style.display = 'none';

            if (data.success && data.payment_plan) {
                displayPaymentPlan(data.payment_plan, data.student, data.course, data.intake);
            } else {
                // No payment plan present — show the message area with explanation
                console.log('No payment plan found or error:', data.message);
                document.getElementById('noPaymentPlanMessage').innerHTML = `\n                    <i class="ti ti-info-circle fs-4 mb-2"></i>\n                    <h6 class="mb-2">No Payment Plan Found</h6>\n                    <p class="mb-0">${data.message || "This student doesn't have a payment plan created yet."}</p>\n                `;
                document.getElementById('noPaymentPlanMessage').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error loading payment plan:', error);
            document.getElementById('paymentPlanLoading').style.display = 'none';
            document.getElementById('noPaymentPlanMessage').innerHTML = `\n                <i class="ti ti-alert-circle fs-4 mb-2"></i>\n                <h6 class="mb-2">Error</h6>\n                <p class="mb-0">Failed to load payment plan details. Please try again later.</p>\n            `;
            document.getElementById('noPaymentPlanMessage').style.display = 'block';
            showToast('Error', 'Failed to load payment plan details.', 'bg-danger');
        });
}

// Display payment plan data
function displayPaymentPlan(paymentPlan, student, course, intake) {
    console.log('Displaying payment plan for student:', student);
    
    // Show the student identification alert at the top
    const studentDisplay = `${student.full_name || student.name_with_initials || 'Unknown'} (ID: ${student.student_id || 'N/A'})`;
    document.getElementById('currentStudentDisplay').textContent = studentDisplay;
    document.getElementById('paymentPlanAlert').style.display = 'block';
    
    // Show student information with clear identification
    document.getElementById('pp_student_name').textContent = student.full_name || student.name_with_initials || '-';
    document.getElementById('pp_student_id').textContent = student.student_id || '-';
    document.getElementById('pp_course').textContent = course.course_name || '-';
    document.getElementById('pp_intake').textContent = intake ? `${intake.batch || intake.intake_no || ''}` : '-';
    
    // Add a visual indicator that this is for the searched student
    const studentInfoCard = document.getElementById('paymentStudentInfo');
    studentInfoCard.style.display = 'block';
    
    // Add a header to make it clear this is for the searched student
    const studentInfoHeader = studentInfoCard.querySelector('h6');
    if (studentInfoHeader) {
        studentInfoHeader.innerHTML = `<i class="ti ti-user"></i> Student Information - ${student.full_name || student.name_with_initials || 'Unknown'}`;
    }

    // Show payment plan summary
    document.getElementById('pp_plan_type_display').textContent = paymentPlan.payment_plan_type || '-';
    document.getElementById('pp_total_amount').textContent = formatCurrency(paymentPlan.total_amount || 0);
    document.getElementById('pp_final_amount_display').textContent = formatCurrency(paymentPlan.final_amount || 0);
    document.getElementById('pp_slt_loan').textContent = paymentPlan.slt_loan_applied === 'yes' ? formatCurrency(paymentPlan.slt_loan_amount || 0) : 'No Loan';
    
    // Add clear header to payment plan summary
    const summaryCard = document.getElementById('paymentPlanSummary');
    const summaryHeader = summaryCard.querySelector('h6');
    if (summaryHeader) {
        summaryHeader.innerHTML = `<i class="ti ti-credit-card"></i> Payment Plan Summary - ${student.full_name || student.name_with_initials || 'Unknown'}`;
    }
    summaryCard.style.display = 'block';

    // Show discounts if any
    if (paymentPlan.discounts && paymentPlan.discounts.length > 0) {
        displayDiscounts(paymentPlan.discounts);
        document.getElementById('discountsInfo').style.display = 'block';
    }

    // Show installments
    if (paymentPlan.installments && paymentPlan.installments.length > 0) {
        displayInstallments(paymentPlan.installments);
        const installmentsCard = document.getElementById('installmentsCard');
        const installmentsHeader = installmentsCard.querySelector('h6');
        if (installmentsHeader) {
            installmentsHeader.innerHTML = `<i class="ti ti-calendar"></i> Payment Installments - ${student.full_name || student.name_with_initials || 'Unknown'}`;
        }
        installmentsCard.style.display = 'block';
    }
}

// Display discounts
function displayDiscounts(discounts) {
    const discountsList = document.getElementById('discountsList');
    discountsList.innerHTML = '';

    if (discounts.length === 0) {
        discountsList.innerHTML = '<p class="text-muted mb-0">No discounts applied.</p>';
        return;
    }

    discounts.forEach(discount => {
        const discountCard = document.createElement('div');
        discountCard.className = 'card border-0 bg-white mb-2';
        discountCard.innerHTML = `
            <div class="card-body py-2">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <strong>${discount.discount_name || 'Discount'}</strong>
                    </div>
                    <div class="col-md-3">
                        <span class="badge bg-info">${discount.discount_type || 'percentage'}</span>
                    </div>
                    <div class="col-md-3">
                        <strong>${discount.discount_value || 0}${discount.discount_type === 'percentage' ? '%' : ' LKR'}</strong>
                    </div>
                    <div class="col-md-2 text-end">
                        <small class="text-muted">${discount.discount_description || ''}</small>
                    </div>
                </div>
            </div>
        `;
        discountsList.appendChild(discountCard);
    });
}

// Store original installments data for editing
let originalInstallmentsData = [];
let isEditMode = false;

// Display installments grouped by fee type
function displayInstallments(installments) {
    const tbody = document.getElementById('installmentsTableBody');
    tbody.innerHTML = '';
    originalInstallmentsData = [...installments]; // Store original data

    // Group installments by fee type
    const feeTypeGroups = {
        'registration_fee': installments.filter(i => i.fee_type === 'registration_fee'),
        'course_fee': installments.filter(i => i.fee_type === 'course_fee'),
        'franchise_fee': installments.filter(i => i.fee_type === 'franchise_fee')
    };
    
    // Define fee types to display
    const feeTypes = [
        { key: 'registration_fee', label: 'Registration Fee' },
        { key: 'course_fee', label: 'Local Course Fee' },
        { key: 'franchise_fee', label: 'Franchise Fee' }
    ];

    // Display each fee type as a section
    feeTypes.forEach(feeType => {
        const feeInstallments = feeTypeGroups[feeType.key];
        
        if (feeInstallments && feeInstallments.length > 0) {
            // Add section header for fee type
            const headerRow = document.createElement('tr');
            headerRow.classList.add('table-primary', 'fw-bold');
            headerRow.innerHTML = `
                <td colspan="10" class="text-center py-3">
                    <i class="ti ti-credit-card me-2"></i>${feeType.label}
                </td>
            `;
            tbody.appendChild(headerRow);

            // Add rows for each installment of this fee type
            feeInstallments.forEach(installment => {
                const row = document.createElement('tr');
                row.dataset.installmentNumber = installment.installment_number;
                row.dataset.feeType = feeType.key;
                row.dataset.installmentId = installment.id;
                
                // Status badge
                let statusBadge = '';
                switch(installment.status) {
                    case 'paid':
                        statusBadge = '<span class="badge bg-success">Paid</span>';
                        break;
                    case 'overdue':
                        statusBadge = '<span class="badge bg-danger">Overdue</span>';
                        break;
                    default:
                        statusBadge = '<span class="badge bg-warning">Pending</span>';
                }

                // Check if installment is overdue
                const dueDate = new Date(installment.due_date);
                const today = new Date();
                const isOverdue = dueDate < today && installment.status !== 'paid';
                
                if (isOverdue) {
                    row.classList.add('table-danger');
                } else if (installment.status === 'paid') {
                    row.classList.add('table-success');
                }

                // For registration fee, show as single payment
                let description = '';
                if (feeType.key === 'registration_fee') {
                    description = 'Registration Payment';
                } else {
                    description = `${getOrdinalNumber(installment.installment_number)} Installment`;
                }

                row.innerHTML = `
                    <td class="text-center">${installment.installment_number}</td>
                    <td class="text-muted">${description}</td>
                    <td>${formatDate(installment.due_date)}</td>
                    <td class="text-end">${formatCurrency(installment.base_amount || 0)}</td>
                    <td class="text-end">${formatCurrency(installment.discount_amount || 0)}</td>
                    <td class="text-end">${formatCurrency(installment.slt_loan_amount || 0)}</td>
                    <td class="text-end"><strong>${formatCurrency(installment.final_amount || 0)}</strong></td>
                    <td class="text-center">${statusBadge}</td>
                    <td class="text-center">${installment.paid_date ? formatDate(installment.paid_date) : '-'}</td>
                    <td class="text-center">
                        <button type="button" class="btn btn-sm btn-outline-primary edit-row-btn" onclick="editRow(this)" style="display: none;">
                            <i class="ti ti-edit"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success save-row-btn" onclick="saveRow(this)" style="display: none;">
                            <i class="ti ti-check"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary cancel-row-btn" onclick="cancelRowEdit(this)" style="display: none;">
                            <i class="ti ti-x"></i>
                        </button>
                    </td>
                `;
                
                tbody.appendChild(row);
            });
        }
    });
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

// Toggle edit mode for the entire table
function toggleEditMode() {
    isEditMode = !isEditMode;
    const editBtn = document.getElementById('editInstallmentsBtn');
    const saveBtn = document.getElementById('saveInstallmentsBtn');
    const cancelBtn = document.getElementById('cancelEditBtn');
    const editRowBtns = document.querySelectorAll('.edit-row-btn');
    
    if (isEditMode) {
        editBtn.style.display = 'none';
        saveBtn.style.display = 'inline-block';
        cancelBtn.style.display = 'inline-block';
        editRowBtns.forEach(btn => btn.style.display = 'inline-block');
    } else {
        editBtn.style.display = 'inline-block';
        saveBtn.style.display = 'none';
        cancelBtn.style.display = 'none';
        editRowBtns.forEach(btn => btn.style.display = 'none');
        // Cancel any ongoing row edits
        document.querySelectorAll('.save-row-btn, .cancel-row-btn').forEach(btn => btn.style.display = 'none');
    }
}

// Edit a specific row
function editRow(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Store original values
    row.dataset.originalDueDate = cells[2].textContent;
    row.dataset.originalBaseAmount = cells[3].textContent.replace(/[^\d.-]/g, '');
    row.dataset.originalDiscount = cells[4].textContent.replace(/[^\d.-]/g, '');
    row.dataset.originalSltLoan = cells[5].textContent.replace(/[^\d.-]/g, '');
    row.dataset.originalStatus = cells[7].textContent.trim();
    row.dataset.originalPaidDate = cells[8].textContent;
    
    // Convert to input fields
    cells[2].innerHTML = `<input type="date" class="form-control form-control-sm" value="${formatDateForInput(cells[2].textContent)}">`;
    cells[3].innerHTML = `<input type="number" class="form-control form-control-sm" value="${cells[3].textContent.replace(/[^\d.-]/g, '')}" step="0.01">`;
    cells[4].innerHTML = `<input type="number" class="form-control form-control-sm" value="${cells[4].textContent.replace(/[^\d.-]/g, '')}" step="0.01">`;
    cells[5].innerHTML = `<input type="number" class="form-control form-control-sm" value="${cells[5].textContent.replace(/[^\d.-]/g, '')}" step="0.01">`;
    cells[7].innerHTML = `
        <select class="form-select form-select-sm">
            <option value="pending" ${cells[7].textContent.includes('Pending') ? 'selected' : ''}>Pending</option>
            <option value="paid" ${cells[7].textContent.includes('Paid') ? 'selected' : ''}>Paid</option>
            <option value="overdue" ${cells[7].textContent.includes('Overdue') ? 'selected' : ''}>Overdue</option>
        </select>
    `;
    cells[8].innerHTML = `<input type="date" class="form-control form-control-sm" value="${formatDateForInput(cells[8].textContent)}">`;
    
    // Update final amount calculation
    updateFinalAmount(row);
    
    // Show/hide buttons
    button.style.display = 'none';
    row.querySelector('.save-row-btn').style.display = 'inline-block';
    row.querySelector('.cancel-row-btn').style.display = 'inline-block';
    
    // Add event listeners for real-time calculation
    row.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('input', () => updateFinalAmount(row));
    });
}

// Save row changes
function saveRow(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Get updated values
    const dueDate = cells[2].querySelector('input').value;
    const baseAmount = parseFloat(cells[3].querySelector('input').value) || 0;
    const discountAmount = parseFloat(cells[4].querySelector('input').value) || 0;
    const sltLoanAmount = parseFloat(cells[5].querySelector('input').value) || 0;
    const status = cells[7].querySelector('select').value;
    const paidDate = cells[8].querySelector('input').value;
    
    // Update display
    cells[2].textContent = formatDate(dueDate);
    cells[3].textContent = formatCurrency(baseAmount);
    cells[4].textContent = formatCurrency(discountAmount);
    cells[5].textContent = formatCurrency(sltLoanAmount);
    cells[6].textContent = formatCurrency(baseAmount - discountAmount - sltLoanAmount);
    
    // Update status badge
    let statusBadge = '';
    switch(status) {
        case 'paid':
            statusBadge = '<span class="badge bg-success">Paid</span>';
            break;
        case 'overdue':
            statusBadge = '<span class="badge bg-danger">Overdue</span>';
            break;
        default:
            statusBadge = '<span class="badge bg-warning">Pending</span>';
    }
    cells[7].innerHTML = statusBadge;
    cells[8].textContent = paidDate ? formatDate(paidDate) : '-';
    
    // Show/hide buttons
    button.style.display = 'none';
    row.querySelector('.cancel-row-btn').style.display = 'none';
    row.querySelector('.edit-row-btn').style.display = 'inline-block';
    
    // Update row styling based on status
    row.classList.remove('table-danger', 'table-success');
    if (status === 'overdue') {
        row.classList.add('table-danger');
    } else if (status === 'paid') {
        row.classList.add('table-success');
    }
    
    showToast('Success', 'Row updated successfully!', 'bg-success');
}

// Cancel row edit
function cancelRowEdit(button) {
    const row = button.closest('tr');
    const cells = row.querySelectorAll('td');
    
    // Restore original values
    cells[2].textContent = row.dataset.originalDueDate;
    cells[3].textContent = formatCurrency(row.dataset.originalBaseAmount);
    cells[4].textContent = formatCurrency(row.dataset.originalDiscount);
    cells[5].textContent = formatCurrency(row.dataset.originalSltLoan);
    cells[6].textContent = formatCurrency(parseFloat(row.dataset.originalBaseAmount) - parseFloat(row.dataset.originalDiscount) - parseFloat(row.dataset.originalSltLoan));
    cells[7].innerHTML = row.dataset.originalStatus;
    cells[8].textContent = row.dataset.originalPaidDate;
    
    // Show/hide buttons
    button.style.display = 'none';
    row.querySelector('.save-row-btn').style.display = 'none';
    row.querySelector('.edit-row-btn').style.display = 'inline-block';
}

// Update final amount calculation
function updateFinalAmount(row) {
    const cells = row.querySelectorAll('td');
    const baseAmount = parseFloat(cells[3].querySelector('input').value) || 0;
    const discountAmount = parseFloat(cells[4].querySelector('input').value) || 0;
    const sltLoanAmount = parseFloat(cells[5].querySelector('input').value) || 0;
    const finalAmount = baseAmount - discountAmount - sltLoanAmount;
    
    cells[6].innerHTML = `<strong>${formatCurrency(finalAmount)}</strong>`;
}

// Save all changes
function saveInstallments() {
    const rows = document.querySelectorAll('#installmentsTableBody tr');
    const updatedData = [];
    
    rows.forEach(row => {
        const installmentData = {
            installment_id: parseInt(row.dataset.installmentId),
            due_date: row.querySelector('td:nth-child(3)').textContent,
            base_amount: parseFloat(row.querySelector('td:nth-child(4)').textContent.replace(/[^\d.-]/g, '')) || 0,
            discount_amount: parseFloat(row.querySelector('td:nth-child(5)').textContent.replace(/[^\d.-]/g, '')) || 0,
            slt_loan_amount: parseFloat(row.querySelector('td:nth-child(6)').textContent.replace(/[^\d.-]/g, '')) || 0,
            status: row.querySelector('td:nth-child(8)').textContent.toLowerCase().includes('paid') ? 'paid' : 
                   row.querySelector('td:nth-child(8)').textContent.toLowerCase().includes('overdue') ? 'overdue' : 'pending',
            paid_date: row.querySelector('td:nth-child(9)').textContent === '-' ? null : row.querySelector('td:nth-child(9)').textContent
        };
        updatedData.push(installmentData);
    });
    
    // Send data to backend
    console.log('Saving installments data:', updatedData);
    
    fetch('/api/payment-installments/update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            installments: updatedData
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showToast('Success', data.message || 'All changes saved successfully!', 'bg-success');
            toggleEditMode();
        } else {
            showToast('Error', data.message || 'Failed to save changes.', 'bg-danger');
        }
    })
    .catch(error => {
        console.error('Error saving installments:', error);
        showToast('Error', 'Failed to save changes. Please try again.', 'bg-danger');
    });
}

// Cancel all edits
function cancelEdit() {
    // Reload the original data
    if (originalInstallmentsData.length > 0) {
        displayInstallments(originalInstallmentsData);
    }
    toggleEditMode();
}

// Helper function to format date for input
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
    if (!courseId || !intakeId) {
        semesterSelect.innerHTML = '<option value="">Select Semester</option>';
        return;
    }
    fetch(`/api/semesters?course_id=${courseId}&intake_id=${intakeId}`)
        .then(r => r.json())
        .then(data => {
            semesterSelect.innerHTML = '<option value="">Select Semester</option>';
            data.semesters.forEach(s => {
                const selected = selectedSemesterId && (s.id == selectedSemesterId) ? 'selected' : '';
                semesterSelect.innerHTML += `<option value="${s.id}" ${selected}>${s.name}</option>`;
            });
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
                // Fill re-register form with latest holding registration
                if(res.holding_history && res.holding_history.length){
                    fillReRegisterForm(res.holding_history[0]);
                    // Also load payment plan data if we have registration info
                    const latestRegistration = res.holding_history[0];
                    if(latestRegistration.course_id) {
                        console.log('Loading payment plan for student:', res.student.student_id, 'course:', latestRegistration.course_id);
                        loadPaymentPlanData(res.student.student_id, latestRegistration.course_id);
                    } else {
                        console.log('No course_id found in registration');
                    }
                } else {
                    console.log('No holding history found');
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
            // Optional: Reload the page or update UI
            setTimeout(() => location.reload(), 2000);
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
                    loadPaymentPlanData(currentStudentData.student.student_id, latestRegistration.course_id);
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
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/repeat_students.blade.php ENDPATH**/ ?>