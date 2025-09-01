
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

            <!-- Inline register section -->
            <div id="registerSection" class="card mb-4 shadow-sm" style="display:none;">
              <div class="card-body bg-light">
                <h5 class="mb-3 text-center">Student Register For Course</h5>
                <form id="registerForm">
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Student NIC</label>
                    <div class="col-sm-8"><input type="text" class="form-control" id="inlineStudentNIC" name="nic" readonly></div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Student Registration Number</label>
                    <div class="col-sm-8"><input type="text" class="form-control" id="inlineStudentRegNo" name="registration_number" readonly></div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Intake</label>
                    <div class="col-sm-8"><input type="text" class="form-control" id="intake" name="intake" readonly></div>
                  </div>
                  <div class="row mb-3">
                    <label class="col-sm-4 col-form-label">Course Registration ID</label>
                    <div class="col-sm-8"><input type="text" class="form-control" id="inlineCourseRegId" name="course_registration_id" readonly></div>
                  </div>
                  <div class="text-center">
                    <button type="submit" class="btn btn-primary px-5 w-100">Register</button>
                  </div>
                </form>
              </div>
            </div>

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

<?php $__env->startPush('scripts'); ?>
<script>
// mini toast helpers
function showSuccessMessage(msg){const m=document.createElement('div');m.className='success-message';m.innerHTML=`<i class="ti ti-check-circle success-icon"></i>${msg}`;document.body.appendChild(m);setTimeout(()=>m.classList.add('show'),100);setTimeout(()=>{m.classList.remove('show');setTimeout(()=>m.remove(),300)},4000)}
function showErrorMessage(msg){const m=document.createElement('div');m.className='error-message';m.innerHTML=`<i class="ti ti-alert-circle error-icon"></i>${msg}`;document.body.appendChild(m);setTimeout(()=>m.classList.add('show'),100);setTimeout(()=>{m.classList.remove('show');setTimeout(()=>m.remove(),300)},5000)}

document.addEventListener('DOMContentLoaded', function() {
  const tableBody = document.getElementById('specialApprovalTableBody');
  const registerSection = document.getElementById('registerSection');
  const registerForm = document.getElementById('registerForm');
  const franchiseTableBody = document.getElementById('franchisePaymentTableBody');
  const semtermTableBody = document.getElementById('semtermTableBody');

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
          <td>${st.approval_status==1?'<span class="badge bg-success status-badge">Approved</span>':'<span class="badge bg-warning status-badge">Pending</span>'}</td>
          <td>${st.approval_status==1?'<span class="badge bg-success status-badge">Approved</span>':
            `<button class="btn btn-success btn-sm approve-btn"
               data-student-id="${st.student_id}"
               data-student-nic="${nic}"
               data-student-name="${st.name||''}"
               data-course-id="${st.course_id||''}"
               data-registration-number="${st.registration_number||''}"
               data-intake="${st.intake||''}">Approve</button>`}
          </td>
        </tr>`;
      tableBody.insertAdjacentHTML('beforeend', row);
    });
  }

  // inline register submit
  registerForm.addEventListener('submit', function(e){
    e.preventDefault();
    const formData = new FormData(registerForm);
    const current = window.currentStudentData;
    if(!current || !current.course_id){ showErrorMessage('Course information not available.'); return; }
    formData.append('course_id', current.course_id);

    fetch('/register-eligible-student',{method:'POST',headers:{'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},body:formData})
      .then(r=>r.json()).then(d=>{
        if(d.success){ showSuccessMessage(d.message||'Student registered successfully!'); setTimeout(()=>{registerSection.style.display='none'; location.reload();},1500); }
        else showErrorMessage(d.message||'Registration failed.');
      }).catch(()=>showErrorMessage('Registration failed. Please try again.'));
  });

  // click handlers in student table
  tableBody.addEventListener('click', function(e){
    if(e.target.classList.contains('approve-btn')){
      const nic  = e.target.getAttribute('data-student-nic');
      const reg  = e.target.getAttribute('data-registration-number');
      const cid  = e.target.getAttribute('data-course-id');
      const intake = e.target.getAttribute('data-intake');

      window.currentStudentData = {
        student_id: e.target.getAttribute('data-student-id'),
        nic, registration_number: reg, course_id: cid, intake
      };
      populateRegistrationForm(nic, reg, cid, intake);
      registerSection.style.display='block';
      registerSection.scrollIntoView({behavior:'smooth'});
    }

    const editBtn = e.target.closest('.edit-comment-btn');
    if(editBtn){
      document.getElementById('editCommentRegistrationId').value = editBtn.getAttribute('data-registration-id');
      document.getElementById('editCommentText').value = (editBtn.getAttribute('data-current-comment')||'').replace(/^No DGM comment$/,'');
      new bootstrap.Modal(document.getElementById('editCommentModal')).show();
    }
  });

  function populateRegistrationForm(nic, reg, courseId, intake){
    document.getElementById('inlineStudentNIC').value = nic||'';
    document.getElementById('inlineStudentRegNo').value = reg||'';
    document.getElementById('intake').value = intake||'2025-September';
    generateCourseRegistrationId(courseId);
  }

  function generateCourseRegistrationId(courseId){
    const intakeValue = (document.getElementById('intake')?.value)||'';
    let intakeId = 3; if(intakeValue==='2025-August') intakeId=2; if(intakeValue==='2025-September') intakeId=1;

    fetch(`/get-next-course-registration-id?intake_id=${intakeId}`,{headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'}})
      .then(r=>r.json()).then(d=>{
        document.getElementById('inlineCourseRegId').value = d.success? d.next_id : '2025/HND/SE/001';
      }).catch(()=>{ document.getElementById('inlineCourseRegId').value='2025/HND/SE/001'; });
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
    fetch('/semester-registration/terminated-requests')
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
        <tr data-request-id="${r.id}">
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
    if(approve || reject){
      const id = (approve||reject).getAttribute('data-id');
      const url = approve ? '/semester-registration/approve-reregister'
                          : '/semester-registration/reject-reregister';
      const confirmText = approve ? 'Approve this re‑registration?' : 'Reject this re‑registration?';
      if(!confirm(confirmText)) return;

      fetch(url, {
        method:'POST',
        headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
        body:JSON.stringify({ request_id:id })
      })
      .then(r=>r.json())
      .then(d=>{
        if(d.success){
          showSuccessMessage(d.message||'Updated successfully.');
          loadSemTermRequests();
        }else{
          showErrorMessage(d.message||'Failed to update.');
        }
      })
      .catch(()=>showErrorMessage('Request failed. Please try again.'));
    }
  });

});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/Special_approval_list.blade.php ENDPATH**/ ?>