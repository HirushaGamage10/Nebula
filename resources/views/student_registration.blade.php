@extends('inc.app')

@section('title', 'NEBULA | Student Registration')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
        <h2 class="text-center mb-4">Student Registration</h2>
            <hr>

            <div id="spinner-overlay" style="display:none;">
                <div class="lds-ring"><div></div><div></div><div></div><div></div></div>
            </div>

            {{-- Success/Error Modals can be included here if needed --}}

            <form id="registrationForm" action="{{ route('student.register') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf
                {{-- <div id="formErrorSummary" class="alert alert-danger d-none" role="alert"></div> --}}
                
                {{-- Personal Information Section --}}
                <h5 class="mb-3">Personal Information</h5>
                
                <div class="row mb-3">
                    <label for="title" class="col-sm-2 col-form-label">Title<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="title" name="title" required>
                            <option selected disabled value="#">Select a Title</option>
                            @foreach ($titles as $title)
                            <option value="{{ $title['TitleID'] }}">{{ $title['TitleName'] }}</option>
                            @endforeach
                        </select>
                        <div id="titleOtherContainer" class="mt-2" style="display: none;">
                            <input type="text" class="form-control" id="titleOther" name="titleOther" placeholder="Please specify your title">
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="nameWithInitials" class="col-sm-2 col-form-label">Name with Initials<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="nameWithInitials" name="nameWithInitials" placeholder="J. A. Smith" required>
                        <div id="nameError" class="text-danger" style="display: none;">Invalid name format. Only alphabets, full stop, and spaces are allowed.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="fullName" class="col-sm-2 col-form-label">Full Name<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="fullName" name="fullName" placeholder="John Adam Smith" required>
                        <div id="fullNameError" class="text-danger" style="display: none;">Invalid name format. Only letters and spaces are allowed.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="birthday" class="col-sm-2 col-form-label">Birthday<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="date" class="form-control" id="birthday" name="birthday" required>
                        <div id="birthdayError" class="text-danger" style="display: none;">Invalid birth year. Please enter a valid 4-digit year.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="gender" class="col-sm-2 col-form-label">Gender<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="gender" name="gender" required>
                            <option selected disabled value="#">Select a Gender</option>
                            @foreach($genders as $gender)
                            <option value="{{ $gender['id'] }}">{{ $gender['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="idValue" class="col-sm-2 col-form-label">ID Value<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <div class="input-group">
                            <select class="form-select bg-primary text-white" id="identificationType" name="identificationType" style="flex: 0 0 150px;" required>
                                @foreach($idTypes as $idType)
                                <option value="{{ $idType['id'] }}">{{ $idType['id_type'] }}</option>
                                @endforeach
                            </select>
                            <input type="text" class="form-control" id="idValue" name="idValue" placeholder="Enter ID value" required>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="address" class="col-sm-2 col-form-label">Address<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <textarea class="form-control" id="address" name="address" placeholder="123 Main Street, City, Country" rows="3" required></textarea>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="email" class="col-sm-2 col-form-label">Email<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="email" class="form-control" id="email" name="email" placeholder="example@example.com" required>
                        <div id="emailError" class="text-danger" style="display: none;">Invalid email format.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="mobilePhone" class="col-sm-2 col-form-label">Mobile Phone No<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="tel" class="form-control" id="mobilePhone" name="mobilePhone" placeholder="0XXXXXXXXX or +94XXXXXXXXX" required>
                        <div id="mobilePhoneError" class="text-danger" style="display: none;">Invalid phone number. Must be 10 digits starting with 0.</div>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <label for="homePhone" class="col-sm-2 col-form-label">Home Phone No</label>
                    <div class="col-sm-10">
                        <input type="tel" class="form-control" id="homePhone" name="homePhone" placeholder="0XXXXXXXXX or +94XXXXXXXXX">
                        <div id="homePhoneError" class="text-danger" style="display: none;">Invalid phone number. Must be 10 digits starting with 0.</div>
                    </div>
                </div>

                <div class="row mb-3">
                    <label for="whatsappPhone" class="col-sm-2 col-form-label">WhatsApp Number<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="tel" class="form-control" id="whatsappPhone" name="whatsappPhone" placeholder="0XXXXXXXXX or +94XXXXXXXXX" required>
                        <div id="whatsappNumberError" class="text-danger" style="display: none;">Invalid WhatsApp number. Must be 10 digits starting with 0.</div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Academic Qualifications Section --}}
                <h5 class="mb-3">Academic Qualifications</h5>
                <div class="row mb-3">
                    <label for="pending_result" class="col-sm-2 col-form-label">O/L Result Pending?<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select id="pending_result" name="pending_result" class="form-select" required>
                            <option value="" selected disabled>Select an Option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>

                <div id="ol_details_container" style="display: none;">
                    <div class="accordion" id="olAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="olHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOL" aria-expanded="true" aria-controls="collapseOL">
                                    O/L Exam Details
                                </button>
                            </h2>
                            <div id="collapseOL" class="accordion-collapse collapse show" aria-labelledby="olHeading" data-bs-parent="#olAccordion">
                                <div class="accordion-body">
                                    <div class="row mb-3">
                                        <label for="ol_index_no" class="col-sm-2 col-form-label">Index No.<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="ol_index_no" name="ol_index_no" placeholder="XXXXXXXXXX">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="ol_exam_type" class="col-sm-2 col-form-label">Exam Type<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_exam_type" name="ol_exam_type">
                                                <option selected disabled>Select an Exam Type</option>
                                                @foreach($examTypes as $examType)
                                                <option value="{{ $examType }}">{{ $examType }}</option>
                                                @endforeach
                                            </select>
                                            <div id="olExamTypeOtherContainer" class="mt-2" style="display: none;">
                                                <input type="text" class="form-control" id="olExamTypeOther" name="olExamTypeOther" placeholder="Please specify the exam type">
                                            </div>
                                        </div>
                                        <label for="ol_exam_year" class="col-sm-2 col-form-label text-end">Exam Year<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" id="ol_exam_year" name="ol_exam_year" placeholder="eg. 2000">
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-end">
                                        <label class="col-sm-2 col-form-label">Result<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_subject_select">
                                                <option selected disabled>Select a Subject</option>
                                                {{-- Add O/L Subjects here --}}
                                            </select>
                                            <input type="text" class="form-control mt-2" id="ol_subject_other_input" name="ol_subject_other" placeholder="Enter subject name" style="display:none;">
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="ol_result_select">
                                                <option selected disabled>Select a Result</option>
                                                <option>A</option>
                                                <option>B</option>
                                                <option>C</option>
                                                <option>S</option>
                                                <option>F</option>
                                            </select>
                                            <div id="olResultError" class="text-danger mt-1" style="display:none;">
                                                This subject is already added.
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary w-100" id="ol_add_btn">Add</button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-10 offset-sm-2">
                                            <table class="table table-bordered">
                                                <thead class="bg-primary text-white">
                                                    <tr>
                                                        <th>O/L Subject</th>
                                                        <th>Result</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- JS will add results here --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="ol_certificate" class="col-sm-2 col-form-label">O/L Certificate<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control" id="ol_certificate" name="ol_certificate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="al_pending_question_container" style="display: none;" class="row mb-3">
                    <label for="al_pending_result" class="col-sm-2 col-form-label">A/L Results Pending?<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select id="al_pending_result" name="al_pending_result" class="form-select">
                            <option value="" selected disabled>Select an Option</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                
                <div id="al_details_container" style="display: none;">
                     <div class="accordion" id="alAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="alHeading">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAL" aria-expanded="true" aria-controls="collapseAL">
                                    A/L Exam Details
                                </button>
                            </h2>
                            <div id="collapseAL" class="accordion-collapse collapse show" aria-labelledby="alHeading" data-bs-parent="#alAccordion">
                                <div class="accordion-body">
                                     <div class="row mb-3">
                                        <label for="al_index_no" class="col-sm-2 col-form-label">Index No.<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="text" class="form-control" id="al_index_no" name="al_index_no" placeholder="XXXXXXXXXX">
                                        </div>
                                    </div>
                                     <div class="row mb-3">
                                        <label for="al_exam_type" class="col-sm-2 col-form-label">Exam Type<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="al_exam_type" name="al_exam_type">
                                                <option selected disabled>Select an Exam Type</option>
                                                @foreach($examTypes as $examType)
                                                <option value="{{ $examType }}">{{ $examType }}</option>
                                                @endforeach
                                            </select>
                                            <div id="alExamTypeOtherContainer" class="mt-2" style="display: none;">
                                                <input type="text" class="form-control" id="alExamTypeOther" name="alExamTypeOther" placeholder="Please specify the exam type">
                                            </div>
                                        </div>
                                        <label for="al_exam_year" class="col-sm-2 col-form-label text-end">Exam Year<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <input type="number" class="form-control" id="al_exam_year" name="al_exam_year" placeholder="eg. 2000">
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="al_stream" class="col-sm-2 col-form-label">A/L Stream<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                             <select class="form-select" id="al_stream" name="al_stream">
                                                <option selected disabled>Select an A/L Stream</option>
                                                <option value="Physical Science">Physical Science</option>
                                                <option value="Bio Science">Bio Science</option>
                                                <option value="Commerce">Commerce</option>
                                                <option value="Arts">Arts</option>
                                                <option value="Technology">Technology</option>
                                                <option value="Other">Other</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-3 align-items-end">
                                        <label class="col-sm-2 col-form-label">Result<span class="text-danger">*</span></label>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="al_subject_select">
                                                <option selected disabled>Select a Subject</option>
                                                {{-- Add A/L Subjects here --}}
                                            </select>
                                            <input type="text" class="form-control mt-2" id="al_subject_other_input" name="al_subject_other" placeholder="Enter subject name" style="display:none;">
                                        </div>
                                        <div class="col-sm-4">
                                            <select class="form-select" id="al_result_select">
                                                <option selected disabled>Select a Result</option>
                                                <option>A</option>
                                                <option>B</option>
                                                <option>C</option>
                                                <option>S</option>
                                                <option>F</option>
                                            </select>
                                            <div id="alResultError" class="text-danger mt-1" style="display:none;">
                                                This subject is already added.
                                            </div>
                                        </div>
                                        <div class="col-sm-2">
                                            <button type="button" class="btn btn-primary w-100" id="al_add_btn">Add</button>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-10 offset-sm-2">
                                            <table class="table table-bordered">
                                                <thead class="bg-primary text-white">
                                                    <tr>
                                                        <th>A/L Subject</th>
                                                        <th>Result</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    {{-- JS will add results here --}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label for="al_certificate" class="col-sm-2 col-form-label">A/L Certificate<span class="text-danger">*</span></label>
                                        <div class="col-sm-10">
                                            <input type="file" class="form-control" id="al_certificate" name="al_certificate">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <hr class="my-4">
                
                {{-- Enrollment Details --}}
                <h5 class="mb-3">Enrollment Details</h5>
                <div class="p-3 border rounded mb-3" style="background-color: #eaf6f6;">
                    <div class="row mb-3">
                        <label for="institute_location" class="col-sm-2 col-form-label">Institute<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="institute_location" name="institute_location" required>
                                <option selected disabled>Select an Institute Location</option>
                                @foreach($campuses as $campus)
                                <option value="{{ $campus['id'] }}">{{ $campus['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">Foundation program (CAIT)<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="foundationComplete" id="foundationYes" value="1" required>
                                <label class="form-check-label" for="foundationYes">Completed</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="foundationComplete" id="foundationNo" value="0" checked required>
                                <label class="form-check-label" for="foundationNo">Not Completed</label>
                            </div>
                        </div>
                    </div>

                     <div class="row mb-3">
                        <label class="col-sm-2 col-form-label">BTEC Level 3<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btecCompleted" id="btecCompleted" value="1" required>
                                <label class="form-check-label" for="btecCompleted">Completed</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="btecCompleted" id="btecNotCompleted" value="0" checked required>
                                <label class="form-check-label" for="btecNotCompleted">Not Completed</label>
                            </div>
                        </div>
                    </div>
                    
                    <div id="courseSelectionSection" style="display: none;" class="row mb-3">
                        <label for="btec_course" class="col-sm-2 col-form-label">BTEC Course<span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="btec_course" name="btec_course">
                                <option selected disabled>Select the BTEC Course</option>
                                @foreach($btecCourses as $course)
                                <option value="{{ $course['id'] }}">{{ $course['course_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <hr class="my-4">
                
                {{-- Parent/Guardian Details --}}
                <div class="accordion" id="parentDetailsAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="parentDetailsHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#parentDetailsSection" aria-expanded="false" aria-controls="parentDetailsSection">
                                Parent/Guardian Details
                            </button>
                        </h2>
                        <div id="parentDetailsSection" class="accordion-collapse collapse" aria-labelledby="parentDetailsHeading" data-bs-parent="#parentDetailsAccordion">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <label for="parentName" class="col-sm-2 col-form-label">Name<span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="parentName" name="parentName" placeholder="John Doe" required>
                                        <div id="parentNameError" class="text-danger" style="display: none;">Invalid name format.</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="parentProfession" class="col-sm-2 col-form-label">Profession</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="parentProfession" name="parentProfession" placeholder="Engineer, Doctor, etc...">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="parentContactNo" class="col-sm-2 col-form-label">Contact No<span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="parentContactNo" name="parentContactNo" placeholder="0XXXXXXXXXX" required>
                                        <div id="parentContactNoError" class="text-danger" style="display: none;">Invalid contact number.</div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="parentEmail" class="col-sm-2 col-form-label">Email</label>
                                    <div class="col-sm-10">
                                        <input type="email" class="form-control" id="parentEmail" name="parentEmail" placeholder="example@example.com">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="parentAddress" class="col-sm-2 col-form-label">Address<span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="parentAddress" name="parentAddress" placeholder="123 Main St, City, Country" required></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="emergencyContactNo" class="col-sm-2 col-form-label">Emergency Contact No<span class="text-danger">*</span></label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="emergencyContactNo" name="emergencyContactNo" placeholder="0XXXXXXXXX" required>
                                        <div id="emergencyContactNoError" class="text-danger" style="display: none;">Invalid contact number.</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                {{-- Other Information --}}
                <div class="accordion" id="otherInfoAccordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="otherInfoHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOtherInfo" aria-expanded="false" aria-controls="collapseOtherInfo">
                                Other Information
                            </button>
                        </h2>
                        <div id="collapseOtherInfo" class="accordion-collapse collapse" aria-labelledby="otherInfoHeading" data-bs-parent="#otherInfoAccordion">
                            <div class="accordion-body">
                                <div class="row mb-3">
                                    <label for="specialNeeds" class="col-sm-2 col-form-label">Special Needs</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="specialNeeds" name="specialNeeds" placeholder="e.g., Dyslexia, physical disability">
                                    </div>
                                </div>
                                 <div class="row mb-3">
                                    <label for="extraCurricular" class="col-sm-2 col-form-label">Extra-Curricular Activities</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="extraCurricular" name="extraCurricular" placeholder="e.g., Sports, clubs, volunteering" rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="futurePotential" class="col-sm-2 col-form-label">Future Potential</label>
                                    <div class="col-sm-10">
                                        <input type="text" class="form-control" id="futurePotential" name="futurePotential" placeholder="Enter future potential">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="userPhoto" class="col-sm-2 col-form-label">Upload Photo</label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control" id="userPhoto" name="userPhoto" accept="image/*">
                                    </div>
                                </div>
                                 <div class="row mb-3">
                                    <label for="otherDocumentsFiles" class="col-sm-2 col-form-label">Other Documents</label>
                                    <div class="col-sm-10">
                                        <input type="file" class="form-control" id="otherDocumentsFiles" name="otherDocumentsFiles[]" multiple accept=".pdf,.doc,.docx,.jpg,.png">
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <label for="remarks" class="col-sm-2 col-form-label">Remarks</label>
                                    <div class="col-sm-10">
                                        <textarea class="form-control" id="remarks" name="remarks" rows="3" placeholder="Enter remarks"></textarea>
                                    </div>
                                </div>
                                <div class="row mb-3">
    <label for="marketing_survey" class="col-sm-2 col-form-label">How did you hear about us?</label>
    <div class="col-sm-10">
        <select class="form-select" id="marketing_survey" name="marketing_survey"
            onchange="document.getElementById('marketing_survey_other').style.display = (this.value === 'Other' ? 'block' : 'none');
                      document.getElementById('marketing_survey_other').required = (this.value === 'Other');">
            <option selected disabled>Select an option</option>
            <option value="LinkedIn">LinkedIn</option>
            <option value="Facebook">Facebook</option>
            <option value="Radio Advertisement">Radio Advertisement</option>
            <option value="TV advertisement">TV advertisement</option>
            <option value="Other">Other</option>
        </select>
        <input type="text" class="form-control mt-2" id="marketing_survey_other" name="marketing_survey_other" placeholder="Please describe how you heard about us" style="display:none;">
    </div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
                
                {{-- Submit Button --}}
                <button type="submit" class="btn btn-primary w-100 mt-4">Register Student</button>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
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

document.addEventListener('DOMContentLoaded', function() {
    // BTEC Course Selection Toggle
    const btecCompletedRadio = document.getElementById('btecCompleted');
    const btecNotCompletedRadio = document.getElementById('btecNotCompleted');
    const courseSelectionSection = document.getElementById('courseSelectionSection');

    function toggleBtecCourse() {
        if (btecCompletedRadio.checked) {
            courseSelectionSection.style.display = 'block';
            document.getElementById('btec_course').required = true;
        } else {
            courseSelectionSection.style.display = 'none';
            document.getElementById('btec_course').required = false;
        }
    }
    btecCompletedRadio.addEventListener('change', toggleBtecCourse);
    btecNotCompletedRadio.addEventListener('change', toggleBtecCourse);


    // O/L & A/L sections toggle
    const olPendingSelect = document.getElementById('pending_result');
    const olDetailsContainer = document.getElementById('ol_details_container');
    const alPendingContainer = document.getElementById('al_pending_question_container');
    const alPendingSelect = document.getElementById('al_pending_result');
    const alDetailsContainer = document.getElementById('al_details_container');

    olPendingSelect.addEventListener('change', function() {
        if (this.value === 'no') {
            olDetailsContainer.style.display = 'block';
            alPendingContainer.style.display = 'flex'; // it's a row, so flex for alignment
        } else { // 'yes'
            olDetailsContainer.style.display = 'none';
            alPendingContainer.style.display = 'none';
            alDetailsContainer.style.display = 'none';
            // Also reset the A/L pending dropdown
            alPendingSelect.value = '';
        }
    });

    alPendingSelect.addEventListener('change', function() {
        if (this.value === 'no') {
            alDetailsContainer.style.display = 'block';
        } else { // 'yes'
            alDetailsContainer.style.display = 'none';
        }
    });

    // --- Validation Listeners ---
    function setupValidator(inputId, errorId, pattern) {
        const input = document.getElementById(inputId);
        const error = document.getElementById(errorId);
        input.addEventListener('input', function() {
            if (pattern.test(this.value)) {
                error.style.display = 'none';
            } else {
                error.style.display = 'block';
            }
        });
    }

    setupValidator('nameWithInitials', 'nameError', /^[A-Za-z.\s]+$/);
    setupValidator('fullName', 'fullNameError', /^[A-Za-z\s]+$/);
    setupValidator('email', 'emailError', /^[^\s@]+@[^\s@]+\.[^\s@]+$/);
    setupValidator('mobilePhone', 'mobilePhoneError', /^0\d{9}$/);
    setupValidator('homePhone', 'homePhoneError', /^(|0\d{9})$/); // Optional
    setupValidator('whatsappPhone', 'whatsappNumberError', /^0\d{9}$/);
    setupValidator('parentName', 'parentNameError', /^[A-Za-z\s]+$/);
    setupValidator('parentContactNo', 'parentContactNoError', /^0\d{9}$/);
    setupValidator('emergencyContactNo', 'emergencyContactNoError', /^0\d{9}$/);
    
    const birthdayInput = document.getElementById("birthday");
    const birthdayError = document.getElementById("birthdayError");
    birthdayInput.addEventListener('input', function() {
        const year = new Date(this.value).getFullYear();
        if (year >= 1890 && year <= new Date().getFullYear()) {
            birthdayError.style.display = "none";
        } else {
            birthdayError.style.display = "block";
        }
    });

    const olSubjectSelect = document.getElementById('ol_subject_select');
    const alStreamSelect = document.getElementById('al_stream');
    const olSubjectOtherInput = document.getElementById('ol_subject_other_input');
    const alSubjectSelect = document.getElementById('al_subject_select');
    const alSubjectOtherInput = document.getElementById('al_subject_other_input');

    // Define the subject lists
    const localSubjects = [
        'Sinhala',
        'History',
        'Religion',
        'English',
        'Maths',
        'Science',
        'Other'
    ];
    const otherSubjects = [
        'Other'
    ];

    // Define A/L subjects for each stream
    const alStreamSubjects = {
        'Physical Science': ['Combined Maths', 'Chemistry', 'Physics', 'English', 'Other'],
        'Bio Science': ['Biology', 'Physics', 'Chemistry', 'English', 'Other'],
        'Arts': ['Sinhala', 'Political Science', 'English', 'Other'],
        'Commerce': ['Economics', 'Business Studies', 'Accounting', 'English', 'Other'],
        'Technology': ['Science for Technology', 'Bio System Technology', 'Engineering Technology', 'ICT', 'English', 'Other'],
        'Other': ['English', 'Other']
    };

    function populateOlSubjects(subjects) {
        olSubjectSelect.innerHTML = '<option selected disabled>Select a Subject</option>';
        subjects.forEach(function(subject) {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            olSubjectSelect.appendChild(option);
        });
    }

    function populateAlSubjects(subjects) {
        alSubjectSelect.innerHTML = '<option selected disabled>Select a Subject</option>';
        subjects.forEach(function(subject) {
            const option = document.createElement('option');
            option.value = subject;
            option.textContent = subject;
            alSubjectSelect.appendChild(option);
        });
    }

    // Initial population (default to local)
    populateOlSubjects(localSubjects);

    // Listen for stream change
    alStreamSelect.addEventListener('change', function() {
        populateOlSubjects(localSubjects);
        olSubjectOtherInput.style.display = 'none';
        
        // Update A/L subjects based on selected stream
        const selectedStream = alStreamSelect.value;
        const subjects = alStreamSubjects[selectedStream] || alStreamSubjects['Other'];
        populateAlSubjects(subjects);
        alSubjectOtherInput.style.display = 'none';
    });

    // Listen for O/L subject change
    olSubjectSelect.addEventListener('change', function() {
        if (olSubjectSelect.value === 'Other') {
            olSubjectOtherInput.style.display = 'block';
        } else {
            olSubjectOtherInput.style.display = 'none';
        }
    });

    // Listen for A/L subject change
    alSubjectSelect.addEventListener('change', function() {
        if (alSubjectSelect.value === 'Other') {
            alSubjectOtherInput.style.display = 'block';
        } else {
            alSubjectOtherInput.style.display = 'none';
        }
    });


    // Initialize for O/L & A/L subject addition/removal
    setupExamSubjects({
        addBtnId: 'ol_add_btn',
        subjectSelectId: 'ol_subject_select',
        subjectOtherInputId: 'ol_subject_other_input',
        resultSelectId: 'ol_result_select',
        tableBodySelector: '#ol_details_container table tbody',
        errorId: 'olResultError',
        indexNoId: 'ol_index_no',
        examTypeId: 'ol_exam_type',
        examYearId: 'ol_exam_year'
    });

    setupExamSubjects({
        addBtnId: 'al_add_btn',
        subjectSelectId: 'al_subject_select',
        subjectOtherInputId: 'al_subject_other_input',
        resultSelectId: 'al_result_select',
        tableBodySelector: '#al_details_container table tbody',
        errorId: 'alResultError',
        indexNoId: 'al_index_no',
        examTypeId: 'al_exam_type',
        examYearId: 'al_exam_year'
    });

});

// Handle "Other" option for title field
document.getElementById('title').addEventListener('change', function() {
    const titleOtherContainer = document.getElementById('titleOtherContainer');
    const titleOtherInput = document.getElementById('titleOther');
    
    if (this.value === 'Other') {
        titleOtherContainer.style.display = 'block';
        titleOtherInput.required = true;
    } else {
        titleOtherContainer.style.display = 'none';
        titleOtherInput.required = false;
        titleOtherInput.value = '';
    }
});

// Handle "Other" option for OL exam type field
document.getElementById('ol_exam_type').addEventListener('change', function() {
    const olExamTypeOtherContainer = document.getElementById('olExamTypeOtherContainer');
    const olExamTypeOtherInput = document.getElementById('olExamTypeOther');
    
    if (this.value === 'Other') {
        olExamTypeOtherContainer.style.display = 'block';
        olExamTypeOtherInput.required = true;
    } else {
        olExamTypeOtherContainer.style.display = 'none';
        olExamTypeOtherInput.required = false;
        olExamTypeOtherInput.value = '';
    }
});

// Handle "Other" option for AL exam type field
document.getElementById('al_exam_type').addEventListener('change', function() {
    const alExamTypeOtherContainer = document.getElementById('alExamTypeOtherContainer');
    const alExamTypeOtherInput = document.getElementById('alExamTypeOther');
    
    if (this.value === 'Other') {
        alExamTypeOtherContainer.style.display = 'block';
        alExamTypeOtherInput.required = true;
    } else {
        alExamTypeOtherContainer.style.display = 'none';
        alExamTypeOtherInput.required = false;
        alExamTypeOtherInput.value = '';
    }
});


function setupExamSubjects(config) {
    const { addBtnId, subjectSelectId, subjectOtherInputId, resultSelectId, 
            tableBodySelector, errorId, indexNoId, examTypeId, examYearId } = config;

    const addBtn = document.getElementById(addBtnId);
    const subjectSelect = document.getElementById(subjectSelectId);
    const subjectOtherInput = document.getElementById(subjectOtherInputId);
    const resultSelect = document.getElementById(resultSelectId);
    const tableBody = document.querySelector(tableBodySelector);
    const errorEl = document.getElementById(errorId);

    // Validation fields
    const indexNoInput = document.getElementById(indexNoId);
    const examTypeSelect = document.getElementById(examTypeId);
    const examYearInput = document.getElementById(examYearId);

    // Helper: show error under input
    function showError(inputEl, message) {
        let errorDiv = inputEl.parentNode.querySelector('.field-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'text-danger mt-1 field-error';
            inputEl.parentNode.appendChild(errorDiv);
        }
        errorDiv.textContent = message;
        errorDiv.style.display = 'block';
    }

    // Helper: hide error
    function hideError(inputEl) {
        const errorDiv = inputEl.parentNode.querySelector('.field-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }

    addBtn.addEventListener('click', function () {
        let hasError = false;

        // ✅ Validate index no
        if (!indexNoInput.value.trim()) {
            showError(indexNoInput, "Index No is required.");
            hasError = true;
        } else {
            hideError(indexNoInput);
        }

        // ✅ Validate exam type
        if (!examTypeSelect.value || examTypeSelect.value === 'Select an Exam Type') {
            showError(examTypeSelect, "Exam Type is required.");
            hasError = true;
        } else {
            hideError(examTypeSelect);
        }

        // ✅ Validate exam year
        if (!examYearInput.value.trim() || examYearInput.value.length !== 4) {
            showError(examYearInput, "Enter a valid 4-digit Exam Year.");
            hasError = true;
        } else {
            hideError(examYearInput);
        }

        if (hasError) return; // stop if any validation failed

        let subject = subjectSelect.value;
        let result = resultSelect.value;
        let subjectOther = subjectOtherInput.value;

        if (subject === 'Other') subject = subjectOther;

        if (!subject || !result || subject === 'Select a Subject' || result === 'Select a Result') {
            showError(resultSelect, "Select both Subject and Result.");
            return;
        } else {
            hideError(resultSelect);
        }

        // ✅ Check duplicates
        let exists = false;
        tableBody.querySelectorAll('tr').forEach(row => {
            if (row.querySelector('td').textContent === subject) {
                exists = true;
            }
        });

        if (exists) {
            errorEl.style.display = 'block';
            return;
        } else {
            errorEl.style.display = 'none';
        }

        // Add row
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>${subject}</td>
            <td>${result}</td>
            <td><button type="button" class="btn btn-danger btn-sm remove-btn">Remove</button></td>
        `;
        tableBody.appendChild(row);

        // Reset inputs
        subjectSelect.value = 'Select a Subject';
        resultSelect.value = 'Select a Result';
        subjectOtherInput.value = '';
        subjectOtherInput.style.display = 'none';
    });

    tableBody.addEventListener('click', function (e) {
        if (e.target.classList.contains('remove-btn')) {
            e.target.closest('tr').remove();
            errorEl.style.display = 'none';
        }
    });
}



$(document).ready(function() {
    // Ensure invalid fields inside collapsed accordions are made visible
    function ensureVisibleInvalidFields(formEl){
        const invalidEls = formEl.querySelectorAll(':invalid');
        invalidEls.forEach((el)=>{
            const collapse = el.closest('.collapse');
            if(collapse && !collapse.classList.contains('show')){
                try { new bootstrap.Collapse(collapse, { toggle: true }); } catch(_) { collapse.classList.add('show'); }
                const toggleBtn = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                if (toggleBtn) toggleBtn.classList.remove('collapsed');
            }
        });
        if(invalidEls.length){
            const target = invalidEls[0];
            target.scrollIntoView({ behavior: 'smooth', block: 'center' });
            try { target.focus({ preventScroll: true }); } catch(_) {}
        }
    }
    @if(session('success'))
        showToast("{{ session('success') }}", 'success');
    @endif

    // Helper: show server-side validation errors inline
    function showServerErrors(errorBag){
        const form = document.getElementById('registrationForm');
        const summary = document.getElementById('formErrorSummary');
        // Reset previous invalid states
        form.querySelectorAll('.is-invalid').forEach(el=>el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.dynamic').forEach(el=>el.remove());

        const list = document.createElement('ul');
        list.className = 'mb-0 ps-3';
        Object.entries(errorBag).forEach(([name, messages])=>{
            const msg = Array.isArray(messages) ? messages[0] : messages;
            const li = document.createElement('li');
            li.textContent = msg;
            list.appendChild(li);

            const field = form.querySelector(`[name="${name}"]`);
            if(field){
                // Make sure hidden/collapsed panels are visible first
                const collapse = field.closest('.collapse');
                if(collapse && !collapse.classList.contains('show')){
                    try { new bootstrap.Collapse(collapse, { toggle: true }); } catch(_) { collapse.classList.add('show'); }
                    const toggleBtn = document.querySelector(`[data-bs-target="#${collapse.id}"]`);
                    if (toggleBtn) toggleBtn.classList.remove('collapsed');
                }
                field.classList.add('is-invalid');
                const fb = document.createElement('div');
                fb.className = 'invalid-feedback dynamic';
                fb.textContent = msg;
                if(field.parentNode) field.parentNode.appendChild(fb);
            }
        });
        summary.innerHTML = '';
        summary.appendChild(list);
        summary.classList.remove('d-none');
        summary.scrollIntoView({ behavior:'smooth', block:'start' });
    }

    $('#registrationForm').on('submit', function(e) {
        e.preventDefault();
        const form = this;

        // Run HTML5 validation manually and show messages inline
        ensureVisibleInvalidFields(form);
        // Build client-side inline feedback and summary
        const summary = document.getElementById('formErrorSummary');
        // clear old states
        form.querySelectorAll('.is-invalid').forEach(el=>el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback.dynamic').forEach(el=>el.remove());
        summary.classList.add('d-none');
        summary.innerHTML = '';

        const invalidEls = Array.from(form.querySelectorAll('input, select, textarea')).filter(el=>!el.checkValidity());
        if (invalidEls.length) {
            const list = document.createElement('ul');
            list.className = 'mb-0 ps-3';
            invalidEls.forEach(el=>{
                // attach inline
                el.classList.add('is-invalid');
                const fb = document.createElement('div');
                fb.className = 'invalid-feedback dynamic';
                fb.textContent = el.validationMessage || 'This field is required.';
                // Put feedback after the control
                if (el.parentNode) el.parentNode.appendChild(fb);
                // Add to summary
                const label = form.querySelector(`label[for="${el.id}"]`);
                const labelText = label ? label.textContent.replace('*','').trim() : (el.name || 'Field');
                const li = document.createElement('li');
                li.textContent = `${labelText}: ${fb.textContent}`;
                list.appendChild(li);
            });
            summary.appendChild(list);
            summary.classList.remove('d-none');
            // focus first invalid
            invalidEls[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
            try { invalidEls[0].focus({ preventScroll: true }); } catch(_) {}
            return; // stop here until user fixes errors
        }

    var formData = new FormData(form);
        
        // Handle "Other" values for title and exam types
        var title = $('#title').val();
        if (title === 'Other') {
            var titleOther = $('#titleOther').val();
            if (titleOther.trim()) {
                formData.set('title', titleOther);
            }
        }
        
        var olExamType = $('#ol_exam_type').val();
        if (olExamType === 'Other') {
            var olExamTypeOther = $('#olExamTypeOther').val();
            if (olExamTypeOther.trim()) {
                formData.set('ol_exam_type', olExamTypeOther);
            }
        }
        
        var alExamType = $('#al_exam_type').val();
        if (alExamType === 'Other') {
            var alExamTypeOther = $('#alExamTypeOther').val();
            if (alExamTypeOther.trim()) {
                formData.set('al_exam_type', alExamTypeOther);
            }
        }

        // Collect O/L subjects and results from the table
        var olSubjects = [];
        var olResults = [];
        $('#ol_details_container table tbody tr').each(function() {
            var subject = $(this).find('td').eq(0).text();
            var result = $(this).find('td').eq(1).text();
            if(subject && result) {
                olSubjects.push(subject);
                olResults.push(result);
            }
        });
        // Append to FormData
        olSubjects.forEach(function(subject) {
            formData.append('ol_subjects[]', subject);
        });
        olResults.forEach(function(result) {
            formData.append('ol_results[]', result);
        });

        // Collect A/L subjects and results from the table
        var alSubjects = [];
        var alResults = [];
        $('#al_details_container table tbody tr').each(function() {
            var subject = $(this).find('td').eq(0).text();
            var result = $(this).find('td').eq(1).text();
            if(subject && result) {
                alSubjects.push(subject);
                alResults.push(result);
            }
        });
        // Append to FormData
        alSubjects.forEach(function(subject) {
            formData.append('al_subjects[]', subject);
        });
        alResults.forEach(function(result) {
            formData.append('al_results[]', result);
        });

        $.ajax({
            url: $(this).attr('action'),
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // clear summary on success
                const summary = document.getElementById('formErrorSummary');
                summary.classList.add('d-none');
                summary.innerHTML = '';
                showToast('Student has been registered successfully!', 'success');
                setTimeout(function() {
                    location.reload();
                }, 1500);
            },
            error: function(xhr) {
                // 422 Unprocessable Entity from Laravel validation
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    showServerErrors(xhr.responseJSON.errors);
                    return;
                }
                let errorMessage = 'Validation failed';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                showToast(errorMessage, 'danger');
            }
        });
    });
});
</script>
@endpush