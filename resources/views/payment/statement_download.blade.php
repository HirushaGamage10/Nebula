@extends('inc.app')

@section('content')
<div class="container mt-5">
    <div class="card shadow-lg border-0" style="background: #fff; border-radius: 12px;">
        <div class="card-body p-4">
            <h3 class="mb-3 text-primary">
                <i class="ti ti-file-download me-2"></i> Payment Statement
            </h3>
            <p class="text-muted mb-4">
                Enter the student NIC/ID and select a course to preview or download the payment statement.
            </p>

            <!-- NIC Input -->
            <div class="mb-4">
                <label for="statement-nic" class="form-label fw-bold">Student NIC / ID</label>
                <input type="text" id="statement-nic" class="form-control form-control-lg" 
                       placeholder="Enter NIC or Student ID" style="border-radius: 8px;">
            </div>

            <!-- Course Dropdown -->
            <div class="mb-4">
                <label for="statement-course" class="form-label fw-bold">Select Course</label>
                <select id="statement-course" class="form-select form-select-lg" style="border-radius: 8px;">
                    <option value="" disabled selected>Select a Course</option>
                </select>
            </div>

            <!-- Buttons Form -->
            <form id="statementDownloadForm" action="{{ route('payment.downloadStatement') }}" method="POST" target="_blank">
                @csrf
                <input type="hidden" name="student_nic" id="download-student-nic">
                <input type="hidden" name="course_id" id="download-course-id">
                <input type="hidden" name="action" id="download-action">

                <div class="d-flex gap-3">
                    <button type="submit" class="btn btn-lg btn-outline-primary px-4" onclick="setAction('preview')">
                        <i class="ti ti-eye me-1"></i> Preview PDF
                    </button>

                    <button type="submit" class="btn btn-lg btn-danger px-4" onclick="setAction('download')">
                        <i class="ti ti-download me-1"></i> Download PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Load courses when NIC is entered
    document.getElementById('statement-nic').addEventListener('change', function() {
        const studentNic = this.value;
        if (!studentNic) return;

        fetch('/payment/get-student-courses', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ student_nic: studentNic })
        })
        .then(response => response.json())
        .then(data => {
            const courseSelect = document.getElementById('statement-course');
            courseSelect.innerHTML = '<option value="" disabled selected>Select a Course</option>';

            if (data.success) {
                data.courses.forEach(course => {
                    const opt = document.createElement('option');
                    opt.value = course.course_id;
                    opt.textContent = course.course_name;
                    courseSelect.appendChild(opt);
                });
            } else {
                alert(data.message || 'No courses found');
            }
        })
        .catch(() => alert('Error loading courses.'));
    });

    // Before form submit → set hidden fields
    document.getElementById('statementDownloadForm').addEventListener('submit', function(e) {
        const nic = document.getElementById('statement-nic').value;
        const courseId = document.getElementById('statement-course').value;

        if (!nic || !courseId) {
            e.preventDefault();
            alert('Please enter NIC and select a course.');
            return;
        }

        document.getElementById('download-student-nic').value = nic;
        document.getElementById('download-course-id').value = courseId;
    });

    // Set action before submitting
    function setAction(action) {
        document.getElementById('download-action').value = action;
    }
</script>
@endsection
