

<?php $__env->startSection('title', 'NEBULA | Semester Registration'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Semester Registration Management</h2>
            <hr>
            <form id="courseForm" method="POST" action="<?php echo e(route('semester.registration.store')); ?>">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="location" id="location_hidden">
                <input type="hidden" name="specialization" id="specialization_hidden">
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location" required>
                            <option selected disabled value="">Select a Location</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="course_id" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course_id" name="course_id" required disabled>
                            <option value="">Select Course</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intake_id" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intake_id" name="intake_id" required disabled>
                            <option value="">Select Intake</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="semester_id" class="col-sm-2 col-form-label">Semester <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="semester_id" name="semester_id" required disabled>
                            <option value="">Select Semester</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3" id="specialization_row" style="display:none;">
                    <label for="specialization" class="col-sm-2 col-form-label">Specialization</label>
                    <div class="col-sm-10">
                        <select class="form-select" id="specialization" name="specialization">
                            <option value="">Select Specialization</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3" id="students_table_row" style="display:none;">
                    <ul class="nav nav-tabs mb-3" id="statusTabs">
                <li class="nav-item">
                    <a class="nav-link active" href="#" data-status="all">All</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="registered">Registered</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#" data-status="terminated">Terminated</a>
                </li>
                </ul>
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="students_table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NIC</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- JS will populate rows here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="d-grid mx-3">
                    <button type="submit" class="btn btn-primary">Update Registration</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php if(session('success')): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            <?php echo e(session('success')); ?>

        </div>
    </div>
</div>
<?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- Toast auto-hide ---
    setTimeout(function() {
        document.querySelectorAll('.toast').forEach(toast => {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        });
    }, 3000);

    // --- DOM refs ---
    const locationSelect = document.getElementById('location');
    const courseSelect = document.getElementById('course_id');
    const intakeSelect = document.getElementById('intake_id');
    const semesterSelect = document.getElementById('semester_id');
    const studentsTableBody = document.querySelector('#students_table tbody');

    // Special approval modal + payload store
    const saModalEl = document.getElementById('specialApprovalModal');
    const saFormEl  = document.getElementById('specialApprovalForm');
    const saStudentIdEl = document.getElementById('sa_student_id');
    const saReasonEl    = document.getElementById('sa_reason');
    const saFileEl      = document.getElementById('sa_file');
    const saModal = saModalEl ? new bootstrap.Modal(saModalEl) : null;

    // Keep per-student SA payload until submit
    const specialApprovalPayload = {}; // { [studentId]: { reason, file } }

    // --- helpers ---
    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
        select.disabled = true;
    }
    function resetSpecialization() {
        document.getElementById('specialization').innerHTML = '<option value="">Select Specialization</option>';
        document.getElementById('specialization_row').style.display = 'none';
        document.getElementById('specialization_hidden').value = '';
    }
    function enableSelect(select) { select.disabled = false; }

    // --- Location change ---
    locationSelect.addEventListener('change', function() {
        resetAndDisable(courseSelect, 'Select Course');
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';
        resetSpecialization();

        document.getElementById('location_hidden').value = this.value;

        if (locationSelect.value) {
            fetch(`/semester-registration/get-courses-by-location?location=${encodeURIComponent(locationSelect.value)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.courses.length > 0) {
                        let options = '<option value="" selected disabled>Select Course</option>';
                        data.courses.forEach(course => {
                            options += `<option value="${course.course_id}">${course.course_name}</option>`;
                        });
                        courseSelect.innerHTML = options;
                        enableSelect(courseSelect);
                    } else {
                        resetAndDisable(courseSelect, 'No courses available');
                    }
                });
        }
    });

    // --- Course change ---
    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';
        document.getElementById('specialization_row').style.display = 'none';

        if (courseSelect.value && locationSelect.value) {
            // Specializations
            fetch(`/api/courses/${courseSelect.value}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.course && data.course.specializations) {
                        let specializations = [];
                        if (typeof data.course.specializations === 'string') {
                            try { specializations = JSON.parse(data.course.specializations); } catch (e) { specializations = []; }
                        } else if (Array.isArray(data.course.specializations)) {
                            specializations = data.course.specializations;
                        }
                        specializations = specializations.filter(spec => spec && spec.trim() !== '');
                        if (specializations.length > 0) {
                            let options = '<option value="">Select Specialization</option>';
                            specializations.forEach(spec => { options += `<option value="${spec}">${spec}</option>`; });
                            document.getElementById('specialization').innerHTML = options;
                            document.getElementById('specialization_row').style.display = '';
                        }
                    }
                });

            // Intakes
            fetch(`/semester-registration/get-ongoing-intakes?course_id=${encodeURIComponent(courseSelect.value)}&location=${encodeURIComponent(locationSelect.value)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.intakes.length > 0) {
                        let options = '<option value="" selected disabled>Select Intake</option>';
                        data.intakes.forEach(intake => {
                            options += `<option value="${intake.intake_id}">${intake.batch}</option>`;
                        });
                        intakeSelect.innerHTML = options;
                        enableSelect(intakeSelect);
                    } else {
                        resetAndDisable(intakeSelect, 'No intakes available');
                    }
                });
        }
    });

    // --- Intake change ---
    intakeSelect.addEventListener('change', function() {
        resetAndDisable(semesterSelect, 'Select Semester');
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';

        if (courseSelect.value && intakeSelect.value && locationSelect.value) {
            fetch(`/semester-registration/get-open-semesters?course_id=${encodeURIComponent(courseSelect.value)}&intake_id=${encodeURIComponent(intakeSelect.value)}&location=${encodeURIComponent(locationSelect.value)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.semesters.length > 0) {
                        let options = '<option value="" selected disabled>Select Semester</option>';
                        data.semesters.forEach(sem => {
                            const statusText = sem.status === 'active' ? ' (Active)' :
                                               sem.status === 'upcoming' ? ' (Upcoming)' :
                                               sem.status === 'completed' ? ' (Completed)' : '';
                            options += `<option value="${sem.semester_id}">${sem.semester_name}${statusText}</option>`;
                        });
                        semesterSelect.innerHTML = options;
                        enableSelect(semesterSelect);
                    } else {
                        resetAndDisable(semesterSelect, 'No semesters available');
                    }
                });
        }
    });

    // --- Semester change ---
    semesterSelect.addEventListener('change', function() {
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';

        if (courseSelect.value && intakeSelect.value && semesterSelect.value) {
            fetch(`/api/courses/${courseSelect.value}`)
                .then(res => res.json())
                .then(data => {
                    let specs = [];
                    if (data.success && data.course && data.course.specializations) {
                        if (typeof data.course.specializations === 'string') {
                            try { specs = JSON.parse(data.course.specializations); } catch (e) { specs = []; }
                        } else if (Array.isArray(data.course.specializations)) {
                            specs = data.course.specializations;
                        }
                        specs = specs.filter(s => s && s.trim() !== '');
                    }
                    if (specs.length > 0) {
                        let options = '<option value="">Select Specialization</option>';
                        specs.forEach(s => { options += `<option value="${s}">${s}</option>`; });
                        document.getElementById('specialization').innerHTML = options;
                        document.getElementById('specialization_row').style.display = '';
                    } else {
                        document.getElementById('specialization_row').style.display = 'none';
                        loadStudentsTable();
                    }
                })
                .catch(() => { loadStudentsTable(); });
        }
    });

    // --- Load students table ---
    function loadStudentsTable(filterStatus = 'all') {
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';

        if (courseSelect.value && intakeSelect.value && semesterSelect.value) {
            fetch(`/semester-registration/get-eligible-students?course_id=${encodeURIComponent(courseSelect.value)}&intake_id=${encodeURIComponent(intakeSelect.value)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.students.length > 0) {
                        let filtered = filterStatus === 'all' ? data.students : data.students.filter(stu => stu.status === filterStatus);
                        let rows = '';
                        filtered.forEach(student => {
                            rows += `
                                <tr 
                                    data-student-id="${student.student_id}" 
                                    data-status="${student.status}" 
                                    data-original-status="${student.status}"
                                    class="${student.status === 'terminated' ? 'table-danger' : student.status === 'holding' ? 'table-warning' : ''}">
                                    <td>${student.student_id}</td>
                                    <td>${student.name}</td>
                                    <td>${student.email}</td>
                                    <td>${student.nic}</td>
                                    <td class="student-status">${student.status}</td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button type="button" class="btn btn-outline-success btn-sm toggle-selection ${student.status === 'registered' ? 'active' : ''}" data-action="registered">Register</button>
                                            <button type="button" class="btn btn-outline-warning btn-sm toggle-selection ${student.status === 'holding' ? 'active' : ''}" data-action="holding">Hold</button>
                                            <button type="button" class="btn btn-outline-danger btn-sm toggle-selection ${student.status === 'terminated' ? 'active' : ''}" data-action="terminated">Terminate</button>
                                        </div>
                                    </td>
                                </tr>`;
                        });
                        studentsTableBody.innerHTML = rows;
                        document.getElementById('students_table_row').style.display = '';
                    } else {
                        studentsTableBody.innerHTML = '<tr><td colspan="6" class="text-center">No eligible students found.</td></tr>';
                        document.getElementById('students_table_row').style.display = '';
                    }
                })
                .catch(() => {
                    studentsTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading students.</td></tr>';
                    document.getElementById('students_table_row').style.display = '';
                });
        }
    }

    // --- Tabs filter ---
    document.querySelectorAll('#statusTabs .nav-link').forEach(tab => {
        tab.addEventListener('click', function(e) {
            e.preventDefault();
            document.querySelectorAll('#statusTabs .nav-link').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            loadStudentsTable(this.dataset.status);
        });
    });

    // --- Specialization change ---
    document.getElementById('specialization').addEventListener('change', function() {
        document.getElementById('specialization_hidden').value = this.value;
        loadStudentsTable();
    });

    // --- Toggle register/terminate selection (with SA trigger) ---
    document.addEventListener('click', function(e) {
        if (!e.target.classList.contains('toggle-selection')) return;

        const btn = e.target;
        const row = btn.closest('tr');
        const action = btn.dataset.action;
        const originalStatus = row.dataset.originalStatus;
        const currentStatus  = row.dataset.status;
        const studentId = row.dataset.studentId;

        // If originally terminated and trying to set to registered → require SA
        if (action === 'registered' && originalStatus === 'terminated') {
            if (saModal) {
                saStudentIdEl.value = studentId;
                saReasonEl.value = '';
                if (saFileEl) saFileEl.value = '';
                saModal.show();
            }
            return;
        }

        // If trying to terminate, check clearances first
        if (action === 'terminated') {
            const payload = new FormData();
            payload.append('student_id', studentId);
            payload.append('course_id', courseSelect.value || '');
            payload.append('intake_id', intakeSelect.value || '');

            fetch('<?php echo e(route("semester.registration.checkClearances")); ?>', {
                method: 'POST',
                body: payload,
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(r => r.json())
            .then(data => {
                if (!data.success) {
                    showToast(data.message || 'Could not check clearances', 'error');
                    return;
                }

                // populate modal list
                const list = document.getElementById('clearanceList');
                list.innerHTML = '';
                data.clearances.forEach(c => {
                    const li = document.createElement('li');
                    li.className = 'mb-2';
                    li.innerHTML = `<strong>${c.label}:</strong> ${c.status_text}` + (c.note ? ` <small class="text-muted">(${c.note})</small>` : '');
                    list.appendChild(li);
                });

                // Show pending payments if any
                if (data.pending_payments && data.pending_payments.length > 0) {
                    const hr = document.createElement('hr');
                    list.appendChild(hr);
                    const pTitle = document.createElement('li');
                    pTitle.innerHTML = '<strong>Pending Payments</strong>';
                    list.appendChild(pTitle);
                    data.pending_payments.forEach(p => {
                        const pli = document.createElement('li');
                        pli.className = 'mb-2';
                        pli.innerHTML = `${p.formatted} — ${p.description}` + (p.due_date ? ` <small class="text-muted">(Due: ${p.due_date})</small>` : '');
                        list.appendChild(pli);
                    });
                }

                // store studentId on confirm button
                document.getElementById('confirmTerminateBtn').dataset.studentId = studentId;
                const clrModal = new bootstrap.Modal(document.getElementById('clearanceCheckModal'));
                clrModal.show();
            })
            .catch(() => showToast('Failed to check clearances', 'error'));

            return;
        }

        // Normal toggle
        row.querySelectorAll('.toggle-selection').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        row.dataset.status = action;
        row.querySelector('.student-status').textContent = action;
        // Update row styling
        row.classList.toggle('table-danger', action === 'terminated');
        row.classList.toggle('table-warning', action === 'holding');
    });

    // Confirm termination button (from clearance modal)
    document.getElementById('confirmTerminateBtn').addEventListener('click', function() {
        const sid = this.dataset.studentId;
        if (!sid) return;
        const row = document.querySelector(`#students_table tbody tr[data-student-id="${sid}"]`);
        if (!row) return;

        // Set the row status to terminated and update visuals
        row.querySelectorAll('.toggle-selection').forEach(b => b.classList.remove('active'));
        const termBtn = row.querySelector('.toggle-selection[data-action="terminated"]');
        if (termBtn) termBtn.classList.add('active');
        row.dataset.status = 'terminated';
        row.querySelector('.student-status').textContent = 'terminated';
        row.classList.add('table-danger');

        // hide modal
        const modalEl = document.getElementById('clearanceCheckModal');
        const m = bootstrap.Modal.getInstance(modalEl);
        if (m) m.hide();
    });

    // --- Special Approval modal submit ---
    if (saFormEl) {
        saFormEl.addEventListener('submit', function(e) {
            e.preventDefault();
            const studentId = saStudentIdEl.value;
            const reason = saReasonEl.value.trim();
            const file = saFileEl && saFileEl.files && saFileEl.files[0] ? saFileEl.files[0] : null;

            if (!reason) {
                alert('Please provide a reason.');
                return;
            }

            // Store payload
            specialApprovalPayload[studentId] = { reason, file };

            // Update the row visually to "registered"
            const row = document.querySelector(`#students_table tbody tr[data-student-id="${studentId}"]`);
            if (row) {
                row.querySelectorAll('.toggle-selection').forEach(b => b.classList.remove('active'));
                const regBtn = row.querySelector('.toggle-selection[data-action="registered"]');
                if (regBtn) regBtn.classList.add('active');
                row.dataset.status = 'registered';
                row.querySelector('.student-status').textContent = 'registered';
                // mark as pending approval locally
                row.classList.remove('table-danger');
                row.classList.add('table-warning'); // hint: has SA attached
            }

            saModal.hide();
        });
    }

    // --- Form submit ---
    document.getElementById('courseForm').addEventListener('submit', function(e) {
        e.preventDefault();

        // Build student selection payload
        const selectedStudents = [];
        document.querySelectorAll('#students_table tbody tr').forEach(row => {
            selectedStudents.push({
                student_id: row.dataset.studentId,
                status: row.dataset.status,
                original_status: row.dataset.originalStatus   // ✅ added
            });
        });
        if (selectedStudents.length === 0) {
            alert('Please select at least one student.');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Registering...';

        const formData = new FormData(this);
        // ensure hidden fields are up to date
        formData.set('location', locationSelect.value || '');
        formData.set('specialization', document.getElementById('specialization').value || '');
        formData.append('register_students', JSON.stringify(selectedStudents));

        // Attach SA payloads
        Object.keys(specialApprovalPayload).forEach(studentId => {
            const p = specialApprovalPayload[studentId];
            formData.append(`sa_reasons[${studentId}]`, p.reason);
            if (p.file) {
                formData.append(`sa_files[${studentId}]`, p.file);
            }
        });

        fetch('<?php echo e(route("semester.registration.store")); ?>', {
            method: 'POST',
            body: formData,
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async r => {
            const ct = r.headers.get('content-type') || '';
            if (!ct.includes('application/json')) {
                const text = await r.text();
                throw new Error(text.slice(0, 300));
            }
            return r.json();
        })
        .then(data => {
            if (data.success) {
                showToast(data.message || 'Saved.', 'success');
                setTimeout(() => {
                    // reset UI
                    document.getElementById('courseForm').reset();
                    studentsTableBody.innerHTML = '';
                    document.getElementById('students_table_row').style.display = 'none';
                    resetSpecialization();
                    resetAndDisable(courseSelect, 'Select Course');
                    resetAndDisable(intakeSelect, 'Select Intake');
                    resetAndDisable(semesterSelect, 'Select Semester');
                }, 1500);
            } else {
                showToast(data.message || 'An error occurred.', 'error');
            }
        })
        .catch(err => {
            console.error(err);
            showToast('An error occurred while saving. Check console.', 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    // --- Toast helpers ---
    function showToast(message, type) {
        document.querySelectorAll('.toast').forEach(t => t.remove());
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        toastContainer.innerHTML = `
            <div class="toast show" role="alert">
                <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
                    <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">${message}</div>
            </div>`;
        setTimeout(() => {
            const toast = toastContainer.querySelector('.toast');
            if (toast) new bootstrap.Toast(toast).hide();
        }, 5000);
    }
    function createToastContainer() {
        const c = document.createElement('div');
        c.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(c);
        return c;
    }
});
</script>

<!-- Clearance Check Modal (shown before terminating a student) -->
<div class="modal fade" id="clearanceCheckModal" tabindex="-1" aria-labelledby="clearanceCheckModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="clearanceCheckModalLabel">Student has existing clearances</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>The selected student has one or more clearance records. Please review them before terminating. Do you still want to proceed?</p>
                <ul id="clearanceList" class="list-unstyled"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmTerminateBtn" class="btn btn-danger">Yes, Terminate</button>
            </div>
        </div>
    </div>
</div>

<!-- Special Approval Modal -->
<div class="modal fade" id="specialApprovalModal" tabindex="-1" aria-labelledby="specialApprovalModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="specialApprovalForm" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="specialApprovalModalLabel">Special Approval (DGM) Required</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" id="sa_student_id">
        <div class="mb-3">
          <label class="form-label">Reason <span class="text-danger">*</span></label>
          <textarea id="sa_reason" class="form-control" rows="4" required placeholder="Explain why this terminated student should be re-registered"></textarea>
        </div>
        <div class="mb-3">
          <label class="form-label">Attachment (optional)</label>
          <input type="file" id="sa_file" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
          <small class="text-muted">Attach any supporting document (max ~2MB recommended).</small>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="submit" class="btn btn-primary">Attach to Request</button>
      </div>
    </form>
  </div>
</div>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/semester_registration.blade.php ENDPATH**/ ?>