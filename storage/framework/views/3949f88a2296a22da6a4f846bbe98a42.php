<div class="table-card">
    <div class="card-header text-white">
        <h5 class="mb-0">
            <?php if($type == 'pending'): ?>
                <i class="ti ti-clock me-2"></i>Pending Clearance Requests
            <?php elseif($type == 'approved'): ?>
                <i class="ti ti-check me-2"></i>Approved Clearance Requests
            <?php else: ?>
                <i class="ti ti-x me-2"></i>Rejected Clearance Requests
            <?php endif; ?>
            <span class="badge bg-light text-dark ms-2"><?php echo e($requests->total()); ?> total</span>
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if($requests->count() > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Location</th>
                            <th>Requested Date</th>
                            <?php if($type == 'pending'): ?>
                                <th>Actions</th>
                            <?php else: ?>
                                <th>Status</th>
                                <th>Processed Date</th>
                                <th>Processed By</th>
                                <th>Remarks</th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $requests; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            <?php echo e(substr($request->student->student_id, -2)); ?>

                                        </div>
                                        <span><?php echo e($request->student->student_id); ?></span>
                                    </div>
                                </td>
                                <td>
                                    <strong><?php echo e($request->student->name_with_initials); ?></strong>
                                </td>
                                <td><?php echo e($request->course->course_name); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($request->intake->batch); ?></span>
                                </td>
                                <td><?php echo e($request->location); ?></td>
                                <td><?php echo e($request->requested_at->format('d/m/Y H:i')); ?></td>
                                
                                <?php if($type == 'pending'): ?>
                                    <td>
                                        <div class="btn-group" role="group">
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
                                        </div>
                                    </td>
                                <?php else: ?>
                                    <td>
                                        <?php if($type == 'approved'): ?>
                                            <span class="badge bg-success status-badge">
                                                <i class="ti ti-check me-1"></i> Approved
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-danger status-badge">
                                                <i class="ti ti-x me-1"></i> Rejected
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($request->approved_at): ?>
                                            <?php echo e($request->approved_at->format('d/m/Y H:i')); ?>

                                        <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($request->approvedBy): ?>
                                            <?php echo e($request->approvedBy->name); ?>

                                        <?php else: ?>
                                            <span class="text-muted">System</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if($request->remarks): ?>
                                            <span class="text-truncate" style="max-width: 200px;" 
                                                  title="<?php echo e($request->remarks); ?>">
                                                <?php echo e(Str::limit($request->remarks, 50)); ?>

                                            </span>
                                        <?php else: ?>
                                            <span class="text-muted">No remarks</span>
                                        <?php endif; ?>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <?php if($requests->hasPages()): ?>
                <div class="pagination-container">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing <?php echo e($requests->firstItem()); ?> to <?php echo e($requests->lastItem()); ?> of <?php echo e($requests->total()); ?> entries
                        </div>
                        <div>
                            <?php echo e($requests->appends(['tab' => $tab, 'search' => request('search')])->links()); ?>

                        </div>
                    </div>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <?php if($type == 'pending'): ?>
                    <i class="ti ti-check-circle text-success"></i>
                    <h5 class="mt-3">No Pending Requests</h5>
                    <p class="text-muted">All hostel clearance requests have been processed.</p>
                <?php elseif($type == 'approved'): ?>
                    <i class="ti ti-inbox text-info"></i>
                    <h5 class="mt-3">No Approved Requests</h5>
                    <p class="text-muted">No hostel clearance requests have been approved yet.</p>
                <?php else: ?>
                    <i class="ti ti-ban text-danger"></i>
                    <h5 class="mt-3">No Rejected Requests</h5>
                    <p class="text-muted">No hostel clearance requests have been rejected yet.</p>
                <?php endif; ?>
                <?php if(request('search')): ?>
                    <a href="<?php echo e(route('hostel.clearance.form.management')); ?>" class="btn btn-primary mt-3">
                        <i class="ti ti-refresh me-1"></i> Clear Search
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/partials/hostel-clearance-tab.blade.php ENDPATH**/ ?>