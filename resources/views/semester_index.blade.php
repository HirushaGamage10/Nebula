@extends('inc.app')

@section('title', 'NEBULA | Semester Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="mb-0">Semester Management</h2>
                <a href="{{ route('semesters.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Create New Semester
                </a>
            </div>
            <hr>
            
            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <thead class="table-dark">
                        <tr>
                            <th>Semester Name</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th>Start Date</th>
                            <th>End Date</th>
                            <th>Status</th>
                            <th>Modules</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($semesters as $semester)
                        <tr>
                            <td>{{ $semester->name }}</td>
                            <td>{{ $semester->course->course_name ?? 'N/A' }}</td>
                            <td>{{ $semester->intake->batch ?? 'N/A' }}</td>
                            <td>{{ $semester->start_date ? $semester->start_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>{{ $semester->end_date ? $semester->end_date->format('Y-m-d') : 'N/A' }}</td>
                            <td>
                                @if($semester->status === 'upcoming')
                                    <span class="badge bg-warning">Upcoming</span>
                                @elseif($semester->status === 'active')
                                    <span class="badge bg-success">Active</span>
                                @else
                                    <span class="badge bg-secondary">Completed</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $moduleCount = $semester->modules->count();
                                @endphp
                                <span class="badge bg-info">{{ $moduleCount }} module{{ $moduleCount !== 1 ? 's' : '' }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('semesters.edit', $semester) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger delete-semester" data-semester-id="{{ $semester->id }}" data-semester-name="{{ $semester->name }}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">No semesters found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div aria-live="polite" aria-atomic="true" class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
    <div id="mainToast" class="toast align-items-center text-bg-primary border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body" id="mainToastBody">
                <!-- Message will go here -->
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete semester functionality
    document.querySelectorAll('.delete-semester').forEach(button => {
        button.addEventListener('click', function() {
            const semesterId = this.dataset.semesterId;
            const semesterName = this.dataset.semesterName;
            
            if (confirm(`Are you sure you want to delete the semester "${semesterName}"? This action cannot be undone.`)) {
                fetch(`/semesters/${semesterId}`, {
                    method: 'DELETE',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showToast(data.message, 'success');
                        // Remove the row from the table
                        this.closest('tr').remove();
                        
                        // Check if table is empty
                        const tbody = document.querySelector('tbody');
                        if (tbody.children.length === 0) {
                            tbody.innerHTML = '<tr><td colspan="8" class="text-center">No semesters found.</td></tr>';
                        }
                    } else {
                        showToast(data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showToast('An error occurred while deleting the semester.', 'danger');
                });
            }
        });
    });
});

// Toast function
function showToast(message, type = 'success') {
    const toastEl = document.getElementById('mainToast');
    const toastBody = document.getElementById('mainToastBody');
    toastBody.textContent = message;
    toastEl.className = 'toast align-items-center border-0 text-bg-' + (type === 'success' ? 'success' : (type === 'danger' ? 'danger' : (type === 'warning' ? 'warning' : 'primary')));
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
}
</script>
@endsection 