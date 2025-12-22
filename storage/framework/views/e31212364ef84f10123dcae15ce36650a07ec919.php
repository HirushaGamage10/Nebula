

<?php $__env->startSection('title', 'NEBULA | Course Registration'); ?>

<?php $__env->startSection('content'); ?>
<style nonce="<?php echo e($cspNonce); ?>">
    /* Existing styles copied from root course_registration view */
    .terminated-disabled { opacity: 0.6; filter: grayscale(100%); pointer-events: none; }
    .terminated-overlay { position: absolute; top: 0; left: 0; right: 0; bottom: 0; z-index: 50; cursor: not-allowed; background: rgba(255,255,255,0); }
    .form-control:focus, .form-select:focus { border-color: #0d6efd; box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25); }
    .btn:hover { transform: translateY(-1px); box-shadow: 0 4px 8px rgba(0,0,0,0.15); transition: all 0.2s ease; }
    .table tbody tr:hover { background-color: #f8f9fa; }
    .alert { border-left: 4px solid; }
    .alert-danger { border-left-color: #dc3545; }
    .alert-success { border-left-color: #198754; }
    .alert-warning { border-left-color: #ffc107; }
    .card { box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    #spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 9999; display: flex; justify-content: center; align-items: center; }
    .lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
    .lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #fff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #fff transparent transparent transparent; }
    .lds-ring div:nth-child(1) { animation-delay: -0.45s; }
    .lds-ring div:nth-child(2) { animation-delay: -0.3s; }
    .lds-ring div:nth-child(3) { animation-delay: -0.15s; }
    @keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .is-invalid { border-color: #dc3545 !important; }
    .is-valid { border-color: #198754 !important; }
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Course Registration</h2>
            <hr>
            <div id="spinner-overlay" style="display:none;">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>
            <div class="accordion" id="searchAccordion">
                <div class="accordion-item">
                    <div class="accordion-body">
                        <form id="searchForm">
                            <?php echo csrf_field(); ?>
                            <div class="mb-3 row mx-3">
                                <label for="studentNicSearch" class="col-sm-2 col-form-label">Student NIC<span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control bg-white" id="studentNicSearch" name="studentNicSearch" placeholder="Enter Student ID (NIC)">
                                </div>
                                <div class="col-sm-2">
                                    <button type="button" class="btn btn-primary w-100" id="searchNicBtn">Search</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div id="searchMessageContainer" class="mx-3"></div>
            <div id="studentDetailsSection" style="display: none;">
                <div class="row mt-3">
                    <div class="col-md-6">
                        <div class="mb-3 row mx-3">
                            <label for="studentName" class="col-sm-3 col-form-label">Name</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-white" id="studentName" name="studentName" readonly>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="studentNIC" class="col-sm-3 col-form-label">NIC</label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control bg-white" id="studentNIC" name="studentNIC" readonly>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(isset($resultsPending) && $resultsPending): ?>
                    <div class="alert alert-warning mt-4"><strong>Pending Results:</strong> Some or all of the student's exam results are still pending.</div>
                <?php else: ?>
                    <div class="mb-3 mt-4">
                        <h5 class="bg-danger p-2 text-white"><strong>O/L Exam Details</strong></h5>
                        <div class="row mt-4 mb-4 mx-3">
                            <div class="mb-3 col-sm-6">
                                <label for="olExamType" class="form-label">Exam Type</label>
                                <input type="text" class="form-control bg-white" id="olExamType" name="olExamType" readonly>
                            </div>
                            <div class="mb-3 col-sm-6">
                                <label for="olExamYear" class="form-label">Exam Year</label>
                                <input type="text" class="form-control bg-white" id="olExamYear" name="olExamYear" readonly>
                            </div>
                        </div>
                        <h6 class="mb-4 mx-3">O/L Exam Subjects and Grades</h6>
                        <div class="col-11 mx-3 mb-4">
                            <table class="table table-bordered table-striped">
                                <thead class="table-light">
                                    <tr>
                                        <th class="bg-primary text-white" scope="col">Subject</th>
                                        <th class="bg-primary text-white" scope="col">Grade</th>
                                    </tr>
                                </thead>
                                <tbody id="olExamSubjectsAndGradesTableBody">
                                    <?php $__currentLoopData = $olSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <tr>
                                            <td><?php echo e($subject['subject'] ?? 'N/A'); ?></td>
                                            <td><?php echo e($subject['result'] ?? 'N/A'); ?></td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <h5 class="bg-danger p-2 text-white mx-3"><strong>A/L Exam Details</strong></h5>
                    <div class="row mt-4 mx-3">
                        <div class="mb-3 col-sm-6">
                            <label for="alExamType" class="col-form-label">Exam Type</label>
                            <input type="text" class="form-control bg-white" id="alExamType" name="alExamType" readonly>
                        </div>
                        <div class="mb-3 col-sm-6">
                            <label for="alExamYear" class="col-form-label">Exam Year</label>
                            <input type="text" class="form-control bg-white" id="alExamYear" name="alExamYear" readonly>
                        </div>
                    </div>
                    <div class="mb-4 row mx-3">
                        <label for="alExamStream" class="col-sm-2 col-form-label">Exam Stream</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control bg-white" id="alExamStream" name="alExamStream" readonly>
                        </div>
                    </div>
                    <h6 class="mb-4 mx-3">A/L Exam Subjects and Grades</h6>
                    <div class="col-11 mx-3 mb-4">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th class="bg-primary text-white" scope="col">Subject</th>
                                    <th class="bg-primary text-white" scope="col">Grade</th>
                                </tr>
                            </thead>
                            <tbody id="alExamSubjectsAndGradesTableBody">
                                <?php $__currentLoopData = $alSubjects; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <tr>
                                        <td><?php echo e($subject['subject'] ?? 'N/A'); ?></td>
                                        <td><?php echo e($subject['result'] ?? 'N/A'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
                <hr>
                <input type="hidden" id="studentId" name="studentId">
                <input type="hidden" id="studentRegistrationId" name="studentRegistrationId">
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location" required>
                            <option selected disabled value="">Choose a location...</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="courseSearch" class="col-sm-2 col-form-label">Course<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select bg-white" id="courseSearch" name="courseSearch" style="cursor: pointer;" required disabled>
                            <option selected disabled>Select a location first</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="intakeId" class="col-sm-2 col-form-label">Intake<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="intakeId" name="intakeId" required disabled>
                            <option value="" selected disabled>Select a course first</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="registrationFee" class="col-sm-2 col-form-label">Registration Fee<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <span class="input-group-text bg-primary text-white">LKR</span>
                            <input type="number" class="form-control bg-white" id="registrationFee" name="registrationFee" placeholder="Enter registration fee" required>
                        </div>
                    </div>
                </div>
                <hr class="mt-4">
                <fieldset class="mx-3 mt-4">
                    <legend class="mb-4" style="font-size: 20px;">Student Counsellor Details</legend>
                    <div class="row mx-3 align-items-center">
                        <label class="col-sm-3 col-form-label">SLT Employee</label>
                        <div class="col-sm-9 d-flex align-items-center">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="radio" name="slt_employee" id="sltYes" value="yes">
                                <label class="form-check-label" for="sltYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input cursor-pointer" type="radio" name="slt_employee" id="sltNo" value="no" checked>
                                <label class="form-check-label" for="sltNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div id="serviceNoField" style="display: none;">
                        <div class="mb-3 mt-3 row mx-3">
                            <label for="serviceNo" class="col-sm-3 col-form-label">Service No<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="serviceNo" name="service_no" placeholder="Enter service number" required>
                            </div>
                        </div>
                    </div>
                    <div id="externalCounselorFields" style="display: none;">
                        <div class="mb-3 mt-3 row mx-3">
                            <label for="counselorName" class="col-sm-3 col-form-label">Counselor Name<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="counselorName" name="counselor_name" placeholder="Enter counselor's name" required>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="counselorNic" class="col-sm-3 col-form-label">Counselor NIC<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="text" class="form-control" id="counselorNic" name="counselor_nic" placeholder="Enter counselor's NIC number" required>
                                <div class="invalid-feedback"><span class="text-danger">✖</span> Invalid NIC. Use 12 digits or 9 digits + 1 letter.</div>
                                <div class="valid-feedback"><span class="text-success">✔</span> Valid NIC.</div>
                            </div>
                        </div>
                        <div class="mb-3 row mx-3">
                            <label for="counselorPhone" class="col-sm-3 col-form-label">Counselor Phone<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="tel" class="form-control" id="counselorPhone" name="counselor_phone" placeholder="Enter counselor's phone number" required>
                                <div class="invalid-feedback"><span class="text-danger">✖</span> Invalid phone. Use "07x xxxxxxx" or "+94 xxxxxxxxx".</div>
                                <div class="valid-feedback"><span class="text-success">✔</span> Valid phone.</div>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <hr class="mt-4">
                <h4 class="mb-4 fw-bold">Course Details</h4>
                <div class="row align-items-center mx-3 mb-3">
                    <label for="courseStartDate" class="col-sm-2 col-form-label fw-bold">Start Date<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="courseStartDate" name="courseStartDate" placeholder="Select start date" style="cursor: pointer;" min="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                </div>
                <hr class="mt-4">
                <fieldset class="mx-3">
                    <legend class="mb-4" style="font-size: 20px;">Marketing Survey</legend>
                    <p class="mx-3"><strong>How did you hear about our institute?</strong></p>
                    <div class="mx-4">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="LinkedIn" id="checkboxLinkedIn">
                                    <label class="form-check-label" for="checkboxLinkedIn">LinkedIn</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Facebook" id="checkboxFacebook">
                                    <label class="form-check-label" for="checkboxFacebook">Facebook</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Radio Advertisement" id="checkboxRadio">
                                    <label class="form-check-label" for="checkboxRadio">Radio Advertisement</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="TV advertisement" id="checkboxTV">
                                    <label class="form-check-label" for="checkboxTV">TV advertisement</label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" value="Other" id="checkboxOther">
                                    <label class="form-check-label" for="checkboxOther">Other</label>
                                </div>
                            </div>
                        </div>
                        <div class="row mt-3" id="otherMarketingSurveyRow" style="display: none;">
                            <div class="col-md-12">
                                <input type="text" class="form-control" id="marketing_survey_other" name="marketing_survey_other" placeholder="Please describe how you heard about us">
                            </div>
                        </div>
                    </div>
                </fieldset>
                <div class="d-flex flex-column gap-3 mt-5">
                    <button id="finalRegister" type="submit" class="btn btn-primary w-100">Pre Register</button>
                    <button id="checkEligibility" type="button" class="btn btn-dark w-100" onclick="redirectToEligibility()">Check Eligibility --></button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script nonce="<?php echo e($cspNonce); ?>">
document.addEventListener('DOMContentLoaded', function () {
    const searchBtn = document.getElementById('searchNicBtn');
    const nicInput = document.getElementById('studentNicSearch');
    const studentDetailsSection = document.getElementById('studentDetailsSection');
    const searchMessageContainer = document.getElementById('searchMessageContainer');
    const spinnerOverlay = document.getElementById('spinner-overlay');
    const olTableBody = document.getElementById('olExamSubjectsAndGradesTableBody');
    const alTableBody = document.getElementById('alExamSubjectsAndGradesTableBody');
    const baseUrl = "<?php echo e(url('/api/course-registration/student-by-nic')); ?>";

    function setLoading(isLoading) {
        if (spinnerOverlay) {
            spinnerOverlay.style.display = isLoading ? 'flex' : 'none';
        }
    }

    function showMessage(type, message) {
        if (!searchMessageContainer) {
            return;
        }
        searchMessageContainer.innerHTML = '<div class="alert alert-' + type + '">' + message + '</div>';
    }

    function renderSubjects(tbody, subjects) {
        if (!tbody) {
            return;
        }
        tbody.innerHTML = '';
        if (!Array.isArray(subjects) || subjects.length == 0) {
            tbody.innerHTML = '<tr><td colspan="2">N/A</td></tr>';
            return;
        }
        subjects.forEach(function (subject) {
            const name = subject.subject || subject.name || subject.title || 'N/A';
            const result = subject.result || subject.grade || subject.mark || 'N/A';
            const row = document.createElement('tr');
            row.innerHTML = '<td>' + name + '</td><td>' + result + '</td>';
            tbody.appendChild(row);
        });
    }

    async function searchStudent() {
        const nic = nicInput ? nicInput.value.trim() : '';
        if (!nic) {
            showMessage('warning', 'Please enter a NIC.');
            if (studentDetailsSection) {
                studentDetailsSection.style.display = 'none';
            }
            return;
        }

        setLoading(true);
        showMessage('info', 'Searching student...');

        try {
            const response = await fetch(baseUrl + '/' + encodeURIComponent(nic), {
                headers: {
                    'Accept': 'application/json'
                }
            });

            const contentType = response.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') === -1) {
                const text = await response.text();
                throw new Error('Unexpected response: ' + text.substring(0, 200));
            }

            const data = await response.json();
            if (!response.ok || !data.success) {
                showMessage('danger', (data && data.message) ? data.message : 'Student not found.');
                if (studentDetailsSection) {
                    studentDetailsSection.style.display = 'none';
                }
                return;
            }

            const student = data.student || {};
            const olExam = Array.isArray(data.ol_exams) && data.ol_exams.length ? data.ol_exams[0] : null;
            const alExam = Array.isArray(data.al_exams) && data.al_exams.length ? data.al_exams[0] : null;

            const studentNameInput = document.getElementById('studentName');
            const studentNicInput = document.getElementById('studentNIC');
            const studentIdInput = document.getElementById('studentId');
            const studentRegIdInput = document.getElementById('studentRegistrationId');
            const olExamTypeInput = document.getElementById('olExamType');
            const olExamYearInput = document.getElementById('olExamYear');
            const alExamTypeInput = document.getElementById('alExamType');
            const alExamYearInput = document.getElementById('alExamYear');
            const alExamStreamInput = document.getElementById('alExamStream');

            if (studentNameInput) {
                studentNameInput.value = student.name_with_initials || '';
            }
            if (studentNicInput) {
                studentNicInput.value = student.id_value || nic;
            }
            if (studentIdInput) {
                studentIdInput.value = student.student_id || '';
            }
            if (studentRegIdInput) {
                studentRegIdInput.value = student.registration_id || student.student_id || '';
            }

            if (olExamTypeInput) {
                olExamTypeInput.value = olExam ? (olExam.exam_type && olExam.exam_type.exam_type ? olExam.exam_type.exam_type : (olExam.exam_type || '')) : '';
            }
            if (olExamYearInput) {
                olExamYearInput.value = olExam ? (olExam.exam_year || '') : '';
            }
            if (alExamTypeInput) {
                alExamTypeInput.value = alExam ? (alExam.exam_type && alExam.exam_type.exam_type ? alExam.exam_type.exam_type : (alExam.exam_type || '')) : '';
            }
            if (alExamYearInput) {
                alExamYearInput.value = alExam ? (alExam.exam_year || '') : '';
            }
            if (alExamStreamInput) {
                alExamStreamInput.value = alExam ? (alExam.stream && alExam.stream.stream ? alExam.stream.stream : (alExam.stream || '')) : '';
            }

            renderSubjects(olTableBody, olExam ? olExam.subjects : []);
            renderSubjects(alTableBody, alExam ? alExam.subjects : []);

            if (studentDetailsSection) {
                studentDetailsSection.style.display = 'block';
            }
            showMessage('success', 'Student found.');
        } catch (error) {
            console.error('NIC search failed:', error);
            showMessage('danger', 'Failed to fetch student details.');
            if (studentDetailsSection) {
                studentDetailsSection.style.display = 'none';
            }
        } finally {
            setLoading(false);
        }
    }

    if (searchBtn) {
        searchBtn.addEventListener('click', searchStudent);
    }

    if (nicInput) {
        nicInput.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                searchStudent();
            }
        });
    }

    // Handle location change to load courses
    const locationSelect = document.getElementById('location');
    const courseSelect = document.getElementById('courseSearch');
    const intakeSelect = document.getElementById('intakeId');

    if (locationSelect) {
        locationSelect.addEventListener('change', function() {
            const location = this.value;
            if (!location) {
                return;
            }

            // Fetch courses for selected location using course registration controller
            fetch('/course-registration/get-courses-by-location/' + encodeURIComponent(location))
                .then(response => response.json())
                .then(data => {
                    if (courseSelect) {
                        courseSelect.innerHTML = '<option selected disabled value="">Choose a course...</option>';
                        if (data.success && data.courses && data.courses.length > 0) {
                            data.courses.forEach(course => {
                                const option = document.createElement('option');
                                option.value = course.course_name;
                                option.setAttribute('data-course-id', course.course_id);
                                option.textContent = course.course_name;
                                courseSelect.appendChild(option);
                            });
                            courseSelect.disabled = false;
                        } else {
                            courseSelect.innerHTML = '<option selected disabled>No courses available</option>';
                            courseSelect.disabled = true;
                        }
                    }
                    // Reset intake dropdown
                    if (intakeSelect) {
                        intakeSelect.innerHTML = '<option value="" selected disabled>Select a course first</option>';
                        intakeSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching courses:', error);
                    if (courseSelect) {
                        courseSelect.innerHTML = '<option selected disabled>Error loading courses</option>';
                        courseSelect.disabled = true;
                    }
                });
        });
    }

    // Handle course change to load intakes
    if (courseSelect) {
        courseSelect.addEventListener('change', function() {
            const courseName = this.value;
            const location = locationSelect ? locationSelect.value : '';
            
            if (!courseName || !location) {
                return;
            }

            // Fetch intakes for selected course and location using course registration controller
            fetch('/course-registration/get-intakes/' + encodeURIComponent(courseName) + '/' + encodeURIComponent(location))
                .then(response => response.json())
                .then(data => {
                    if (intakeSelect) {
                        intakeSelect.innerHTML = '<option value="" selected disabled>Choose an intake...</option>';
                        if (data.success && data.intakes && data.intakes.length > 0) {
                            data.intakes.forEach(intake => {
                                const option = document.createElement('option');
                                option.value = intake.intake_id;
                                option.textContent = intake.batch;
                                option.setAttribute('data-start-date', intake.start_date);
                                option.setAttribute('data-registration-fee', intake.registration_fee);
                                intakeSelect.appendChild(option);
                            });
                            intakeSelect.disabled = false;
                        } else {
                            intakeSelect.innerHTML = '<option selected disabled>No intakes available</option>';
                            intakeSelect.disabled = true;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error fetching intakes:', error);
                    if (intakeSelect) {
                        intakeSelect.innerHTML = '<option selected disabled>Error loading intakes</option>';
                        intakeSelect.disabled = true;
                    }
                });
        });
    }

    // Handle intake change to auto-populate registration fee and start date
    if (intakeSelect) {
        intakeSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const registrationFeeInput = document.getElementById('registrationFee');
            const startDateInput = document.getElementById('courseStartDate');
            
            if (selectedOption && selectedOption.value) {
                const startDate = selectedOption.getAttribute('data-start-date');
                const registrationFee = selectedOption.getAttribute('data-registration-fee');
                
                if (registrationFeeInput && registrationFee) {
                    registrationFeeInput.value = registrationFee;
                    registrationFeeInput.readOnly = true;
                }
                
                if (startDateInput && startDate) {
                    // Convert ISO date to YYYY-MM-DD format
                    const dateObj = new Date(startDate);
                    const formattedDate = dateObj.toISOString().split('T')[0];
                    startDateInput.value = formattedDate;
                    startDateInput.readOnly = true;
                }
            }
        });
    }

    // Handle SLT Employee radio buttons
    const sltYesRadio = document.getElementById('sltYes');
    const sltNoRadio = document.getElementById('sltNo');
    const serviceNoField = document.getElementById('serviceNoField');
    const externalCounselorFields = document.getElementById('externalCounselorFields');
    const serviceNoInput = document.getElementById('serviceNo');
    const counselorNameInput = document.getElementById('counselorName');
    const counselorNicInput = document.getElementById('counselorNic');
    const counselorPhoneInput = document.getElementById('counselorPhone');

    function handleSltEmployeeChange() {
        if (sltYesRadio && sltYesRadio.checked) {
            if (serviceNoField) serviceNoField.style.display = 'block';
            if (externalCounselorFields) externalCounselorFields.style.display = 'none';
            if (serviceNoInput) serviceNoInput.required = true;
            if (counselorNameInput) counselorNameInput.required = false;
            if (counselorNicInput) counselorNicInput.required = false;
            if (counselorPhoneInput) counselorPhoneInput.required = false;
        } else if (sltNoRadio && sltNoRadio.checked) {
            if (serviceNoField) serviceNoField.style.display = 'none';
            if (externalCounselorFields) externalCounselorFields.style.display = 'block';
            if (serviceNoInput) serviceNoInput.required = false;
            if (counselorNameInput) counselorNameInput.required = true;
            if (counselorNicInput) counselorNicInput.required = true;
            if (counselorPhoneInput) counselorPhoneInput.required = true;
        }
    }

    if (sltYesRadio) {
        sltYesRadio.addEventListener('change', handleSltEmployeeChange);
    }
    if (sltNoRadio) {
        sltNoRadio.addEventListener('change', handleSltEmployeeChange);
    }

    // Initialize on page load (default is No)
    handleSltEmployeeChange();

    // Handle "Other" checkbox for marketing survey
    const checkboxOther = document.getElementById('checkboxOther');
    const otherMarketingSurveyRow = document.getElementById('otherMarketingSurveyRow');

    if (checkboxOther) {
        checkboxOther.addEventListener('change', function() {
            if (otherMarketingSurveyRow) {
                otherMarketingSurveyRow.style.display = this.checked ? 'block' : 'none';
            }
        });
    }

    // Handle Pre Register button
    const finalRegisterBtn = document.getElementById('finalRegister');
    if (finalRegisterBtn) {
        finalRegisterBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            // Validate required fields
            const studentId = document.getElementById('studentId')?.value;
            const location = document.getElementById('location')?.value;
            const courseSelect = document.getElementById('courseSearch');
            const courseName = courseSelect?.value;
            const courseId = courseSelect?.options[courseSelect.selectedIndex]?.getAttribute('data-course-id');
            const intakeId = document.getElementById('intakeId')?.value;
            const registrationFee = document.getElementById('registrationFee')?.value;
            const courseStartDate = document.getElementById('courseStartDate')?.value;
            
            if (!studentId) {
                showMessage('warning', 'Please search for a student first.');
                return;
            }
            
            if (!location || !courseName || !intakeId || !registrationFee || !courseStartDate) {
                showMessage('warning', 'Please fill in all required course fields.');
                return;
            }
            
            // Validate SLT Employee fields
            const sltEmployee = document.querySelector('input[name="slt_employee"]:checked')?.value;
            if (sltEmployee === 'yes') {
                const serviceNo = document.getElementById('serviceNo')?.value;
                if (!serviceNo) {
                    showMessage('warning', 'Please enter the service number.');
                    return;
                }
            } else if (sltEmployee === 'no') {
                const counselorName = document.getElementById('counselorName')?.value;
                const counselorNic = document.getElementById('counselorNic')?.value;
                const counselorPhone = document.getElementById('counselorPhone')?.value;
                
                if (!counselorName || !counselorNic || !counselorPhone) {
                    showMessage('warning', 'Please fill in all counselor details.');
                    return;
                }
            }
            
            // Collect marketing survey options
            const marketingOptions = [];
            document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
                if (checkbox.value && checkbox.id.startsWith('checkbox')) {
                    if (checkbox.id === 'checkboxOther') {
                        const otherValue = document.getElementById('marketing_survey_other')?.value;
                        if (otherValue) {
                            marketingOptions.push(otherValue);
                        }
                    } else {
                        marketingOptions.push(checkbox.value);
                    }
                }
            });
            
            // Prepare form data
            const formData = {
                studentId: studentId,
                course: courseId,
                location: location,
                sltEmployee: sltEmployee,
                serviceNo: document.getElementById('serviceNo')?.value || '',
                counselorName: document.getElementById('counselorName')?.value || '',
                counselorNic: document.getElementById('counselorNic')?.value || '',
                counselorPhone: document.getElementById('counselorPhone')?.value || '',
                options: marketingOptions.join(', '),
                surveyNo: marketingOptions.length,
                registrationFee: registrationFee,
                courseStartDate: courseStartDate,
                intakeId: intakeId
            };
            
            setLoading(true);
            showMessage('info', 'Submitting registration...');
            
            try {
                const response = await fetch('/store-course-registration', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || ''
                    },
                    body: JSON.stringify(formData)
                });
                
                const data = await response.json();
                
                if (data.success) {
                    showMessage('success', data.message || 'Registration completed successfully!');
                    // Optionally redirect after success
                    setTimeout(() => {
                        window.location.href = '/course-registration';
                    }, 2000);
                } else {
                    showMessage('danger', data.message || 'Registration failed. Please try again.');
                }
            } catch (error) {
                console.error('Registration error:', error);
                showMessage('danger', 'An error occurred while submitting the registration.');
            } finally {
                setLoading(false);
            }
        });
    }

    window.redirectToEligibility = function () {
        const nic = nicInput ? nicInput.value.trim() : '';
        const courseId = courseSelect && courseSelect.value ? courseSelect.value : '';
        const courseName = courseSelect && courseSelect.selectedIndex >= 0 ? courseSelect.options[courseSelect.selectedIndex].text : '';
        const params = new URLSearchParams();
        if (nic) {
            params.set('nic', nic);
        }
        if (courseId) {
            params.set('course_id', courseId);
        }
        if (courseName) {
            params.set('course_name', courseName);
        }
        const query = params.toString();
        window.location.href = '/eligibility-registration' + (query ? ('?' + query) : '');
    };
});
</script>
<?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/registration/course_registration.blade.php ENDPATH**/ ?>