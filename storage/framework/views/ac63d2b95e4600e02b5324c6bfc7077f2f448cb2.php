

<?php $__env->startSection('title', 'NEBULA | Payment Management'); ?>

<?php $__env->startSection('content'); ?>

<style>
/* Toast Notification Styles */
.toast-container {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    max-width: 400px;
}

.toast {
    background: white;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    margin-bottom: 10px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid;
    min-width: 300px;
}

.toast.show {
    transform: translateX(0);
}

.toast.success {
    border-left-color: #10b981;
    background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
}

.toast.error {
    border-left-color: #ef4444;
    background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%);
}

.toast.warning {
    border-left-color: #f59e0b;
    background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%);
}

.toast.info {
    border-left-color: #3b82f6;
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
}

.toast-icon {
    width: 24px;
    height: 24px;
    margin-right: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.toast.success .toast-icon {
    background: #10b981;
    color: white;
}

.toast.error .toast-icon {
    background: #ef4444;
    color: white;
}

.toast.warning .toast-icon {
    background: #f59e0b;
    color: white;
}

.toast.info .toast-icon {
    background: #3b82f6;
    color: white;
}

.toast-content {
    flex: 1;
}

.toast-title {
    font-weight: 600;
    margin-bottom: 4px;
    color: #1f2937;
}

.toast-message {
    color: #6b7280;
    font-size: 14px;
}

.toast-close {
    background: none;
    border: none;
    color: #6b7280;
    cursor: pointer;
    padding: 4px;
    margin-left: 8px;
}

.toast-close:hover {
    color: #374151;
}

/* Print Styles */
@media print {
    body * {
        visibility: hidden;
    }
    
    #printableSlip, #printableSlip * {
        visibility: visible;
    }
    
    #printableSlip {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        margin: 0;
        padding: 20px;
    }
    
    .payment-slip-template {
        max-width: none !important;
        margin: 0 !important;
        border: none !important;
    }
}

/* Payment Slip Template Styles */
.payment-slip-template {
    background: white;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.payment-slip-template h2 {
    color: #1f2937;
    font-weight: bold;
}

.payment-slip-template h3 {
    color: #374151;
    font-weight: 600;
}

.payment-slip-template p {
    margin: 8px 0;
    line-height: 1.5;
}

.payment-slip-template table {
    border: 1px solid #ddd;
}

.payment-slip-template th,
.payment-slip-template td {
    padding: 12px;
    border: 1px solid #ddd;
}

.payment-slip-template th {
    background-color: #f8f9fa;
    font-weight: 600;
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOut {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

.toast.slide-in {
    animation: slideIn 0.3s ease-out;
}

.toast.slide-out {
    animation: slideOut 0.3s ease-in;
}

.lds-ring {
    display: inline-block;
    position: relative;
    width: 80px;
    height: 80px;
}
.lds-ring div {
    box-sizing: border-box;
    display: block;
    position: absolute;
    width: 64px;
    height: 64px;
    margin: 8px;
    border: 8px solid #007bff;
    border-radius: 50%;
    animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
    border-color: #007bff transparent transparent transparent;
}
.lds-ring div:nth-child(1) {
    animation-delay: -0.45s;
}
.lds-ring div:nth-child(2) {
    animation-delay: -0.3s;
}
.lds-ring div:nth-child(3) {
    animation-delay: -0.15s;
}
@keyframes lds-ring {
    0% {
        transform: rotate(0deg);
    }
    100% {
        transform: rotate(360deg);
    }
}
#spinner-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
}
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Payment Management</h2>
            <hr>

            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Navigation Tabs -->
            <ul class="nav nav-tabs" id="paymentTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="payment-plans-tab" data-bs-toggle="tab" data-bs-target="#payment-plans" type="button" role="tab" aria-controls="payment-plans" aria-selected="true">
                        <i class="ti ti-calendar me-2"></i>Payment Plans
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="generate-slips-tab" data-bs-toggle="tab" data-bs-target="#generate-slips" type="button" role="tab" aria-controls="generate-slips" aria-selected="false">
                        <i class="ti ti-receipt me-2"></i>Generate Slips
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="update-records-tab" data-bs-toggle="tab" data-bs-target="#update-records" type="button" role="tab" aria-controls="update-records" aria-selected="false">
                        <i class="ti ti-edit me-2"></i>Update Records
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="payment-summary-tab" data-bs-toggle="tab" data-bs-target="#payment-summary" type="button" role="tab" aria-controls="payment-summary" aria-selected="false">
                        <i class="ti ti-chart-pie me-2"></i>Payment Summary
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="paymentTabContent">
                <!-- Payment Plans Tab -->
                <div class="tab-pane fade show active" id="payment-plans" role="tabpanel" aria-labelledby="payment-plans-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Student NIC <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="plan-student-nic" placeholder="Enter Student NIC" required>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select filter-param" id="plan-course" name="course_id" required>
                                        <option selected disabled value="">Select a Course</option>
                                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <!-- Payment Plan Creation Form -->
                        <div class="mt-4" id="paymentPlanFormSection">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Create Payment Plan</h5>
                                </div>
                                <div class="card-body">
                                    <form id="createPaymentPlanForm">
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Student Information</label>
                                                <div class="mb-2">
                                                    <strong>Name:</strong> <span id="student-name-display">-</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Student ID:</strong> <span id="student-id-display">-</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Course:</strong> <span id="course-name-display">-</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Intake:</strong> <span id="intake-name-display">-</span>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Fee Structure</label>
                                                <div class="mb-2">
                                                    <strong>Course Fee:</strong> <span id="course-fee-display">-</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Registration Fee:</strong> <span id="registration-fee-display">-</span>
                                                </div>
                                                <div class="mb-2">
                                                    <strong>Total Amount:</strong> <span id="total-amount-display">-</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <hr>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Payment Plan Type <span class="text-danger">*</span></label>
                                                <select class="form-select" id="payment-plan-type" name="payment_plan_type" required>
                                                    <option value="">Select Payment Plan</option>
                                                    <option value="installments">Installments</option>
                                                    <option value="full">Full Payment</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Discounts</label>
                                                <div id="discounts-container">
                                                    <div class="discount-item mb-2">
                                                        <select class="form-select discount-select" name="discounts[]">
                                                            <option value="">No Discount</option>
                                                            <!-- Discounts will be loaded dynamically -->
                                                        </select>
                                                    </div>
                                                </div>
                                                <button type="button" class="btn btn-sm btn-outline-primary mt-2" id="add-discount-btn">
                                                    <i class="ti ti-plus"></i> Add Another Discount
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">SLT Loan Applied</label>
                                                <select class="form-select" id="slt-loan-applied" name="slt_loan_applied">
                                                    <option value="">No SLT Loan</option>
                                                    <option value="yes">Yes - SLT Loan Applied</option>
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">SLT Loan Amount</label>
                                                <input type="number" class="form-control" id="slt-loan-amount" name="slt_loan_amount" min="0" step="0.01" placeholder="Enter SLT loan amount" disabled>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold">Final Amount After Discount & Loan</label>
                                                <input type="text" class="form-control" id="final-amount" name="final_amount" readonly>
                                            </div>
                                        </div>
                                        
                                        <div class="row mb-3">
                                            <div class="col-12">
                                                <label class="form-label fw-bold">Installment Details</label>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered" id="installmentTable">
                                                        <thead class="table-light">
                                                            <tr>
                                                                <th>Installment #</th>
                                                                <th>Due Date</th>
                                                                <th>Amount</th>
                                                                <th>Discount</th>
                                                                <th>SLT Loan</th>
                                                                <th>Final Amount</th>
                                                                <th>Status</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="installmentTableBody">
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-12 text-center">
                                                <button type="button" class="btn btn-primary" onclick="createPaymentPlan()">
                                                    <i class="ti ti-check me-2"></i>Submit
                                                </button>
                                                <button type="button" class="btn btn-secondary" onclick="resetPaymentPlanForm()">
                                                    <i class="ti ti-refresh me-2"></i>Reset
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Existing Payment Plans Table -->
                        <div class="mt-4" id="existingPaymentPlansSection" style="display:none;">
                            <h4 class="text-center mb-3">Existing Payment Plans</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Student NIC</th>
                                            <th>Course</th>
                                            <th>Payment Plan Type</th>
                                            <th>Total Amount</th>
                                            <th>Installments</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="existingPaymentPlansTableBody">
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Generate Slips Tab -->
                <div class="tab-pane fade" id="generate-slips" role="tabpanel" aria-labelledby="generate-slips-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Student ID <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="slip-student-id" placeholder="Enter Student ID / NIC" required>
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Payment Type <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="slip-payment-type" required onchange="loadPaymentDetails()">
                                        <option value="" selected disabled>Select Payment Type</option>
                                        <option value="course_fee">Course Fee</option>
                                        <option value="franchise_fee">Franchise Fee</option>
                                        <option value="registration_fee">Registration Fee</option>
                                        <option value="library_fee">Library Fee</option>
                                        <option value="hostel_fee">Hostel Fee</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Details Table -->
                        <div class="mt-4" id="paymentDetailsSection" style="display:none;">
                            <h4 class="text-center mb-3">Payment Details</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="paymentDetailsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Select</th>
                                            <th>Installment #</th>
                                            <th>Due Date</th>
                                            <th>Amount</th>
                                            <th>Paid Date</th>
                                            <th>Status</th>
                                            <th>Receipt No</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentDetailsTableBody">
                                        <!-- Payment details will be loaded here -->
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <button type="button" class="btn btn-primary" id="generateSlipBtn" onclick="generatePaymentSlip()" disabled>
                                    <i class="ti ti-receipt me-2"></i>Generate Payment Slip
                                </button>
                            </div>
                        </div>

                        <!-- Generated Slip Preview -->
                        <div class="mt-4" id="slipPreviewSection" style="display:none;">
                            <h4 class="text-center mb-3">Payment Slip Preview</h4>
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5>Student Information</h5>
                                            <p><strong>Student ID:</strong> <span id="slip-student-id-display"></span></p>
                                            <p><strong>Student Name:</strong> <span id="slip-student-name-display"></span></p>
                                            <p><strong>Course:</strong> <span id="slip-course-display"></span></p>
                                            <p><strong>Intake:</strong> <span id="slip-intake-display"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Payment Information</h5>
                                            <p><strong>Payment Type:</strong> <span id="slip-payment-type-display"></span></p>
                                            <p><strong>Amount:</strong> <span id="slip-amount-display"></span></p>
                                            <p><strong>Installment #:</strong> <span id="slip-installment-display"></span></p>
                                            <p><strong>Due Date:</strong> <span id="slip-due-date-display"></span></p>
                                            <p><strong>Date:</strong> <span id="slip-date-display"></span></p>
                                            <p><strong>Receipt No:</strong> <span id="slip-receipt-no-display"></span></p>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-success me-2" onclick="printPaymentSlip()">
                                            <i class="ti ti-printer me-2"></i>Print Slip
                                        </button>
                                        <button type="button" class="btn btn-info me-2" onclick="downloadPaymentSlip()">
                                            <i class="ti ti-download me-2"></i>Download PDF
                                        </button>
                                        <button type="button" class="btn btn-warning" onclick="savePaymentRecord()">
                                            <i class="ti ti-device-floppy me-2"></i>Save Record
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Print-friendly Payment Slip Template -->
                        <div id="printableSlip" style="display:none;">
                            <div class="payment-slip-template" style="max-width: 800px; margin: 0 auto; padding: 20px; border: 2px solid #000; font-family: Arial, sans-serif;">
                                <!-- Header -->
                                <div style="text-align: center; border-bottom: 2px solid #000; padding-bottom: 20px; margin-bottom: 30px;">
                                    <img src="<?php echo e(asset('images/logos/nebula.png')); ?>" alt="Nebula Logo" style="height: 60px; margin-bottom: 10px;">
                                    <h2 style="margin: 0; color: #333;">SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY</h2>
                                    <p style="margin: 5px 0; font-size: 14px;">Payment Slip</p>
                                    <p style="margin: 5px 0; font-size: 12px;">Generated on: <span id="print-generated-date"></span></p>
                                </div>

                                <!-- Student Information -->
                                <div style="margin-bottom: 30px;">
                                    <h3 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px;">Student Information</h3>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div>
                                            <p><strong>Student ID:</strong> <span id="print-student-id"></span></p>
                                            <p><strong>Student Name:</strong> <span id="print-student-name"></span></p>
                                            <p><strong>Course:</strong> <span id="print-course"></span></p>
                                        </div>
                                        <div>
                                            <p><strong>Intake:</strong> <span id="print-intake"></span></p>
                                            <p><strong>Location:</strong> <span id="print-location"></span></p>
                                            <p><strong>Registration Date:</strong> <span id="print-registration-date"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Information -->
                                <div style="margin-bottom: 30px;">
                                    <h3 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px;">Payment Information</h3>
                                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                                        <div>
                                            <p><strong>Payment Type:</strong> <span id="print-payment-type"></span></p>
                                            <p><strong>Installment #:</strong> <span id="print-installment"></span></p>
                                            <p><strong>Due Date:</strong> <span id="print-due-date"></span></p>
                                        </div>
                                        <div>
                                            <p><strong>Amount:</strong> <span id="print-amount"></span></p>
                                            <p><strong>Receipt No:</strong> <span id="print-receipt-no"></span></p>
                                            <p><strong>Valid Until:</strong> <span id="print-valid-until"></span></p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Payment Details Table -->
                                <div style="margin-bottom: 30px;">
                                    <h3 style="border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 15px;">Payment Breakdown</h3>
                                    <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                        <thead>
                                            <tr style="background-color: #f8f9fa;">
                                                <th style="border: 1px solid #ddd; padding: 10px; text-align: left;">Description</th>
                                                <th style="border: 1px solid #ddd; padding: 10px; text-align: right;">Amount (LKR)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td style="border: 1px solid #ddd; padding: 10px;">Course Fee</td>
                                                <td style="border: 1px solid #ddd; padding: 10px; text-align: right;" id="print-course-fee">0.00</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #ddd; padding: 10px;">Franchise Fee</td>
                                                <td style="border: 1px solid #ddd; padding: 10px; text-align: right;" id="print-franchise-fee">0.00</td>
                                            </tr>
                                            <tr>
                                                <td style="border: 1px solid #ddd; padding: 10px;">Registration Fee</td>
                                                <td style="border: 1px solid #ddd; padding: 10px; text-align: right;" id="print-registration-fee">0.00</td>
                                            </tr>
                                            <tr style="background-color: #f8f9fa; font-weight: bold;">
                                                <td style="border: 1px solid #ddd; padding: 10px;">Total Amount</td>
                                                <td style="border: 1px solid #ddd; padding: 10px; text-align: right;" id="print-total-amount">0.00</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Instructions -->
                                <div style="margin-bottom: 30px; padding: 15px; background-color: #f8f9fa; border-left: 4px solid #007bff;">
                                    <h4 style="margin: 0 0 10px 0; color: #007bff;">Payment Instructions</h4>
                                    <ol style="margin: 0; padding-left: 20px;">
                                        <li>Please present this slip when making payment</li>
                                        <li>Payment can be made in cash or bank transfer</li>
                                        <li>Keep this slip for your records</li>
                                        <li>Return the paid slip to the office for record update</li>
                                        <li>This slip is valid for 7 days from the date of issue</li>
                                    </ol>
                                </div>

                                <!-- Footer -->
                                <div style="text-align: center; border-top: 2px solid #000; padding-top: 20px; margin-top: 30px;">
                                    <p style="margin: 5px 0; font-size: 12px;">© 2024 SLTMOBITEL NEBULA INSTITUTE OF TECHNOLOGY. All rights reserved.</p>
                                    <p style="margin: 5px 0; font-size: 10px;">This is a computer-generated document. No signature required.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Update Records Tab -->
                <div class="tab-pane fade" id="update-records" role="tabpanel" aria-labelledby="update-records-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Student NIC <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="update-student-nic" placeholder="Enter Student NIC" required onchange="loadStudentCoursesForUpdate()">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="update-course" required>
                                        <option value="" selected disabled>Select a Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="loadPaymentRecords()">
                                        <i class="ti ti-search me-2"></i>Load Payment Records
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Records Table -->
                        <div class="mt-4" id="paymentRecordsSection" style="display:none;">
                            <h4 class="text-center mb-3">Payment Records</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Payment Type</th>
                                            <th>Amount</th>
                                            <th>Payment Method</th>
                                            <th>Payment Date</th>
                                            <th>Receipt No</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody id="paymentRecordsTableBody">
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3" id="updateSaveBtnSection" style="display:none;">
                                <button type="button" class="btn btn-success" onclick="updatePaymentRecords()">
                                    <i class="ti ti-device-floppy me-2"></i>Update Records
                                </button>
                            </div>
                        </div>

                        <!-- Upload Paid Slip Section -->
                        <div class="mt-4" id="uploadPaidSlipSection">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="ti ti-upload me-2"></i>Upload Paid Slip
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Receipt Number <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="upload-receipt-no" placeholder="Enter receipt number from generated slip" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Payment Method <span class="text-danger">*</span></label>
                                                <select class="form-select" id="upload-payment-method" required>
                                                    <option value="" selected disabled>Select Payment Method</option>
                                                    <option value="Cash">Cash</option>
                                                    <option value="Bank Transfer">Bank Transfer</option>
                                                    <option value="Cheque">Cheque</option>
                                                    <option value="Credit Card">Credit Card</option>
                                                    <option value="Debit Card">Debit Card</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Payment Date <span class="text-danger">*</span></label>
                                                <input type="date" class="form-control" id="upload-payment-date" required>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Paid Slip <span class="text-danger">*</span></label>
                                                <input type="file" class="form-control" id="upload-paid-slip" accept=".jpg,.jpeg,.png,.pdf" required>
                                                <small class="text-muted">Upload the paid slip (JPG, PNG, or PDF - max 2MB)</small>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-bold">Remarks</label>
                                                <textarea class="form-control" id="upload-remarks" rows="3" placeholder="Any additional remarks..."></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-center mt-3">
                                        <button type="button" class="btn btn-primary" onclick="uploadPaidSlip()">
                                            <i class="ti ti-upload me-2"></i>Upload Paid Slip
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary Tab -->
                <div class="tab-pane fade" id="payment-summary" role="tabpanel" aria-labelledby="payment-summary-tab">
                    <div class="mt-4">
                        <!-- Filters -->
                        <div class="mb-4">
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Student NIC <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="summary-student-nic" placeholder="Enter Student NIC" required onchange="loadStudentCoursesForSummary()">
                                </div>
                            </div>
                            <div class="row mb-3 align-items-center">
                                <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                                <div class="col-sm-10">
                                    <select class="form-select" id="summary-course" required>
                                        <option value="" selected disabled>Select a Course</option>
                                    </select>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 text-center">
                                    <button type="button" class="btn btn-primary" onclick="generatePaymentSummary()">
                                        <i class="ti ti-chart-pie me-2"></i>Generate Summary
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Payment Summary -->
                        <div class="mt-4" id="paymentSummarySection" style="display:none;">
                            <h4 class="text-center mb-3">Payment Summary</h4>
                            
                            <!-- Student Information -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="ti ti-user me-2"></i>Student Information
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <p><strong>Student ID:</strong> <span id="summary-student-id"></span></p>
                                            <p><strong>Student Name:</strong> <span id="summary-student-name"></span></p>
                                            <p><strong>Course:</strong> <span id="summary-course-name"></span></p>
                                        </div>
                                        <div class="col-md-6">
                                            <p><strong>Registration Date:</strong> <span id="summary-registration-date"></span></p>
                                            <p><strong>Total Course Fee:</strong> <span id="summary-total-course-fee"></span></p>
                                            <p><strong>Total Paid:</strong> <span id="summary-total-paid"></span></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Summary Cards -->
                            <div class="row mb-4">
                                <div class="col-md-3">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h5>Total Amount</h5>
                                            <h3 id="total-amount">Rs. 0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h5>Total Paid</h5>
                                            <h3 id="total-paid">Rs. 0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-warning text-white">
                                        <div class="card-body text-center">
                                            <h5>Outstanding</h5>
                                            <h3 id="total-outstanding">Rs. 0</h3>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h5>Payment Rate</h5>
                                            <h3 id="payment-rate">0%</h3>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Payment Details Table -->
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">
                                        <i class="ti ti-list me-2"></i>Payment Details by Type
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <!-- Local Course Fee Table -->
                                    <div class="mb-4">
                                        <h6 class="text-primary mb-3">
                                            <i class="ti ti-book me-2"></i>Local Course Fee
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="courseFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No course fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Franchise Payments Table -->
                                    <div class="mb-4">
                                        <h6 class="text-success mb-3">
                                            <i class="ti ti-building me-2"></i>Franchise Payments
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="franchiseFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No franchise fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Registration Fee Table -->
                                    <div class="mb-4">
                                        <h6 class="text-info mb-3">
                                            <i class="ti ti-file-text me-2"></i>Registration Fee
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="registrationFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No registration fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Hostel Fee Table -->
                                    <div class="mb-4">
                                        <h6 class="text-warning mb-3">
                                            <i class="ti ti-home me-2"></i>Hostel Fee
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="hostelFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No hostel fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Library Fee Table -->
                                    <div class="mb-4">
                                        <h6 class="text-secondary mb-3">
                                            <i class="ti ti-library me-2"></i>Library Fee
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="libraryFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No library fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Other Fees Table -->
                                    <div class="mb-4">
                                        <h6 class="text-dark mb-3">
                                            <i class="ti ti-plus me-2"></i>Other
                                        </h6>
                                        <div class="table-responsive">
                                            <table class="table table-bordered table-sm">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th>Total Amount</th>
                                                        <th>Paid Amount</th>
                                                        <th>Outstanding</th>
                                                        <th>Paid Date</th>
                                                        <th>Due Date</th>
                                                        <th>Receipt No</th>
                                                        <th>Uploaded Receipt</th>
                                                        <th>Installments</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="otherFeeTableBody">
                                                    <tr><td colspan="8" class="text-center text-muted">No other fee data available</td></tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let paymentPlans = [];
let paymentRecords = [];
let paymentSummary = {};

// Toast Notification Functions
function showSuccessMessage(message) {
    showToast('Success', message, 'success');
}

function showErrorMessage(message) {
    showToast('Error', message, 'error');
}

function showWarningMessage(message) {
    showToast('Warning', message, 'warning');
}

function showInfoMessage(message) {
    showToast('Info', message, 'info');
}

// Toast notification function
function showToast(title, message, type = 'info') {
    const container = document.getElementById('toastContainer');
    const toast = document.createElement('div');
    const toastId = 'toast-' + Date.now();
    
    const icons = {
        success: '✓',
        error: '✕',
        warning: '⚠',
        info: 'ℹ'
    };

    toast.className = `toast ${type}`;
    toast.id = toastId;
    toast.innerHTML = `
        <div class="toast-icon">
            ${icons[type]}
        </div>
        <div class="toast-content">
            <div class="toast-title">${title}</div>
            <div class="toast-message">${message}</div>
        </div>
        <button class="toast-close" onclick="removeToast('${toastId}')">
            ×
        </button>
    `;

    container.appendChild(toast);

    // Trigger animation
    setTimeout(() => {
        toast.classList.add('show');
    }, 10);

    // Auto remove after 5 seconds
    setTimeout(() => {
        removeToast(toastId);
    }, 5000);
}

function removeToast(toastId) {
    const toast = document.getElementById(toastId);
    if (toast) {
        toast.classList.add('slide-out');
        setTimeout(() => {
            if (toast.parentNode) {
                toast.parentNode.removeChild(toast);
            }
        }, 300);
    }
}

// Spinner functions
function showSpinner(show) {
    document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
}

// Load discounts from backend
function loadDiscounts() {
    fetch('/payment/get-discounts', {
        method: 'GET',
        headers: {'Content-Type': 'application/json'}
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update all discount selects
            const discountSelects = document.querySelectorAll('.discount-select');
            discountSelects.forEach(select => {
                select.innerHTML = '<option value="">No Discount</option>';
                
                data.discounts.forEach(discount => {
                    const valueDisplay = discount.type === 'percentage' ? 
                        `${discount.name} (${discount.value}%)` : 
                        `${discount.name} (LKR ${discount.value.toLocaleString()})`;
                    select.innerHTML += `<option value="${discount.id}" data-type="${discount.type}" data-value="${discount.value}">${valueDisplay}</option>`;
                });
            });
        } else {
            console.error('Failed to load discounts:', data.message);
        }
    })
    .catch(error => {
        console.error('Error loading discounts:', error);
    });
}

// Load courses for student based on NIC
function loadCoursesForStudent() {
    const studentNic = document.getElementById('plan-student-nic').value;
    
    if (!studentNic) {
        // Reset course dropdown to show all courses
        document.getElementById('plan-course').innerHTML = '<option selected disabled value="">Select a Course</option>' + 
            '<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>';
        return;
    }

    showSpinner(true);
    
    // Make API call to get courses for the student
    fetch('/payment/get-student-courses', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const courseSelect = document.getElementById('plan-course');
            courseSelect.innerHTML = '<option selected disabled value="">Select a Course</option>';
            
            data.courses.forEach(course => {
                courseSelect.innerHTML += `<option value="${course.course_id}">${course.course_name}</option>`;
            });
            
            if (data.courses.length === 0) {
                showInfoMessage('No courses found for this student.');
            }
        } else {
            showErrorMessage(data.message || 'Failed to load courses for student.');
            // Reset to all courses on error
            document.getElementById('plan-course').innerHTML = '<option selected disabled value="">Select a Course</option>' + 
                '<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading courses.');
        // Reset to all courses on error
        document.getElementById('plan-course').innerHTML = '<option selected disabled value="">Select a Course</option>' + 
            '<?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>';
    })
    .finally(() => showSpinner(false));
}

// Load student and course details for payment plan creation
function loadStudentForPaymentPlan() {
    const studentNic = document.getElementById('plan-student-nic').value;
    const courseId = document.getElementById('plan-course').value;

    if (!studentNic || !courseId) {
        showWarningMessage('Please enter Student NIC and select a Course.');
        return;
    }

    showSpinner(true);
    
    // Make API call to get student and course details
    fetch('/payment/get-plans', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic,
            course_id: parseInt(courseId)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Populate the form with student and course details
            populatePaymentPlanForm(data.student);
            document.getElementById('paymentPlanFormSection').style.display = '';
            document.getElementById('existingPaymentPlansSection').style.display = '';
            loadExistingPaymentPlans(studentNic, parseInt(courseId));
        } else {
            showErrorMessage(data.message || 'Failed to load student details.');
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading student details.');
    })
    .finally(() => showSpinner(false));
}

// Populate payment plan form with student and course details
function populatePaymentPlanForm(studentData) {
    console.log('populatePaymentPlanForm called with studentData:', studentData);
    
    document.getElementById('student-name-display').textContent = studentData.student_name || 'N/A';
    document.getElementById('student-id-display').textContent = studentData.student_id || 'N/A';
    document.getElementById('course-name-display').textContent = studentData.course_name || 'N/A';
    document.getElementById('intake-name-display').textContent = studentData.intake_name || 'N/A';
    document.getElementById('course-fee-display').textContent = 'LKR ' + (studentData.course_fee || 0).toLocaleString();
    document.getElementById('registration-fee-display').textContent = 'LKR ' + (studentData.registration_fee || 0).toLocaleString();
    document.getElementById('total-amount-display').textContent = 'LKR ' + (studentData.total_amount || 0).toLocaleString();
    
    // Store student data for later use
    window.currentStudentData = studentData;
    
    // Calculate initial final amount
    calculateFinalAmount();
}

// Load existing payment plans for the student and course
function loadExistingPaymentPlans(studentNic, courseId) {
    console.log('Loading existing payment plans for:', { studentNic, courseId });
    
    // First try to fetch existing payment plan installments
    fetch('/payment/get-installments', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic,
            course_id: parseInt(courseId)
        })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Payment plan installments response:', data);
        if (data.success) {
            console.log('Payment plan found, displaying installments');
            // Display existing installments in the table
            displayInstallments(data.installments);
        } else {
            console.log('No payment plan found for this course and intake, showing preview');
            // Show preview of installments based on payment plan type
            showInstallmentPreview();
        }
    })
    .catch(error => {
        console.error('Error loading installments:', error);
        // Show error message
        const tbody = document.getElementById('installmentTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center text-danger">Error loading payment plan installments.</td></tr>';
    });
}

// Display installments in the table
function displayInstallments(installments) {
    const tbody = document.getElementById('installmentTableBody');
    tbody.innerHTML = '';
    
    console.log('displayInstallments called with installments:', installments);
    
    // Check if installments array is empty
    if (!installments || installments.length === 0) {
        console.log('No installments found, showing preview');
        showInstallmentPreview();
        return;
    }
    
    // Get SLT loan settings from the form
    const sltLoanApplied = document.getElementById('slt-loan-applied').value;
    const sltLoanAmount = parseFloat(document.getElementById('slt-loan-amount').value) || 0;
    
    installments.forEach(installment => {
        let finalAmount = parseFloat(installment.final_amount);
        let sltLoanText = '-';
        
        // Apply SLT loan to every installment
        if (sltLoanApplied === 'yes' && sltLoanAmount > 0) {
            const sltLoanPerInstallment = sltLoanAmount / installments.length;
            finalAmount -= sltLoanPerInstallment;
            sltLoanText = `LKR ${sltLoanPerInstallment.toFixed(2)}`;
        }
        
        const row = `
            <tr>
                <td>${installment.installment_number}</td>
                <td>${new Date(installment.due_date).toLocaleDateString()}</td>
                <td>LKR ${parseFloat(installment.amount).toLocaleString()}</td>
                <td>${installment.discount || '-'}</td>
                <td>${sltLoanText}</td>
                <td>LKR ${Math.max(0, finalAmount).toLocaleString()}</td>
                <td>
                    <span class="badge bg-${getStatusBadgeColor(installment.status)}">
                        ${installment.status}
                    </span>
                </td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Get badge color based on status
function getStatusBadgeColor(status) {
    switch (status.toLowerCase()) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warning';
        case 'overdue':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Show preview of installments when no payment plan exists
function showInstallmentPreview() {
    const planType = document.getElementById('payment-plan-type').value;
    const tbody = document.getElementById('installmentTableBody');
    
    console.log('showInstallmentPreview called with planType:', planType);
    console.log('currentStudentData:', window.currentStudentData);
    
    if (planType === 'full') {
        // Show single installment for full payment
        const totalAmount = window.currentStudentData ? (window.currentStudentData.final_amount || window.currentStudentData.total_amount) : 0;
        console.log('Showing full payment preview with amount:', totalAmount);
        tbody.innerHTML = `
            <tr>
                <td>1</td>
                <td>${new Date().toLocaleDateString()}</td>
                <td>LKR ${totalAmount.toLocaleString()}</td>
                <td>-</td>
                <td>-</td>
                <td>LKR ${totalAmount.toLocaleString()}</td>
                <td><span class="badge bg-warning">Pending</span></td>
            </tr>
        `;
    } else if (planType === 'installments') {
        // Show message that installments will be loaded from payment plan
        console.log('Showing installments preview message');
        tbody.innerHTML = `
            <tr>
                <td colspan="7" class="text-center text-muted">
                    <i class="ti ti-info-circle me-2"></i>
                    Installments will be loaded from the payment plan once it is created in the Payment Plan page.
                    <br><small class="text-muted">Please create a payment plan for this course and intake first.</small>
                </td>
            </tr>
        `;
    } else {
        console.log('No plan type selected');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Please select a payment plan type.</td></tr>';
    }
}

// Calculate and display installments based on current form data
function calculateAndDisplayInstallments() {
    console.log('calculateAndDisplayInstallments called');
    console.log('currentStudentData:', window.currentStudentData);
    
    if (!window.currentStudentData) {
        console.log('No currentStudentData, showing message');
        const tbody = document.getElementById('installmentTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Please load student details first.</td></tr>';
        return;
    }

    const planType = document.getElementById('payment-plan-type').value;
    console.log('Selected plan type:', planType);
    
    if (!planType) {
        console.log('No plan type selected, showing message');
        const tbody = document.getElementById('installmentTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Please select a payment plan type.</td></tr>';
        return;
    }

    console.log('Loading existing payment plans for student:', window.currentStudentData.student_nic, 'course:', window.currentStudentData.course_id);
    // Try to load installments from payment plan first
    loadExistingPaymentPlans(window.currentStudentData.student_nic, window.currentStudentData.course_id);
}

// Calculate final amount after multiple discounts and SLT loan
function calculateFinalAmount() {
    const totalAmount = window.currentStudentData ? window.currentStudentData.total_amount : 0;
    const discountSelects = document.querySelectorAll('.discount-select');
    const sltLoanApplied = document.getElementById('slt-loan-applied').value;
    const sltLoanAmount = parseFloat(document.getElementById('slt-loan-amount').value) || 0;
    const finalAmountField = document.getElementById('final-amount');
    
    let finalAmount = totalAmount;
    let totalDiscountAmount = 0;
    let totalDiscountPercentage = 0;
    
    // Calculate total discounts
    discountSelects.forEach(select => {
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            const discountType = selectedOption.dataset.type;
            const discountValue = parseFloat(selectedOption.dataset.value);
            
            if (discountType === 'percentage') {
                totalDiscountPercentage += discountValue;
            } else if (discountType === 'amount') {
                totalDiscountAmount += discountValue;
            }
        }
    });
    
    // Apply percentage discounts first
    if (totalDiscountPercentage > 0) {
        finalAmount = finalAmount - (finalAmount * totalDiscountPercentage / 100);
    }
    
    // Apply fixed amount discounts
    if (totalDiscountAmount > 0) {
        finalAmount = finalAmount - totalDiscountAmount;
    }
    
    // Apply SLT loan if selected
    if (sltLoanApplied === 'yes' && sltLoanAmount > 0) {
        finalAmount = finalAmount - sltLoanAmount;
    }
    
    // Ensure final amount is not negative
    finalAmount = Math.max(0, finalAmount);
    
    finalAmountField.value = 'LKR ' + finalAmount.toLocaleString();
    
    // Update window.currentStudentData with final amount
    if (window.currentStudentData) {
        window.currentStudentData.final_amount = finalAmount;
    }
}

// Calculate and display installments
function calculateInstallments() {
    const planType = document.getElementById('payment-plan-type').value;
    
    if (!planType) {
        return;
    }
    
    if (!window.currentStudentData) {
        const tbody = document.getElementById('installmentTableBody');
        tbody.innerHTML = '<tr><td colspan="7" class="text-center">Please load student details first.</td></tr>';
        return;
    }
    
    // Load installments from payment plan
    loadExistingPaymentPlans(window.currentStudentData.student_nic, window.currentStudentData.course_id);
}

// Create payment plan
function createPaymentPlan() {
    const planType = document.getElementById('payment-plan-type').value;
    const discountSelects = document.querySelectorAll('.discount-select');
    const sltLoanApplied = document.getElementById('slt-loan-applied').value;
    const sltLoanAmount = document.getElementById('slt-loan-amount').value;
    
    console.log('Creating payment plan with data:', {
        planType,
        studentData: window.currentStudentData,
        discountSelects: discountSelects.length,
        sltLoanApplied,
        sltLoanAmount
    });
    
    if (!planType) {
        showWarningMessage('Please fill in all required fields.');
        return;
    }
    
    if (!window.currentStudentData) {
        showErrorMessage('No student data available. Please load student details first.');
        return;
    }
    
    showSpinner(true);
    
    // Collect all selected discounts
    const selectedDiscounts = [];
    discountSelects.forEach(select => {
        if (select.value) {
            const selectedOption = select.options[select.selectedIndex];
            selectedDiscounts.push({
                discount_id: select.value,
                discount_type: selectedOption.dataset.type,
                discount_value: selectedOption.dataset.value
            });
        }
    });
    
    // First, get the payment plan installments from the backend
    fetch('/payment/get-installments', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: window.currentStudentData.student_nic,
            course_id: parseInt(window.currentStudentData.course_id)
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Use the installments from the payment plan
            const installments = data.installments.map(installment => ({
                installment_number: installment.installment_number,
                due_date: installment.due_date,
                amount: installment.amount,
                status: 'pending'
            }));
            
            // Prepare payment plan data
            const paymentPlanData = {
                student_id: window.currentStudentData.student_id,
                course_id: parseInt(window.currentStudentData.course_id),
                payment_plan_type: planType,
                discounts: selectedDiscounts,
                slt_loan_applied: sltLoanApplied,
                slt_loan_amount: sltLoanAmount,
                total_amount: window.currentStudentData.total_amount,
                final_amount: window.currentStudentData.final_amount || window.currentStudentData.total_amount,
                installments: installments
            };
            
            // Send to backend
            return fetch('/payment/create-payment-plan', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
                body: JSON.stringify(paymentPlanData)
            });
        } else {
            throw new Error(data.message || 'Failed to get payment plan installments');
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Payment plan created successfully:', data);
            showSuccessMessage('Payment plan created successfully! 🎉');
            resetPaymentPlanForm();
            loadExistingPaymentPlans(window.currentStudentData.student_nic, window.currentStudentData.course_id);
        } else {
            console.error('Failed to create payment plan:', data);
            showErrorMessage(data.message || 'Failed to create payment plan.');
        }
    })
    .catch((error) => {
        console.error('Error creating payment plan:', error);
        showErrorMessage(error.message || 'An error occurred while creating payment plan.');
    })
    .finally(() => showSpinner(false));
}

// Reset payment plan form
function resetPaymentPlanForm() {
    document.getElementById('createPaymentPlanForm').reset();
    document.getElementById('installmentTableBody').innerHTML = '';
    document.getElementById('paymentPlanFormSection').style.display = 'none';
    document.getElementById('existingPaymentPlansSection').style.display = 'none';
    
    // Reset discount fields to only one
    const discountsContainer = document.getElementById('discounts-container');
    const discountItems = discountsContainer.querySelectorAll('.discount-item');
    
    // Remove all discount items except the first one
    for (let i = 1; i < discountItems.length; i++) {
        discountItems[i].remove();
    }
    
    // Reset the first discount select
    const firstDiscountSelect = discountsContainer.querySelector('.discount-select');
    if (firstDiscountSelect) {
        firstDiscountSelect.value = '';
    }
    
    window.currentStudentData = null;
}

// Render payment plans table
function renderPaymentPlans() {
    const tbody = document.getElementById('paymentPlansTableBody');
    tbody.innerHTML = '';
    
    paymentPlans.forEach((plan, index) => {
        const row = `<tr>
            <td>${plan.student_id}</td>
            <td>${plan.student_name}</td>
            <td>${plan.student_nic}</td>
            <td>${plan.course_name}</td>
            <td>Rs. ${plan.course_fee.toLocaleString()}</td>
            <td>Rs. ${plan.franchise_fee.toLocaleString()}</td>
            <td>Rs. ${plan.registration_fee.toLocaleString()}</td>
            <td>Rs. ${plan.total_amount.toLocaleString()}</td>
            <td>Rs. ${plan.paid_amount.toLocaleString()}</td>
            <td>Rs. ${plan.outstanding_amount.toLocaleString()}</td>
            <td>
                <select class="form-select" onchange="updatePaymentPlan(${index}, this.value)">
                    <option value="Monthly" ${plan.payment_plan === 'Monthly' ? 'selected' : ''}>Monthly</option>
                    <option value="Quarterly" ${plan.payment_plan === 'Quarterly' ? 'selected' : ''}>Quarterly</option>
                    <option value="Semester" ${plan.payment_plan === 'Semester' ? 'selected' : ''}>Semester</option>
                    <option value="Full" ${plan.payment_plan === 'Full' ? 'selected' : ''}>Full Payment</option>
                </select>
            </td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick="editPaymentPlan(${index})">
                    <i class="ti ti-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-info" onclick="viewPaymentHistory(${index})">
                    <i class="ti ti-history"></i>
                </button>
            </td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update payment plan
window.updatePaymentPlan = function(index, value) {
    paymentPlans[index].payment_plan = value;
}

// Save payment plans
function savePaymentPlans() {
    if (paymentPlans.length === 0) {
        showWarningMessage('No payment plan to save.');
        return;
    }

    const plan = paymentPlans[0];
    showSpinner(true);
    
    fetch('/payment/save-plans', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_id: plan.student_id,
            course_id: document.getElementById('plan-course').value,
            payment_plan: plan.payment_plan
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'Payment plan saved successfully! ✨');
        } else {
            showErrorMessage(data.message || 'Failed to save payment plan.');
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while saving payment plan.');
    })
    .finally(() => showSpinner(false));
}

// Generate payment slip for selected payment
function generatePaymentSlip() {
    const selectedPayment = document.querySelector('input[name="selectedPayment"]:checked');
    
    if (!selectedPayment) {
        showWarningMessage('Please select a payment to generate slip for.');
        return;
    }
    
    const studentId = document.getElementById('slip-student-id').value;
    const paymentType = document.getElementById('slip-payment-type').value;
    
    // Get the selected payment data
    const paymentIndex = selectedPayment.value;
    const selectedPaymentData = window.paymentDetailsData[paymentIndex];
    
    if (!selectedPaymentData) {
        showErrorMessage('Selected payment data not found.');
        return;
    }
    
    showSpinner(true);
    
    fetch('/payment/generate-slip', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_id: studentId,
            payment_type: paymentType,
            amount: selectedPaymentData.amount,
            installment_number: selectedPaymentData.installment_number,
            due_date: selectedPaymentData.due_date,
            remarks: ''
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store slip data for later use
            window.currentSlipData = data.slip_data;
            
            // Display slip preview with improved formatting
            document.getElementById('slip-student-id-display').textContent = data.slip_data.student_id;
            document.getElementById('slip-student-name-display').textContent = data.slip_data.student_name;
            document.getElementById('slip-course-display').textContent = data.slip_data.course_name;
            document.getElementById('slip-intake-display').textContent = data.slip_data.intake;
            document.getElementById('slip-payment-type-display').textContent = data.slip_data.payment_type_display || data.slip_data.payment_type;
            document.getElementById('slip-amount-display').textContent = 'LKR ' + parseFloat(data.slip_data.amount).toLocaleString();
            document.getElementById('slip-installment-display').textContent = selectedPaymentData.installment_number || '-';
            document.getElementById('slip-due-date-display').textContent = selectedPaymentData.due_date ? new Date(selectedPaymentData.due_date).toLocaleDateString() : '-';
            document.getElementById('slip-date-display').textContent = data.slip_data.payment_date;
            document.getElementById('slip-receipt-no-display').textContent = data.slip_data.receipt_no;
            
            // Show slip preview section
            document.getElementById('slipPreviewSection').style.display = 'block';
            
            // Scroll to slip preview
            document.getElementById('slipPreviewSection').scrollIntoView({ behavior: 'smooth' });
            
            showSuccessMessage(data.message || 'Payment slip generated successfully! 🎉');
        } else {
            showErrorMessage(data.message || 'Failed to generate payment slip.');
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while generating payment slip.');
    })
    .finally(() => showSpinner(false));
}

// Print payment slip
function printPaymentSlip() {
    if (!window.currentSlipData) {
        showErrorMessage('No slip data available for printing.');
        return;
    }

    // Populate the print template
    const slipData = window.currentSlipData;
    
    document.getElementById('print-generated-date').textContent = new Date().toLocaleDateString();
    document.getElementById('print-student-id').textContent = slipData.student_id;
    document.getElementById('print-student-name').textContent = slipData.student_name;
    document.getElementById('print-course').textContent = slipData.course_name;
    document.getElementById('print-intake').textContent = slipData.intake;
    document.getElementById('print-location').textContent = slipData.location;
    document.getElementById('print-registration-date').textContent = slipData.registration_date ? new Date(slipData.registration_date).toLocaleDateString() : 'N/A';
    document.getElementById('print-payment-type').textContent = slipData.payment_type_display || slipData.payment_type;
    document.getElementById('print-installment').textContent = slipData.installment_number || 'N/A';
    document.getElementById('print-due-date').textContent = slipData.due_date ? new Date(slipData.due_date).toLocaleDateString() : 'N/A';
    document.getElementById('print-amount').textContent = 'LKR ' + parseFloat(slipData.amount).toLocaleString();
    document.getElementById('print-receipt-no').textContent = slipData.receipt_no;
    document.getElementById('print-valid-until').textContent = slipData.valid_until ? new Date(slipData.valid_until).toLocaleDateString() : 'N/A';
    
    // Populate fee breakdown
    document.getElementById('print-course-fee').textContent = parseFloat(slipData.course_fee || 0).toLocaleString() + '.00';
    document.getElementById('print-franchise-fee').textContent = parseFloat(slipData.franchise_fee || 0).toLocaleString() + '.00';
    document.getElementById('print-registration-fee').textContent = parseFloat(slipData.registration_fee || 0).toLocaleString() + '.00';
    document.getElementById('print-total-amount').textContent = parseFloat(slipData.amount).toLocaleString() + '.00';

    // Show the printable slip
    document.getElementById('printableSlip').style.display = 'block';
    
    // Hide other content temporarily
    const mainContent = document.querySelector('.container-fluid');
    const originalDisplay = mainContent.style.display;
    mainContent.style.display = 'none';
    
    // Print
    window.print();
    
    // Restore content
    setTimeout(() => {
        mainContent.style.display = originalDisplay;
        document.getElementById('printableSlip').style.display = 'none';
    }, 1000);
}

// Download payment slip
function downloadPaymentSlip() {
    showToast('Info', 'Download functionality will be implemented soon.', 'bg-info');
}

// Save payment record
function savePaymentRecord() {
    showSpinner(true);
    setTimeout(() => {
        showToast('Success', 'Payment record saved successfully.', 'bg-success');
        showSpinner(false);
    }, 1000);
}

// Load payment records
function loadPaymentRecords() {
    const studentNic = document.getElementById('update-student-nic').value;
    const courseId = document.getElementById('update-course').value;

    if (!studentNic || !courseId) {
        showWarningMessage('Please enter student NIC and select a course.');
        return;
    }

    showSpinner(true);

    fetch('/payment/get-records', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic,
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.paymentRecords = data.records;
            renderPaymentRecords();
            document.getElementById('paymentRecordsSection').style.display = 'block';
            showSuccessMessage('Payment records loaded successfully!');
        } else {
            showErrorMessage(data.message || 'Failed to load payment records.');
            document.getElementById('paymentRecordsSection').style.display = 'none';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading payment records.');
        document.getElementById('paymentRecordsSection').style.display = 'none';
    })
    .finally(() => showSpinner(false));
}

// Load courses for student when NIC is entered
function loadStudentCoursesForUpdate() {
    const studentNic = document.getElementById('update-student-nic').value;
    
    if (!studentNic) {
        document.getElementById('update-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
        return;
    }

    showSpinner(true);

    fetch('/payment/get-student-courses', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const courseSelect = document.getElementById('update-course');
            courseSelect.innerHTML = '<option value="" selected disabled>Select a Course</option>';
            
            data.courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.course_id;
                option.textContent = course.course_name;
                courseSelect.appendChild(option);
            });
            
            showSuccessMessage('Courses loaded successfully!');
        } else {
            showErrorMessage(data.message || 'Failed to load courses.');
            document.getElementById('update-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading courses.');
        document.getElementById('update-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
    })
    .finally(() => showSpinner(false));
}

// Render payment records table
function renderPaymentRecords() {
    const tbody = document.getElementById('paymentRecordsTableBody');
    tbody.innerHTML = '';
    
    paymentRecords.forEach((record, index) => {
        const row = `<tr>
            <td>${record.student_id}</td>
            <td>${record.student_name}</td>
            <td>${record.payment_type}</td>
            <td>Rs. ${record.amount.toLocaleString()}</td>
            <td>${record.payment_method}</td>
            <td>${record.payment_date}</td>
            <td>${record.receipt_no}</td>
            <td><span class="badge bg-success">${record.status}</span></td>
            <td>
                <button type="button" class="btn btn-sm btn-primary" onclick="editPaymentRecord(${index})">
                    <i class="ti ti-edit"></i>
                </button>
                <button type="button" class="btn btn-sm btn-danger" onclick="deletePaymentRecord(${index})">
                    <i class="ti ti-trash"></i>
                </button>
            </td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Update payment records
function updatePaymentRecords() {
    showSpinner(true);
    setTimeout(() => {
        showToast('Success', 'Payment records updated successfully.', 'bg-success');
        showSpinner(false);
    }, 1000);
}

// Load courses for student when NIC is entered (for summary)
function loadStudentCoursesForSummary() {
    const studentNic = document.getElementById('summary-student-nic').value;
    
    if (!studentNic) {
        document.getElementById('summary-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
        return;
    }

    showSpinner(true);

    fetch('/payment/get-student-courses', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const courseSelect = document.getElementById('summary-course');
            courseSelect.innerHTML = '<option value="" selected disabled>Select a Course</option>';
            
            data.courses.forEach(course => {
                const option = document.createElement('option');
                option.value = course.course_id;
                option.textContent = course.course_name;
                courseSelect.appendChild(option);
            });
            
            showSuccessMessage('Courses loaded successfully!');
        } else {
            showErrorMessage(data.message || 'Failed to load courses.');
            document.getElementById('summary-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading courses.');
        document.getElementById('summary-course').innerHTML = '<option value="" selected disabled>Select a Course</option>';
    })
    .finally(() => showSpinner(false));
}

// Generate payment summary
function generatePaymentSummary() {
    const studentNic = document.getElementById('summary-student-nic').value;
    const courseId = document.getElementById('summary-course').value;

    if (!studentNic || !courseId) {
        showWarningMessage('Please enter student NIC and select a course.');
        return;
    }

    showSpinner(true);

    fetch('/payment/get-summary', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_nic: studentNic,
            course_id: courseId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayPaymentSummary(data.summary);
            document.getElementById('paymentSummarySection').style.display = 'block';
            showSuccessMessage('Payment summary generated successfully!');
        } else {
            showErrorMessage(data.message || 'Failed to generate payment summary.');
            document.getElementById('paymentSummarySection').style.display = 'none';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while generating payment summary.');
        document.getElementById('paymentSummarySection').style.display = 'none';
    })
    .finally(() => showSpinner(false));
}

// Display payment summary
function displayPaymentSummary(summary) {
    console.log('displayPaymentSummary called with summary:', summary);
    
    // Display student information
    document.getElementById('summary-student-id').textContent = summary.student.student_id || 'N/A';
    document.getElementById('summary-student-name').textContent = summary.student.student_name || 'N/A';
    document.getElementById('summary-course-name').textContent = summary.student.course_name || 'N/A';
    document.getElementById('summary-registration-date').textContent = summary.student.registration_date || 'N/A';
    document.getElementById('summary-total-course-fee').textContent = 'LKR ' + parseFloat(summary.student.total_amount || 0).toLocaleString();
    document.getElementById('summary-total-paid').textContent = 'LKR ' + parseFloat(summary.total_paid || 0).toLocaleString();

    // Update summary cards
    document.getElementById('total-amount').textContent = 'LKR ' + parseFloat(summary.total_amount || 0).toLocaleString();
    document.getElementById('total-paid').textContent = 'LKR ' + parseFloat(summary.total_paid || 0).toLocaleString();
    document.getElementById('total-outstanding').textContent = 'LKR ' + parseFloat(summary.total_outstanding || 0).toLocaleString();
    document.getElementById('payment-rate').textContent = (summary.payment_rate || 0) + '%';

    // Populate separate tables for each payment type
    populatePaymentTypeTable('courseFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Course Fee') || {});
    populatePaymentTypeTable('franchiseFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Franchise Fee') || {});
    populatePaymentTypeTable('registrationFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Registration Fee') || {});
    populatePaymentTypeTable('hostelFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Hostel Fee') || {});
    populatePaymentTypeTable('libraryFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Library Fee') || {});
    populatePaymentTypeTable('otherFeeTableBody', summary.payment_details.find(d => d.payment_type === 'Other') || {});
}

// Populate individual payment type table
function populatePaymentTypeTable(tableId, paymentData) {
    const tbody = document.getElementById(tableId);
    tbody.innerHTML = '';
    
    if (!paymentData || !paymentData.payments || paymentData.payments.length === 0) {
        tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">No data available</td></tr>';
        return;
    }
    
    // Sort payments by date (most recent first)
    const sortedPayments = paymentData.payments.sort((a, b) => new Date(b.payment_date) - new Date(a.payment_date));
    
    sortedPayments.forEach(payment => {
        const row = `<tr>
            <td>LKR ${parseFloat(payment.total_amount || 0).toLocaleString()}</td>
            <td>LKR ${parseFloat(payment.paid_amount || 0).toLocaleString()}</td>
            <td>LKR ${parseFloat(payment.outstanding || 0).toLocaleString()}</td>
            <td>${payment.payment_date || 'N/A'}</td>
            <td>${payment.due_date || 'N/A'}</td>
            <td>${payment.receipt_no || 'N/A'}</td>
            <td>
                ${payment.uploaded_receipt ? 
                    `<a href="/storage/${payment.uploaded_receipt}" target="_blank" class="btn btn-sm btn-outline-primary">
                        <i class="ti ti-download me-1"></i>View
                    </a>` : 
                    '<span class="text-muted">Not uploaded</span>'
                }
            </td>
            <td>${payment.installment_number || 'N/A'}</td>
        </tr>`;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Export payment summary
function exportPaymentSummary(format) {
    showToast('Info', `${format.toUpperCase()} export functionality will be implemented soon.`, 'bg-info');
}

// Placeholder functions for editing and deleting
function editPaymentPlan(index) {
    showToast('Info', 'Edit payment plan functionality will be implemented soon.', 'bg-info');
}

function deletePaymentPlan(index) {
    if (confirm('Are you sure you want to delete this payment plan?')) {
        paymentPlans.splice(index, 1);
        renderPaymentPlans();
        showToast('Success', 'Payment plan deleted successfully.', 'bg-success');
    }
}

function viewPaymentHistory(index) {
    const plan = paymentPlans[index];
    showToast('Info', `Viewing payment history for ${plan.student_name} (${plan.student_nic})`, 'bg-info');
    // This would open a modal or navigate to payment history page
}

function editPaymentRecord(index) {
    showToast('Info', 'Edit payment record functionality will be implemented soon.', 'bg-info');
}

function deletePaymentRecord(index) {
    if (confirm('Are you sure you want to delete this payment record?')) {
        paymentRecords.splice(index, 1);
        renderPaymentRecords();
        showToast('Success', 'Payment record deleted successfully.', 'bg-success');
    }
}

function viewPaymentDetails(index) {
    showToast('Info', 'View payment details functionality will be implemented soon.', 'bg-info');
}

// Initialize page
document.addEventListener('DOMContentLoaded', function() {
    // Add event listener for NIC field to filter courses
    const studentNicField = document.getElementById('plan-student-nic');
    if (studentNicField) {
        studentNicField.addEventListener('input', function() {
            const nicValue = this.value.trim();
            
            // Wait for complete NIC number (assuming NIC is 10-12 characters)
            if (nicValue.length >= 10) {
                // Add a small delay to avoid too many API calls while typing
                clearTimeout(this.timeout);
                this.timeout = setTimeout(() => {
                    loadCoursesForStudent();
                }, 1000); // 1 second delay after complete NIC
            }
        });
    }
    
    // Add event listeners for payment plan form fields
    const paymentPlanFields = ['payment-plan-type'];
    paymentPlanFields.forEach(fieldId => {
        const field = document.getElementById(fieldId);
        if (field) {
            field.addEventListener('change', calculateInstallments);
        }
    });
    
    // Load discounts when page loads
    loadDiscounts();
    
    // Add event listener for discount type
    const discountTypeField = document.getElementById('discount-type');
    if (discountTypeField) {
        discountTypeField.addEventListener('change', calculateFinalAmount);
    }
    
    // Add event listener for SLT loan applied
    const sltLoanAppliedField = document.getElementById('slt-loan-applied');
    if (sltLoanAppliedField) {
        sltLoanAppliedField.addEventListener('change', function() {
            const sltLoanAmountField = document.getElementById('slt-loan-amount');
            if (this.value === 'yes') {
                sltLoanAmountField.disabled = false;
                sltLoanAmountField.required = true;
            } else {
                sltLoanAmountField.disabled = true;
                sltLoanAmountField.required = false;
                sltLoanAmountField.value = '';
            }
            calculateFinalAmount();
        });
    }
    
    // Add event listener for SLT loan amount
    const sltLoanAmountField = document.getElementById('slt-loan-amount');
    if (sltLoanAmountField) {
        sltLoanAmountField.addEventListener('input', calculateFinalAmount);
    }
    
    // Add event listener for course selection
    const courseSelect = document.getElementById('plan-course');
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
            const studentNic = document.getElementById('plan-student-nic').value;
            const courseId = this.value;
            
            if (studentNic && courseId) {
                loadStudentForPaymentPlan();
            }
        });
    }

    // Add discount functionality
    document.getElementById('add-discount-btn').addEventListener('click', function() {
        addDiscountField();
    });

    // Add event listeners for form changes
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('discount-select')) {
            calculateFinalAmount();
            // Recalculate installments when discounts change
            if (window.currentStudentData) {
                calculateAndDisplayInstallments();
            }
        }
        
        // Recalculate installments when payment plan type changes
        if (e.target.id === 'payment-plan-type') {
            if (window.currentStudentData) {
                calculateAndDisplayInstallments();
            }
        }
        
        // Recalculate installments when SLT loan changes
        if (e.target.id === 'slt-loan-applied' || e.target.id === 'slt-loan-amount') {
            calculateFinalAmount();
            if (window.currentStudentData) {
                calculateAndDisplayInstallments();
            }
        }
    });

    // Add discount field function
    function addDiscountField() {
        const container = document.getElementById('discounts-container');
        const discountItem = document.createElement('div');
        discountItem.className = 'discount-item mb-2 d-flex align-items-center';
        
        // Get the first discount select to clone its options
        const firstSelect = document.querySelector('.discount-select');
        const options = firstSelect.innerHTML;
        
        discountItem.innerHTML = `
            <select class="form-select discount-select me-2" name="discounts[]">
                ${options}
            </select>
            <button type="button" class="btn btn-sm btn-outline-danger remove-discount-btn">
                <i class="ti ti-trash"></i>
            </button>
        `;
        
        container.appendChild(discountItem);
        
        // Add event listener to remove button
        discountItem.querySelector('.remove-discount-btn').addEventListener('click', function() {
            container.removeChild(discountItem);
            calculateFinalAmount();
            // Recalculate installments when discount is removed
            if (window.currentStudentData) {
                calculateAndDisplayInstallments();
            }
        });
    }
    
    // Add event listeners for filter changes
    const filterSelects = document.querySelectorAll('.filter-param');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            // Reset dependent dropdowns
            const dependentSelects = this.parentElement.parentElement.nextElementSibling?.querySelectorAll('.filter-param');
            if (dependentSelects) {
                dependentSelects.forEach(depSelect => {
                    depSelect.innerHTML = '<option selected disabled value="">Select...</option>';
                });
            }
        });
    });
});

// Load payment details when payment type is selected
function loadPaymentDetails() {
    const studentId = document.getElementById('slip-student-id').value;
    const paymentType = document.getElementById('slip-payment-type').value;
    
    if (!studentId || !paymentType) {
        document.getElementById('paymentDetailsSection').style.display = 'none';
        return;
    }
    
    showSpinner(true);
    
    fetch('/payment/get-payment-details', {
        method: 'POST',
        headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({
            student_id: studentId,
            payment_type: paymentType
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.paymentDetailsData = data.payment_details; // Store globally
            displayPaymentDetails(data.payment_details);
            document.getElementById('paymentDetailsSection').style.display = 'block';
        } else {
            showErrorMessage(data.message || 'Failed to load payment details.');
            document.getElementById('paymentDetailsSection').style.display = 'none';
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while loading payment details.');
        document.getElementById('paymentDetailsSection').style.display = 'none';
    })
    .finally(() => showSpinner(false));
}

// Display payment details in the table
function displayPaymentDetails(paymentDetails) {
    const tbody = document.getElementById('paymentDetailsTableBody');
    tbody.innerHTML = '';
    
    paymentDetails.forEach((payment, index) => {
        const row = `
            <tr>
                <td>
                    <input type="radio" name="selectedPayment" value="${index}" onchange="enableGenerateButton()">
                </td>
                <td>${payment.installment_number || '-'}</td>
                <td>${payment.due_date ? new Date(payment.due_date).toLocaleDateString() : '-'}</td>
                <td>LKR ${parseFloat(payment.amount).toLocaleString()}</td>
                <td>${payment.paid_date ? new Date(payment.paid_date).toLocaleDateString() : '-'}</td>
                <td>
                    <span class="badge bg-${getPaymentStatusBadgeColor(payment.status)}">
                        ${payment.status}
                    </span>
                </td>
                <td>${payment.receipt_no || '-'}</td>
            </tr>
        `;
        tbody.insertAdjacentHTML('beforeend', row);
    });
}

// Enable generate button when a payment is selected
function enableGenerateButton() {
    const selectedPayment = document.querySelector('input[name="selectedPayment"]:checked');
    const generateBtn = document.getElementById('generateSlipBtn');
    
    if (selectedPayment) {
        generateBtn.disabled = false;
    } else {
        generateBtn.disabled = true;
    }
}

// Get badge color for payment status
function getPaymentStatusBadgeColor(status) {
    switch (status.toLowerCase()) {
        case 'paid':
            return 'success';
        case 'pending':
            return 'warning';
        case 'overdue':
            return 'danger';
        default:
            return 'secondary';
    }
}

// Upload paid slip function
function uploadPaidSlip() {
    const receiptNo = document.getElementById('upload-receipt-no').value;
    const paymentMethod = document.getElementById('upload-payment-method').value;
    const paymentDate = document.getElementById('upload-payment-date').value;
    const paidSlip = document.getElementById('upload-paid-slip').files[0];
    const remarks = document.getElementById('upload-remarks').value;

    if (!receiptNo || !paymentMethod || !paymentDate || !paidSlip) {
        showWarningMessage('Please fill in all required fields.');
        return;
    }

    // Validate file size (2MB max)
    if (paidSlip.size > 2 * 1024 * 1024) {
        showErrorMessage('File size must be less than 2MB.');
        return;
    }

    showSpinner(true);

    const formData = new FormData();
    formData.append('receipt_no', receiptNo);
    formData.append('payment_method', paymentMethod);
    formData.append('payment_date', paymentDate);
    formData.append('paid_slip', paidSlip);
    formData.append('remarks', remarks);
    formData.append('_token', '<?php echo e(csrf_token()); ?>');

    fetch('/payment/upload-paid-slip', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showSuccessMessage(data.message || 'Paid slip uploaded successfully! 🎉');
            
            // Clear form
            document.getElementById('upload-receipt-no').value = '';
            document.getElementById('upload-payment-method').value = '';
            document.getElementById('upload-payment-date').value = '';
            document.getElementById('upload-paid-slip').value = '';
            document.getElementById('upload-remarks').value = '';
            
            // Reload payment records if they are currently displayed
            if (document.getElementById('paymentRecordsSection').style.display !== 'none') {
                loadPaymentRecords();
            }
        } else {
            showErrorMessage(data.message || 'Failed to upload paid slip.');
        }
    })
    .catch(() => {
        showErrorMessage('An error occurred while uploading the paid slip.');
    })
    .finally(() => showSpinner(false));
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\JOB\Projects\Nebula\resources\views/payment.blade.php ENDPATH**/ ?>