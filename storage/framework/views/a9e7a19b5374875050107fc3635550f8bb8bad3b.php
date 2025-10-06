

<?php $__env->startSection('title', 'Payment Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4">Payment Summary Dashboard</h3>

            
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 text-center h-100">
                        <h6 class="text-muted mb-1">Total Collected</h6>
                        <h4 class="fw-bold text-success">LKR <?php echo e(number_format($totalCollected, 2)); ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 text-center h-100">
                        <h6 class="text-muted mb-1">Pending Payments</h6>
                        <h4 class="fw-bold text-warning">LKR <?php echo e(number_format($totalPending, 2)); ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 text-center h-100">
                        <h6 class="text-muted mb-1">Late Fees</h6>
                        <h4 class="fw-bold text-danger">LKR <?php echo e(number_format($totalLateFee, 2)); ?></h4>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card border-0 shadow-sm p-3 text-center h-100">
                        <h6 class="text-muted mb-1">Total Discounts</h6>
                        <h4 class="fw-bold text-info">LKR <?php echo e(number_format($totalDiscount, 2)); ?></h4>
                    </div>
                </div>
            </div>

            
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-3 h-100">
                        <h6 class="fw-bold text-secondary mb-3">Payment by Method</h6>
                        <canvas id="methodChart" height="200"></canvas>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card border-0 shadow-sm p-3 h-100">
                        <h6 class="fw-bold text-secondary mb-3">Payment by Type</h6>
                        <canvas id="typeChart" height="200"></canvas>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="card border-0 shadow-sm p-3">
                        <h6 class="fw-bold text-secondary mb-3">Monthly Collection Trend</h6>
                        <canvas id="monthlyChart" height="100"></canvas>
                    </div>
                </div>
            </div>

            
            <hr class="my-4">
            <h5 class="text-info mb-3">Top 5 Paying Students</h5>
            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Student ID</th>
                            <th>Total Paid (LKR)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $topStudents; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($i+1); ?></td>
                                <td>
                                    <a href="<?php echo e(route('payment.summary.student', $student->student_id)); ?>" 
                                       class="text-decoration-none text-primary fw-semibold">
                                        <?php echo e($student->student_id); ?>

                                    </a>
                                </td>
                                <td><?php echo e(number_format($student->total, 2)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No student data found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {

    const paymentByMethod = <?php echo json_encode($paymentByMethod, 15, 512) ?>;
    const paymentByType   = <?php echo json_encode($paymentByType, 15, 512) ?>;
    const monthlyIncome   = <?php echo json_encode($monthlyIncome, 15, 512) ?>;

    // ---- Payment by Method (Pie) ----
    new Chart(document.getElementById('methodChart'), {
        type: 'pie',
        data: {
            labels: paymentByMethod.map(p => p.payment_method ?? 'Unknown'),
            datasets: [{
                data: paymentByMethod.map(p => p.total),
                backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
            }]
        },
        options: {
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // ---- Payment by Type (Doughnut) ----
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            // ✅ use "type" alias from controller instead of "installment_type"
            labels: paymentByType.map(p => p.type ?? 'Unknown'),
            datasets: [{
                data: paymentByType.map(p => p.total),
                backgroundColor: ['#36b9cc', '#f6c23e', '#e74a3b', '#858796', '#20c997']
            }]
        },
        options: {
            cutout: '60%',
            plugins: { legend: { position: 'bottom' } }
        }
    });

    // ---- Monthly Collection (Line) ----
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: monthlyIncome.map(p => p.month),
            datasets: [{
                label: 'Monthly Income (LKR)',
                data: monthlyIncome.map(p => p.total),
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78,115,223,0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            scales: { y: { beginAtZero: true } },
            plugins: { legend: { display: false } }
        }
    });

});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/payment/summary.blade.php ENDPATH**/ ?>