@extends('inc.app')

@section('title', 'NEBULA | Bursar Dashboard')

@section('content')

<style>
    .stat-card {
        transition: 0.2s;
        border-left: 4px solid #0d6efd;
        background: white;
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 6px 18px rgba(0,0,0,0.1);
    }

    .badge-status {
        padding: 5px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: bold;
        text-transform: capitalize;
    }
    .badge-pending { background: #0d6efd; color: white; }
    .badge-approved { background: #198754; color: white; }
    .badge-rejected { background: #dc3545; color: white; }
</style>

<div class="container-fluid">
    <div class="page-wrapper">

        <!-- Header -->
        <div class="card shadow-sm p-3 mb-4 bg-white">
            <h3 class="fw-bold">Bursar Dashboard</h3>
            <small class="text-muted">
                Monitoring financial clearance for non-tuition and bursary requirements
            </small>
        </div>

        <!-- KPI SUMMARY -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card stat-card p-4 shadow-sm">
                    <h6 class="text-muted">Pending Bursar Approvals</h6>
                    <h2 class="text-primary fw-bold">{{ $pendingCount }}</h2>
                </div>
            </div>
        </div>

        <!-- STUDENT FINANCIAL REVIEW LIST -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-semibold m-0">Financial Clearance Pending</h5>
                <span class="badge bg-primary">{{ count($pendingList) }} pending</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>Student ID</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th>Review</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingList as $req)
                        <tr>
                            <td>{{ $req->student->full_name }}</td>
                            <td><code>{{ $req->student->student_id }}</code></td>
                            <td>{{ $req->course->course_name }}</td>
                            <td>{{ $req->intake->intake_name }}</td>
                            <td>{{ $req->requested_at?->format('Y-m-d') }}</td>
                            <td>
                                <span class="badge-status badge-pending">{{ $req->status }}</span>
                            </td>
                            <td>
                                <a href="{{ route('payment.clearance', $req->id) }}"
                                   class="btn btn-sm btn-primary">
                                   Review
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-muted py-3">No pending financial clearances</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- RECENT FINANCIAL CLEARANCE UPDATES -->
        <div class="card shadow-sm p-4 mb-4 bg-white">
            <h5 class="fw-semibold mb-3">Recent Financial Clearance Updates</h5>

            <div class="table-responsive">
                <table class="table table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>Student</th>
                            <th>ID</th>
                            <th>Course</th>
                            <th>Status</th>
                            <th>Updated</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recent as $req)
                        <tr>
                            <td>{{ $req->student->full_name }}</td>
                            <td><code>{{ $req->student->student_id }}</code></td>
                            <td>{{ $req->course->course_name }}</td>
                            <td>
                                <span class="badge-status badge-{{ $req->status }}">
                                    {{ $req->status }}
                                </span>
                            </td>
                            <td>{{ $req->updated_at?->format('Y-m-d H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
@endsection
