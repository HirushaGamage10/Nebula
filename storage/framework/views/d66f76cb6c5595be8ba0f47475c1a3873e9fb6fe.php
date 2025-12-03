

<?php $__env->startSection('title', 'NEBULA | Student List'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Student List</h2>
      <hr>

      <div id="spinner-overlay" style="display:none;">
        <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
      </div>
      <div id="toastContainer" aria-live="polite" aria-atomic="true"
           style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

      <!-- Filters -->
      <div id="student-list-filters" class="mb-4">
        <div class="mb-3 row mx-3">
          <label class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <select class="form-select" id="location">
              <option value="" selected disabled>Select a Location</option>
              <?php $__currentLoopData = $locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loc): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($loc); ?>">Nebula Institute of Technology - <?php echo e($loc); ?></option>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
          </div>
        </div>
        <div class="mb-3 row mx-3">
          <label class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <select class="form-select" id="course" disabled>
              <option value="" selected disabled>Select Course</option>
            </select>
          </div>
        </div>
        <div class="mb-3 row mx-3">
          <label class="col-sm-2 col-form-label">Batch <span class="text-danger">*</span></label>
          <div class="col-sm-10">
            <select class="form-select" id="intake" disabled>
              <option value="" selected disabled>Select Batch</option>
            </select>
          </div>
        </div>
      </div>

      <hr class="my-4">

      <!-- Tabs + Table -->
      <div class="mt-4" id="studentTableSection" style="display:none;">
        <div class="d-flex justify-content-between align-items-center mb-2">
          <ul class="nav nav-pills" id="statusTabs">
  <li class="nav-item">
    <button class="nav-link active" data-status="all" id="tab-all">
      All <span class="badge bg-secondary ms-1" id="count-all">0</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-status="pending" id="tab-pending">
      Pending <span class="badge bg-secondary ms-1" id="count-pending">0</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-status="registered" id="tab-registered">
      Registered <span class="badge bg-secondary ms-1" id="count-registered">0</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-status="terminated" id="tab-terminated">
      Not Eligible <span class="badge bg-secondary ms-1" id="count-terminated">0</span>
    </button>
  </li>
  <li class="nav-item">
    <button class="nav-link" data-status="completed" id="tab-completed">
      Completed <span class="badge bg-secondary ms-1" id="count-completed">0</span>
    </button>
  </li>
</ul>

          <div class="d-flex gap-2">
            <button id="downloadListBtn" class="btn btn-primary" type="button">
              <i class="bi bi-download"></i> Download PDF
            </button>
            <button id="downloadListExcelBtn" class="btn btn-success" type="button">
              <i class="bi bi-file-earmark-spreadsheet"></i> Download Excel
            </button>
          </div>
        </div>

        <h4 class="text-center mb-3" id="studentListHeader"></h4>

        <div class="table-responsive">
          <table class="table table-bordered table-striped">
            <thead class="table-light">
              <tr>
                <th>#</th>
                <th>Course Registration ID</th>
                <th>Student ID</th>
                <th>Student Name</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="studentTableBody"></tbody>
          </table>
        </div>

        <div class="d-flex justify-content-end mt-2">
          <span id="studentTotalCount" class="fw-bold"></span>
        </div>
      </div>
    </div>
  </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const locationSelect = document.getElementById('location');
  const courseSelect   = document.getElementById('course');
  const intakeSelect   = document.getElementById('intake');
  const section        = document.getElementById('studentTableSection');
  const tbody          = document.getElementById('studentTableBody');
  const downloadBtn    = document.getElementById('downloadListBtn');
  const downloadExcelBtn = document.getElementById('downloadListExcelBtn');
  const headerEl       = document.getElementById('studentListHeader');

  let allStudents = [];
  let currentStatus = 'all';

  function reset(select, placeholder){
    select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
    select.disabled = true;
  }

  function showSpinner(show){
    document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
  }

  function showToast(title, message, bg){
    const container = document.getElementById('toastContainer');
    const el = document.createElement('div');
    el.className = `toast align-items-center text-white ${bg} border-0`;
    el.role = 'alert'; el.ariaLive = 'assertive'; el.ariaAtomic = 'true';
    el.innerHTML = `
      <div class="d-flex">
        <div class="toast-body"><strong>${title}:</strong> ${message}</div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
      </div>`;
    container.appendChild(el);
    new bootstrap.Toast(el).show();
    el.addEventListener('hidden.bs.toast', ()=> el.remove());
  }

  locationSelect.addEventListener('change', () => {
    const loc = locationSelect.value;
    reset(courseSelect,'Select Course');
    reset(intakeSelect,'Select Batch');
    section.style.display='none';
    if(!loc) return;

    showSpinner(true);
    fetch(`/api/courses-by-location/${encodeURIComponent(loc)}`)
      .then(r=>r.json())
      .then(data=>{
        if(data.success && data.courses?.length){
          courseSelect.innerHTML = `<option selected disabled value="">Select Course</option>`;
          data.courses.forEach(c=>{
            courseSelect.add(new Option(c.course_name, c.course_id));
          });
          courseSelect.disabled=false;
        }else{
          showToast('Info','No courses found for this location.','bg-info');
        }
      })
      .catch(()=>showToast('Error','Failed to fetch courses.','bg-danger'))
      .finally(()=>showSpinner(false));
  });

  courseSelect.addEventListener('change', ()=>{
    const courseId = courseSelect.value;
    const loc = locationSelect.value;
    reset(intakeSelect,'Select Batch');
    section.style.display='none';
    if(!courseId || !loc) return;

    showSpinner(true);
    fetch(`/get-intakes/${encodeURIComponent(courseId)}/${encodeURIComponent(loc)}`)
      .then(r=>r.json())
      .then(data=>{
        if(data.intakes?.length){
          intakeSelect.innerHTML = `<option selected disabled value="">Select Batch</option>`;
          data.intakes.forEach(i=>{
            intakeSelect.add(new Option(i.batch, i.intake_id));
          });
          intakeSelect.disabled=false;
        }else{
          showToast('Info','No intakes for this course/location.','bg-info');
        }
      })
      .catch(()=>showToast('Error','Failed to fetch intakes.','bg-danger'))
      .finally(()=>showSpinner(false));
  });

  intakeSelect.addEventListener('change', fetchStudents);

  function fetchStudents(){
    const location = locationSelect.value;
    const courseId = courseSelect.value;
    const intakeId = intakeSelect.value;
    if(!location || !courseId || !intakeId){ section.style.display='none'; return; }

    showSpinner(true);
    fetch('/get-student-list-data', {
      method:'POST',
      headers:{'Content-Type':'application/json','X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'},
      body: JSON.stringify({location, course_id:courseId, intake_id:intakeId})
    })
    .then(r=>r.json())
    .then(data=>{
      if(data.success){
        allStudents = data.students ?? [];
        // counts
        document.getElementById('count-all').textContent        = allStudents.length;
        document.getElementById('count-pending').textContent    = allStudents.filter(s=>s.status==='pending').length;
        document.getElementById('count-registered').textContent = allStudents.filter(s=>s.status==='registered').length;
        document.getElementById('count-terminated').textContent = allStudents.filter(s=>s.status==='terminated').length;
        document.getElementById('count-completed').textContent  = allStudents.filter(s=>s.status==='completed').length;


        // header
        const locText = locationSelect.options[locationSelect.selectedIndex].text;
        const crsText = courseSelect.options[courseSelect.selectedIndex].text;
        const inText  = intakeSelect.options[intakeSelect.selectedIndex].text;
        headerEl.innerHTML = `Student list - ${locText}<br>${crsText} - ${inText}`;

        renderTable();
        section.style.display='block';
      }else{
        section.style.display='none';
        showToast('Info','No students found for the selected criteria.','bg-info');
      }
    })
    .catch(()=>{ section.style.display='none'; showToast('Error','Error fetching students.','bg-danger'); })
    .finally(()=>showSpinner(false));
  }

  function renderTable(){
    const list = (currentStatus==='all')
      ? allStudents
      : allStudents.filter(s=>s.status===currentStatus);

    tbody.innerHTML='';
    list.forEach((s, idx)=>{
      const isTerminated = s.status === 'terminated';
      const trClass = isTerminated ? 'table-danger' : '';
      tbody.insertAdjacentHTML('beforeend', `
        <tr class="${trClass}">
          <td>${idx+1}</td>
          <td>${s.course_registration_id ?? ''}</td>
          <td>${s.student_id ?? ''}</td>
          <td>${s.name ?? ''}</td>
          <td class="text-capitalize">${s.status ?? ''}</td>
        </tr>
      `);
    });

    document.getElementById('studentTotalCount').textContent =
      `Total Students: ${list.length}`;
  }

  // Tabs
  document.getElementById('statusTabs').addEventListener('click', (e)=>{
    const btn = e.target.closest('button[data-status]');
    if(!btn) return;
    document.querySelectorAll('#statusTabs .nav-link').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    currentStatus = btn.dataset.status;
    renderTable();
  });

  // Download matches active tab
  downloadBtn.addEventListener('click', ()=>{
    const location = locationSelect.value;
    const courseId = courseSelect.value;
    const intakeId = intakeSelect.value;
    if(!location || !courseId || !intakeId){
      showToast('Error','Please select all filters before downloading.','bg-danger');
      return;
    }

    const form = document.createElement('form');
    form.method='POST'; form.action='/download-student-list'; form.target='_blank'; form.style.display='none';
    form.innerHTML = `
      <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
      <input type="hidden" name="location" value="${location}">
      <input type="hidden" name="course_id" value="${courseId}">
      <input type="hidden" name="intake_id" value="${intakeId}">
      <input type="hidden" name="status" value="${currentStatus}">
    `;
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  });

  // Excel Download matches active tab
  downloadExcelBtn.addEventListener('click', ()=>{
    const location = locationSelect.value;
    const courseId = courseSelect.value;
    const intakeId = intakeSelect.value;
    if(!location || !courseId || !intakeId){
      showToast('Error','Please select all filters before downloading.','bg-danger');
      return;
    }

    const form = document.createElement('form');
    form.method='POST'; form.action='/download-student-list-excel'; form.target='_blank'; form.style.display='none';
    form.innerHTML = `
      <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
      <input type="hidden" name="location" value="${location}">
      <input type="hidden" name="course_id" value="${courseId}">
      <input type="hidden" name="intake_id" value="${intakeId}">
      <input type="hidden" name="status" value="${currentStatus}">
    `;
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
  });
});
</script>

<style>
  .lds-ring { display:inline-block; position:relative; width:80px; height:80px; }
  .lds-ring div { box-sizing:border-box; display:block; position:absolute; width:64px; height:64px; margin:8px;
    border:8px solid #fff; border-radius:50%; animation:lds-ring 1.2s cubic-bezier(0.5,0,0.5,1) infinite;
    border-color:#fff transparent transparent transparent; }
  .lds-ring div:nth-child(1){animation-delay:-.45s}
  .lds-ring div:nth-child(2){animation-delay:-.3s}
  .lds-ring div:nth-child(3){animation-delay:-.15s}
  @keyframes lds-ring { 0%{transform:rotate(0)} 100%{transform:rotate(360deg)} }
  #spinner-overlay { position:fixed; inset:0; background:rgba(0,0,0,.5); display:flex; justify-content:center; align-items:center; z-index:9999; }
</style>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/student_list.blade.php ENDPATH**/ ?>