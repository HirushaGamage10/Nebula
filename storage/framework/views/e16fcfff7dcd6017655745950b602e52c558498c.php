

<?php $__env->startSection('title', 'NEBULA | Module Creation'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Create New Module</h2>
            <hr>
            
            <!-- Tabs Navigation -->
            <ul class="nav nav-tabs mb-4" id="moduleTypeTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree-module" type="button" role="tab">
                        <i class="ti ti-school me-2"></i>Degree/Diploma Modules
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate-module" type="button" role="tab">
                        <i class="ti ti-certificate me-2"></i>Certificate Modules
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="moduleTypeTabContent">
                <!-- Degree/Diploma Module Form -->
                <div class="tab-pane fade show active" id="degree-module" role="tabpanel">
                    <form id="degreeModuleForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="degree_module_id" name="module_id">
                        <input type="hidden" name="module_category" value="degree">
                        <div class="mb-3 row mx-3">
                            <label for="degree_module_name" class="col-sm-2 col-form-label">Module Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                               <input type="text" class="form-control" id="degree_module_name" name="module_name" placeholder="Enter module name" required style="text-transform:none !important;">
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="degree_module_code" class="col-sm-2 col-form-label">Module Code <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="degree_module_code" name="module_code" 
                                       placeholder="e.g., CS101_Programming_001" 
                                       pattern="^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$"
                                       title="Module code must follow the pattern: program_name_specification_unit_code"
                                       required>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Format: <code>programName_unitName_unitCode</code> (e.g., CS101_Programming_001)
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="degree_credits" class="col-sm-2 col-form-label">Credits <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="degree_credits" name="credits" placeholder="Enter module credits" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="degree_module_type" class="col-sm-2 col-form-label">Module Type <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <select class="form-select" id="degree_module_type" name="module_type" required>
                                    <option selected disabled value="">Choose a type...</option>
                                    <option value="core">Core</option>
                                    <option value="elective">Elective</option>
                                    <option value="special_unit_compulsory">Special Unit Compulsory (S/U)</option>
                                </select>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary" id="degreeModuleSubmitBtn">Create Degree Module</button>
                            <button type="button" class="btn btn-secondary mt-2" id="degreeCancelEditBtn" style="display:none;">Cancel Edit</button>
                        </div>
                    </form>
                </div>

                <!-- Certificate Module Form -->
                <div class="tab-pane fade" id="certificate-module" role="tabpanel">
                    <form id="certificateModuleForm">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" id="cert_module_id" name="module_id">
                        <input type="hidden" name="module_category" value="certificate">
                        <div class="mb-3 row mx-3">
                            <label for="cert_module_name" class="col-sm-2 col-form-label">Module Name <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                               <input type="text" class="form-control" id="cert_module_name" name="module_name" placeholder="Enter module name" required style="text-transform:none !important;">
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="cert_module_code" class="col-sm-2 col-form-label">Module Code <span class="text-danger">*</span></label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="cert_module_code" name="module_code" 
                                       placeholder="e.g., CERT_BasicCoding_001" 
                                       pattern="^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$"
                                       title="Module code must follow the pattern: program_name_specification_unit_code"
                                       required>
                                <div class="form-text">
                                    <small class="text-muted">
                                        <i class="ti ti-info-circle me-1"></i>
                                        Format: <code>programName_unitName_unitCode</code> (e.g., CERT_BasicCoding_001)
                                    </small>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid mt-3">
                            <button type="submit" class="btn btn-primary" id="certModuleSubmitBtn">Create Certificate Module</button>
                            <button type="button" class="btn btn-secondary mt-2" id="certCancelEditBtn" style="display:none;">Cancel Edit</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Existing Modules</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-danger" id="bulkDeleteBtn" style="display:none;">
                        <i class="ti ti-trash"></i> Delete Selected
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="exportBtn">
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
                        <input type="text" class="form-control" id="searchInput" placeholder="Search modules...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterCategory">
                        <option value="">All Categories</option>
                        <option value="degree">Degree/Diploma</option>
                        <option value="certificate">Certificate</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterType">
                        <option value="">All Types</option>
                        <option value="core">Core</option>
                        <option value="elective">Elective</option>
                        <option value="special_unit_compulsory">Special Unit Compulsory</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="perPageSelect">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="100">100 per page</option>
                        <option value="all">Show All</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-outline-secondary w-100" id="clearFiltersBtn">
                        <i class="ti ti-filter-off"></i> Clear Filters
                    </button>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted" id="resultsInfo">Showing 0 modules</small>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAll">
                    <label class="form-check-label" for="selectAll">
                        <small>Select All</small>
                    </label>
                </div>
            </div>

            <!-- Table -->
            <div class="scrollable-table-container" style="max-height: 500px; overflow-y: auto; width: 100%;">
                <table class="table table-striped table-bordered table-hover scrollable-table" style="width: 100%;">
                    <thead style="position: sticky; top: 0; background: #fff; z-index: 2;">
                        <tr>
                            <th style="position: sticky; top: 0; background: #fff; width: 40px;">
                                <input type="checkbox" id="selectAllHeader" class="form-check-input">
                            </th>
                            <th class="sortable" data-column="module_name" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Module Name <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable" data-column="module_code" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Module Code <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable" data-column="module_category" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Category <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable" data-column="credits" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Credits <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable" data-column="module_type" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Type <i class="ti ti-selector"></i>
                            </th>
                            <th style="position: sticky; top: 0; background: #fff; width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="module-table-body">
                        <?php $__empty_1 = true; $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr id="module-row-<?php echo e($module->module_id); ?>" data-module-id="<?php echo e($module->module_id); ?>">
                            <td>
                                <input type="checkbox" class="form-check-input module-checkbox" data-module-id="<?php echo e($module->module_id); ?>">
                            </td>
                            <td class="module-name" style="text-transform:none !important;"><?php echo e($module->module_name); ?></td>
                            <td class="module-code" style="text-transform:none !important;"><?php echo e($module->module_code); ?></td>
                            <td class="module-category">
                                <?php if($module->module_category == 'certificate'): ?>
                                    <span class="badge bg-success">Certificate</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Degree/Diploma</span>
                                <?php endif; ?>
                            </td>
                            <td class="module-credits"><?php echo e($module->credits ?? 'N/A'); ?></td>
                            <td class="module-type">
                                <?php if($module->module_type == 'core'): ?>
                                    <span class="badge bg-primary">Core</span>
                                <?php elseif($module->module_type == 'elective'): ?>
                                    <span class="badge bg-info">Elective</span>
                                <?php elseif($module->module_type == 'special_unit_compulsory'): ?>
                                    <span class="badge bg-warning">S/U</span>
                                <?php else: ?>
                                    <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary edit-module-btn" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-module-btn" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr id="no-modules-row">
                            <td colspan="7" class="text-center">No modules found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Module pagination" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center" id="pagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script nonce="<?php echo e($cspNonce); ?>">
$(document).ready(function() {
    let degreeEditMode = false;
    let certEditMode = false;
    let editingModuleId = null;
    let allModules = [];
    let filteredModules = [];
    let currentPage = 1;
    let perPage = 25;
    let sortColumn = 'module_name';
    let sortDirection = 'asc';

    // Initialize modules array from table
    function initializeModules() {
        allModules = [];
        $('#module-table-body tr[data-module-id]').each(function() {
            const row = $(this);
            const categoryBadge = row.find('.module-category .badge').text().trim();
            const moduleCategory = categoryBadge === 'Certificate' ? 'certificate' : 'degree';
            
            const module = {
                module_id: row.data('module-id'),
                module_name: row.find('.module-name').text().trim(),
                module_code: row.find('.module-code').text().trim(),
                module_category: moduleCategory,
                credits: row.find('.module-credits').text().trim(),
                module_type: row.find('.module-type').text().trim().toLowerCase()
            };
            allModules.push(module);
        });
        filteredModules = [...allModules];
        renderTable();
    }

    initializeModules();

    // Apply all filters function
    function applyFilters() {
        const searchTerm = $('#searchInput').val().toLowerCase();
        const filterCategory = $('#filterCategory').val();
        const filterType = $('#filterType').val();
        
        filteredModules = allModules.filter(module => {
            const matchesSearch = !searchTerm || 
                module.module_name.toLowerCase().includes(searchTerm) ||
                module.module_code.toLowerCase().includes(searchTerm) ||
                module.credits.toString().includes(searchTerm) ||
                module.module_type.includes(searchTerm);
            
            const matchesCategory = !filterCategory || module.module_category === filterCategory;
            const matchesType = !filterType || module.module_type === filterType;
            
            return matchesSearch && matchesCategory && matchesType;
        });
        
        currentPage = 1;
        renderTable();
    }

    // Search functionality
    $('#searchInput').on('keyup', function() {
        applyFilters();
    });

    // Filter handlers
    $('#filterCategory, #filterType').on('change', function() {
        applyFilters();
    });

    // Clear filters button
    $('#clearFiltersBtn').on('click', function() {
        $('#searchInput').val('');
        $('#filterCategory').val('');
        $('#filterType').val('');
        filteredModules = [...allModules];
        currentPage = 1;
        renderTable();
        showToast('Filters cleared', 'info');
    });

    // Per page selection
    $('#perPageSelect').on('change', function() {
        perPage = $(this).val() === 'all' ? filteredModules.length : parseInt($(this).val());
        currentPage = 1;
        renderTable();
    });

    // Sorting
    $('.sortable').on('click', function() {
        const column = $(this).data('column');
        if (sortColumn === column) {
            sortDirection = sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortColumn = column;
            sortDirection = 'asc';
        }
        
        // Update sort icons
        $('.sortable i').attr('class', 'ti ti-selector');
        $(this).find('i').attr('class', sortDirection === 'asc' ? 'ti ti-sort-ascending' : 'ti ti-sort-descending');
        
        sortModules();
        renderTable();
    });

    function sortModules() {
        filteredModules.sort((a, b) => {
            let aVal = a[sortColumn];
            let bVal = b[sortColumn];
            
            if (sortColumn === 'credits') {
                aVal = parseInt(aVal);
                bVal = parseInt(bVal);
            } else {
                aVal = aVal.toString().toLowerCase();
                bVal = bVal.toString().toLowerCase();
            }
            
            if (aVal < bVal) return sortDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortDirection === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // Render table
    function renderTable() {
        const start = (currentPage - 1) * perPage;
        const end = start + perPage;
        const pageModules = filteredModules.slice(start, end);
        
        $('#module-table-body').empty();
        
        if (pageModules.length === 0) {
            $('#module-table-body').html('<tr><td colspan="7" class="text-center">No modules found.</td></tr>');
            $('#resultsInfo').text('Showing 0 modules');
        } else {
            pageModules.forEach(module => {
                const categoryBadge = module.module_category === 'certificate' ? 
                    '<span class="badge bg-success">Certificate</span>' : 
                    '<span class="badge bg-secondary">Degree/Diploma</span>';
                    
                const badgeClass = module.module_type === 'core' ? 'primary' : 
                                 module.module_type === 'elective' ? 'info' : 'warning';
                const badgeText = module.module_type === 'core' ? 'Core' : 
                                module.module_type === 'elective' ? 'Elective' : 'S/U';
                
                const typeDisplay = module.module_type && module.module_type !== '-' ? 
                    `<span class="badge bg-${badgeClass}">${badgeText}</span>` : 
                    '<span class="text-muted">-</span>';
                
                const creditsDisplay = module.credits && module.credits !== 'N/A' ? module.credits : 'N/A';
                
                const row = `
                    <tr id="module-row-${module.module_id}" data-module-id="${module.module_id}">
                        <td>
                            <input type="checkbox" class="form-check-input module-checkbox" data-module-id="${module.module_id}">
                        </td>
                        <td class="module-name" style="text-transform:none !important;">${module.module_name}</td>
                        <td class="module-code" style="text-transform:none !important;">${module.module_code}</td>
                        <td class="module-category">${categoryBadge}</td>
                        <td class="module-credits">${creditsDisplay}</td>
                        <td class="module-type">${typeDisplay}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary edit-module-btn" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger delete-module-btn" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('#module-table-body').append(row);
            });
            
            const showing = filteredModules.length > perPage ? 
                `Showing ${start + 1}-${Math.min(end, filteredModules.length)} of ${filteredModules.length} modules` :
                `Showing ${filteredModules.length} modules`;
            $('#resultsInfo').text(showing);
        }
        
        renderPagination();
    }

    // Render pagination
    function renderPagination() {
        const totalPages = Math.ceil(filteredModules.length / perPage);
        $('#pagination').empty();
        
        if (totalPages <= 1) return;
        
        // Previous button
        $('#pagination').append(`
            <li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage - 1}">Previous</a>
            </li>
        `);
        
        // Page numbers
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - 2 && i <= currentPage + 2)) {
                $('#pagination').append(`
                    <li class="page-item ${i === currentPage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (i === currentPage - 3 || i === currentPage + 3) {
                $('#pagination').append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }
        
        // Next button
        $('#pagination').append(`
            <li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentPage + 1}">Next</a>
            </li>
        `);
    }

    // Pagination click
    $(document).on('click', '#pagination a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page > 0 && page <= Math.ceil(filteredModules.length / perPage)) {
            currentPage = page;
            renderTable();
            $('.scrollable-table-container').scrollTop(0);
        }
    });

    // Select all checkboxes
    $('#selectAll, #selectAllHeader').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('#selectAll, #selectAllHeader').prop('checked', isChecked);
        $('.module-checkbox').prop('checked', isChecked);
        updateBulkDeleteButton();
    });

    $(document).on('change', '.module-checkbox', function() {
        updateBulkDeleteButton();
        const total = $('.module-checkbox').length;
        const checked = $('.module-checkbox:checked').length;
        $('#selectAll, #selectAllHeader').prop('checked', total === checked);
    });

    function updateBulkDeleteButton() {
        const checkedCount = $('.module-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteBtn').show().text(`Delete Selected (${checkedCount})`);
        } else {
            $('#bulkDeleteBtn').hide();
        }
    }

    // Bulk delete
    $('#bulkDeleteBtn').on('click', function() {
        const selectedIds = [];
        $('.module-checkbox:checked').each(function() {
            selectedIds.push($(this).data('module-id'));
        });
        
        if (selectedIds.length === 0) return;
        
        if (!confirm(`Are you sure you want to delete ${selectedIds.length} module(s)?`)) return;
        
        let completed = 0;
        selectedIds.forEach(id => {
            $.ajax({
                url: '/modules/' + id,
                type: 'DELETE',
                data: { _token: '<?php echo e(csrf_token()); ?>' },
                success: function(response) {
                    completed++;
                                            allModules = allModules.filter(m => m.module_id != id);
                    if (completed === selectedIds.length) {
                        showToast(`Successfully deleted ${selectedIds.length} module(s)`, 'success');
                        applyFilters();
                        updateBulkDeleteButton();
                        $('#selectAll, #selectAllHeader').prop('checked', false);
                    }
                },
                error: function() {
                    showToast('Error deleting some modules', 'danger');
                }
            });
        });
    });

    // Export to CSV
    $('#exportBtn').on('click', function() {
        const csv = [];
        csv.push(['Module Name', 'Module Code', 'Credits', 'Type'].join(','));
        
        filteredModules.forEach(module => {
            csv.push([
                `"${module.module_name}"`,
                `"${module.module_code}"`,
                module.credits,
                `"${module.module_type}"`
            ].join(','));
        });
        
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `modules_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
        showToast('Modules exported successfully', 'success');
    });

    // Handle Edit button click
    $(document).on('click', '.edit-module-btn', function() {
        const row = $(this).closest('tr');
        const moduleId = row.data('module-id');
        const moduleName = row.find('.module-name').text().trim();
        const moduleCode = row.find('.module-code').text().trim();
        const credits = row.find('.module-credits').text().trim();
        const categoryBadge = row.find('.module-category .badge').text().trim();
        const moduleCategory = categoryBadge === 'Certificate' ? 'certificate' : 'degree';
        const moduleTypeBadge = row.find('.module-type .badge').text().trim();
        
        let moduleType = '';
        if (moduleTypeBadge === 'Core') moduleType = 'core';
        else if (moduleTypeBadge === 'Elective') moduleType = 'elective';
        else if (moduleTypeBadge === 'S/U') moduleType = 'special_unit_compulsory';

        if (moduleCategory === 'certificate') {
            // Edit certificate module
            $('#cert_module_id').val(moduleId);
            $('#cert_module_name').val(moduleName);
            $('#cert_module_code').val(moduleCode);
            $('#certModuleSubmitBtn').text('Update Certificate Module');
            $('#certCancelEditBtn').show();
            certEditMode = true;
            editingModuleId = moduleId;
            
            // Switch to certificate tab
            $('#certificate-tab').tab('show');
        } else {
            // Edit degree module
            $('#degree_module_id').val(moduleId);
            $('#degree_module_name').val(moduleName);
            $('#degree_module_code').val(moduleCode);
            $('#degree_credits').val(credits !== 'N/A' ? credits : '');
            $('#degree_module_type').val(moduleType);
            $('#degreeModuleSubmitBtn').text('Update Degree Module');
            $('#degreeCancelEditBtn').show();
            degreeEditMode = true;
            editingModuleId = moduleId;
            
            // Switch to degree tab
            $('#degree-tab').tab('show');
        }
        
        $('html, body').animate({ scrollTop: 0 }, 500);
    });

    // Cancel edit buttons
    $('#degreeCancelEditBtn').on('click', function() {
        resetDegreeForm();
    });
    
    $('#certCancelEditBtn').on('click', function() {
        resetCertForm();
    });

    // Module code pattern validation
    $('#degree_module_code, #cert_module_code').on('input', function() {
        const moduleCode = $(this).val();
        const pattern = /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/;
        const isValid = pattern.test(moduleCode);
        
        $(this).removeClass('is-valid is-invalid');
        
        if (moduleCode.length > 0) {
            if (isValid) {
                $(this).addClass('is-valid');
                $(this).next('.form-text').find('.validation-message').remove();
            } else {
                $(this).addClass('is-invalid');
                if ($(this).next('.form-text').find('.validation-message').length === 0) {
                    $(this).next('.form-text').append('<div class="validation-message text-danger mt-1"><small><i class="ti ti-alert-circle me-1"></i>Invalid format. Use: program_name_specification_unit_code</small></div>');
                }
            }
        }
    });

    // Handle Degree Module form submit
    $('#degreeModuleForm').on('submit', function(e) {
        e.preventDefault();
        
        const moduleCode = $('#degree_module_code').val();
        const pattern = /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/;
        
        if (!pattern.test(moduleCode)) {
            showToast('Module code must follow the pattern: program_name_specification_unit_code', 'danger');
            $('#degree_module_code').addClass('is-invalid');
            return;
        }
        
        const moduleId = $('#degree_module_id').val();
        const formData = $(this).serialize();
        
        if (degreeEditMode && moduleId) {
            $.ajax({
                url: '/modules/' + moduleId,
                type: 'PATCH',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        const module = response.module;
                        const index = allModules.findIndex(m => m.module_id == module.module_id);
                        if (index !== -1) {
                            allModules[index] = {
                                module_id: module.module_id,
                                module_name: module.module_name,
                                module_code: module.module_code,
                                module_category: module.module_category || 'degree',
                                credits: module.credits ? module.credits.toString() : 'N/A',
                                module_type: module.module_type || '-'
                            };
                        }
                        applyFilters();
                        resetDegreeForm();
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    handleError(xhr);
                }
            });
        } else {
            $.ajax({
                url: '<?php echo e(route("module.store")); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        $('#degreeModuleForm')[0].reset();
                        const module = response.module;
                        allModules.push({
                            module_id: module.module_id,
                            module_name: module.module_name,
                            module_code: module.module_code,
                            module_category: module.module_category || 'degree',
                            credits: module.credits ? module.credits.toString() : 'N/A',
                            module_type: module.module_type || '-'
                        });
                        applyFilters();
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    handleError(xhr);
                }
            });
        }
    });

    // Handle Certificate Module form submit
    $('#certificateModuleForm').on('submit', function(e) {
        e.preventDefault();
        
        const moduleCode = $('#cert_module_code').val();
        const pattern = /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/;
        
        if (!pattern.test(moduleCode)) {
            showToast('Module code must follow the pattern: program_name_specification_unit_code', 'danger');
            $('#cert_module_code').addClass('is-invalid');
            return;
        }
        
        const moduleId = $('#cert_module_id').val();
        const formData = $(this).serialize();
        
        if (certEditMode && moduleId) {
            $.ajax({
                url: '/modules/' + moduleId,
                type: 'PATCH',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        const module = response.module;
                        const index = allModules.findIndex(m => m.module_id == module.module_id);
                        if (index !== -1) {
                            allModules[index] = {
                                module_id: module.module_id,
                                module_name: module.module_name,
                                module_code: module.module_code,
                                module_category: module.module_category || 'certificate',
                                credits: 'N/A',
                                module_type: '-'
                            };
                        }
                        applyFilters();
                        resetCertForm();
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    handleError(xhr);
                }
            });
        } else {
            $.ajax({
                url: '<?php echo e(route("module.store")); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        $('#certificateModuleForm')[0].reset();
                        const module = response.module;
                        allModules.push({
                            module_id: module.module_id,
                            module_name: module.module_name,
                            module_code: module.module_code,
                            module_category: module.module_category || 'certificate',
                            credits: 'N/A',
                            module_type: '-'
                        });
                        applyFilters();
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    handleError(xhr);
                }
            });
        }
    });

    // Handle Delete button click
    $(document).on('click', '.delete-module-btn', function() {
        const row = $(this).closest('tr');
        const moduleId = row.data('module-id');
        
        if (!confirm('Are you sure you want to delete this module?')) return;

        $.ajax({
            url: '/modules/' + moduleId,
            type: 'DELETE',
            data: { _token: '<?php echo e(csrf_token()); ?>' },
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');
                    allModules = allModules.filter(m => m.module_id != moduleId);
                    applyFilters();
                } else {
                    showToast(response.message, 'danger');
                }
            },
            error: function(xhr) {
                handleError(xhr);
            }
        });
    });

    function resetDegreeForm() {
        $('#degreeModuleForm')[0].reset();
        $('#degree_module_id').val('');
        $('#degreeModuleSubmitBtn').text('Create Degree Module');
        $('#degreeCancelEditBtn').hide();
        $('#degree_module_code').removeClass('is-valid is-invalid');
        $('.validation-message').remove();
        degreeEditMode = false;
        editingModuleId = null;
    }
    
    function resetCertForm() {
        $('#certificateModuleForm')[0].reset();
        $('#cert_module_id').val('');
        $('#certModuleSubmitBtn').text('Create Certificate Module');
        $('#certCancelEditBtn').hide();
        $('#cert_module_code').removeClass('is-valid is-invalid');
        $('.validation-message').remove();
        certEditMode = false;
        editingModuleId = null;
    }

    function handleError(xhr) {
        let errorMessage = 'An error occurred.';
        if (xhr.responseJSON) {
            errorMessage = xhr.responseJSON.message;
            if (xhr.responseJSON.errors) {
                const errors = Object.values(xhr.responseJSON.errors).flat();
                errorMessage += '<br>' + errors.join('<br>');
            }
        }
        showToast(errorMessage, 'danger');
    }

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
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/courses_&_modules/module_creation.blade.php ENDPATH**/ ?>