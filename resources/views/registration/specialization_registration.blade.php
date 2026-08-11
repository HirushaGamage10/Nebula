@extends('inc.app')

@section('title', 'NEBULA | Specialization Registration')

@section('content')
<div class="container-fluid">
  <div class="card">
    <div class="card-body">
      <h2 class="text-center mb-4">Degree &amp; Diploma Specialization Registration</h2>
      <p class="text-muted text-center">Only students already eligible and course-registered can be assigned to a specialization.</p>
      <hr>

      <div id="statusMessage" class="mx-3"></div>

      <div class="mb-3 row mx-3">
        <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <select id="location" class="form-select">
            <option value="" selected>Select location</option>
            @foreach($locations as $location)
              <option value="{{ $location }}">{{ $location }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="mb-3 row mx-3">
        <label for="course" class="col-sm-2 col-form-label">Course <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <select id="course" class="form-select" disabled>
            <option value="" selected>Select course</option>
          </select>
        </div>
      </div>

      <div class="mb-3 row mx-3">
        <label for="intake" class="col-sm-2 col-form-label">Intake <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <select id="intake" class="form-select" disabled>
            <option value="" selected>Select intake</option>
          </select>
        </div>
      </div>

      <div class="mb-3 row mx-3">
        <label for="specialization" class="col-sm-2 col-form-label">Specialization <span class="text-danger">*</span></label>
        <div class="col-sm-10">
          <select id="specialization" class="form-select" disabled>
            <option value="" selected>Select specialization</option>
          </select>
        </div>
      </div>

      <div id="studentArea" class="mt-4 d-none">
        <div class="d-flex justify-content-between align-items-center mb-2 mx-3">
          <strong id="count">0 eligible students</strong>
          <button id="save" class="btn btn-primary">Register Selected Students</button>
        </div>

        <div class="table-responsive mx-3">
          <table class="table table-bordered table-striped">
            <thead class="table-light">
              <tr>
                <th><input id="selectAll" type="checkbox"></th>
                <th>Course Registration ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>NIC</th>
                <th>Current Specialization</th>
              </tr>
            </thead>
            <tbody id="students"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', () => {
  const location = document.getElementById('location');
  const course = document.getElementById('course');
  const intake = document.getElementById('intake');
  const specialization = document.getElementById('specialization');
  const studentsBody = document.getElementById('students');
  const studentArea = document.getElementById('studentArea');
  const count = document.getElementById('count');
  const statusMessage = document.getElementById('statusMessage');
  const selectAll = document.getElementById('selectAll');

  let courseOptions = [];

  function showMessage(type, message) {
    if (!message) {
      statusMessage.innerHTML = '';
      return;
    }

    statusMessage.innerHTML = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
  }

  function resetSelect(el, placeholder) {
    el.innerHTML = `<option value="" selected>${placeholder}</option>`;
    el.disabled = true;
  }

  function clearStudentTable() {
    studentsBody.innerHTML = '';
    count.textContent = '0 eligible students';
    studentArea.classList.add('d-none');
    selectAll.checked = false;
  }

  async function postJson(url, data) {
    const response = await fetch(url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'X-CSRF-TOKEN': '{{ csrf_token() }}'
      },
      body: JSON.stringify(data)
    });

    const payload = await response.json().catch(() => ({}));

    if (!response.ok || payload.success === false) {
      throw new Error(payload.message || 'Request failed.');
    }

    return payload;
  }

  function extractSpecializations(courseId) {
    let specs = courseOptions.find(item => String(item.course_id) === String(courseId))?.specializations ?? [];

    for (let i = 0; i < 2 && typeof specs === 'string'; i++) {
      try {
        specs = JSON.parse(specs);
      } catch (_error) {
        specs = [];
        break;
      }
    }

    return Array.isArray(specs) ? specs.filter(spec => spec && String(spec).trim() !== '') : [];
  }

  async function loadStudents(preserveMessage = false) {
    clearStudentTable();

    if (!location.value || !course.value || !intake.value || !specialization.value) {
      return;
    }

    try {
      const data = await postJson('/specialization-registration/students', {
        location: location.value,
        course_id: course.value,
        intake_id: intake.value
      });

      const students = Array.isArray(data.students) ? data.students : [];

      studentsBody.innerHTML = students.map(student => `
        <tr>
          <td><input class="student-check" type="checkbox" value="${student.student_id}" ${student.specialization === specialization.value ? 'checked' : ''}></td>
          <td>${student.course_registration_id || '-'}</td>
          <td>${student.name || ''}</td>
          <td>${student.email || ''}</td>
          <td>${student.nic || '-'}</td>
          <td>${student.specialization || '-'}</td>
        </tr>
      `).join('');

      count.textContent = `${students.length} eligible students`;
      studentArea.classList.remove('d-none');
      if (!preserveMessage) {
        showMessage('', '');
      }
    } catch (error) {
      showMessage('danger', error.message);
    }
  }

  location.addEventListener('change', async () => {
    resetSelect(course, 'Select course');
    resetSelect(intake, 'Select intake');
    resetSelect(specialization, 'Select specialization');
    clearStudentTable();

    if (!location.value) {
      return;
    }

    try {
      const data = await postJson('/specialization-registration/courses', { location: location.value });
      courseOptions = Array.isArray(data.courses) ? data.courses : [];
      course.disabled = false;

      courseOptions.forEach(item => {
        course.add(new Option(item.course_name, item.course_id));
      });

      showMessage('', '');
    } catch (error) {
      showMessage('danger', error.message);
    }
  });

  course.addEventListener('change', async () => {
    resetSelect(intake, 'Select intake');
    resetSelect(specialization, 'Select specialization');
    clearStudentTable();

    if (!course.value || !location.value) {
      return;
    }

    try {
      const data = await postJson('/specialization-registration/intakes', {
        location: location.value,
        course_id: course.value
      });

      const intakes = Array.isArray(data.intakes) ? data.intakes : [];
      intake.disabled = false;
      intakes.forEach(item => intake.add(new Option(item.batch, item.intake_id)));

      const specs = extractSpecializations(course.value);
      if (specs.length > 0) {
        specialization.disabled = false;
        specs.forEach(item => specialization.add(new Option(item, item)));
      }

      showMessage('', '');
    } catch (error) {
      showMessage('danger', error.message);
    }
  });

  intake.addEventListener('change', loadStudents);
  specialization.addEventListener('change', loadStudents);

  selectAll.addEventListener('change', (event) => {
    document.querySelectorAll('.student-check').forEach(input => {
      input.checked = event.target.checked;
    });
  });

  document.getElementById('save').addEventListener('click', async () => {
    const studentIds = Array.from(document.querySelectorAll('.student-check:checked')).map(input => input.value);

    if (studentIds.length === 0) {
      showMessage('warning', 'Select at least one student.');
      return;
    }

    try {
      const data = await postJson('/specialization-registration/store', {
        location: location.value,
        course_id: course.value,
        intake_id: intake.value,
        specialization: specialization.value,
        student_ids: studentIds
      });

      await loadStudents(true);
      showMessage('success', data.message || 'Saved successfully.');
    } catch (error) {
      showMessage('danger', error.message);
    }
  });
});
</script>
@endpush
@endsection
