

<?php $__env->startSection('title', 'NEBULA | Semester Management'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Semester Management</h2>
                <div class="d-flex gap-2">
                    <button id="bulkActionsBtn" class="btn btn-outline-secondary" style="display: none;">
                        <i class="fas fa-tasks"></i> Bulk Actions
                    </button>
                    <a href="<?php echo e(route('semesters.create')); ?>" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create New Semester
                    </a>
                </div>
            </div>
            <hr>
            
            <!-- Search and Filter Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="searchInput" class="form-control" placeholder="Search semesters...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select id="statusFilter" class="form-select">
                        <option value="">All Status</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="courseFilter" class="form-select">
                        <option value="">All Courses</option>
                        <?php $__currentLoopData = $courses ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($course->course_name); ?>"><?php echo e($course->course_name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <button id="clearFilters" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-times"></i> Clear
                    </button>
                </div>
            </div>
            
            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="totalSemesters"><?php echo e($semesters->count()); ?></h4>
                                    <small>Total Semesters</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-calendar fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="activeSemesters"><?php echo e($semesters->where('status', 'active')->count()); ?></h4>
                                    <small>Active Semesters</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-play-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="upcomingSemesters"><?php echo e($semesters->where('status', 'upcoming')->count()); ?></h4>
                                    <small>Upcoming Semesters</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-clock fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-secondary text-white">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h4 class="mb-0" id="completedSemesters"><?php echo e($semesters->where('status', 'completed')->count()); ?></h4>
                                    <small>Completed Semesters</small>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-check-circle fa-2x"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered" id="semestersTable">
                    <thead class="table-dark">
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll" class="form-check-input">
                            </th>
                            <th>Semester Name</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Duration</th>
                            <th>Status</th>
                            <th>Modules</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr data-semester="<?php echo e(strtolower($semester->name)); ?>" 
                            data-course="<?php echo e(strtolower($semester->course->course_name ?? '')); ?>"
                            data-status="<?php echo e($semester->status); ?>">
                            <td>
                                <input type="checkbox" class="form-check-input semester-checkbox" value="<?php echo e($semester->id); ?>">
                            </td>
                            <td>
                                <strong><?php echo e($semester->name); ?></strong>
                                <?php if($semester->status === 'active'): ?>
                                    <span class="badge bg-success ms-2">Current</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo e($semester->course->course_name ?? 'N/A'); ?></td>
                            <td><?php echo e($semester->intake->batch ?? 'N/A'); ?></td>
                            <td><?php echo e($semester->start_date ? (is_string($semester->start_date) ? \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') : $semester->start_date->format('M d, Y')) : 'N/A'); ?></td>
                            <td><?php echo e($semester->end_date ? (is_string($semester->end_date) ? \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') : $semester->end_date->format('M d, Y')) : 'N/A'); ?></td>
                            <td>
                                <?php if($semester->start_date && $semester->end_date): ?>
                                    <?php
                                        $start = \Carbon\Carbon::parse($semester->start_date);
                                        $end = \Carbon\Carbon::parse($semester->end_date);
                                        $duration = $start->diffInDays($end);
                                    ?>
                                    <?php echo e($duration); ?> days
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($semester->status === 'upcoming'): ?>
                                    <span class="badge bg-warning">Upcoming</span>
                                <?php elseif($semester->status === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Completed</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $moduleCount = $semester->modules->count();
                                ?>
                                <span class="badge bg-info"><?php echo e($moduleCount); ?> module<?php echo e($moduleCount !== 1 ? 's' : ''); ?></span>
                                <?php if($moduleCount > 0): ?>
                                    <button type="button" class="btn btn-sm btn-outline-info ms-1" 
                                            data-bs-toggle="modal" data-bs-target="#modulesModal<?php echo e($semester->id); ?>">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="<?php echo e(route('semesters.edit', $semester)); ?>" class="btn btn-sm btn-outline-primary" 
                                       title="Edit Semester">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" data-bs-target="#semesterModal<?php echo e($semester->id); ?>"
                                            title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-semester" 
                                            data-semester-id="<?php echo e($semester->id); ?>" 
                                            data-semester-name="<?php echo e($semester->name); ?>"
                                            title="Delete Semester">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center">No semesters found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Semester Detail Modals -->
<?php $__currentLoopData = $semesters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $semester): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div class="modal fade" id="semesterModal<?php echo e($semester->id); ?>" tabindex="-1" aria-labelledby="semesterModalLabel<?php echo e($semester->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="semesterModalLabel<?php echo e($semester->id); ?>">Semester Details - <?php echo e($semester->name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Basic Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Name:</strong></td><td><?php echo e($semester->name); ?></td></tr>
                            <tr><td><strong>Course:</strong></td><td><?php echo e($semester->course->course_name ?? 'N/A'); ?></td></tr>
                            <tr><td><strong>Intake:</strong></td><td><?php echo e($semester->intake->batch ?? 'N/A'); ?></td></tr>
                            <tr><td><strong>Status:</strong></td><td>
                                <?php if($semester->status === 'upcoming'): ?>
                                    <span class="badge bg-warning">Upcoming</span>
                                <?php elseif($semester->status === 'active'): ?>
                                    <span class="badge bg-success">Active</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Completed</span>
                                <?php endif; ?>
                            </td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h6>Date Information</h6>
                        <table class="table table-sm">
                            <tr><td><strong>Start Date:</strong></td><td><?php echo e($semester->start_date ? (is_string($semester->start_date) ? \Carbon\Carbon::parse($semester->start_date)->format('M d, Y') : $semester->start_date->format('M d, Y')) : 'N/A'); ?></td></tr>
                            <tr><td><strong>End Date:</strong></td><td><?php echo e($semester->end_date ? (is_string($semester->end_date) ? \Carbon\Carbon::parse($semester->end_date)->format('M d, Y') : $semester->end_date->format('M d, Y')) : 'N/A'); ?></td></tr>
                            <tr><td><strong>Duration:</strong></td><td>
                                <?php if($semester->start_date && $semester->end_date): ?>
                                    <?php
                                        $start = \Carbon\Carbon::parse($semester->start_date);
                                        $end = \Carbon\Carbon::parse($semester->end_date);
                                        $duration = $start->diffInDays($end);
                                    ?>
                                    <?php echo e($duration); ?> days
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td></tr>
                            <tr><td><strong>Created:</strong></td><td><?php echo e($semester->created_at ? (is_string($semester->created_at) ? \Carbon\Carbon::parse($semester->created_at)->format('M d, Y H:i') : $semester->created_at->format('M d, Y H:i')) : 'N/A'); ?></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <a href="<?php echo e(route('semesters.edit', $semester)); ?>" class="btn btn-primary">Edit Semester</a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modules Modal -->
<div class="modal fade" id="modulesModal<?php echo e($semester->id); ?>" tabindex="-1" aria-labelledby="modulesModalLabel<?php echo e($semester->id); ?>" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modulesModalLabel<?php echo e($semester->id); ?>">Modules - <?php echo e($semester->name); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <?php if($semester->modules->count() > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Module Name</th>
                                    <th>Type</th>
                                    <th>Credits</th>
                                    <th>Specialization</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__currentLoopData = $semester->modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($module->module_name); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($module->module_type === 'core' ? 'primary' : ($module->module_type === 'elective' ? 'success' : 'warning')); ?>">
                                            <?php echo e(ucfirst(str_replace('_', ' ', $module->module_type))); ?>

                                        </span>
                                    </td>
                                    <td><?php echo e($module->credits ?? 'N/A'); ?></td>
                                    <td><?php echo e($module->pivot->specialization ?? 'N/A'); ?></td>
                                </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">No modules assigned to this semester.</p>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<!-- Bulk Actions Modal -->
<div class="modal fade" id="bulkActionsModal" tabindex="-1" aria-labelledby="bulkActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionsModalLabel">Bulk Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">Selected Semesters: <span id="selectedCount">0</span></label>
                </div>
                <div class="mb-3">
                    <label for="bulkStatus" class="form-label">Update Status:</label>
                    <select id="bulkStatus" class="form-select">
                        <option value="">Select Status</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" id="bulkDeleteBtn">
                    <i class="fas fa-trash"></i> Delete Selected
                </button>
                <button type="button" class="btn btn-primary" id="bulkUpdateStatusBtn">
                    <i class="fas fa-save"></i> Update Status
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                <!-- Message will go here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const courseFilter = document.getElementById('courseFilter');
    const clearFilters = document.getElementById('clearFilters');
    const table = document.getElementById('semestersTable');
    const tbody = table.querySelector('tbody');
    const rows = tbody.querySelectorAll('tr');
    
    // Bulk operations elements
    const selectAllCheckbox = document.getElementById('selectAll');
    const bulkActionsBtn = document.getElementById('bulkActionsBtn');
    const bulkActionsModal = new bootstrap.Modal(document.getElementById('bulkActionsModal'));
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkStatusSelect = document.getElementById('bulkStatus');
    const bulkUpdateStatusBtn = document.getElementById('bulkUpdateStatusBtn');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');

    // Search functionality
    function filterTable() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value.toLowerCase();
        const courseValue = courseFilter.value.toLowerCase();

        rows.forEach(row => {
            if (row.cells.length === 0) return; // Skip empty rows
            
            const semesterName = row.getAttribute('data-semester') || '';
            const courseName = row.getAttribute('data-course') || '';
            const status = row.getAttribute('data-status') || '';
            
            const matchesSearch = semesterName.includes(searchTerm) || courseName.includes(searchTerm);
            const matchesStatus = !statusValue || status === statusValue;
            const matchesCourse = !courseValue || courseName.includes(courseValue);
            
            if (matchesSearch && matchesStatus && matchesCourse) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
        
        updateStatistics();
    }

    // Update statistics based on visible rows
    function updateStatistics() {
        const visibleRows = Array.from(rows).filter(row => row.style.display !== 'none');
        const totalSemesters = visibleRows.length;
        const activeSemesters = visibleRows.filter(row => row.getAttribute('data-status') === 'active').length;
        const upcomingSemesters = visibleRows.filter(row => row.getAttribute('data-status') === 'upcoming').length;
        const completedSemesters = visibleRows.filter(row => row.getAttribute('data-status') === 'completed').length;
        
        document.getElementById('totalSemesters').textContent = totalSemesters;
        document.getElementById('activeSemesters').textContent = activeSemesters;
        document.getElementById('upcomingSemesters').textContent = upcomingSemesters;
        document.getElementById('completedSemesters').textContent = completedSemesters;
    }

    // Event listeners
    searchInput.addEventListener('input', filterTable);
    statusFilter.addEventListener('change', filterTable);
    courseFilter.addEventListener('change', filterTable);
    
    clearFilters.addEventListener('click', function() {
        searchInput.value = '';
        statusFilter.value = '';
        courseFilter.value = '';
        filterTable();
    });

    // Bulk operations functionality
    function updateBulkActionsButton() {
        const selectedCheckboxes = document.querySelectorAll('.semester-checkbox:checked');
        const selectedCount = selectedCheckboxes.length;
        
        if (selectedCount > 0) {
            bulkActionsBtn.style.display = 'inline-block';
            selectedCountSpan.textContent = selectedCount;
        } else {
            bulkActionsBtn.style.display = 'none';
            selectedCountSpan.textContent = '0';
        }
    }

    // Select all functionality
    selectAllCheckbox.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.semester-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkActionsButton();
    });

    // Individual checkbox functionality
    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('semester-checkbox')) {
            updateBulkActionsButton();
            
            // Update select all checkbox
            const checkboxes = document.querySelectorAll('.semester-checkbox');
            const checkedCheckboxes = document.querySelectorAll('.semester-checkbox:checked');
            
            if (checkedCheckboxes.length === 0) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = false;
            } else if (checkedCheckboxes.length === checkboxes.length) {
                selectAllCheckbox.indeterminate = false;
                selectAllCheckbox.checked = true;
            } else {
                selectAllCheckbox.indeterminate = true;
            }
        }
    });

    // Bulk actions button click
    bulkActionsBtn.addEventListener('click', function() {
        bulkActionsModal.show();
    });

    // Bulk update status
    bulkUpdateStatusBtn.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.semester-checkbox:checked');
        const status = bulkStatusSelect.value;
        
        if (selectedCheckboxes.length === 0) {
            showToast('Please select at least one semester.', 'warning');
            return;
        }
        
        if (!status) {
            showToast('Please select a status to update.', 'warning');
            return;
        }
        
        const semesterIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        fetch('<?php echo e(route("semesters.bulkUpdateStatus")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                semester_ids: semesterIds,
                status: status
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bulkActionsModal.hide();
                
                // Update the status in the table
                selectedCheckboxes.forEach(checkbox => {
                    const row = checkbox.closest('tr');
                    const statusCell = row.querySelector('td:nth-child(8)'); // Status column
                    const statusBadge = statusCell.querySelector('.badge');
                    
                    if (statusBadge) {
                        statusBadge.className = 'badge bg-' + (status === 'upcoming' ? 'warning' : (status === 'active' ? 'success' : 'secondary'));
                        statusBadge.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    }
                    
                    row.setAttribute('data-status', status);
                });
                
                // Uncheck all checkboxes
                selectAllCheckbox.checked = false;
                document.querySelectorAll('.semester-checkbox').forEach(cb => cb.checked = false);
                updateBulkActionsButton();
                updateStatistics();
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while updating semester statuses.', 'danger');
        });
    });

    // Bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedCheckboxes = document.querySelectorAll('.semester-checkbox:checked');
        
        if (selectedCheckboxes.length === 0) {
            showToast('Please select at least one semester.', 'warning');
            return;
        }
        
        if (!confirm(`Are you sure you want to delete ${selectedCheckboxes.length} semester(s)? This action cannot be undone.`)) {
            return;
        }
        
        const semesterIds = Array.from(selectedCheckboxes).map(cb => cb.value);
        
        fetch('<?php echo e(route("semesters.bulkDelete")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                semester_ids: semesterIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                bulkActionsModal.hide();
                
                // Remove rows from table
                selectedCheckboxes.forEach(checkbox => {
                    checkbox.closest('tr').remove();
                });
                
                // Uncheck all checkboxes
                selectAllCheckbox.checked = false;
                document.querySelectorAll('.semester-checkbox').forEach(cb => cb.checked = false);
                updateBulkActionsButton();
                updateStatistics();
                
                // Check if table is empty
                const visibleRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
                if (visibleRows.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="10" class="text-center">No semesters found.</td></tr>';
                }
            } else {
                showToast(data.message, 'danger');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while deleting semesters.', 'danger');
        });
    });

    // Delete semester functionality
    document.querySelectorAll('.delete-semester').forEach(button => {
        button.addEventListener('click', function() {
            const semesterId = this.dataset.semesterId;
            const semesterName = this.dataset.semesterName;
            
            if (confirm(`Are you sure you want to delete the semester "${semesterName}"? This action cannot be undone.`)) {
                fetch(`/semesters/${semesterId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        // Remove the row from the table
                        this.closest('tr').remove();
                        
                        // Check if table is empty
                        const visibleRows = Array.from(tbody.querySelectorAll('tr')).filter(row => row.style.display !== 'none');
                        if (visibleRows.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="9" class="text-center">No semesters found.</td></tr>';
                        }
                        
                        // Update statistics
                        updateStatistics();
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while deleting the semester.', 'danger');
                });
            }
        });
    });
});

// Toast function
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('mainToast');
    const toastBody = document.getElementById('mainToastBody');
    toastBody.textContent = message;
    toastEl.className = 'toast align-items-center border-0 text-bg-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary')));
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}
</script>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/semester_index.blade.php ENDPATH**/ ?>