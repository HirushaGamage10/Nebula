

<?php $__env->startSection('title', 'Miscellaneous Payments'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">
            <h3 class="text-primary mb-4">Miscellaneous Payment Entry</h3>

            <!-- Search Student -->
            <form id="searchForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="student_id" placeholder="Enter NIC or Student ID">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <!-- Payment Form -->
            <div id="paymentSection" style="display:none;">
                <form id="miscForm">
                    <?php echo csrf_field(); ?>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Payment Category</label>
                            <select name="misc_category" id="misc_category" class="form-select" required>
                                <option value="">Select Category</option>
                                <option>Library Fine</option>
                                <option>Certificate Reprint</option>
                                <option>ID Card Replacement</option>
                                <option>Event / Exam Fee</option>
                                <option>Hostel Fee</option>
                                <option value="other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6" id="otherFieldContainer" style="display:none;">
                            <label class="form-label">Specify Other Category</label>
                            <input type="text" id="otherField" class="form-control" placeholder="Enter custom category">
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Amount (LKR)</label>
                            <input type="number" step="0.01" name="amount" class="form-control" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Payment Method</label>
                            <select name="payment_method" class="form-select" required>
                                <option value="">Select Method</option>
                                <option value="cash">Cash</option>
                                <option value="bank_transfer">Bank Transfer</option>
                                <option value="cheque">Cheque</option>
                                <option value="card">Card</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Transaction ID</label>
                            <input type="text" name="transaction_id" class="form-control" placeholder="If applicable">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks</label>
                        <textarea name="remarks" class="form-control" rows="2"></textarea>
                    </div>

                    <button type="submit" class="btn btn-success">Save Payment</button>
                </form>

                <hr>
                <h5 class="text-info mt-4">Recent Miscellaneous Payments</h5>
                <table class="table table-bordered align-middle" id="paymentTable">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Category</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 🔹 View Details Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title" id="paymentModalLabel">Payment Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <ul class="list-group" id="paymentDetailList"></ul>
      </div>
      <div class="modal-footer">
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

<script>
// ========================= SEARCH STUDENT =========================
document.getElementById('searchForm').addEventListener('submit', async e => {
    e.preventDefault();
    const studentId = document.getElementById('student_id').value.trim();
    if (!studentId) return alert('Enter NIC or Student ID');

    const res = await fetch(`/misc-payment/fetch/${studentId}`);
    const data = await res.json();

    if (data.success) {
        document.getElementById('paymentSection').style.display = 'block';
        renderTable(data.payments);
    } else alert(data.message || 'No records found');
});

// ========================= CATEGORY LOGIC =========================
const categorySelect = document.getElementById('misc_category');
const otherFieldContainer = document.getElementById('otherFieldContainer');
const otherField = document.getElementById('otherField');

categorySelect.addEventListener('change', () => {
    if (categorySelect.value === 'other') {
        otherFieldContainer.style.display = 'block';
        otherField.required = true;
    } else {
        otherFieldContainer.style.display = 'none';
        otherField.required = false;
        otherField.value = '';
    }
});

otherField.addEventListener('input', () => {
    let val = otherField.value;
    if (val.length > 0) {
        otherField.value = val.charAt(0).toUpperCase() + val.slice(1);
    }
});

// ========================= SAVE PAYMENT =========================
document.getElementById('miscForm').addEventListener('submit', async e => {
    e.preventDefault();
    const formData = new FormData(e.target);
    formData.append('student_id', document.getElementById('student_id').value);

    // ✅ If "Other" selected, use the custom field
    if (formData.get('misc_category') === 'other') {
        formData.set('misc_category', otherField.value);
    }

    const res = await fetch('<?php echo e(route('misc.payment.store')); ?>', {
        method: 'POST',
        body: formData,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
    });
    const data = await res.json();

    if (data.success) {
        alert(data.message);
        const reload = await fetch(`/misc-payment/fetch/${formData.get('student_id')}`);
        const newData = await reload.json();
        renderTable(newData.payments);
        e.target.reset();
        otherFieldContainer.style.display = 'none';
    } else {
        console.error(data);
        alert(data.message || 'Error saving payment');
    }
});

// ========================= RENDER TABLE =========================
function renderTable(payments) {
    const tbody = document.querySelector('#paymentTable tbody');
    tbody.innerHTML = '';
    if (!payments.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No payments found.</td></tr>';
        return;
    }

    payments.forEach((p, i) => {
        tbody.innerHTML += `
            <tr>
                <td>${i+1}</td>
                <td>${p.misc_category || '-'}</td>
                <td>${parseFloat(p.amount).toLocaleString('en-LK', { style: 'currency', currency: 'LKR' })}</td>
                <td>${p.payment_method}</td>
                <td>${new Date(p.created_at).toLocaleDateString()}</td>
                <td><button class="btn btn-sm btn-outline-primary viewBtn" data-id="${p.id}">View</button></td>
            </tr>`;
    });

    // Attach view button click events
    document.querySelectorAll('.viewBtn').forEach(btn => {
        btn.addEventListener('click', async () => {
            const id = btn.dataset.id;
            const payment = payments.find(x => x.id == id);
            showPaymentModal(payment);
        });
    });
}

// ========================= MODAL DETAILS =========================
function showPaymentModal(p) {
    const list = document.getElementById('paymentDetailList');
    list.innerHTML = `
        <li class="list-group-item"><strong>Category:</strong> ${p.misc_category}</li>
        <li class="list-group-item"><strong>Amount:</strong> LKR ${parseFloat(p.amount).toLocaleString()}</li>
        <li class="list-group-item"><strong>Method:</strong> ${p.payment_method}</li>
        <li class="list-group-item"><strong>Date:</strong> ${new Date(p.created_at).toLocaleString()}</li>
        <li class="list-group-item"><strong>Transaction ID:</strong> ${p.transaction_id ?? '-'}</li>
        <li class="list-group-item"><strong>Remarks:</strong> ${p.description ?? '-'}</li>
    `;
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    modal.show();
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/finance/misc_payment.blade.php ENDPATH**/ ?>