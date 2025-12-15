<div class="table-card">
    <div class="card-header text-white">
        <h5 class="mb-0">
            @if($type == 'pending')
                <i class="ti ti-clock me-2"></i>Pending Clearance Requests
            @elseif($type == 'approved')
                <i class="ti ti-check me-2"></i>Approved Clearance Requests
            @else
                <i class="ti ti-x me-2"></i>Rejected Clearance Requests
            @endif
            <span class="badge bg-light text-dark ms-2">{{ $requests->total() }} total</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($requests->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Student ID</th>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Location</th>
                            <th>Requested Date</th>
                            @if($type == 'pending')
                                <th>Actions</th>
                            @else
                                <th>Status</th>
                                <th>Processed Date</th>
                                <th>Processed By</th>
                                <th>Remarks</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($requests as $request)
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                            {{ substr($request->student->student_id, -2) }}
                                        </div>
                                        <span>{{ $request->student->student_id }}</span>
                                    </div>
                                </td>
                                <td>
                                    <strong>{{ $request->student->name_with_initials }}</strong>
                                </td>
                                <td>{{ $request->course->course_name }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $request->intake->batch }}</span>
                                </td>
                                <td>{{ $request->location }}</td>
                                <td>{{ $request->requested_at->format('d/m/Y H:i') }}</td>
                                
                                @if($type == 'pending')
                                    <td>
                                        <div class="btn-group" role="group">
                                            <button class="btn btn-success btn-sm approve-btn" 
                                                    data-request-id="{{ $request->id }}"
                                                    data-student-name="{{ $request->student->name_with_initials }}">
                                                <i class="ti ti-check"></i> Approve
                                            </button>
                                            <button class="btn btn-danger btn-sm reject-btn" 
                                                    data-request-id="{{ $request->id }}"
                                                    data-student-name="{{ $request->student->name_with_initials }}">
                                                <i class="ti ti-x"></i> Reject
                                            </button>
                                        </div>
                                    </td>
                                @else
                                    <td>
                                        @if($type == 'approved')
                                            <span class="badge bg-success status-badge">
                                                <i class="ti ti-check me-1"></i> Approved
                                            </span>
                                        @else
                                            <span class="badge bg-danger status-badge">
                                                <i class="ti ti-x me-1"></i> Rejected
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->approved_at)
                                            {{ $request->approved_at->format('d/m/Y H:i') }}
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->approvedBy)
                                            {{ $request->approvedBy->name }}
                                        @else
                                            <span class="text-muted">System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($request->remarks)
                                            <span class="text-truncate" style="max-width: 200px;" 
                                                  title="{{ $request->remarks }}">
                                                {{ Str::limit($request->remarks, 50) }}
                                            </span>
                                        @else
                                            <span class="text-muted">No remarks</span>
                                        @endif
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            @if($requests->hasPages())
                <div class="pagination-container">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            Showing {{ $requests->firstItem() }} to {{ $requests->lastItem() }} of {{ $requests->total() }} entries
                        </div>
                        <div>
                            {{ $requests->appends(['tab' => $tab, 'search' => request('search')])->links() }}
                        </div>
                    </div>
                </div>
            @endif
        @else
            <div class="empty-state">
                @if($type == 'pending')
                    <i class="ti ti-check-circle text-success"></i>
                    <h5 class="mt-3">No Pending Requests</h5>
                    <p class="text-muted">All hostel clearance requests have been processed.</p>
                @elseif($type == 'approved')
                    <i class="ti ti-inbox text-info"></i>
                    <h5 class="mt-3">No Approved Requests</h5>
                    <p class="text-muted">No hostel clearance requests have been approved yet.</p>
                @else
                    <i class="ti ti-ban text-danger"></i>
                    <h5 class="mt-3">No Rejected Requests</h5>
                    <p class="text-muted">No hostel clearance requests have been rejected yet.</p>
                @endif
                @if(request('search'))
                    <a href="{{ route('hostel.clearance.form.management') }}" class="btn btn-primary mt-3">
                        <i class="ti ti-refresh me-1"></i> Clear Search
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>