@extends('inc.app')

@section('title', 'Late Fee Approval')

@section('content')
<div class="container mt-4">
    <h2>Late Fee Approval</h2>
    <hr>

    {{-- Student & Course Selection --}}
<div class="card mb-4">
    <div class="card-header bg-secondary text-white">Select Student & Course</div>
    <div class="card-body">
        <form method="GET" onsubmit="event.preventDefault(); goToApprovalPage();">
    <div class="row mb-3">
        <div class="col-md-5">
            <label for="student_nic">Student NIC</label>
            <input type="text" id="student-nic" name="student_nic" class="form-control" placeholder="Enter NIC" required>
        </div>

        <div class="col-md-5">
            <label for="course_id">Course</label>
            <select id="course_id" class="form-control" required>
                <option value="">-- Select Course --</option>
            </select>
        </div>

        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Load</button>
        </div>
    </div>
</form>

    </div>
</div>


    @isset($installments)

    {{-- Global Reduction Form --}}
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">Global Reduction - Feature Coming Soon</div>
        <div class="card-body">
            <form method="POST" action="{{ route('latefee.approve.global', [$student->id_value ?? $studentId, $courseId]) }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label>Reduction Amount</label>
                        <input type="number" step="0.01" min="0.01" name="reduction_amount" class="form-control" required>
                    </div>
                    <div class="col-md-8">
                        <label>Approval Note</label>
                        <input type="text" name="approval_note" class="form-control">
                    </div>
                </div>
                <button class="btn btn-success" >Apply Global Reduction</button>
            </form>
        </div>
    </div>

    {{-- Installment-wise Table --}}
<div class="card shadow-lg rounded-3 border-0">
    <div class="card-header bg-dark text-white fw-bold">
        <i class="bi bi-cash-coin me-2"></i> Installment-wise Approval
    </div>
    <div class="card-body p-3">
        <div class="table-responsive">
            <table class="table table-bordered align-middle text-center">
                <thead class="table-dark">
                    <tr>
                        <th>Installment #</th>
                        <th>Due Date</th>
                        <th>Final Amount</th>
                        <th>Calculated Late Fee</th>
                        <th>Approved Late Fee</th>
                        <th>Overdue (Calc - Approved)</th>
                        <th>Approval Note</th>
                        <th>History</th>
                        <th style="width: 220px;">Action</th>

                    </tr>
                </thead>
                <tbody>
                    @forelse($installments as $installment)
                        <tr>
                            <td class="fw-bold">{{ $installment->installment_number }}</td>
                            <td>{{ $installment->formatted_due_date }}</td>
                            <td class="text-primary fw-semibold">{{ $installment->formatted_amount }}</td>
                            <td class="text-warning fw-semibold">
                                LKR {{ number_format($installment->calculated_late_fee ?? 0, 2) }}
                            </td>
                            <td>
                                @if($installment->approved_late_fee !== null)
                                    <span class="badge bg-success p-2">
                                        LKR {{ number_format($installment->approved_late_fee, 2) }}
                                    </span>
                                @else
                                    <span class="badge bg-secondary">Not approved</span>
                                @endif
                            </td>
                            <td>
                                @php
                                    $calcFee = $installment->calculated_late_fee ?? 0;
                                    $approvedFee = $installment->approved_late_fee ?? 0;
                                    $overdue = $calcFee - $approvedFee;
                                @endphp

                                @if($overdue > 0)
                                    <span class="text-danger fw-bold">
                                        LKR {{ number_format($overdue, 2) }}
                                    </span>
                                @else
                                    <span class="text-success fw-bold">LKR 0.00</span>
                                @endif
                            </td>
                            <td>{{ $installment->approval_note ?? '-' }}</td>
                            <td>
                                <button class="btn btn-sm btn-outline-info" 
                                        data-bs-toggle="collapse" 
                                        data-bs-target="#history-{{ $installment->id }}">
                                    View History
                                </button>

                                <div id="history-{{ $installment->id }}" class="collapse mt-2 text-start">
                                    @php
                                        $histories = is_array($installment->approval_history)
                                            ? $installment->approval_history
                                            : json_decode($installment->approval_history ?? '[]', true);
                                    @endphp

                                    @if(empty($histories))
                                        <small class="text-muted fst-italic">No history yet</small>
                                    @else
                                        <ul class="list-group list-group-flush small">
                                            @foreach($histories as $h)
                                                <li class="list-group-item py-1">
                                                    <strong>LKR {{ number_format($h['approved_late_fee'], 2) }}</strong>
                                                    ({{ $h['approval_note'] ?? 'No note' }}) 
                                                    by <span class="fw-semibold">{{ $h['approved_by'] ?? 'System' }}</span>
                                                    <small class="text-muted d-block">on {{ $h['approved_at'] ?? '-' }}</small>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <form method="POST" action="{{ route('latefee.approve.installment', $installment->id) }}">
                                    @csrf
                                    <div class="row g-2">
                                        @php
                                            $isPast = \Carbon\Carbon::parse($installment->due_date)->isPast();
                                        @endphp

                                        <div class="col-md-6">
                                            <input type="number" step="0.01" min="0.01" name="approved_late_fee" 
                                                class="form-control form-control-sm"
                                                placeholder="Approved Fee"
                                                value="{{ $installment->approved_late_fee ?? '' }}"
                                                {{ $isPast ? '' : 'disabled' }}>
                                        </div>
                                        <div class="col-md-6">
                                            <input type="text" name="approval_note" 
                                                class="form-control form-control-sm"
                                                placeholder="Note"
                                                value="{{ $installment->approval_note ?? '' }}"
                                                {{ $isPast ? '' : 'disabled' }}>
                                        </div>

                                        <div class="col-12">
                                            @if($isPast)
                                                <button class="btn btn-sm btn-primary w-100">
                                                    Approve
                                                </button>
                                            @else
                                                <button class="btn btn-sm btn-secondary w-100" disabled
                                                        title="Approval only allowed after due date">
                                                    Approve
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">
                                No installments found for this student & course.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

    @endisset
</div>

<script>
document.getElementById("student-nic").addEventListener("blur", function() {
    let nic = this.value;
    if (!nic) return;

    fetch("{{ route('latefee.get.courses') }}", {
        method: "POST",
        headers: {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": "{{ csrf_token() }}"
        },
        body: JSON.stringify({ student_nic: nic })
    })
    .then(res => res.json())
    .then(data => {
        let courseSelect = document.getElementById("course_id");
        courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";

        if (data.success) {
            data.courses.forEach(c => {
                let option = document.createElement("option");
                option.value = c.course_id;
                option.textContent = c.course_name;
                courseSelect.appendChild(option);
            });
        } else {
            alert("No courses found for this NIC.");
        }
    })
    .catch(err => console.error(err));
});

function goToApprovalPage() {
    let nic = document.getElementById("student-nic").value;
    let courseId = document.getElementById("course_id").value;

    if (!nic || !courseId) {
        alert("Please enter NIC and select a course.");
        return;
    }

    let url = "{{ url('/late-fee/approval') }}/" + nic + "/" + courseId;
    window.location.href = url;
}


document.addEventListener("DOMContentLoaded", function () {
    const studentNicInput = document.getElementById("student-nic");
    const courseSelect = document.getElementById("course-select");

    studentNicInput.addEventListener("change", function () {
        let nic = studentNicInput.value.trim();
        if (!nic) return;

        // Clear old options
        courseSelect.innerHTML = '<option value="">-- Select Course --</option>';

        fetch("{{ route('latefee.get.courses') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
            },
            body: JSON.stringify({ student_nic: nic })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                data.courses.forEach(course => {
                    let opt = document.createElement("option");
                    opt.value = course.course_id;
                    opt.textContent = course.course_name + " (" + course.registration_date + ")";
                    courseSelect.appendChild(opt);
                });
            } else {
                alert(data.message || "No courses found for this NIC");
            }
        })
        .catch(err => console.error("Error fetching courses:", err));
    });
});

</script>
<script>
    if (performance.navigation.type === performance.navigation.TYPE_RELOAD) {
        window.location.href = "{{ url('/late-fee/approval') }}";
    }
</script>

@endsection
