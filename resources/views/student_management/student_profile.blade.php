@extends('inc.app')

@section('title', 'NEBULA | Student Profile')

@section('content')
<style>
/* Validation Error Styles */
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

.is-invalid:focus {
    border-color: #dc3545 !important;
    box-shadow: 0 0 0 0.2rem rgba(220, 53, 69, 0.25) !important;
}

/* Success Message Styles */
.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.success-message.show {
    transform: translateX(0);
}

.success-message .success-icon {
    margin-right: 10px;
    font-size: 18px;
}

/* Error Message Styles */
.error-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.error-message.show {
    transform: translateX(0);
}

.error-message .error-icon {
    margin-right: 10px;
    font-size: 18px;
}

</style>

@php
  $status = $student->academic_status ?? 'active';
@endphp

<div class="container-fluid">
  <div class="row justify-content-center mt-4">
    <div class="col-md-11">
      <div class="p-4 rounded shadow w-100 bg-white">
        @if (session('success'))
          <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if (session('error'))
          <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <h2 class="text-center mb-4">Student Profile</h2>
        <hr style="margin-bottom:30px;">

        {{-- NIC Search --}}
        <div class="row mb-4 justify-content-center">
          <div class="col-md-10">
            <div class="p-3 rounded" style="background-color:#e0f1ff;">
              <form id="nicSearchForm" autocomplete="off">
                <div class="input-group">
                  <input type="text" class="form-control" id="nicInput" name="nic" placeholder="Enter NIC number" required>
                  <button class="btn btn-primary" type="submit" style="min-width:120px;">Search</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="container mt-4 rounded border p-3" id="profileSection" style="{{ isset($student) ? '' : 'display:none;' }}">
          <input type="hidden" id="studentIdHidden" value="{{ $student->student_id ?? '' }}">

          {{-- Tabs --}}
          <ul class="nav nav-tabs" id="studentTabs">
            <li class="nav-item"><a class="nav-link active bg-primary text-white" id="personal-tab" data-bs-toggle="tab" href="#personal">Personal Info</a></li>
            <li class="nav-item"><a class="nav-link" id="parent-tab" data-bs-toggle="tab" href="#parent">Parent/Guardian Info</a></li>
            <li class="nav-item"><a class="nav-link" id="academic-tab" data-bs-toggle="tab" href="#academic">Academic</a></li>
            <li class="nav-item"><a class="nav-link" id="exams-tab" data-bs-toggle="tab" href="#exams">Exams Results</a></li>
            <li class="nav-item"><a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history">History</a></li>
            <li class="nav-item"><a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance">Attendance</a></li>
              <li class="nav-item"><a class="nav-link" id="payment-summary-tab" data-bs-toggle="tab" href="#payment-summary">Payment Summary</a></li>
            <li class="nav-item"><a class="nav-link" id="clearance-tab" data-bs-toggle="tab" href="#clearance">Clearance</a></li>
            <li class="nav-item"><a class="nav-link" id="certificates-tab" data-bs-toggle="tab" href="#certificates">Certificates</a></li>
            <li class="nav-item"><a class="nav-link" id="status-history-tab" data-bs-toggle="tab" href="#status-history">Status History <span id="statusHistoryCount" class="badge bg-danger ms-1" style="display:none;">0</span></a></li>
            <li class="nav-item"><a class="nav-link" id="other-info-tab" data-bs-toggle="tab" href="#other-info">Other Information</a></li>
          </ul>

          <div class="tab-content mt-2">
            {{-- PERSONAL TAB --}}
            <div class="tab-pane fade show active" id="personal">
              {{-- Status + Actions --}}
              <div class="d-flex align-items-center justify-content-between mt-3 mb-3 px-2">
                <div>
                  <span class="fw-bold me-2">Academic Status:</span>
                  <span id="studentStatusBadge" class="badge {{ strtolower($status)==='terminated' ? 'bg-danger' : 'bg-success' }}">{{ strtoupper($status) }}</span>
                </div>
                <div class="d-flex gap-2">
                  <button type="button" id="terminateBtn" class="btn btn-outline-danger" style="{{ strtolower($status)==='terminated' ? 'display:none;' : '' }}">
                    <i class="ti ti-user-x me-1"></i> Terminate
                  </button>
                  <button type="button" id="reinstateBtn" class="btn btn-success" style="{{ strtolower($status)==='terminated' ? '' : 'display:none;' }}">
                    <i class="ti ti-user-check me-1"></i> Re‑Register
                  </button>
                </div>
              </div>

              {{-- Profile Picture --}}
              <div class="mb-3 mt-4 text-center position-relative">
                <div class="d-flex justify-content-end">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 position-relative" style="width:150px;height:150px;border:2px solid #ccc;">
                    <img src="{{ !empty($student->user_photo) ? asset('storage/' . $student->user_photo) : asset('images/profile/user-1.jpg') }}" alt="Student Profile" width="150" height="150" class="rounded-circle" id="studentProfilePictureImg">
                  </div>
                </div>
                <input type="file" class="form-control visually-hidden" id="profilePicture" accept="image/*">
                <div class="d-flex justify-content-end mx-4">
                  <button type="button" class="btn btn-sm btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#editPictureModal" id="editPictureBtn" style="display:none;">Edit Picture</button>
                </div>
              </div>

              {{-- Personal Details --}}
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentTitle" class="col-sm-3 col-form-label fw-bold">Title <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentTitle" value="{{ $student->title ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentName" class="col-sm-3 col-form-label fw-bold">Name <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentName" value="{{ $student->full_name ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentNIC" class="col-sm-3 col-form-label fw-bold">NIC <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentNIC" value="{{ $student->id_value ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentInstitute" class="col-sm-3 col-form-label fw-bold">Institute <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentInstitute" value="{{ $student->institute_location ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentDOB" class="col-sm-3 col-form-label fw-bold">Date of Birth <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentDOB" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentGender" class="col-sm-3 col-form-label fw-bold">Gender <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentGender" value="{{ $student->gender ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentEmail" class="col-sm-3 col-form-label fw-bold">Email <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="email" class="form-control" id="studentEmail" value="{{ $student->email ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentMobile" class="col-sm-3 col-form-label fw-bold">Mobile Phone No <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="tel" class="form-control" id="studentMobile" value="{{ $student->mobile_phone ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentHomePhone" class="col-sm-3 col-form-label fw-bold">Home Phone No</label>
                <div class="col-sm-9">
                  <input type="tel" class="form-control" id="studentHomePhone" value="{{ $student->home_phone ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentAddress" class="col-sm-3 col-form-label fw-bold">Address <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentAddress" rows="2" readonly>{{ $student->address ?? '' }}</textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentFoundation" class="col-sm-3 col-form-label fw-bold">Foundation Program</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentFoundation" value="{{ $student->foundation_program ?? '' }}" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentSpecialNeeds" class="col-sm-3 col-form-label fw-bold">Special Needs</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentSpecialNeeds" rows="2" readonly>{{ $student->special_needs ?? '' }}</textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentExtraCurricular" class="col-sm-3 col-form-label fw-bold">Extra Curricular Activities</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentExtraCurricular" rows="2" readonly>{{ $student->extracurricular_activities ?? '' }}</textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentFuturePotentials" class="col-sm-3 col-form-label fw-bold">Future Potentials</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentFuturePotentials" rows="2" readonly>{{ $student->future_potentials ?? '' }}</textarea>
                </div>
              </div>

              {{-- Edit Buttons --}}
              <div class="mt-4 mb-3">
                <button type="button" class="btn btn-primary" id="showEditPersonalInfoBtn">Edit Personal Info</button>
                <button type="button" class="btn btn-success ms-2" id="updatePersonalInfoBtn" style="display:none;">Update Personal Info</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelEditBtn" style="display:none;">Cancel</button>
              </div>
            </div>

      {{-- PARENT TAB --}}
      <div class="tab-pane fade" id="parent">
      <!-- In the parent tab section of student_profile.blade.php -->
    <div class="mb-3 row align-items-center mx-3">
        <label for="parentName" class="col-sm-3 col-form-label fw-bold">Name <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="parentName" value="{{ $student->parent->guardian_name ?? '' }}" readonly>
          <div class="invalid-feedback" id="parentNameFeedback" style="display:none;"></div>
        </div>
      </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentProfession" class="col-sm-3 col-form-label fw-bold">Profession <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="parentProfession" value="{{ $student->parent->guardian_profession ?? '' }}" readonly>
          <div class="invalid-feedback" id="parentProfessionFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentContactNo" class="col-sm-3 col-form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="tel" class="form-control" id="parentContactNo" value="{{ $student->parent->guardian_contact_number ?? '' }}" readonly>
          <div class="invalid-feedback" id="parentContactNoFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentEmail" class="col-sm-3 col-form-label fw-bold">Email <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="email" class="form-control" id="parentEmail" value="{{ $student->parent->guardian_email ?? '' }}" readonly>
          <div class="invalid-feedback" id="parentEmailFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentAddress" class="col-sm-3 col-form-label fw-bold">Address <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <textarea class="form-control" id="parentAddress" rows="2" readonly>{{ $student->parent->guardian_address ?? '' }}</textarea>
          <div class="invalid-feedback" id="parentAddressFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentEmergencyContact" class="col-sm-3 col-form-label fw-bold">Emergency Contact Number <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control bg-danger text-white" id="parentEmergencyContact" value="{{ $student->parent->emergency_contact_number ?? '' }}" readonly>
          <div class="invalid-feedback" id="parentEmergencyContactFeedback" style="display:none;"></div>
        </div>
            </div>
              <div class="mt-4 mb-3">
                <button type="button" class="btn btn-primary" id="showEditParentInfoBtn">Edit Parent/Guardian Info</button>
                <button type="button" class="btn btn-success ms-2" id="updateParentInfoBtn" style="display:none;">Update Parent/Guardian Info</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelEditParentBtn" style="display:none;">Cancel</button>
              </div>
            </div>

            {{-- ACADEMIC TAB (server-rendered summary, JS will also build) --}}
            <div class="tab-pane fade" id="academic">
              @php
                $ol_pending = true; $al_pending = true; $ol_exam=null; $al_exam=null;
                if (isset($student->exams) && !$student->exams->isEmpty()) {
                  $exam = $student->exams->first();
                  if ($exam) {
                    $ol_subjects = is_array($exam->ol_exam_subjects) ? $exam->ol_exam_subjects : json_decode($exam->ol_exam_subjects, true);
                    if (!empty($ol_subjects)) { $ol_pending=false; $ol_exam=$exam; }
                    $al_subjects = is_array($exam->al_exam_subjects) ? $exam->al_exam_subjects : json_decode($exam->al_exam_subjects, true);
                    if (!empty($al_subjects)) { $al_pending=false; $al_exam=$exam; }
                  }
                }
              @endphp

              @if ($ol_pending)
                <div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student's O/L exam results are still pending.</div>
              @else
                <div id="olExamSection">
                  <h5 class="mt-4 mb-3 fw-bold">O/L Exam Details</h5>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $ol_exam->ol_index_no ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $ol_exam->ol_exam_type ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $ol_exam->ol_exam_year ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                    <div class="col-sm-9">
                      <table class="table table-bordered mb-0">
                        <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                        <tbody>
                          @foreach (json_decode($ol_exam->ol_exam_subjects, true) ?? [] as $subject)
                            <tr><td>{{ $subject['subject'] ?? '' }}</td><td>{{ $subject['result'] ?? '' }}</td></tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
                    <div class="col-sm-9">
                      @if (!empty($ol_exam->ol_certificate))
                        <a href="{{ asset('storage/certificates/' . $ol_exam->ol_certificate) }}" target="_blank">View Certificate</a>
                      @else
                        <span class="text-muted">Not uploaded</span>
                      @endif
                    </div>
                  </div>
                </div>
              @endif

              @if ($al_pending)
                <div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student's A/L exam results are still pending.</div>
              @else
                <div id="alExamSection">
                  <hr>
                  <h5 class="mt-4 mb-3 fw-bold">A/L Exam Details</h5>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $al_exam->al_index_no ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $al_exam->al_exam_type ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $al_exam->al_exam_year ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">A/L Stream</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="{{ $al_exam->al_stream ?? '' }}" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                    <div class="col-sm-9">
                      <table class="table table-bordered mb-0">
                        <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                        <tbody>
                          @foreach (json_decode($al_exam->al_exam_subjects, true) ?? [] as $subject)
                            <tr><td>{{ $subject['subject'] ?? '' }}</td><td>{{ $subject['result'] ?? '' }}</td></tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
                    <div class="col-sm-9">
                      @if (!empty($al_exam->al_certificate))
                        <a href="{{ asset('storage/certificates/' . $al_exam->al_certificate) }}" target="_blank">View Certificate</a>
                      @else
                        <span class="text-muted">Not uploaded</span>
                      @endif
                    </div>
                  </div>
                </div>
              @endif

            </div>

            {{-- EXAMS TAB --}}
            <div class="tab-pane fade" id="exams">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="examCourseSelect" class="form-label fw-bold">Select Course</label>
                  <select id="examCourseSelect" class="form-select">
                    <option value="">Select a course</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <label for="examSemesterSelect" class="form-label fw-bold">Select Semester</label>
                  <select id="examSemesterSelect" class="form-select" disabled>
                    <option value="">Select a semester</option>
                  </select>
                </div>
              </div>
              <div id="examResultsTableWrapper" style="display:none;">
                <h5 class="fw-bold mb-3">Module Results</h5>
                <table class="table table-bordered">
                  <thead class="bg-primary text-white">
                    <tr><th>Module Name</th><th>Marks</th><th>Grade</th></tr>
                  </thead>
                  <tbody id="examResultsTableBody"></tbody>
                </table>
              </div>
            </div>

            {{-- HISTORY TAB --}}
            <div class="tab-pane fade" id="history">
              <h5 class="fw-bold mb-3">Course Registration History</h5>
              <table class="table table-bordered">
                <thead class="bg-primary text-white">
                  <tr>
                    <th>Course</th>
                    <th>Intake</th>
                    <th>Status</th>
                    <th>Specialization</th>
                    <th>Overall Grade</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="historyTableBody"></tbody>
                  <!-- Populated by JS -->
                </tbody>
              </table>
            </div>

            {{-- ATTENDANCE TAB --}}
            <div class="tab-pane fade" id="attendance">
              <div class="row mb-3">
                <div class="col-md-6">
                  <label for="attendanceCourseSelect" class="form-label fw-bold">Select Course</label>
                  <select id="attendanceCourseSelect" class="form-select"><option value="">Select a course</option></select>
                </div>
                <div class="col-md-6">
                  <label for="attendanceSemesterSelect" class="form-label fw-bold">Select Semester</label>
                  <!-- Start not disabled in markup; JS will control enabled/disabled state -->
                  <select id="attendanceSemesterSelect" class="form-select"><option value="">Select a semester</option></select>
                </div>
              </div>
              <div id="attendanceTableWrapper" style="display:none;">
                <h5 class="fw-bold mb-3">Module Attendance</h5>
                <table class="table table-bordered">
                  <thead class="bg-primary text-white">
                    <tr><th>Module Name</th><th>Total Days</th><th>Present Days</th><th>Absent Days</th><th>Attendance %</th></tr>
                  </thead>
                  <tbody id="attendanceTableBody"></tbody>
                </table>
              </div>
            </div>

            <!-- Payment Summary Tab -->
            <div class="tab-pane fade" id="payment-summary" role="tabpanel" aria-labelledby="payment-summary-tab">
              <div class="mt-4">
                <!-- Filters -->
                <div class="mb-4">
                  <div class="row mb-3 align-items-center">
                    <label class="col-sm-2 col-form-label fw-bold">Course <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                      <select class="form-select" id="summary-course" required>
                        <option value="" selected disabled>Select a Course</option>
                      </select>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12 text-center">
                      <button type="button" class="btn btn-primary" id="generatePaymentSummaryBtn">
                        <i class="ti ti-chart-pie me-2"></i>Generate Summary
                      </button>
                    </div>
                  </div>
                </div>

                <!-- Payment Summary -->
                <div class="mt-4" id="paymentSummarySection" style="display:none;">
                  <h4 class="text-center mb-3">Payment Summary</h4>
                  <!-- Student Information -->
                  <div class="card mb-4">
                    <div class="card-header">
                      <h5 class="mb-0">
                        <i class="ti ti-user me-2"></i>Student Information
                      </h5>
                    </div>
                    <div class="card-body">
                      <div class="row">
                        <div class="col-md-6">
                          <p><strong>Student ID:</strong> <span id="summary-student-id"></span></p>
                          <p><strong>Student Name:</strong> <span id="summary-student-name"></span></p>
                          <p><strong>Course:</strong> <span id="summary-course-name"></span></p>
                        </div>
                        <div class="col-md-6">
                          <p><strong>Registration Date:</strong> <span id="summary-registration-date"></span></p>
                          <p><strong>Total Course Fee:</strong> <span id="summary-total-course-fee"></span></p>
                          <p><strong>Total Paid:</strong> <span id="summary-total-paid"></span></p>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Summary Cards -->
                  <div class="row mb-4">
                    <div class="col-md-3">
                      <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                          <h5>Total Amount</h5>
                          <h3 id="total-amount">Rs. 0</h3>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-success text-white">
                        <div class="card-body text-center">
                          <h5>Total Paid</h5>
                          <h3 id="total-paid">Rs. 0</h3>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-warning text-white">
                        <div class="card-body text-center">
                          <h5>Outstanding</h5>
                          <h3 id="total-outstanding">Rs. 0</h3>
                        </div>
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="card bg-info text-white">
                        <div class="card-body text-center">
                          <h5>Payment Rate</h5>
                          <h3 id="payment-rate">0%</h3>
                        </div>
                      </div>
                    </div>
                  </div>
                  <!-- Payment Details Table -->
                  <div class="card">
                    <div class="card-header">
                      <h5 class="mb-0">
                        <i class="ti ti-list me-2"></i>Payment Details by Type
                      </h5>
                    </div>
                    <div class="card-body">
                      <!-- Local Course Fee Table -->
                      <div class="mb-4">
                        <h6 class="text-primary mb-3">
                          <i class="ti ti-book me-2"></i>Local Course Fee
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="courseFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No course fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- Franchise Payments Table -->
                      <div class="mb-4">
                        <h6 class="text-success mb-3">
                          <i class="ti ti-building me-2"></i>Franchise Payments
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="franchiseFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No franchise fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- Registration Fee Table -->
                      <div class="mb-4">
                        <h6 class="text-info mb-3">
                          <i class="ti ti-file-text me-2"></i>Registration Fee
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="registrationFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No registration fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- Hostel Fee Table -->
                      <div class="mb-4">
                        <h6 class="text-warning mb-3">
                          <i class="ti ti-home me-2"></i>Hostel Fee
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="hostelFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No hostel fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- Library Fee Table -->
                      <div class="mb-4">
                        <h6 class="text-secondary mb-3">
                          <i class="ti ti-library me-2"></i>Library Fee
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="libraryFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No library fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                      <!-- Other Fees Table -->
                      <div class="mb-4">
                        <h6 class="text-dark mb-3">
                          <i class="ti ti-plus me-2"></i>Other
                        </h6>
                        <div class="table-responsive">
                          <table class="table table-bordered table-sm">
                            <thead class="table-light">
                              <tr>
                                <th>Total Amount</th>
                                <th>Paid Amount</th>
                                <th>Outstanding</th>
                                <th>Paid Date</th>
                                <th>Due Date</th>
                                <th>Receipt No</th>
                                <th>Uploaded Receipt</th>
                                <th>Installments</th>
                              </tr>
                            </thead>
                            <tbody id="otherFeeTableBody">
                              <tr><td colspan="8" class="text-center text-muted">No other fee data available</td></tr>
                            </tbody>
                          </table>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            {{-- CLEARANCE TAB --}}
            <div class="tab-pane fade" id="clearance">
              <h5 class="fw-bold mb-3">Student Clearance Status</h5>
              <table class="table table-bordered">
                <thead class="bg-primary text-white">
                  <tr><th>Clearance Type</th><th>Status</th><th>Approved Date</th><th>Remarks</th><th>Uploaded Document</th></tr>
                </thead>
                <tbody id="clearanceTableBody"></tbody>
              </table>
            </div>

            {{-- CERTIFICATES TAB --}}
            <div class="tab-pane fade" id="certificates">
              <h5 class="mt-4 mb-3 fw-bold">Certificates</h5>
              <div class="mb-3 row align-items-center mx-3">
                <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
                <div class="col-sm-9" id="olCertificate"></div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
                <div class="col-sm-9" id="alCertificate"></div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label class="col-sm-3 col-form-label fw-bold">Disciplinary Issue Document</label>
                <div class="col-sm-9" id="disciplinaryDocument"></div>
              </div>
            </div>
            
            {{-- STATUS HISTORY TAB --}}
            <div class="tab-pane fade" id="status-history">
              <h5 class="mt-4 mb-3 fw-bold">Status / Termination History</h5>
              <div class="table-responsive">
                <table class="table table-bordered">
                  <thead class="bg-primary text-white">
                    <tr><th>#</th><th>From Status</th><th>To Status</th><th>Reason</th><th>Document</th><th>Changed By</th><th>Date</th></tr>
                    </thead>
                  <tbody id="statusHistoryTableBody">
                    <tr><td colspan="7" class="text-center text-muted">No status history available.</td></tr>
                  </tbody>
                </table>
              </div>
            </div>
            
            
            {{-- OTHER INFO TAB --}}
            <div class="tab-pane fade" id="other-info">
              <h5 class="mt-4 mb-3 fw-bold">Other Information</h5>
              @if(isset($student->other_information))
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Disciplinary Issues</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" rows="2" readonly>{{ $student->other_information->disciplinary_issues ?? '' }}</textarea>
                  </div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Disciplinary Document</label>
                  <div class="col-sm-9">
                    @if($student->other_information->disciplinary_issue_document)
                      <a href="{{ asset('storage/' . $student->other_information->disciplinary_issue_document) }}" target="_blank">View Document</a>
                    @else
                      <span class="text-muted">Not uploaded</span>
                    @endif
                  </div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Institute</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="{{ $student->other_information->institute ?? '' }}"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Field of Study</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="{{ $student->other_information->field_of_study ?? '' }}"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Job Title</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="{{ $student->other_information->job_title ?? '' }}"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Workplace</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="{{ $student->other_information->workplace ?? '' }}"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Other Information</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" rows="2" readonly>{{ $student->other_information->other_information ?? '' }}</textarea>
                  </div>
                </div>
              @else
                <div class="alert alert-warning">No other information found for this student.</div>
              @endif
            </div>
          </div>
        </div> {{-- /#profileSection --}}
      </div>
    </div>
  </div>
</div>

<script>
/* The page contains extensive JavaScript for interactivity as in original file. */
</script>

{{-- Modals (edit picture, terminate, reinstate, terminateClearance) --}}

@endsection
