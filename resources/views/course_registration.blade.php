@extends('inc.app')

@section('title', 'NEBULA | Course Registration')

@section('content')
<style>
    /* Dim and disable the student details when a student is terminated */
    .terminated-disabled {
        opacity: 0.6;
        filter: grayscale(100%);
        pointer-events: none;
    }
    /* Overlay to fully block interactions inside the details section */
    .terminated-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 50;
        cursor: not-allowed;
        background: rgba(255,255,255,0);
    }
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
                      @csrf
                      <div class="mb-3 row mx-3">
                        <label for="studentNicSearch" class="col-sm-2 col-form-label">Student NIC<span class="text-danger">*</span></label>
                        <div class="col-sm-8">
                          <input type="text" class="form-control bg-white" id="studentNicSearch" name="studentNicSearch" placeholder="Enter Student ID (NIC) ">
                        </div>
                        <div class="col-sm-2">
                          <button type="button" class="btn btn-primary w-100" id="searchNicBtn">Search</button>
                        </div>
                      </div>
                    </form>
                  </div>
                </div>
              </div>

              <!-- Message container: render search/termination banners here so they are always visible -->
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

                @if(isset($resultsPending) && $resultsPending)
                    <div class="alert alert-warning mt-4"><strong>Pending Results:</strong> Some or all of the student's exam results are still pending.</div>
                @else
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
                                    @foreach($olSubjects as $subject)
                                        <tr>
                                            <td>{{ $subject['subject'] ?? 'N/A' }}</td>
                                            <td>{{ $subject['result'] ?? 'N/A' }}</td>
                                        </tr>
                                    @endforeach
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
                                @foreach($alSubjects as $subject)
                                    <tr>
                                        <td>{{ $subject['subject'] ?? 'N/A' }}</td>
                                        <td>{{ $subject['result'] ?? 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
                
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
                        <input type="text" class="form-control" id="serviceNo" name="service_no" placeholder="Enter service number">
                      </div>
                    </div>
                  </div>
                  <div id="externalCounselorFields" style="display: none;">
                    <div class="mb-3 mt-3 row mx-3">
                        <label for="counselorName" class="col-sm-3 col-form-label">Counselor Name<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="counselorName" name="counselor_name" placeholder="Enter counselor's name">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="counselorNic" class="col-sm-3 col-form-label">Counselor NIC<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="counselorNic" name="counselor_nic" placeholder="Enter counselor's NIC number">
                        </div>
                    </div>
                    {{-- <div class="mb-3 row mx-3">
                        <label for="counselorId" class="col-sm-3 col-form-label">Counselor ID<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="counselorId" name="counselor_id" placeholder="Enter counselor's ID">
                        </div>
                    </div> --}}
                    <div class="mb-3 row mx-3">
                        <label for="counselorPhone" class="col-sm-3 col-form-label">Counselor Phone<span class="text-danger">*</span></label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="counselorPhone" name="counselor_phone" placeholder="Enter counselor's phone number">
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

    <!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <!-- Toasts will be appended here -->
          </div>
@endsection

@push('scripts')
  <script>
    $(document).ready(function() {
    // Handle NIC search
    $('#searchNicBtn').on('click', function() {
            var nic = $('#studentNicSearch').val();
            if (nic) {
        // clear previous messages/banners
        $('#searchMessageContainer').empty();
        $('#terminatedBanner').remove();
        $('#statusBanner').remove();
        $('#studentDetailsOverlay').remove();
        $('#spinner-overlay').show();
                $.ajax({
                    url: '/api/students/' + nic,
                    type: 'GET',
                    success: function(response) {
                        if (response.success) {
                            var student = response.student;
                            var ol_exams = response.ol_exams;
                            var al_exams = response.al_exams;

                            $('#studentName').val(student.name_with_initials);
                            $('#studentNIC').val(student.id_value);
                            $('#studentId').val(student.student_id);
                            $('#studentRegistrationId').val(student.registration_id);

                            $('#olExamType').val(ol_exams.length > 0 ? ol_exams[0].exam_type.exam_type : '');
                            $('#olExamYear').val(ol_exams.length > 0 ? ol_exams[0].exam_year : '');
                            
                            var olTableBody = $('#olExamSubjectsAndGradesTableBody');
                            olTableBody.empty();
                            if (ol_exams.length > 0 && Array.isArray(ol_exams[0].subjects) && ol_exams[0].subjects.length > 0) {
                                ol_exams[0].subjects.forEach(function(subject) {
                                    olTableBody.append('<tr><td>' + (subject.subject_name || subject.name || subject.subject || 'N/A') + '</td><td>' + (subject.grade || subject.result || 'N/A') + '</td></tr>');
                                });
                            } else {
                                olTableBody.append('<tr><td colspan="2" class="text-center">No O/L subjects found</td></tr>');
                            }

                            $('#alExamType').val(al_exams.length > 0 ? al_exams[0].exam_type.exam_type : '');
                            $('#alExamYear').val(al_exams.length > 0 ? al_exams[0].exam_year : '');
                            $('#alExamStream').val(al_exams.length > 0 ? al_exams[0].stream.stream : '');

                            var alTableBody = $('#alExamSubjectsAndGradesTableBody');
                            alTableBody.empty();
                            if (al_exams.length > 0 && Array.isArray(al_exams[0].subjects) && al_exams[0].subjects.length > 0) {
                                al_exams[0].subjects.forEach(function(subject) {
                                    var subjectName = (subject.subject_name !== undefined && subject.subject_name !== null && subject.subject_name !== '')
                                        ? subject.subject_name
                                        : (subject.name !== undefined && subject.name !== null && subject.name !== ''
                                            ? subject.name
                                            : (subject.subject !== undefined && subject.subject !== null && subject.subject !== '' ? subject.subject : 'N/A'));
                                    var subjectGrade = (subject.grade !== undefined && subject.grade !== null && subject.grade !== '')
                                        ? subject.grade
                                        : (subject.result !== undefined && subject.result !== null && subject.result !== '' ? subject.result : 'N/A');
                                    alTableBody.append('<tr><td>' + subjectName + '</td><td>' + subjectGrade + '</td></tr>');
                                });
                            } else {
                                alTableBody.append('<tr><td colspan="2" class="text-center">No A/L subjects found</td></tr>');
                            }
                            
                            $('#studentDetailsSection').show();

                            // If the student is terminated, show a prominent banner and disable register actions
                            if (response.is_terminated) {
                                var termMsg = 'This student is terminated and cannot register.';
                                if (response.latest_semester_registration && response.latest_semester_registration.updated_at) {
                                    try {
                                        var termDate = response.latest_semester_registration.updated_at.split('T')[0];
                                        termMsg += ' (Terminated on: ' + termDate + ')';
                                    } catch (e) {
                                        // ignore formatting errors
                                    }
                                }
                                if ($('#terminatedBanner').length === 0) {
                                    // Render banner into the stable message container so it's visible below the search
                                    $('#searchMessageContainer').html('<div id="terminatedBanner" class="alert alert-danger mt-3" role="alert">' + termMsg + '</div>');
                                } else {
                                    $('#terminatedBanner').text(termMsg).show();
                                }
                                // visually dim/disable details section and fully block interactions
                                $('#studentDetailsSection').addClass('terminated-disabled').show();
                                $('#studentDetailsSection').css('position', 'relative');
                                // disable and set readonly where applicable
                                $('#studentDetailsSection').find('input, select, textarea, button').each(function() {
                                    $(this).prop('disabled', true);
                                    if ($(this).is('input, textarea')) $(this).prop('readonly', true);
                                });
                                // add overlay to ensure nothing is clickable
                                if ($('#studentDetailsOverlay').length === 0) {
                                    $('#studentDetailsSection').append('<div id="studentDetailsOverlay" class="terminated-overlay" aria-hidden="true"></div>');
                                }
                                // show popup toast with student's name if available
                                try {
                                    var sname = (student && student.name_with_initials) ? student.name_with_initials : $('#studentNicSearch').val();
                                    showToast('The student ' + sname + ' is terminated.', 'danger');
                                } catch (e) {}
                                // disable registration actions
                                $('#finalRegister, #checkEligibility').prop('disabled', true).addClass('disabled');
                            } else {
                                // remove any existing banner and enable actions
                                $('#terminatedBanner').remove();
                                // restore details section and remove overlay
                                    $('#studentDetailsSection').removeClass('terminated-disabled');
                                    $('#studentDetailsSection').find('input, select, textarea, button').each(function() {
                                        $(this).prop('disabled', false);
                                        if ($(this).is('input, textarea')) $(this).prop('readonly', false);
                                    });
                                    $('#studentDetailsOverlay').remove();
                                    $('#finalRegister, #checkEligibility').prop('disabled', false).removeClass('disabled');
                                    // Show active/status banner in the stable message container
                                    // Prefer explicit student.status if provided by the API, else fall back to latest_semester_registration.status
                                    var statusText = 'ACTIVE';
                                    if (response.student && response.student.status) {
                                        statusText = response.student.status.toString().toUpperCase();
                                    } else if (response.latest_semester_registration && response.latest_semester_registration.status) {
                                        statusText = response.latest_semester_registration.status.toString().toUpperCase();
                                    }
                                    // If status indicates termination, show red terminated banner and hide details
                                    if (statusText && statusText.toLowerCase().includes('terminat')) {
                                        $('#statusBanner').remove();
                                        var termMsg = 'This student is terminated and cannot register.';
                                        if (response.latest_semester_registration && response.latest_semester_registration.updated_at) {
                                            try {
                                                var termDate = response.latest_semester_registration.updated_at.split('T')[0];
                                                termMsg += ' (Terminated on: ' + termDate + ')';
                                            } catch (e) {}
                                        }
                                        $('#searchMessageContainer').html('<div id="terminatedBanner" class="alert alert-danger mt-3" role="alert"><strong>Terminated:</strong> ' + termMsg + '</div>');
                                        $('#studentDetailsSection').hide();
                                        $('#studentDetailsOverlay').remove();
                                        $('#finalRegister, #checkEligibility').prop('disabled', true).addClass('disabled');
                                        try {
                                            var snameX = (response.student && response.student.name_with_initials) ? response.student.name_with_initials : $('#studentNicSearch').val();
                                            showToast('The student ' + snameX + ' is terminated.', 'danger');
                                        } catch (e) {}
                                    } else if (statusText && statusText.toLowerCase() === 'active') {
                                        // Active: remove any banners entirely and ensure details are visible and enabled
                                        $('#statusBanner').remove();
                                        $('#terminatedBanner').remove();
                                        $('#studentDetailsSection').show();
                                        $('#studentDetailsSection').removeClass('terminated-disabled');
                                        $('#studentDetailsSection').find('input, select, textarea, button').each(function() {
                                            $(this).prop('disabled', false);
                                            if ($(this).is('input, textarea')) $(this).prop('readonly', false);
                                        });
                                        $('#studentDetailsOverlay').remove();
                                        $('#finalRegister, #checkEligibility').prop('disabled', false).removeClass('disabled');
                                    } else {
                                        // Other statuses (e.g., HOLDING) — show green status banner
                                        if ($('#statusBanner').length === 0) {
                                            $('#searchMessageContainer').html('<div id="statusBanner" class="alert alert-success mt-3" role="alert">Student status: ' + statusText + '</div>');
                                        } else {
                                            $('#statusBanner').text('Student status: ' + statusText).show();
                                        }
                                    }
                            }
                        } else {
                            // If server returned non-success but indicates termination, still show terminated banner
                            var msg = response.message || '';
                            var studentStatusMsg = (response.student && response.student.status) ? response.student.status.toString().toLowerCase() : '';
                            if (response.is_terminated || (msg && msg.toLowerCase().includes('terminat')) || (studentStatusMsg && studentStatusMsg.includes('terminat'))) {
                                var termMsg = msg && msg.toLowerCase().includes('terminat') ? msg : 'This student is terminated and cannot register.';
                                if (response.latest_semester_registration && response.latest_semester_registration.updated_at) {
                                    try {
                                        var termDate = response.latest_semester_registration.updated_at.split('T')[0];
                                        termMsg += ' (Terminated on: ' + termDate + ')';
                                    } catch (e) {}
                                }
                                if ($('#terminatedBanner').length === 0) {
                                    $('#searchMessageContainer').html('<div id="terminatedBanner" class="alert alert-danger mt-3" role="alert">' + termMsg + '</div>');
                                } else {
                                    $('#terminatedBanner').text(termMsg).show();
                                }
                                $('#studentDetailsSection').hide();
                                $('#studentDetailsSection').find('input, select, button').prop('disabled', true);
                                $('#finalRegister, #checkEligibility').prop('disabled', true).addClass('disabled');
                                // popup message with student name if present
                                try {
                                    var sname2 = (response.student && response.student.name_with_initials) ? response.student.name_with_initials : $('#studentNicSearch').val();
                                    showToast('The student ' + sname2 + ' is terminated.', 'danger');
                                } catch (e) {}
                            } else {
                                // show error message both as toast and banner
                                var errMsg = response.message || 'An error occurred while fetching student data.';
                                showToast(errMsg, 'danger');
                                $('#searchMessageContainer').html('<div class="alert alert-danger mt-3" role="alert">' + errMsg + '</div>');
                                $('#studentDetailsSection').hide();
                            }
                        }
                    },
                    error: function(xhr, status, error) {
                        // If the server returned a termination message or 422, show terminated banner
                        var handled = false;
                            if (xhr && xhr.responseJSON) {
                            var msg = xhr.responseJSON.message || xhr.responseJSON.error || '';
                            var respStudentStatus = (xhr.responseJSON && xhr.responseJSON.student && xhr.responseJSON.student.status) ? xhr.responseJSON.student.status.toString().toLowerCase() : '';
                            if (xhr.status === 422 || (msg && msg.toLowerCase().includes('terminat')) || (respStudentStatus && respStudentStatus.includes('terminat'))) {
                                var termMsg = msg && msg.toLowerCase().includes('terminat') ? msg : 'This student is terminated and cannot register.';
                                if (xhr.responseJSON.latest_semester_registration && xhr.responseJSON.latest_semester_registration.updated_at) {
                                    try {
                                        var termDate = xhr.responseJSON.latest_semester_registration.updated_at.split('T')[0];
                                        termMsg += ' (Terminated on: ' + termDate + ')';
                                    } catch (e) {}
                                }
                                if ($('#terminatedBanner').length === 0) {
                                    $('#searchMessageContainer').html('<div id="terminatedBanner" class="alert alert-danger mt-3" role="alert">' + termMsg + '</div>');
                                } else {
                                    $('#terminatedBanner').text(termMsg).show();
                                }
                                $('#studentDetailsSection').hide();
                                $('#studentDetailsSection').find('input, select, button').prop('disabled', true);
                                $('#finalRegister, #checkEligibility').prop('disabled', true).addClass('disabled');
                                handled = true;
                                // popup message: try read student name from response or fallback to NIC
                                try {
                                    var sname3 = xhr.responseJSON.student && xhr.responseJSON.student.name_with_initials ? xhr.responseJSON.student.name_with_initials : $('#studentNicSearch').val();
                                    showToast('The student ' + sname3 + ' is terminated.', 'danger');
                                } catch (e) {}
                            }
                        }
                        if (!handled) {
                            var fallback = 'An error occurred while fetching student data.';
                            var serverMsg = fallback;
                            try {
                                if (xhr && xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.error)) {
                                    serverMsg = xhr.responseJSON.message || xhr.responseJSON.error;
                                } else if (xhr && xhr.status) {
                                    serverMsg = 'Server returned status: ' + xhr.status;
                                }
                            } catch (e) {}
                            showToast(serverMsg, 'danger');
                            $('#searchMessageContainer').html('<div class="alert alert-danger mt-3" role="alert">' + serverMsg + '</div>');
                            $('#studentDetailsSection').hide();
                        }
                    },
                    complete: function() {
                        $('#spinner-overlay').hide();
                    }
                });
            } else {
                showToast('Please enter a student NIC to search.', 'warning');
            }
        });

        // Handle location change to fetch courses
        $('#location').on('change', function() {
            var location = $(this).val();
            var courseSelect = $('#courseSearch');
            var intakeSelect = $('#intakeId');

            courseSelect.empty().append('<option selected disabled>Select a location first</option>').prop('disabled', true);
            intakeSelect.empty().append('<option value="" selected disabled>Select a course first</option>').prop('disabled', true);

            if (location) {
                $('#spinner-overlay').show();
                $.ajax({
                    url: '/api/courses-by-location/' + location,
                    type: 'GET',
                    success: function(response) {
                        if (response.success && response.courses.length > 0) {
                            courseSelect.prop('disabled', false);
                            courseSelect.empty().append('<option selected disabled>Select a Course</option>');
                            $.each(response.courses, function(key, course) {
                                courseSelect.append('<option value="' + course.course_id + '">' + course.course_name + '</option>');
                    });
                } else {
                            showToast(response.message || 'No courses found for this location.', 'warning');
                        }
                    },
                    error: function(xhr, status, error) {
                        showToast('An error occurred while fetching courses.', 'danger');
                    },
                    complete: function() {
                        $('#spinner-overlay').hide();
                    }
                });
            }
        });

        // Handle course selection to fetch intakes
        $('#courseSearch').on('change', function() {
            const courseId = $(this).val();
            const location = $('#location').val();
            const intakeSelect = $('#intakeId');
            const registrationFeeInput = $('#registrationFee');

            if (courseId && location) {
                $.ajax({
                    url: '{{ route("intakes.get") }}',
                    type: 'POST',
                    data: {
                        course_name: $(this).find('option:selected').text(),
                        location: location,
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(data) {
                        intakeSelect.empty().append('<option value="" selected disabled>Select an intake</option>');
                        registrationFeeInput.val('').prop('readonly', true);

                        if (data.length) {
                            intakeSelect.data('intakes', data); 
                            
                            $.each(data, function(index, intake) {
                                intakeSelect.append(`<option value="${intake.intake_id}">${intake.batch}</option>`);
                            });
                            intakeSelect.prop('disabled', false);
                        } else {
                            intakeSelect.append('<option value="" disabled>No intakes available</option>');
                            intakeSelect.prop('disabled', true);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = 'An error occurred while fetching intakes.';
                        if (xhr.responseJSON) {
                            if (xhr.responseJSON.error) {
                                errorMessage = xhr.responseJSON.error;
                            } else if (xhr.responseJSON.message) {
                                errorMessage = xhr.responseJSON.message;
                            }
                        }
                        showToast(errorMessage, 'danger');
                        intakeSelect.empty().append('<option value="" selected disabled>Error loading intakes</option>').prop('disabled', true);
                    }
                });
            }
        });

        // Handle intake selection to auto-fill registration fee and start date
        $('#intakeId').on('change', function() {
            const selectedIntakeId = $(this).val();
            const intakesData = $(this).data('intakes');
            const registrationFeeInput = $('#registrationFee');
            const startDateInput = $('#courseStartDate');

            const selectedIntake = intakesData.find(intake => intake.intake_id == selectedIntakeId);

            if (selectedIntake) {
                registrationFeeInput.val(selectedIntake.registration_fee).prop('readonly', true);
                
                // The date from the server might have time information, so we format it.
                const formattedDate = selectedIntake.start_date.split('T')[0];
                startDateInput.val(formattedDate).prop('readonly', true);
            } else {
                registrationFeeInput.val('').prop('readonly', false);
                startDateInput.val('').prop('readonly', false);
            }
        });

        // Toggle Service No field based on SLT Employee radio button
        $('input[name="slt_employee"]').on('change', function() {
            if (this.value === 'yes') {
                $('#serviceNoField').show();
                $('#externalCounselorFields').hide();
            } else {
                $('#serviceNoField').hide();
                $('#externalCounselorFields').show();
            }
        }).trigger('change');

        // Handle "Other" checkbox for marketing survey
        $('#checkboxOther').on('change', function() {
            if (this.checked) {
                $('#otherMarketingSurveyRow').show();
                $('#marketing_survey_other').prop('required', true);
            } else {
                $('#otherMarketingSurveyRow').hide();
                $('#marketing_survey_other').prop('required', false);
                $('#marketing_survey_other').val('');
            }
        });
    
        // Handle form submission
        $('#finalRegister').on('click', function(e) {
            e.preventDefault();
            
            const marketing_survey_options = [];
            $('input[type="checkbox"]:checked').each(function() {
                let value = $(this).val();
                // If "Other" is selected, replace it with the text input value
                if (value === 'Other' && $('#marketing_survey_other').val().trim()) {
                    value = $('#marketing_survey_other').val().trim();
                }
                marketing_survey_options.push(value);
            });

            const formData = {
                student_id: $('#studentId').val(),
                course_id: $('#courseSearch').val(),
                intake_id: $('#intakeId').val(),
                location: $('#location').val(),
                registration_fee: $('#registrationFee').val(),
                slt_employee: $('input[name="slt_employee"]:checked').val(),
                service_no: $('#serviceNo').val(),
                counselor_name: $('#counselorName').val(),
                counselor_id: $('#counselorId').val(),
                counselor_phone: $('#counselorPhone').val(),
                counselor_nic: $('#counselorNic').val(),
                course_start_date: $('#courseStartDate').val(),
                marketing_survey_options: marketing_survey_options,
                _token: '{{ csrf_token() }}'
            };

            $.ajax({
                url: '{{ route("register.course.api") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 1500);
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred during registration.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage += '<br>' + errors.join('<br>');
                        }
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        });

        function resetForm() {
            $('#studentNicSearch').val('');
            $('#studentDetailsSection').hide();
            $('#location').val('');
            $('#courseSearch').val('');
            $('#intakeId').empty().append('<option value="" selected disabled>Select Intake</option>');
            $('#registrationFee').val('');
            $('input[name="slt_employee"][value="no"]').prop('checked', true);
            $('#serviceNoField').hide();
            $('#serviceNo').val('');
            $('#courseStartDate').val('');
            $('.form-check-input').prop('checked', false);
        }

        function showToast(message, type) {
            var toastHtml = '<div class="toast align-items-center text-white bg-' + type + ' border-0" role="alert" aria-live="assertive" aria-atomic="true">' +
                              '<div class="d-flex">' +
                                '<div class="toast-body">' + message + '</div>' +
                                '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>' +
                              '</div>' +
                            '</div>';
            $('.toast-container').append(toastHtml);
            var toastEl = $('.toast-container .toast').last();
            var toast = new bootstrap.Toast(toastEl);
            toast.show();
        }
        window.showToast = showToast;

        // Show toast if session success message exists (for non-AJAX form)
        @if(session('success'))
            showToast('Student has been registered successfully', 'success');
        @endif
    });

        function redirectToEligibility() {
            var studentNic = document.getElementById('studentNicSearch').value;
            if (!studentNic) {
                alert('Please enter a student NIC first.');
                return;
            }

            // Check if all required fields are filled
            var courseId = $('#courseSearch').val();
            var intakeId = $('#intakeId').val();
            var location = $('#location').val();
            var registrationFee = $('#registrationFee').val();
            var sltEmployee = $('input[name="slt_employee"]:checked').val();
            var courseStartDate = $('#courseStartDate').val();

            if (!courseId || !intakeId || !location || !registrationFee || !sltEmployee || !courseStartDate) {
                alert('Please fill in all required fields before checking eligibility.');
                return;
            }

            // Collect marketing survey options
            const marketing_survey_options = [];
            $('input[type="checkbox"]:checked').each(function() {
                let value = $(this).val();
                // If "Other" is selected, replace it with the text input value
                if (value === 'Other' && $('#marketing_survey_other').val().trim()) {
                    value = $('#marketing_survey_other').val().trim();
                }
                marketing_survey_options.push(value);
            });

            // Prepare form data
            const formData = {
                student_id: $('#studentId').val(),
                course_id: courseId,
                intake_id: intakeId,
                location: location,
                registration_fee: registrationFee,
                slt_employee: sltEmployee,
                service_no: $('#serviceNo').val(),
                counselor_name: $('#counselorName').val(),
                counselor_id: $('#counselorId').val(),
                counselor_phone: $('#counselorPhone').val(),
                counselor_nic: $('#counselorNic').val(),
                course_start_date: courseStartDate,
                marketing_survey_options: marketing_survey_options,
                _token: '{{ csrf_token() }}'
            };

            // Save course registration data first
            $.ajax({
                url: '{{ route("register.course.eligibility.api") }}',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(formData),
                success: function(response) {
                    if (response.success) {
                        showToast('Course registration saved successfully. Redirecting to eligibility page...', 'success');
                        // Redirect to eligibility page with NIC and course parameters
                        setTimeout(function() {
                            const courseName = $('#courseSearch option:selected').text();
                            const courseId = $('#courseSearch').val();
                            const redirectUrl = `/eligibility-registration?nic=${studentNic}&course_id=${courseId}&course_name=${encodeURIComponent(courseName)}`;
                            console.log('Redirecting to:', redirectUrl);
                            console.log('Course Name:', courseName);
                            console.log('Course ID:', courseId);
                            console.log('Student NIC:', studentNic);
                            window.location.href = redirectUrl;
                        }, 1500);
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    console.log(xhr.responseText); // Log backend error for debugging
                    let errorMessage = 'An error occurred while saving registration data.';
                    if (xhr.responseJSON) {
                        if (xhr.responseJSON.error) {
                            errorMessage = xhr.responseJSON.error;
                        } else if (xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage += '<br>' + errors.join('<br>');
                        }
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        }
  </script>
@endpush