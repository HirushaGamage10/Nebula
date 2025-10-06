

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
    if(!nic) return alert('Please enter NIC');

    const res = await fetch(`/api/repeat-student-by-nic?nic=${nic}`);
    const data = await res.json();
    if(!data.success || !data.student) return alert('Student not found.');

    const studentId = data.student.student_id;
    let courseId = data.holding_history?.length ? data.holding_history[0].course_id : data.current_registration?.course_id;
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


function buildPlanRow(i, due='', local=0, intl=0, localCur='LKR', intlCur=globalCurrency) {
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

// Update the event listener for input fields
document.addEventListener('input', e => {
    if (e.target.classList.contains('local') || e.target.classList.contains('intl')) {
        const row = e.target.closest('tr');
        const localInput = row.querySelector('.local');
        const intlInput = row.querySelector('.intl');
        const currencyDisplay = row.querySelector('.currency-display');
        const currencyHidden = row.querySelector('.currency-hidden');

        // If local amount is being entered
        if (e.target.classList.contains('local') && e.target.value !== '') {
            intlInput.value = ''; // Clear international amount
            intlInput.disabled = true; // Disable international input
            currencyDisplay.value = 'LKR';
            currencyHidden.value = 'LKR';
        }
        // If international amount is being entered
        else if (e.target.classList.contains('intl') && e.target.value !== '') {
            localInput.value = ''; // Clear local amount
            localInput.disabled = true; // Disable local input
            currencyDisplay.value = globalCurrency;
            currencyHidden.value = globalCurrency;
        }
        // If field is cleared
        else if (e.target.value === '') {
            localInput.disabled = false;
            intlInput.disabled = false;
            currencyDisplay.value = '';
            currencyHidden.value = '';
        }
    }
});

// Add this function to reset fields when adding new row
document.getElementById('addRow').addEventListener('click', () => {
    const tbody = document.querySelector('#newPlanTable tbody');
    const i = tbody.rows.length;
    tbody.insertAdjacentHTML('beforeend', buildPlanRow(i));
});



// ➕ Add / ➖ Remove rows
document.addEventListener('click', e => {
    if(e.target.classList.contains('removeRow')) e.target.closest('tr').remove();
    if(e.target.id === 'addRow'){
        const tbody = document.querySelector('#newPlanTable tbody');
        const i = tbody.rows.length;
        tbody.insertAdjacentHTML('beforeend', buildPlanRow(i));
    }
});

// ⚙️ Auto-update currency logic
document.addEventListener('input', e => {
    if (e.target.classList.contains('local') || e.target.classList.contains('intl')) {
        const row = e.target.closest('tr');
        const localVal = parseFloat(row.querySelector('.local').value || 0);
        const intlVal = parseFloat(row.querySelector('.intl').value || 0);
        const localCur = row.querySelector('.local_currency');
        const intlCur = row.querySelector('.intl_currency');

        localCur.value = localVal > 0 ? 'LKR' : '';
        intlCur.value = intlVal > 0 ? globalCurrency : '';
        if (localVal > 0 && intlVal > 0)
            intlCur.value = `LKR + ${globalCurrency}`;
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
    alert(data.message);
});
</script>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/repeat_students/payment_plan.blade.php ENDPATH**/ ?>