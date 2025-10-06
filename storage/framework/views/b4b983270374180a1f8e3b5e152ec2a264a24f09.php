

<?php $__env->startSection('title', 'Student Payment Summary - ' . $studentId); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4 mb-5">
    
    <div class="card border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="card-body text-white py-4">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="mb-2">
                        <i class="bi bi-person-circle"></i> Student Payment Dashboard
                    </h3>
                    <p class="mb-0 opacity-75">
                        Student ID: <strong class="fs-5"><?php echo e($studentId); ?></strong>
                        <?php if($student): ?>
                            - <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?>

                        <?php endif; ?>
                    </p>
                </div>
                <div class="text-end">
                    <a href="<?php echo e(route('payment.summary')); ?>" class="btn btn-light">
                        <i class="bi bi-arrow-left"></i> Back to Dashboard
                    </a>
                    <button class="btn btn-outline-light ms-2" onclick="printReport()">
                        <i class="bi bi-printer"></i> Print
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">Time Range</label>
                    <select class="form-select" id="rangeFilter" onchange="filterData()">
                        <option value="all">All Time</option>
                        <option value="1m">Last Month</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="6m">Last 6 Months</option>
                        <option value="1y" selected>Last Year</option>
                        <option value="2y">Last 2 Years</option>
                    </select>
                </div>
                <div class="col-md-8 text-end">
                    <button class="btn btn-success" onclick="exportStudentData()">
                        <i class="bi bi-download"></i> Export Report
                    </button>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Total Collected</p>
                            <h4 class="fw-bold text-success mb-0">
                                LKR <?php echo e(number_format($totalCollected, 2)); ?>

                            </h4>
                        </div>
                        <div class="bg-success bg-opacity-10 p-3 rounded">
                            <i class="bi bi-cash-stack text-success fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Pending Amount</p>
                            <h4 class="fw-bold text-warning mb-0">
                                LKR <?php echo e(number_format($totalPending, 2)); ?>

                            </h4>
                        </div>
                        <div class="bg-warning bg-opacity-10 p-3 rounded">
                            <i class="bi bi-hourglass-split text-warning fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Late Fees</p>
                            <h4 class="fw-bold text-danger mb-0">
                                LKR <?php echo e(number_format($totalLateFee, 2)); ?>

                            </h4>
                        </div>
                        <div class="bg-danger bg-opacity-10 p-3 rounded">
                            <i class="bi bi-exclamation-triangle text-danger fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <p class="text-muted mb-1 small">Discounts Received</p>
                            <h4 class="fw-bold text-info mb-0">
                                LKR <?php echo e(number_format($totalDiscount, 2)); ?>

                            </h4>
                        </div>
                        <div class="bg-info bg-opacity-10 p-3 rounded">
                            <i class="bi bi-tag text-info fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-check-circle text-primary fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Approved Late Fees</h6>
                    <h5 class="fw-bold">LKR <?php echo e(number_format($approvedLateFees ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-currency-exchange text-success fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Foreign Currency</h6>
                    <h5 class="fw-bold"><?php echo e(number_format($foreignCurrencyTotal ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-percent text-info fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">SSCL Tax</h6>
                    <h5 class="fw-bold">LKR <?php echo e(number_format($ssclTaxTotal ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <i class="bi bi-bank text-warning fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Bank Charges</h6>
                    <h5 class="fw-bold">LKR <?php echo e(number_format($bankChargesTotal ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📊 Payment Trend Over Time</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">💳 Payment Methods</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodChart" height="200"></canvas>
                    <div class="mt-3">
                        <?php $__currentLoopData = $paymentByMethod; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted"><?php echo e(ucfirst($method->payment_method ?? 'Unknown')); ?></span>
                                <span class="fw-bold"><?php echo e($method->count); ?> txns</span>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📋 Payment Types Breakdown</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📈 Payment Status Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                    <div class="mt-3">
                        <?php $__currentLoopData = $paymentByStatus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <span class="badge bg-<?php echo e($status->status == 'paid' ? 'success' : ($status->status == 'pending' ? 'warning' : 'danger')); ?>">
                                        <?php echo e(ucfirst($status->status)); ?>

                                    </span>
                                </div>
                                <div class="text-end">
                                    <div class="fw-bold">LKR <?php echo e(number_format($status->total, 2)); ?></div>
                                    <small class="text-muted"><?php echo e($status->count); ?> payments</small>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">💰 Payment Method Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Payment Method</th>
                            <th class="text-end">Average Amount</th>
                            <th class="text-end">Maximum Amount</th>
                            <th class="text-end">Minimum Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $methodComparison ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $method): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td>
                                    <i class="bi bi-<?php echo e($method->payment_method == 'cash' ? 'cash' : ($method->payment_method == 'card' ? 'credit-card' : 'bank')); ?> me-2"></i>
                                    <?php echo e(ucfirst($method->payment_method ?? 'Unknown')); ?>

                                </td>
                                <td class="text-end">LKR <?php echo e(number_format($method->avg_amount, 2)); ?></td>
                                <td class="text-end">LKR <?php echo e(number_format($method->max_amount, 2)); ?></td>
                                <td class="text-end">LKR <?php echo e(number_format($method->min_amount, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="4" class="text-center text-muted">No data available</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">🕐 Complete Payment History</h6>
                <input type="text" class="form-control form-control-sm" id="searchTable" 
                       placeholder="Search payments..." style="max-width: 250px;">
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="paymentTable">
                    <thead class="table-light">
                        <tr>
                            <th width="50">#</th>
                            <th>Transaction ID</th>
                            <th>Payment Type</th>
                            <th>Method</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Late Fee</th>
                            <th class="text-end">Remaining</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $paymentRecords; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i + 1); ?></td>
                                <td>
                                    <small class="font-monospace text-primary">
                                        <?php echo e($payment->transaction_id); ?>

                                    </small>
                                </td>
                                <td>
                                    <?php if(is_null($payment->installment_type) && !is_null($payment->misc_category)): ?>
                                        <span class="badge bg-secondary">
                                            Misc: <?php echo e(ucfirst($payment->misc_category)); ?>

                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $payment->installment_type ?? 'Unknown'))); ?>

                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="bi bi-<?php echo e($payment->payment_method == 'cash' ? 'cash' : ($payment->payment_method == 'card' ? 'credit-card' : 'bank')); ?>"></i>
                                    <?php echo e(ucfirst($payment->payment_method ?? '-')); ?>

                                </td>
                                <td class="text-end fw-bold">
                                    LKR <?php echo e(number_format($payment->total_fee, 2)); ?>

                                </td>
                                <td class="text-end text-danger">
                                    <?php if($payment->late_fee > 0): ?>
                                        LKR <?php echo e(number_format($payment->late_fee, 2)); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-end text-warning">
                                    <?php if($payment->remaining_amount > 0): ?>
                                        LKR <?php echo e(number_format($payment->remaining_amount, 2)); ?>

                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger')); ?>">
                                        <?php echo e(ucfirst($payment->status)); ?>

                                    </span>
                                </td>
                                <td>
                                    <small><?php echo e(\Carbon\Carbon::parse($payment->created_at)->format('M d, Y')); ?></small>
                                    <br>
                                    <small class="text-muted"><?php echo e(\Carbon\Carbon::parse($payment->created_at)->format('h:i A')); ?></small>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-info" 
                                            onclick="viewPaymentDetails(<?php echo e($payment->id); ?>)"
                                            data-bs-toggle="tooltip" title="View Details">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="10" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                    No payment records found
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>


<div class="modal fade" id="paymentDetailsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Payment Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetailsContent">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const paymentByMethod = <?php echo json_encode($paymentByMethod, 15, 512) ?>;
    const paymentByType = <?php echo json_encode($paymentByType, 15, 512) ?>;
    const paymentByStatus = <?php echo json_encode($paymentByStatus, 15, 512) ?>;
    const monthlyIncome = <?php echo json_encode($monthlyIncome, 15, 512) ?>;

    // Chart.js Configuration
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // ---- Monthly Trend (Line + Area) ----
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyIncome.map(p => p.month),
            datasets: [{
                label: 'Paid',
                data: monthlyIncome.map(p => p.paid),
                borderColor: '#1cc88a',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6
            }, {
                label: 'Pending',
                data: monthlyIncome.map(p => p.pending),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            interaction: {
                mode: 'index',
                intersect: false,
            },
            plugins: {
                legend: {
                    position: 'top',
                    labels: { usePointStyle: true, padding: 20 }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            return context.dataset.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return 'LKR ' + new Intl.NumberFormat().format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });

    // ---- Payment Methods (Doughnut) ----
    new Chart(document.getElementById('methodChart'), {
        type: 'doughnut',
        data: {
            labels: paymentByMethod.map(p => {
                const methods = {
                    'cash': 'Cash',
                    'cheque': 'Cheque',
                    'bank_transfer': 'Bank Transfer',
                    'online': 'Online',
                    'card': 'Card'
                };
                return methods[p.payment_method] || 'Unknown';
            }),
            datasets: [{
                data: paymentByMethod.map(p => p.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '65%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed);
                        }
                    }
                }
            }
        }
    });

    // ---- Payment Types (Bar) ----
    new Chart(document.getElementById('typeChart'), {
        type: 'bar',
        data: {
            labels: paymentByType.map(p => p.type || 'Unknown'),
            datasets: [{
                label: 'Amount',
                data: paymentByType.map(p => p.total),
                backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#20c997'],
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            indexAxis: 'y',
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'LKR ' + new Intl.NumberFormat().format(context.parsed.x);
                        }
                    }
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                y: {
                    grid: { display: false }
                }
            }
        }
    });

    // ---- Payment Status (Pie) ----
    new Chart(document.getElementById('statusChart'), {
        type: 'pie',
        data: {
            labels: paymentByStatus.map(p => p.status ? p.status.charAt(0).toUpperCase() + p.status.slice(1) : 'Unknown'),
            datasets: [{
                data: paymentByStatus.map(p => p.total),
                backgroundColor: ['#1cc88a', '#f6c23e', '#e74a3b', '#858796'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 15, usePointStyle: true }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((context.parsed / total) * 100).toFixed(1);
                            return context.label + ': LKR ' + 
                                   new Intl.NumberFormat().format(context.parsed) + 
                                   ' (' + percentage + '%)';
                        }
                    }
                }
            }
        }
    });

    // Initialize Bootstrap tooltips
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Filter Data
function filterData() {
    const range = document.getElementById('rangeFilter').value;
    window.location.href = `<?php echo e(route('payment.summary.student', $studentId)); ?>?range=${range}`;
}

// Search Table
document.getElementById('searchTable').addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const tableRows = document.querySelectorAll('#paymentTable tbody tr');
    
    tableRows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// View Payment Details
function viewPaymentDetails(paymentId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentDetailsModal'));
    modal.show();
    
    // Simulate loading payment details (replace with actual AJAX call)
    setTimeout(() => {
        document.getElementById('paymentDetailsContent').innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>Payment ID:</strong> ${paymentId}</p>
                    <p><strong>Student ID:</strong> <?php echo e($studentId); ?></p>
                </div>
                <div class="col-md-6">
                    <p><strong>Status:</strong> <span class="badge bg-success">Paid</span></p>
                    <p><strong>Date:</strong> ${new Date().toLocaleDateString()}</p>
                </div>
            </div>
            <hr>
            <p class="text-muted">Full payment details would be loaded here via AJAX.</p>
        `;
    }, 500);
}

// Print Report
function printReport() {
    window.print();
}

// Export Student Data
function exportStudentData() {
    const range = document.getElementById('rangeFilter').value;
    window.location.href = `<?php echo e(route('payment.export')); ?>?format=csv&range=${range}&student_id=<?php echo e($studentId); ?>`;
}
</script>

<style>
@media print {
    .btn, .card-header, .filter-section {
        display: none !important;
    }
}

.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
    cursor: pointer;
}

.font-monospace {
    font-family: 'Courier New', monospace;
}

.badge {
    font-weight: 500;
    padding: 0.4em 0.8em;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/payment/student_summary.blade.php ENDPATH**/ ?>