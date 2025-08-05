<?php $__env->startSection('title', 'NEBULA | Library Clearance'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-12 mt-2">
            <div class="p-4 rounded shadow w-100 bg-white mt-4">
                <h2 class="text-center mb-4">Library Clearance Management</h2>
                <hr style="margin-bottom: 30px;">

                <!-- Pending Requests Section -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0"><i class="ti ti-clock"></i> Pending Clearance Requests</h5>
                    </div>
                    <div class="card-body">
                        <?php if($pendingRequests->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Course</th>
                                            <th>Intake</th>
                                            <th>Location</th>
                                            <th>Requested Date</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $pendingRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($request->student->student_id); ?></td>
                                                <td><?php echo e($request->student->name_with_initials); ?></td>
                                                <td><?php echo e($request->course->course_name); ?></td>
                                                <td><?php echo e($request->intake->batch); ?></td>
                                                <td><?php echo e($request->location); ?></td>
                                                <td><?php echo e($request->requested_at->format('d/m/Y H:i')); ?></td>
                                                <td>
                                                    <button class="btn btn-success btn-sm approve-btn" 
                                                            data-request-id="<?php echo e($request->id); ?>"
                                                            data-student-name="<?php echo e($request->student->name_with_initials); ?>">
                                                        <i class="ti ti-check"></i> Approve
                                                    </button>
                                                    <button class="btn btn-danger btn-sm reject-btn" 
                                                            data-request-id="<?php echo e($request->id); ?>"
                                                            data-student-name="<?php echo e($request->student->name_with_initials); ?>">
                                                        <i class="ti ti-x"></i> Reject
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="ti ti-check-circle text-success" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No Pending Requests</h5>
                                <p class="text-muted">All library clearance requests have been processed.</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Processed Requests Section -->
                <div class="card">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="ti ti-list-check"></i> Processed Clearance Requests</h5>
                    </div>
                    <div class="card-body">
                        <?php if($processedRequests->count() > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Course</th>
                                            <th>Intake</th>
                                            <th>Location</th>
                                            <th>Status</th>
                                            <th>Processed Date</th>
                                            <th>Remarks</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php $__currentLoopData = $processedRequests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($request->student->student_id); ?></td>
                                                <td><?php echo e($request->student->name_with_initials); ?></td>
                                                <td><?php echo e($request->course->course_name); ?></td>
                                                <td><?php echo e($request->intake->batch); ?></td>
                                                <td><?php echo e($request->location); ?></td>
                                                <td>
                                                    <?php if($request->status === 'approved'): ?>
                                                        <span class="badge bg-success">Approved</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Rejected</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo e($request->approved_at ? $request->approved_at->format('d/m/Y H:i') : 'N/A'); ?></td>
                                                <td><?php echo e($request->remarks ?: 'No remarks'); ?></td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="ti ti-inbox text-muted" style="font-size: 3rem;"></i>
                                <h5 class="mt-3">No Processed Requests</h5>
                                <p class="text-muted">No clearance requests have been processed yet.</p>
                            </div>
                        <?php endif; ?>
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

<?php $__env->startPush('scripts'); ?>
<script>
$(document).ready(function() {
    let currentRequestId = null;
    let currentAction = null;

    // Approve button click
    $('.approve-btn').on('click', function() {
        currentRequestId = $(this).data('request-id');
        currentAction = 'approve';
        const studentName = $(this).data('student-name');
        
        $('#approvalModalTitle').text('Approve Clearance');
        $('#approvalModalText').text(`Are you sure you want to approve library clearance for ${studentName}?`);
        $('#remarks').val('');
        $('#clearance_slip').val(''); // Clear file input
        $('#confirmApproval').removeClass('btn-danger').addClass('btn-success');
        $('#approvalModal').modal('show');
    });

    // Reject button click
    $('.reject-btn').on('click', function() {
        currentRequestId = $(this).data('request-id');
        currentAction = 'reject';
        const studentName = $(this).data('student-name');
        
        $('#approvalModalTitle').text('Reject Clearance');
        $('#approvalModalText').text(`Are you sure you want to reject library clearance for ${studentName}?`);
        $('#remarks').val('');
        $('#clearance_slip').val(''); // Clear file input
        $('#confirmApproval').removeClass('btn-success').addClass('btn-danger');
        $('#approvalModal').modal('show');
    });

    // Confirm approval/rejection
    $('#confirmApproval').on('click', function() {
        if (!currentRequestId || !currentAction) return;

        const url = currentAction === 'approve' 
            ? '<?php echo e(route("library.approve.clearance")); ?>'
            : '<?php echo e(route("library.reject.clearance")); ?>';

        // Create FormData to handle file upload
        const formData = new FormData();
        formData.append('request_id', currentRequestId);
        formData.append('remarks', $('#remarks').val());
        formData.append('_token', '<?php echo e(csrf_token()); ?>');
        
        // Add file if selected
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
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/inazawaelectronics/Documents/SLT/Nebula/resources/views/library_clearance.blade.php ENDPATH**/ ?>