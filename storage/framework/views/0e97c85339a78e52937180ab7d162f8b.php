

<?php $__env->startSection('title', 'NEBULA | Student Profile'); ?>

<?php $__env->startSection('content'); ?>
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

<?php
  $status = $student->academic_status ?? 'active';
?>

<div class="container-fluid">
  <div class="row justify-content-center mt-4">
    <div class="col-md-11">
      <div class="p-4 rounded shadow w-100 bg-white">
        <?php if(session('success')): ?>
          <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>
        <?php if(session('error')): ?>
          <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
        <?php endif; ?>

        <h2 class="text-center mb-4">Student Profile</h2>
        <hr style="margin-bottom:30px;">

        
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

        <div class="container mt-4 rounded border p-3" id="profileSection" style="<?php echo e(isset($student) ? '' : 'display:none;'); ?>">
          <input type="hidden" id="studentIdHidden" value="<?php echo e($student->student_id ?? ''); ?>">

          
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
            
            <div class="tab-pane fade show active" id="personal">
              
              <div class="d-flex align-items-center justify-content-between mt-3 mb-3 px-2">
                <div>
                  <span class="fw-bold me-2">Academic Status:</span>
                  <span id="studentStatusBadge" class="badge <?php echo e(strtolower($status)==='terminated' ? 'bg-danger' : 'bg-success'); ?>"><?php echo e(strtoupper($status)); ?></span>
                </div>
                <div class="d-flex gap-2">
                  <button type="button" id="terminateBtn" class="btn btn-outline-danger" style="<?php echo e(strtolower($status)==='terminated' ? 'display:none;' : ''); ?>">
                    <i class="ti ti-user-x me-1"></i> Terminate
                  </button>
                  <button type="button" id="reinstateBtn" class="btn btn-success" style="<?php echo e(strtolower($status)==='terminated' ? '' : 'display:none;'); ?>">
                    <i class="ti ti-user-check me-1"></i> Re‑Register
                  </button>
                </div>
              </div>

              
              <div class="mb-3 mt-4 text-center position-relative">
                <div class="d-flex justify-content-end">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 position-relative" style="width:150px;height:150px;border:2px solid #ccc;">
                    <img src="<?php echo e(!empty($student->user_photo) ? asset('storage/' . $student->user_photo) : asset('images/profile/user-1.jpg')); ?>" alt="Student Profile" width="150" height="150" class="rounded-circle" id="studentProfilePictureImg">
                  </div>
                </div>
                <input type="file" class="form-control visually-hidden" id="profilePicture" accept="image/*">
                <div class="d-flex justify-content-end mx-4">
                  <button type="button" class="btn btn-sm btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#editPictureModal" id="editPictureBtn" style="display:none;">Edit Picture</button>
                </div>
              </div>

              
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentTitle" class="col-sm-3 col-form-label fw-bold">Title <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentTitle" value="<?php echo e($student->title ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentName" class="col-sm-3 col-form-label fw-bold">Name <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentName" value="<?php echo e($student->full_name ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentNIC" class="col-sm-3 col-form-label fw-bold">NIC <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentNIC" value="<?php echo e($student->id_value ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentInstitute" class="col-sm-3 col-form-label fw-bold">Institute <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentInstitute" value="<?php echo e($student->institute_location ?? ''); ?>" readonly>
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
                  <input type="text" class="form-control" id="studentGender" value="<?php echo e($student->gender ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentEmail" class="col-sm-3 col-form-label fw-bold">Email <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="email" class="form-control" id="studentEmail" value="<?php echo e($student->email ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentMobile" class="col-sm-3 col-form-label fw-bold">Mobile Phone No <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <input type="tel" class="form-control" id="studentMobile" value="<?php echo e($student->mobile_phone ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentHomePhone" class="col-sm-3 col-form-label fw-bold">Home Phone No</label>
                <div class="col-sm-9">
                  <input type="tel" class="form-control" id="studentHomePhone" value="<?php echo e($student->home_phone ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentAddress" class="col-sm-3 col-form-label fw-bold">Address <span class="text-danger">*</span></label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentAddress" rows="2" readonly><?php echo e($student->address ?? ''); ?></textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentFoundation" class="col-sm-3 col-form-label fw-bold">Foundation Program</label>
                <div class="col-sm-9">
                  <input type="text" class="form-control" id="studentFoundation" value="<?php echo e($student->foundation_program ?? ''); ?>" readonly>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentSpecialNeeds" class="col-sm-3 col-form-label fw-bold">Special Needs</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentSpecialNeeds" rows="2" readonly><?php echo e($student->special_needs ?? ''); ?></textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentExtraCurricular" class="col-sm-3 col-form-label fw-bold">Extra Curricular Activities</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentExtraCurricular" rows="2" readonly><?php echo e($student->extracurricular_activities ?? ''); ?></textarea>
                </div>
              </div>
              <div class="mb-3 row align-items-center mx-3">
                <label for="studentFuturePotentials" class="col-sm-3 col-form-label fw-bold">Future Potentials</label>
                <div class="col-sm-9">
                  <textarea class="form-control" id="studentFuturePotentials" rows="2" readonly><?php echo e($student->future_potentials ?? ''); ?></textarea>
                </div>
              </div>

              
              <div class="mt-4 mb-3">
                <button type="button" class="btn btn-primary" id="showEditPersonalInfoBtn">Edit Personal Info</button>
                <button type="button" class="btn btn-success ms-2" id="updatePersonalInfoBtn" style="display:none;">Update Personal Info</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelEditBtn" style="display:none;">Cancel</button>
              </div>
            </div>

      
      <div class="tab-pane fade" id="parent">
      <!-- In the parent tab section of student_profile.blade.php -->
    <div class="mb-3 row align-items-center mx-3">
        <label for="parentName" class="col-sm-3 col-form-label fw-bold">Name <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="parentName" value="<?php echo e($student->parent->guardian_name ?? ''); ?>" readonly>
          <div class="invalid-feedback" id="parentNameFeedback" style="display:none;"></div>
        </div>
      </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentProfession" class="col-sm-3 col-form-label fw-bold">Profession <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control" id="parentProfession" value="<?php echo e($student->parent->guardian_profession ?? ''); ?>" readonly>
          <div class="invalid-feedback" id="parentProfessionFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentContactNo" class="col-sm-3 col-form-label fw-bold">Contact Number <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="tel" class="form-control" id="parentContactNo" value="<?php echo e($student->parent->guardian_contact_number ?? ''); ?>" readonly>
          <div class="invalid-feedback" id="parentContactNoFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentEmail" class="col-sm-3 col-form-label fw-bold">Email <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="email" class="form-control" id="parentEmail" value="<?php echo e($student->parent->guardian_email ?? ''); ?>" readonly>
          <div class="invalid-feedback" id="parentEmailFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentAddress" class="col-sm-3 col-form-label fw-bold">Address <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <textarea class="form-control" id="parentAddress" rows="2" readonly><?php echo e($student->parent->guardian_address ?? ''); ?></textarea>
          <div class="invalid-feedback" id="parentAddressFeedback" style="display:none;"></div>
        </div>
            </div>
            <div class="mb-3 row align-items-center mx-3">
                <label for="parentEmergencyContact" class="col-sm-3 col-form-label fw-bold">Emergency Contact Number <span class="text-danger">*</span></label>
        <div class="col-sm-9">
          <input type="text" class="form-control bg-danger text-white" id="parentEmergencyContact" value="<?php echo e($student->parent->emergency_contact_number ?? ''); ?>" readonly>
          <div class="invalid-feedback" id="parentEmergencyContactFeedback" style="display:none;"></div>
        </div>
            </div>
              <div class="mt-4 mb-3">
                <button type="button" class="btn btn-primary" id="showEditParentInfoBtn">Edit Parent/Guardian Info</button>
                <button type="button" class="btn btn-success ms-2" id="updateParentInfoBtn" style="display:none;">Update Parent/Guardian Info</button>
                <button type="button" class="btn btn-secondary ms-2" id="cancelEditParentBtn" style="display:none;">Cancel</button>
              </div>
            </div>

            
            <div class="tab-pane fade" id="academic">
              <?php
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
              ?>

              <?php if($ol_pending): ?>
                <div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student's O/L exam results are still pending.</div>
              <?php else: ?>
                <div id="olExamSection">
                  <h5 class="mt-4 mb-3 fw-bold">O/L Exam Details</h5>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($ol_exam->ol_index_no ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($ol_exam->ol_exam_type ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($ol_exam->ol_exam_year ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                    <div class="col-sm-9">
                      <table class="table table-bordered mb-0">
                        <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                        <tbody>
                          <?php $__currentLoopData = json_decode($ol_exam->ol_exam_subjects, true) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td><?php echo e($subject['subject'] ?? ''); ?></td><td><?php echo e($subject['result'] ?? ''); ?></td></tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
                    <div class="col-sm-9">
                      <?php if(!empty($ol_exam->ol_certificate)): ?>
                        <a href="<?php echo e(asset('storage/certificates/' . $ol_exam->ol_certificate)); ?>" target="_blank">View Certificate</a>
                      <?php else: ?>
                        <span class="text-muted">Not uploaded</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>

              <?php if($al_pending): ?>
                <div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student's A/L exam results are still pending.</div>
              <?php else: ?>
                <div id="alExamSection">
                  <hr>
                  <h5 class="mt-4 mb-3 fw-bold">A/L Exam Details</h5>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Index No.</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($al_exam->al_index_no ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Type</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($al_exam->al_exam_type ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Exam Year</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($al_exam->al_exam_year ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">A/L Stream</label>
                    <div class="col-sm-9"><input type="text" class="form-control" value="<?php echo e($al_exam->al_stream ?? ''); ?>" readonly></div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
                    <div class="col-sm-9">
                      <table class="table table-bordered mb-0">
                        <thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead>
                        <tbody>
                          <?php $__currentLoopData = json_decode($al_exam->al_exam_subjects, true) ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $subject): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr><td><?php echo e($subject['subject'] ?? ''); ?></td><td><?php echo e($subject['result'] ?? ''); ?></td></tr>
                          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                      </table>
                    </div>
                  </div>
                  <div class="mb-3 row align-items-center mx-3">
                    <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
                    <div class="col-sm-9">
                      <?php if(!empty($al_exam->al_certificate)): ?>
                        <a href="<?php echo e(asset('storage/certificates/' . $al_exam->al_certificate)); ?>" target="_blank">View Certificate</a>
                      <?php else: ?>
                        <span class="text-muted">Not uploaded</span>
                      <?php endif; ?>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
            </div>

            
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

            
            <div class="tab-pane fade" id="clearance">
              <h5 class="fw-bold mb-3">Student Clearance Status</h5>
              <table class="table table-bordered">
                <thead class="bg-primary text-white">
                  <tr><th>Clearance Type</th><th>Status</th><th>Approved Date</th><th>Remarks</th><th>Uploaded Document</th></tr>
                </thead>
                <tbody id="clearanceTableBody"></tbody>
              </table>
            </div>

            
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
            
            
            
            <div class="tab-pane fade" id="other-info">
              <h5 class="mt-4 mb-3 fw-bold">Other Information</h5>
              <?php if(isset($student->other_information)): ?>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Disciplinary Issues</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" rows="2" readonly><?php echo e($student->other_information->disciplinary_issues ?? ''); ?></textarea>
                  </div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Disciplinary Document</label>
                  <div class="col-sm-9">
                    <?php if($student->other_information->disciplinary_issue_document): ?>
                      <a href="<?php echo e(asset('storage/' . $student->other_information->disciplinary_issue_document)); ?>" target="_blank">View Document</a>
                    <?php else: ?>
                      <span class="text-muted">Not uploaded</span>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Institute</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="<?php echo e($student->other_information->institute ?? ''); ?>"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Field of Study</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="<?php echo e($student->other_information->field_of_study ?? ''); ?>"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Job Title</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="<?php echo e($student->other_information->job_title ?? ''); ?>"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Workplace</label>
                  <div class="col-sm-9"><input type="text" class="form-control" readonly value="<?php echo e($student->other_information->workplace ?? ''); ?>"></div>
                </div>
                <div class="mb-3 row align-items-center mx-3">
                  <label class="col-sm-3 col-form-label fw-bold">Other Information</label>
                  <div class="col-sm-9">
                    <textarea class="form-control" rows="2" readonly><?php echo e($student->other_information->other_information ?? ''); ?></textarea>
                  </div>
                </div>
              <?php else: ?>
                <div class="alert alert-warning">No other information found for this student.</div>
              <?php endif; ?>
            </div>
          </div>
        </div> 
      </div>
    </div>
  </div>
</div>

<script>
// ---------- Notifications ----------
function showSuccessMessage(message){
  document.querySelectorAll('.success-message,.error-message').forEach(m=>m.remove());
  const n=document.createElement('div'); n.className='success-message';
  n.innerHTML=`<i class="ti ti-check-circle success-icon"></i>${message}`;
  document.body.appendChild(n); setTimeout(()=>n.classList.add('show'),100);
  setTimeout(()=>{n.classList.remove('show'); setTimeout(()=>n.remove(),300)},4000);
}
function showErrorMessage(message){
  document.querySelectorAll('.success-message,.error-message').forEach(m=>m.remove());
  const n=document.createElement('div'); n.className='error-message';
  n.innerHTML=`<i class="ti ti-alert-circle error-icon"></i>${message}`;
  document.body.appendChild(n); setTimeout(()=>n.classList.add('show'),100);
  setTimeout(()=>{n.classList.remove('show'); setTimeout(()=>n.remove(),300)},5000);
}

// ---------- Helper: status UI ----------
// Add these functions at the top with your other helper functions
function isValidPhone(phone) {
    // Allows formats like: +94771234567, 0771234567, 771234567
    return /^(?:\+94|0)?[0-9]{9}$/.test(phone.replace(/\s/g, ''));
}

function isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Update the updateParentInfoBtn click handler
$('#updateParentInfoBtn').on('click', function() {
    const studentId = $('#studentIdHidden').val();
    if (!studentId) {
        showErrorMessage('No student selected.');
        return;
    }

  // Clear previous validation states and inline feedback
  $('.is-invalid').removeClass('is-invalid');
  $('.invalid-feedback').text('').hide();

    // Validate all required fields
    const fields = {
        'guardian_name': $('#parentName').val().trim(),
        'guardian_profession': $('#parentProfession').val().trim(),
        'guardian_contact_number': $('#parentContactNo').val().trim(),
        'guardian_email': $('#parentEmail').val().trim(),
        'guardian_address': $('#parentAddress').val().trim(),
        'emergency_contact_number': $('#parentEmergencyContact').val().trim()
    };

    let hasError = false;
    const errors = [];

  // Check empty fields and show inline messages
  Object.entries(fields).forEach(([key, value]) => {
    const idKey = key.split('_')[1];
    const $field = $(`#parent${idKey.charAt(0).toUpperCase() + idKey.slice(1)}`);
    const feedbackSelector = `#${$field.attr('id')}Feedback`;
    if (!value) {
      $field.addClass('is-invalid');
      $(feedbackSelector).text('This field is required.').show();
      hasError = true;
      errors.push(`${key.split('_').map(word => word.charAt(0).toUpperCase() + word.slice(1)).join(' ')}`);
    }
  });

    // Validate phone numbers
  if (fields.guardian_contact_number && !isValidPhone(fields.guardian_contact_number)) {
    $('#parentContactNo').addClass('is-invalid');
    $('#parentContactNoFeedback').text('Invalid contact number format.').show();
    hasError = true;
    errors.push('Invalid Contact Number format');
  }
  if (fields.emergency_contact_number && !isValidPhone(fields.emergency_contact_number)) {
    $('#parentEmergencyContact').addClass('is-invalid');
    $('#parentEmergencyContactFeedback').text('Invalid emergency contact number format.').show();
    hasError = true;
    errors.push('Invalid Emergency Contact Number format');
  }

    // Validate email
    if (fields.guardian_email && !isValidEmail(fields.guardian_email)) {
        $('#parentEmail').addClass('is-invalid');
        hasError = true;
        errors.push('Invalid Email format');
    }

  if (hasError) {
    showErrorMessage('Please correct the following errors: ' + errors.join(', '));
    return;
  }

    // Proceed with update if validation passes
    const data = {
        student_id: studentId,
        ...fields,
        _token: '<?php echo e(csrf_token()); ?>'
    };

  $.post("<?php echo e(route('student.update.parent.info')); ?>", data, function(resp) {
    if (resp.success) {
      // Clear any inline errors
      $('.invalid-feedback').text('').hide();
      $('.is-invalid').removeClass('is-invalid');
      showSuccessMessage('Parent/Guardian information updated successfully!');
      $('#cancelEditParentBtn').click();
            
      // Update the display values
      Object.entries(fields).forEach(([key, value]) => {
        const idKey = key.split('_')[1];
        const $field = $(`#parent${idKey.charAt(0).toUpperCase() + idKey.slice(1)}`);
        $field.val(value);
      });
    } else {
      showErrorMessage(resp.message || 'Failed to update parent/guardian information.');
    }
  }).fail(function(xhr) {
    if (xhr && xhr.status === 422) {
      const errors = (xhr.responseJSON && xhr.responseJSON.errors) ? xhr.responseJSON.errors : {};
      const mapping = {
        'guardian_name':'#parentName',
        'guardian_profession':'#parentProfession',
        'guardian_contact_number':'#parentContactNo',
        'guardian_email':'#parentEmail',
        'guardian_address':'#parentAddress',
        'emergency_contact_number':'#parentEmergencyContact'
      };
      Object.keys(errors).forEach(function(field){
        const sel = mapping[field] || ('#' + field);
        const $el = $(sel);
        if ($el.length) {
          $el.addClass('is-invalid');
          const feed = '#' + $el.attr('id') + 'Feedback';
          $(feed).text(errors[field][0]).show();
        }
      });
      showErrorMessage('Please correct the highlighted fields.');
    } else {
      showErrorMessage('An error occurred while updating parent/guardian information.');
    }
  });
});

function setStatusUI(status){
  const isTerminated=(status||'').toString().toLowerCase()==='terminated';
  const badge=$('#studentStatusBadge');
  badge.text((status||'active').toUpperCase());
  badge.toggleClass('bg-danger',isTerminated).toggleClass('bg-success',!isTerminated);
  $('#terminateBtn').toggle(!isTerminated);
  $('#reinstateBtn').toggle(isTerminated);

  // lock personal edit if terminated
  $('#showEditPersonalInfoBtn').prop('disabled', isTerminated);
}

// ---------- Document Ready ----------
$(function(){
  // Restore tab
  var lastTab = localStorage.getItem('studentProfileActiveTab');
  if (lastTab) {
    var tabTrigger = document.querySelector('a[href="' + lastTab + '"]');
    if (tabTrigger) new bootstrap.Tab(tabTrigger).show();
  }
  $('a[data-bs-toggle="tab"]').on('shown.bs.tab', e => {
    localStorage.setItem('studentProfileActiveTab', $(e.target).attr('href'));
  });

  // If server provided $student, set initial status badge
  setStatusUI('<?php echo e($status); ?>');

  // NIC Search
  $('#nicSearchForm').on('submit', function(e){
    e.preventDefault();
    const nic=$('#nicInput').val().trim();
    if(!nic) return;
    $.ajax({
      url:'/api/student-details-by-nic',
      method:'GET',
      data:{nic:nic},
      success:function(res){
        if(res.success && res.student){
          populateStudentProfile(res.student);
          $('#studentIdHidden').val(res.student.student_id);
          $('#profileSection').show();
          $('#personal-tab').tab('show');
          setStatusUI(res.student.academic_status || 'active');
          fetchRegisteredCourses(); // for Exams tab
        }else{
          $('#profileSection').hide();
          $('#editPictureBtn').hide();
          showErrorMessage('Student not found!');
        }
      },
      error:function(){
        $('#profileSection').hide();
        $('#editPictureBtn').hide();
        showErrorMessage('Error fetching student details.');
      }
    });
  });

  // ----- populate profile (builds Academic + Other Info + fills fields) -----
  window.populateStudentProfile = function(student){
    // PERSONAL
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
    setStatusUI(student.academic_status || 'active');
    
    // Show edit picture button and update profile image
    $('#editPictureBtn').show();
    updateStudentProfileImage(student.user_photo);
    
    if (student.birthday) {
      const dob = new Date(student.birthday);
      const formatted = dob.toISOString().split('T')[0];
      $('#studentDOB').val(formatted);
    } else {
      $('#studentDOB').val('');
    }

    // PARENT
    if(student.parent){
      $('#parentName').val(student.parent.guardian_name || '');
      $('#parentProfession').val(student.parent.guardian_profession || '');
      $('#parentContactNo').val(student.parent.guardian_contact_number || '');
      $('#parentEmail').val(student.parent.guardian_email || '');
      $('#parentAddress').val(student.parent.guardian_address || '');
      $('#parentEmergencyContact').val(student.parent.emergency_contact_number || '');
    }

    // Academic (client-rendered summary)
    const $academic = $('#academic'); $academic.empty();
    let ol_exam=null, al_exam=null, ol_pending=true, al_pending=true;
    if (student.exams && student.exams.length){
      student.exams.forEach(exam=>{
        let ols = typeof exam.ol_exam_subjects==='string' ? (JSON.parse(exam.ol_exam_subjects||'[]')) : (exam.ol_exam_subjects||[]);
        let als = typeof exam.al_exam_subjects==='string' ? (JSON.parse(exam.al_exam_subjects||'[]')) : (exam.al_exam_subjects||[]);
        if(ols && ols.length){ ol_exam=exam; ol_pending=false; }
        if(als && als.length){ al_exam=exam; al_pending=false; }
      });
    }
    if(ol_pending){
      $academic.append('<div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student\'s O/L exam results are still pending.</div>');
    }else if(ol_exam){
      let rows=''; (typeof ol_exam.ol_exam_subjects==='string' ? JSON.parse(ol_exam.ol_exam_subjects||'[]') : (ol_exam.ol_exam_subjects||[])).forEach(s=>{ rows+=`<tr><td>${s.subject||''}</td><td>${s.result||''}</td></tr>`; });
      $academic.append(`
        <div id="olExamSection">
          <h5 class="mt-4 mb-3 fw-bold">O/L Exam Details</h5>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Index No.</label><div class="col-sm-9"><input class="form-control" value="${ol_exam.ol_index_no||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Exam Type</label><div class="col-sm-9"><input class="form-control" value="${ol_exam.ol_exam_type||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Exam Year</label><div class="col-sm-9"><input class="form-control" value="${ol_exam.ol_exam_year||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
            <div class="col-sm-9"><table class="table table-bordered mb-0"><thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead><tbody>${rows}</tbody></table></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">O/L Certificate</label>
            <div class="col-sm-9">${ol_exam.ol_certificate?`<a href="/storage/certificates/${ol_exam.ol_certificate}" target="_blank">View Certificate</a>`:'<span class="text-muted">Not uploaded</span>'}</div>
          </div>
        </div>`);
    }
    if(al_pending){
      $academic.append('<div class="alert alert-warning mb-3"><strong>Pending Results:</strong> The student\'s A/L exam results are still pending.</div>');
    }else if(al_exam){
      let rows=''; (typeof al_exam.al_exam_subjects==='string' ? JSON.parse(al_exam.al_exam_subjects||'[]') : (al_exam.al_exam_subjects||[])).forEach(s=>{ rows+=`<tr><td>${s.subject||''}</td><td>${s.result||''}</td></tr>`; });
      $academic.append(`
        <div id="alExamSection">
          <hr><h5 class="mt-4 mb-3 fw-bold">A/L Exam Details</h5>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Index No.</label><div class="col-sm-9"><input class="form-control" value="${al_exam.al_index_no||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Exam Type</label><div class="col-sm-9"><input class="form-control" value="${al_exam.al_exam_type||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Exam Year</label><div class="col-sm-9"><input class="form-control" value="${al_exam.al_exam_year||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">A/L Stream</label><div class="col-sm-9"><input class="form-control" value="${al_exam.al_stream||''}" readonly></div></div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">Subjects & Results</label>
            <div class="col-sm-9"><table class="table table-bordered mb-0"><thead class="bg-primary text-white"><tr><th>Subject</th><th>Result</th></tr></thead><tbody>${rows}</tbody></table></div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label class="col-sm-3 col-form-label fw-bold">A/L Certificate</label>
            <div class="col-sm-9">${al_exam.al_certificate?`<a href="/storage/certificates/${al_exam.al_certificate}" target="_blank">View Certificate</a>`:'<span class="text-muted">Not uploaded</span>'}</div>
          </div>
        </div>`);
    }

    // Other Info tab (client refresh)
    const $otherInfoTab=$('#other-info'); $otherInfoTab.empty().append('<h5 class="mt-4 mb-3 fw-bold">Other Information</h5>');
    const oi=student.other_information;
    if(!!oi){
      $otherInfoTab.append(`
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Disciplinary Issues</label><div class="col-sm-9"><textarea class="form-control" rows="2" readonly>${oi.disciplinary_issues||''}</textarea></div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Disciplinary Document</label><div class="col-sm-9">${oi.disciplinary_issue_document?`<a href="/storage/${oi.disciplinary_issue_document}" target="_blank">View Document</a>`:'<span class="text-muted">Not uploaded</span>'}</div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Institute</label><div class="col-sm-9"><input class="form-control" readonly value="${oi.institute||'-'}"></div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Field of Study</label><div class="col-sm-9"><input class="form-control" readonly value="${oi.field_of_study||'-'}"></div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Job Title</label><div class="col-sm-9"><input class="form-control" readonly value="${oi.job_title||'-'}"></div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Workplace</label><div class="col-sm-9"><input class="form-control" readonly value="${oi.workplace||'-'}"></div></div>
        <div class="mb-3 row align-items-center mx-3"><label class="col-sm-3 col-form-label fw-bold">Other Information</label><div class="col-sm-9"><textarea class="form-control" rows="2" readonly>${oi.other_information||'-'}</textarea></div></div>
      `);
    }else{
      $otherInfoTab.append('<div class="alert alert-warning">No other information found for this student.</div>');
    }
  };

  // ----- Personal edit buttons -----
  $('#showEditPersonalInfoBtn').on('click', function(){
    $('#studentTitle,#studentName,#studentNIC,#studentIndexNo,#studentInstitute,#studentDOB,#studentGender,#studentEmail,#studentMobile,#studentHomePhone,#studentEmergencyContact,#studentAddress,#studentFoundation,#studentSpecialNeeds,#studentExtraCurricular,#studentFuturePotentials').prop('readonly',false);
    $('#showEditPersonalInfoBtn').hide(); $('#updatePersonalInfoBtn,#cancelEditBtn').show();
  });
  $('#cancelEditBtn').on('click', function(){
    $('#studentTitle,#studentName,#studentNIC,#studentIndexNo,#studentInstitute,#studentDOB,#studentGender,#studentEmail,#studentMobile,#studentHomePhone,#studentEmergencyContact,#studentAddress,#studentFoundation,#studentSpecialNeeds,#studentExtraCurricular,#studentFuturePotentials').prop('readonly',true);
    // Clear validation error classes
    $('.is-invalid').removeClass('is-invalid');
    $('#showEditPersonalInfoBtn').show(); $('#updatePersonalInfoBtn,#cancelEditBtn').hide();
  });
  $('#updatePersonalInfoBtn').on('click', function(){
    const studentId=$('#studentIdHidden').val(); 
    if(!studentId){ return showErrorMessage('No student selected.'); }
    
    // Validate required fields
    const requiredFields = [
      {id: '#studentTitle', name: 'Title'},
      {id: '#studentName', name: 'Name'},
      {id: '#studentNIC', name: 'NIC'},
      {id: '#studentInstitute', name: 'Institute'},
      {id: '#studentDOB', name: 'Date of Birth'},
      {id: '#studentGender', name: 'Gender'},
      {id: '#studentEmail', name: 'Email'},
      {id: '#studentMobile', name: 'Mobile Phone No'},
      {id: '#studentAddress', name: 'Address'}
    ];
    
    let validationErrors = [];
    
    // Check each required field
    requiredFields.forEach(function(field) {
      const value = $(field.id).val();
      if (!value || value.trim() === '') {
        validationErrors.push(field.name);
        $(field.id).addClass('is-invalid');
      } else {
        $(field.id).removeClass('is-invalid');
      }
    });
    
    // Email validation
    const email = $('#studentEmail').val();
    if (email && !isValidEmail(email)) {
      validationErrors.push('Valid Email');
      $('#studentEmail').addClass('is-invalid');
    }
    
    // If there are validation errors, show them and return
    if (validationErrors.length > 0) {
      showErrorMessage('Please fill in all required fields: ' + validationErrors.join(', '));
      return;
    }
    
    const data={
      student_id:studentId,
      title:$('#studentTitle').val(),
      full_name:$('#studentName').val(),
      id_value:$('#studentNIC').val(),
      registration_id:$('#studentIndexNo').val(),
      institute_location:$('#studentInstitute').val(),
      birthday:$('#studentDOB').val(),
      gender:$('#studentGender').val(),
      email:$('#studentEmail').val(),
      mobile_phone:$('#studentMobile').val(),
      home_phone:$('#studentHomePhone').val(),
      emergency_contact_number:$('#studentEmergencyContact').val(),
      address:$('#studentAddress').val(),
      foundation_program:$('#studentFoundation').val(),
      special_needs:$('#studentSpecialNeeds').val(),
      extracurricular_activities:$('#studentExtraCurricular').val(),
      future_potentials:$('#studentFuturePotentials').val(),
      _token:'<?php echo e(csrf_token()); ?>'
    };
    
    $.post("<?php echo e(route('student.update.personal.info')); ?>", data, function(resp){
      if(resp.success){
        // Remove any validation error classes
        $('.is-invalid').removeClass('is-invalid');
        showSuccessMessage('Personal information updated successfully!');
        $('#cancelEditBtn').click();
      }else{
        // Handle validation errors from server
        if (resp.errors) {
          let errorMessages = [];
          Object.keys(resp.errors).forEach(function(field) {
            errorMessages.push(resp.errors[field][0]);
          });
          showErrorMessage('Validation Error: ' + errorMessages.join(', '));
        } else {
          showErrorMessage(resp.message||'Failed to update personal information.');
        }
      }
    }).fail(function(xhr) {
      if (xhr.status === 422) {
        const errors = xhr.responseJSON.errors;
        let errorMessages = [];
        Object.keys(errors).forEach(function(field) {
          errorMessages.push(errors[field][0]);
        });
        showErrorMessage('Validation Error: ' + errorMessages.join(', '));
      } else {
        showErrorMessage('An error occurred while updating personal information.');
      }
    });
  });

  // ----- Parent edit buttons -----
  // Show / cancel handlers remain. The actual update is handled by the
  // validated handler defined earlier (to avoid duplicate bindings and
  // inconsistent null submissions).
  $('#showEditParentInfoBtn').on('click', function(){
    $('#parentName,#parentProfession,#parentContactNo,#parentEmail,#parentAddress,#parentEmergencyContact').prop('readonly',false);
    $('#showEditParentInfoBtn').hide(); $('#updateParentInfoBtn,#cancelEditParentBtn').show();
  });
  $('#cancelEditParentBtn').on('click', function(){
    $('#parentName,#parentProfession,#parentContactNo,#parentEmail,#parentAddress,#parentEmergencyContact').prop('readonly',true);
    $('#showEditParentInfoBtn').show(); $('#updateParentInfoBtn,#cancelEditParentBtn').hide();
  });

  // ----- Exams tab dynamic -----
  function getStudentId(){ return $('#studentIdHidden').val(); }
  function fetchRegisteredCourses(){
    const sid=getStudentId(); if(!sid) return;
    $.get('/api/student/'+sid+'/courses', res=>{
      const $s=$('#examCourseSelect'); $s.empty().append('<option value="">Select a course</option>');
      if(res.success && res.courses.length){ res.courses.forEach(c=>$s.append(`<option value="${c.course_id}">${c.course_name}</option>`)); }
    });
  }
  function fetchSemesters(courseId){
    const sid=getStudentId(); if(!sid||!courseId) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/semesters', res=>{
      const $s=$('#examSemesterSelect'); $s.empty().append('<option value="">Select a semester</option>');
      if(res.success && res.semesters.length){ res.semesters.forEach(v=>$s.append(`<option value="${v}">${v}</option>`)); $s.prop('disabled',false); } else { $s.prop('disabled',true); }
    });
  }
  function fetchModuleResults(courseId, sem){
    const sid=getStudentId(); if(!sid||!courseId||!sem) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/semester/'+sem+'/results', res=>{
      const $tb=$('#examResultsTableBody').empty();
      if(res.success && res.results.length){ res.results.forEach(r=>$tb.append(`<tr><td>${r.module_name}</td><td>${r.marks}</td><td>${r.grade}</td></tr>`)); }
      else{ $tb.append('<tr><td colspan="3" class="text-center">No results found.</td></tr>'); }
      $('#examResultsTableWrapper').show();
    });
  }
  $('a[data-bs-toggle="tab"][href="#exams"]').on('shown.bs.tab', function(){ fetchRegisteredCourses(); $('#examSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled',true); $('#examResultsTableWrapper').hide(); });
  $('#examCourseSelect').on('change', function(){ const c=$(this).val(); if(c){ fetchSemesters(c); $('#examResultsTableWrapper').hide(); } else { $('#examSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled',true); $('#examResultsTableWrapper').hide(); }});
  $('#examSemesterSelect').on('change', function(){ const c=$('#examCourseSelect').val(), s=$(this).val(); if(c&&s){ fetchModuleResults(c,s); } else { $('#examResultsTableWrapper').hide(); }});

  // ----- Attendance tab -----
  function fetchAttendanceCourses(){
    const sid=getStudentId(); if(!sid) return;
    $.get('/api/student/'+sid+'/courses', res=>{
      const $s=$('#attendanceCourseSelect').empty().append('<option value="">Select a course</option>');
      if(res.success && res.courses.length){ res.courses.forEach(c=>$s.append(`<option value="${c.course_id}">${c.course_name}</option>`)); }
    });
  }
  function fetchAttendanceSemesters(courseId){
    const sid=getStudentId(); if(!sid||!courseId) return;
    const $s = $('#attendanceSemesterSelect');
    // reset and show loading state
    $s.empty().append('<option value="">Select a semester</option>');
    $s.prop('disabled', true).attr('aria-busy', 'true');

    $.get('/api/student/'+sid+'/course/'+courseId+'/semesters', res=>{
      // DEBUG: show raw response in console to aid troubleshooting
      try{ if(window && window.location && window.location.hostname && !window.location.hostname.includes('your-production-hostname')) console.debug('fetchAttendanceSemesters response:', res); }catch(e){}

      // Normalize many possible API shapes: res.semesters, res.data.semesters, res.data (array), res (array)
      let semesters = [];
      if (Array.isArray(res)) semesters = res;
      else if (res && Array.isArray(res.semesters)) semesters = res.semesters;
      else if (res && res.data && Array.isArray(res.data.semesters)) semesters = res.data.semesters;
      else if (res && res.data && Array.isArray(res.data)) semesters = res.data;
      else if (res && res.success && Array.isArray(res.semesters)) semesters = res.semesters;

      if (Array.isArray(semesters) && semesters.length){
        semesters.forEach(function(v){
          // guard: skip null/empty
          if (v === null || typeof v === 'undefined') return;
          const val = (typeof v === 'object' && v.semester) ? v.semester : v;
          $s.append(`<option value="${val}">${val}</option>`);
        });
        // enable the select reliably
        $s.prop('disabled', false);
        $s.removeAttr('disabled');
        $s.attr('aria-busy', 'false').attr('aria-disabled', 'false');
      } else {
        // no semesters: keep it disabled and show a helpful single option
        $s.empty().append('<option value="">No semesters registered for this course</option>');
        $s.prop('disabled', true).attr('disabled', 'disabled').attr('aria-busy', 'false');
      }
    }).fail(function(){
      console.error('Failed to load semesters for course', courseId);
      $s.empty().append('<option value="">Failed to load semesters</option>').prop('disabled', true).attr('disabled','disabled').attr('aria-busy','false');
    });
  }
  function fetchAttendanceTable(courseId, sem){
    const sid=getStudentId(); if(!sid||!courseId||!sem) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/semester/'+sem+'/attendance', res=>{
      const $tb=$('#attendanceTableBody').empty();
      if(res.success && res.attendance.length){
        res.attendance.forEach(a=>$tb.append(`<tr><td>${a.module_name}</td><td>${a.total_days}</td><td>${a.present_days}</td><td>${a.absent_days}</td><td>${a.attendance_percent}</td></tr>`));
      }else{ $tb.append('<tr><td colspan="5" class="text-center">No attendance data found.</td></tr>'); }
      $('#attendanceTableWrapper').show();
    });
  }
  $('a[data-bs-toggle="tab"][href="#attendance"]').on('shown.bs.tab', function(){ fetchAttendanceCourses(); $('#attendanceSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled',true); $('#attendanceTableWrapper').hide(); });
  $('#attendanceCourseSelect').on('change', function(){ const c=$(this).val(); if(c){ fetchAttendanceSemesters(c); $('#attendanceTableWrapper').hide(); } else { $('#attendanceSemesterSelect').empty().append('<option value="">Select a semester</option>').prop('disabled',true); $('#attendanceTableWrapper').hide(); }});
  $('#attendanceSemesterSelect').on('change', function(){ const c=$('#attendanceCourseSelect').val(), s=$(this).val(); if(c&&s){ fetchAttendanceTable(c,s); } else { $('#attendanceTableWrapper').hide(); }});

  // ----- Payment tab -----
  function fetchPaymentCourses(){
    const sid=getStudentId(); if(!sid) return;
    $.get('/api/student/'+sid+'/courses', res=>{
      const $s=$('#paymentCourseSelect').empty().append('<option value="">Select a course</option>');
      if(res.success && res.courses.length){ res.courses.forEach(c=>$s.append(`<option value="${c.course_id}">${c.course_name}</option>`)); }
    });
  }
  function fetchPaymentIntakes(courseId){
    const sid=getStudentId(); if(!sid||!courseId) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/intakes', res=>{
      const $s=$('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>');
      if(res.success && res.intakes.length){ res.intakes.forEach(i=>$s.append(`<option value="${i}">${i}</option>`)); $s.prop('disabled',false); } else { $s.prop('disabled',true); }
    });
  }
  function fetchPaymentDetails(courseId, intake){
    const sid=getStudentId(); if(!sid||!courseId||!intake) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/intake/'+intake+'/payment-details', res=>{
      if(res.success){ $('#totalFee').text(res.total_fee||'N/A'); $('#paidAmount').text(res.paid_amount||'0'); $('#balance').text(res.balance||'0'); $('#paymentStatus').text(res.payment_status||'N/A'); $('#paymentTableWrapper').show(); }
      else{ $('#paymentTableWrapper').hide(); }
    });
  }
  function fetchPaymentHistory(courseId,intake){
    const sid=getStudentId(); if(!sid||!courseId||!intake) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/intake/'+intake+'/payment-history', res=>{
      const $h=$('#paymentHistory').empty();
      if(res.success && res.history && res.history.length){
        res.history.forEach(p=>$h.append(`<p class="mb-1"><strong>Date:</strong> ${p.payment_date||'N/A'}</p><p class="mb-1"><strong>Amount:</strong> ${p.amount||'0'}</p><p class="mb-1"><strong>Method:</strong> ${p.payment_method||'N/A'}</p><p class="mb-1"><strong>Receipt:</strong> ${p.receipt_url?`<a href="${p.receipt_url}" target="_blank">View Receipt</a>`:'N/A'}</p><hr class="my-2">`));
      }else{ $h.append('<p class="text-muted">No payment history found for this intake.</p>'); }
    });
  }
  function fetchPaymentSchedule(courseId,intake){
    const sid=getStudentId(); if(!sid||!courseId||!intake) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/intake/'+intake+'/payment-schedule', res=>{
      const $tb=$('#paymentScheduleTableBody').empty();
      if(res.success && res.schedule.length){
        res.schedule.forEach(p=>$tb.append(`<tr><td>${p.due_date||'N/A'}</td><td>${p.amount||'0'}</td><td>${p.status||'N/A'}</td><td>${p.payment_date||'N/A'}</td><td>${p.receipt_url?`<a href="${p.receipt_url}" target="_blank">View Receipt</a>`:'N/A'}</td></tr>`));
      }else{ $tb.append('<tr><td colspan="5" class="text-center">No payment schedule found for this intake.</td></tr>'); }
    });
  }
  $('a[data-bs-toggle="tab"][href="#payment"]').on('shown.bs.tab', function(){ fetchPaymentCourses(); $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled',true); $('#paymentTableWrapper').hide(); $('#paymentHistory').empty(); $('#paymentScheduleTableBody').empty(); });
  $('#paymentCourseSelect').on('change', function(){ const c=$(this).val(); if(c){ fetchPaymentIntakes(c); $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled',true); $('#paymentTableWrapper').hide(); $('#paymentHistory').empty(); $('#paymentScheduleTableBody').empty(); } else { $('#paymentIntakeSelect').empty().append('<option value="">Select an intake</option>').prop('disabled',true); $('#paymentTableWrapper').hide(); $('#paymentHistory').empty(); $('#paymentScheduleTableBody').empty(); }});
  $('#paymentIntakeSelect').on('change', function(){ const c=$('#paymentCourseSelect').val(), i=$(this).val(); if(c&&i){ fetchPaymentDetails(c,i); fetchPaymentHistory(c,i); fetchPaymentSchedule(c,i); } else { $('#paymentTableWrapper').hide(); $('#paymentHistory').empty(); $('#paymentScheduleTableBody').empty(); }});

  // ----- Clearance tab -----
  function fetchStudentClearances(){
    const sid=$('#studentIdHidden').val(); if(!sid) return;
    $.get('/api/student/'+sid+'/clearances', res=>{
      const $tb=$('#clearanceTableBody').empty();
      if(res.success && res.clearances && res.clearances.length){
        res.clearances.forEach(info=>$tb.append(`<tr>
          <td>${info.label}</td>
          <td>${info.status?'<span class="badge bg-success">Approved</span>':'<span class="badge bg-warning text-dark">Pending</span>'}</td>
          <td>${info.approved_date||'N/A'}</td>
          <td>${info.remarks||'-'}</td>
          <td><a href="/storage/${info.clearance_slip||''}" target="_blank" class="btn btn-outline-primary btn-sm" ${info.clearance_slip?'':'disabled'}><i class="ti ti-download"></i> Download</a>${!info.clearance_slip?'<span class="text-muted ms-2">No Document</span>':''}</td>
        </tr>`));
        if(!$tb.children().length){ $tb.append('<tr><td colspan="5" class="text-center">No uploaded clearance documents found.</td></tr>'); }
      }else{
        $tb.append('<tr><td colspan="5" class="text-center">No clearance data found.</td></tr>');
      }
    });
  }
  $('a[data-bs-toggle="tab"][href="#clearance"]').on('shown.bs.tab', function(){ fetchStudentClearances(); });

  // ----- Status History tab -----
  function fetchStatusHistory(){
    const sid = $('#studentIdHidden').val(); if(!sid) return;
    $.get('/api/student/' + sid + '/status-history', function(res){
      const $tb = $('#statusHistoryTableBody').empty();
      if(res && res.success && res.history && res.history.length){
        res.history.forEach(function(h, idx){
          const docLink = h.document ? `<a href="/storage/${h.document}" target="_blank">View</a>` : '—';
          // highlight rows that represent a termination event
          const rowClass = (h.to_status || '').toString().toLowerCase() === 'terminated' ? 'table-danger' : '';
          $tb.append(`<tr class="${rowClass}">
            <td>${idx+1}</td>
            <td>${h.from_status || 'N/A'}</td>
            <td>${h.to_status || 'N/A'}</td>
            <td>${h.reason || ''}</td>
            <td>${docLink}</td>
            <td>${h.changed_by_name || h.changed_by || 'System'}</td>
            <td>${h.created_at || ''}</td>
          </tr>`);
        });
        // highlight tab in red and show count
        $('#status-history-tab').addClass('bg-danger text-white');
        $('#statusHistoryCount').text(res.history.length).show();
      } else {
        $tb.append('<tr><td colspan="7" class="text-center text-muted">No status history available.</td></tr>');
        $('#status-history-tab').removeClass('bg-danger text-white');
        $('#statusHistoryCount').hide();
      }
    }).fail(function(){
      $('#statusHistoryTableBody').html('<tr><td colspan="7" class="text-center text-danger">Error loading status history.</td></tr>');
      $('#status-history-tab').removeClass('bg-danger text-white');
      $('#statusHistoryCount').hide();
    });
  }
  $('a[data-bs-toggle="tab"][href="#status-history"]').on('shown.bs.tab', function(){ fetchStatusHistory(); });

  //-- payment summary tab --
  function fetchCoursesForPaymentSummary() {
    const sid = $('#studentIdHidden').val();
    if (!sid) return;
    $.get('/api/student/' + sid + '/courses', function(courseRes) {
      const $courseSelect = $('#summary-course').empty().append('<option value="" selected disabled>Select a Course</option>');
      if (courseRes.success && courseRes.courses.length) {
        courseRes.courses.forEach(c => $courseSelect.append(`<option value="${c.course_id}">${c.course_name}</option>`));
      }
    });
  }
  $('a[data-bs-toggle="tab"][href="#payment-summary"]').on('shown.bs.tab', fetchCoursesForPaymentSummary);

  $('#generatePaymentSummaryBtn').on('click', function() {
    const sid = $('#studentIdHidden').val();
    const courseId = $('#summary-course').val();
    if (!sid || !courseId) {
      showErrorMessage('Please select a course.');
      return;
    }
    $.get('/api/student/' + sid + '/course/' + courseId + '/payment-summary', function(res) {
      if (res.success && res.summary) {
        const summary = res.summary;
        $('#paymentSummarySection').show();
        $('#summary-student-id').text(summary.student.student_id || '');
        $('#summary-student-name').text(summary.student.student_name || '');
        $('#summary-course-name').text(summary.student.course_name || '');
        $('#summary-registration-date').text(summary.student.registration_date || '');
        $('#summary-total-course-fee').text('Rs. ' + (summary.student.total_amount || 0));
        $('#summary-total-paid').text('Rs. ' + (summary.total_paid || 0));
        $('#total-amount').text('Rs. ' + (summary.total_amount || 0));
        $('#total-paid').text('Rs. ' + (summary.total_paid || 0));
        $('#total-outstanding').text('Rs. ' + (summary.total_outstanding || 0));
        $('#payment-rate').text((summary.payment_rate || 0) + '%');

        // Helper to fill tables by payment type
        function fillTable(tableId, detailsType) {
          const details = (summary.payment_details || []).find(d => d.payment_type.toLowerCase().includes(detailsType));
          const $tb = $(tableId).empty();
          if (details && details.payments && details.payments.length) {
            details.payments.forEach(row => {
              $tb.append(`<tr>
                <td>Rs. ${row.total_amount || '-'}</td>
                <td>Rs. ${row.paid_amount || '-'}</td>
                <td>Rs. ${row.outstanding || '-'}</td>
                <td>${row.payment_date || '-'}</td>
                <td>${row.due_date || '-'}</td>
                <td>${row.receipt_no || '-'}</td>
                <td>${row.uploaded_receipt ? `<a href="${row.uploaded_receipt}" target="_blank">View</a>` : '-'}</td>
                <td>${row.installment_number || '-'}</td>
              </tr>`);
            });
          } else {
            $tb.append(`<tr><td colspan="8" class="text-center text-muted">No data available</td></tr>`);
          }
        }
        fillTable('#courseFeeTableBody', 'course');
        fillTable('#franchiseFeeTableBody', 'franchise');
        fillTable('#registrationFeeTableBody', 'registration');
        fillTable('#hostelFeeTableBody', 'hostel');
        fillTable('#libraryFeeTableBody', 'library');
        fillTable('#otherFeeTableBody', 'other');
      } else {
        showErrorMessage(res.message || 'No payment summary found.');
        $('#paymentSummarySection').hide();
      }
    }).fail(function() {
      showErrorMessage('Error loading payment summary.');
      $('#paymentSummarySection').hide();
    });
  });

  // ----- History tab -----
  function fetchCourseRegistrationHistory(){
    const sid = $('#studentIdHidden').val();
    if (!sid) return;
    $.get('/api/student/' + sid + '/course-registration-history', function(res){
      const $tb = $('#historyTableBody').empty();
      if (res.success && res.history && res.history.length) {
        res.history.forEach(h => {
          $tb.append(`<tr data-id="${h.id}" data-course-id="${h.course_id}">
            <td>${h.course_name}</td>
            <td>${h.intake}</td>
            <td>${h.status}</td>
            <td class="specialization-cell">
              <span class="specialization-text">${h.specialization || ''}</span>
              <select class="form-select specialization-select" style="display:none;"></select>
            </td>
            <td class="full-grade-cell">
              <span class="grade-text">${h.full_grade || ''}</span>
              <input type="text" class="form-control grade-input" style="display:none;" value="${h.full_grade || ''}">
            </td>
            <td>
              <button type="button" class="btn btn-sm btn-primary edit-grade-btn">Edit</button>
              <button type="button" class="btn btn-sm btn-success save-grade-btn" style="display:none;">Save</button>
              <button type="button" class="btn btn-sm btn-secondary cancel-grade-btn" style="display:none;">Cancel</button>
            </td>
          </tr>`);
        });
      } else {
        $tb.append('<tr><td colspan="6" class="text-center">No registration history found.</td></tr>');
      }
    }).fail(function(){
      $('#historyTableBody').html('<tr><td colspan="6" class="text-center text-danger">Error loading history.</td></tr>');
    });
  }
  $('a[data-bs-toggle="tab"][href="#history"]').on('shown.bs.tab', function(){
    fetchCourseRegistrationHistory();
  });

  // --- Grade & Specialization Edit/Save/Cancel Handlers ---
  $(document).on('click', '.edit-grade-btn', function(){
    const $tr = $(this).closest('tr');
    $tr.find('.grade-text').hide();
    $tr.find('.grade-input').show().focus();
    $tr.find('.edit-grade-btn').hide();
    $tr.find('.save-grade-btn,.cancel-grade-btn').show();

    // Specialization dropdown
    const courseId = $tr.data('course-id');
    const $specCell = $tr.find('.specialization-cell');
    const $specText = $specCell.find('.specialization-text');
    const $specSelect = $specCell.find('.specialization-select');
    $specText.hide();
    $specSelect.show();

    // Load specializations for this course
    $.get('/api/course/' + courseId + '/specializations', function(res){
      $specSelect.empty();
      $specSelect.append(`<option value="">(No Specialization)</option>`);
      if(res.success && res.specializations && res.specializations.length){
        res.specializations.forEach(function(spec){
          if(spec && spec.toString().trim()!=='') {
            $specSelect.append(`<option value="${spec}">${spec}</option>`);
          }
        });
      }
      const current = $specText.text().trim();
      $specSelect.val(current !== '' ? current : '');
    }).fail(function(){
      $specSelect.empty().append(`<option value="">(No Specialization)</option>`);
    });
  });

  $(document).on('click', '.cancel-grade-btn', function(){
    const $tr = $(this).closest('tr');
    $tr.find('.grade-input').hide();
    $tr.find('.grade-text').show();
    $tr.find('.edit-grade-btn').show();
    $tr.find('.save-grade-btn,.cancel-grade-btn').hide();

    const $specCell = $tr.find('.specialization-cell');
    $specCell.find('.specialization-select').hide();
    $specCell.find('.specialization-text').show();
  });


  $(document).on('click', '.cancel-grade-btn', function(){
    const $tr = $(this).closest('tr');
    $tr.find('.grade-input').hide();
    $tr.find('.grade-text').show();
    $tr.find('.edit-grade-btn').show();
    $tr.find('.save-grade-btn,.cancel-grade-btn').hide();

    // Specialization dropdown
    const $specCell = $tr.find('.specialization-cell');
    $specCell.find('.specialization-select').hide();
    $specCell.find('.specialization-text').show();
  });

  $(document).on('click', '.save-grade-btn', function(e){
    e.preventDefault();
    const $tr = $(this).closest('tr');
    const id = $tr.data('id');
    if (!id) return showErrorMessage('Invalid registration record.');

    const grade = $tr.find('.grade-input').val().trim();
    const specialization = $tr.find('.specialization-select').val();

    $.ajax({
      url: '/api/course-registration/' + id + '/update-grade',
      method: 'POST',
      data: {
        full_grade: grade,
        specialization: specialization,
        _token: '<?php echo e(csrf_token()); ?>'
      },
      success: function(res){
        if(res.success){
          // refresh history from server to reflect DB state (authoritative)
          fetchCourseRegistrationHistory();
          showSuccessMessage('Grade and specialization updated successfully!');
        } else {
          showErrorMessage(res.message || 'Failed to update.');
        }
      },
      error: function(xhr){
        if(xhr && xhr.status === 419){
          showErrorMessage('Session expired. Please refresh the page and try again.');
        } else {
          showErrorMessage('Error updating. Check the browser console / network tab for details.');
        }
      }
    });
  });

  $(document).on('click', '.cancel-grade-btn', function(){
    const $tr = $(this).closest('tr');
    $tr.find('.grade-input').hide();
    $tr.find('.grade-text').show();
    $tr.find('.edit-grade-btn').show();
    $tr.find('.save-grade-btn,.cancel-grade-btn').hide();

    // Specialization dropdown
    const $specCell = $tr.find('.specialization-cell');
    $specCell.find('.specialization-select').hide();
    $specCell.find('.specialization-text').show();
  });

  $(document).on('click', '.save-grade-btn', function(){
    const $tr = $(this).closest('tr');
    const id = $tr.data('id');
    const grade = $tr.find('.grade-input').val().trim();
    const specialization = $tr.find('.specialization-select').val();
    if (!id) return showErrorMessage('Invalid registration record.');
    $.ajax({
      url: '/api/course-registration/' + id + '/update-grade',
      method: 'POST',
      data: { full_grade: grade, specialization: specialization, _token: '<?php echo e(csrf_token()); ?>' },
      success: function(res){
        if(res.success){
          $tr.find('.grade-text').text(grade).show();
          $tr.find('.grade-input').hide();
          $tr.find('.edit-grade-btn').show();
          $tr.find('.save-grade-btn,.cancel-grade-btn').hide();
          $tr.find('.specialization-text').text(specialization).show();
          $tr.find('.specialization-select').hide();
          showSuccessMessage('Grade and specialization updated successfully!');
        }else{
          showErrorMessage(res.message || 'Failed to update.');
        }
      },
      error: function(){
        showErrorMessage('Error updating.');
      }
    });
  });

  //-- Fetch Module Results --
 function fetchModuleResults(courseId, sem){
    const sid=getStudentId(); if(!sid||!courseId||!sem) return;
    $.get('/api/student/'+sid+'/course/'+courseId+'/semester/'+sem+'/results', res=>{
      const $tb=$('#examResultsTableBody').empty();
      if(res.success && res.results.length){
        res.results.forEach(r=>$tb.append(`<tr><td>${r.module_name}</td><td>${r.marks}</td><td>${r.grade}</td></tr>`));
      }
      else{
        $tb.append('<tr><td colspan="3" class="text-center">No results found.</td></tr>');
      }
      $('#examResultsTableWrapper').show();
    });
  }

  
  // Certificates tab (lazy load)
  function fetchStudentCertificates(){
    const sid=$('#studentIdHidden').val(); if(!sid) return;
    $.get('/api/student/'+sid+'/certificates', res=>{
      if(res.success){
        $('#olCertificate').html(res.ol_certificate?`<a href="/storage/certificates/${res.ol_certificate}" target="_blank">View Certificate</a>`:'<span class="text-muted">Not uploaded</span>');
        $('#alCertificate').html(res.al_certificate?`<a href="/storage/certificates/${res.al_certificate}" target="_blank">View Certificate</a>`:'<span class="text-muted">Not uploaded</span>');
        $('#disciplinaryDocument').html(res.disciplinary_issue_document?`<a href="/storage/${res.disciplinary_issue_document}" target="_blank">View Document</a>`:'<span class="text-muted">Not uploaded</span>');
      }else{
        $('#olCertificate,#alCertificate,#disciplinaryDocument').html('<span class="text-muted">Not uploaded</span>');
      }
    });
  }
  $('a[data-bs-toggle="tab"][href="#certificates"]').on('shown.bs.tab', function(){ fetchStudentCertificates(); });

  // ----- Terminate / Reinstate actions -----
  // Intercept terminate click to check for existing clearances first
  $(document).on('click','#terminateBtn', function(e){
    e.preventDefault();
    const studentId = $('#studentIdHidden').val();
    if(!studentId) return showErrorMessage('No student selected.');

    const $btn = $(this);
    if ($btn.hasClass('loading')) return;
    $btn.addClass('loading').prop('disabled', true);

    $.ajax({
      url: '/api/student/' + encodeURIComponent(studentId) + '/clearances',
      method: 'GET',
      success: function(res){
        if(res && res.success && Array.isArray(res.clearances) && res.clearances.length>0){
          let html = '';
          res.clearances.forEach(function(c){
            const statusText = c.status ? 'Approved' : 'Pending';
            const dateText = c.approved_date ? ' ('+c.approved_date+')' : '';
            const remarks = c.remarks ? ' — '+c.remarks : '';
            html += `<li class="mb-2"><strong>${c.label}</strong>: ${statusText}${dateText}${remarks}</li>`;
          });
          $('#profileTerminateClearanceList').html(html);
          new bootstrap.Modal(document.getElementById('terminateClearanceModal')).show();
        } else if(res && res.success) {
          new bootstrap.Modal(document.getElementById('terminateModal')).show();
        } else {
          showErrorMessage(res && res.message ? res.message : 'Failed to check clearances.');
        }
      },
      error: function(){ showErrorMessage('Failed to check clearances.'); },
      complete: function(){ $btn.removeClass('loading').prop('disabled', false); }
    });
  });
  $(document).on('click','#reinstateBtn',()=> new bootstrap.Modal(document.getElementById('reinstateModal')).show());

  $('#confirmTerminate').on('click', function(){
    const sid=$('#studentIdHidden').val(); const reason=$('#terminateReason').val().trim(); const doc=$('#terminateDocument')[0].files[0];
    if(!sid) return showErrorMessage('No student selected.'); if(!reason) return $('#terminateReason').focus();
    const fd=new FormData(); fd.append('_token','<?php echo e(csrf_token()); ?>'); fd.append('student_id',sid); fd.append('reason',reason); if(doc) fd.append('document',doc);
    $.ajax({type:'POST',url:"<?php echo e(route('student.terminate')); ?>",data:fd,processData:false,contentType:false,
      success:function(res){
        if(res.success){ showSuccessMessage(res.message||'Student terminated.'); setStatusUI('terminated'); $('#terminateReason').val(''); $('#terminateDocument').val(''); bootstrap.Modal.getInstance(document.getElementById('terminateModal')).hide(); }
        else{ showErrorMessage(res.message||'Failed to terminate.'); }
      }, error:function(){ showErrorMessage('Error while terminating student.'); }
    });
  });

  $('#confirmReinstate').on('click', function(){
    const sid=$('#studentIdHidden').val(); const reason=$('#reinstateReason').val().trim(); const doc=$('#reinstateDocument')[0].files[0];
    if(!sid) return showErrorMessage('No student selected.'); if(!reason) return $('#reinstateReason').focus();
    const fd=new FormData(); fd.append('_token','<?php echo e(csrf_token()); ?>'); fd.append('student_id',sid); fd.append('reason',reason); if(doc) fd.append('document',doc);
    $.ajax({type:'POST',url:"<?php echo e(route('student.reinstate')); ?>",data:fd,processData:false,contentType:false,
      success:function(res){
        if(res.success){ showSuccessMessage(res.message||'Student re‑registered.'); setStatusUI(res.academic_status||'active'); $('#reinstateReason').val(''); $('#reinstateDocument').val(''); bootstrap.Modal.getInstance(document.getElementById('reinstateModal')).hide(); }
        else{ showErrorMessage(res.message||'Failed to re‑register.'); }
      }, error:function(){ showErrorMessage('Error while re‑registering student.'); }
    });
  });

  // If user chooses to proceed despite clearances
  $(document).on('click', '#proceedToTerminateFromClearance', function(){
    bootstrap.Modal.getInstance(document.getElementById('terminateClearanceModal')).hide();
    new bootstrap.Modal(document.getElementById('terminateModal')).show();
  });

  // Tab coloring
  $('#studentTabs a[data-bs-toggle="tab"]').on('shown.bs.tab', e=>{
    $('#studentTabs a.nav-link').removeClass('bg-primary text-white');
    $(e.target).addClass('bg-primary text-white');
  });

  // Helper function to update profile image
  window.updateStudentProfileImage = function(imagePath) {
    const profileImg = document.getElementById('studentProfilePictureImg');
    if (profileImg) {
      if (imagePath) {
        profileImg.src = '<?php echo e(asset("storage/")); ?>/' + imagePath + '?' + Date.now();
      } else {
        profileImg.src = '<?php echo e(asset("images/profile/user-1.jpg")); ?>';
      }
    }
  };

  // (email validation helper is defined earlier)

  // On initial load, populate from server (if provided)
  <?php if(isset($student)): ?>
    populateStudentProfile(<?php echo json_encode($student, 15, 512) ?>);
    $('#profileSection').show();
    $('#editPictureBtn').show();
  <?php endif; ?>

  // Profile picture upload functionality
  const studentPictureForm = document.getElementById('studentProfilePictureForm');
  const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

  if (studentPictureForm) {
    studentPictureForm.addEventListener('submit', function(e) {
      e.preventDefault();
      
      // Get student ID from the hidden field (populated when student is loaded)
      const studentId = document.getElementById('studentIdHidden')?.value;
      
      if (!studentId) {
        showErrorMessage('Please select a student first');
        return;
      }
      
      const formData = new FormData(studentPictureForm);
      const submitBtn = document.getElementById('saveStudentProfilePictureBtn');
      
      // Disable submit button
      submitBtn.disabled = true;
      submitBtn.textContent = 'Uploading...';
      
      fetch(`/student/update-profile-picture/${studentId}`, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': csrf,
          'Accept': 'application/json',
        },
        body: formData
      })
      .then(resp => resp.json())
      .then(data => {
        if (data.success) {
          // Update the profile image immediately
          const profileImg = document.getElementById('studentProfilePictureImg');
          if (profileImg && data.url) {
            // Force image reload by updating src with cache busting
            const newUrl = data.url + '?' + Date.now();
            
            // Create new image object to preload and verify the image
            const newImage = new Image();
            newImage.onload = function() {
              // Image loaded successfully, now update the profile image
              profileImg.src = newUrl;
              console.log('Profile image successfully updated to:', newUrl);
            };
            
            newImage.onerror = function() {
              console.error('Failed to load new profile image:', newUrl);
              // Keep the current image if new one fails to load
              showErrorMessage('Uploaded image could not be displayed');
            };
            
            // Start loading the new image
            newImage.src = newUrl;
            
          } else {
            console.warn('Profile image element not found or URL missing', {
              profileImg: !!profileImg, 
              url: data.url,
              fullResponse: data
            });
          }
          
          const modalEl = document.getElementById('editPictureModal');
          const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
          modal.hide();
          showSuccessMessage(data.message);
          studentPictureForm.reset();
        } else {
          showErrorMessage(data.message || 'Failed to update profile picture');
        }
      })
      .catch(error => {
        console.error('Error uploading profile picture:', error);
        showErrorMessage('An error occurred while uploading the profile picture');
      })
      .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.textContent = 'Save changes';
      });
    });
  }
});
</script>


<!-- Edit Picture Modal -->
<div class="modal fade" id="editPictureModal" tabindex="-1" role="dialog" aria-labelledby="editPictureModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editPictureModalLabel">Edit Profile Picture</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="studentProfilePictureForm" method="POST" enctype="multipart/form-data">
                    <?php echo csrf_field(); ?>
                    <div class="mb-3">
                        <label for="newStudentProfilePicture" class="form-label fw-bold">New Profile Picture</label>
                        <input type="file" class="form-control" id="newStudentProfilePicture" name="profile_picture" accept="image/*" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" form="studentProfilePictureForm" class="btn btn-primary" id="saveStudentProfilePictureBtn">Save changes</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="terminateModal" tabindex="-1" aria-labelledby="terminateModalLabel" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="terminateModalLabel">Terminate Student</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <div class="mb-3">
        <label class="form-label">Reason <span class="text-danger">*</span></label>
        <textarea id="terminateReason" class="form-control" rows="4" placeholder="Explain the reason"></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">Attach document (optional)</label>
        <input type="file" id="terminateDocument" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>
      <small class="text-muted">This will set academic status to <b>terminated</b>.</small>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" id="confirmTerminate" class="btn btn-danger">Confirm</button>
    </div>
  </div></div>
</div>


<div class="modal fade" id="reinstateModal" tabindex="-1" aria-labelledby="reinstateModalLabel" aria-hidden="true">
  <div class="modal-dialog"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="reinstateModalLabel">Re‑Register Student</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
    </div>
    <div class="modal-body">
      <div class="mb-3">
        <label class="form-label">Reason <span class="text-danger">*</span></label>
        <textarea id="reinstateReason" class="form-control" rows="4" placeholder="Why reinstate?"></textarea>
      </div>
      <div class="mb-2">
        <label class="form-label">Attach document (optional)</label>
        <input type="file" id="reinstateDocument" class="form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
      </div>
      <small class="text-muted">This will set academic status to <b>active</b>.</small>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" id="confirmReinstate" class="btn btn-success">Confirm</button>
    </div>
  </div></div>
</div>

<!-- Modal shown when student has existing clearances -->
<div class="modal fade" id="terminateClearanceModal" tabindex="-1" aria-labelledby="terminateClearanceModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg"><div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title" id="terminateClearanceModalLabel">Student has existing clearances</h5>
      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
    </div>
    <div class="modal-body">
      <p>The selected student has one or more clearance records. Please review them before terminating. Do you still want to proceed?</p>
      <ul id="profileTerminateClearanceList" class="list-unstyled mb-3"></ul>
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      <button type="button" class="btn btn-danger" id="proceedToTerminateFromClearance">Yes, Terminate</button>
    </div>
  </div></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\danid\Desktop\Project\New Nebula\resources\views/student_profile.blade.php ENDPATH**/ ?>