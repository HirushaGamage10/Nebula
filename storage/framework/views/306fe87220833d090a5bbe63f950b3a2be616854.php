<?php $__env->startSection('title', 'NEBULA | Special Approval List'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Toast-like messages */
.success-message,.error-message{
  position:fixed;top:20px;right:20px;z-index:9999;color:#fff;padding:15px 20px;border-radius:10px;
  box-shadow:0 4px 15px rgba(0,0,0,.15);font-weight:500;font-size:14px;max-width:400px;
  transform:translateX(100%);transition:transform .3s ease-in-out;border-left:4px solid #fff
}
.success-message{background:linear-gradient(135deg,#28a745,#20c997)}
.error-message{background:linear-gradient(135deg,#dc3545,#e74c3c)}
.success-message.show,.error-message.show{transform:translateX(0)}
.success-message .success-icon,.error-message .error-icon{margin-right:10px;font-size:18px}

/* Tabs */
.nav-tabs .nav-link{border:none;border-bottom:3px solid transparent;color:#6c757d;font-weight:500;padding:12px 20px;transition:.3s}
.nav-tabs .nav-link:hover{border-color:#dee2e6;color:#495057}
.nav-tabs .nav-link.active{border-bottom-color:#0d6efd;color:#0d6efd;background-color:transparent}
.nav-tabs .nav-link i{font-size:1.1rem}
.tab-content{padding-top:20px}

.franchise-payment-table th{background-color:#f8f9fa;border-color:#dee2e6;font-weight:600}
.status-badge{font-size:.75rem;padding:4px 8px}
</style>

<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Special Approval List</h2>
      <hr>

      <!-- Tabs -->
      <ul class="nav nav-tabs" id="specialApprovalTabs" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="student-registration-tab" data-bs-toggle="tab" data-bs-target="#student-registration" type="button" role="tab" aria-controls="student-registration" aria-selected="true">
            <i class="ti ti-user me-2"></i>Student Registration
          </button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="franchise-payment-tab" data-bs-toggle="tab" data-bs-target="#franchise-payment" type="button" role="tab" aria-controls="franchise-payment" aria-selected="false">
            <i class="ti ti-currency-dollar me-2"></i>Franchise Payment Delays
          </button>
        </li>
        <!-- NEW TAB -->
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="semterm-tab" data-bs-toggle="tab" data-bs-target="#semterm" type="button" role="tab" aria-controls="semterm" aria-selected="false">
            <i class="ti ti-rotate-2 me-2"></i>Semester Register Termination
          </button>
        </li>
      </ul>

      <!-- Tab panes -->
      <div class="tab-content" id="specialApprovalTabContent">

        <!-- Student Registration (existing) -->
        <div class="tab-pane fade show active" id="student-registration" role="tabpanel" aria-labelledby="student-registration-tab">
          <div class="mt-4">
            <div class="alert alert-info">
              <i class="ti ti-info-circle me-2"></i>
              <strong>Student Registration Approvals</strong>
              <p class="mb-0 mt-2">Review and approve student registration requests that require special approval.</p>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered">
                <thead class="table-light">
                  <tr>
                    <th>Registration Number</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Document</th>
                    <th>Remarks</th>
                    <th>DGM Comment</th>
                    <th>Approval Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="specialApprovalTableBody"></tbody>
              </table>
            </div>

            <!-- Approval modal trigger replaces inline register -->

          </div>
        </div>


        <!-- Franchise Payment Delays (existing placeholder) -->
        <div class="tab-pane fade" id="franchise-payment" role="tabpanel" aria-labelledby="franchise-payment-tab">
          <div class="mt-4">
            <div class="alert alert-info">
              <i class="ti ti-info-circle me-2"></i>
              <strong>Franchise Payment Delays</strong>
              <p class="mb-0 mt-2">Review and approve franchise payment delay requests that require special approval.</p>
            </div>

            <div class="table-responsive">
              <table class="table table-bordered franchise-payment-table">
                <thead class="table-light">
                  <tr>
                    <th>Franchise Name</th>
                    <th>Student Name</th>
                    <th>Course</th>
                    <th>Due Date</th>
                    <th>Days Delayed</th>
                    <th>Amount Due</th>
                    <th>Reason</th>
                    <th>DGM Comment</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="franchisePaymentTableBody">
                  <tr>
                    <td colspan="10" class="text-center text-muted py-4">
                      <i class="ti ti-inbox" style="font-size:2rem;"></i>
                      <p class="mt-2 mb-0">No franchise payment delay requests found</p>
                      <small class="text-muted">This feature will be implemented in future updates</small>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

          </div>
        </div>

        <!-- NEW: Semester Register Termination -->
        <div class="tab-pane fade" id="semterm" role="tabpanel" aria-labelledby="semterm-tab">
          <div class="mt-4">
            <div class="alert alert-warning">
              <i class="ti ti-alert-triangle me-2"></i>
              <strong>Terminated → Re‑Registration Requests</strong>
              <p class="mb-0 mt-2">Review requests from terminated students who seek re‑registration for a semester.</p>
            </div>

            <!-- Nested tabs for Pending / Rejected within SemTerm -->
            <ul class="nav nav-tabs" id="semtermSubTabs" role="tablist">
              <li class="nav-item" role="presentation">
                <button class="nav-link active" id="semterm-pending-tab" data-bs-toggle="tab" data-bs-target="#semterm-pending" type="button" role="tab" aria-controls="semterm-pending" aria-selected="true">
                  Pending
                </button>
              </li>
              <li class="nav-item" role="presentation">
                <button class="nav-link" id="semterm-rejected-tab" data-bs-toggle="tab" data-bs-target="#semterm-rejected" type="button" role="tab" aria-controls="semterm-rejected" aria-selected="false">
                  Rejected
                </button>
              </li>
            </ul>

            <div class="tab-content pt-3" id="semtermSubTabContent">
              <!-- Pending table -->
              <div class="tab-pane fade show active" id="semterm-pending" role="tabpanel" aria-labelledby="semterm-pending-tab">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th>Semester</th>
                        <th>Current Status</th>
                        <th>Reason</th>
                        <th>Requested At</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody id="semtermTableBody">
                      <tr>
                        <td colspan="9" class="text-center text-muted">Loading…</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Rejected table -->
              <div class="tab-pane fade" id="semterm-rejected" role="tabpanel" aria-labelledby="semterm-rejected-tab">
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead class="table-light">
                      <tr>
                        <th>Student ID</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th>Semester</th>
                        <th>Reason</th>
                        <th>Rejected At</th>
                      </tr>
                    </thead>
                    <tbody id="semtermRejectedTableBody">
                      <tr>
                        <td colspan="7" class="text-center text-muted">Loading…</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>

          </div>
        </div>

      </div>
    </div>
  </div>
</div>

<!-- Reuse: DGM Comment Edit Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="editCommentModalLabel">Edit DGM Comment</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <form id="editCommentForm">
        <input type="hidden" id="editCommentRegistrationId">
        <div class="mb-3">
          <label for="editCommentText" class="form-label">DGM Comment</label>
          <textarea class="form-control" id="editCommentText" rows="4" placeholder="Enter your comment for this special approval request..."></textarea>
          <small class="text-muted">Maximum 1000 characters</small>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-primary" id="saveDgmCommentBtn">Save Comment</button>
    </div>
  </div></div>
</div>

<!-- NEW: Reason viewer -->
<div class="modal fade" id="viewReasonModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Re‑register Reason</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body"><p id="viewReasonText" class="mb-0"></p></div>
    <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button></div>
  </div></div>
</div>

<!-- Approve Modal: Reason + Attachment -->
<div class="modal fade" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="approveForm" class="modal-content" enctype="multipart/form-data">
      <div class="modal-header">
        <h5 class="modal-title" id="approveModalLabel">Special Approval (DGM) Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="approve_registration_id" name="registration_id">
        <div class="mb-3">
          <label class="form-label">Reason</label>
          <textarea id="approve_reason" name="reason" class="form-control" rows="4" placeholder="Reason for approving this registration (optional)"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Attachment (optional)</label>
          <input type="file" id="approve_file" name="attachment" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp">
          <small class="text-muted">Attach supporting document (max 5MB).</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Attach to Request</button>
      </div>
    </form>
  </div>
  </div>

<!-- Reject Modal: Reason required -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-labelledby="rejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="rejectForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="rejectModalLabel">Reject Registration</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="reject_registration_id" name="registration_id">
        <div class="mb-3">
          <label class="form-label">Reason <span class="text-danger">*</span></label>
          <textarea id="reject_reason" name="reason" class="form-control" rows="4" placeholder="Why is this registration being rejected?" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Reject</button>
      </div>
    </form>
  </div>
  </div>

<!-- SemTerm Reject Modal: capture reason/comment -->
<div class="modal fade" id="semtermRejectModal" tabindex="-1" aria-labelledby="semtermRejectModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="semtermRejectForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="semtermRejectModalLabel">Reject Re‑Registration Request</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="semterm_reject_student_id">
        <input type="hidden" id="semterm_reject_intake_id">
        <input type="hidden" id="semterm_reject_semester_id">
        <div class="mb-3">
          <label class="form-label">Reason <span class="text-danger">*</span></label>
          <textarea id="semterm_reject_reason" class="form-control" rows="4" placeholder="Why is this re‑registration being rejected?" required></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-danger">Reject</button>
      </div>
    </form>
  </div>
  </div>

<?php $__env->startPush('scripts'); ?>
<script>
// mini toast helpers
function showSuccessMessage(msg){const m=document.createElement('div');m.className='success-message';m.innerHTML=`<i class="ti ti-check-circle success-icon"></i>${msg}`;document.body.appendChild(m);setTimeout(()=>m.classList.add('show'),100);setTimeout(()=>{m.classList.remove('show');setTimeout(()=>m.remove(),300)},4000)}
function showErrorMessage(msg){const m=document.createElement('div');m.className='error-message';m.innerHTML=`<i class="ti ti-alert-circle error-icon"></i>${msg}`;document.body.appendChild(m);setTimeout(()=>m.classList.add('show'),100);setTimeout(()=>{m.classList.remove('show');setTimeout(()=>m.remove(),300)},5000)}

document.addEventListener('DOMContentLoaded', function() {
  const tableBody = document.getElementById('specialApprovalTableBody');
  const franchiseTableBody = document.getElementById('franchisePaymentTableBody');
  const semtermTableBody = document.getElementById('semtermTableBody');

  // Approve modal refs
  const approveModalEl = document.getElementById('approveModal');
  const approveModal = approveModalEl ? new bootstrap.Modal(approveModalEl) : null;
  const approveForm = document.getElementById('approveForm');
  const approveRegIdEl = document.getElementById('approve_registration_id');
  const approveReasonEl = document.getElementById('approve_reason');
  const approveFileEl = document.getElementById('approve_file');

  // Reject modal refs
  const rejectModalEl = document.getElementById('rejectModal');
  const rejectModal = rejectModalEl ? new bootstrap.Modal(rejectModalEl) : null;
  const rejectForm = document.getElementById('rejectForm');
  const rejectRegIdEl = document.getElementById('reject_registration_id');
  const rejectReasonEl = document.getElementById('reject_reason');

  // tab switch
  document.querySelectorAll('[data-bs-toggle="tab"]').forEach(tab=>{
    tab.addEventListener('shown.bs.tab', (ev)=>{
      const t = ev.target.getAttribute('data-bs-target');
      if(t==='#student-registration') loadStudentRegistrationData();
      if(t==='#franchise-payment')   loadFranchisePaymentData();
      if(t==='#semterm')             loadSemTermRequests();
    });
  });

  // initial
  loadStudentRegistrationData();

  // ===== Student registration (existing) =====
  function loadStudentRegistrationData(){
    fetch('/get-special-approval-list')
      .then(r=>r.json())
      .then(data=>{
        if(data.success && data.students){ renderSpecialApprovalTable(data.students); }
        else tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No students found.</td></tr>';
      })
      .catch(()=> tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Error loading data.</td></tr>');
  }

  function renderSpecialApprovalTable(students){
    tableBody.innerHTML = '';
    students.forEach(st=>{
      const nic = st.nic && st.nic!=='N/A' ? st.nic : '';
      const docHtml = st.document_path
        ? `<a href="/special-approval-document/${st.document_path.split('/').pop()}" target="_blank" class="btn btn-outline-primary btn-sm"><i class="ti ti-download"></i> View Document</a>`
        : '<span class="text-muted">No document</span>';
      const remarks = st.remarks || 'No remarks';
      const dgm     = st.dgm_comment || 'No DGM comment';
      const row = `
        <tr>
          <td>${st.registration_number||''}</td>
          <td>${st.name||''}</td>
          <td>${st.course_name||''}</td>
          <td>${docHtml}</td>
          <td title="${remarks}">${remarks.length>50?remarks.substring(0,50)+'…':remarks}</td>
          <td title="${dgm}">
            <div class="d-flex align-items-center">
              <span class="me-2">${dgm.length>50?dgm.substring(0,50)+'…':dgm}</span>
              <button class="btn btn-sm btn-outline-primary edit-comment-btn" data-registration-id="${st.registration_id}" data-current-comment="${dgm}">
                <i class="ti ti-edit"></i>
              </button>
            </div>
          </td>
          <td>${st.approval_status==='Approved by manager'?'<span class="badge bg-success status-badge">Approved</span>':(st.approval_status==='Rejected'?'<span class="badge bg-danger status-badge">Rejected</span>':'<span class="badge bg-warning status-badge">Pending</span>')}</td>
          <td>${st.approval_status==='Approved by manager'?'<span class="badge bg-success status-badge">Approved</span>':
            `<div class="d-flex gap-2">
                <button class="btn btn-success btn-sm approve-btn"
                   data-student-id="${st.student_id}"
                   data-student-nic="${nic}"
                   data-student-name="${st.name||''}"
                   data-course-id="${st.course_id||''}"
                   data-registration-number="${st.registration_number||''}"
                   data-intake="${st.intake||''}"
                   data-registration-id="${st.registration_id}">
                   Register
                </button>
                <button class="btn btn-outline-danger btn-sm reject-btn"
                   data-student-id="${st.student_id}"
                   data-registration-id="${st.registration_id}">
                   Reject
                </button>
             </div>`}
          </td>
        </tr>`;
      tableBody.insertAdjacentHTML('beforeend', row);
    });
  }

  // click handlers in student table
  tableBody.addEventListener('click', function(e){
    if(e.target.classList.contains('approve-btn')){
      // open modal and set registration id
      const rid = e.target.getAttribute('data-registration-id');
      if (approveRegIdEl && approveModal) {
        approveRegIdEl.value = rid;
        approveReasonEl.value = '';
        if (approveFileEl) approveFileEl.value = '';
        approveModal.show();
      }
    }

    const editBtn = e.target.closest('.edit-comment-btn');
    if(editBtn){
      document.getElementById('editCommentRegistrationId').value = editBtn.getAttribute('data-registration-id');
      document.getElementById('editCommentText').value = (editBtn.getAttribute('data-current-comment')||'').replace(/^No DGM comment$/,'');
      new bootstrap.Modal(document.getElementById('editCommentModal')).show();
    }

    if(e.target.closest('.reject-btn')) {
        const registrationId = e.target.closest('.reject-btn').getAttribute('data-registration-id');
        if (rejectRegIdEl && rejectModal) {
          rejectRegIdEl.value = registrationId;
          rejectReasonEl.value = '';
          rejectModal.show();
        }
    }
  });

  // Approve modal submit
  if (approveForm) {
    approveForm.addEventListener('submit', function(e){
      e.preventDefault();
      const fd = new FormData(approveForm);
      const btn = approveForm.querySelector('button[type="submit"]');
      const original = btn.textContent;
      btn.disabled = true; btn.textContent = 'Saving…';
      fetch('<?php echo e(route('special.approval.approve')); ?>', {
        method: 'POST',
        body: fd,
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' }
      })
      .then(r=>r.json())
      .then(d=>{
        if (d.success) {
          showSuccessMessage(d.message || 'Approved successfully');
          if (approveModal) approveModal.hide();
          loadStudentRegistrationData();
        } else {
          showErrorMessage(d.message || 'Failed to approve');
        }
      })
      .catch(()=> showErrorMessage('Failed to approve'))
      .finally(()=> { btn.disabled=false; btn.textContent = original; });
    });
  }

  // Reject modal submit
  if (rejectForm) {
    rejectForm.addEventListener('submit', function(e){
      e.preventDefault();
      const payload = {
        registration_id: rejectRegIdEl.value,
        reason: rejectReasonEl.value.trim()
      };
      if (!payload.reason) { showErrorMessage('Please enter a reason.'); return; }
      const btn = rejectForm.querySelector('button[type="submit"]');
      const original = btn.textContent;
      btn.disabled = true; btn.textContent = 'Rejecting…';
      fetch('<?php echo e(route('special.approval.reject')); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
        body: JSON.stringify(payload)
      })
      .then(r=>r.json())
      .then(d=>{
        if (d.success) {
          showSuccessMessage(d.message || 'Rejected');
          if (rejectModal) rejectModal.hide();
          loadStudentRegistrationData();
        } else {
          showErrorMessage(d.message || 'Failed to reject');
        }
      })
      .catch(()=> showErrorMessage('Failed to reject'))
      .finally(()=> { btn.disabled=false; btn.textContent = original; });
    });
  }

  function loadRejectedRegistrations(){
    if (!rejectedTableBody) return;
    rejectedTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">Loading…</td></tr>';
    fetch('<?php echo e(route('special.approval.rejected')); ?>')
      .then(r=>r.json())
      .then(d=>{
        if (!d.success || !d.students || d.students.length === 0) {
          rejectedTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-muted">No rejected registrations.</td></tr>';
          return;
        }
        rejectedTableBody.innerHTML = '';
        d.students.forEach(st => {
          const tr = `
            <tr>
              <td>${st.registration_number || ''}</td>
              <td>${st.name || ''}</td>
              <td>${st.course_name || ''}</td>
              <td>${st.intake || ''}</td>
              <td title="${st.reason || ''}">${(st.reason||'').length>80?(st.reason||'').substring(0,80)+'…':(st.reason||'')}</td>
              <td>${st.rejected_at || ''}</td>
            </tr>`;
          rejectedTableBody.insertAdjacentHTML('beforeend', tr);
        });
      })
      .catch(()=>{
        rejectedTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading rejected list.</td></tr>';
      });
  }

  document.getElementById('saveDgmCommentBtn').addEventListener('click', function(){
    const rid = document.getElementById('editCommentRegistrationId').value;
    const txt = document.getElementById('editCommentText').value;
    if(!rid){ showErrorMessage('Registration ID is required.'); return; }
    const btn=this; btn.disabled=true; btn.innerHTML='<span class="spinner-border spinner-border-sm"></span> Saving...';
    fetch('/update-dgm-comment',{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},body:JSON.stringify({registration_id:rid,dgm_comment:txt})})
      .then(r=>r.json()).then(d=>{
        if(d.success){ bootstrap.Modal.getInstance(document.getElementById('editCommentModal')).hide(); location.reload(); }
        else showErrorMessage(d.message||'Failed to update comment.');
      }).catch(()=>showErrorMessage('An error occurred while updating the comment.'))
      .finally(()=>{btn.disabled=false;btn.innerHTML='Save Comment';});
  });

  // ===== Franchise stub =====
  function loadFranchisePaymentData(){
    franchiseTableBody.innerHTML = `
      <tr>
        <td colspan="10" class="text-center text-muted py-4">
          <i class="ti ti-inbox" style="font-size: 2rem;"></i>
          <p class="mt-2 mb-0">No franchise payment delay requests found</p>
          <small class="text-muted">This feature will be implemented in future updates</small>
        </td>
      </tr>`;
  }

  // ===== NEW: Semester termination → re-register =====
  function loadSemTermRequests(){
    semtermTableBody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">Loading…</td></tr>`;
    fetch('/semester-registration/terminated-requests?status=pending')
      .then(r=>r.json())
      .then(d=>{
        if(!d.success || !d.requests || !d.requests.length){
          semtermTableBody.innerHTML = `<tr><td colspan="9" class="text-center text-muted">No requests found.</td></tr>`;
          return;
        }
        renderSemTermTable(d.requests);
      })
      .catch(()=>{
        semtermTableBody.innerHTML = `<tr><td colspan="9" class="text-center text-danger">Error loading requests.</td></tr>`;
      });
  }

  function renderSemTermTable(rows){
    semtermTableBody.innerHTML='';
    rows.forEach(r=>{
      const tr = `
        <tr data-request-id="${r.id}" data-student-id="${r.student_id}" data-intake-id="${r.intake_id}" data-semester-id="${r.semester_id}">
          <td>${r.student_id}</td>
          <td>${r.student_name||''}</td>
          <td>${r.course_name||''}</td>
          <td>${r.intake||''}</td>
          <td>${r.semester_name||''}</td>
          <td><span class="badge ${r.current_status==='terminated'?'bg-danger':'bg-secondary'}">${r.current_status}</span></td>
          <td>
            <button type="button" class="btn btn-outline-info btn-sm view-reason-btn" data-reason="${(r.reason||'').replace(/"/g,'&quot;')}">
              View
            </button>
          </td>
          <td>${r.requested_at||''}</td>
          <td class="d-flex gap-2">
            <button class="btn btn-success btn-sm sem-approve-btn" data-id="${r.id}">Approve</button>
            <button class="btn btn-outline-danger btn-sm sem-reject-btn" data-id="${r.id}">Reject</button>
          </td>
        </tr>`;
      semtermTableBody.insertAdjacentHTML('beforeend', tr);
    });
  }

  // actions in semterm table
  semtermTableBody.addEventListener('click', function(e){
    const viewBtn = e.target.closest('.view-reason-btn');
    if(viewBtn){
      document.getElementById('viewReasonText').textContent = viewBtn.getAttribute('data-reason') || '—';
      new bootstrap.Modal(document.getElementById('viewReasonModal')).show();
      return;
    }

    const approve = e.target.closest('.sem-approve-btn');
    const reject  = e.target.closest('.sem-reject-btn');
    if(approve){
      const id = approve.getAttribute('data-id');
      if(!confirm('Approve this re‑registration?')) return;
      fetch('/semester-registration/approve-reregister', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
        body:JSON.stringify({ request_id:id })
      })
      .then(r=>r.json())
      .then(d=>{
        if(d.success){ showSuccessMessage(d.message||'Updated successfully.'); loadSemTermRequests(); loadSemTermRejected(); }
        else { showErrorMessage(d.message||'Failed to update.'); }
      })
      .catch(()=>showErrorMessage('Request failed. Please try again.'));
      return;
    }

    if(reject){
      // open modal to capture reason/comment
      const row = reject.closest('tr');
      const rid = reject.getAttribute('data-id'); // request row id (not used by API)
      const sid = row?.dataset.studentId || '';
      const iid = row?.dataset.intakeId || '';
      const sem = row?.dataset.semesterId || '';
      const modalEl = document.getElementById('semtermRejectModal');
      modalEl.querySelector('#semterm_reject_student_id').value = sid;
      modalEl.querySelector('#semterm_reject_intake_id').value = iid;
      modalEl.querySelector('#semterm_reject_semester_id').value = sem;
      modalEl.querySelector('#semterm_reject_reason').value = '';
      new bootstrap.Modal(modalEl).show();
      return;
    }
  });

  // Nested tabs inside semterm
  document.querySelectorAll('#semtermSubTabs [data-bs-toggle="tab"]').forEach(tab => {
    tab.addEventListener('shown.bs.tab', (ev) => {
      const t = ev.target.getAttribute('data-bs-target');
      if (t === '#semterm-pending') loadSemTermRequests();
      if (t === '#semterm-rejected') loadSemTermRejected();
    });
  });

  function loadSemTermRejected(){
    const rejectedBody = document.getElementById('semtermRejectedTableBody');
    if (!rejectedBody) return;
    rejectedBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">Loading…</td></tr>`;
    fetch('/semester-registration/terminated-requests?status=rejected')
      .then(r=>r.json())
      .then(d=>{
        if(!d.success || !d.requests || !d.requests.length){
          rejectedBody.innerHTML = `<tr><td colspan="7" class="text-center text-muted">No rejected requests.</td></tr>`;
          return;
        }
        rejectedBody.innerHTML='';
        d.requests.forEach(r=>{
          const tr = `
            <tr>
              <td>${r.student_id}</td>
              <td>${r.student_name||''}</td>
              <td>${r.course_name||''}</td>
              <td>${r.intake||''}</td>
              <td>${r.semester_name||''}</td>
              <td title="${r.reason||''}">${(r.reason||'').length>80?(r.reason||'').substring(0,80)+'…':(r.reason||'')}</td>
              <td>${r.rejected_at||r.requested_at||''}</td>
            </tr>`;
          rejectedBody.insertAdjacentHTML('beforeend', tr);
        });
      })
      .catch(()=>{
        rejectedBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Error loading rejected requests.</td></tr>`;
      });
  }

  // Submit handler for SemTerm reject modal
  (function(){
    const form = document.getElementById('semtermRejectForm');
    if(!form) return;
    form.addEventListener('submit', function(e){
      e.preventDefault();
      const sid = document.getElementById('semterm_reject_student_id').value;
      const iid = document.getElementById('semterm_reject_intake_id').value;
      const sem = document.getElementById('semterm_reject_semester_id').value;
      const reason = document.getElementById('semterm_reject_reason').value.trim();
      if(!reason){ showErrorMessage('Please enter a reason.'); return; }

      const btn = form.querySelector('button[type="submit"]');
      const original = btn.textContent; btn.disabled = true; btn.textContent = 'Rejecting…';

      fetch('/semester-registration/reject-reregister', {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
        body: JSON.stringify({ student_id: sid, intake_id: iid, semester_id: sem, comment: reason })
      })
      .then(r=>r.json())
      .then(d=>{
        if(d.success){
          showSuccessMessage(d.message||'Rejected');
          const m = bootstrap.Modal.getInstance(document.getElementById('semtermRejectModal'));
          if (m) m.hide();
          loadSemTermRequests();
          loadSemTermRejected();
        } else {
          showErrorMessage(d.message||'Failed to reject');
        }
      })
      .catch(()=> showErrorMessage('Reject failed'))
      .finally(()=> { btn.disabled=false; btn.textContent = original; });
    });
  })();

});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/nebula final/Nebula/resources/views/Special_approval_list.blade.php ENDPATH**/ ?>