@extends('inc.app')

@section('title', 'Course / Intake Change')

@section('content')
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4">
                <i class="ti ti-refresh"></i> Course / Intake Change
            </h3>

            <!-- Alert Messages -->
            <div id="alert-container"></div>

            <!-- Search Student -->
            <div class="card mb-4 border-primary">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">
                        <i class="ti ti-search"></i> Search Student
                    </h5>
                </div>
                <div class="card-body">
                    <form id="searchForm" class="mb-0">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <label for="nic" class="form-label">Enter Student NIC</label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="ti ti-id"></i>
                                    </span>
                                    <input type="text" class="form-control" id="nic" 
                                           placeholder="Enter Student NIC" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary w-100" type="submit" id="searchBtn">
                                    <i class="ti ti-search me-2"></i> Search
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Student Details Section -->
            <div id="student-section" class="d-none">
                <div class="card mb-4 border-info">
                    <div class="card-header bg-info text-white d-flex align-items-center justify-content-between">
                        <h5 class="mb-0">
                            <i class="ti ti-user"></i> Student Details
                        </h5>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-light btn-sm" id="logsBtn" disabled>
                                <i class="ti ti-list-details me-1"></i> View Logs
                            </button>
                            <button type="button" class="btn btn-light btn-sm" id="remarksBtn" disabled>
                                <i class="ti ti-notes me-1"></i> Remarks
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Name:</strong> <span id="s_name" class="text-primary"></span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Student ID:</strong> <span id="s_id" class="text-primary"></span></p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>NIC:</strong> <span id="s_nic" class="text-primary"></span></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card border-secondary">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="ti ti-book"></i> Current Course Registrations
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <!-- Loading Spinner -->
                        <div id="table-loader" class="d-none text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2 text-muted">Refreshing course registrations...</p>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0" id="reg_table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course</th>
                                        <th>Intake/Batch</th>
                                        <th>Start Date</th>
                                        <th>Status</th>
                                        <th>Payment Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Rows will be populated by JavaScript -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course + Intake Selection Modal -->
            <div class="modal fade" id="changeCourseModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-warning text-dark">
                            <h5 class="modal-title">
                                <i class="ti ti-refresh me-2"></i> Change Course & Intake
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Payment Warning -->
                            <div id="paymentWarning" class="alert alert-warning d-none">
                                <i class="ti ti-alert-triangle me-2"></i>
                                <strong>Payment Records Found!</strong>
                                <span id="paymentWarningText"></span>
                            </div>

                            <!-- Year Warning -->
                            <div id="yearWarning" class="alert alert-danger d-none">
                                <i class="ti ti-alert-octagon me-2"></i>
                                <strong>Course Change Not Allowed!</strong>
                                <span id="yearWarningText"></span>
                            </div>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Select New Course</label>
                                    <select id="course_select" class="form-select" disabled>
                                        <option value="">Select Course</option>
                                    </select>
                                    <div class="form-text" id="courseError" style="color: red; display: none;"></div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Select New Intake</label>
                                    <select id="new_intake" class="form-select" disabled>
                                        <option value="">Select course first</option>
                                    </select>
                                    <div class="form-text" id="intakeError" style="color: red; display: none;"></div>
                                </div>
                            </div>

                            <div class="alert alert-info mt-3 mb-0">
                                <i class="ti ti-info-circle me-2"></i>
                                <small>Select the <strong>same course</strong> if you only need to move the student to a different intake/batch.</small>
                            </div>

                            <div class="mt-4">
                                <div class="card border-primary">
                                    <div class="card-header bg-primary text-white py-2">
                                        <small>New Course Registration ID</small>
                                    </div>
                                    <div class="card-body p-3">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="ti ti-id-badge"></i>
                                            </span>
                                            <input type="text" id="generated_id" class="form-control" readonly placeholder="Select intake to generate">
                                            <button class="btn btn-outline-secondary" type="button" id="generateBtn" disabled>
                                                <i class="ti ti-refresh"></i> Generate
                                            </button>
                                        </div>
                                        <div class="form-text" id="idError" style="color: red; display: none;"></div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="alert alert-info">
                                    <i class="ti ti-info-circle me-2"></i>
                                    <small>
                                        <strong>Note:</strong> Old course payment records are archived. The total amount already paid is carried forward and settled from installment 1 of the new course payment plan.
                                        The student continues with that new payment plan for the remaining balance.
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-2"></i> Cancel
                            </button>
                            <button type="button" class="btn btn-success" id="submitBtn" disabled>
                                <i class="ti ti-check me-2"></i> Confirm Change
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment Summary Modal -->
            <div class="modal fade" id="paymentSummaryModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header bg-success text-white">
                            <h5 class="modal-title">
                                <i class="ti ti-credit-card me-2"></i> Payment Summary
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="paymentSummaryContent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading payment information...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-2"></i> Close
                            </button>
                            <a href="{{ route('payment.index') }}" class="btn btn-primary" target="_blank">
                                <i class="ti ti-credit-card me-2"></i> Student Payment Plans
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payment History Preview Modal -->
            <div class="modal fade" id="paymentHistoryPreviewModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white">
                            <h5 class="modal-title">
                                <i class="ti ti-history me-2"></i> Current Payment History
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="paymentHistoryPreviewContent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading payment history...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-2"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cancelled Payments Remarks Modal -->
            <div class="modal fade" id="cancelledPaymentsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header bg-secondary text-white">
                            <h5 class="modal-title">
                                <i class="ti ti-notes me-2"></i> Cancelled Payments Remarks
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="cancelledPaymentsContent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading cancelled payments...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-2"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Course Change Logs Modal -->
            <div class="modal fade" id="courseChangeLogsModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title">
                                <i class="ti ti-list-details me-2"></i> Course Change History
                            </h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div id="courseChangeLogsContent">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                    <p class="mt-2">Loading course change history...</p>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                <i class="ti ti-x me-2"></i> Close
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Plan Panel -->
<div id="paymentPlanPanel" class="container mt-4 d-none">
    <div class="card border-success shadow-sm">
        <div class="card-header bg-success text-white d-flex align-items-center justify-content-between">
            <h6 class="mb-0">
                <i class="ti ti-credit-card me-2"></i> Payment Summary & Student Payment Plans
            </h6>
            <button type="button" class="btn btn-light btn-sm" id="refreshPaymentPlanBtn">
                <i class="ti ti-refresh me-1"></i> Refresh
            </button>
        </div>
        <div class="card-body">
            <p class="mb-3">
                <strong>Last paid amount:</strong>
                <span id="lastPaidAmount" class="text-primary">LKR 0.00</span>
            </p>
            <iframe id="paymentPlanFrame"
                    title="Student Payment Plans"
                    style="width: 100%; height: 650px; border: 1px solid #dee2e6; border-radius: 6px;"
                    loading="lazy"></iframe>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1090">
    <div id="successToast" class="toast align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="ti ti-check me-2"></i>
                <span id="successMessage">Course / intake change completed successfully!</span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
    <div id="errorToast" class="toast align-items-center text-white bg-danger border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body">
                <i class="ti ti-alert-triangle me-2"></i>
                <span id="errorMessage"></span>
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script nonce="{{ $cspNonce }}">
// Global variables
let selectedRegistration = null;
let currentIntakeId = null;
let currentCourseId = null;
let courseStartDate = null;
let searchedNIC = null;
let studentId = null;
let paymentInfo = null;

// Show alert message
function showAlert(message, type = 'info', duration = 5000) {
    const alertContainer = document.getElementById('alert-container');
    const alertId = 'alert-' + Date.now();
    
    const alertHTML = `
        <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show" role="alert">
            <i class="ti ti-${type === 'success' ? 'check' : type === 'warning' ? 'alert-triangle' : type === 'danger' ? 'alert-circle' : 'info-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    alertContainer.innerHTML = alertHTML;
    
    // Auto remove after duration
    if (duration > 0) {
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, duration);
    }
}

// Show toast message
function showToast(message, type = 'success') {
    let toastEl;
    
    if (type === 'success') {
        toastEl = document.getElementById('successToast');
        const toastMessage = document.getElementById('successMessage');
        toastMessage.textContent = message;
    } else {
        toastEl = document.getElementById('errorToast');
        const toastMessage = document.getElementById('errorMessage');
        toastMessage.textContent = message;
    }
    
    const toast = bootstrap.Toast.getOrCreateInstance(toastEl);
    toast.show();
}

function getPaymentTypeLabel(type) {
    const labels = {
        course_fee: 'Course Fee',
        franchise_fee: 'Franchise Fee',
        registration_fee: 'Registration Fee',
        other: 'Other'
    };

    return labels[type] || type || '-';
}

function showPaymentHistoryPreview(data) {
    const content = document.getElementById('paymentHistoryPreviewContent');
    if (!content) {
        return;
    }

    const history = Array.isArray(data?.payment_history) ? data.payment_history : [];
    const totalPaid = Number(data?.total_paid_amount || 0);

    if (history.length === 0) {
        content.innerHTML = `
            <div class="alert alert-info mb-0">
                <i class="ti ti-info-circle me-2"></i>
                No payment records found for this registration.
            </div>
        `;
    } else {
        const rows = history.map(item => {
            const status = (item.status || 'pending').toLowerCase();
            const statusClass = status === 'paid' ? 'success' : (status === 'cancelled' ? 'secondary' : 'warning');

            return `
                <tr>
                    <td>${item.payment_date || '-'}</td>
                    <td>${getPaymentTypeLabel(item.payment_type)}</td>
                    <td>${item.installment_number ?? '-'}</td>
                    <td>${item.receipt_no || '-'}</td>
                    <td>${Number(item.total_fee || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td>${Number(item.paid_amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td>${Number(item.remaining_amount || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</td>
                    <td><span class="badge bg-${statusClass}">${item.status || '-'}</span></td>
                    <td>${item.payment_method || '-'}</td>
                </tr>
            `;
        }).join('');

        content.innerHTML = `
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Payment Records (${history.length})</h6>
                <span class="badge bg-primary p-2">Total Paid: LKR ${totalPaid.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-bordered table-sm align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Payment Date</th>
                            <th>Type</th>
                            <th>Installment</th>
                            <th>Receipt</th>
                            <th>Total Fee</th>
                            <th>Paid</th>
                            <th>Remaining</th>
                            <th>Status</th>
                            <th>Method</th>
                        </tr>
                    </thead>
                    <tbody>${rows}</tbody>
                </table>
            </div>
        `;
    }

    const previewModal = new bootstrap.Modal(document.getElementById('paymentHistoryPreviewModal'));
    previewModal.show();
}

function showPaymentPlanPanel(totalPaidAmount) {
    const panel = document.getElementById('paymentPlanPanel');
    const amountEl = document.getElementById('lastPaidAmount');
    const frame = document.getElementById('paymentPlanFrame');

    const amount = Number(totalPaidAmount || 0);
    amountEl.textContent = `LKR ${amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

    if (!frame.src) {
        frame.src = "{{ route('payment.index') }}";
    }

    panel.classList.remove('d-none');
}

function refreshPaymentPlanFrame() {
    const frame = document.getElementById('paymentPlanFrame');
    if (frame.src) {
        frame.src = frame.src;
    } else {
        frame.src = "{{ route('payment.index') }}";
    }
}

async function showCancelledPayments() {
    if (!studentId) {
        showAlert('Please search a student first', 'warning');
        return;
    }

    const content = document.getElementById('cancelledPaymentsContent');
    content.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading cancelled payments...</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('cancelledPaymentsModal'));
    modal.show();

    try {
        const response = await fetch(`{{ url('registration/course-change/cancelled-payments') }}/${studentId}`);
        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error(data.message || 'Failed to load cancelled payments');
        }

        if (!data.payments || data.payments.length === 0) {
            content.innerHTML = `
                <div class="text-center text-muted py-4">
                    <i class="ti ti-info-circle me-2"></i>No cancelled payments found.
                </div>
            `;
            return;
        }

        let rowsHtml = '';
        data.payments.forEach(payment => {
            const isCancelled = isCancelledByRemarks(payment.remarks || '');
            rowsHtml += `
                <tr>
                    <td>${payment.course_registration_id ?? '-'}</td>
                    <td>${payment.remarks ?? '-'}</td>
                    <td>${payment.updated_at ?? '-'}</td>
                    <td class="text-center">
                        <input type="checkbox"
                               ${isCancelled ? 'checked' : ''}
                               onchange="toggleCancelledPaymentStatus(${payment.id}, this.checked)">
                    </td>
                </tr>
            `;
        });

        content.innerHTML = `
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Registration ID</th>
                            <th>Remarks</th>
                            <th>Updated</th>
                            <th>Cancel</th>
                        </tr>
                    </thead>
                    <tbody>${rowsHtml}</tbody>
                </table>
            </div>
        `;
    } catch (error) {
        console.error('Cancelled payments error:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                Failed to load cancelled payments: ${error.message}
            </div>
        `;
    }
}

async function showChangeLogs() {
    if (!studentId) {
        showAlert('Please search a student first', 'warning');
        return;
    }

    const content = document.getElementById('courseChangeLogsContent');
    content.innerHTML = `
        <div class="text-center py-3">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading course change history...</p>
        </div>
    `;

    const modal = new bootstrap.Modal(document.getElementById('courseChangeLogsModal'));
    modal.show();

    try {
        const response = await fetch(`{{ url('registration/course-change/change-history') }}/${studentId}`);
        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error(data.message || 'Failed to load history');
        }

        let logsHtml = '';
        if (data.logs && data.logs.length > 0) {
            data.logs.forEach(log => {
                logsHtml += `
                    <tr>
                        <td>${log.changed_at ?? '-'}</td>
                        <td>${log.old_course_id ?? '-'}</td>
                        <td>${log.new_course_id ?? '-'}</td>
                        <td>${log.old_intake_id ?? '-'}</td>
                        <td>${log.new_intake_id ?? '-'}</td>
                        <td>${log.total_paid_amount ?? '-'}</td>
                        <td>${log.changed_by_name ?? '-'}</td>
                        <td>${log.remarks ?? '-'}</td>
                    </tr>
                `;
            });
        } else {
            logsHtml = `
                <tr>
                    <td colspan="8" class="text-center text-muted">No course change logs found.</td>
                </tr>
            `;
        }

        let paymentsHtml = '';
        if (data.payments && data.payments.length > 0) {
            data.payments.forEach(payment => {
                paymentsHtml += `
                    <tr>
                        <td>${payment.created_at ?? '-'}</td>
                        <td>${payment.old_course_id ?? '-'}</td>
                        <td>${payment.old_intake_id ?? '-'}</td>
                        <td>${payment.old_payment_plan_id ?? '-'}</td>
                        <td>${payment.total_paid_amount ?? '-'}</td>
                        <td>${payment.remarks ?? '-'}</td>
                    </tr>
                `;
            });
        } else {
            paymentsHtml = `
                <tr>
                    <td colspan="6" class="text-center text-muted">No course change payments found.</td>
                </tr>
            `;
        }

        content.innerHTML = `
            <div class="mb-4">
                <h6 class="text-primary">Course Change Logs</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Changed At</th>
                                <th>Old Course</th>
                                <th>New Course</th>
                                <th>Old Intake</th>
                                <th>New Intake</th>
                                <th>Total Paid</th>
                                <th>Changed By</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>${logsHtml}</tbody>
                    </table>
                </div>
            </div>
            <div>
                <h6 class="text-primary">Course Change Payments</h6>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Created At</th>
                                <th>Old Course</th>
                                <th>Old Intake</th>
                                <th>Old Plan</th>
                                <th>Total Paid</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>${paymentsHtml}</tbody>
                    </table>
                </div>
            </div>
        `;
    } catch (error) {
        console.error('Course change history error:', error);
        content.innerHTML = `
            <div class="alert alert-danger">
                Failed to load course change history: ${error.message}
            </div>
        `;
    }
}

function isCancelledByRemarks(remarks) {
    const text = remarks || '';
    const lastUpdated = text.lastIndexOf('Payment Updated');
    const lastPending = text.lastIndexOf('Pending the Payment Update');

    if (lastUpdated === -1 && lastPending === -1) {
        return true;
    }

    return lastUpdated > lastPending;
}

async function toggleCancelledPaymentStatus(paymentId, isChecked) {
    try {
        const response = await fetch(`{{ url('registration/course-change/cancelled-payments') }}/${paymentId}/status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                status: isChecked ? 'cancelled' : 'pending'
            })
        });

        const data = await response.json();
        if (data.status !== 'success') {
            throw new Error(data.message || 'Failed to update payment');
        }

        await showCancelledPayments();
    } catch (error) {
        console.error('Update payment status error:', error);
        showAlert('Failed to update payment status: ' + error.message, 'danger');
        await showCancelledPayments();
    }
}

// Clear errors
function clearErrors() {
    ['courseError', 'intakeError', 'idError'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.style.display = 'none';
            el.textContent = '';
        }
    });
}

// Show error
function showError(fieldId, message) {
    const el = document.getElementById(fieldId);
    if (el) {
        el.textContent = message;
        el.style.display = 'block';
    }
}

// Generate color from string
function stringToColor(str) {
    let hash = 0;
    for (let i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    let color = '#';
    for (let i = 0; i < 3; i++) {
        let value = (hash >> (i * 8)) & 255;
        color += ('00' + value.toString(16)).slice(-2);
    }
    return color;
}

// ========================= SEARCH STUDENT =========================
async function searchStudent(nic) {
    const searchBtn = document.getElementById('searchBtn');
    const originalText = searchBtn.innerHTML;
    
    try {
        searchBtn.innerHTML = '<i class="ti ti-loader spinner me-2"></i>Searching...';
        searchBtn.disabled = true;
        
        const response = await fetch("{{ route('course.change.find.student') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ nic: nic })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            searchedNIC = nic;
            studentId = data.student.student_id;
            document.getElementById('remarksBtn').disabled = false;
            document.getElementById('logsBtn').disabled = false;
            
            // Show student section
            document.getElementById('student-section').classList.remove('d-none');
            
            // Update student details
            document.getElementById('s_name').textContent = data.student.full_name;
            document.getElementById('s_id').textContent = data.student.student_id;
            document.getElementById('s_nic').textContent = data.student.id_value;
            
            // Populate registrations table
            const tbody = document.querySelector('#reg_table tbody');
            tbody.innerHTML = '';
            
            if (data.registrations.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">
                            <i class="ti ti-book-off me-2"></i>No course registrations found
                        </td>
                    </tr>
                `;
            } else {
                data.registrations.forEach(reg => {
                    const startDate = new Date(reg.course_start_date);
                    const deadline = new Date(startDate);
                    deadline.setFullYear(deadline.getFullYear() + 1);
                    const isAllowed = new Date() < deadline;
                    const statusClass = isAllowed ? 'badge bg-success' : 'badge bg-warning';
                    const statusText = isAllowed ? 'Change Allowed' : 'Restricted';
                    
                    const actionButton = isAllowed 
                        ? `<button class="btn btn-warning btn-sm btn-change-modal" 
                                  data-reg-id="${reg.id}" 
                                  data-course-id="${reg.course_id}" 
                                  data-intake-id="${reg.intake_id}" 
                                  data-start-date="${reg.course_start_date}">
                              <i class="ti ti-refresh me-1"></i> Change
                           </button>`
                        : `<span class="text-muted">Not Allowed</span>`;
                    
                    const row = `
                        <tr>
                            <td>
                                <strong>${reg.course?.course_name || 'N/A'}</strong><br>
                                <small class="text-muted">${reg.course?.location || ''} | ${reg.course?.course_type || ''}</small>
                            </td>
                            <td>${reg.intake?.batch || 'N/A'}</td>
                            <td>${reg.course_start_date}</td>
                            <td><span class="${statusClass}">${statusText}</span></td>
                            <td>
                                <button class="btn btn-info btn-sm btn-check-payment" 
                                        data-reg-id="${reg.id}">
                                    <i class="ti ti-credit-card me-1"></i> Check
                                </button>
                            </td>
                            <td>${actionButton}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            }
            
        } else {
            showAlert(data.message || 'Student not found', 'danger');
            document.getElementById('remarksBtn').disabled = true;
            document.getElementById('logsBtn').disabled = true;
        }
        
    } catch (error) {
        console.error('Search error:', error);
        showAlert('Error searching student: ' + error.message, 'danger');
    } finally {
        searchBtn.innerHTML = originalText;
        searchBtn.disabled = false;
    }
}

// Check payment status
// Check payment status
async function checkPaymentStatus(registrationId) {
    try {
        console.log('Checking payment status for registration:', registrationId);
        
        showAlert('Checking payment status...', 'info', 2000);
        
        // Debug: Show what we're sending
        console.log('Sending request to:', "{{ route('course.change.check.payment') }}");
        console.log('CSRF Token exists:', !!document.querySelector('meta[name="csrf-token"]')?.content);
        
        const response = await fetch("{{ route('course.change.check.payment') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}'
            },
            body: JSON.stringify({ 
                registration_id: registrationId,
                _token: '{{ csrf_token() }}'
            })
        });
        
        console.log('Response status:', response.status, response.statusText);
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text.substring(0, 500));
            throw new Error(`Server returned ${response.status}: ${response.statusText}. Response: ${text.substring(0, 200)}`);
        }
        
        const data = await response.json();
        console.log('Payment check response:', data);
        
        if (data.status === 'success') {
            let message, alertType;
            if (!data.has_payment_plan) {
                message = 'No active payment plan found for this course.';
                alertType = 'info';
            } else if (!data.has_payments) {
                message = 'Payment plan exists but no payments have been made yet.';
                alertType = 'info';
            } else {
                message = `Payment plan found with LKR ${data.total_paid_amount.toLocaleString()} paid.`;
                alertType = 'warning';
            }

            showPaymentHistoryPreview(data);
            
            showAlert(message, alertType);
        } else {
            console.error('Payment check error from server:', data);
            showAlert(data.message || 'Error checking payment status', 'danger');
        }
        
    } catch (error) {
        console.error('Payment check error:', error);
        console.error('Error details:', {
            name: error.name,
            message: error.message,
            stack: error.stack
        });
        showAlert('Error checking payment status: ' + error.message, 'danger');
    }
}
// Show change modal
async function showChangeModal(regId, courseId, intakeId, startDate) {
    selectedRegistration = regId;
    currentCourseId = courseId;
    currentIntakeId = intakeId;
    courseStartDate = startDate;
    
    // Clear previous errors and reset modal
    clearErrors();
    document.getElementById('paymentWarning').classList.add('d-none');
    document.getElementById('yearWarning').classList.add('d-none');
    document.getElementById('course_select').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('new_intake').innerHTML = '<option value="">Loading...</option>';
    document.getElementById('generated_id').value = '';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('generateBtn').disabled = true;
    
    // Check if within 1 year from course start date
    const referenceDate = new Date(startDate);
    const deadline = new Date(referenceDate);
    deadline.setFullYear(deadline.getFullYear() + 1);

    if (new Date() >= deadline) {
        const warningDiv = document.getElementById('yearWarning');
        const warningText = document.getElementById('yearWarningText');
        
        warningText.textContent = `Course start date is ${referenceDate.toISOString().split('T')[0]}. Course changes are only allowed within 1 year from the start date.`;
        warningDiv.classList.remove('d-none');
        
        // Disable modal buttons
        document.getElementById('submitBtn').disabled = true;
        
        // Show modal anyway for information
        const modal = new bootstrap.Modal(document.getElementById('changeCourseModal'));
        modal.show();
        return;
    }
    
    // Check payment status
    try {
        const response = await fetch("{{ route('course.change.check.payment') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ registration_id: regId })
        });
        
        paymentInfo = await response.json();
        
        // Show payment warning if payments exist
        const warningDiv = document.getElementById('paymentWarning');
        const warningText = document.getElementById('paymentWarningText');
        
        if (paymentInfo.status === 'success' && paymentInfo.has_payments) {
            warningText.textContent = `Student has paid LKR ${paymentInfo.total_paid_amount.toLocaleString()} for this course. All payment records will be cancelled.`;
            warningDiv.classList.remove('d-none');
        } else {
            warningDiv.classList.add('d-none');
        }
        
    } catch (error) {
        console.error('Payment check error:', error);
        warningDiv.classList.add('d-none');
    }
    
    // Load courses
    await loadCourses();
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('changeCourseModal'));
    modal.show();
}

// Load courses
async function loadCourses() {
    const courseSelect = document.getElementById('course_select');
    courseSelect.innerHTML = '<option value="">Loading courses...</option>';
    courseSelect.disabled = true;
    clearErrors();
    
    try {
        const response = await fetch("{{ route('course.change.courses') }}");
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const data = await response.json();
        
        console.log('Courses response:', data); // Debug log
        
        if (data.status !== 'success' || !data.courses) {
            throw new Error(data.message || 'Invalid response from server');
        }
        
        courseSelect.innerHTML = '<option value="">Select New Course</option>';
        
        if (data.courses.length === 0) {
            courseSelect.innerHTML = '<option value="">No courses available</option>';
            return;
        }
        
        // Group by location
        const coursesByLocation = {};
        data.courses.forEach(course => {
            if (!coursesByLocation[course.location]) {
                coursesByLocation[course.location] = [];
            }
            coursesByLocation[course.location].push(course);
        });
        
        // Populate with location groups
        Object.keys(coursesByLocation).sort().forEach(location => {
            const color = stringToColor(location);
            
            // Location header - create option element and set style via JavaScript
            const headerOption = document.createElement('option');
            headerOption.disabled = true;
            headerOption.style.fontWeight = 'bold';
            headerOption.style.color = color;
            headerOption.style.padding = '8px';
            headerOption.style.borderTop = `2px solid ${color}`;
            headerOption.style.background = `linear-gradient(to right, ${color}20, transparent)`;
            headerOption.style.cursor = 'default';
            headerOption.textContent = `📍 ${location.toUpperCase()}`;
            courseSelect.appendChild(headerOption);
            
            // Courses in this location
            coursesByLocation[location].forEach(course => {
                const isCurrentCourse = String(course.course_id) === String(currentCourseId);
                const courseOption = document.createElement('option');
                courseOption.value = course.course_id;
                courseOption.dataset.current = isCurrentCourse ? 'true' : 'false';
                courseOption.textContent = `${isCurrentCourse ? '⮕ Current: ' : ''}${course.course_type} - ${course.course_name}`;
                courseSelect.appendChild(courseOption);
            });
        });
        
        courseSelect.disabled = false;

        if (currentCourseId) {
            courseSelect.value = String(currentCourseId);
            if (courseSelect.value === String(currentCourseId)) {
                courseSelect.dispatchEvent(new Event('change'));
            }
        }

    } catch (error) {
        console.error('Error loading courses:', error);
        courseSelect.innerHTML = '<option value="">Error loading courses</option>';
        showError('courseError', 'Failed to load courses: ' + error.message);
        showAlert('Error loading courses. Please try again.', 'danger');
    }
}
// ========================= LOAD INTAKES =========================
document.getElementById('course_select').addEventListener('change', async function() {
    const courseId = this.value;
    const intakeSelect = document.getElementById('new_intake');
    const generateBtn = document.getElementById('generateBtn');
    
    if (!courseId) {
        intakeSelect.innerHTML = '<option value="">Select course first</option>';
        intakeSelect.disabled = true;
        generateBtn.disabled = true;
        document.getElementById('submitBtn').disabled = true;
        return;
    }
    
    intakeSelect.innerHTML = '<option value="">Loading intakes...</option>';
    intakeSelect.disabled = true;
    generateBtn.disabled = true;
    document.getElementById('generated_id').value = '';
    clearErrors();
    
    try {
        const response = await fetch("{{ route('course.change.new.intakes') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ course_id: courseId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            intakeSelect.innerHTML = '<option value="">Select New Intake</option>';
            
            // Filter out current intake and sort by start date (newest first)
            const filteredIntakes = data.intakes.filter(intake => intake.intake_id != currentIntakeId);
            filteredIntakes.sort((a, b) => new Date(b.start_date) - new Date(a.start_date));
            
            if (filteredIntakes.length === 0) {
                intakeSelect.innerHTML = '<option value="">No other intakes available</option>';
            } else {
                filteredIntakes.forEach(intake => {
                    const startDate = new Date(intake.start_date).toLocaleDateString();
                    intakeSelect.innerHTML += `
                        <option value="${intake.intake_id}">
                            ${intake.batch} (Starts: ${startDate})
                        </option>
                    `;
                });
                intakeSelect.disabled = false;
                generateBtn.disabled = false;
            }
            
        } else {
            intakeSelect.innerHTML = '<option value="">Error loading intakes</option>';
            showError('intakeError', data.message || 'Failed to load intakes');
        }
        
    } catch (error) {
        console.error('Error loading intakes:', error);
        intakeSelect.innerHTML = '<option value="">Error loading intakes</option>';
        showError('intakeError', 'Failed to load intakes. Please try again.');
    }
});

// ========================= GENERATE NEW ID =========================
async function generateNewId() {
    const intakeId = document.getElementById('new_intake').value;
    const generatedIdInput = document.getElementById('generated_id');
    const generateBtn = document.getElementById('generateBtn');
    
    if (!intakeId) {
        showAlert('Please select an intake first', 'warning');
        return;
    }
    
    generateBtn.innerHTML = '<i class="ti ti-loader spinner me-1"></i>Generating...';
    generateBtn.disabled = true;
    clearErrors();
    
    try {
        const response = await fetch("{{ route('course.change.generateId') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ intake_id: intakeId })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            generatedIdInput.value = data.new_id;
            document.getElementById('submitBtn').disabled = false;
            showAlert('Registration ID generated successfully', 'success', 2000);
        } else {
            showError('idError', data.message || 'Error generating ID');
            showAlert(data.message || 'Error generating ID', 'danger');
        }
        
    } catch (error) {
        console.error('Error generating ID:', error);
        showError('idError', 'Failed to generate ID. Please try again.');
        showAlert('Error generating registration ID', 'danger');
    } finally {
        generateBtn.innerHTML = '<i class="ti ti-refresh me-1"></i>Generate';
        generateBtn.disabled = false;
    }
}

// Intake change event
document.getElementById('new_intake').addEventListener('change', function() {
    const generatedIdInput = document.getElementById('generated_id');
    generatedIdInput.value = '';
    document.getElementById('submitBtn').disabled = true;
    document.getElementById('generateBtn').disabled = false;
    clearErrors();
});

// ========================= SUBMIT CHANGE =========================
async function submitChange() {
    const intakeId = document.getElementById('new_intake').value;
    const newCourseRegId = document.getElementById('generated_id').value;
    const submitBtn = document.getElementById('submitBtn');
    const modal = bootstrap.Modal.getInstance(document.getElementById('changeCourseModal'));
    
    if (!intakeId || !newCourseRegId) {
        showAlert('Please select both intake and generate ID first', 'warning');
        return;
    }
    
    submitBtn.innerHTML = '<i class="ti ti-loader spinner me-2"></i>Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch("{{ route('course.change.submit') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                registration_id: selectedRegistration,
                new_intake_id: intakeId,
                new_course_registration_id: newCourseRegId
            })
        });
        
        const data = await response.json();
        
        if (data.status === 'success') {
            // Close modal
            if (modal) modal.hide();
            
            // Show success toast
            showToast(data.message || 'Course / intake updated successfully');

            showPaymentPlanPanel(data.payment_summary?.total_paid_amount || 0);
            
            // Show payment summary if applicable
            if (data.payment_summary && data.payment_summary.has_payments) {
                const summaryContent = document.getElementById('paymentSummaryContent');
                summaryContent.innerHTML = `
                    <div class="alert alert-success">
                        <h6><i class="ti ti-check me-2"></i>Course Change Successful!</h6>
                        <hr>
                        <p><strong>Total Amount Paid for Old Course:</strong></p>
                        <h4 class="text-center text-primary my-3">
                            LKR ${data.payment_summary.total_paid_amount?.toLocaleString() || '0.00'}
                        </h4>
                        <p class="mb-2">Payment records have been updated:</p>
                        <ul class="mb-3">
                            <li>✓ Old course payment records archived</li>
                            <li>✓ Old payment statuses marked as "cancelled"</li>
                            <li>✓ Carry-forward settled on new plan from installment 1</li>
                            <li>✓ Student continues with remaining installments of the new plan</li>
                        </ul>
                        <p class="mb-0">
                            <i class="ti ti-info-circle me-1"></i>
                            You can view the updated payment plans in the Student Payment Plans section.
                        </p>
                    </div>
                `;
                
                const paymentModal = new bootstrap.Modal(document.getElementById('paymentSummaryModal'));
                paymentModal.show();
            } else {
                // Show simple success alert
                showAlert(data.message || 'Course / intake updated successfully', 'success');
            }
            
            // Refresh student data after delay
            setTimeout(() => {
                if (searchedNIC) {
                    searchStudent(searchedNIC);
                }
            }, 1500);
            
        } else {
            showAlert(data.message || 'Error changing course', 'danger');
        }
        
    } catch (error) {
        console.error('Submit error:', error);
        showAlert('Error submitting change: ' + error.message, 'danger');
    } finally {
        submitBtn.innerHTML = '<i class="ti ti-check me-2"></i>Confirm Change';
        submitBtn.disabled = false;
    }
}

// ========================= EVENT LISTENERS =========================
document.addEventListener('DOMContentLoaded', function() {
    // Search form submission
    document.getElementById('searchForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const nic = document.getElementById('nic').value.trim();
        
        if (!nic) {
            showAlert('Please enter a NIC number', 'warning');
            return;
        }
        
        await searchStudent(nic);
    });
    
    // Modal hidden event
    document.getElementById('changeCourseModal').addEventListener('hidden.bs.modal', function() {
        // Reset modal
        document.getElementById('course_select').value = '';
        document.getElementById('new_intake').value = '';
        document.getElementById('generated_id').value = '';
        document.getElementById('paymentWarning').classList.add('d-none');
        document.getElementById('yearWarning').classList.add('d-none');
        document.getElementById('submitBtn').disabled = true;
        document.getElementById('generateBtn').disabled = true;
        clearErrors();
        selectedRegistration = null;
        paymentInfo = null;
    });
    
    // Debug: Test routes
    console.log('Course change routes loaded');
    console.log('Check payment route:', "{{ route('course.change.check.payment') }}");
    
    // Add event listeners for buttons to avoid inline onclick (CSP compliance)
    document.getElementById('logsBtn')?.addEventListener('click', showChangeLogs);
    document.getElementById('remarksBtn')?.addEventListener('click', showCancelledPayments);
    document.getElementById('generateBtn')?.addEventListener('click', generateNewId);
    document.getElementById('submitBtn')?.addEventListener('click', submitChange);
    document.getElementById('refreshPaymentPlanBtn')?.addEventListener('click', refreshPaymentPlanFrame);
    
    // Event delegation for dynamically created buttons
    document.getElementById('reg_table')?.addEventListener('click', function(e) {
        const changeBtn = e.target.closest('.btn-change-modal');
        if (changeBtn) {
            const regId = changeBtn.dataset.regId;
            const courseId = changeBtn.dataset.courseId;
            const intakeId = changeBtn.dataset.intakeId;
            const startDate = changeBtn.dataset.startDate;
            showChangeModal(regId, courseId, intakeId, startDate);
            return;
        }
        
        const checkPaymentBtn = e.target.closest('.btn-check-payment');
        if (checkPaymentBtn) {
            const regId = checkPaymentBtn.dataset.regId;
            checkPaymentStatus(regId);
            return;
        }
    });
});
</script>

<style nonce="{{ $cspNonce }}">
.spinner {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

.card-header {
    font-weight: 600;
}

#paymentWarning, #yearWarning {
    animation: fadeIn 0.5s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.alert ul {
    margin-bottom: 0;
}

.toast {
    z-index: 1090;
}

.form-text[style*="color: red"] {
    font-size: 0.875em;
    margin-top: 0.25rem;
}

#course_select option:disabled {
    background-color: #f8f9fa;
    font-weight: bold;
}
</style>
@endsection
