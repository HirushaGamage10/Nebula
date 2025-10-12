

<?php $__env->startSection('title', 'NEBULA | Payment Plans'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <?php if(session('success')): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo e(session('success')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo e(session('error')); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card mb-3 shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Payment Plans</h2>
                <div class="btn-group">
                    <button type="button" class="btn btn-success btn-sm" onclick="exportToCSV()">
                        <i class="bi bi-file-earmark-spreadsheet"></i> Export CSV
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="exportToPDF()">
                        <i class="bi bi-file-earmark-pdf"></i> Export PDF
                    </button>
                </div>
            </div>
            
            <form method="GET" action="<?php echo e(route('payment.plan.index')); ?>" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-2">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($loc); ?>" <?php if(request('location')===$loc): echo 'selected'; endif; ?>><?php echo e($loc); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" id="filter-course" class="form-select form-select-sm">
                        <option value="">All</option>
                        <?php $__currentLoopData = $courses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($c->course_id); ?>" <?php if((string)request('course_id')===(string)$c->course_id): echo 'selected'; endif; ?>>
                                <?php echo e($c->course_name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Intake</label>
                    <select name="intake_id" id="filter-intake" class="form-select form-select-sm" <?php if(!request('course_id')): echo 'disabled'; endif; ?>>
                        <option value="">All</option>
                        <?php $__currentLoopData = $intakes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($i->intake_id); ?>" <?php if((string)request('intake_id')===(string)$i->intake_id): echo 'selected'; endif; ?>>
                                <?php echo e($i->batch ?? 'Batch ' . $i->intake_id); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Discount Status</label>
                    <select name="has_discount" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" <?php if(request('has_discount')==='1'): echo 'selected'; endif; ?>>With Discount</option>
                        <option value="0" <?php if(request('has_discount')==='0'): echo 'selected'; endif; ?>>No Discount</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Installments</label>
                    <select name="has_installments" class="form-select form-select-sm">
                        <option value="">All</option>
                        <option value="1" <?php if(request('has_installments')==='1'): echo 'selected'; endif; ?>>With Installments</option>
                        <option value="0" <?php if(request('has_installments')==='0'): echo 'selected'; endif; ?>>No Installments</option>
                    </select>
                </div>
                <div class="col-md-1 d-grid">
                    <button class="btn btn-primary btn-sm">
                        <i class="bi bi-funnel"></i> Filter
                    </button>
                </div>
            </form>

            <!-- Search Box -->
            <div class="row mt-3">
                <div class="col-md-4">
                    <input type="text" id="searchInput" class="form-control form-control-sm" placeholder="🔍 Search in table...">
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle" id="paymentPlansTable">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" data-column="0"># <span class="sort-icon">⇅</span></th>
                            <th class="sortable" data-column="1">Location <span class="sort-icon">⇅</span></th>
                            <th class="sortable" data-column="2">Course <span class="sort-icon">⇅</span></th>
                            <th class="sortable" data-column="3">Intake <span class="sort-icon">⇅</span></th>
                            <th class="text-end sortable" data-column="4">Reg. Fee (LKR) <span class="sort-icon">⇅</span></th>
                            <th class="text-end sortable" data-column="5">Local Fee (LKR) <span class="sort-icon">⇅</span></th>
                            <th class="text-end sortable" data-column="6">Franchise <span class="sort-icon">⇅</span></th>
                            <th class="sortable" data-column="7">Discount <span class="sort-icon">⇅</span></th>
                            <th>Installments</th>
                            <th class="sortable" data-column="9">Created <span class="sort-icon">⇅</span></th>
                            <th style="width: 130px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $plans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $plan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $items = is_array($plan->installments) ? $plan->installments : (json_decode($plan->installments, true) ?? []);
                            $count = count($items);

                            $firstDue = $count ? ($items[0]['due_date'] ?? null) : null;
                            $lastDue  = $count ? ($items[array_key_last($items)]['due_date'] ?? null) : null;

                            $totalLocal = 0; $totalIntl = 0;
                            foreach ($items as $it) {
                                $totalLocal += (float)($it['local_amount'] ?? 0);
                                $totalIntl  += (float)($it['international_amount'] ?? 0);
                            }
                        ?>
                        <tr class="data-row">
                            <td><?php echo e($plan->id); ?></td>
                            <td><?php echo e($plan->location); ?></td>
                            <td><?php echo e(optional($plan->course)->course_name ?? '—'); ?></td>
                            <td><?php echo e(optional($plan->intake)->batch ?? '—'); ?></td>
                            <td class="text-end"><?php echo e(number_format($plan->registration_fee, 2, '.', ',')); ?></td>
                            <td class="text-end"><?php echo e(number_format($plan->local_fee, 2, '.', ',')); ?></td>
                            <td class="text-end">
                                <?php echo e(number_format($plan->international_fee, 2, '.', ',')); ?>

                                <small class="text-muted"><?php echo e($plan->international_currency); ?></small>
                            </td>
                            <td>
                                <?php if($plan->apply_discount): ?>
                                    <span class="badge bg-success"><?php echo e(rtrim(rtrim(number_format($plan->discount ?? 0, 2, '.', ''), '0'), '.')); ?>%</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($plan->installment_plan): ?>
                                    <button class="btn btn-sm btn-outline-info"
                                            type="button"
                                            data-bs-toggle="collapse"
                                            data-bs-target="#inst-<?php echo e($plan->id); ?>">
                                        <i class="bi bi-list-ul"></i> View <?php echo e($count); ?>

                                    </button>
                                    <div class="small text-muted mt-1">
                                        <?php if($count): ?>
                                            <?php echo e($firstDue); ?> → <?php echo e($lastDue); ?>

                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark">—</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="small text-muted"><?php echo e($plan->created_at?->format('Y-m-d H:i')); ?></div>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="<?php echo e(route('payment.plan.edit',$plan->id)); ?>" class="btn btn-sm btn-warning">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php if($plan->installment_plan): ?>
                            <tr class="collapse" id="inst-<?php echo e($plan->id); ?>">
                                <td colspan="11" class="bg-light">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-striped mb-2">
                                            <thead class="table-secondary">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Due Date</th>
                                                    <th class="text-end">Local (LKR)</th>
                                                    <th class="text-end">International (<?php echo e($plan->international_currency); ?>)</th>
                                                    <th>Tax?</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__empty_2 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $it): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_2 = false; ?>
                                                    <tr>
                                                        <td><span class="badge bg-primary"><?php echo e($it['installment_number'] ?? ''); ?></span></td>
                                                        <td><?php echo e($it['due_date'] ?? ''); ?></td>
                                                        <td class="text-end"><?php echo e(number_format((float)($it['local_amount'] ?? 0), 2, '.', ',')); ?></td>
                                                        <td class="text-end"><?php echo e(number_format((float)($it['international_amount'] ?? 0), 2, '.', ',')); ?></td>
                                                        <td>
                                                            <?php if(!empty($it['apply_tax'])): ?>
                                                                <span class="badge bg-success">Yes</span>
                                                            <?php else: ?>
                                                                <span class="badge bg-secondary">No</span>
                                                            <?php endif; ?>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_2): ?>
                                                    <tr><td colspan="5" class="text-center text-muted">No installments found</td></tr>
                                                <?php endif; ?>
                                            </tbody>
                                            <tfoot class="table-info">
                                                <tr class="fw-semibold">
                                                    <td colspan="2" class="text-end">Totals:</td>
                                                    <td class="text-end"><?php echo e(number_format($totalLocal, 2, '.', ',')); ?></td>
                                                    <td class="text-end"><?php echo e(number_format($totalIntl, 2, '.', ',')); ?></td>
                                                    <td></td>
                                                </tr>
                                                <tr class="small text-muted">
                                                    <td colspan="2" class="text-end">Required:</td>
                                                    <td class="text-end"><?php echo e(number_format((float)$plan->local_fee, 2, '.', ',')); ?></td>
                                                    <td class="text-end"><?php echo e(number_format((float)$plan->international_fee, 2, '.', ',')); ?></td>
                                                    <td></td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="11" class="text-center text-muted py-4">No payment plans found.</td></tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="small text-muted">
                    Showing <?php echo e($plans->firstItem() ?? 0); ?>–<?php echo e($plans->lastItem() ?? 0); ?> of <?php echo e($plans->total()); ?>

                </div>
                <?php echo e($plans->links()); ?>

            </div>
        </div>
    </div>
</div>

<!-- Include jsPDF from CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>

<style>
.sortable {
    cursor: pointer;
    user-select: none;
    position: relative;
}
.sortable:hover {
    background-color: rgba(255, 255, 255, 0.1);
}
.sort-icon {
    font-size: 0.8em;
    opacity: 0.5;
    margin-left: 4px;
}
.sortable.asc .sort-icon::before {
    content: '↑';
    opacity: 1;
}
.sortable.desc .sort-icon::before {
    content: '↓';
    opacity: 1;
}
.table-hover tbody tr:hover {
    background-color: rgba(0, 123, 255, 0.05);
}
</style>

<script>
// Table Search Functionality
document.getElementById('searchInput')?.addEventListener('keyup', function() {
    const searchValue = this.value.toLowerCase();
    const rows = document.querySelectorAll('#paymentPlansTable tbody tr.data-row');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchValue) ? '' : 'none';
    });
});

// Table Sorting
let sortDirection = {};
document.querySelectorAll('.sortable').forEach(header => {
    header.addEventListener('click', function() {
        const column = this.getAttribute('data-column');
        const tbody = document.querySelector('#paymentPlansTable tbody');
        const rows = Array.from(tbody.querySelectorAll('tr.data-row'));
        
        // Toggle sort direction
        sortDirection[column] = sortDirection[column] === 'asc' ? 'desc' : 'asc';
        
        // Remove sort classes from all headers
        document.querySelectorAll('.sortable').forEach(h => {
            h.classList.remove('asc', 'desc');
        });
        this.classList.add(sortDirection[column]);
        
        rows.sort((a, b) => {
            const aVal = a.children[column].textContent.trim();
            const bVal = b.children[column].textContent.trim();
            
            // Try to parse as number
            const aNum = parseFloat(aVal.replace(/[^0-9.-]/g, ''));
            const bNum = parseFloat(bVal.replace(/[^0-9.-]/g, ''));
            
            let comparison = 0;
            if (!isNaN(aNum) && !isNaN(bNum)) {
                comparison = aNum - bNum;
            } else {
                comparison = aVal.localeCompare(bVal);
            }
            
            return sortDirection[column] === 'asc' ? comparison : -comparison;
        });
        
        // Re-append sorted rows
        rows.forEach(row => {
            const nextRow = row.nextElementSibling;
            tbody.appendChild(row);
            if (nextRow && nextRow.classList.contains('collapse')) {
                tbody.appendChild(nextRow);
            }
        });
    });
});

// Export to CSV
function exportToCSV() {
    const table = document.getElementById('paymentPlansTable');
    const rows = table.querySelectorAll('tr.data-row');
    let csv = [];
    
    // Headers
    csv.push(['"#"', '"Location"', '"Course"', '"Intake"', '"Reg. Fee"', '"Local Fee"', '"Franchise"', '"Discount"', '"Created"'].join(','));
    
    // Data rows
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            const cols = row.querySelectorAll('td');
            const rowData = [
                `"${cols[0].textContent.trim()}"`,
                `"${cols[1].textContent.trim()}"`,
                `"${cols[2].textContent.trim()}"`,
                `"${cols[3].textContent.trim()}"`,
                `"${cols[4].textContent.trim()}"`,
                `"${cols[5].textContent.trim()}"`,
                `"${cols[6].textContent.trim()}"`,
                `"${cols[7].textContent.trim()}"`,
                `"${cols[9].textContent.trim()}"`
            ];
            csv.push(rowData.join(','));
        }
    });
    
    // Download
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'payment_plans_' + new Date().toISOString().split('T')[0] + '.csv';
    link.click();
}

// Export to PDF
function exportToPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF('l', 'mm', 'a4');
    
    doc.setFontSize(18);
    doc.text('Payment Plans Report', 14, 15);
    doc.setFontSize(10);
    doc.text('Generated: ' + new Date().toLocaleString(), 14, 22);
    
    const table = document.getElementById('paymentPlansTable');
    const rows = Array.from(table.querySelectorAll('tr.data-row')).filter(row => row.style.display !== 'none');
    
    const tableData = rows.map(row => {
        const cols = row.querySelectorAll('td');
        return [
            cols[0].textContent.trim(),
            cols[1].textContent.trim(),
            cols[2].textContent.trim(),
            cols[3].textContent.trim(),
            cols[4].textContent.trim(),
            cols[5].textContent.trim(),
            cols[6].textContent.trim(),
            cols[7].textContent.trim(),
            cols[9].textContent.trim()
        ];
    });
    
    doc.autoTable({
        head: [['#', 'Location', 'Course', 'Intake', 'Reg. Fee', 'Local Fee', 'Franchise', 'Discount', 'Created']],
        body: tableData,
        startY: 28,
        styles: { fontSize: 8 },
        headStyles: { fillColor: [52, 58, 64] }
    });
    
    doc.save('payment_plans_' + new Date().toISOString().split('T')[0] + '.pdf');
}

// Location filter cascade (unchanged)
document.querySelector('select[name="location"]')?.addEventListener('change', function () {
    const location = this.value;
    const courseSelect = document.getElementById('filter-course');
    const intakeSelect = document.getElementById('filter-intake');

    courseSelect.innerHTML = '<option value="">All</option>';
    intakeSelect.innerHTML = '<option value="">All</option>';
    intakeSelect.disabled = true;

    if (!location) return;

    fetch("<?php echo e(route('courses.byLocation')); ?>", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ location })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data.length) {
            data.data.forEach(course => {
                const opt = document.createElement('option');
                opt.value = course.course_id;
                opt.textContent = course.course_name;
                courseSelect.appendChild(opt);
            });
        }
    });
});

// Course filter cascade (unchanged)
document.getElementById('filter-course')?.addEventListener('change', function () {
    const courseId = this.value;
    const location = document.querySelector('select[name="location"]').value;
    const intakeSelect = document.getElementById('filter-intake');

    intakeSelect.disabled = true;
    intakeSelect.innerHTML = '<option value="">Loading...</option>';

    if (!courseId || !location) {
        intakeSelect.innerHTML = '<option value="">All</option>';
        intakeSelect.disabled = false;
        return;
    }

   fetch("<?php echo e(route('intakes.byCourse')); ?>", {
    method: 'POST',
    headers: {
        'Content-Type':'application/json',
        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
    },
    body: JSON.stringify({ course_id: courseId, location })
})
.then(res => res.json())
.then(data => {
    console.log("Intake API response:", data);
    intakeSelect.innerHTML = '<option value="">All</option>';
    if (data.success && data.data.length) {
        data.data.forEach(intake => {
            const opt = document.createElement('option');
            opt.value = intake.intake_id;
            opt.textContent = intake.batch ? intake.batch : `Batch ${intake.intake_id}`;
            intakeSelect.appendChild(opt);
        });
    }
    intakeSelect.disabled = false;
})
.catch(() => {
    intakeSelect.innerHTML = '<option value="">All</option>';
    intakeSelect.disabled = false;
});
});
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\danid\Desktop\Clone Neb\Nebula\resources\views/payment_plan_index.blade.php ENDPATH**/ ?>