@extends('inc.app')

@section('title', 'NEBULA | Termination Tracking')

@section('content')
<style nonce="{{ $cspNonce }}">
.summary-card {
    border: 1px solid #e5e7eb;
    border-radius: 14px;
    padding: 18px;
    background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.06);
}

.summary-card .label {
    color: #64748b;
    font-size: 0.85rem;
    margin-bottom: 8px;
}

.summary-card .value {
    font-size: 1.8rem;
    font-weight: 700;
    color: #0f172a;
}

.termination-table td,
.termination-table th {
    vertical-align: middle;
}

.detail-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 12px;
}

.detail-card {
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 14px;
    background: #fff;
}

.detail-card h6 {
    margin-bottom: 10px;
    font-weight: 700;
}

.detail-meta {
    color: #64748b;
    font-size: 0.85rem;
}

.empty-state {
    border: 1px dashed #cbd5e1;
    border-radius: 14px;
    padding: 36px;
    text-align: center;
    color: #64748b;
    background: #f8fafc;
}
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
                <div>
                    <h2 class="mb-1">Termination Tracking</h2>
                    <p class="text-muted mb-0">Track clearance progress and any DGM-related approval status for terminated students.</p>
                </div>
                <a href="{{ route('termination.tracking') }}" class="btn btn-outline-primary">
                    <i class="ti ti-refresh me-1"></i>Refresh
                </a>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="label">Currently Terminated Students</div>
                        <div class="value">{{ $summary['total'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="label">Clearance In Progress</div>
                        <div class="value">{{ $summary['clearance_in_progress'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="label">Awaiting DGM</div>
                        <div class="value">{{ $summary['awaiting_dgm'] }}</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="summary-card">
                        <div class="label">Completed</div>
                        <div class="value">{{ $summary['completed'] }}</div>
                    </div>
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-12">
                    <input type="text" id="terminationSearch" class="form-control" placeholder="Search by student ID, NIC, name, location, course, intake or reason">
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-3">
                    <select id="locationFilter" class="form-select">
                        <option value="">All Locations</option>
                        @foreach($filters['locations'] as $location)
                            <option value="{{ $location }}">{{ $location }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="courseFilter" class="form-select">
                        <option value="">All Courses</option>
                        @foreach($filters['courses'] as $course)
                            <option value="{{ $course }}">{{ $course }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="intakeFilter" class="form-select">
                        <option value="">All Intakes</option>
                        @foreach($filters['intakes'] as $intake)
                            <option value="{{ $intake }}">{{ $intake }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select id="overallStatusFilter" class="form-select">
                        <option value="">All Process Statuses</option>
                        <option value="not_started">No clearances requested</option>
                        <option value="awaiting_clearances">Awaiting clearances</option>
                        <option value="clearance_rejected">Clearance rejected</option>
                        <option value="awaiting_dgm">Awaiting DGM approval</option>
                        <option value="dgm_rejected">DGM rejected</option>
                        <option value="dgm_approved">DGM approved</option>
                        <option value="completed">Clearances completed</option>
                    </select>
                </div>
            </div>

            @if($processes->isEmpty())
                <div class="empty-state">
                    <h5 class="mb-2">No terminated students to track</h5>
                    <p class="mb-0">Once a student is terminated, their process status will appear here.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover termination-table">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Course / Intake</th>
                                <th>Terminated On</th>
                                <th>Clearances</th>
                                <th>DGM Status</th>
                                <th>Overall</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody id="terminationTableBody">
                            @foreach($processes as $process)
                                @php
                                    $summaryText = $process['clearance_summary']['requested'] === 0
                                        ? 'No requests'
                                        : $process['clearance_summary']['approved'] . ' approved / ' .
                                            $process['clearance_summary']['pending'] . ' pending / ' .
                                            $process['clearance_summary']['rejected'] . ' rejected / ' .
                                            $process['clearance_summary']['not_requested'] . ' not requested';
                                @endphp
                                <tr
                                    data-student-id="{{ $process['student_id'] }}"
                                    data-overall-status="{{ $process['overall_status']['key'] }}"
                                    data-location="{{ strtolower((string) ($process['location'] ?? '')) }}"
                                    data-course="{{ strtolower((string) ($process['course_name'] ?? '')) }}"
                                    data-intake="{{ strtolower((string) ($process['intake_name'] ?? '')) }}"
                                    data-search="{{ strtolower(implode(' ', array_filter([
                                        $process['student_id'],
                                        $process['student_nic'],
                                        $process['student_name'],
                                        $process['location'],
                                        $process['course_name'],
                                        $process['intake_name'],
                                        $process['termination_reason'],
                                    ]))) }}"
                                >
                                    <td>
                                        <div class="fw-semibold">{{ $process['student_name'] }}</div>
                                        <div class="text-muted small">ID: {{ $process['student_id'] }}</div>
                                        <div class="text-muted small">NIC: {{ $process['student_nic'] ?? 'N/A' }}</div>
                                        <div class="text-muted small">Location: {{ $process['location'] ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $process['course_name'] ?? 'N/A' }}</div>
                                        <div class="text-muted small">{{ $process['intake_name'] ?? 'No intake' }}</div>
                                    </td>
                                    <td>
                                        <div>{{ $process['terminated_at'] ?? 'N/A' }}</div>
                                        <div class="text-muted small">By: {{ $process['terminated_by'] ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <div class="small">{{ $summaryText }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $process['dgm_status']['badge_class'] }}">{{ $process['dgm_status']['status_label'] }}</span>
                                    </td>
                                    <td>
                                        <span class="badge {{ $process['overall_status']['badge_class'] }}">{{ $process['overall_status']['label'] }}</span>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary view-process-btn" data-student-id="{{ $process['student_id'] }}">
                                            View Process
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>

<div class="modal fade" id="terminationProcessModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Termination Process Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="detail-grid mb-3">
                    <div class="detail-card">
                        <h6>Student</h6>
                        <div id="processStudentBlock"></div>
                    </div>
                    <div class="detail-card">
                        <h6>Termination</h6>
                        <div id="processTerminationBlock"></div>
                    </div>
                    <div class="detail-card">
                        <h6>Current Context</h6>
                        <div id="processContextBlock"></div>
                    </div>
                    <div class="detail-card">
                        <h6>Overall Progress</h6>
                        <div id="processOverallBlock"></div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header fw-semibold">Clearance Status</div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Clearance Type</th>
                                        <th>Status</th>
                                        <th>Requested At</th>
                                        <th>Approved / Rejected At</th>
                                        <th>Approved By</th>
                                        <th>Remarks</th>
                                        <th>Document</th>
                                    </tr>
                                </thead>
                                <tbody id="processClearanceBody"></tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header fw-semibold">DGM / Re-Registration Approval</div>
                    <div class="card-body" id="processDgmBlock"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function () {
    const processes = @json($processes->keyBy('student_id')->all());
    const tableBody = document.getElementById('terminationTableBody');
    const searchInput = document.getElementById('terminationSearch');
    const locationFilter = document.getElementById('locationFilter');
    const courseFilter = document.getElementById('courseFilter');
    const intakeFilter = document.getElementById('intakeFilter');
    const statusFilter = document.getElementById('overallStatusFilter');
    const modalElement = document.getElementById('terminationProcessModal');
    const modal = modalElement ? new bootstrap.Modal(modalElement) : null;

    function escapeHtml(text) {
        const map = {'&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;'};
        return String(text ?? '').replace(/[&<>"']/g, function (match) {
            return map[match];
        });
    }

    function renderBadge(label, badgeClass) {
        return '<span class="badge ' + badgeClass + '">' + escapeHtml(label) + '</span>';
    }

    function normalizeValue(value) {
        return String(value ?? '').trim().toLowerCase();
    }

    function applyFilters() {
        if (!tableBody) {
            return;
        }

        const query = normalizeValue(searchInput?.value);
        const location = normalizeValue(locationFilter?.value);
        const course = normalizeValue(courseFilter?.value);
        const intake = normalizeValue(intakeFilter?.value);
        const status = normalizeValue(statusFilter?.value);

        Array.from(tableBody.querySelectorAll('tr[data-student-id]')).forEach(function (row) {
            const matchesQuery = !query || row.dataset.search.includes(query);
            const matchesStatus = !status || row.dataset.overallStatus === status;
            const matchesLocation = !location || row.dataset.location === location;
            const matchesCourse = !course || row.dataset.course === course;
            const matchesIntake = !intake || row.dataset.intake === intake;

            row.style.display = matchesQuery && matchesLocation && matchesCourse && matchesIntake && matchesStatus ? '' : 'none';
        });
    }

    function setHtml(id, html) {
        const element = document.getElementById(id);
        if (element) {
            element.innerHTML = html;
        }
    }

    function renderProcess(studentId) {
        const process = processes[studentId];
        if (!process) {
            return;
        }

        setHtml('processStudentBlock', [
            '<div class="fw-semibold">' + escapeHtml(process.student_name) + '</div>',
            '<div class="detail-meta">Student ID: ' + escapeHtml(process.student_id) + '</div>',
            '<div class="detail-meta">NIC: ' + escapeHtml(process.student_nic || 'N/A') + '</div>',
            '<div class="detail-meta">Location: ' + escapeHtml(process.location || 'N/A') + '</div>',
            '<div class="mt-2"><a class="btn btn-sm btn-outline-secondary" href="' + escapeHtml(process.profile_url) + '">Open Profile</a></div>'
        ].join(''));

        setHtml('processTerminationBlock', [
            '<div>' + renderBadge(process.academic_status, 'bg-danger') + '</div>',
            '<div class="detail-meta mt-2">Terminated At: ' + escapeHtml(process.terminated_at || 'N/A') + '</div>',
            '<div class="detail-meta">Terminated By: ' + escapeHtml(process.terminated_by || 'N/A') + '</div>',
            '<div class="mt-2"><strong>Reason:</strong><br>' + escapeHtml(process.termination_reason || 'No reason recorded') + '</div>',
            (process.termination_document_url
                ? '<div class="mt-2"><a class="btn btn-sm btn-outline-primary" target="_blank" href="' + escapeHtml(process.termination_document_url) + '">View Document</a></div>'
                : '')
        ].join(''));

        setHtml('processContextBlock', [
            '<div><strong>Course:</strong> ' + escapeHtml(process.course_name || 'N/A') + '</div>',
            '<div class="detail-meta">Intake: ' + escapeHtml(process.intake_name || 'N/A') + '</div>'
        ].join(''));

        setHtml('processOverallBlock', [
            '<div>' + renderBadge(process.overall_status.label, process.overall_status.badge_class) + '</div>',
            '<div class="mt-2 detail-meta">Clearances: ' + process.clearance_summary.approved + ' approved / ' + process.clearance_summary.pending + ' pending / ' + process.clearance_summary.rejected + ' rejected / ' + process.clearance_summary.not_requested + ' not requested</div>',
            '<div class="mt-2">DGM Status: ' + renderBadge(process.dgm_status.status_label, process.dgm_status.badge_class) + '</div>'
        ].join(''));

        const clearanceRows = process.clearances.map(function (clearance) {
            return [
                '<tr>',
                '<td>' + escapeHtml(clearance.label) + '</td>',
                '<td>' + renderBadge(clearance.status_label, clearance.badge_class) + '</td>',
                '<td>' + escapeHtml(clearance.requested_at || 'N/A') + '</td>',
                '<td>' + escapeHtml(clearance.approved_at || 'N/A') + '</td>',
                '<td>' + escapeHtml(clearance.approved_by || 'N/A') + '</td>',
                '<td>' + escapeHtml(clearance.remarks || 'N/A') + '</td>',
                '<td>' + (clearance.clearance_slip_url
                    ? '<a target="_blank" href="' + escapeHtml(clearance.clearance_slip_url) + '">View</a>'
                    : '<span class="text-muted">N/A</span>') + '</td>',
                '</tr>'
            ].join('');
        }).join('');

        setHtml('processClearanceBody', clearanceRows);

        const dgm = process.dgm_status;
        setHtml('processDgmBlock', [
            '<div class="mb-2">' + renderBadge(dgm.status_label, dgm.badge_class) + '</div>',
            '<div><strong>Course:</strong> ' + escapeHtml(dgm.course_name || 'N/A') + '</div>',
            '<div><strong>Intake:</strong> ' + escapeHtml(dgm.intake_name || 'N/A') + '</div>',
            '<div><strong>Semester:</strong> ' + escapeHtml(dgm.semester_name || 'N/A') + '</div>',
            '<div><strong>Requested At:</strong> ' + escapeHtml(dgm.requested_at || 'N/A') + '</div>',
            '<div><strong>Decided At:</strong> ' + escapeHtml(dgm.decided_at || 'N/A') + '</div>',
            '<div class="mt-2"><strong>Reason:</strong><br>' + escapeHtml(dgm.reason || 'N/A') + '</div>',
            '<div class="mt-2"><strong>DGM Comment:</strong><br>' + escapeHtml(dgm.comment || 'N/A') + '</div>',
            (dgm.document_url
                ? '<div class="mt-3"><a class="btn btn-sm btn-outline-primary" target="_blank" href="' + escapeHtml(dgm.document_url) + '">View Attachment</a></div>'
                : '')
        ].join(''));

        if (modal) {
            modal.show();
        }
    }

    searchInput?.addEventListener('input', applyFilters);
    locationFilter?.addEventListener('change', applyFilters);
    courseFilter?.addEventListener('change', applyFilters);
    intakeFilter?.addEventListener('change', applyFilters);
    statusFilter?.addEventListener('change', applyFilters);

    tableBody?.addEventListener('click', function (event) {
        const button = event.target.closest('.view-process-btn');
        if (!button) {
            return;
        }

        renderProcess(button.getAttribute('data-student-id'));
    });
});
</script>
@endsection