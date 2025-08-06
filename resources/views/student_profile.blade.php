@extends('inc.app')

@section('title', 'NEBULA | Student Profile')

@section('content')
<style>
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
        <hr style="margin-bottom: 30px;">
        <!-- NIC Search Field (styled like previous search bar) -->
        <div class="row mb-4 justify-content-center">
          <div class="col-md-10">
            <div class="p-3 rounded" style="background-color: #e0f1ff;">
              <form id="nicSearchForm" autocomplete="off">
                <div class="input-group">
                  <input type="text" class="form-control" id="nicInput" name="nic" placeholder="Enter NIC number" required>
                  <button class="btn btn-primary" type="submit" style="min-width: 120px;">Search</button>
                </div>
              </form>
            </div>
          </div>
        </div>
        <div class="container mt-4 rounded border p-3" id="profileSection" style="display: none;">
          <input type="hidden" id="studentIdHidden" value="">
                      <ul class="nav nav-tabs" id="studentTabs">
                        <li class="nav-item">
                          <a class="nav-link active bg-primary text-white" id="personal-tab" data-bs-toggle="tab" href="#personal">Personal Info</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="parent-tab" data-bs-toggle="tab" href="#parent">Parent/Guardian Info</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="academic-tab" data-bs-toggle="tab" href="#academic">Academic</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="exams-tab" data-bs-toggle="tab" href="#exams">Exams Results</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="history-tab" data-bs-toggle="tab" href="#history">History</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="attendance-tab" data-bs-toggle="tab" href="#attendance">Attendance</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="payment-tab" data-bs-toggle="tab" href="#payment">Payment</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="clearance-tab" data-bs-toggle="tab" href="#clearance">Clearance</a>
                        </li>
                        <li class="nav-item">
                          <a class="nav-link" id="certificates-tab" data-bs-toggle="tab" href="#certificates">Certificates</a>
                        </li>
                      </ul>


                <div class="tab-content mt-2">
                  <!-- Personal Info Tab Content -->
                  <div class="tab-pane fade show active" id="personal">
                    <!-- Profile Picture -->
                    <div class="mb-3 mt-5 text-center position-relative">
                      <div class="d-flex justify-content-end">
                        <div class="rounded-circle overflow-hidden mx-auto mb-3 position-relative" style="width: 150px; height: 150px; border: 2px solid #ccc;">
                          <img src="{{asset('images/profile/user-1.jpg') }}" alt="User" width="150" height="150" class="rounded-circle">
                        </div>
                      </div>
                      <input type="file" class="form-control visually-hidden" id="profilePicture" accept="image/*">
                      <div class="d-flex justify-content-end mx-4">
                        <button type="button" class="btn btn-sm btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#editPictureModal">Edit Picture</button>
                      </div>
                    </div>
                    <!-- Personal Details Fields (screenshot style: label left, field right, both vertically centered) -->
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentTitle" class="col-sm-3 col-form-label fw-bold">Title</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentTitle" placeholder="Title" value="{{ $student->title ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentName" class="col-sm-3 col-form-label fw-bold">Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentName" placeholder="Student name" value="{{ $student->full_name ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentNIC" class="col-sm-3 col-form-label fw-bold">NIC</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentNIC" placeholder="NIC" value="{{ $student->id_value ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentIndexNo" class="col-sm-3 col-form-label fw-bold">Index Number</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentIndexNo" placeholder="Index Number" value="{{ $student->registration_id ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentInstitute" class="col-sm-3 col-form-label fw-bold">Institute</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentInstitute" placeholder="Institute" value="{{ $student->institute_location ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentDOB" class="col-sm-3 col-form-label fw-bold">Date of Birth</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentDOB" placeholder="Date of Birth" value="{{ $student->birthday ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentGender" class="col-sm-3 col-form-label fw-bold">Gender</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentGender" placeholder="Gender" value="{{ $student->gender ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentEmail" class="col-sm-3 col-form-label fw-bold">Email</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" id="studentEmail" placeholder="Email" value="{{ $student->email ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentMobile" class="col-sm-3 col-form-label fw-bold">Mobile Phone No</label>
                      <div class="col-sm-9">
                        <input type="tel" class="form-control" id="studentMobile" placeholder="Mobile Phone No" value="{{ $student->mobile_phone ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentHomePhone" class="col-sm-3 col-form-label fw-bold">Home Phone No</label>
                      <div class="col-sm-9">
                        <input type="tel" class="form-control" id="studentHomePhone" placeholder="Home Phone No" value="{{ $student->home_phone ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentEmergencyContact" class="col-sm-3 col-form-label fw-bold">Emergency Contact Number</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control bg-danger text-white" id="studentEmergencyContact" placeholder="Emergency Contact Number" value="{{ $student->emergency_contact_number ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentAddress" class="col-sm-3 col-form-label fw-bold">Address</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="studentAddress" rows="2" placeholder="Address" readonly>{{ $student->address ?? '' }}</textarea>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentFoundation" class="col-sm-3 col-form-label fw-bold">Foundation Program</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="studentFoundation" placeholder="Foundation Program" value="{{ $student->foundation_program ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentSpecialNeeds" class="col-sm-3 col-form-label fw-bold">Special Needs</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="studentSpecialNeeds" rows="2" placeholder="Special Needs" readonly>{{ $student->special_needs ?? '' }}</textarea>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                    <label for="studentExtraCurricular" class="col-sm-3 col-form-label fw-bold">Extra Curricular Activities</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="studentExtraCurricular" rows="2" placeholder="Extra Curricular Activities" readonly>{{ $student->extracurricular_activities ?? '' }}</textarea>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="studentFuturePotentials" class="col-sm-3 col-form-label fw-bold">Future Potentials</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="studentFuturePotentials" rows="2" placeholder="Future Potentials" readonly>{{ $student->future_potentials ?? '' }}</textarea>
                      </div>
                    </div>                  
                    <!-- Edit Personal Info Button -->
                    <div class="mt-4 mb-3">
                      <button type="button" class="btn btn-primary" id="showEditPersonalInfoBtn">Edit Personal Info</button>
                      <button type="button" class="btn btn-success ms-2" id="updatePersonalInfoBtn" style="display: none;">Update Personal Info</button>
                      <button type="button" class="btn btn-secondary ms-2" id="cancelEditBtn" style="display: none;">Cancel</button>
                    </div>
                  </div>

                  <!-- Parent/Guardian Info Tab Content -->
                  <div class="tab-pane fade" id="parent">
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentName" class="col-sm-3 col-form-label fw-bold">Name</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="parentName" placeholder="Parent/Guardian Name" value="{{ $student->parent->guardian_name ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentProfession" class="col-sm-3 col-form-label fw-bold">Profession</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control" id="parentProfession" placeholder="Profession" value="{{ $student->parent->guardian_profession ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentContactNo" class="col-sm-3 col-form-label fw-bold">Contact Number</label>
                      <div class="col-sm-9">
                        <input type="tel" class="form-control" id="parentContactNo" placeholder="Contact Number" value="{{ $student->parent->guardian_contact_number ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentEmail" class="col-sm-3 col-form-label fw-bold">Email</label>
                      <div class="col-sm-9">
                        <input type="email" class="form-control" id="parentEmail" placeholder="Email" value="{{ $student->parent->guardian_email ?? '' }}" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentAddress" class="col-sm-3 col-form-label fw-bold">Address</label>
                      <div class="col-sm-9">
                        <textarea class="form-control" id="parentAddress" rows="2" placeholder="Address" readonly>{{ $student->parent->guardian_address ?? '' }}</textarea>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="parentEmergencyContact" class="col-sm-3 col-form-label fw-bold">Emergency Contact Number</label>
                      <div class="col-sm-9">
                        <input type="text" class="form-control bg-danger text-white" id="parentEmergencyContact" placeholder="Emergency Contact Number" value="{{ $student->parent->emergency_contact_number ?? '' }}" readonly>
                      </div>
                    </div>
                    <!-- Edit Parent/Guardian Info Button -->
                    <div class="mt-4 mb-3">
                      <button type="button" class="btn btn-primary" id="showEditParentInfoBtn">Edit Parent/Guardian Info</button>
                      <button type="button" class="btn btn-success ms-2" id="updateParentInfoBtn" style="display: none;">Update Parent/Guardian Info</button>
                      <button type="button" class="btn btn-secondary ms-2" id="cancelEditParentBtn" style="display: none;">Cancel</button>
                    </div>
                  </div>

                  <!-- Academic Tab Content -->
                  <div class="tab-pane fade" id="academic">
                    @php
                        $ol_pending = true;
                        $al_pending = true;
                        $ol_exam = null;
                        $al_exam = null;

                        if (isset($student->exams) && !$student->exams->isEmpty()) {
                            $exam = $student->exams->first();
                            if ($exam) {
                                $ol_subjects = is_array($exam->ol_exam_subjects) ? $exam->ol_exam_subjects : json_decode($exam->ol_exam_subjects, true);
                                if (!empty($ol_subjects)) {
                                    $ol_pending = false;
                                    $ol_exam = $exam;
                                }

                                $al_subjects = is_array($exam->al_exam_subjects) ? $exam->al_exam_subjects : json_decode($exam->al_exam_subjects, true);
                                if (!empty($al_subjects)) {
                                    $al_pending = false;
                                    $al_exam = $exam;
                                }
                            }
                        }
                    @endphp

                    @if ($ol_pending)
                        <div class="alert alert-warning mb-3">
                            <strong>Pending Results:</strong> The student's O/L exam results are still pending.
                        </div>
                    @else
                        <div id="olExamSection">
                            <h5 class="mt-4 mb-3 fw-bold">O/L Exam Details</h5>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="olIndexNo" class="col-sm-3 col-form-label fw-bold">Index No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="olIndexNo"
                                        placeholder="O/L Index No"
                                        value="{{ $ol_exam->ol_index_no ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="olExamType" class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="olExamType"
                                        placeholder="O/L Exam Type"
                                        value="{{ $ol_exam->ol_exam_type ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="olExamYear" class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="olExamYear"
                                        placeholder="O/L Exam Year"
                                        value="{{ $ol_exam->ol_exam_year ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                                <div class="col-sm-9">
                                    <table class="table table-bordered mb-0">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (json_decode($ol_exam->ol_exam_subjects, true) ?? [] as $subject)
                                                <tr>
                                                    <td>{{ $subject['subject'] ?? '' }}</td>
                                                    <td>{{ $subject['result'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
                                <div class="col-sm-9">
                                    @if (!empty($ol_exam->ol_certificate))
                                        <a href="{{ asset('storage/certificates/' . $ol_exam->ol_certificate) }}"
                                            target="_blank">View Certificate</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($al_pending)
                        <div class="alert alert-warning mb-3">
                            <strong>Pending Results:</strong> The student's A/L exam results are still pending.
                        </div>
                    @else
                        <div id="alExamSection">
                            <hr>
                            <h5 class="mt-4 mb-3 fw-bold">A/L Exam Details</h5>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="alIndexNo" class="col-sm-3 col-form-label fw-bold">Index No.</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="alIndexNo"
                                        placeholder="A/L Index No"
                                        value="{{ $al_exam->al_index_no ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="alExamType" class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="alExamType"
                                        placeholder="A/L Exam Type"
                                        value="{{ $al_exam->al_exam_type ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="alExamYear" class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="alExamYear"
                                        placeholder="A/L Exam Year"
                                        value="{{ $al_exam->al_exam_year ?? '' }}" readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label for="alStream" class="col-sm-3 col-form-label fw-bold">A/L Stream</label>
                                <div class="col-sm-9">
                                    <input type="text" class="form-control" id="alStream"
                                        placeholder="A/L Stream" value="{{ $al_exam->al_stream ?? '' }}"
                                        readonly>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                                <div class="col-sm-9">
                                    <table class="table table-bordered mb-0">
                                        <thead class="bg-primary text-white">
                                            <tr>
                                                <th>Subject</th>
                                                <th>Result</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (json_decode($al_exam->al_exam_subjects, true) ?? [] as $subject)
                                                <tr>
                                                    <td>{{ $subject['subject'] ?? '' }}</td>
                                                    <td>{{ $subject['result'] ?? '' }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center mx-3">
                                <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
                                <div class="col-sm-9">
                                    @if (!empty($al_exam->al_certificate))
                                        <a href="{{ asset('storage/certificates/' . $al_exam->al_certificate) }}"
                                            target="_blank">View Certificate</a>
                                    @else
                                        <span class="text-muted">Not uploaded</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                  <!-- Exams Tab Content -->
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
                          <tr>
                            <th>Module Name</th>
                            <th>Marks</th>
                            <th>Grade</th>
                          </tr>
                        </thead>
                        <tbody id="examResultsTableBody">
                          <!-- Results will be populated here -->
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Attendance Tab Content -->
                  <div class="tab-pane fade" id="attendance">
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <label for="attendanceCourseSelect" class="form-label fw-bold">Select Course</label>
                        <select id="attendanceCourseSelect" class="form-select">
                          <option value="">Select a course</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="attendanceSemesterSelect" class="form-label fw-bold">Select Semester</label>
                        <select id="attendanceSemesterSelect" class="form-select" disabled>
                          <option value="">Select a semester</option>
                        </select>
                      </div>
                    </div>
                    <div id="attendanceTableWrapper" style="display:none;">
                      <h5 class="fw-bold mb-3">Module Attendance</h5>
                      <table class="table table-bordered">
                        <thead class="bg-primary text-white">
                          <tr>
                            <th>Module Name</th>
                            <th>Total Days</th>
                            <th>Present Days</th>
                            <th>Absent Days</th>
                            <th>Attendance %</th>
                          </tr>
                        </thead>
                        <tbody id="attendanceTableBody">
                          <!-- Attendance will be populated here -->
                        </tbody>
                      </table>
                    </div>
                  </div>

                  <!-- Payment Tab Content -->
                  <div class="tab-pane fade" id="payment">
                    <div class="row mb-3">
                      <div class="col-md-6">
                        <label for="paymentCourseSelect" class="form-label fw-bold">Select Course</label>
                        <select id="paymentCourseSelect" class="form-select">
                          <option value="">Select a course</option>
                        </select>
                      </div>
                      <div class="col-md-6">
                        <label for="paymentIntakeSelect" class="form-label fw-bold">Select Intake</label>
                        <select id="paymentIntakeSelect" class="form-select" disabled>
                          <option value="">Select an intake</option>
                        </select>
                      </div>
                    </div>
                    <div id="paymentTableWrapper" style="display:none;">
                      <h5 class="fw-bold mb-3">Payment Details</h5>
                      <div class="row">
                        <div class="col-md-6">
                          <div class="card">
                            <div class="card-header bg-primary text-white">
                              <h6 class="mb-0">Payment Summary</h6>
                            </div>
                            <div class="card-body">
                              <div class="row mb-2">
                                <div class="col-6"><strong>Total Fee:</strong></div>
                                <div class="col-6" id="totalFee">-</div>
                              </div>
                              <div class="row mb-2">
                                <div class="col-6"><strong>Paid Amount:</strong></div>
                                <div class="col-6" id="paidAmount">-</div>
                              </div>
                              <div class="row mb-2">
                                <div class="col-6"><strong>Balance:</strong></div>
                                <div class="col-6" id="balance">-</div>
                              </div>
                              <div class="row mb-2">
                                <div class="col-6"><strong>Status:</strong></div>
                                <div class="col-6" id="paymentStatus">-</div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="col-md-6">
                          <div class="card">
                            <div class="card-header bg-success text-white">
                              <h6 class="mb-0">Payment History</h6>
                            </div>
                            <div class="card-body">
                              <div id="paymentHistory">
                                <p class="text-muted">Select a course and intake to view payment history.</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="mt-3">
                        <h6 class="fw-bold">Payment Schedule</h6>
                        <table class="table table-bordered">
                          <thead class="bg-info text-white">
                            <tr>
                              <th>Due Date</th>
                              <th>Amount</th>
                              <th>Status</th>
                              <th>Payment Date</th>
                              <th>Receipt</th>
                            </tr>
                          </thead>
                          <tbody id="paymentScheduleTableBody">
                            <!-- Payment schedule will be populated here -->
                          </tbody>
                        </table>
                      </div>
                    </div>
                  </div>

                  <!-- Clearance Tab Content -->
                  <div class="tab-pane fade" id="clearance">
                    <h5 class="fw-bold mb-3">Student Clearance Status</h5>
                    <table class="table table-bordered">
                      <thead class="bg-primary text-white">
                        <tr>
                          <th>Clearance Type</th>
                          <th>Status</th>
                          <th>Approved Date</th>
                          <th>Remarks</th>
                          <th>Uploaded Document</th>
                        </tr>
                      </thead>
                      <tbody id="clearanceTableBody">
                        <!-- Populated by JS -->
                      </tbody>
                    </table>
                  </div>

                  <!-- Certificates Tab Content -->
                  <div class="tab-pane fade" id="certificates">
                    <h5 class="mt-4 mb-3 fw-bold">Certificates</h5>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="olCertificate" class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
                      
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="alCertificate" class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
                    </div>
                  </div>

                  <!-- History Tab Content -->
                  <div class="tab-pane fade" id="history">
                    <h5 class="fw-bold mb-3">Course Registration History</h5>
                    <table class="table table-bordered">
                      <thead class="bg-primary text-white">
                        <tr>
                          <th>Course</th>
                          <th>Intake</th>
                          <th>Start Date</th>
                          <th>End Date</th>
                          <th>Status</th>
                        </tr>
                      </thead>
                      <tbody id="historyTableBody">
                        <!-- Populated by JS -->
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>


<script>
// Success and Error Message Functions
function showSuccessMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `
        <i class="ti ti-check-circle success-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(successDiv);
    
    // Show the message
    setTimeout(() => successDiv.classList.add('show'), 100);
    
    // Hide after 4 seconds
    setTimeout(() => {
        successDiv.classList.remove('show');
        setTimeout(() => successDiv.remove(), 300);
    }, 4000);
}

function showErrorMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `
        <i class="ti ti-alert-circle error-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(errorDiv);
    
    // Show the message
    setTimeout(() => errorDiv.classList.add('show'), 100);
    
    // Hide after 5 seconds
    setTimeout(() => {
        errorDiv.classList.remove('show');
        setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
}

  $(document).ready(function () {
    // Restore last active tab from localStorage
    var lastTab = localStorage.getItem('studentProfileActiveTab');
    if (lastTab) {
      var tabTrigger = document.querySelector('a[href="' + lastTab + '"]');
      if (tabTrigger) {
        var tab = new bootstrap.Tab(tabTrigger);
        tab.show();
      }
    }

    // Save active tab to localStorage on click
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
      localStorage.setItem('studentProfileActiveTab', $(e.target).attr('href'));
    });

    // Show profile section if $student is set on page load
    @if(isset($student))
      populateStudentProfile(@json($student));
      $('#profileSection').show();
    @endif

    function populateStudentProfile(student) {
    $('#academic').empty();

    let ol_exam = null;
    let al_exam = null;
    let ol_pending = true;
    let al_pending = true;

    if (student.exams && student.exams.length > 0) {
      student.exams.forEach(exam => {
        // O/L
        let ol_subjects = exam.ol_exam_subjects;
        if (typeof ol_subjects === 'string') {
          try { ol_subjects = JSON.parse(ol_subjects); } catch (e) { ol_subjects = []; }
        }
        if (ol_subjects && ol_subjects.length > 0) {
          ol_exam = exam;
          ol_pending = false;
        }
        // A/L
        let al_subjects = exam.al_exam_subjects;
        if (typeof al_subjects === 'string') {
          try { al_subjects = JSON.parse(al_subjects); } catch (e) { al_subjects = []; }
        }
        if (al_subjects && al_subjects.length > 0) {
          al_exam = exam;
          al_pending = false;
        }
      });
    }

    // O/L Section
    if (ol_pending) {
      $('#academic').append(
        '<div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student\'s O/L exam results are still pending.</div>'
      );
    } else if (ol_exam) {
      let olSubjectsHtml = '';
      let ol_subjects = typeof ol_exam.ol_exam_subjects === 'string' ? JSON.parse(ol_exam.ol_exam_subjects) : ol_exam.ol_exam_subjects;
      (ol_subjects || []).forEach(subject => {
        olSubjectsHtml += `<tr><td>${subject.subject || ''}</td><td>${subject.result || ''}</td></tr>`;
      });
      const olCertificateHtml = ol_exam.ol_certificate
        ? `<a href="/storage/certificates/${ol_exam.ol_certificate}" target="_blank">View Certificate</a>`
        : '<span class="text-muted">Not uploaded</span>';
      $('#academic').append(`
        <div id="olExamSection">
          <h5 class="mt-4 mb-3 fw-bold">O/L Exam Details</h5>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${ol_exam.ol_index_no || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${ol_exam.ol_exam_type || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${ol_exam.ol_exam_year || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
            <div class="col-sm-9">
              <table class="table table-bordered mb-0">
                <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                <tbody>${olSubjectsHtml}</tbody>
              </table>
            </div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
            <div class="col-sm-9">${olCertificateHtml}</div>
          </div>
        </div>
      `);
    }

    // A/L Section
    if (al_pending) {
      $('#academic').append(
        '<div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student\'s A/L exam results are still pending.</div>'
      );
    } else if (al_exam) {
      let alSubjectsHtml = '';
      let al_subjects = typeof al_exam.al_exam_subjects === 'string' ? JSON.parse(al_exam.al_exam_subjects) : al_exam.al_exam_subjects;
      (al_subjects || []).forEach(subject => {
        alSubjectsHtml += `<tr><td>${subject.subject || ''}</td><td>${subject.result || ''}</td></tr>`;
      });
      const alCertificateHtml = al_exam.al_certificate
        ? `<a href="/storage/certificates/${al_exam.al_certificate}" target="_blank">View Certificate</a>`
        : '<span class="text-muted">Not uploaded</span>';
      $('#academic').append(`
        <div id="alExamSection">
          <hr>
          <h5 class="mt-4 mb-3 fw-bold">A/L Exam Details</h5>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${al_exam.al_index_no || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${al_exam.al_exam_type || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${al_exam.al_exam_year || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">A/L Stream</label>
            <div class="col-sm-9"><input type="text" class="form-control" value="${al_exam.al_stream || ''}" readonly></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
            <div class="col-sm-9">
              <table class="table table-bordered mb-0">
                <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                <tbody>${alSubjectsHtml}</tbody>
              </table>
            </div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
            <div class="col-sm-9">${alCertificateHtml}</div>
          </div>
        </div>
      `);
    }

    // Populate all personal detail fields
      $('#studentIdHidden').val(student.student_id || '');
      $('#studentTitle').val(student.title || '');
      $('#studentName').val(student.full_name || '');
      $('#studentNIC').val(student.id_value || '');
      $('#studentIndexNo').val(student.registration_id || '');
      $('#studentInstitute').val(student.institute_location || '');
      $('#studentDOB').val(student.birthday || '');
      $('#studentGender').val(student.gender || '');
      $('#studentEmail').val(student.email || '');
      $('#studentMobile').val(student.mobile_phone || '');
      $('#studentHomePhone').val(student.home_phone || '');
      $('#studentEmergencyContact').val(student.emergency_contact_number || '');
      $('#studentAddress').val(student.address || '');
      $('#studentFoundation').val(student.foundation_program || '');
      $('#studentSpecialNeeds').val(student.special_needs || '');
      $('#studentExtraCurricular').val(student.extracurricular_activities || '');
      $('#studentFuturePotentials').val(student.future_potentials || '');
      
      // Populate parent/guardian fields
      console.log('Student parent data:', student.parent);
      if (student.parent) {
        $('#parentName').val(student.parent.guardian_name || '');
        $('#parentProfession').val(student.parent.guardian_profession || '');
        $('#parentContactNo').val(student.parent.guardian_contact_number || '');
        $('#parentEmail').val(student.parent.guardian_email || '');
        $('#parentAddress').val(student.parent.guardian_address || '');
        $('#parentEmergencyContact').val(student.parent.emergency_contact_number || '');
      } else {
        console.log('No parent data found for student');
      }

      // Populate academic fields
      if (student.exams && student.exams.length > 0) {
        $('#olIndexNo').val(student.exams[0].ol_index_no || '');
        $('#olExamType').val(student.exams[0].ol_exam_type || '');
        $('#olExamYear').val(student.exams[0].ol_exam_year || '');
        $('#olCertificate').html(student.exams[0].ol_certificate ? `<a href="/storage/certificates/${student.exams[0].ol_certificate}" target="_blank">View Certificate</a>` : '<span class="text-muted">Not uploaded</span>');
      }
      if (student.exams && student.exams.length > 1) {
        $('#alIndexNo').val(student.exams[1].al_index_no || '');
        $('#alExamType').val(student.exams[1].al_exam_type || '');
        $('#alExamYear').val(student.exams[1].al_exam_year || '');
        $('#alStream').val(student.exams[1].al_stream || '');
        $('#alCertificate').html(student.exams[1].al_certificate ? `<a href="/storage/certificates/${student.exams[1].al_certificate}" target="_blank">View Certificate</a>` : '<span class="text-muted">Not uploaded</span>');
      }
  }

    // Handle Edit Personal Info button click
    $('#showEditPersonalInfoBtn').on('click', function() {
      // Make all fields editable
      $('#studentTitle, #studentName, #studentNIC, #studentIndexNo, #studentInstitute, #studentDOB, #studentGender, #studentEmail, #studentMobile, #studentHomePhone, #studentEmergencyContact, #studentAddress, #studentFoundation, #studentSpecialNeeds, #studentExtraCurricular, #studentFuturePotentials').prop('readonly', false);
      
      // Show update and cancel buttons, hide edit button
      $('#showEditPersonalInfoBtn').hide();
      $('#updatePersonalInfoBtn, #cancelEditBtn').show();
    });

    // Handle Cancel button click
    $('#cancelEditBtn').on('click', function() {
      // Make all fields readonly again
      $('#studentTitle, #studentName, #studentNIC, #studentIndexNo, #studentInstitute, #studentDOB, #studentGender, #studentEmail, #studentMobile, #studentHomePhone, #studentEmergencyContact, #studentAddress, #studentFoundation, #studentSpecialNeeds, #studentExtraCurricular, #studentFuturePotentials').prop('readonly', true);
      
      // Show edit button, hide update and cancel buttons
      $('#showEditPersonalInfoBtn').show();
      $('#updatePersonalInfoBtn, #cancelEditBtn').hide();
    });

  // Handle Update Personal Info button click
  $('#updatePersonalInfoBtn').on('click', function() {
                      const studentId = $('#studentIdHidden').val();
                      if (!studentId) {
      alert('No student selected.');
                        return;
                      }

    // Collect all form data
    const formData = {
      student_id: studentId,
      title: $('#studentTitle').val(),
      full_name: $('#studentName').val(),
      id_value: $('#studentNIC').val(),
      registration_id: $('#studentIndexNo').val(),
      institute_location: $('#studentInstitute').val(),
      birthday: $('#studentDOB').val(),
      gender: $('#studentGender').val(),
      email: $('#studentEmail').val(),
      mobile_phone: $('#studentMobile').val(),
      home_phone: $('#studentHomePhone').val(),
      emergency_contact_number: $('#studentEmergencyContact').val(),
      address: $('#studentAddress').val(),
      foundation_program: $('#studentFoundation').val(),
      special_needs: $('#studentSpecialNeeds').val(),
      extracurricular_activities: $('#studentExtraCurricular').val(),
      future_potentials: $('#studentFuturePotentials').val(),
      _token: '{{ csrf_token() }}'
    };

    // Send update request
                      $.ajax({
      url: "{{ route('student.update.personal.info') }}",
      type: "POST",
      data: formData,
                        success: function(response) {
        if (response.success) {
          showSuccessMessage('Personal information updated successfully!');
          // Make fields readonly again
          $('#studentTitle, #studentName, #studentNIC, #studentIndexNo, #studentInstitute, #studentDOB, #studentGender, #studentEmail, #studentMobile, #studentHomePhone, #studentEmergencyContact, #studentAddress, #studentFoundation, #studentSpecialNeeds, #studentExtraCurricular, #studentFuturePotentials').prop('readonly', true);
          
          // Show edit button, hide update and cancel buttons
          $('#showEditPersonalInfoBtn').show();
          $('#updatePersonalInfoBtn, #cancelEditBtn').hide();
                          } else {
          showErrorMessage(response.message || 'Failed to update personal information.');
        }
      },
      error: function(xhr, status, error) {
        showErrorMessage('An error occurred while updating personal information.');
      }
    });
      });

  // Handle Edit Parent/Guardian Info button click
  $('#showEditParentInfoBtn').on('click', function() {
    // Make all parent/guardian fields editable
    $('#parentName, #parentProfession, #parentContactNo, #parentEmail, #parentAddress, #parentEmergencyContact').prop('readonly', false);
    
    // Show update and cancel buttons, hide edit button
    $('#showEditParentInfoBtn').hide();
    $('#updateParentInfoBtn, #cancelEditParentBtn').show();
  });

  // Handle Cancel Parent/Guardian button click
  $('#cancelEditParentBtn').on('click', function() {
    // Make all parent/guardian fields readonly again
    $('#parentName, #parentProfession, #parentContactNo, #parentEmail, #parentAddress, #parentEmergencyContact').prop('readonly', true);
    
    // Show edit button, hide update and cancel buttons
    $('#showEditParentInfoBtn').show();
    $('#updateParentInfoBtn, #cancelEditParentBtn').hide();
  });

  // Handle Update Parent/Guardian Info button click
  $('#updateParentInfoBtn').on('click', function() {
    const studentId = $('#studentIdHidden').val();
    if (!studentId) {
      alert('No student selected.');
      return;
    }

    // Collect all parent/guardian form data
    const formData = {
      student_id: studentId,
      guardian_name: $('#parentName').val(),
      guardian_profession: $('#parentProfession').val(),
      guardian_contact_number: $('#parentContactNo').val(),
      guardian_email: $('#parentEmail').val(),
      guardian_address: $('#parentAddress').val(),
      emergency_contact_number: $('#parentEmergencyContact').val(),
      _token: '{{ csrf_token() }}'
    };

    // Send update request
        $.ajax({
      url: "{{ route('student.update.parent.info') }}",
          type: "POST",
          data: formData,
      success: function(response) {
            if (response.success) {
          showSuccessMessage('Parent/Guardian information updated successfully!');
          // Make fields readonly again
          $('#parentName, #parentProfession, #parentContactNo, #parentEmail, #parentAddress, #parentEmergencyContact').prop('readonly', true);
          
          // Show edit button, hide update and cancel buttons
          $('#showEditParentInfoBtn').show();
          $('#updateParentInfoBtn, #cancelEditParentBtn').hide();
            } else {
          showErrorMessage(response.message || 'Failed to update parent/guardian information.');
        }
      },
      error: function(xhr, status, error) {
        showErrorMessage('An error occurred while updating parent/guardian information.');
      }
    });
    });

  // Exam Results Tab Logic
  function getStudentId() {
    return $('#studentIdHidden').val();
  }

  function fetchRegisteredCourses() {
    const studentId = getStudentId();
    if (!studentId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/courses',
      method: 'GET',
      success: function (res) {
        const $select = $('#examCourseSelect');
        $select.empty().append('<option value="">Select a course</option>');
        if (res.success && res.courses.length) {
          res.courses.forEach(c => {
            $select.append(`<option value="${c.course_id}">${c.course_name}</option>`);
          });
        }
      }
    });
  }

  function fetchSemesters(courseId) {
    const studentId = getStudentId();
    if (!studentId || !courseId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/semesters',
      method: 'GET',
      success: function (res) {
        const $select = $('#examSemesterSelect');
        $select.empty().append('<option value="">Select a semester</option>');
        if (res.success && res.semesters.length) {
          res.semesters.forEach(s => {
            $select.append(`<option value="${s}">${s}</option>`);
          });
          $select.prop('disabled', false);
        } else {
          $select.prop('disabled', true);
        }
      }
    });
  }

  function fetchModuleResults(courseId, semester) {
    const studentId = getStudentId();
    if (!studentId || !courseId || !semester) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/semester/' + semester + '/results',
      method: 'GET',
      success: function (res) {
        const $tbody = $('#examResultsTableBody');
        $tbody.empty();
        if (res.success && res.results.length) {
          res.results.forEach(r => {
            $tbody.append(`<tr><td>${r.module_name}</td><td>${r.marks}</td><td>${r.grade}</td></tr>`);
          });
          $('#examResultsTableWrapper').show();
        } else {
          $tbody.append('<tr><td colspan="3" class="text-center">No results found.</td></tr>');
          $('#examResultsTableWrapper').show();
        }
      }
    });
  }

  // When Exams tab is shown, fetch courses
  $('a[data-bs-toggle="tab"][href="#exams"]').on('shown.bs.tab', function () {
    fetchRegisteredCourses();
    $('#examSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled', true);
    $('#examResultsTableWrapper').hide();
  });

  // On course change, fetch semesters
  $('#examCourseSelect').on('change', function () {
    const courseId = $(this).val();
    if (courseId) {
      fetchSemesters(courseId);
      $('#examResultsTableWrapper').hide();
    } else {
      $('#examSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled', true);
      $('#examResultsTableWrapper').hide();
    }
  });

  // On semester change, fetch results
  $('#examSemesterSelect').on('change', function () {
    const courseId = $('#examCourseSelect').val();
    const semester = $(this).val();
    if (courseId && semester) {
      fetchModuleResults(courseId, semester);
    } else {
      $('#examResultsTableWrapper').hide();
    }
  });
  

  // Attendance Tab Logic
  function getStudentId() {
    return $('#studentIdHidden').val();
  }

  function fetchAttendanceCourses() {
    const studentId = getStudentId();
    if (!studentId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/courses',
      method: 'GET',
      success: function (res) {
        const $select = $('#attendanceCourseSelect');
        $select.empty().append('<option value="">Select a course</option>');
        if (res.success && res.courses.length) {
          res.courses.forEach(c => {
            $select.append(`<option value="${c.course_id}">${c.course_name}</option>`);
          });
        }
      }
    });
  }

  function fetchAttendanceSemesters(courseId) {
    const studentId = getStudentId();
    if (!studentId || !courseId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/semesters',
      method: 'GET',
      success: function (res) {
        const $select = $('#attendanceSemesterSelect');
        $select.empty().append('<option value="">Select a semester</option>');
        if (res.success && res.semesters.length) {
          res.semesters.forEach(s => {
            $select.append(`<option value="${s}">${s}</option>`);
          });
          $select.prop('disabled', false);
        } else {
          $select.prop('disabled', true);
        }
      }
    });
  }

  function fetchAttendanceTable(courseId, semester) {
    const studentId = getStudentId();
    if (!studentId || !courseId || !semester) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/semester/' + semester + '/attendance',
      method: 'GET',
      success: function (res) {
        const $tbody = $('#attendanceTableBody');
        $tbody.empty();
        if (res.success && res.attendance.length) {
          res.attendance.forEach(a => {
            $tbody.append(`<tr>
              <td>${a.module_name}</td>
              <td>${a.total_days}</td>
              <td>${a.present_days}</td>
              <td>${a.absent_days}</td>
              <td>${a.attendance_percent}</td>
            </tr>`);
          });
          $('#attendanceTableWrapper').show();
        } else {
          $tbody.append('<tr><td colspan="5" class="text-center">No attendance data found.</td></tr>');
          $('#attendanceTableWrapper').show();
        }
      }
    });
  }

  // Populate courses on tab show
  $('a[data-bs-toggle="tab"][href="#attendance"]').on('shown.bs.tab', function () {
    fetchAttendanceCourses();
    $('#attendanceSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled', true);
    $('#attendanceTableWrapper').hide();
  });

  // On course change, fetch semesters
  $('#attendanceCourseSelect').on('change', function () {
    const courseId = $(this).val();
    if (courseId) {
      fetchAttendanceSemesters(courseId);
      $('#attendanceTableWrapper').hide();
    } else {
      $('#attendanceSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled', true);
      $('#attendanceTableWrapper').hide();
    }
  });

  // On semester change, fetch attendance
  $('#attendanceSemesterSelect').on('change', function () {
    const courseId = $('#attendanceCourseSelect').val();
    const semester = $(this).val();
    if (courseId && semester) {
      fetchAttendanceTable(courseId, semester);
    } else {
      $('#attendanceTableWrapper').hide();
    }
  });

  // Payment Tab Logic
  function fetchPaymentCourses() {
    const studentId = getStudentId();
    if (!studentId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/courses',
      method: 'GET',
      success: function (res) {
        const $select = $('#paymentCourseSelect');
        $select.empty().append('<option value="">Select a course</option>');
        if (res.success && res.courses.length) {
          res.courses.forEach(c => {
            $select.append(`<option value="${c.course_id}">${c.course_name}</option>`);
          });
        }
      }
    });
  }

  function fetchPaymentIntakes(courseId) {
    const studentId = getStudentId();
    if (!studentId || !courseId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/intakes',
      method: 'GET',
      success: function (res) {
        const $select = $('#paymentIntakeSelect');
        $select.empty().append('<option value="">Select an intake</option>');
        if (res.success && res.intakes.length) {
          res.intakes.forEach(i => {
            $select.append(`<option value="${i}">${i}</option>`);
          });
          $select.prop('disabled', false);
        } else {
          $select.prop('disabled', true);
        }
      }
    });
  }

  function fetchPaymentDetails(courseId, intake) {
    const studentId = getStudentId();
    if (!studentId || !courseId || !intake) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/intake/' + intake + '/payment-details',
      method: 'GET',
      success: function (res) {
        if (res.success) {
          $('#totalFee').text(res.total_fee || 'N/A');
          $('#paidAmount').text(res.paid_amount || '0');
          $('#balance').text(res.balance || '0');
          $('#paymentStatus').text(res.payment_status || 'N/A');
          $('#paymentTableWrapper').show();
        } else {
          $('#paymentTableWrapper').hide();
        }
      }
    });
  }

  function fetchPaymentHistory(courseId, intake) {
    const studentId = getStudentId();
    if (!studentId || !courseId || !intake) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/intake/' + intake + '/payment-history',
      method: 'GET',
      success: function (res) {
        const $historyDiv = $('#paymentHistory');
        $historyDiv.empty();
        if (res.success && res.history && res.history.length) {
          res.history.forEach(payment => {
            $historyDiv.append(`<p class="mb-1"><strong>Date:</strong> ${payment.payment_date || 'N/A'}</p>
                                <p class="mb-1"><strong>Amount:</strong> ${payment.amount || '0'}</p>
                                <p class="mb-1"><strong>Method:</strong> ${payment.payment_method || 'N/A'}</p>
                                <p class="mb-1"><strong>Receipt:</strong> ${payment.receipt_url ? `<a href="${payment.receipt_url}" target="_blank">View Receipt</a>` : 'N/A'}</p>
                                <hr class="my-2">`);
          });
        } else {
          $historyDiv.append('<p class="text-muted">No payment history found for this intake.</p>');
        }
      }
    });
  }

  function fetchPaymentSchedule(courseId, intake) {
    const studentId = getStudentId();
    if (!studentId || !courseId || !intake) return;
    $.ajax({
      url: '/api/student/' + studentId + '/course/' + courseId + '/intake/' + intake + '/payment-schedule',
      method: 'GET',
      success: function (res) {
        const $tbody = $('#paymentScheduleTableBody');
        $tbody.empty();
        if (res.success && res.schedule.length) {
          res.schedule.forEach(payment => {
            $tbody.append(`<tr>
              <td>${payment.due_date || 'N/A'}</td>
              <td>${payment.amount || '0'}</td>
              <td>${payment.status || 'N/A'}</td>
              <td>${payment.payment_date || 'N/A'}</td>
              <td>${payment.receipt_url ? `<a href="${payment.receipt_url}" target="_blank">View Receipt</a>` : 'N/A'}</td>
            </tr>`);
          });
        } else {
          $tbody.append('<tr><td colspan="5" class="text-center">No payment schedule found for this intake.</td></tr>');
        }
      }
    });
  }

  // Populate courses on tab show
  $('a[data-bs-toggle="tab"][href="#payment"]').on('shown.bs.tab', function () {
    fetchPaymentCourses();
    $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled', true);
    $('#paymentTableWrapper').hide();
    $('#paymentHistory').empty();
    $('#paymentScheduleTableBody').empty();
  });

  // On course change, fetch intakes
  $('#paymentCourseSelect').on('change', function () {
    const courseId = $(this).val();
    if (courseId) {
      fetchPaymentIntakes(courseId);
      $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled', true);
      $('#paymentTableWrapper').hide();
      $('#paymentHistory').empty();
      $('#paymentScheduleTableBody').empty();
    } else {
      $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled', true);
      $('#paymentTableWrapper').hide();
      $('#paymentHistory').empty();
      $('#paymentScheduleTableBody').empty();
    }
  });

  // On intake change, fetch payment details, history, and schedule
  $('#paymentIntakeSelect').on('change', function () {
    const courseId = $('#paymentCourseSelect').val();
    const intake = $(this).val();
    if (courseId && intake) {
      fetchPaymentDetails(courseId, intake);
      fetchPaymentHistory(courseId, intake);
      fetchPaymentSchedule(courseId, intake);
    } else {
      $('#paymentTableWrapper').hide();
      $('#paymentHistory').empty();
      $('#paymentScheduleTableBody').empty();
    }
  });


  // Clearance Tab Logic
  function fetchStudentClearances() {
    const studentId = $('#studentIdHidden').val();
    if (!studentId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/clearances',
      method: 'GET',
      success: function (res) {
        const $tbody = $('#clearanceTableBody');
        $tbody.empty();
        if (res.success && res.clearances && res.clearances.length) {
          res.clearances.forEach(info => {
            $tbody.append(`<tr>
              <td>${info.label}</td>
              <td>${info.status ? '<span class="badge bg-success">Approved</span>' : '<span class="badge bg-warning text-dark">Pending</span>'}</td>
              <td>${info.approved_date ? info.approved_date : 'N/A'}</td>
              <td>${info.remarks ? info.remarks : '-'}</td>
              <td>
                <a href="/storage/${info.clearance_slip || ''}" target="_blank" class="btn btn-outline-primary btn-sm"
                  ${info.clearance_slip ? '' : 'disabled'}>
                  <i class="ti ti-download"></i> Download
                </a>
                ${!info.clearance_slip ? '<span class="text-muted ms-2">No Document</span>' : ''}
              </td>
            </tr>`);
          });
          // If no uploaded documents, show message
          if ($tbody.children().length === 0) {
            $tbody.append('<tr><td colspan="5" class="text-center">No uploaded clearance documents found.</td></tr>');
          }
        } else {
          $tbody.append('<tr><td colspan="5" class="text-center">No clearance data found.</td></tr>');
        }
      }
    });
  }

  // Fetch clearances when the Clearance tab is shown
  $('a[data-bs-toggle="tab"][href="#clearance"]').on('shown.bs.tab', function () {
    fetchStudentClearances();
  });

  function fetchStudentHistory() {
    const studentId = getStudentId();
    if (!studentId) return;
    $.ajax({
      url: '/api/student/' + studentId + '/history',
      method: 'GET',
      success: function (res) {
        const $tbody = $('#historyTableBody');
        $tbody.empty();
        if (res.success && res.history && res.history.length) {
          res.history.forEach(item => {
            $tbody.append(`<tr>
              <td>${item.course_name}</td>
              <td>${item.intake}</td>
              <td>${item.start_date}</td>
              <td>${item.end_date}</td>
              <td>${item.status}</td>
            </tr>`);
          });
        } else {
          $tbody.append('<tr><td colspan="5" class="text-center">No registration history found.</td></tr>');
        }
      }
    });
  }
  // Fetch history when the History tab is shown
  $('a[data-bs-toggle="tab"][href="#history"]').on('shown.bs.tab', function () {
    fetchStudentHistory();
  });

 $('#nicSearchForm').on('submit', function(e) {
    e.preventDefault();
    const nic = $('#nicInput').val().trim();
    if (!nic) return;
    $.ajax({
      url: '/api/student-details-by-nic',
      method: 'GET',
      data: { nic: nic },
      success: function(res) {
        if (res.success && res.student) {
          populateStudentProfile(res.student);
          $('#studentIdHidden').val(res.student.student_id);
          $('#profileSection').show();
          $('#personal-tab').tab('show');
          fetchRegisteredCourses();
        } else {
          $('#profileSection').hide();
          alert('Student not found!');
        }
      },
      error: function() {
        $('#profileSection').hide();
        alert('Error fetching student details.');
      }
    });
  });

  // Tab coloring logic
  $('#studentTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
    $('#studentTabs a.nav-link').removeClass('bg-primary text-white');
    $(e.target).addClass('bg-primary text-white');
  });
});
  </script>
  @endsection