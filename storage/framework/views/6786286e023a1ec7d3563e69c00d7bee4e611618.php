<?php $__env->startSection('title', 'NEBULA | Late Payment Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Late Payment Management</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Search Filters -->
            <div class="mb-4">
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label fw-bold">Student NIC <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="student-nic" placeholder="Enter Student NIC" required>
                    </div>
                </div>
                <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course-select" required disabled>
                            <option value="" selected disabled>Select Course</option>
                        </select>
                    </div>
                </div>

            </div>

            <hr class="my-4">

            <!-- Student Information Section -->
            <div id="student-info" class="mt-4" style="display: none;">
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="ti ti-user me-2"></i>Student Information
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-user-circle text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 text-muted">Student Name</h6>
                                        <h5 class="mb-0" id="student-name-display">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-id text-info" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 text-muted">Student ID</h6>
                                        <h5 class="mb-0" id="student-id-display">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-book text-warning" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 text-muted">Course</h6>
                                        <h5 class="mb-0" id="course-name-display">-</h5>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ti ti-currency-dollar text-success" style="font-size: 2rem;"></i>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 text-muted">Total Course Fee</h6>
                                        <h5 class="mb-0" id="total-course-fee-display">-</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Summary Cards -->
            <div id="summary-cards" class="mt-4" style="display: none;">
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body text-center">
                                <h5>Total Installments</h5>
                                <h3 id="total-installments">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body text-center">
                                <h5>Paid Installments</h5>
                                <h3 id="paid-installments">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body text-center">
                                <h5>Late Installments</h5>
                                <h3 id="late-installments">0</h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-danger text-white">
                            <div class="card-body text-center">
                                <h5>Total Late Fees</h5>
                                <h3 id="total-late-fees">LKR 0</h3>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Plan Section -->
            <div id="payment-plan-section" class="mt-4" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-calendar me-2"></i>Payment Plan - Local Course Fee
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">Installment #</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-center">Days Late</th>
                                        <th class="text-center">Late Fee</th>
                                        <th class="text-center">Total Due</th>
                                    </tr>
                                </thead>
                                <tbody id="payment-plan-table-body">
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">No payment plan data available</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Paid Payment Details Section -->
            <div id="paid-payments-section" class="mt-4" style="display: none;">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="ti ti-check me-2"></i>Paid Local Course Fee Payment Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead class="table-dark">
                                    <tr>
                                        <th class="text-center">Payment Date</th>
                                        <th class="text-center">Amount</th>
                                        <th class="text-center">Payment Method</th>
                                        <th class="text-center">Receipt No</th>
                                        <th class="text-center">Installment #</th>
                                        <th class="text-center">Due Date</th>
                                        <th class="text-center">Days Late</th>
                                        <th class="text-center">Late Fee Paid</th>
                                        <th class="text-center">Uploaded Receipt</th>
                                    </tr>
                                </thead>
                                <tbody id="paid-payments-table-body">
                                    <tr>
                                        <td colspan="9" class="text-center text-muted py-4">
                                            <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">No paid payment data available</p>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Global variables
let currentStudentData = null;
let currentPaymentPlan = null;
let currentPaidPayments = null;

// Load late payment data
function loadLatePaymentData() {
    try {
        const studentNic = document.getElementById('student-nic').value.trim();
        const courseId = document.getElementById('course-select').value;

        if (!studentNic) {
            showToast('Error', 'Please enter Student NIC.', 'error');
            return;
        }

        if (!courseId) {
            showToast('Error', 'Please select a course.', 'error');
            return;
        }

        showSpinner(true);
        hideAllSections();

        // Load payment plan data
        loadPaymentPlan(studentNic, courseId);
        
        // Load paid payment details
        loadPaidPaymentDetails(studentNic, courseId);
    } catch (error) {
        console.error('Error in loadLatePaymentData:', error);
        showToast('Error', 'An unexpected error occurred: ' + error.message, 'error');
        showSpinner(false);
    }
}

// Load payment plan data
function loadPaymentPlan(studentNic, courseId) {
    console.log('Loading payment plan for:', { studentNic, courseId });
    
    const requestData = {
        student_nic: studentNic,
        course_id: parseInt(courseId) || courseId
    };
    
    fetch('/late-payment/get-payment-plan', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify(requestData)
    })
    .then(response => {
        console.log('Payment plan response status:', response.status);
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        console.log('Payment plan response data:', data);
        if (data.success) {
            currentStudentData = data.student;
            currentPaymentPlan = data.payment_plan;
            displayStudentInfo(data.student);
            displayPaymentPlan(data.payment_plan);
            showToast('Success', 'Payment plan loaded successfully.', 'success');
        } else {
            console.error('Payment plan API returned error:', data.message);
            showToast('Error', data.message || 'Failed to load payment plan.', 'error');
            // Still show the payment plan section even if no data
            displayPaymentPlan(null);
        }
    })
    .catch(error => {
        console.error('Error loading payment plan:', error);
        showToast('Error', 'An error occurred while loading payment plan: ' + error.message, 'error');
        // Still show the payment plan section even if there's an error
        displayPaymentPlan(null);
    })
    .finally(() => {
        showSpinner(false);
    });
}

// Load paid payment details
function loadPaidPaymentDetails(studentNic, courseId) {
    console.log('Loading paid payments for:', { studentNic, courseId });
    
    fetch('/late-payment/get-paid-payments', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            student_nic: studentNic,
            course_id: parseInt(courseId) || courseId
        })
    })
    .then(response => {
        console.log('Paid payments response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Paid payments response data:', data);
        if (data.success) {
            currentPaidPayments = data.paid_payments;
            displayPaidPayments(data.paid_payments);
        } else {
            console.log('No paid payments found or error:', data.message);
            // Still show the paid payments section even if no data
            displayPaidPayments([]);
        }
    })
    .catch(error => {
        console.error('Error loading paid payments:', error);
        // Still show the paid payments section even if there's an error
        displayPaidPayments([]);
    });
}

// Display student information
function displayStudentInfo(student) {
    
    document.getElementById('student-name-display').textContent = student.student_name || 'N/A';
    document.getElementById('student-id-display').textContent = student.student_id || 'N/A';
    document.getElementById('course-name-display').textContent = student.course_name || 'N/A';
    document.getElementById('total-course-fee-display').textContent = 'LKR ' + (student.total_amount || 0).toLocaleString();
    
    const studentInfoSection = document.getElementById('student-info');
    studentInfoSection.style.display = 'block';
}

// Display payment plan
function displayPaymentPlan(paymentPlan) {
    console.log('Displaying payment plan:', paymentPlan);
    
    const tbody = document.getElementById('payment-plan-table-body');
    tbody.innerHTML = '';

    if (!paymentPlan || !paymentPlan.installments || paymentPlan.installments.length === 0) {
        console.log('No payment plan data available, showing empty table');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted py-4">
                    <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No payment plan data available</p>
                </td>
            </tr>
        `;
        // Still show the section even if no data
        const paymentPlanSection = document.getElementById('payment-plan-section');
        paymentPlanSection.style.display = 'block';
        
        // Show summary cards with zero values
        showSummaryCards(0, 0, 0, 0);
        return;
    }

    console.log('Payment plan has installments:', paymentPlan.installments.length);
    
    // Calculate summary statistics
    let totalInstallments = paymentPlan.installments.length;
    let paidInstallments = paymentPlan.installments.filter(i => i.status === 'paid').length;
    let lateInstallments = paymentPlan.installments.filter(i => i.is_late).length;
    let totalLateFees = paymentPlan.installments.reduce((sum, i) => sum + (i.late_fee || 0), 0);
    
    // Update summary cards
    showSummaryCards(totalInstallments, paidInstallments, lateInstallments, totalLateFees);
    
    paymentPlan.installments.forEach(installment => {
        const row = `
            <tr>
                <td class="text-center">${installment.installment_number}</td>
                <td class="text-center">${new Date(installment.due_date).toLocaleDateString()}</td>
                <td class="text-center fw-bold">LKR ${parseFloat(installment.amount).toLocaleString()}</td>
                <td class="text-center">
                    <span class="badge bg-${installment.status === 'paid' ? 'success' : 'warning'}">
                        ${installment.status}
                    </span>
                </td>
                <td class="text-center">
                    ${installment.is_late ? 
                        `<span class="badge bg-danger">${installment.days_late} days</span>` : 
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td class="text-center">
                    ${installment.late_fee > 0 ? 
                        `<span class="text-danger fw-bold">LKR ${parseFloat(installment.late_fee).toLocaleString()}</span>` : 
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td class="text-center fw-bold">
                    LKR ${parseFloat(installment.total_due).toLocaleString()}
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    const paymentPlanSection = document.getElementById('payment-plan-section');
    console.log('Showing payment plan section');
    paymentPlanSection.style.display = 'block';
}

// Show summary cards with statistics
function showSummaryCards(totalInstallments, paidInstallments, lateInstallments, totalLateFees) {
    document.getElementById('total-installments').textContent = totalInstallments;
    document.getElementById('paid-installments').textContent = paidInstallments;
    document.getElementById('late-installments').textContent = lateInstallments;
    document.getElementById('total-late-fees').textContent = 'LKR ' + totalLateFees.toLocaleString();
    
    const summaryCards = document.getElementById('summary-cards');
    summaryCards.style.display = 'block';
}

// Display paid payments
function displayPaidPayments(paidPayments) {
    console.log('Displaying paid payments:', paidPayments);
    
    const tbody = document.getElementById('paid-payments-table-body');
    tbody.innerHTML = '';

    if (!paidPayments || paidPayments.length === 0) {
        console.log('No paid payment data available, showing empty table');
        tbody.innerHTML = `
            <tr>
                <td colspan="9" class="text-center text-muted py-4">
                    <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No paid payment data available</p>
                </td>
            </tr>
        `;
        // Still show the section even if no data
        const paidPaymentsSection = document.getElementById('paid-payments-section');
        console.log('Showing paid payments section (no data)');
        paidPaymentsSection.style.display = 'block';
        return;
    }

    console.log('Paid payments count:', paidPayments.length);
    
    paidPayments.forEach(payment => {
        const row = `
            <tr>
                <td class="text-center">${new Date(payment.payment_date).toLocaleDateString()}</td>
                <td class="text-center fw-bold">LKR ${parseFloat(payment.amount).toLocaleString()}</td>
                <td class="text-center">${payment.payment_method || 'N/A'}</td>
                <td class="text-center">${payment.receipt_no || 'N/A'}</td>
                <td class="text-center">${payment.installment_number || 'N/A'}</td>
                <td class="text-center">${payment.due_date ? new Date(payment.due_date).toLocaleDateString() : 'N/A'}</td>
                <td class="text-center">
                    ${payment.days_late > 0 ? 
                        `<span class="badge bg-warning">${payment.days_late} days</span>` : 
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td class="text-center">
                    ${payment.late_fee_paid > 0 ? 
                        `<span class="text-danger fw-bold">LKR ${parseFloat(payment.late_fee_paid).toLocaleString()}</span>` : 
                        '<span class="text-muted">-</span>'
                    }
                </td>
                <td class="text-center">
                    ${payment.paid_slip_path ? 
                        `<a href="/storage/${payment.paid_slip_path}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="ti ti-download me-1"></i>View
                        </a>` : 
                        '<span class="text-muted">Not uploaded</span>'
                    }
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });

    const paidPaymentsSection = document.getElementById('paid-payments-section');
    console.log('Showing paid payments section');
    paidPaymentsSection.style.display = 'block';
}

// Show spinner
function showSpinner(show) {
    document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
}

// Hide all sections
function hideAllSections() {
    document.getElementById('student-info').style.display = 'none';
    document.getElementById('payment-plan-section').style.display = 'none';
    document.getElementById('paid-payments-section').style.display = 'none';
    document.getElementById('summary-cards').style.display = 'none'; // Hide summary cards
}

// Show toast notification
function showToast(title, message, type = 'info') {
    const toastContainer = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    toast.className = `toast ${type} slide-in`;
    toast.innerHTML = `
        <div class="toast-icon">
            <i class="ti ti-${type === 'success' ? 'check' : type === 'error' ? 'x' : type === 'warning' ? 'alert-triangle' : 'info'}"></i>
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="this.parentElement.remove()">
            <i class="ti ti-x"></i>
        </button>
    `;
    
    toastContainer.appendChild(toast);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (toast.parentElement) {
            toast.remove();
        }
    }, 5000);
}

// Load student courses by NIC
function loadStudentCourses(studentNic) {
    console.log('loadStudentCourses called with NIC:', studentNic);
    
    if (!studentNic) {
        console.log('No NIC provided, resetting course select');
        resetCourseSelect();
        return;
    }

    console.log('Fetching courses for student NIC:', studentNic);
    
    // Show loading state
    const courseSelect = document.getElementById('course-select');
    courseSelect.innerHTML = '<option value="" selected disabled>Loading courses...</option>';
    courseSelect.disabled = true;
    
    fetch('/late-payment/get-student-courses', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            student_nic: studentNic
        })
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Courses API response:', data);
        if (data.success) {
            console.log('Courses found:', data.courses);
            populateCourseSelect(data.courses);
        } else {
            console.log('No courses found for student:', data.message);
            resetCourseSelect();
        }
    })
    .catch(error => {
        console.error('Error loading student courses:', error);
        resetCourseSelect();
    });
}

// Populate course select dropdown
function populateCourseSelect(courses) {
    console.log('populateCourseSelect called with courses:', courses);
    const courseSelect = document.getElementById('course-select');
    courseSelect.innerHTML = '<option value="" selected disabled>Select Course</option>';
    
    if (courses && courses.length > 0) {
        console.log('Adding', courses.length, 'courses to dropdown');
        courses.forEach(course => {
            const option = document.createElement('option');
            option.value = course.course_id;
            option.textContent = `${course.course_name} (Registered: ${course.registration_date})`;
            courseSelect.appendChild(option);
        });
        courseSelect.disabled = false;
        console.log('Course select enabled');
    } else {
        console.log('No courses available, disabling course select');
        courseSelect.innerHTML = '<option value="" selected disabled>No courses found for this student</option>';
        courseSelect.disabled = true;
    }
}

// Reset course select dropdown
function resetCourseSelect() {
    const courseSelect = document.getElementById('course-select');
    courseSelect.innerHTML = '<option value="" selected disabled>Select Course</option>';
    courseSelect.disabled = true;
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, setting up event listeners');
    
    const studentNicInput = document.getElementById('student-nic');
    const courseSelect = document.getElementById('course-select');
    
    // Enter key on student NIC field
    studentNicInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            loadLatePaymentData();
        }
    });

    // Course select change event
    courseSelect.addEventListener('change', function() {
        if (this.value) {
            loadLatePaymentData();
        }
    });

    // Student NIC field input event for course filtering with debounce
    let debounceTimer;
    studentNicInput.addEventListener('input', function() {
        const studentNic = this.value.trim();
        console.log('Student NIC input detected:', studentNic);
        
        // Clear previous timer
        clearTimeout(debounceTimer);
        
        // Set new timer to delay API call
        debounceTimer = setTimeout(() => {
            loadStudentCourses(studentNic);
        }, 500); // 500ms delay
    });
    

    
    console.log('Event listeners set up successfully');
});
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/late_payment.blade.php ENDPATH**/ ?>