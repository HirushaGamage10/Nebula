

<?php $__env->startSection('title', 'NEBULA | Intake Creation'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Create New Intake</h2>
            <hr>
            <form id="intakeForm">
                <?php echo csrf_field(); ?>
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location">
                            <option value="">Choose a location...</option>
                            <option value="Welisara" <?php echo e($selectedLocation == 'Welisara' ? 'selected' : ''); ?>>Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa" <?php echo e($selectedLocation == 'Moratuwa' ? 'selected' : ''); ?>>Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya" <?php echo e($selectedLocation == 'Peradeniya' ? 'selected' : ''); ?>>Nebula Institute of Technology - Peradeniya</option>
                        </select>

                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course_type" class="col-sm-2 col-form-label">Course Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course_type" name="course_type" required>
                            <option selected disabled value="">Choose course type...</option>
                            <option value="degree">Degree Program</option>
                            <option value="diploma">Diploma Program</option>
                            <option value="certificate">Certificate Program</option>
                        </select>
                    </div>
                </div>

                <!-- All fields container (hidden until course type selected) -->
                <div id="intake_fields_container" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="course_name" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="course_id" name="course_id" required>
                                <option selected disabled value="">Choose a course...</option>
                                <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($course->course_id); ?>" data-type="<?php echo e($course->course_type); ?>">
                                        <?php echo e($course->course_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>

                    </div>
                </div>
                <div id="courseDetailsBox" class="mb-3 row mx-3" style="display:none;">
                    <div class="col-sm-12">
                        <div style="background:#ededed; border-radius:10px; padding:18px;">
                            <div><b>Conducted By</b> <span id="cd_conducted_by"></span></div>
                            <div><b>Minimum credits</b> <span id="cd_min_credits"></span></div>
                            <div><b>Medium</b> <span id="cd_medium"></span></div>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="batch" class="col-sm-2 col-form-label">Batch Name / Code <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="batch" name="batch" placeholder="e.g., 2024-Sep-CS" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="batch_size" class="col-sm-2 col-form-label">Batch Size <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="batch_size" name="batch_size" placeholder="Enter number of students" min="1" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intake_mode" class="col-sm-2 col-form-label">Intake Mode <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intake_mode" name="intake_mode" required>
                            <option selected disabled value="">Choose a mode...</option>
                            <option value="Physical">Physical</option>
                            <option value="Online">Online</option>
                            <option value="Hybrid">Hybrid</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intake_type" class="col-sm-2 col-form-label">Intake Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intake_type" name="intake_type" required>
                            <option selected disabled value="">Choose a type...</option>
                            <option value="Fulltime">Full Time</option>
                            <option value="Parttime">Part Time</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="registration_fee" class="col-sm-2 col-form-label">Registration Fee (LKR) <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="registration_fee" name="registration_fee" placeholder="e.g., 5000.00" step="0.01" min="0" required>
                    </div>
                </div>

                    <!-- Degree/Diploma Only Fields -->
                    <div id="degree_diploma_fields" style="display: none;">
                        <div class="row mb-3 align-items-center mx-3">
                            <label for="franchise_payment" class="col-sm-3 col-form-label fw-bold">Franchise Payment <span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <div class="input-group">
                                    <select class="form-select" id="franchise_payment_currency" name="franchise_payment_currency" style="max-width:90px; flex-shrink:0;">
                                        <option value="LKR">LKR</option>
                                        <option value="USD">USD</option>
                                        <option value="GBP">GBP</option>
                                        <option value="EUR">EUR</option>
                                    </select>
                                    <input type="number" class="form-control" id="franchise_payment" name="franchise_payment" placeholder="e.g., 10000.00" step="0.01" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="sscl_tax" class="col-sm-2 col-form-label">SSCL Tax Percentage <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <input type="number" class="form-control" id="sscl_tax" name="sscl_tax" placeholder="e.g., 15.00" step="0.01" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="bank_charges" class="col-sm-2 col-form-label">Bank Charges (LKR)<span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="bank_charges" name="bank_charges" placeholder="e.g., 500.00" step="0.01" min="0">
                            </div>
                        </div>
                    </div>

                    <!-- Certificate Only Fields -->
                    <div id="certificate_fields" style="display: none;">
                        <div class="mb-3 row mx-3">
                            <label for="modules_select" class="col-sm-2 col-form-label">Select Modules <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select" id="modules_select">
                                    <option value="">Choose modules to add...</option>
                                    <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($module->module_id); ?>" data-name="<?php echo e($module->module_name); ?>" data-code="<?php echo e($module->module_code); ?>">
                                            <?php echo e($module->module_code); ?> - <?php echo e($module->module_name); ?>

                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                                <small class="form-text text-muted">Select modules one at a time to add them to the intake</small>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label class="col-sm-2 col-form-label">Selected Modules</label>
                            <div class="col-sm-10">
                                <div id="selected_modules_container" class="border rounded p-3" style="min-height: 100px; background-color: #f8f9fa;">
                                    <div id="selected_modules_list"></div>
                                    <div id="no_modules_message" class="text-muted text-center">No modules selected yet</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3 row mx-3">
                        <label for="course_fee" class="col-sm-2 col-form-label">Course Fee (LKR) <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="course_fee" name="course_fee" placeholder="e.g., 250000.00" step="0.01" min="0" required>
                        </div>
                    </div>
                <div class="mb-3 row mx-3">
                    <label for="start_date" class="col-sm-2 col-form-label">Start Date <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="start_date" name="start_date" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="end_date" class="col-sm-2 col-form-label">End Date <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="end_date" name="end_date" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="enrollment_end_date" class="col-sm-2 col-form-label">Enrollment End Date</label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="enrollment_end_date" name="enrollment_end_date">
                        <small class="form-text text-muted">Last date for students to enroll in this intake (optional)</small>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course_registration_id_pattern" class="col-sm-2 col-form-label">Course Registration ID pattern</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="course_registration_id_pattern" name="course_registration_id_pattern" placeholder="e.g., REG-2023-001" required>
                    </div>
                </div>
                </div><!-- End intake_fields_container -->

                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary" id="submitIntakeBtn" disabled>Create Intake</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Existing Intakes</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-danger" id="bulkDeleteIntakeBtn" style="display:none;">
                        <i class="ti ti-trash"></i> Delete Selected
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="exportIntakeBtn">
                        <i class="ti ti-download"></i> Export CSV
                    </button>
                </div>
            </div>
            <hr>
            
            <!-- Table Controls -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="searchIntakeInput" placeholder="Search intakes...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterIntakeLocation">
                        <option value="">All Locations</option>
                        <option value="Welisara">Welisara</option>
                        <option value="Moratuwa">Moratuwa</option>
                        <option value="Peradeniya">Peradeniya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterIntakeMode">
                        <option value="">All Modes</option>
                        <option value="Physical">Physical</option>
                        <option value="Online">Online</option>
                        <option value="Hybrid">Hybrid</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterIntakeStatus">
                        <option value="">All Status</option>
                        <option value="upcoming">Upcoming</option>
                        <option value="ongoing">Ongoing</option>
                        <option value="finished">Finished</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="perPageIntakeSelect">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="all">Show All</option>
                    </select>
                </div>
                <div class="col-md-1">
                    <button class="btn btn-outline-secondary w-100" id="clearIntakeFiltersBtn" title="Clear Filters">
                        <i class="ti ti-filter-off"></i>
                    </button>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted" id="intakeResultsInfo">Showing 0 intakes</small>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllIntakes">
                    <label class="form-check-label" for="selectAllIntakes">
                        <small>Select All</small>
                    </label>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                <table class="table table-striped table-bordered table-hover" style="table-layout: fixed; width: max-content; min-width: 1200px;">
                    <thead style="position: sticky; top: 0; background: #fff; z-index: 2;">
                        <tr>
                            <th style="position: sticky; top: 0; background: #fff; width: 40px;">
                                <input type="checkbox" id="selectAllIntakesHeader" class="form-check-input">
                            </th>
                            <th class="sortable-intake" data-column="course_name" style="position: sticky; top: 0; background: #fff; width: 180px; cursor: pointer;">
                                Course Name <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="batch" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Batch <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="location" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Location <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="intake_mode" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Mode <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="intake_type" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Type <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="start_date" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Start Date <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-intake" data-column="end_date" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                End Date <i class="ti ti-selector"></i>
                            </th>
                            <th style="position: sticky; top: 0; background: #fff;">Enrollment End</th>
                            <th style="position: sticky; top: 0; background: #fff;">Capacity</th>
                            <th class="sortable-intake" data-column="status" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Status <i class="ti ti-selector"></i>
                            </th>
                            <th style="position: sticky; top: 0; background: #fff; width: 120px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="intake-table-body">
                        <?php $__empty_1 = true; $__currentLoopData = $intakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $intake): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr id="intake-row-<?php echo e($intake->intake_id); ?>" data-intake-id="<?php echo e($intake->intake_id); ?>">
                            <td>
                                <input type="checkbox" class="form-check-input intake-checkbox" data-intake-id="<?php echo e($intake->intake_id); ?>">
                            </td>
                            <td class="intake-course-name" style="word-break: break-word;"><?php echo e($intake->course_name); ?></td>
                            <td class="intake-batch"><?php echo e($intake->batch); ?></td>
                            <td class="intake-location"><?php echo e($intake->location); ?></td>
                            <td class="intake-mode"><?php echo e($intake->intake_mode); ?></td>
                            <td class="intake-type"><?php echo e($intake->intake_type); ?></td>
                            <td class="intake-start-date"><?php echo e($intake->start_date ? (is_string($intake->start_date) ? \Carbon\Carbon::parse($intake->start_date)->format('Y-m-d') : $intake->start_date->format('Y-m-d')) : ''); ?></td>
                            <td class="intake-end-date"><?php echo e($intake->end_date ? (is_string($intake->end_date) ? \Carbon\Carbon::parse($intake->end_date)->format('Y-m-d') : $intake->end_date->format('Y-m-d')) : ''); ?></td>
                            <td class="intake-enrollment-end"><?php echo e($intake->enrollment_end_date ? (is_string($intake->enrollment_end_date) ? \Carbon\Carbon::parse($intake->enrollment_end_date)->format('Y-m-d') : $intake->enrollment_end_date->format('Y-m-d')) : '-'); ?></td>
                            <td class="intake-capacity"><?php echo e($intake->registrations->count()); ?> / <?php echo e($intake->batch_size); ?></td>
                            <td class="intake-status" data-status="<?php echo e($intake->isPast() ? 'finished' : ($intake->isCurrent() ? 'ongoing' : 'upcoming')); ?>">
                                <?php if($intake->isPast()): ?>
                                    <span class="badge bg-danger">Finished</span>
                                <?php elseif($intake->isCurrent()): ?>
                                    <span class="badge bg-success">Ongoing</span>
                                <?php else: ?>
                                    <span class="badge bg-warning">Upcoming</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button type="button" class="btn btn-outline-primary edit-intake-btn" data-intake-id="<?php echo e($intake->intake_id); ?>" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr id="no-intakes-row">
                            <td colspan="12" class="text-center">No intakes found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Intake pagination" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center" id="intakePagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Edit Intake Modal -->
<div class="modal fade" id="editIntakeModal" tabindex="-1" aria-labelledby="editIntakeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editIntakeModalLabel">Edit Intake</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editIntakeForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="edit_intake_id" name="intake_id">
                    
                    <div class="mb-3 row">
                        <label for="edit_location" class="col-sm-3 col-form-label">Location <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_location" name="location" required>
                                <option value="">Choose a location...</option>
                                <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_course_name" class="col-sm-3 col-form-label">Course <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_course_id" name="course_id" required>
                                <option value="">Choose a course...</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_batch" class="col-sm-3 col-form-label">Batch Name / Code <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit_batch" name="batch" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_batch_size" class="col-sm-3 col-form-label">Batch Size <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="edit_batch_size" name="batch_size" min="1" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_intake_mode" class="col-sm-3 col-form-label">Intake Mode <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_intake_mode" name="intake_mode" required>
                                <option value="">Choose a mode...</option>
                                <option value="Physical">Physical</option>
                                <option value="Online">Online</option>
                                <option value="Hybrid">Hybrid</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_intake_type" class="col-sm-3 col-form-label">Intake Type <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_intake_type" name="intake_type" required>
                                <option value="">Choose a type...</option>
                                <option value="Fulltime">Full Time</option>
                                <option value="Parttime">Part Time</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_registration_fee" class="col-sm-3 col-form-label">Registration Fee (LKR) <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="edit_registration_fee" name="registration_fee" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_franchise_payment" class="col-sm-3 col-form-label">Franchise Payment <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <select class="form-select" id="edit_franchise_payment_currency" name="franchise_payment_currency" style="max-width:90px;">
                                    <option value="LKR">LKR</option>
                                    <option value="USD">USD</option>
                                    <option value="GBP">GBP</option>
                                    <option value="EUR">EUR</option>
                                </select>
                                <input type="number" class="form-control" id="edit_franchise_payment" name="franchise_payment" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_course_fee" class="col-sm-3 col-form-label">Course Fee (LKR) <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="edit_course_fee" name="course_fee" step="0.01" min="0" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_sscl_tax" class="col-sm-3 col-form-label">SSCL Tax Percentage <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <div class="input-group">
                                <input type="number" class="form-control" id="edit_sscl_tax" name="sscl_tax" step="0.01" min="0" max="100" required>
                                <span class="input-group-text">%</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_bank_charges" class="col-sm-3 col-form-label">Bank Charges (LKR)</label>
                        <div class="col-sm-9">
                            <input type="number" class="form-control" id="edit_bank_charges" name="bank_charges" step="0.01" min="0">
                        </div>
                    </div>
                    
                    <div class="mb-3 row" id="edit_certificate_fields" style="display: none;">
                        <label for="edit_modules_select" class="col-sm-3 col-form-label">Select Modules <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <select class="form-select" id="edit_modules_select">
                                <option value="">Choose modules to add...</option>
                                <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($module->module_id); ?>" data-name="<?php echo e($module->module_name); ?>" data-code="<?php echo e($module->module_code); ?>">
                                        <?php echo e($module->module_code); ?> - <?php echo e($module->module_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                    </div>
                    
                    <div class="mb-3 row" id="edit_selected_modules_row" style="display: none;">
                        <label class="col-sm-3 col-form-label">Selected Modules</label>
                        <div class="col-sm-9">
                            <div id="edit_selected_modules_container" class="border rounded p-3" style="min-height: 100px; background-color: #f8f9fa;">
                                <div id="edit_selected_modules_list"></div>
                                <div id="edit_no_modules_message" class="text-muted text-center">No modules selected yet</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_start_date" class="col-sm-3 col-form-label">Start Date <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="edit_start_date" name="start_date" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_end_date" class="col-sm-3 col-form-label">End Date <span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="edit_end_date" name="end_date" required>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_enrollment_end_date" class="col-sm-3 col-form-label">Enrollment End Date</label>
                        <div class="col-sm-9">
                            <input type="date" class="form-control" id="edit_enrollment_end_date" name="enrollment_end_date">
                            <small class="form-text text-muted">Last date for students to enroll in this intake (optional)</small>
                        </div>
                    </div>
                    
                    <div class="mb-3 row">
                        <label for="edit_course_registration_id_pattern" class="col-sm-3 col-form-label">Course Registration ID pattern</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="edit_course_registration_id_pattern" name="course_registration_id_pattern" required>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="updateIntakeBtn">Update Intake</button>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script nonce="<?php echo e($cspNonce); ?>">
$(document).ready(function() {
    let allIntakes = [];
    let filteredIntakes = [];
    let currentIntakePage = 1;
    let perIntakePage = 25;
    let sortIntakeColumn = 'start_date';
    let sortIntakeDirection = 'desc';
    const allCourses = <?php echo json_encode($allCoursesForJson, 15, 512) ?>;
    let selectedCourseType = '';
    let selectedModules = []; // Track selected module IDs

    // Handle course type change
    $('#course_type').on('change', function() {
        selectedCourseType = $(this).val();
        const location = $('#location').val();
        
        if (!selectedCourseType) {
            $('#intake_fields_container').hide();
            $('#submitIntakeBtn').prop('disabled', true);
            return;
        }
        
        if (!location) {
            showToast('Please select a location first', 'warning');
            $(this).val('');
            return;
        }
        
        // Show/hide conditional fields
        if (selectedCourseType === 'degree' || selectedCourseType === 'diploma') {
            $('#degree_diploma_fields').show();
            $('#certificate_fields').hide();
            $('#franchise_payment, #sscl_tax, #bank_charges').prop('required', true);
        } else if (selectedCourseType === 'certificate') {
            $('#degree_diploma_fields').hide();
            $('#certificate_fields').show();
            $('#franchise_payment, #sscl_tax, #bank_charges').prop('required', false).val('');
            selectedModules = []; // Reset selected modules
            updateSelectedModulesList();
        }
        
        // Filter and populate course dropdown
        filterCoursesByType(location, selectedCourseType);
        
        // Show fields container
        $('#intake_fields_container').show();
        $('#submitIntakeBtn').prop('disabled', false);
    });
    
    function filterCoursesByType(location, courseType) {
        const $courseSelect = $('#course_id');
        $courseSelect.empty().append('<option selected disabled value="">Choose a course...</option>');
        
        const filteredCourses = allCourses.filter(course => 
            course.location === location && course.course_type === courseType
        );
        
        if (filteredCourses.length === 0) {
            $courseSelect.append('<option disabled>No courses available for this type</option>');
            showToast(`No ${courseType} courses available at ${location}`, 'warning');
        } else {
            filteredCourses.forEach(course => {
                $courseSelect.append(new Option(course.course_name, course.course_id));
            });
        }
    }

    // Module selection handling
    $('#modules_select').on('change', function() {
        const moduleId = parseInt($(this).val());
        if (!moduleId) return;
        
        const option = $(this).find('option:selected');
        const moduleName = option.data('name');
        const moduleCode = option.data('code');
        
        // Check if module already selected
        if (selectedModules.includes(moduleId)) {
            showToast('This module has already been added', 'warning');
            $(this).val('');
            return;
        }
        
        // Add module to selected list
        selectedModules.push(moduleId);
        updateSelectedModulesList();
        
        // Reset dropdown
        $(this).val('');
        showToast(`Module "${moduleCode}" added successfully`, 'success');
    });

    function updateSelectedModulesList() {
        const container = $('#selected_modules_list');
        container.empty();
        
        if (selectedModules.length === 0) {
            $('#no_modules_message').show();
        } else {
            $('#no_modules_message').hide();
            
            selectedModules.forEach(moduleId => {
                const option = $(`#modules_select option[value="${moduleId}"]`);
                const moduleName = option.data('name');
                const moduleCode = option.data('code');
                
                const moduleTag = `
                    <div class="badge bg-primary me-2 mb-2 p-2 d-inline-flex align-items-center" style="font-size: 0.9rem;" data-module-id="${moduleId}">
                        <span>${moduleCode} - ${moduleName}</span>
                        <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="removeModule(${moduleId})" aria-label="Remove"></button>
                    </div>
                `;
                container.append(moduleTag);
            });
        }
    }

    // Global function to remove module
    window.removeModule = function(moduleId) {
        selectedModules = selectedModules.filter(id => id !== moduleId);
        updateSelectedModulesList();
        showToast('Module removed', 'info');
    };

    function formatCourseType(type) {
        if (!type) {
            return '';
        }
        return type.charAt(0).toUpperCase() + type.slice(1);
    }

    function populateEditCourseOptions(location, selectedId) {
        const $select = $('#edit_course_id');
        $select.empty().append('<option value="">Choose a course...</option>');

        if (!location) {
            return;
        }

        const coursesForLocation = allCourses.filter(course => course.location === location);
        coursesForLocation.forEach(course => {
            const label = `${formatCourseType(course.course_type)} - ${course.course_name}`;
            $select.append(new Option(label, course.course_id));
        });

        if (selectedId) {
            $select.val(String(selectedId));
        }
    }

    // Initialize intakes array from table
    function initializeIntakes() {
        allIntakes = [];
        $('#intake-table-body tr[data-intake-id]').each(function() {
            const row = $(this);
            const intake = {
                intake_id: row.data('intake-id'),
                course_name: row.find('.intake-course-name').text().trim(),
                batch: row.find('.intake-batch').text().trim(),
                location: row.find('.intake-location').text().trim(),
                intake_mode: row.find('.intake-mode').text().trim(),
                intake_type: row.find('.intake-type').text().trim(),
                start_date: row.find('.intake-start-date').text().trim(),
                end_date: row.find('.intake-end-date').text().trim(),
                enrollment_end: row.find('.intake-enrollment-end').text().trim(),
                capacity: row.find('.intake-capacity').text().trim(),
                status: row.find('.intake-status').data('status')
            };
            allIntakes.push(intake);
        });
        filteredIntakes = [...allIntakes];
        renderIntakeTable();
    }

    initializeIntakes();

    // Apply all filters function
    function applyIntakeFilters() {
        const searchTerm = $('#searchIntakeInput').val().toLowerCase();
        const filterLoc = $('#filterIntakeLocation').val();
        const filterMode = $('#filterIntakeMode').val();
        const filterStatus = $('#filterIntakeStatus').val();
        
        filteredIntakes = allIntakes.filter(intake => {
            const matchesSearch = !searchTerm || 
                intake.course_name.toLowerCase().includes(searchTerm) ||
                intake.batch.toLowerCase().includes(searchTerm) ||
                intake.location.toLowerCase().includes(searchTerm) ||
                intake.intake_mode.toLowerCase().includes(searchTerm) ||
                intake.intake_type.toLowerCase().includes(searchTerm);
            
            const matchesLocation = !filterLoc || intake.location === filterLoc;
            const matchesMode = !filterMode || intake.intake_mode === filterMode;
            const matchesStatus = !filterStatus || intake.status === filterStatus;
            
            return matchesSearch && matchesLocation && matchesMode && matchesStatus;
        });
        
        currentIntakePage = 1;
        renderIntakeTable();
    }

    // Auto-filter when location changes in form
    $('#location').on('change', function() {
        const selectedLocation = $(this).val();
        if (selectedLocation) {
            $('#filterIntakeLocation').val(selectedLocation);
            applyIntakeFilters();
            showToast(`Table filtered to show ${selectedLocation} intakes`, 'info');
        }
    });

    // Search functionality
    $('#searchIntakeInput').on('keyup', function() {
        applyIntakeFilters();
    });

    // Filter handlers
    $('#filterIntakeLocation, #filterIntakeMode, #filterIntakeStatus').on('change', function() {
        applyIntakeFilters();
    });

    // Clear filters button
    $('#clearIntakeFiltersBtn').on('click', function() {
        $('#searchIntakeInput').val('');
        $('#filterIntakeLocation').val('');
        $('#filterIntakeMode').val('');
        $('#filterIntakeStatus').val('');
        filteredIntakes = [...allIntakes];
        currentIntakePage = 1;
        renderIntakeTable();
        showToast('Filters cleared', 'info');
    });

    // Per page selection
    $('#perPageIntakeSelect').on('change', function() {
        perIntakePage = $(this).val() === 'all' ? filteredIntakes.length : parseInt($(this).val());
        currentIntakePage = 1;
        renderIntakeTable();
    });

    // Sorting
    $('.sortable-intake').on('click', function() {
        const column = $(this).data('column');
        if (sortIntakeColumn === column) {
            sortIntakeDirection = sortIntakeDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortIntakeColumn = column;
            sortIntakeDirection = 'asc';
        }
        
        $('.sortable-intake i').attr('class', 'ti ti-selector');
        $(this).find('i').attr('class', sortIntakeDirection === 'asc' ? 'ti ti-sort-ascending' : 'ti ti-sort-descending');
        
        sortIntakes();
        renderIntakeTable();
    });

    function sortIntakes() {
        filteredIntakes.sort((a, b) => {
            let aVal = a[sortIntakeColumn] || '';
            let bVal = b[sortIntakeColumn] || '';
            
            if (sortIntakeColumn === 'start_date' || sortIntakeColumn === 'end_date') {
                aVal = new Date(aVal);
                bVal = new Date(bVal);
            } else {
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
            }
            
            if (aVal < bVal) return sortIntakeDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortIntakeDirection === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // Render table
    function renderIntakeTable() {
        const start = (currentIntakePage - 1) * perIntakePage;
        const end = start + perIntakePage;
        const pageIntakes = filteredIntakes.slice(start, end);
        
        $('#intake-table-body').empty();
        
        if (pageIntakes.length === 0) {
            $('#intake-table-body').html('<tr><td colspan="12" class="text-center">No intakes found.</td></tr>');
            $('#intakeResultsInfo').text('Showing 0 intakes');
        } else {
            pageIntakes.forEach(intake => {
                let statusBadge = '';
                let badgeClass = '';
                if (intake.status === 'finished') {
                    statusBadge = 'Finished';
                    badgeClass = 'danger';
                } else if (intake.status === 'ongoing') {
                    statusBadge = 'Ongoing';
                    badgeClass = 'success';
                } else {
                    statusBadge = 'Upcoming';
                    badgeClass = 'warning';
                }
                
                const row = `
                    <tr id="intake-row-${intake.intake_id}" data-intake-id="${intake.intake_id}">
                        <td>
                            <input type="checkbox" class="form-check-input intake-checkbox" data-intake-id="${intake.intake_id}">
                        </td>
                        <td class="intake-course-name" style="word-break: break-word;">${intake.course_name}</td>
                        <td class="intake-batch">${intake.batch}</td>
                        <td class="intake-location">${intake.location}</td>
                        <td class="intake-mode">${intake.intake_mode}</td>
                        <td class="intake-type">${intake.intake_type}</td>
                        <td class="intake-start-date">${intake.start_date}</td>
                        <td class="intake-end-date">${intake.end_date}</td>
                        <td class="intake-enrollment-end">${intake.enrollment_end}</td>
                        <td class="intake-capacity">${intake.capacity}</td>
                        <td class="intake-status" data-status="${intake.status}">
                            <span class="badge bg-${badgeClass}">${statusBadge}</span>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button type="button" class="btn btn-outline-primary edit-intake-btn" data-intake-id="${intake.intake_id}" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('#intake-table-body').append(row);
            });
            
            const showing = filteredIntakes.length > perIntakePage ? 
                `Showing ${start + 1}-${Math.min(end, filteredIntakes.length)} of ${filteredIntakes.length} intakes` :
                `Showing ${filteredIntakes.length} intakes`;
            $('#intakeResultsInfo').text(showing);
        }
        
        renderIntakePagination();
    }

    // Render pagination
    function renderIntakePagination() {
        const totalPages = Math.ceil(filteredIntakes.length / perIntakePage);
        $('#intakePagination').empty();
        
        if (totalPages <= 1) return;
        
        $('#intakePagination').append(`
            <li class="page-item ${currentIntakePage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentIntakePage - 1}">Previous</a>
            </li>
        `);
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentIntakePage - 2 && i <= currentIntakePage + 2)) {
                $('#intakePagination').append(`
                    <li class="page-item ${i === currentIntakePage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (i === currentIntakePage - 3 || i === currentIntakePage + 3) {
                $('#intakePagination').append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }
        
        $('#intakePagination').append(`
            <li class="page-item ${currentIntakePage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentIntakePage + 1}">Next</a>
            </li>
        `);
    }

    // Pagination click
    $(document).on('click', '#intakePagination a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page > 0 && page <= Math.ceil(filteredIntakes.length / perIntakePage)) {
            currentIntakePage = page;
            renderIntakeTable();
            $('.table-responsive').scrollTop(0);
        }
    });

    // Select all checkboxes
    $('#selectAllIntakes, #selectAllIntakesHeader').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('#selectAllIntakes, #selectAllIntakesHeader').prop('checked', isChecked);
        $('.intake-checkbox').prop('checked', isChecked);
        updateBulkDeleteIntakeButton();
    });

    $(document).on('change', '.intake-checkbox', function() {
        updateBulkDeleteIntakeButton();
        const total = $('.intake-checkbox').length;
        const checked = $('.intake-checkbox:checked').length;
        $('#selectAllIntakes, #selectAllIntakesHeader').prop('checked', total === checked);
    });

    function updateBulkDeleteIntakeButton() {
        const checkedCount = $('.intake-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteIntakeBtn').show().text(`Delete Selected (${checkedCount})`);
        } else {
            $('#bulkDeleteIntakeBtn').hide();
        }
    }

    // Export to CSV
    $('#exportIntakeBtn').on('click', function() {
        const csv = [];
        csv.push(['Course Name', 'Batch', 'Location', 'Mode', 'Type', 'Start Date', 'End Date', 'Enrollment End', 'Capacity', 'Status'].join(','));
        
        filteredIntakes.forEach(intake => {
            csv.push([
                `"${intake.course_name}"`,
                `"${intake.batch}"`,
                `"${intake.location}"`,
                `"${intake.intake_mode}"`,
                `"${intake.intake_type}"`,
                `"${intake.start_date}"`,
                `"${intake.end_date}"`,
                `"${intake.enrollment_end}"`,
                `"${intake.capacity}"`,
                `"${intake.status}"`
            ].join(','));
        });
        
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `intakes_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
        showToast('Intakes exported successfully', 'success');
    });

    // Form submission
    $('#intakeForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate modules for certificate courses
        if (selectedCourseType === 'certificate' && selectedModules.length === 0) {
            showToast('Please select at least one module for certificate course', 'danger');
            return;
        }
        
        const formData = new FormData(this);
        
        // Add selected modules to form data
        if (selectedCourseType === 'certificate') {
            selectedModules.forEach(moduleId => {
                formData.append('module_ids[]', moduleId);
            });
        }

        $.ajax({
            url: '<?php echo e(route("intake.store")); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#intakeForm')[0].reset();
                    selectedModules = []; // Reset modules
                    updateSelectedModulesList();
                    $('#intake_fields_container').hide();
                    $('#degree_diploma_fields').hide();
                    $('#certificate_fields').hide();
                    
                    const intake = response.intake;
                    const status = intake.isPast ? 'finished' : (intake.isCurrent ? 'ongoing' : 'upcoming');
                    
                    allIntakes.unshift({
                        intake_id: intake.intake_id,
                        course_name: intake.course_name,
                        batch: intake.batch,
                        location: intake.location,
                        intake_mode: intake.intake_mode,
                        intake_type: intake.intake_type,
                        start_date: formatDate(intake.start_date),
                        end_date: formatDate(intake.end_date),
                        enrollment_end: intake.enrollment_end_date ? formatDate(intake.enrollment_end_date) : '-',
                        capacity: `${intake.registrations_count ?? 0} / ${intake.batch_size}`,
                        status: status
                    });
                    
                    applyIntakeFilters();
                    
                    setTimeout(() => {
                        const row = $(`#intake-row-${intake.intake_id}`);
                        if (row.length) {
                            $('html, body').animate({
                                scrollTop: row.offset().top - 150
                            }, 800);
                            row.addClass('table-success');
                            setTimeout(() => row.removeClass('table-success'), 2500);
                        }
                    }, 300);
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while creating the intake.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage += '<br>' + errors.join('<br>');
                    }
                }
                showToast(errorMessage, 'danger');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.edit-intake-btn', function() {
        const intakeId = $(this).data('intake-id');
        editIntake(intakeId);
    });

    let editSelectedModules = []; // Track selected module IDs for edit modal

    // Edit modal module selection
    $('#edit_modules_select').on('change', function() {
        const moduleId = parseInt($(this).val());
        if (!moduleId) return;
        
        const option = $(this).find('option:selected');
        const moduleName = option.data('name');
        const moduleCode = option.data('code');
        
        if (editSelectedModules.includes(moduleId)) {
            showToast('This module has already been added', 'warning');
            $(this).val('');
            return;
        }
        
        editSelectedModules.push(moduleId);
        updateEditSelectedModulesList();
        $(this).val('');
        showToast(`Module "${moduleCode}" added successfully`, 'success');
    });

    function updateEditSelectedModulesList() {
        const container = $('#edit_selected_modules_list');
        container.empty();
        
        if (editSelectedModules.length === 0) {
            $('#edit_no_modules_message').show();
        } else {
            $('#edit_no_modules_message').hide();
            
            editSelectedModules.forEach(moduleId => {
                const option = $(`#edit_modules_select option[value="${moduleId}"]`);
                const moduleName = option.data('name');
                const moduleCode = option.data('code');
                
                const moduleTag = `
                    <div class="badge bg-primary me-2 mb-2 p-2 d-inline-flex align-items-center" style="font-size: 0.9rem;" data-module-id="${moduleId}">
                        <span>${moduleCode} - ${moduleName}</span>
                        <button type="button" class="btn-close btn-close-white ms-2" style="font-size: 0.6rem;" onclick="removeEditModule(${moduleId})" aria-label="Remove"></button>
                    </div>
                `;
                container.append(moduleTag);
            });
        }
    }

    window.removeEditModule = function(moduleId) {
        editSelectedModules = editSelectedModules.filter(id => id !== moduleId);
        updateEditSelectedModulesList();
        showToast('Module removed', 'info');
    };

    function editIntake(intakeId) {
        $.ajax({
            url: `/intake-creation/${intakeId}/edit`,
            type: 'GET',
            success: function(response) {
                if (response.success) {
                    const intake = response.intake;

                    $('#edit_intake_id').val(intake.intake_id);
                    $('#edit_location').val(intake.location);
                    populateEditCourseOptions(intake.location, intake.course_id);
                    $('#edit_batch').val(intake.batch);
                    $('#edit_batch_size').val(intake.batch_size);
                    $('#edit_intake_mode').val(intake.intake_mode);
                    $('#edit_intake_type').val(intake.intake_type);
                    $('#edit_registration_fee').val(intake.registration_fee);
                    $('#edit_franchise_payment').val(intake.franchise_payment);
                    $('#edit_franchise_payment_currency').val(intake.franchise_payment_currency);
                    $('#edit_course_fee').val(intake.course_fee);
                    $('#edit_sscl_tax').val(intake.sscl_tax);
                    $('#edit_bank_charges').val(intake.bank_charges);

                    $('#edit_start_date').val(formatDateForInput(intake.start_date));
                    $('#edit_end_date').val(formatDateForInput(intake.end_date));
                    $('#edit_enrollment_end_date').val(formatDateForInput(intake.enrollment_end_date));

                    $('#edit_course_registration_id_pattern').val(intake.course_registration_id_pattern);

                    // Handle modules for certificate courses
                    if (intake.course && intake.course.course_type === 'certificate') {
                        $('#edit_certificate_fields').show();
                        $('#edit_selected_modules_row').show();
                        
                        // Load existing modules
                        editSelectedModules = [];
                        if (intake.modules && intake.modules.length > 0) {
                            intake.modules.forEach(module => {
                                editSelectedModules.push(module.module_id);
                            });
                        }
                        updateEditSelectedModulesList();
                    } else {
                        $('#edit_certificate_fields').hide();
                        $('#edit_selected_modules_row').hide();
                        editSelectedModules = [];
                    }

                    $('#editIntakeModal').modal('show');
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                showToast('Error loading intake data.', 'danger');
            }
        });
    }

    // Update intake
    $('#updateIntakeBtn').on('click', function() {
        const intakeId = $('#edit_intake_id').val();
        
        if (!intakeId) {
            showToast('Intake ID not found. Please try again.', 'danger');
            return;
        }
        
        const formData = new FormData($('#editIntakeForm')[0]);
        formData.append('_method', 'PUT');
        
        // Add modules for certificate courses
        const courseId = $('#edit_course_id').val();
        const selectedCourse = allCourses.find(c => c.course_id == courseId);
        if (selectedCourse && selectedCourse.course_type === 'certificate') {
            if (editSelectedModules.length === 0) {
                showToast('Please select at least one module for certificate course', 'danger');
                return;
            }
            editSelectedModules.forEach(moduleId => {
                formData.append('module_ids[]', moduleId);
            });
        }
        
        $.ajax({
            url: `/intake-creation/${intakeId}`,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    $('#editIntakeModal').modal('hide');
                    
                    const intake = response.intake;
                    const index = allIntakes.findIndex(i => i.intake_id == intake.intake_id);
                    
                    const startDate = new Date(intake.start_date);
                    const endDate = new Date(intake.end_date);
                    const now = new Date();
                    let status = 'upcoming';
                    if (now > endDate) status = 'finished';
                    else if (now >= startDate && now <= endDate) status = 'ongoing';
                    
                    if (index !== -1) {
                        allIntakes[index] = {
                            intake_id: intake.intake_id,
                            course_name: intake.course_name,
                            batch: intake.batch,
                            location: intake.location,
                            intake_mode: intake.intake_mode,
                            intake_type: intake.intake_type,
                            start_date: formatDate(intake.start_date),
                            end_date: formatDate(intake.end_date),
                            enrollment_end: intake.enrollment_end_date ? formatDate(intake.enrollment_end_date) : '-',
                            capacity: `${intake.registrations_count || 0} / ${intake.batch_size}`,
                            status: status
                        };
                    }
                    
                    applyIntakeFilters();
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                let errorMessage = 'An error occurred while updating the intake.';
                if (xhr.responseJSON) {
                    if (xhr.responseJSON.error) {
                        errorMessage = xhr.responseJSON.error;
                    } else if (xhr.responseJSON.message) {
                        errorMessage = xhr.responseJSON.message;
                    }
                    if (xhr.responseJSON.errors) {
                        const errors = Object.values(xhr.responseJSON.errors).flat();
                        errorMessage += '<br>' + errors.join('<br>');
                    }
                }
                showToast(errorMessage, 'danger');
            }
        });
    });

    // Course details fetch
    $('#course_id').on('change', function() {
        const courseId = $(this).val();
        if (!courseId) {
            $('#courseDetailsBox').hide();
            return;
        }
        
        $.ajax({
            url: '/api/courses/' + courseId,
            type: 'GET',
            success: function(response) {
                if (response.success && response.course) {
                    const c = response.course;
                    $('#cd_duration').text(c.duration_formatted ? c.duration_formatted : '-');
                    $('#cd_min_credits').text(c.min_credits ? c.min_credits : '-');
                    $('#cd_training').text(c.training_period ? c.training_period : '-');
                    $('#cd_entry_qualification').html(c.entry_qualification ? c.entry_qualification.replace(/\n/g, '<br>') : '-');
                    $('#cd_medium').text(c.course_medium ? c.course_medium : '-');
                    $('#cd_conducted_by').text(c.conducted_by ? c.conducted_by : '-');
                    $('#courseDetailsBox').show();
                } else {
                    $('#courseDetailsBox').hide();
                }
            },
            error: function() {
                $('#courseDetailsBox').hide();
            }
        });
    });

    // Autofill payment plan
    function autofillPaymentPlan() {
        const courseId = $('#course_id').val();
        const location = $('#location').val();
        const courseType = $('#intake_type').val();
        if (!courseId || !location || !courseType) return;
        
        $.ajax({
            url: '/get-payment-plan-details',
            type: 'POST',
            data: {
                _token: $('input[name="_token"]').val(),
                course_id: courseId,
                location: location,
                course_type: courseType
            },
            success: function(response) {
                if (response.success) {
                    $('#registration_fee').val(response.registration_fee);
                    $('#course_fee').val(response.course_fee);
                }
            }
        });
    }

    $('#course_id, #location, #intake_type').on('change', autofillPaymentPlan);

    $('#edit_location').on('change', function() {
        populateEditCourseOptions($(this).val());
    });

    // Enrollment end date validation
    $('#enrollment_end_date').on('blur change', function() {
        const value = $(this).val();
        if (!value) return;

        const enrollmentEndDate = new Date(value);
        if (isNaN(enrollmentEndDate)) return;

        const startVal = $('#start_date').val();
        const startDate = startVal ? new Date(startVal) : null;

        if (startDate) {
            // Calculate one month after start date
            const oneMonthAfterStart = new Date(startDate);
            oneMonthAfterStart.setMonth(oneMonthAfterStart.getMonth() + 1);

            if (enrollmentEndDate > oneMonthAfterStart) {
                showToast('Enrollment end date cannot be more than one month after the course start date.', 'danger');
                $(this).val('');
            }
        }
    });

    $('#edit_enrollment_end_date').on('blur change', function() {
        const value = $(this).val();
        if (!value) return;

        const enrollmentEndDate = new Date(value);
        if (isNaN(enrollmentEndDate)) return;

        const startVal = $('#edit_start_date').val();
        const startDate = startVal ? new Date(startVal) : null;

        if (startDate) {
            // Calculate one month after start date
            const oneMonthAfterStart = new Date(startDate);
            oneMonthAfterStart.setMonth(oneMonthAfterStart.getMonth() + 1);

            if (enrollmentEndDate > oneMonthAfterStart) {
                showToast('Enrollment end date cannot be more than one month after the course start date.', 'danger');
                $(this).val('');
            }
        }
    });

    function showToast(message, type) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        $('.toast-container').append(toastHtml);
        const toastEl = $('.toast-container .toast').last();
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }

    function formatDate(dateStr) {
        if (!dateStr) return '';
        const d = new Date(dateStr);
        if (isNaN(d)) return dateStr;
        return d.toISOString().slice(0, 10);
    }

    function formatDateForInput(dateValue) {
        if (!dateValue) return '';
        const dateObj = new Date(dateValue);
        if (isNaN(dateObj)) return '';
        return dateObj.toISOString().split('T')[0];
    }
});

window.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('intakeForm');
    const locationSelect = document.getElementById('location');
    const courseTypeSelect = document.getElementById('course_type');

    function toggleFields() {
        const hasLocation = locationSelect.value !== '';

        // Enable course type only if location is selected
        courseTypeSelect.disabled = !hasLocation;
        if (!hasLocation) {
            courseTypeSelect.classList.add('locked-field');
        } else {
            courseTypeSelect.classList.remove('locked-field');
        }
    }

    // Run on load and when location changes
    toggleFields();
    locationSelect.addEventListener('change', function() {
        toggleFields();
        // Reset course type and hide fields when location changes
        courseTypeSelect.value = '';
        document.getElementById('intake_fields_container').style.display = 'none';
        document.getElementById('submitIntakeBtn').disabled = true;
        
        if (this.value) {
            window.location = '?location=' + this.value;
        }
    });
});

</script>


<style nonce="<?php echo e($cspNonce); ?>">
.table th {
    font-size: 0.95rem !important;
    font-weight: 600;
    background: #f5f7fa;
}
.table td {
    font-size: 0.9rem !important;
}
/* Apply soft visual dim only to locked inputs */
.locked-field {
    background-color: #f1f3f5 !important;
    cursor: not-allowed;
    opacity: 1 !important; /* Keep text readable */
}

/* Keep labels and text clear */
#intakeForm label {
    opacity: 1 !important;
}

</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/courses_&_modules/intake_creation.blade.php ENDPATH**/ ?>