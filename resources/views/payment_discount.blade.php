@extends('inc.app')

@section('title', 'NEBULA | Payment Discount')

@section('content')

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
    color: #9ca3af;
    cursor: pointer;
    padding: 4px;
    border-radius: 4px;
    transition: color 0.2s;
}

.toast-close:hover {
    color: #6b7280;
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
</style>

<!-- Toast Container -->
<div class="toast-container" id="toastContainer"></div>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Payment Discount</h2>
            <hr>
            <ul class="nav nav-tabs mb-4" id="discountTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="slt-loan-tab" data-bs-toggle="tab" data-bs-target="#slt-loan" type="button" role="tab" aria-controls="slt-loan" aria-selected="true">SLT Loan</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="local-course-discounts-tab" data-bs-toggle="tab" data-bs-target="#local-course-discounts" type="button" role="tab" aria-controls="local-course-discounts" aria-selected="false">Discounts for Local Course Fee</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="registration-discounts-tab" data-bs-toggle="tab" data-bs-target="#registration-discounts" type="button" role="tab" aria-controls="registration-discounts" aria-selected="false">Discounts for Registration Fee</button>
                </li>
            </ul>
            <div class="tab-content" id="discountTabsContent">
                <!-- SLT Loan Tab -->
                <div class="tab-pane fade show active" id="slt-loan" role="tabpanel" aria-labelledby="slt-loan-tab">
                    <form id="slt-loan-form">
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">SLT Loan Amount<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <span class="input-group-text">LKR</span>
                                    <input type="number" class="form-control" id="sltLoanAmount" name="slt_loan_amount" min="0" step="0.01" required>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">No. of Installments<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="sltInstallments" name="slt_installments" min="1" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <div class="alert alert-info" id="sltLoanSummary" style="display:none;"></div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12">
                                <h5>Local Fee Installments</h5>
                                <div class="table-responsive">
                                    <table class="table table-bordered" id="sltInstallmentsTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Installment No</th>
                                                <th>Local Fee Amount</th>
                                                <th>Due Date</th>
                                                <th>Amount Should Be Paid</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <!-- Populated by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                
                <!-- Local Course Fee Discounts Tab -->
                <div class="tab-pane fade" id="local-course-discounts" role="tabpanel" aria-labelledby="local-course-discounts-tab">
                    <form id="local-course-discount-form">
                        <input type="hidden" id="localCourseDiscountCategory" value="local_course_fee">
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">Name of Discount<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="localCourseDiscountName" name="discount_name" required>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">Discount Type<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" id="localCourseDiscountType" name="discount_type" required>
                                    <option value="">Select Type</option>
                                    <option value="amount">Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold" id="localCourseDiscountValueLabel">Amount<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="localCourseDiscountValue" name="discount_value" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-success" id="addLocalCourseDiscount">
                                    <i class="ti ti-plus"></i> Add Discount
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Local Course Fee Discounts Table -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <h5>Created Discounts for Local Course Fee</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="localCourseDiscountsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Discount Name</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Populated by JavaScript -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Registration Fee Discounts Tab -->
                <div class="tab-pane fade" id="registration-discounts" role="tabpanel" aria-labelledby="registration-discounts-tab">
                    <form id="registration-discount-form">
                        <input type="hidden" id="registrationDiscountCategory" value="registration_fee">
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">Name of Discount<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="registrationDiscountName" name="discount_name" required>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold">Discount Type<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select" id="registrationDiscountType" name="discount_type" required>
                                    <option value="">Select Type</option>
                                    <option value="amount">Amount</option>
                                    <option value="percentage">Percentage</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <label class="col-sm-3 col-form-label fw-bold" id="registrationDiscountValueLabel">Amount<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="number" class="form-control" id="registrationDiscountValue" name="discount_value" min="0" step="0.01" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-sm-12 text-center">
                                <button type="button" class="btn btn-success" id="addRegistrationDiscount">
                                    <i class="ti ti-plus"></i> Add Discount
                                </button>
                            </div>
                        </div>
                    </form>
                    
                    <!-- Registration Fee Discounts Table -->
                    <div class="row mb-3">
                        <div class="col-sm-12">
                            <h5>Created Discounts for Registration Fee</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover" id="registrationDiscountsTable">
                                    <thead class="table-light">
                                        <tr>
                                            <th>#</th>
                                            <th>Discount Name</th>
                                            <th>Type</th>
                                            <th>Value</th>
                                            <th>Created Date</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Populated by JavaScript -->
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
<script>
$(document).ready(function() {
    // Auto-calculate when both fields are filled
    $('#sltLoanAmount, #sltInstallments').on('input change', function() {
        const loanAmount = parseFloat($('#sltLoanAmount').val()) || 0;
        const numInstallments = parseInt($('#sltInstallments').val()) || 0;
        
        // Auto-calculate if both fields have values
        if (loanAmount > 0 && numInstallments > 0) {
            updateSltLoanSummaryAndTable();
        } else {
            // Clear table and summary if fields are empty
            $('#sltLoanSummary').hide();
            $('#sltInstallmentsTable tbody').empty();
        }
    });

    function updateSltLoanSummaryAndTable() {
        const loanAmount = parseFloat($('#sltLoanAmount').val()) || 0;
        const numInstallments = parseInt($('#sltInstallments').val()) || 0;

        // Calculations
        if (loanAmount > 0 && numInstallments > 0) {
            const loanInstallment = loanAmount / numInstallments;
            
            // Create summary
            const summary = `<b>Loan Amount:</b> LKR ${loanAmount.toFixed(2)}<br>` +
                           `<b>Number of Installments:</b> ${numInstallments}<br>` +
                           `<b>Loan Installment Amount:</b> LKR ${loanInstallment.toFixed(2)}`;
            $('#sltLoanSummary').html(summary).show();

            // Generate installment table
            let tableRows = '';
            const currentDate = new Date();
            
            for (let i = 1; i <= numInstallments; i++) {
                // Calculate due date (3 months apart)
                const dueDate = new Date(currentDate);
                dueDate.setMonth(dueDate.getMonth() + (i * 3));
                const dueDateStr = dueDate.toISOString().split('T')[0];
                
                // For demo purposes, assume each installment is equal
                const installmentAmount = loanInstallment;
                
                tableRows += `<tr>
                    <td>${i}</td>
                    <td>LKR ${installmentAmount.toFixed(2)}</td>
                    <td>${dueDateStr}</td>
                    <td>LKR ${installmentAmount.toFixed(2)}</td>
                </tr>`;
            }
            
            $('#sltInstallmentsTable tbody').html(tableRows);
        } else {
            $('#sltLoanSummary').hide();
            $('#sltInstallmentsTable tbody').empty();
        }
    }

    // Local Course Fee Discount tab: change label based on type
    $('#localCourseDiscountType').on('change', function() {
        const type = $(this).val();
        $('#localCourseDiscountValueLabel').text(type === 'percentage' ? 'Percentage' : 'Amount');
    });

    // Registration Fee Discount tab: change label based on type
    $('#registrationDiscountType').on('change', function() {
        const type = $(this).val();
        $('#registrationDiscountValueLabel').text(type === 'percentage' ? 'Percentage' : 'Amount');
    });

    // Load discounts from database when page loads
    loadLocalCourseDiscountsFromDatabase();
    loadRegistrationDiscountsFromDatabase();

    // Add/Update Local Course Fee discount functionality
    $('#addLocalCourseDiscount').on('click', function() {
        const discountName = $('#localCourseDiscountName').val().trim();
        const discountType = $('#localCourseDiscountType').val();
        const discountValue = parseFloat($('#localCourseDiscountValue').val()) || 0;
        const discountCategory = $('#localCourseDiscountCategory').val();
        const editId = $(this).attr('data-edit-id');

        // Validation
        if (!discountName) {
            alert('Please enter a discount name.');
            return;
        }
        if (!discountType) {
            alert('Please select a discount type.');
            return;
        }
        if (discountValue <= 0) {
            alert('Please enter a valid discount value.');
            return;
        }

        if (editId) {
            // Update existing discount
            updateDiscountInDatabase(editId, discountName, discountType, discountValue, discountCategory, 'local');
        } else {
            // Save new discount to database
            saveDiscountToDatabase(discountName, discountType, discountValue, discountCategory, 'local');
        }
    });

    // Add/Update Registration Fee discount functionality
    $('#addRegistrationDiscount').on('click', function() {
        const discountName = $('#registrationDiscountName').val().trim();
        const discountType = $('#registrationDiscountType').val();
        const discountValue = parseFloat($('#registrationDiscountValue').val()) || 0;
        const discountCategory = $('#registrationDiscountCategory').val();
        const editId = $(this).attr('data-edit-id');

        // Validation
        if (!discountName) {
            alert('Please enter a discount name.');
            return;
        }
        if (!discountType) {
            alert('Please select a discount type.');
            return;
        }
        if (discountValue <= 0) {
            alert('Please enter a valid discount value.');
            return;
        }

        if (editId) {
            // Update existing discount
            updateDiscountInDatabase(editId, discountName, discountType, discountValue, discountCategory, 'registration');
        } else {
            // Save new discount to database
            saveDiscountToDatabase(discountName, discountType, discountValue, discountCategory, 'registration');
        }
    });

    // Load Local Course Fee discounts from database
    function loadLocalCourseDiscountsFromDatabase() {
        fetch('/payment-discount/get-discounts-by-category', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                category: 'local_course_fee'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLocalCourseDiscountsTable(data.discounts);
            } else {
                console.error('Failed to load local course fee discounts:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading local course fee discounts:', error);
        });
    }

    // Load Registration Fee discounts from database
    function loadRegistrationDiscountsFromDatabase() {
        fetch('/payment-discount/get-discounts-by-category', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                category: 'registration_fee'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateRegistrationDiscountsTable(data.discounts);
            } else {
                console.error('Failed to load registration fee discounts:', data.message);
            }
        })
        .catch(error => {
            console.error('Error loading registration fee discounts:', error);
        });
    }

    // Save discount to database
    function saveDiscountToDatabase(name, type, value, category, tabType) {
        fetch('/payment-discount/save-discount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                name: name,
                type: type,
                discount_category: category,
                value: value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Discount saved successfully! 🎉');
                if (tabType === 'local') {
                    loadLocalCourseDiscountsFromDatabase();
                    $('#local-course-discount-form')[0].reset();
                    $('#localCourseDiscountValueLabel').text('Amount');
                } else {
                    loadRegistrationDiscountsFromDatabase();
                    $('#registration-discount-form')[0].reset();
                    $('#registrationDiscountValueLabel').text('Amount');
                }
            } else {
                showErrorMessage('Error saving discount: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error saving discount:', error);
            showErrorMessage('Error saving discount. Please try again.');
        });
    }

    // Update discount in database
    function updateDiscountInDatabase(id, name, type, value, category, tabType) {
        fetch('/payment-discount/update-discount', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            body: JSON.stringify({
                id: id,
                name: name,
                type: type,
                discount_category: category,
                value: value
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage('Discount updated successfully! ✨');
                if (tabType === 'local') {
                    loadLocalCourseDiscountsFromDatabase();
                    $('#local-course-discount-form')[0].reset();
                    $('#localCourseDiscountValueLabel').text('Amount');
                    $('#addLocalCourseDiscount').text('Add Discount').removeAttr('data-edit-id');
                } else {
                    loadRegistrationDiscountsFromDatabase();
                    $('#registration-discount-form')[0].reset();
                    $('#registrationDiscountValueLabel').text('Amount');
                    $('#addRegistrationDiscount').text('Add Discount').removeAttr('data-edit-id');
                }
            } else {
                showErrorMessage('Error updating discount: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error updating discount:', error);
            showErrorMessage('Error updating discount. Please try again.');
        });
    }

    function updateLocalCourseDiscountsTable(discounts) {
        let tableRows = '';
        discounts.forEach((discount, index) => {
            const valueDisplay = discount.type === 'percentage' ? 
                `${discount.value}%` : 
                `LKR ${discount.value.toFixed(2)}`;
            
            tableRows += `<tr>
                <td>${index + 1}</td>
                <td>${discount.name}</td>
                <td><span class="badge bg-${discount.type === 'percentage' ? 'info' : 'primary'}">${discount.type}</span></td>
                <td>${valueDisplay}</td>
                <td>${new Date(discount.created_at).toLocaleDateString()}</td>
                <td><span class="badge bg-success">${discount.status}</span></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning edit-local-course-discount" data-id="${discount.id}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-local-course-discount" data-id="${discount.id}">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#localCourseDiscountsTable tbody').html(tableRows);
    }

    function updateRegistrationDiscountsTable(discounts) {
        let tableRows = '';
        discounts.forEach((discount, index) => {
            const valueDisplay = discount.type === 'percentage' ? 
                `${discount.value}%` : 
                `LKR ${discount.value.toFixed(2)}`;
            
            tableRows += `<tr>
                <td>${index + 1}</td>
                <td>${discount.name}</td>
                <td><span class="badge bg-${discount.type === 'percentage' ? 'info' : 'primary'}">${discount.type}</span></td>
                <td>${valueDisplay}</td>
                <td>${new Date(discount.created_at).toLocaleDateString()}</td>
                <td><span class="badge bg-success">${discount.status}</span></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning edit-registration-discount" data-id="${discount.id}">
                        <i class="ti ti-edit"></i>
                    </button>
                    <button type="button" class="btn btn-sm btn-danger delete-registration-discount" data-id="${discount.id}">
                        <i class="ti ti-trash"></i>
                    </button>
                </td>
            </tr>`;
        });
        $('#registrationDiscountsTable tbody').html(tableRows);
    }

    // Edit Local Course Fee discount
    $(document).on('click', '.edit-local-course-discount', function() {
        const discountId = parseInt($(this).data('id'));
        
        // Load discount details for editing
        fetch(`/payment-discount/get-discounts`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const discount = data.discounts.find(d => d.id === discountId);
                if (discount) {
                    $('#localCourseDiscountName').val(discount.name);
                    $('#localCourseDiscountType').val(discount.type);
                    $('#localCourseDiscountValue').val(discount.value);
                    $('#localCourseDiscountValueLabel').text(discount.type === 'percentage' ? 'Percentage' : 'Amount');
                    
                    // Change add button to update button
                    $('#addLocalCourseDiscount').text('Update Discount').attr('data-edit-id', discountId);
                }
            }
        })
        .catch(error => {
            console.error('Error loading discount for edit:', error);
        });
    });

    // Edit Registration Fee discount
    $(document).on('click', '.edit-registration-discount', function() {
        const discountId = parseInt($(this).data('id'));
        
        // Load discount details for editing
        fetch(`/payment-discount/get-discounts`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const discount = data.discounts.find(d => d.id === discountId);
                if (discount) {
                    $('#registrationDiscountName').val(discount.name);
                    $('#registrationDiscountType').val(discount.type);
                    $('#registrationDiscountValue').val(discount.value);
                    $('#registrationDiscountValueLabel').text(discount.type === 'percentage' ? 'Percentage' : 'Amount');
                    
                    // Change add button to update button
                    $('#addRegistrationDiscount').text('Update Discount').attr('data-edit-id', discountId);
                }
            }
        })
        .catch(error => {
            console.error('Error loading discount for edit:', error);
        });
    });

    // Delete Local Course Fee discount
    $(document).on('click', '.delete-local-course-discount', function() {
        const discountId = parseInt($(this).data('id'));
        
        if (confirm('Are you sure you want to delete this discount?')) {
            fetch('/payment-discount/delete-discount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    id: discountId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Discount deleted successfully! 🗑️');
                    loadLocalCourseDiscountsFromDatabase();
                } else {
                    showErrorMessage('Error deleting discount: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting discount:', error);
                showErrorMessage('Error deleting discount. Please try again.');
            });
        }
    });

    // Delete Registration Fee discount
    $(document).on('click', '.delete-registration-discount', function() {
        const discountId = parseInt($(this).data('id'));
        
        if (confirm('Are you sure you want to delete this discount?')) {
            fetch('/payment-discount/delete-discount', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({
                    id: discountId
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showSuccessMessage('Discount deleted successfully! 🗑️');
                    loadRegistrationDiscountsFromDatabase();
                } else {
                    showErrorMessage('Error deleting discount: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error deleting discount:', error);
                showErrorMessage('Error deleting discount. Please try again.');
            });
        }
    });

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
});
</script>
@endsection 