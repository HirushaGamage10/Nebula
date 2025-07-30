@extends('inc.app')

@section('title', 'NEBULA | Eligibility & Registration')
 
@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Eligibility & Registration</h2>
            <hr>
            <form id="eligibilityForm">
                <h5 class="mb-3">Eligibility Search</h5>
                <div class="row mb-3">
                    <label for="nic" class="col-sm-2 col-form-label">NIC<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nic" name="nic" placeholder="Enter NIC or search...">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="course" class="col-sm-2 col-form-label">Course<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select filter-param" id="course" name="course" disabled>
                            <option selected disabled value="">Select a course</option>
                        </select>
                    </div>
                </div>
                <hr class="my-4">
            </form>
            <div class="mt-4" id="resultsTableSection" style="display:none;">
                <h5 class="mb-3">Eligibility Results</h5>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Registration Number</th>
                                <th>Student Name</th>
                                <th>Approval Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="resultsTableBody">
                            <!-- Rows will be added here dynamically -->
                        </tbody>
                    </table>
                </div>
                <hr class="my-4">
            </div>
            <div class="mt-4" id="studentDetailsSection" style="display:none;">
                <h5 class="mb-3">Student Details</h5>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body bg-light">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Full Name</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="studentFullNameInput" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">NIC</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="studentNICInput" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h5 class="mb-3">O/L Exam Details</h5>
                <div class="card mb-4 shadow-sm" id="olExamCard" style="display:none;">
                    <div class="card-body bg-info-subtle">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Exam Type</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="olExamTypeInput" readonly>
                            </div>
                            <label class="col-sm-2 col-form-label text-end">Exam Year</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="olExamYearInput" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Subjects & Results</label>
                            <div class="col-sm-10">
                                <table class="table table-bordered mt-3 bg-white border">
                                    <thead class="table-light"><tr><th>Subject</th><th>Result</th></tr></thead>
                                    <tbody id="olSubjectsTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h5 class="mb-3">A/L Exam Details</h5>
                <div class="card mb-4 shadow-sm" id="alExamCard" style="display:none;">
                    <div class="card-body bg-success-subtle">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Exam Type</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="alExamTypeInput" readonly>
                            </div>
                            <label class="col-sm-2 col-form-label text-end">Exam Year</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="alExamYearInput" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Stream</label>
                            <div class="col-sm-4">
                                <input type="text" class="form-control" id="alExamStreamInput" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Subjects & Results</label>
                            <div class="col-sm-10">
                                <table class="table table-bordered mt-3 bg-white border">
                                    <thead class="table-light"><tr><th>Subject</th><th>Result</th></tr></thead>
                                    <tbody id="alSubjectsTableBody"></tbody>
                                </table>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Remarks</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="alRemarksInput" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <hr class="my-4">
                <h5 class="mb-3">Course & Entry Qualifications</h5>
                <div class="card mb-4 shadow-sm">
                    <div class="card-body bg-light">
                        <div id="courseEntryFields" style="display:none;">
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Course</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="selectedCourseNameInput" readonly>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label class="col-sm-2 col-form-label">Entry Qualifications</label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control" id="entryQualificationsInput" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr class="my-4">
            <div class="mb-4" id="eligibilityActionButtons" style="display:none;">
                <button class="btn btn-success me-2" id="eligibleBtn">Eligible</button>
                <button class="btn btn-danger" id="notEligibleBtn">Not Eligible</button>
            </div>
            <div class="mb-4" id="notEligibleOptions" style="display:none;">
                <button class="btn btn-outline-primary me-2" id="registerAnotherCourseBtn">Register for Another Course</button>
                <button class="btn btn-outline-warning" id="specialApprovalBtn">Special Approval</button>
            </div>
            <div id="registerSection" class="card mb-4 shadow-sm" style="display:none;">
                <div class="card-body bg-light">
                    <h5 class="mb-3 text-center">Student Register For Course</h5>
                    <form id="registerForm">
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Student NIC</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inlineStudentNIC" name="nic" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Student Registration Number</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inlineStudentRegNo" name="registration_number" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Intake</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="intake" name="intake" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label class="col-sm-4 col-form-label">Course Registration ID</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" id="inlineCourseRegId" name="course_registration_id" readonly>
                            </div>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary px-5 w-100">Register</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const nicInput = document.getElementById('nic');
    const courseSelect = document.getElementById('course');
    const resultsTableSection = document.getElementById('resultsTableSection');
    const resultsTableBody = document.getElementById('resultsTableBody');

    // Auto-fill NIC and course from URL parameters if present
    const urlParams = new URLSearchParams(window.location.search);
    const nicFromUrl = urlParams.get('nic');
    const courseIdFromUrl = urlParams.get('course_id');
    const courseNameFromUrl = urlParams.get('course_name');
    
    console.log('URL Parameters:', {
        nic: nicFromUrl,
        course_id: courseIdFromUrl,
        course_name: courseNameFromUrl
    });
    
    if (nicFromUrl) {
        console.log('Setting NIC input to:', nicFromUrl);
        nicInput.value = nicFromUrl;
        
        // Trigger the change event to fetch registered courses - using multiple methods
        console.log('Triggering NIC change event...');
        nicInput.dispatchEvent(new Event('change', { bubbles: true }));
        nicInput.dispatchEvent(new Event('input', { bubbles: true }));
        
        // Also try triggering the event handler directly
        setTimeout(() => {
            console.log('Manually triggering NIC change handler...');
            const event = new Event('change', { bubbles: true });
            nicInput.dispatchEvent(event);
        }, 100);
        
        // If the event doesn't trigger, manually fetch courses after a delay
        setTimeout(() => {
            console.log('Manual fallback: fetching courses for NIC:', nicFromUrl);
            const nic = nicFromUrl.trim();
            if (nic) {
                fetch(`/get-registered-courses-by-nic?nic=${encodeURIComponent(nic)}`)
                    .then(response => {
                        console.log('Manual fetch response status:', response.status);
                        return response.json();
                    })
                    .then(data => {
                        console.log('Manual fetch courses data:', data);
                        if (data.success && data.courses) {
                            courseSelect.innerHTML = `<option selected disabled value="">Select a course</option>`;
                            data.courses.forEach(course => {
                                courseSelect.add(new Option(course.course_name, course.course_id));
                            });
                            courseSelect.disabled = false;
                            console.log('Manual fetch: Courses loaded, total options:', courseSelect.options.length);
                            
                            // Check if there's a pending course selection
                            if (window.pendingCourseSelection) {
                                console.log('Manual fetch: Attempting to select course:', window.pendingCourseSelection);
                                for (let i = 0; i < courseSelect.options.length; i++) {
                                    if (courseSelect.options[i].value === window.pendingCourseSelection) {
                                        console.log('Manual fetch: Found matching course, selecting index:', i);
                                        courseSelect.selectedIndex = i;
                                        courseSelect.dispatchEvent(new Event('change'));
                                        window.pendingCourseSelection = null;
                                        break;
                                    }
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Manual fetch error:', error);
                    });
            }
        }, 500);
        
        // If course parameters are also present, we'll handle course selection after courses are loaded
        if (courseIdFromUrl && courseNameFromUrl) {
            console.log('Storing pending course selection:', courseIdFromUrl);
            // Store the course ID to select after courses are loaded
            window.pendingCourseSelection = courseIdFromUrl;
        }
    }

    function resetAndDisable(select, placeholder) {
        select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
        select.disabled = true;
    }

    resetAndDisable(courseSelect, 'Select a course');

    // When NIC is entered/changed, fetch registered courses for that NIC
    nicInput.addEventListener('change', function() {
        console.log('NIC change event triggered with value:', nicInput.value);
        resetAndDisable(courseSelect, 'Select a course');
        resultsTableSection.style.display = 'none';
        const nic = nicInput.value.trim();
        if (nic) {
            console.log('Fetching courses for NIC:', nic);
            fetch(`/get-registered-courses-by-nic?nic=${encodeURIComponent(nic)}`)
                .then(response => {
                    console.log('Response status:', response.status);
                    return response.json();
                })
                .then(data => {
                    console.log('Courses data received:', data);
                    if (data.success && data.courses) {
                        courseSelect.innerHTML = `<option selected disabled value="">Select a course</option>`;
                        data.courses.forEach(course => {
                            courseSelect.add(new Option(course.course_name, course.course_id));
                        });
                        courseSelect.disabled = false;
                        console.log('Courses loaded, total options:', courseSelect.options.length);
                        
                        // Check if there's a pending course selection from URL parameters
                        if (window.pendingCourseSelection) {
                            console.log('Attempting to select course:', window.pendingCourseSelection);
                            console.log('Available courses:', Array.from(courseSelect.options).map(opt => ({value: opt.value, text: opt.text})));
                            // Find and select the course
                            for (let i = 0; i < courseSelect.options.length; i++) {
                                if (courseSelect.options[i].value === window.pendingCourseSelection) {
                                    console.log('Found matching course, selecting index:', i);
                                    courseSelect.selectedIndex = i;
                                    courseSelect.dispatchEvent(new Event('change'));
                                    // Clear the pending selection
                                    window.pendingCourseSelection = null;
                                    break;
                                }
                            }
                        }
                    } else {
                        console.log('No courses found or error in response:', data);
                    }
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                });
        } else {
            console.log('NIC is empty, not fetching courses');
        }
    });

    courseSelect.addEventListener('change', function() {
        resultsTableSection.style.display = 'none';
        document.getElementById('studentDetailsSection').style.display = 'none';
        const nic = nicInput.value.trim();
        const courseId = courseSelect.value;
        if (nic && courseId) {
            fetch('/get-eligible-students-by-nic', {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                body: JSON.stringify({ nic: nic, course_id: courseId })
            })
            .then(response => response.json())
            .then(data => {
                // Fetch and display student details
                fetch('/get-student-exam-details-by-nic-course', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
                    body: JSON.stringify({ nic: nic, course_id: courseId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.student) {
                        document.getElementById('studentFullNameInput').value = data.student.full_name;
                        document.getElementById('studentNICInput').value = data.student.nic;
                        document.getElementById('studentDetailsSection').style.display = '';
                        document.getElementById('eligibilityActionButtons').style.display = '';
                        // O/L
                        if (data.student.ol) {
                            document.getElementById('olExamTypeInput').value = data.student.ol.type || '';
                            document.getElementById('olExamYearInput').value = data.student.ol.year || '';
                            let olBody = '';
                            (data.student.ol.subjects || []).forEach(sub => {
                                olBody += `<tr><td>${sub.subject}</td><td>${sub.result}</td></tr>`;
                            });
                            document.getElementById('olSubjectsTableBody').innerHTML = olBody;
                            document.getElementById('olExamCard').style.display = '';
                        } else {
                            document.getElementById('olExamCard').style.display = 'none';
                        }
                        // A/L
                        if (data.student.al) {
                            document.getElementById('alExamTypeInput').value = data.student.al.type || '';
                            document.getElementById('alExamYearInput').value = data.student.al.year || '';
                            document.getElementById('alExamStreamInput').value = data.student.al.stream || '';
                            let alBody = '';
                            (data.student.al.subjects || []).forEach(sub => {
                                alBody += `<tr><td>${sub.subject}</td><td>${sub.result}</td></tr>`;
                            });
                            document.getElementById('alSubjectsTableBody').innerHTML = alBody;
                            document.getElementById('alRemarksInput').value = data.student.al.remarks || '';
                            document.getElementById('alExamCard').style.display = '';
                        } else {
                            document.getElementById('alExamCard').style.display = 'none';
                        }
                        // Show course and entry qualifications
                        if (courseId) {
                            fetch(`/get-course-entry-qualification?course_id=${courseId}`)
                                .then(response => response.json())
                                .then(courseData => {
                                    if (courseData.success && courseData.course) {
                                        document.getElementById('selectedCourseNameInput').value = courseData.course.course_name;
                                        document.getElementById('entryQualificationsInput').value = courseData.course.entry_qualification || 'N/A';
                                        document.getElementById('courseEntryFields').style.display = '';
                                    } else {
                                        document.getElementById('selectedCourseNameInput').value = '';
                                        document.getElementById('entryQualificationsInput').value = 'N/A';
                                        document.getElementById('courseEntryFields').style.display = '';
                                    }
                                });
                        }
                        // --- FIX: Set lastEligibleStudent for use in register section ---
                        window.lastEligibleStudent = data.student;
                        window.lastEligibleStudent.course_id = courseId;
                        // Set the Course Registration ID field in the register section
                        document.getElementById('inlineCourseRegId').value = data.student.course_registration_id || '';
                    } else {
                        document.getElementById('studentDetailsSection').style.display = 'none';
                        document.getElementById('eligibilityActionButtons').style.display = 'none';
                        document.getElementById('courseEntryFields').style.display = 'none';
                    }
                });
            });
        }
    });

    resultsTableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('approve-btn')) {
            // Approve button logic
        }
    });

    document.getElementById('notEligibleBtn').addEventListener('click', function() {
        document.getElementById('notEligibleOptions').style.display = '';
    });
    // --- Populate Intake Dropdown on Eligible ---
    document.getElementById('eligibleBtn').addEventListener('click', function() {
        document.getElementById('notEligibleOptions').style.display = 'none';
        // Fill inline form fields with student details
        document.getElementById('inlineStudentNIC').value = document.getElementById('studentNICInput').value;
        // FIX: Set registration number to actual value if available
        if (window.lastEligibleStudent && window.lastEligibleStudent.registration_number) {
            document.getElementById('inlineStudentRegNo').value = window.lastEligibleStudent.registration_number;
        } else {
            document.getElementById('inlineStudentRegNo').value = '';
        }
        document.getElementById('registerSection').style.display = '';

        // --- Fetch and populate Intake dropdown ---
        const courseId = document.getElementById('course').value;
        let location = null;
        const locationInput = document.getElementById('location');
        if (locationInput) {
            location = locationInput.value;
        } else if (window.userLocation) {
            location = window.userLocation;
        }
        const intakeInput = document.getElementById('intake');
        if (courseId && location) {
            fetch(`/get-intakes/${courseId}/${location}`)
                .then(response => response.json())
                .then(data => {
                    if (data.intakes && data.intakes.length > 0) {
                        intakeInput.value = data.intakes[0].batch; // Assuming the first intake is the default
                        intakeInput.readOnly = true;
                    } else {
                        intakeInput.value = '';
                        intakeInput.readOnly = true;
                    }
                });
        } else {
            intakeInput.value = '';
            intakeInput.readOnly = true;
        }
    });

    document.getElementById('registerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        const nic = document.getElementById('inlineStudentNIC').value;
        const courseId = document.getElementById('course').value;
        
        // Show loading spinner or disable button
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Registering...';

        fetch('/register-eligible-student', {
            method: 'POST',
            headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}'},
            body: JSON.stringify({ nic: nic, course_id: courseId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Use the globally available showToast function
                showToast('Student registered successfully! Page will refresh.', 'success');
                
                // Refresh the page after a delay
                setTimeout(() => {
                    window.location.reload();
                }, 2000);

            } else {
                showToast(data.message || 'Registration failed. Please try again.', 'danger');
                // Re-enable the button if registration fails
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Register';
            }
        })
        .catch(error => {
            console.error('Registration error:', error);
            showToast('An unexpected error occurred. Please check the console.', 'danger');
            // Re-enable the button on error
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Register';
        });
    });

    document.getElementById('registerAnotherCourseBtn').addEventListener('click', function() {
        window.location.href = '{{ route('course.registration') }}';
    });
    document.getElementById('specialApprovalBtn').addEventListener('click', function() {
        const nic = document.getElementById('studentNICInput').value;
        const courseId = document.getElementById('course').value;
        
        if (!nic || !courseId) {
            if (typeof showToast === 'function') {
                showToast('Please enter NIC and select a course first', 'warning');
            }
            return;
        }
        
        // Populate modal fields
        document.getElementById('modalStudentNIC').value = nic;
        document.getElementById('modalCourseName').value = document.getElementById('selectedCourseNameInput').value;
        
        // Show Bootstrap modal
        const modalElement = document.getElementById('specialApprovalModal');
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            const modal = new bootstrap.Modal(modalElement);
            modal.show();
        } else {
            // Fallback for when Bootstrap is not available
            modalElement.style.display = 'block';
            modalElement.classList.add('show');
            document.body.classList.add('modal-open');
        }
    });

    // Handler: Set registration number and populate intake when showing register section
    window.showRegisterSection = function(student) {
        document.getElementById('inlineStudentRegNo').value = student.student_id || '';
        // Set intake field to intake batch and make it readonly
        const intakeInput = document.getElementById('intake');
        if (student.intake_batch) {
            intakeInput.value = student.intake_batch;
        } else {
            intakeInput.value = '';
        }
        intakeInput.readOnly = true;
        // Set course registration id field (fetch next available from backend)
        const courseRegIdInput = document.getElementById('inlineCourseRegId');
        console.log('DEBUG: intake_id for next course reg id:', student.intake_id);
        if (student.intake_id) {
            fetch(`/get-next-course-registration-id?intake_id=${student.intake_id}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.next_id) {
                        courseRegIdInput.value = data.next_id;
                    } else {
                        courseRegIdInput.value = '';
                    }
                    courseRegIdInput.readOnly = true;
                });
        } else {
            courseRegIdInput.value = '';
            courseRegIdInput.readOnly = true;
        }
        document.getElementById('registerSection').style.display = '';
    };

    // Call showRegisterSection when Eligible button is clicked and student data is available
    const eligibleBtn = document.getElementById('eligibleBtn');
    if (eligibleBtn) {
        eligibleBtn.addEventListener('click', function() {
            if (window.lastEligibleStudent) {
                showRegisterSection(window.lastEligibleStudent);
            }
        });
    }

    // Populate intake dropdown function (should already exist, but ensure it's here)
    window.populateIntakeDropdown = function(courseId) {
        const intakeSelect = document.getElementById('intake');
        intakeSelect.innerHTML = '<option value="" selected disabled>Select Intake</option>';
        if (!courseId) return;
        fetch(`/get-intakes/${courseId}`)
            .then(response => response.json())
            .then(data => {
                if (data.intakes) {
                    data.intakes.forEach(intake => {
                        const option = document.createElement('option');
                        option.value = intake.id;
                        option.text = intake.name;
                        intakeSelect.appendChild(option);
                    });
                }
            });
    };

    // Handle Special Approval Form Submission
    document.getElementById('submitSpecialApprovalBtn').addEventListener('click', function() {
        const form = document.getElementById('specialApprovalForm');
        const formData = new FormData(form);
        const nic = document.getElementById('modalStudentNIC').value;
        const courseId = document.getElementById('course').value; // Assuming course is selected in the main form

        if (!nic || !courseId) {
            showToast('Please select a course first.', 'warning');
            return;
        }

        if (!formData.get('special_approval_document')) {
            showToast('Please select a document to upload.', 'warning');
            return;
        }

        // Add the missing fields to FormData
        formData.append('nic', nic);
        formData.append('course_id', courseId);
        
        // Debug: Log the FormData contents
        console.log('FormData contents:');
        for (let pair of formData.entries()) {
            console.log(pair[0] + ': ' + pair[1]);
        }

        // Test the route first
        console.log('Testing route access...');
        fetch('/test-special-approval', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({test: 'data'})
        })
        .then(response => {
            console.log('Test route response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Test route response:', data);
        })
        .catch(error => {
            console.error('Test route error:', error);
        });

        // Show loading spinner or disable button
        const submitBtn = this;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...';

        console.log('CSRF Token:', '{{ csrf_token() }}');
        fetch('/send-special-approval-request', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                // Don't set Content-Type for FormData - browser will set it automatically
            },
            body: formData
        })
        .then(response => {
            console.log('Response status:', response.status);
            console.log('Response headers:', response.headers);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showToast('Special approval request has been sent successfully!', 'success');
                document.getElementById('specialApprovalModal').style.display = 'none'; // Hide modal
                document.getElementById('eligibilityActionButtons').style.display = '';
                document.getElementById('notEligibleOptions').style.display = 'none';
                setTimeout(() => {
                    window.location.reload();
                }, 2000);
            } else {
                showToast(data.message || 'Failed to send special approval request', 'danger');
                // Re-enable the button if registration fails
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Request';
            }
        })
        .catch(error => {
            console.error('Special Approval Error:', error);
            console.error('Error details:', {
                name: error.name,
                message: error.message,
                stack: error.stack
            });
            showToast('An unexpected error occurred. Please check the console.', 'danger');
            // Re-enable the button on error
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Submit Request';
        });
    });

    // Handle modal closing
    document.getElementById('specialApprovalModal').addEventListener('hidden.bs.modal', function () {
        // Reset form when modal is closed
        document.getElementById('specialApprovalForm').reset();
        const submitBtn = document.getElementById('submitSpecialApprovalBtn');
        submitBtn.disabled = false;
        submitBtn.innerHTML = 'Submit Request';
    });
    
    // Handle close button clicks for fallback
    document.querySelectorAll('[data-bs-dismiss="modal"]').forEach(button => {
        button.addEventListener('click', function() {
            const modalElement = document.getElementById('specialApprovalModal');
            if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                const modal = bootstrap.Modal.getInstance(modalElement);
                if (modal) {
                    modal.hide();
                }
            } else {
                // Fallback close
                modalElement.style.display = 'none';
                modalElement.classList.remove('show');
                document.body.classList.remove('modal-open');
                // Reset form
                document.getElementById('specialApprovalForm').reset();
                const submitBtn = document.getElementById('submitSpecialApprovalBtn');
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Submit Request';
            }
        });
    });
});

window.showToast = function(message, type = 'success') {
    const toastEl = document.getElementById('mainToast');
    const toastBody = document.getElementById('mainToastBody');
    toastBody.textContent = message;

    // Set color based on type
    toastEl.className = 'toast align-items-center border-0 text-bg-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary')));

    // Show toast using Bootstrap's JS API
    const toast = new bootstrap.Toast(toastEl, { delay: 2000 });
    toast.show();
};
</script>
@endpush

<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                <!-- Message will go here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>

<!-- Special Approval Document Upload Modal -->
<div class="modal fade" id="specialApprovalModal" tabindex="-1" aria-labelledby="specialApprovalModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="specialApprovalModalLabel">Special Approval Request</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="specialApprovalForm">
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Student NIC</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="modalStudentNIC" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Course</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="modalCourseName" readonly>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Upload Document<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="file" class="form-control" id="specialApprovalDocument" name="special_approval_document" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Allowed formats: PDF, DOC, DOCX, JPG, JPEG, PNG (Max size: 5MB)</small>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <label class="col-sm-3 col-form-label fw-bold">Remarks</label>
                        <div class="col-sm-9">
                            <textarea class="form-control" id="specialApprovalRemarks" name="remarks" rows="3" placeholder="Please provide any additional remarks for the special approval request..."></textarea>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitSpecialApprovalBtn">Submit Request</button>
            </div>
        </div>
    </div>
</div>

@endsection
