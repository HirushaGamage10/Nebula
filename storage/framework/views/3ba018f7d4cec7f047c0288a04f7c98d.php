

<?php $__env->startSection('title', 'Repeat Student Payment Plan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4">Repeat Student Payment Plan</h3>

            <!-- 🔍 Search Student -->
            <form id="searchForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="nic" placeholder="Enter Student NIC">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <div id="paymentSection" style="display:none;">

                <!-- 🗃 Archived Payment Plan -->
                <h5 class="fw-bold text-secondary mb-3">Archived Payment Plan</h5>
                <table class="table table-bordered" id="archivedTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <!-- 💰 Current Active Payment Plan -->
                <h5 class="fw-bold text-success mt-4">Current Payment Plan (Latest Intake)</h5>
                <table class="table table-bordered" id="currentTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Due Date</th>
                            <th>Local Amount (LKR)</th>
                            <th>International Amount (<span id="currencyLabel">Currency</span>)</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>

                <!-- ✏️ Create New Payment Plan -->
                <h5 class="fw-bold text-primary mt-4">Create New Payment Plan</h5>
                <form id="newPlanForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="student_id" name="student_id">
                    <input type="hidden" id="course_id" name="course_id">

                    <table class="table table-bordered" id="newPlanTable">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th>No</th>
                                <th>Due Date</th>
                                <th>Local Amount (LKR)</th>
                                <th>International Amount</th>
                                <th>Currency</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>

                    <button type="button" id="addRow" class="btn btn-secondary">Add Row</button>
                    <button type="submit" class="btn btn-success">Save Plan</button>
                </form>
                <hr>
                <div id="createdPlansSection" class="mt-5">
                    <h5 class="fw-bold text-info">Created Payment Plans</h5>
                    <table class="table table-bordered" id="createdPlansTable">
                        <thead class="bg-info text-white">
                            <tr>
                                <th>#</th>
                                <th>Due Date</th>
                                <th>Local Amount (LKR)</th>
                                <th>International Amount</th>
                                <th>Currency</th>
                                <th>Type</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
let globalCurrency = 'USD'; // default (will update dynamically)

// 🔍 Search Student
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const nic = document.getElementById('nic').value.trim();
    if (!nic) return alert('Please enter NIC');

    const res = await fetch(`/api/repeat-student-by-nic?nic=${nic}`);
    const data = await res.json();
    if (!data.success || !data.student) return alert('Student not found.');

    const studentId = data.student.student_id;
    let courseId = data.holding_history?.length
        ? data.holding_history[0].course_id
        : data.current_registration?.course_id;
    if (!courseId) return alert('No course registration found.');

    document.getElementById('student_id').value = studentId;
    document.getElementById('course_id').value = courseId;

    const planRes = await fetch(`/api/repeat-payment-plan/${studentId}/${courseId}`);
    const planData = await planRes.json();
    console.log('Fetched plan data:', planData);

    const section = document.getElementById('paymentSection');
    section.style.display = planData.success ? 'block' : 'none';

    if (!planData.success) return alert(planData.message || 'Error fetching plan.');

    // --- Current Plan ---
    const current = planData.current_plan;
    const currentTbody = document.querySelector('#currentTable tbody');
    currentTbody.innerHTML = '';

    if (current?.installments?.length > 0) {
        globalCurrency = current.plan?.currency ?? current.installments[0]?.currency ?? 'USD';
        document.getElementById('currencyLabel').innerText = globalCurrency;

        current.installments.forEach((inst, i) => {
            const localAmt = parseFloat(inst.base_amount ?? inst.amount ?? 0);
            const intlAmt = parseFloat(inst.international_amount ?? 0);
            const localCur = 'LKR';
            const intlCur = inst.currency ?? globalCurrency;

            currentTbody.innerHTML += `
                <tr>
                    <td>${i + 1}</td>
                    <td>${inst.due_date?.split('T')[0] ?? '-'}</td>
                    <td>${localAmt.toLocaleString()} ${localCur}</td>
                    <td>${intlAmt.toLocaleString()} ${intlCur}</td>
                    <td><span class="badge bg-success">active</span></td>
                </tr>`;
        });
    } else {
        currentTbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted">No current plan found.</td></tr>`;
    }

    // --- Create New Plan (prefill) ---
    const newBody = document.querySelector('#newPlanTable tbody');
    newBody.innerHTML = '';
    if (current?.installments?.length > 0) {
        current.installments.forEach((inst, i) => {
            newBody.innerHTML += buildPlanRow(
                i,
                inst.due_date,
                inst.base_amount ?? inst.amount ?? 0,
                inst.international_amount ?? 0,
                'LKR',
                inst.currency ?? globalCurrency
            );
        });
    } else {
        newBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No editable plan found.</td></tr>`;
    }
});


// 🧩 Build dynamic table row
function buildPlanRow(i, due = '', local = 0, intl = 0, localCur = 'LKR', intlCur = globalCurrency) {
    return `
        <tr>
            <td>${i + 1}</td>
            <td>
                <input type="date" class="form-control"
                       name="installments[${i}][due_date]"
                       value="${due?.split('T')[0] ?? ''}" required>
            </td>
            <td>
                <input type="number" step="0.01" class="form-control local"
                       name="installments[${i}][local_amount]"
                       value="${local}" placeholder="Local Fee">
            </td>
            <td>
                <input type="number" step="0.01" class="form-control intl"
                       name="installments[${i}][international_amount]"
                       value="${intl}" placeholder="Intl Fee">
            </td>
            <td>
                <input type="text" class="form-control currency-display"
                       value="${local > 0 ? 'LKR' : (intl > 0 ? intlCur : '')}" readonly>
                <input type="hidden" name="installments[${i}][currency]"
                       class="currency-hidden"
                       value="${intl > 0 ? intlCur : 'LKR'}">
            </td>
            <td>
                <button type="button" class="btn btn-danger btn-sm removeRow">Remove</button>
            </td>
        </tr>`;
}


// 🔁 Update logic for amount fields (LKR / Intl)
document.addEventListener('input', e => {
    if (e.target.classList.contains('local') || e.target.classList.contains('intl')) {
        const row = e.target.closest('tr');
        const localInput = row.querySelector('.local');
        const intlInput = row.querySelector('.intl');
        const currencyDisplay = row.querySelector('.currency-display');
        const currencyHidden = row.querySelector('.currency-hidden');

        if (e.target.classList.contains('local') && e.target.value !== '') {
            intlInput.value = '';
            intlInput.disabled = true;
            currencyDisplay.value = 'LKR';
            currencyHidden.value = 'LKR';
        } else if (e.target.classList.contains('intl') && e.target.value !== '') {
            localInput.value = '';
            localInput.disabled = true;
            currencyDisplay.value = globalCurrency;
            currencyHidden.value = globalCurrency;
        } else if (e.target.value === '') {
            localInput.disabled = false;
            intlInput.disabled = false;
            currencyDisplay.value = '';
            currencyHidden.value = '';
        }
    }
});


// ✅ Add new row (always starts from 1, removes placeholder if any)
document.getElementById('addRow').addEventListener('click', () => {
    const tbody = document.querySelector('#newPlanTable tbody');

    // 🔹 Remove "No editable plan found" placeholder row if exists
    const placeholder = tbody.querySelector('.text-muted');
    if (placeholder) tbody.innerHTML = '';

    // 🔹 Calculate proper new index
    const i = tbody.rows.length; // starts from 0 when first row
    tbody.insertAdjacentHTML('beforeend', buildPlanRow(i));

    // 🔹 Re-index all rows to ensure correct numbering
    reindexRows();
});

// ✅ Remove row + reindex
document.addEventListener('click', e => {
    if (e.target.classList.contains('removeRow')) {
        e.target.closest('tr').remove();
        reindexRows();
    }
});

// 🔢 Helper: Reindex all rows
function reindexRows() {
    const rows = document.querySelectorAll('#newPlanTable tbody tr');
    rows.forEach((row, index) => {
        row.querySelector('td:first-child').textContent = index + 1;

        // Update input name indexes to stay consistent
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
        });
    });
}


// ⚙️ Auto-update currency logic (visual only)
document.addEventListener('input', e => {
    if (e.target.classList.contains('local') || e.target.classList.contains('intl')) {
        const row = e.target.closest('tr');
        const localVal = parseFloat(row.querySelector('.local').value || 0);
        const intlVal = parseFloat(row.querySelector('.intl').value || 0);
        const localCur = row.querySelector('.local_currency');
        const intlCur = row.querySelector('.intl_currency');

        if (localCur && intlCur) {
            localCur.value = localVal > 0 ? 'LKR' : '';
            intlCur.value = intlVal > 0 ? globalCurrency : '';
            if (localVal > 0 && intlVal > 0)
                intlCur.value = `LKR + ${globalCurrency}`;
        }
    }
});


// 💾 Save new plan
document.getElementById('newPlanForm').addEventListener('submit', async function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const res = await fetch('/repeat-student-payment/save', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    });
    const data = await res.json();

    if (data.success) {
        alert(data.message);
        const studentId = document.getElementById('student_id').value;
        const courseId = document.getElementById('course_id').value;
        loadCreatedPlans(studentId, courseId);
    } else {
        alert(data.message || 'Error occurred while saving.');
    }
});


// 📦 Load created plans
async function loadCreatedPlans(studentId, courseId) {
    try {
        const res = await fetch(`/api/repeat-created-plans/${studentId}/${courseId}`);
        const data = await res.json();
        const tbody = document.querySelector('#createdPlansTable tbody');
        tbody.innerHTML = '';

        if (data.success && data.installments.length > 0) {
            data.installments.forEach((inst, i) => {
                tbody.innerHTML += `
                    <tr>
                        <td>${i + 1}</td>
                        <td>${inst.due_date?.split('T')[0] ?? '-'}</td>
                        <td>${parseFloat(inst.base_amount ?? inst.amount ?? 0).toLocaleString()}</td>
                        <td>${parseFloat(inst.international_amount ?? 0).toLocaleString()}</td>
                        <td>${inst.international_currency ?? 'LKR'}</td>
                        <td>${inst.installment_type ?? '-'}</td>
                        <td><span class="badge bg-${inst.status === 'pending' ? 'warning' : 'success'}">${inst.status}</span></td>
                    </tr>`;
            });
        } else {
            tbody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No created plans found.</td></tr>`;
        }

        document.getElementById('createdPlansSection').scrollIntoView({ behavior: 'smooth' });
    } catch (err) {
        console.error('Error loading created plans:', err);
        alert('Failed to load created plans.');
    }
}
</script>



<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/repeat_students/payment_plan.blade.php ENDPATH**/ ?>