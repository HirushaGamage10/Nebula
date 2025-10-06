

<?php $__env->startSection('title', 'Payment Dashboard - Advanced Analytics'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">💰 Payment Analytics Dashboard</h2>
            <p class="text-muted mb-0">Real-time insights and comprehensive reports</p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?php echo e(route('payment.analytics')); ?>" class="btn btn-outline-primary">
                <i class="bi bi-graph-up"></i> Advanced Analytics
            </a>
            <a href="<?php echo e(route('payment.comparison')); ?>" class="btn btn-outline-info">
                <i class="bi bi-bar-chart"></i> Comparison
            </a>
            <button class="btn btn-success" onclick="exportData()">
                <i class="bi bi-download"></i> Export
            </button>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small text-muted">Time Range</label>
                    <select class="form-select" id="rangeFilter" onchange="applyFilters()">
                        <option value="1w">Last Week</option>
                        <option value="1m">Last Month</option>
                        <option value="3m">Last 3 Months</option>
                        <option value="6m">Last 6 Months</option>
                        <option value="1y" selected>Last Year</option>
                        <option value="2y">Last 2 Years</option>
                        <option value="5y">Last 5 Years</option>
                        <option value="10y">All Time</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Payment Method</label>
                    <select class="form-select" id="methodFilter" onchange="applyFilters()">
                        <option value="">All Methods</option>
                        <option value="cash">Cash</option>
                        <option value="cheque">Cheque</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="online">Online</option>
                        <option value="card">Card</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Status</label>
                    <select class="form-select" id="statusFilter" onchange="applyFilters()">
                        <option value="">All Status</option>
                        <option value="paid">Paid</option>
                        <option value="pending">Pending</option>
                        <option value="failed">Failed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">Student ID</label>
                    <input type="text" class="form-control" id="studentFilter" placeholder="Enter Student ID">
                </div>
            </div>
            <div class="mt-3 text-end">
                <button class="btn btn-sm btn-primary" onclick="applyFilters()">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="resetFilters()">
                    <i class="bi bi-x-circle"></i> Reset
                </button>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4" id="kpiSection">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Total Collected</p>
                            <h3 class="fw-bold mb-0">LKR <?php echo e(number_format($totalCollected, 2)); ?></h3>
                            <small class="opacity-75"><?php echo e($totalTransactions ?? 0); ?> transactions</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-cash-stack fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Pending Payments</p>
                            <h3 class="fw-bold mb-0">LKR <?php echo e(number_format($totalPending, 2)); ?></h3>
                            <small class="opacity-75">Awaiting collection</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-clock-history fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Average Transaction</p>
                            <h3 class="fw-bold mb-0">LKR <?php echo e(number_format($averageTransaction ?? 0, 2)); ?></h3>
                            <small class="opacity-75">Per payment</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-graph-up-arrow fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 bg-gradient" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                <div class="card-body text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="mb-1 opacity-75 small">Late Fees</p>
                            <h3 class="fw-bold mb-0">LKR <?php echo e(number_format($totalLateFee, 2)); ?></h3>
                            <small class="opacity-75">Total penalties</small>
                        </div>
                        <div class="bg-white bg-opacity-25 p-3 rounded">
                            <i class="bi bi-exclamation-triangle fs-4"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-tag text-success fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Total Discounts</h6>
                    <h5 class="fw-bold text-success">LKR <?php echo e(number_format($totalDiscount, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-percent text-info fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">SSCL Tax</h6>
                    <h5 class="fw-bold text-info">LKR <?php echo e(number_format($ssclTaxTotal ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-bank text-warning fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Bank Charges</h6>
                    <h5 class="fw-bold text-warning">LKR <?php echo e(number_format($bankChargesTotal ?? 0, 2)); ?></h5>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="bi bi-currency-exchange text-primary fs-3 mb-2"></i>
                    <h6 class="text-muted mb-1">Transactions</h6>
                    <h5 class="fw-bold text-primary"><?php echo e($totalTransactions ?? 0); ?></h5>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📊 Monthly Collection Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📈 Payment Status</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">💳 Payment Methods</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📋 Payment Types</h6>
                </div>
                <div class="card-body">
                    <canvas id="typeChart" height="200"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📅 Weekly Trend</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">🏆 Top 10 Paying Students</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th width="50">#</th>
                                    <th>Student ID</th>
                                    <th>Payments</th>
                                    <th class="text-end">Total Amount</th>
                                    <th width="80">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $topStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <span class="badge bg-<?php echo e($i < 3 ? 'warning' : 'secondary'); ?>">
                                                <?php echo e($i + 1); ?>

                                            </span>
                                        </td>
                                        <td class="fw-semibold"><?php echo e($student->student_id); ?></td>
                                        <td>
                                            <span class="badge bg-info">
                                                <?php echo e($student->payment_count ?? 0); ?> txns
                                            </span>
                                        </td>
                                        <td class="text-end fw-bold text-success">
                                            LKR <?php echo e(number_format($student->total, 2)); ?>

                                        </td>
                                        <td>
                                            <a href="<?php echo e(route('payment.summary.student', $student->student_id)); ?>" 
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No student data available
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">🕐 Recent Transactions</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-hover mb-0">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th>Student</th>
                                    <th>Type</th>
                                    <th class="text-end">Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $recentPayments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo e(route('payment.summary.student', $payment->student_id)); ?>" 
                                               class="text-decoration-none">
                                                <?php echo e($payment->student_id); ?>

                                            </a>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php if(is_null($payment->installment_type) && !is_null($payment->misc_category)): ?>
                                                    Misc (<?php echo e(ucfirst($payment->misc_category)); ?>)
                                                <?php else: ?>
                                                    <?php echo e(ucfirst($payment->installment_type ?? 'Unknown')); ?>

                                                <?php endif; ?>
                                            </small>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            <?php echo e(number_format($payment->total_fee, 2)); ?>

                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo e($payment->status == 'paid' ? 'success' : ($payment->status == 'pending' ? 'warning' : 'danger')); ?>">
                                                <?php echo e(ucfirst($payment->status)); ?>

                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">
                                                <?php echo e(\Carbon\Carbon::parse($payment->created_at)->format('M d, Y')); ?>

                                            </small>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            No recent payments
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", () => {
    const paymentByMethod = <?php echo json_encode($paymentByMethod, 15, 512) ?>;
    const paymentByType = <?php echo json_encode($paymentByType, 15, 512) ?>;
    const paymentByStatus = <?php echo json_encode($paymentByStatus ?? [], 15, 512) ?>;
    const monthlyIncome = <?php echo json_encode($monthlyIncome, 15, 512) ?>;
    const weeklyTrend = <?php echo json_encode($weeklyTrend ?? [], 15, 512) ?>;

    // Chart.js default options
    Chart.defaults.font.family = "'Inter', sans-serif";
    Chart.defaults.color = '#6c757d';

    // ---- Monthly Collection Trend (Line + Bar) ----
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyIncome.map(p => p.month),
            datasets: [{
                label: 'Paid',
                data: monthlyIncome.map(p => p.paid),
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2
            }, {
                label: 'Pending',
                data: monthlyIncome.map(p => p.pending),
                borderColor: '#f6c23e',
                backgroundColor: 'rgba(246, 194, 62, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 2
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
                    labels: { usePointStyle: true, padding: 15 }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 12,
                    titleFont: { size: 14 },
                    bodyFont: { size: 13 },
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

    // ---- Payment Status (Doughnut) ----
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
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
            cutout: '70%',
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

    // ---- Payment Methods (Pie) ----
    new Chart(document.getElementById('methodChart'), {
        type: 'pie',
        data: {
            labels: paymentByMethod.map(p => {
                const methods = {
                    'cash': 'Cash',
                    'cheque': 'Cheque',
                    'bank_transfer': 'Bank Transfer',
                    'online': 'Online',
                    'card': 'Card'
                };
                return methods[p.payment_method] || p.payment_method || 'Unknown';
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
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, usePointStyle: true, font: { size: 11 } }
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

    // ---- Payment Types (Doughnut) ----
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: paymentByType.map(p => p.type || 'Unknown'),
            datasets: [{
                data: paymentByType.map(p => p.total),
                backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#20c997'],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            cutout: '60%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 10, usePointStyle: true, font: { size: 11 } }
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

    // ---- Weekly Trend (Bar) ----
    new Chart(document.getElementById('weeklyChart'), {
        type: 'bar',
        data: {
            labels: weeklyTrend.map(p => 'Week ' + p.week),
            datasets: [{
                label: 'Weekly Revenue',
                data: weeklyTrend.map(p => p.total),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return 'LKR ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});

// Filter Functions
function applyFilters() {
    const range = document.getElementById('rangeFilter').value;
    const method = document.getElementById('methodFilter').value;
    const status = document.getElementById('statusFilter').value;
    const studentId = document.getElementById('studentFilter').value;

    const params = new URLSearchParams({
        range: range,
        payment_method: method,
        status: status,
        student_id: studentId
    });

    fetch(`<?php echo e(route('payment.summary.filter')); ?>?${params}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        updateDashboard(data);
    })
    .catch(error => console.error('Error:', error));
}

function resetFilters() {
    document.getElementById('rangeFilter').value = '1y';
    document.getElementById('methodFilter').value = '';
    document.getElementById('statusFilter').value = '';
    document.getElementById('studentFilter').value = '';
    location.reload();
}

function updateDashboard(data) {
    // Update KPIs dynamically (you can enhance this)
    location.reload(); // Simple reload for now
}

function exportData() {
    const range = document.getElementById('rangeFilter').value;
    window.location.href = `<?php echo e(route('payment.export')); ?>?format=csv&range=${range}`;
}
</script>

<style>
.bg-gradient {
    background-size: cover;
}

.table-hover tbody tr:hover {
    background-color: rgba(0, 0, 0, 0.02);
}

.card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
}

.sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/payment/summary.blade.php ENDPATH**/ ?>