@extends('inc.app')

@section('title', 'NEBULA | All Clearance')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">All Clearance Requests</h2>
            <hr>
            <ul class="nav nav-tabs mb-4" id="clearanceTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="intake-tab" data-bs-toggle="tab" data-bs-target="#intake-clearance" type="button" role="tab" aria-controls="intake-clearance" aria-selected="true">Intake Clearance</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="individual-tab" data-bs-toggle="tab" data-bs-target="#individual-clearance" type="button" role="tab" aria-controls="individual-clearance" aria-selected="false">Individual Clearance</button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="status-tab" data-bs-toggle="tab" data-bs-target="#request-status" type="button" role="tab" aria-controls="request-status" aria-selected="false">Request Status</button>
                </li>
            </ul>
            <div class="tab-content" id="clearanceTabsContent">
                <!-- Intake Clearance Tab -->
                <div class="tab-pane fade show active" id="intake-clearance" role="tabpanel" aria-labelledby="intake-tab">
                    <form id="clearanceFilterForm">
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="locationDropdown" class="col-sm-3 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="locationDropdown" class="form-select" required>
                                    <option selected disabled value="">Select a Location</option>
                                    <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                    <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                    <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="courseDropdown" class="col-sm-3 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="courseDropdown" class="form-select" required>
                                    <option selected disabled value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->course_id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="intakeDropdown" class="col-sm-3 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="intakeDropdown" class="form-select" required disabled>
                                    <option selected disabled value="">Select Intake</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div id="clearanceTableSection" style="display:none;">
                        <table class="table table-bordered align-middle text-center mt-4">
                            <thead style="background-color: #5D9CFF; color: white;">
                                <tr>
                                    <th>Description of Clearance</th>
                                    <th>Clearance Form</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>Payment</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-clearance-btn" data-type="payment" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Library</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-clearance-btn" data-type="library" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Hostel</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-clearance-btn" data-type="hostel" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>Project Tutor</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-clearance-btn" data-type="project" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    @if(in_array(auth()->user()->user_role, ['Librarian', 'Hostel Manager', 'Bursar', 'Project Tutor']))
                    <div id="studentListSection" style="display:none;">
                        <h5 class="fw-bold mb-3">Students in Selected Intake</h5>
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle text-center">
                                <thead style="background-color: #5D9CFF; color: white;">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Clearance Status</th>
                                    </tr>
                                </thead>
                                <tbody id="studentListTableBody">
                                    <!-- Populated by JS -->
                                </tbody>
                            </table>
                        </div>
                </div>
                    @endif
                </div>
                <!-- Individual Clearance Tab -->
                <div class="tab-pane fade" id="individual-clearance" role="tabpanel" aria-labelledby="individual-tab">
                    <form id="individualClearanceFilterForm">
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="ind_locationDropdown" class="col-sm-3 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="ind_locationDropdown" class="form-select" required>
                                    <option selected disabled value="">Select Location</option>
                                    <option value="Welisara">Welisara</option>
                                    <option value="Moratuwa">Moratuwa</option>
                                    <option value="Peradeniya">Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="ind_courseDropdown" class="col-sm-3 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="ind_courseDropdown" class="form-select" required>
                                    <option selected disabled value="">Select Course</option>
                                    @foreach($courses as $course)
                                        <option value="{{ $course->course_id }}">{{ $course->course_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="ind_intakeDropdown" class="col-sm-3 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="ind_intakeDropdown" class="form-select" required disabled>
                                    <option selected disabled value="">Select Intake</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center mx-3">
                            <label for="ind_studentDropdown" class="col-sm-3 col-form-label fw-bold">Student<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select id="ind_studentDropdown" class="form-select" required disabled>
                                    <option selected disabled value="">Select Student</option>
                                </select>
                            </div>
                        </div>
                    </form>
                    <div id="individualClearanceTableSection" style="display:none;">
                        <table class="table table-bordered align-middle text-center mt-4">
            <thead style="background-color: #5D9CFF; color: white;">
                <tr>
                    <th>Description of Clearance</th>
                    <th>Clearance Form</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Payment</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-individual-clearance-btn" data-type="payment" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                </tr>
                <tr>
                    <td>Library</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-individual-clearance-btn" data-type="library" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                </tr>
                <tr>
                    <td>Hostel</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-individual-clearance-btn" data-type="hostel" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                </tr>
                <tr>
                    <td>Project Tutor</td>
                                    <td>
                                        @if(auth()->user()->user_role === 'Program Administrator (level 01)' || auth()->user()->user_role === 'Developer')
                                            <button class="btn px-4 send-individual-clearance-btn" data-type="project" style="background-color: #5D9CFF; color: white;">Send</button>
                                        @endif
                                    </td>
                </tr>
            </tbody>
        </table>
</div>
            </div>
            
            <!-- Request Status Tab -->
            <div class="tab-pane fade" id="request-status" role="tabpanel" aria-labelledby="status-tab">
                <div class="row">
                    <!-- Summary Cards -->
                    <div class="col-md-12 mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">{{ $pendingRequests->count() }}</h4>
                                        <p class="card-text">Pending Requests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">{{ $approvedRequests->count() }}</h4>
                                        <p class="card-text">Approved Requests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-danger text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">{{ $rejectedRequests->count() }}</h4>
                                        <p class="card-text">Rejected Requests</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-info text-white">
                                    <div class="card-body text-center">
                                        <h4 class="card-title">{{ $allClearanceRequests->count() }}</h4>
                                        <p class="card-text">Total Requests</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Intake Requests Table -->
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="ti ti-users"></i> Intake Clearance Requests</h5>
                            </div>
                            <div class="card-body">
                                @if($intakeRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Intake</th>
                                                    <th>Course</th>
                                                    <th>Location</th>
                                                    <th>Clearance Type</th>
                                                    <th>Total Students</th>
                                                    <th>Responses Received</th>
                                                    <th>Requested Date</th>
                                                    <th>Progress</th>
                                                    <th>Actions</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($intakeRequests as $request)
                                                    <tr>
                                                        <td>
                                                            <strong>{{ $request->intake->batch }}</strong>
                                                        </td>
                                                        <td>{{ $request->course->course_name }}</td>
                                                        <td>{{ $request->location }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($request->clearance_type) }}
                                                            </span>
                                                        </td>
                                                        <td>
                                                            <span class="badge bg-info">{{ $request->total_students }}</span>
                                                        </td>
                                                        <td>
                                                            <div class="d-flex align-items-center">
                                                                <span class="badge bg-success me-1">{{ $request->approved_count }} Approved</span>
                                                                <span class="badge bg-danger me-1">{{ $request->rejected_count }} Rejected</span>
                                                                <span class="badge bg-warning">{{ $request->pending_count }} Pending</span>
                                                            </div>
                                                        </td>
                                                        <td>{{ \Carbon\Carbon::parse($request->requested_at)->format('d/m/Y H:i') }}</td>
                                                        <td>
                                                            @php
                                                                $progress = $request->total_students > 0 ? 
                                                                    round(($request->received_count / $request->total_students) * 100) : 0;
                                                            @endphp
                                                            <div class="progress" style="height: 20px;">
                                                                <div class="progress-bar bg-success" role="progressbar" 
                                                                     style="width: {{ $progress }}%" 
                                                                     aria-valuenow="{{ $progress }}" 
                                                                     aria-valuemin="0" 
                                                                     aria-valuemax="100">
                                                                    {{ $progress }}%
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <button class="btn btn-sm btn-outline-primary view-intake-details" 
                                                                    data-intake="{{ $request->intake->intake_id }}"
                                                                    data-course="{{ $request->course->course_id }}"
                                                                    data-location="{{ $request->location }}"
                                                                    data-type="{{ $request->clearance_type }}">
                                                                <i class="ti ti-eye"></i> View Details
                                                            </button>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="ti ti-info-circle text-info" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3">No Intake Clearance Requests</h5>
                                        <p class="text-muted">No intake clearance requests have been sent yet.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Individual Requests Table -->
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0"><i class="ti ti-user"></i> Individual Clearance Requests</h5>
                            </div>
                            <div class="card-body">
                                @if($individualRequests->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Student ID</th>
                                                    <th>Student Name</th>
                                                    <th>Clearance Type</th>
                                                    <th>Course</th>
                                                    <th>Intake</th>
                                                    <th>Location</th>
                                                    <th>Status</th>
                                                    <th>Requested Date</th>
                                                    <th>Processed By</th>
                                                    <th>Processed Date</th>
                                                    <th>Remarks</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($individualRequests as $request)
                                                    <tr>
                                                        <td>{{ $request->student->student_id }}</td>
                                                        <td>{{ $request->student->name_with_initials }}</td>
                                                        <td>
                                                            <span class="badge bg-secondary">
                                                                {{ ucfirst($request->clearance_type) }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $request->course->course_name }}</td>
                                                        <td>{{ $request->intake->batch ?? 'N/A' }}</td>
                                                        <td>{{ $request->location }}</td>
                                                        <td>
                                                            <span class="badge bg-{{ $request->status_color }}">
                                                                {{ $request->status_text }}
                                                            </span>
                                                        </td>
                                                        <td>{{ $request->requested_at->format('d/m/Y H:i') }}</td>
                                                        <td>{{ $request->approvedBy->name ?? 'N/A' }}</td>
                                                        <td>{{ $request->approved_at ? $request->approved_at->format('d/m/Y H:i') : 'N/A' }}</td>
                                                        <td>{{ $request->remarks ?? 'N/A' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <div class="text-center py-4">
                                        <i class="ti ti-info-circle text-info" style="font-size: 3rem;"></i>
                                        <h5 class="mt-3">No Individual Clearance Requests</h5>
                                        <p class="text-muted">No individual clearance requests have been sent yet.</p>
                                    </div>
                                @endif
                            </div>
                            </div>
                        </div>
    </div>
</div>
            </div>
            <!-- Toast for success -->
        </div>
    </div>
</div>

<!-- Modal for Intake Details -->
<div class="modal fade" id="intakeDetailsModal" tabindex="-1" aria-labelledby="intakeDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="intakeDetailsModalLabel">Intake Clearance Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="intakeDetailsContent">
                    <!-- Content will be loaded here -->
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1055;">
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Intake dropdown population
    $('#courseDropdown, #locationDropdown').on('change', function() {
        $('#intakeDropdown').val('').prop('disabled', true);
        $('#clearanceTableSection').hide();
        $('#studentListSection').hide();
        if($('#courseDropdown').val() && $('#locationDropdown').val()) {
            // AJAX to get intakes for course/location
            $.ajax({
                url: '{{ route('module.management.getIntakes') }}',
                method: 'POST',
                data: {
                    course_id: $('#courseDropdown').val(),
                    location: $('#locationDropdown').val(),
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if(response.success) {
                        $('#intakeDropdown').empty().append('<option selected disabled value="">Select Intake</option>');
                        response.data.forEach(function(intake) {
                            $('#intakeDropdown').append(`<option value="${intake.intake_id}">${intake.intake_name}</option>`);
                        });
                        $('#intakeDropdown').prop('disabled', false);
                    }
                }
            });
        }
    });
    // Show table when all fields selected
    $('#intakeDropdown').on('change', function() {
        if($('#locationDropdown').val() && $('#courseDropdown').val() && $('#intakeDropdown').val()) {
            $('#clearanceTableSection').show();
            @if(in_array(auth()->user()->user_role, ['Librarian', 'Hostel Manager', 'Bursar', 'Project Tutor']))
            loadStudentList();
            $('#studentListSection').show();
            @endif
            } else {
            $('#clearanceTableSection').hide();
            $('#studentListSection').hide();
        }
    });
    // Send button logic
    $(document).on('click', '.send-clearance-btn', function() {
        const button = $(this);
        const type = button.data('type');
        const location = $('#locationDropdown').val();
        const courseId = $('#courseDropdown').val();
        const intakeId = $('#intakeDropdown').val();
        
        // Prevent multiple clicks
        if (button.hasClass('loading')) {
            return;
        }
        
        // Add loading state
        button.addClass('loading').prop('disabled', true).text('Sending...');
        
        // AJAX to backend to send notification (to be implemented)
        $.ajax({
            url: '{{ route('clearance.sendRequest') }}',
            method: 'POST',
            data: {
                type: type,
                location: location,
                course_id: courseId,
                intake_id: intakeId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                showToast(response.message || 'Clearance request sent successfully!', 'success');
            },
            error: function() {
                showToast('Failed to send clearance request.', 'danger');
            },
            complete: function() {
                // Reset button state
                button.removeClass('loading').prop('disabled', false).text('Send');
            }
        });
    });
    // Show toast
    function showToast(message, type) {
        // Remove any existing toasts first
        $('.toast').remove();
        
        const toast = `<div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true"><div class="d-flex"><div class="toast-body">${message}</div><button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button></div></div>`;
        $('.toast-container').append(toast);
        
        // Show the toast and auto-remove after it's hidden
        const toastElement = $('.toast').last();
        toastElement.toast({
            delay: 3000,
            autohide: true
        });
        toastElement.toast('show');
        toastElement.on('hidden.bs.toast', function() { 
            $(this).remove(); 
        });
    }
    // Load student list for other users
    function loadStudentList() {
        const intakeId = $('#intakeDropdown').val();
        if(!intakeId) return;
        $.ajax({
            url: '{{ route('clearance.getStudentsForIntake') }}',
            method: 'POST',
            data: {
                intake_id: intakeId,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    let html = '';
                    response.data.forEach(function(student) {
                        html += `<tr><td>${student.student_id}</td><td>${student.name}</td><td>${student.clearance_status}</td></tr>`;
                    });
                    $('#studentListTableBody').html(html);
                }
            }
        });
    }
    
    // Handle View Details button for intake requests
    $(document).on('click', '.view-intake-details', function() {
        const intakeId = $(this).data('intake');
        const courseId = $(this).data('course');
        const location = $(this).data('location');
        const type = $(this).data('type');
        
        // Show loading state
        $('#intakeDetailsContent').html('<div class="text-center"><i class="ti ti-loader ti-spin" style="font-size: 2rem;"></i><p>Loading details...</p></div>');
        $('#intakeDetailsModal').modal('show');
        
        // AJAX to get detailed student list for this intake request
        $.ajax({
            url: '{{ route("clearance.getIntakeDetails") }}',
            method: 'POST',
            data: {
                intake_id: intakeId,
                course_id: courseId,
                location: location,
                clearance_type: type,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    let tableHtml = `
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <h6 class="text-muted">Intake: ${response.intake_name} | Course: ${response.course_name} | Location: ${response.location}</h6>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Status</th>
                                        <th>Processed By</th>
                                        <th>Processed Date</th>
                                        <th>Remarks</th>
                                    </tr>
                                </thead>
                                <tbody>
                    `;
                    
                    response.students.forEach(function(student) {
                        const statusBadge = `<span class="badge bg-${student.status_color}">${student.status_text}</span>`;
                        tableHtml += `
                            <tr>
                                <td>${student.student_id}</td>
                                <td>${student.student_name}</td>
                                <td>${statusBadge}</td>
                                <td>${student.processed_by || 'N/A'}</td>
                                <td>${student.processed_date || 'N/A'}</td>
                                <td>${student.remarks || 'N/A'}</td>
                            </tr>
                        `;
                    });
                    
                    tableHtml += `
                                </tbody>
                            </table>
                        </div>
                    `;
                    
                    $('#intakeDetailsContent').html(tableHtml);
                } else {
                    $('#intakeDetailsContent').html('<div class="alert alert-danger">Failed to load details: ' + response.message + '</div>');
                }
            },
            error: function() {
                $('#intakeDetailsContent').html('<div class="alert alert-danger">Failed to load details. Please try again.</div>');
            }
        });
    });
    // Tab coloring logic (like student profile)
    $('#clearanceTabs .nav-link').on('shown.bs.tab', function (e) {
        $('#clearanceTabs .nav-link').removeClass('bg-primary text-white');
        $(e.target).addClass('bg-primary text-white');
    });
});
</script>
@endpush
