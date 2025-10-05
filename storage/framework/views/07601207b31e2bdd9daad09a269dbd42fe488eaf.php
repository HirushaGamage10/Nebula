

<?php $__env->startSection('title', 'Repeat Student Payment Plan'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4">Repeat Student Payment Plan</h3>

            <!-- Search Student -->
            <form id="searchForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="nic" placeholder="Enter Student NIC">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <div id="paymentSection" style="display:none;">
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
                                <th>Amount</th>
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
document.getElementById('searchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const nic = document.getElementById('nic').value.trim();
    if(!nic) return alert('Please enter NIC');

    const res = await fetch(`/api/repeat-student-by-nic?nic=${nic}`);
    const data = await res.json();

    if(!data.success || !data.student){
        alert('Student not found.');
        return;
    }

    const studentId = data.student.student_id;
    let courseId = null;

if (data.holding_history && data.holding_history.length > 0) {
    courseId = data.holding_history[0].course_id;
} else if (data.current_registration && data.current_registration.course_id) {
    // fallback to latest re-registered intake
    courseId = data.current_registration.course_id;
}

if (!courseId) {
    alert('No course registration found.');
    return;
}


    document.getElementById('student_id').value = studentId;
    document.getElementById('course_id').value = courseId;

    const planRes = await fetch(`/api/repeat-payment-plan/${studentId}/${courseId}`);
    const planData = await planRes.json();
    console.log('Archived plan data:', planData);

    const section = document.getElementById('paymentSection');
    const tbody = document.querySelector('#archivedTable tbody');
    tbody.innerHTML = '';

    if(planData.success){
        section.style.display = 'block';
        planData.installments.forEach((inst, i) => {
            tbody.innerHTML += `
                <tr>
                    <td>${i+1}</td>
                    <td>${inst.due_date?.split('T')[0]}</td>
                    <td>${parseFloat(inst.amount).toLocaleString()}</td>
                    <td><span class="badge bg-secondary">${inst.status}</span></td>
                </tr>`;
        });

        const newBody = document.querySelector('#newPlanTable tbody');
        newBody.innerHTML = '';
        planData.installments.forEach((inst, i) => {
            newBody.innerHTML += `
                <tr>
                    <td>${i+1}</td>
                    <td><input type="date" class="form-control" name="installments[${i}][due_date]" value="${inst.due_date?.split('T')[0]}" required></td>
                    <td><input type="number" step="0.01" class="form-control" name="installments[${i}][amount]" value="${inst.amount}" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm removeRow">Remove</button></td>
                </tr>`;
        });
    } else {
        alert('No archived plan found.');
    }
});

document.addEventListener('click', e => {
    if(e.target.classList.contains('removeRow')){
        e.target.closest('tr').remove();
    }
    if(e.target.id === 'addRow'){
        const tbody = document.querySelector('#newPlanTable tbody');
        const i = tbody.rows.length;
        tbody.insertAdjacentHTML('beforeend', `
            <tr>
                <td>${i+1}</td>
                <td><input type="date" class="form-control" name="installments[${i}][due_date]" required></td>
                <td><input type="number" step="0.01" class="form-control" name="installments[${i}][amount]" required></td>
                <td><button type="button" class="btn btn-danger btn-sm removeRow">Remove</button></td>
            </tr>`);
    }
});

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