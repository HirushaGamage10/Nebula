

<?php $__env->startSection('title', 'Payment Comparison Dashboard'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid mt-4 mb-5">
    
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">📊 Year-over-Year Comparison</h2>
            <p class="text-muted mb-0">Compare payment performance across different time periods</p>
        </div>
        <a href="<?php echo e(route('payment.summary')); ?>" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body text-white">
                    <h6 class="mb-3 opacity-75"><?php echo e($currentYear); ?> Total Revenue</h6>
                    <h2 class="fw-bold mb-2">LKR <?php echo e(number_format($currentYearTotal, 2)); ?></h2>
                    <p class="mb-0 opacity-75">Current year performance</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="card-body text-white">
                    <h6 class="mb-3 opacity-75"><?php echo e($previousYear); ?> Total Revenue</h6>
                    <h2 class="fw-bold mb-2">LKR <?php echo e(number_format($previousYearTotal, 2)); ?></h2>
                    <p class="mb-0 opacity-75">Previous year performance</p>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="card-body text-white">
                    <h6 class="mb-3 opacity-75">Growth Rate</h6>
                    <h2 class="fw-bold mb-2">
                        <?php if($growthRate >= 0): ?>
                            <i class="bi bi-arrow-up-right"></i> +<?php echo e(number_format($growthRate, 2)); ?>%
                        <?php else: ?>
                            <i class="bi bi-arrow-down-right"></i> <?php echo e(number_format($growthRate, 2)); ?>%
                        <?php endif; ?>
                    </h2>
                    <p class="mb-0 opacity-75">Year-over-year change</p>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">📈 Monthly Revenue Comparison</h6>
        </div>
        <div class="card-body">
            <canvas id="comparisonChart" height="80"></canvas>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">📅 Month-by-Month Analysis</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Month</th>
                            <th class="text-end"><?php echo e($currentYear); ?> Revenue</th>
                            <th class="text-end"><?php echo e($previousYear); ?> Revenue</th>
                            <th class="text-end">Difference</th>
                            <th class="text-end">Growth %</th>
                            <th class="text-center">Trend</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php for($month = 1; $month <= 12; $month++): ?>
                            <?php
                                $currentRevenue = $currentYearData->get($month)->revenue ?? 0;
                                $previousRevenue = $previousYearData->get($month)->revenue ?? 0;
                                $difference = $currentRevenue - $previousRevenue;
                                $growthPercent = $previousRevenue > 0 ? (($difference / $previousRevenue) * 100) : 0;
                                $monthName = date('F', mktime(0, 0, 0, $month, 1));
                            ?>
                            <tr>
                                <td><strong><?php echo e($monthName); ?></strong></td>
                                <td class="text-end">LKR <?php echo e(number_format($currentRevenue, 2)); ?></td>
                                <td class="text-end">LKR <?php echo e(number_format($previousRevenue, 2)); ?></td>
                                <td class="text-end <?php echo e($difference >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e($difference >= 0 ? '+' : ''); ?>LKR <?php echo e(number_format($difference, 2)); ?>

                                </td>
                                <td class="text-end">
                                    <span class="badge bg-<?php echo e($growthPercent >= 0 ? 'success' : 'danger'); ?>">
                                        <?php echo e($growthPercent >= 0 ? '+' : ''); ?><?php echo e(number_format($growthPercent, 1)); ?>%
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if($growthPercent > 10): ?>
                                        <i class="bi bi-arrow-up-circle-fill text-success fs-5"></i>
                                    <?php elseif($growthPercent > 0): ?>
                                        <i class="bi bi-arrow-up-circle text-success fs-5"></i>
                                    <?php elseif($growthPercent == 0): ?>
                                        <i class="bi bi-dash-circle text-secondary fs-5"></i>
                                    <?php elseif($growthPercent > -10): ?>
                                        <i class="bi bi-arrow-down-circle text-danger fs-5"></i>
                                    <?php else: ?>
                                        <i class="bi bi-arrow-down-circle-fill text-danger fs-5"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endfor; ?>
                    </tbody>
                    <tfoot class="table-light fw-bold">
                        <tr>
                            <td>TOTAL</td>
                            <td class="text-end">LKR <?php echo e(number_format($currentYearTotal, 2)); ?></td>
                            <td class="text-end">LKR <?php echo e(number_format($previousYearTotal, 2)); ?></td>
                            <td class="text-end <?php echo e(($currentYearTotal - $previousYearTotal) >= 0 ? 'text-success' : 'text-danger'); ?>">
                                <?php echo e(($currentYearTotal - $previousYearTotal) >= 0 ? '+' : ''); ?>LKR <?php echo e(number_format($currentYearTotal - $previousYearTotal, 2)); ?>

                            </td>
                            <td class="text-end">
                                <span class="badge bg-<?php echo e($growthRate >= 0 ? 'success' : 'danger'); ?> fs-6">
                                    <?php echo e($growthRate >= 0 ? '+' : ''); ?><?php echo e(number_format($growthRate, 1)); ?>%
                                </span>
                            </td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">🏆 Best Performing Months</h6>
                </div>
                <div class="card-body">
                    <?php
                        $bestMonths = [];
                        for($m = 1; $m <= 12; $m++) {
                            $currentRev = $currentYearData->get($m)->revenue ?? 0;
                            $previousRev = $previousYearData->get($m)->revenue ?? 0;
                            if($previousRev > 0) {
                                $growth = (($currentRev - $previousRev) / $previousRev) * 100;
                                $bestMonths[] = [
                                    'month' => date('F', mktime(0, 0, 0, $m, 1)),
                                    'growth' => $growth,
                                    'revenue' => $currentRev
                                ];
                            }
                        }
                        usort($bestMonths, function($a, $b) {
                            return $b['growth'] <=> $a['growth'];
                        });
                        $bestMonths = array_slice($bestMonths, 0, 5);
                    ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $bestMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-warning me-2">#<?php echo e($i + 1); ?></span>
                                        <strong><?php echo e($month['month']); ?></strong>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-success fw-bold">+<?php echo e(number_format($month['growth'], 1)); ?>%</div>
                                        <small class="text-muted">LKR <?php echo e(number_format($month['revenue'], 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">⚠️ Areas Needing Attention</h6>
                </div>
                <div class="card-body">
                    <?php
                        $worstMonths = [];
                        for($m = 1; $m <= 12; $m++) {
                            $currentRev = $currentYearData->get($m)->revenue ?? 0;
                            $previousRev = $previousYearData->get($m)->revenue ?? 0;
                            if($previousRev > 0) {
                                $growth = (($currentRev - $previousRev) / $previousRev) * 100;
                                $worstMonths[] = [
                                    'month' => date('F', mktime(0, 0, 0, $m, 1)),
                                    'growth' => $growth,
                                    'revenue' => $currentRev
                                ];
                            }
                        }
                        usort($worstMonths, function($a, $b) {
                            return $a['growth'] <=> $b['growth'];
                        });
                        $worstMonths = array_slice($worstMonths, 0, 5);
                    ?>
                    <div class="list-group list-group-flush">
                        <?php $__currentLoopData = $worstMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="list-group-item px-0">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="badge bg-danger me-2">#<?php echo e($i + 1); ?></span>
                                        <strong><?php echo e($month['month']); ?></strong>
                                    </div>
                                    <div class="text-end">
                                        <div class="text-danger fw-bold"><?php echo e(number_format($month['growth'], 1)); ?>%</div>
                                        <small class="text-muted">LKR <?php echo e(number_format($month['revenue'], 2)); ?></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white border-0 py-3">
            <h6 class="fw-bold mb-0">💡 Key Insights</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-lightbulb-fill"></i> Average Monthly Growth
                        </h6>
                        <p class="mb-0">
                            <?php
                                $monthlyGrowths = [];
                                for($m = 1; $m <= 12; $m++) {
                                    $current = $currentYearData->get($m)->revenue ?? 0;
                                    $previous = $previousYearData->get($m)->revenue ?? 0;
                                    if($previous > 0) {
                                        $monthlyGrowths[] = (($current - $previous) / $previous) * 100;
                                    }
                                }
                                $avgGrowth = count($monthlyGrowths) > 0 ? array_sum($monthlyGrowths) / count($monthlyGrowths) : 0;
                            ?>
                            <strong class="text-<?php echo e($avgGrowth >= 0 ? 'success' : 'danger'); ?>">
                                <?php echo e($avgGrowth >= 0 ? '+' : ''); ?><?php echo e(number_format($avgGrowth, 2)); ?>%
                            </strong>
                            per month
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-graph-up"></i> Strongest Quarter
                        </h6>
                        <p class="mb-0">
                            <?php
                                $quarters = [];
                                for($q = 1; $q <= 4; $q++) {
                                    $sum = 0;
                                    for($m = ($q-1)*3+1; $m <= $q*3; $m++) {
                                        $sum += $currentYearData->get($m)->revenue ?? 0;
                                    }
                                    $quarters[$q] = $sum;
                                }
                                $strongestQuarter = array_search(max($quarters), $quarters);
                            ?>
                            <strong class="text-success">Q<?php echo e($strongestQuarter); ?></strong> with 
                            LKR <?php echo e(number_format(max($quarters), 2)); ?>

                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="p-3 bg-light rounded">
                        <h6 class="text-primary mb-2">
                            <i class="bi bi-trophy-fill"></i> Achievement Status
                        </h6>
                        <p class="mb-0">
                            <?php if($growthRate > 20): ?>
                                <strong class="text-success">Excellent Growth!</strong> 🎉
                            <?php elseif($growthRate > 10): ?>
                                <strong class="text-success">Strong Performance</strong> 👍
                            <?php elseif($growthRate > 0): ?>
                                <strong class="text-info">Steady Progress</strong> 📈
                            <?php else: ?>
                                <strong class="text-warning">Needs Improvement</strong> ⚠️
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener("DOMContentLoaded", () => {
    const currentYearData = <?php echo json_encode($currentYearData, 15, 512) ?>;
    const previousYearData = <?php echo json_encode($previousYearData, 15, 512) ?>;
    const currentYear = <?php echo e($currentYear); ?>;
    const previousYear = <?php echo e($previousYear); ?>;

    // Prepare data for all 12 months
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    const currentData = [];
    const previousData = [];

    for(let i = 1; i <= 12; i++) {
        const current = currentYearData[i];
        const previous = previousYearData[i];
        currentData.push(current ? current.revenue : 0);
        previousData.push(previous ? previous.revenue : 0);
    }

    // Year Comparison Chart
    new Chart(document.getElementById('comparisonChart'), {
        type: 'line',
        data: {
            labels: months,
            datasets: [{
                label: currentYear + ' Revenue',
                data: currentData,
                borderColor: '#667eea',
                backgroundColor: 'rgba(102, 126, 234, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7
            }, {
                label: previousYear + ' Revenue',
                data: previousData,
                borderColor: '#f093fb',
                backgroundColor: 'rgba(240, 147, 251, 0.1)',
                fill: true,
                tension: 0.4,
                borderWidth: 3,
                pointRadius: 5,
                pointHoverRadius: 7,
                borderDash: [5, 5]
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
                    labels: {
                        usePointStyle: true,
                        padding: 20,
                        font: { size: 13, weight: 'bold' }
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 15,
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
});
</script>

<style>
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-3px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.list-group-item {
    border: none;
    border-bottom: 1px solid rgba(0,0,0,0.05);
}

.list-group-item:last-child {
    border-bottom: none;
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/payment/comparison.blade.php ENDPATH**/ ?>