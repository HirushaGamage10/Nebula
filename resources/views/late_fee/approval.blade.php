@extends('inc.app')

@section('title', 'Late Fee Approval')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Late Fee Approval</h2>
            <hr>
            
{{-- Student & Course Selection --}}
<div class="mb-4">
    <h5 class="mb-3">Select Student & Course</h5>

    <form method="GET" onsubmit="event.preventDefault(); goToApprovalPage();">
        <div class="row mb-3">

            {{-- ✅ Student NIC --}}
            <div class="col-md-5">
                <label for="student-nic" class="form-label fw-semibold">Student NIC</label>
                <input type="text" 
                       id="student-nic" 
                       name="student_nic" 
                       class="form-control" 
                       placeholder="Enter NIC" 
                       value="{{ $studentNic ?? '' }}" 
                       required>
            </div>

            {{-- ✅ Course Dropdown --}}
            <div class="col-md-5">
                <label for="course_id" class="form-label fw-semibold">Course</label>
                <select id="course_id" name="course_id" class="form-control" required>
                    <option value="">-- Select Course --</option>
                    @foreach($courses ?? [] as $c)
                        <option value="{{ $c['course_id'] }}" 
                            {{ ($courseId ?? '') == $c['course_id'] ? 'selected' : '' }}>
                            {{ $c['course_name'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- ✅ Action Buttons --}}
            <div class="col-md-2 d-flex align-items-end">
                <div class="d-flex w-100 gap-2">
                    <button type="submit" class="btn btn-primary flex-fill">Load</button>
                    <button type="button" 
                            class="btn btn-outline-secondary flex-fill"
                            onclick="window.location.href='{{ url('/late-fee/approval') }}'">
                        Clear
                    </button>
                </div>
            </div>

        </div>
    </form>
</div>


    @isset($installments)

    {{-- Global Reduction Form --}}
    <div class="mb-4">
        <h5 class="mb-3">Global Reduction - Feature Coming Soon</h5>
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
            <button class="btn btn-success">Apply Global Reduction</button>
        </form>
    </div>

    {{-- Installment-wise Table --}}
    <div class="mb-4">
        <h5 class="mb-3">Installment-wise Approval</h5>
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

    @endisset
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const studentNicInput = document.getElementById("student-nic");
    const courseSelect = document.getElementById("course_id");

    // 1️⃣ Fetch and populate courses when NIC is entered or changed
    studentNicInput.addEventListener("blur", fetchCoursesForNIC);
    studentNicInput.addEventListener("change", fetchCoursesForNIC);

    function fetchCoursesForNIC() {
        const nic = studentNicInput.value.trim();
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
            courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";

            if (data.success && Array.isArray(data.courses)) {
                data.courses.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.course_id;
                    opt.textContent = c.course_name;
                    courseSelect.appendChild(opt);
                });
            } else {
                alert("No courses found for this NIC.");
            }
        })
        .catch(err => console.error("Error fetching courses:", err));
    }

    // 2️⃣ Auto-fill when returning from backend (values from Blade)
    const prefilledNic = "{{ $studentNic ?? '' }}";
    const prefilledCourseId = "{{ $courseId ?? '' }}";

    if (prefilledNic) {
        studentNicInput.value = prefilledNic;

        // If NIC is filled, fetch courses and auto-select correct one
        fetch("{{ route('latefee.get.courses') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ student_nic: prefilledNic })
        })
        .then(res => res.json())
        .then(data => {
            courseSelect.innerHTML = "<option value=''>-- Select Course --</option>";
            if (data.success && Array.isArray(data.courses)) {
                data.courses.forEach(c => {
                    const opt = document.createElement("option");
                    opt.value = c.course_id;
                    opt.textContent = c.course_name;
                    if (c.course_id == prefilledCourseId) {
                        opt.selected = true;
                    }
                    courseSelect.appendChild(opt);
                });
            }
        })
        .catch(err => console.error("Error preloading courses:", err));
    }
});

// 3️⃣ Redirect to approval page
function goToApprovalPage() {
    const nic = document.getElementById("student-nic").value.trim();
    const courseId = document.getElementById("course_id").value;

    if (!nic || !courseId) {
        alert("Please enter NIC and select a course.");
        return;
    }

    const url = "{{ url('/late-fee/approval') }}/" + nic + "/" + courseId;
    window.location.href = url;
}
</script>

@endsection
