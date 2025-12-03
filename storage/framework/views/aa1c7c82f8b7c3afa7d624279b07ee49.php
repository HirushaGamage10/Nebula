<?php $__env->startSection('title', 'Course Change - Switch Intake'); ?>

<?php $__env->startSection('content'); ?>
<div class="container mt-5 mb-5">
    <div class="card shadow border-0">
        <div class="card-body">

            <h3 class="text-primary mb-4">Course Change (Switch Intake)</h3>

            <!-- Search Student -->
            <form id="searchForm" class="mb-4">
                <div class="input-group">
                    <input type="text" class="form-control" id="nic" placeholder="Enter Student NIC">
                    <button class="btn btn-primary" type="submit">Search</button>
                </div>
            </form>

            <!-- Student Details Section -->
            <div id="student-section" style="display:none;">

                <div class="mb-4">
                    <h5 class="text-info">Student Details</h5>
                    <p><strong>Name:</strong> <span id="s_name"></span></p>
                    <p><strong>ID:</strong> <span id="s_id"></span></p>
                </div>

                <h5 class="text-secondary">Current Course Registrations</h5>

                <table class="table table-bordered align-middle" id="reg_table">
                    <thead class="table-light">
                        <tr>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Start Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>

            <!-- Course + Intake Selection -->
            <div id="intake-section" style="display:none;" class="mt-4">
                <h5 class="text-info">Change Course and Intake</h5>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Select Course</label>
                        <select id="course_select" class="form-select">
                            <option value="">Select Course</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Select New Intake</label>
                        <select id="new_intake" class="form-select">
                            <option value="">Select Intake</option>
                        </select>
                    </div>
                </div>

                <button class="btn btn-success mt-3" onclick="submitChange()">Confirm Change</button>
            </div>

        </div>
    </div>
</div>

<script>
let selectedRegistration = null;
let currentIntakeId = null;

// ========================= SEARCH STUDENT =========================
document.getElementById('searchForm').addEventListener('submit', async e => {
    e.preventDefault();
    let nic = document.getElementById('nic').value.trim();
    if (!nic) return alert('Enter NIC');

    const res = await fetch("<?php echo e(route('course.change.find')); ?>", {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ nic: nic })
    });

    const data = await res.json();

    if (data.status === 'success') {
        document.getElementById('student-section').style.display = 'block';

        document.getElementById('s_name').innerText = data.student.full_name;
        document.getElementById('s_id').innerText = data.student.student_id;

        const tbody = document.querySelector('#reg_table tbody');
        tbody.innerHTML = '';

        data.registrations.forEach(r => {
            tbody.innerHTML += `
                <tr>
                    <td>${r.course.course_name}</td>
                    <td>${r.intake.batch}</td>
                    <td>${r.course_start_date}</td>
                    <td>
                        <button class="btn btn-warning btn-sm"
                            onclick="loadCourseOptions(${r.id}, ${r.course_id}, '${r.location}', ${r.intake_id})">
                            Change
                        </button>
                    </td>
                </tr>
            `;
        });

    } else {
        alert(data.message || 'Student not found');
    }
});


// ========================= LOAD COURSE LIST =========================
function loadCourseOptions(regId, courseId, location, intakeId) {
    selectedRegistration = regId;
    currentIntakeId = intakeId;

    fetch("<?php echo e(route('course.change.courses')); ?>")
    .then(res => res.json())
    .then(data => {
        let courseSelect = document.getElementById('course_select');
        courseSelect.innerHTML = '<option value="">Select Course</option>';

        data.courses.forEach(c => {
            courseSelect.innerHTML += `<option value="${c.course_id}">${c.course_name}</option>`;
        });

        document.getElementById('intake-section').style.display = 'block';
    });
}


// ========================= LOAD INTAKES AFTER COURSE SELECT =========================
document.getElementById('course_select').addEventListener('change', function () {
    let courseId = this.value;

    if (!courseId) return;

    fetch("<?php echo e(route('course.change.new.intakes')); ?>", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({ course_id: courseId })
    })
    .then(res => res.json())
    .then(res => {
        let select = document.getElementById('new_intake');
        select.innerHTML = '<option value="">Select Intake</option>';

        res.intakes
            .filter(i => i.intake_id != currentIntakeId)
            .sort((a,b) => new Date(b.start_date) - new Date(a.start_date))
            .forEach(i => {
                select.innerHTML += `
                    <option value="${i.intake_id}">
                        ${i.batch} (Starts: ${i.start_date})
                    </option>`;
            });
    });
});


// ========================= SUBMIT CHANGE =========================
function submitChange() {
    let intakeId = document.getElementById('new_intake').value;

    if (!intakeId) return alert('Select a new intake');

    fetch("<?php echo e(route('course.change.submit')); ?>", {
        method:'POST',
        headers:{
            'Content-Type':'application/json',
            'X-CSRF-TOKEN':'<?php echo e(csrf_token()); ?>'
        },
        body: JSON.stringify({
            registration_id: selectedRegistration,
            new_intake_id: intakeId
        })
    })
    .then(res => res.json())
    .then(res => {
        alert(res.message);
        if (res.status === 'success') {
            window.location.reload();
        }
    });
}
</script>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/course_change/index.blade.php ENDPATH**/ ?>