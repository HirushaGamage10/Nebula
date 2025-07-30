@extends('inc.app')

@section('title', 'NEBULA | Semester Registration')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Semester Registration Management</h2>
            <hr>
            <form id="courseForm" method="POST" action="{{ route('semester.registration.store') }}">
                @csrf
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
                    <div class="col-sm-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="students_table">
                                <thead>
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>NIC</th>
                                        <th>Register</th>
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
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="toast-header bg-success text-white">
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
        </div>
        <div class="toast-body">
            {{ session('success') }}
        </div>
    </div>
</div>
@endif
<script>
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const toasts = document.querySelectorAll('.toast');
        toasts.forEach(toast => {
            const bsToast = new bootstrap.Toast(toast);
            bsToast.hide();
        });
    }, 3000);

    const locationSelect = document.getElementById('location');
    const courseSelect = document.getElementById('course_id');
    const intakeSelect = document.getElementById('intake_id');
    const semesterSelect = document.getElementById('semester_id');
    const studentsTableBody = document.querySelector('#students_table tbody');

    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option value="" selected disabled>${placeholder}</option>`;
        select.disabled = true;
    }
    
    function resetSpecialization() {
        document.getElementById('specialization').innerHTML = '<option value="">Select Specialization</option>';
        document.getElementById('specialization_row').style.display = 'none';
        document.getElementById('specialization_hidden').value = '';
    }
    function enableSelect(select) {
        select.disabled = false;
    }

    locationSelect.addEventListener('change', function() {
        resetAndDisable(courseSelect, 'Select Course');
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';
        resetSpecialization();
        
        // Update hidden location field
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

    courseSelect.addEventListener('change', function() {
        resetAndDisable(intakeSelect, 'Select Intake');
        resetAndDisable(semesterSelect, 'Select Semester');
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';
        document.getElementById('specialization_row').style.display = 'none';
        
        if (courseSelect.value && locationSelect.value) {
            // First, check if the course has specializations
            fetch(`/api/courses/${courseSelect.value}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.course && data.course.specializations) {
                        let specializations = [];
                        if (typeof data.course.specializations === 'string') {
                            try {
                                specializations = JSON.parse(data.course.specializations);
                            } catch (e) {
                                console.error('Error parsing specializations JSON:', e);
                                specializations = [];
                            }
                        } else if (Array.isArray(data.course.specializations)) {
                            specializations = data.course.specializations;
                        }
                        
                        // Filter out empty/null values
                        specializations = specializations.filter(spec => spec && spec.trim() !== '');
                        
                        if (specializations.length > 0) {
                            let options = '<option value="">Select Specialization</option>';
                            specializations.forEach(spec => {
                                options += `<option value="${spec}">${spec}</option>`;
                            });
                            document.getElementById('specialization').innerHTML = options;
                            document.getElementById('specialization_row').style.display = '';
                        } else {
                            document.getElementById('specialization_row').style.display = 'none';
                        }
                    } else {
                        document.getElementById('specialization_row').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching course details:', error);
                    document.getElementById('specialization_row').style.display = 'none';
                });
            
            // Then fetch intakes
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

    semesterSelect.addEventListener('change', function() {
        // Semester selection doesn't trigger table loading anymore
    });

    // Add event listener for specialization dropdown
    document.getElementById('specialization').addEventListener('change', function() {
        document.getElementById('specialization_hidden').value = this.value;
        
        // Load students table when specialization is selected
        studentsTableBody.innerHTML = '';
        document.getElementById('students_table_row').style.display = 'none';
        
        if (courseSelect.value && intakeSelect.value && semesterSelect.value && this.value) {
            fetch(`/semester-registration/get-eligible-students?course_id=${encodeURIComponent(courseSelect.value)}&intake_id=${encodeURIComponent(intakeSelect.value)}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.students.length > 0) {
                        let rows = '';
                        data.students.forEach(student => {
                            rows += `<tr>
                                <td>${student.student_id}</td>
                                <td>${student.name}</td>
                                <td>${student.email}</td>
                                <td>${student.nic}</td>
                                <td><input type="checkbox" name="register_students[]" value="${student.student_id}"></td>
                            </tr>`;
                        });
                        studentsTableBody.innerHTML = rows;
                        document.getElementById('students_table_row').style.display = '';
                    } else {
                        studentsTableBody.innerHTML = '<tr><td colspan="5" class="text-center">No eligible students found.</td></tr>';
                        document.getElementById('students_table_row').style.display = '';
                    }
                });
        }
    });

    // Handle form submission
    document.getElementById('courseForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Get selected students
        const selectedStudents = [];
        document.querySelectorAll('input[name="register_students[]"]:checked').forEach(checkbox => {
            selectedStudents.push(checkbox.value);
        });
        
        if (selectedStudents.length === 0) {
            alert('Please select at least one student to register.');
            return;
        }
        
        // Prepare form data
        const formData = new FormData();
        formData.append('course_id', courseSelect.value);
        formData.append('intake_id', intakeSelect.value);
        formData.append('semester_id', semesterSelect.value);
        formData.append('location', document.getElementById('location_hidden').value);
        formData.append('specialization', document.getElementById('specialization_hidden').value);
        formData.append('register_students', JSON.stringify(selectedStudents));
        formData.append('_token', document.querySelector('input[name="_token"]').value);
        
        // Debug: Log the form data being sent
        console.log('Form data being sent:');
        for (let [key, value] of formData.entries()) {
            console.log(key + ': ' + value);
        }
        
        // Show loading state
        const submitBtn = document.querySelector('button[type="submit"]');
        const originalText = submitBtn.textContent;
        submitBtn.disabled = true;
        submitBtn.textContent = 'Registering...';
        
        // Submit form via AJAX
        fetch('{{ route("semester.registration.store") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return response.json();
            } else {
                // If not JSON, throw an error with the response text
                return response.text().then(text => {
                    throw new Error('Server returned HTML instead of JSON. Response: ' + text.substring(0, 200));
                });
            }
        })
        .then(data => {
            if (data.success) {
                // Show success message
                showToast(data.message, 'success');
                
                // Reset form
                setTimeout(() => {
                    document.getElementById('courseForm').reset();
                    studentsTableBody.innerHTML = '';
                    document.getElementById('students_table_row').style.display = 'none';
                    resetSpecialization();
                    resetAndDisable(courseSelect, 'Select Course');
                    resetAndDisable(intakeSelect, 'Select Intake');
                    resetAndDisable(semesterSelect, 'Select Semester');
                }, 2000);
            } else {
                showToast(data.message || 'An error occurred.', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred while registering students. Please check the console for details.', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        });
    });

    function showToast(message, type) {
        // Remove existing toasts
        document.querySelectorAll('.toast').forEach(toast => toast.remove());
        
        const toastContainer = document.querySelector('.toast-container') || createToastContainer();
        
        const toastHtml = `
            <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="toast-header bg-${type === 'success' ? 'success' : 'danger'} text-white">
                    <strong class="me-auto">${type === 'success' ? 'Success' : 'Error'}</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        `;
        
        toastContainer.innerHTML = toastHtml;
        
        // Auto-hide after 5 seconds
        setTimeout(() => {
            const toast = toastContainer.querySelector('.toast');
            if (toast) {
                const bsToast = new bootstrap.Toast(toast);
                bsToast.hide();
            }
        }, 5000);
    }

    function createToastContainer() {
        const container = document.createElement('div');
        container.className = 'toast-container position-fixed bottom-0 end-0 p-3';
        document.body.appendChild(container);
        return container;
    }
});
</script>
@endsection 