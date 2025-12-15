<style>
    .stat-card {
        transition: 0.3s;
        cursor: pointer;
        background: white;
        border-left: 4px solid transparent;
    }
    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }
    .stat-card.pending { border-left-color: #0d6efd; }
    .stat-card.approved { border-left-color: #198754; }
    .stat-card.rejected { border-left-color: #dc3545; }
    
    .badge-status {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
        text-transform: capitalize;
    }
    .badge-pending { background: #0d6efd; color: white; }
    .badge-approved { background: #198754; color: white; }
    .badge-rejected { background: #dc3545; color: white; }
    
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .chart-container {
        height: 300px;
        position: relative;
    }
    
    .date-display {
        background: #f8f9fa;
        padding: 8px 15px;
        border-radius: 5px;
        font-weight: 500;
    }
    
    .select-all-checkbox {
        margin-right: 10px;
    }
    
    .bulk-actions-bar {
        position: fixed;
        bottom: 20px;
        right: 20px;
        z-index: 1000;
        background: white;
        padding: 15px 20px;
        border-radius: 10px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        display: none;
    }
    
    .action-buttons {
        display: flex;
        gap: 10px;
    }
    
    .status-selector {
        width: 150px;
    }
</style>

<div class="container-fluid">
    <div class="page-wrapper">

        <!-- Header with Current Date -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h3 class="fw-bold text-dark m-0">Hostel Manager Dashboard</h3>
                    <div class="text-muted mt-1">
                        <i class="bi bi-calendar"></i> 
                        <span id="currentDate"><?php echo e(now()->format('Y-m-d')); ?></span> | 
                        <span id="currentTime"><?php echo e(now()->format('H:i:s')); ?></span>
                    </div>
                </div>
                <div class="date-display">
                    <i class="bi bi-clock-history"></i> Last Updated: <?php echo e(now()->format('Y-m-d H:i')); ?>

                </div>
            </div>
        </div>

        <!-- Analytics Overview -->
        <div class="card shadow-sm p-4 mb-4">
            <h5 class="fw-semibold mb-3">Analytics Overview</h5>
            <div class="row g-4">
                <div class="col-md-3">
                    <div class="analytics-card">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Avg. Processing Time</h6>
                                <h3 id="avgProcessingTime" class="fw-bold">0h</h3>
                            </div>
                            <i class="bi bi-speedometer2 fs-2 opacity-75"></i>
                        </div>
                        <small class="text-white-75">This Month</small>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="analytics-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h6 class="text-white-50">Clearance Rate</h6>
                                <h3 id="clearanceRate" class="fw-bold">0%</h3>
                                <small class="text-white-75">Approval Percentage</small>
                            </div>
                            <i class="bi bi-check-circle fs-2 opacity-75"></i>
                        </div>
                    </div>
                </div>

<?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/dashboards/hostel_manager_content.blade.php ENDPATH**/ ?>