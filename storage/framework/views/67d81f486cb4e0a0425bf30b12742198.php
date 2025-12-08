

<?php $__env->startSection('title', 'NEBULA | Hostel Clearance'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-2">
            <div class="p-4 rounded shadow w-100 bg-white mt-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="mb-0">Hostel Clearance Management</h2>
                    <div class="search-box" style="width: 300px;">
                        <form method="GET" action="<?php echo e(route('hostel.clearance.form.management')); ?>">
                            <div class="input-group">
                                <input type="text" 
                                       class="form-control" 
                                       placeholder="Search by Student ID or Name..." 
                                       name="search"
                                       value="<?php echo e(request('search')); ?>">
                                <input type="hidden" name="tab" value="<?php echo e($tab); ?>">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="ti ti-search"></i>
                                </button>
                                <?php if(request('search')): ?>
                                    <a href="<?php echo e(route('hostel.clearance.form.management')); ?>" class="btn btn-outline-secondary">
                                        <i class="ti ti-x"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>
                <hr>

                <!-- Tabs Navigation -->
                <ul class="nav nav-tabs mb-4" id="clearanceTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo e($tab == 'pending' ? 'active' : ''); ?>" 
                                id="pending-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#pending" 
                                type="button" 
                                role="tab"
                                data-url="<?php echo e(route('hostel.clearance.form.management', ['tab' => 'pending'])); ?>">
                            <i class="ti ti-clock me-1"></i>
                            Pending
                            <span class="badge bg-warning ms-2"><?php echo e($pendingRequests->total()); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo e($tab == 'approved' ? 'active' : ''); ?>" 
                                id="approved-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#approved" 
                                type="button" 
                                role="tab"
                                data-url="<?php echo e(route('hostel.clearance.form.management', ['tab' => 'approved'])); ?>">
                            <i class="ti ti-check me-1"></i>
                            Approved
                            <span class="badge bg-success ms-2"><?php echo e($approvedRequests->total()); ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?php echo e($tab == 'rejected' ? 'active' : ''); ?>" 
                                id="rejected-tab" 
                                data-bs-toggle="tab" 
                                data-bs-target="#rejected" 
                                type="button" 
                                role="tab"
                                data-url="<?php echo e(route('hostel.clearance.form.management', ['tab' => 'rejected'])); ?>">
                            <i class="ti ti-x me-1"></i>
                            Rejected
                            <span class="badge bg-danger ms-2"><?php echo e($rejectedRequests->total()); ?></span>
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="clearanceTabsContent">
                    <!-- Pending Tab -->
                    <div class="tab-pane fade <?php echo e($tab == 'pending' ? 'show active' : ''); ?>" 
                         id="pending" 
                         role="tabpanel" 
                         aria-labelledby="pending-tab">
                        <?php echo $__env->make('partials.hostel-clearance-tab', [
                            'requests' => $pendingRequests,
                            'type' => 'pending',
                            'tab' => 'pending'
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                    <!-- Approved Tab -->
                    <div class="tab-pane fade <?php echo e($tab == 'approved' ? 'show active' : ''); ?>" 
                         id="approved" 
                         role="tabpanel" 
                         aria-labelledby="approved-tab">
                        <?php echo $__env->make('partials.hostel-clearance-tab', [
                            'requests' => $approvedRequests,
                            'type' => 'approved',
                            'tab' => 'approved'
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>

                    <!-- Rejected Tab -->
                    <div class="tab-pane fade <?php echo e($tab == 'rejected' ? 'show active' : ''); ?>" 
                         id="rejected" 
                         role="tabpanel" 
                         aria-labelledby="rejected-tab">
                        <?php echo $__env->make('partials.hostel-clearance-tab', [
                            'requests' => $rejectedRequests,
                            'type' => 'rejected',
                            'tab' => 'rejected'
                        ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Approval Modal -->
<div class="modal fade" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="approvalModalTitle">Confirm Action</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p id="approvalModalText">Are you sure you want to proceed with this action?</p>
                <div class="mb-3">
                    <label for="remarks" class="form-label">Remarks (Optional)</label>
                    <textarea class="form-control" id="remarks" rows="3" placeholder="Enter any remarks..."></textarea>
                </div>
                <div class="mb-3">
                    <label for="clearance_slip" class="form-label">Clearance Slip (Optional)</label>
                    <input type="file" class="form-control" id="clearance_slip" name="clearance_slip" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                    <small class="text-muted">Accepted formats: PDF, JPG, PNG, DOC, DOCX (Max size: 5MB)</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-success" id="confirmApproval">
                    <i class="ti ti-check"></i> Confirm
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
        position: relative;
    }
    
    .nav-tabs .nav-link.active {
        color: #0d6efd;
        background-color: transparent;
        border-bottom: 3px solid #0d6efd;
    }
    
    .nav-tabs .nav-link:hover {
        color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .table-card {
        border: 1px solid #e9ecef;
        border-radius: 0.5rem;
        overflow: hidden;
    }
    
    .table-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-bottom: none;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(13, 110, 253, 0.05);
    }
    
    .status-badge {
        padding: 0.35rem 0.65rem;
        font-size: 0.875em;
        border-radius: 50rem;
    }
    
    .empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #6c757d;
    }
    
    .empty-state i {
        font-size: 4rem;
        opacity: 0.5;
        margin-bottom: 1rem;
    }
    
    .pagination-container {
        background: #f8f9fa;
        padding: 1rem;
        border-radius: 0.5rem;
        margin-top: 1rem;
    }
    
    .search-box .input-group {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-radius: 0.5rem;
        overflow: hidden;
    }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let currentRequestId = null;
    let currentAction = null;

    // Handle tab changes
    $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const url = $(this).data('url');
        if (url) {
            window.history.pushState({}, '', url);
        }
    });

    // Approve button click
    $(document).on('click', '.approve-btn', function() {
        currentRequestId = $(this).data('request-id');
        currentAction = 'approve';
        const studentName = $(this).data('student-name');
        
        $('#approvalModalTitle').text('Approve Clearance');
        $('#approvalModalText').text(`Are you sure you want to approve hostel clearance for ${studentName}?`);
        $('#remarks').val('');
        $('#clearance_slip').val('');
        $('#confirmApproval').removeClass('btn-danger').addClass('btn-success');
        $('#approvalModal').modal('show');
    });

    // Reject button click
    $(document).on('click', '.reject-btn', function() {
        currentRequestId = $(this).data('request-id');
        currentAction = 'reject';
        const studentName = $(this).data('student-name');
        
        $('#approvalModalTitle').text('Reject Clearance');
        $('#approvalModalText').text(`Are you sure you want to reject hostel clearance for ${studentName}?`);
        $('#remarks').val('');
        $('#clearance_slip').val('');
        $('#confirmApproval').removeClass('btn-success').addClass('btn-danger');
        $('#approvalModal').modal('show');
    });

    // Confirm approval/rejection
    $('#confirmApproval').on('click', function() {
        if (!currentRequestId || !currentAction) return;

        const url = currentAction === 'approve' 
            ? '<?php echo e(route("hostel.approve.clearance")); ?>'
            : '<?php echo e(route("hostel.reject.clearance")); ?>';

        const formData = new FormData();
        formData.append('request_id', currentRequestId);
        formData.append('remarks', $('#remarks').val());
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        
        const fileInput = document.getElementById('clearance_slip');
        if (fileInput.files.length > 0) {
            formData.append('clearance_slip', fileInput.files[0]);
        }

        $.ajax({
            url: url,
            method: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function() {
                showToast('An error occurred while processing the request.', 'danger');
            }
        });

        $('#approvalModal').modal('hide');
    });

    // Show toast function
    function showToast(message, type) {
        const toast = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;
        $('.toast-container').append(toast);
        $('.toast').toast('show');
        $('.toast').on('hidden.bs.toast', function() { 
            $(this).remove(); 
        });
    }

    // Handle pagination clicks
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        const url = $(this).attr('href');
        const activeTab = $('.nav-tabs .nav-link.active').attr('id').replace('-tab', '');
        
        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                // Extract the tab content from the response
                const parser = new DOMParser();
                const doc = parser.parseFromString(response, 'text/html');
                const tabContent = doc.querySelector(`#${activeTab}`);
                
                if (tabContent) {
                    $(`#${activeTab}`).html(tabContent.innerHTML);
                }
                
                // Update URL without reloading
                window.history.pushState({}, '', url);
            },
            error: function() {
                showToast('Error loading page', 'danger');
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/hostel_clearance.blade.php ENDPATH**/ ?>