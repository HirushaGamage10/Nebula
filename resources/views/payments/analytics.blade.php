@extends('inc.app')

@section('title', 'Advanced Payment Analytics')

@section('content')
<div class="container-fluid mt-4 mb-5">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="text-primary mb-1">📊 Advanced Analytics</h2>
            <p class="text-muted mb-0">Deep dive into payment performance metrics</p>
        </div>
        <a href="{{ route('payment.summary') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    {{-- Summary KPI Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Total Revenue</p>
                            <h4 class="fw-bold mb-0">LKR {{ number_format($totalRevenue ?? 0, 2) }}</h4>
                        </div>
                        <div class="text-primary fs-3">
                            <i class="bi bi-currency-dollar"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Pending Payments</p>
                            <h4 class="fw-bold mb-0">LKR {{ number_format($totalPendingPayments ?? 0, 2) }}</h4>
                        </div>
                        <div class="text-warning fs-3">
                            <i class="bi bi-clock-history"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">Avg Paid Transaction</p>
                            <h4 class="fw-bold mb-0">LKR {{ number_format($averagePaidTransaction ?? 0, 2) }}</h4>
                        </div>
                        <div class="text-success fs-3">
                            <i class="bi bi-graph-up"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <p class="text-muted small mb-1">SLT Recoveries</p>
                            <h4 class="fw-bold mb-0">{{ $totalSltLoanRecoveries ?? 0 }} items</h4>
                            <small class="text-muted">LKR {{ number_format($totalSltRecoveryAmount ?? 0, 2) }}</small>
                        </div>
                        <div class="text-danger fs-3">
                            <i class="bi bi-bank"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3">
                    <h6 class="fw-bold mb-0">📈 Daily Revenue Trend</h6>
                    <small class="text-muted">Paid revenue by day within selected range.</small>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" height="100"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0">🆕 New Registrations</h6>
                        <small class="text-muted">Current month registrations by course</small>
                    </div>
                    <span class="badge bg-primary">{{ number_format($newRegistrationsCount ?? 0) }} regs</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Current month total</div>
                            <div class="display-6 fw-bold text-primary mb-1">LKR {{ number_format($newRegistrationsAmount ?? 0, 2) }}</div>
                            <div class="text-muted small">{{ number_format($newRegistrationsCount ?? 0) }} new registrations this month</div>
                        </div>
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                            <i class="bi bi-journal-plus fs-3"></i>
                        </div>
                    </div>

                    <label class="form-label small text-muted" for="newRegistrationCourseFilter">Filter by course</label>
                    <select class="form-select" id="newRegistrationCourseFilter" name="new_registration_course_id">
                        <option value="">All courses</option>
                        @foreach(($courses ?? []) as $course)
                            <option value="{{ $course->course_id }}" {{ (isset($selectedNewRegistrationCourseId) && $selectedNewRegistrationCourseId == $course->course_id) ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">Select one course to recalculate the current month registration fee total.</small>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100 bg-white">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0">📚 Ongoing Courses</h6>
                        <small class="text-muted">Current month collected amount from active registrations</small>
                    </div>
                    <span class="badge bg-success">{{ number_format($ongoingCoursesCount ?? 0) }} active</span>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <div class="text-muted small mb-1">Current month collected</div>
                            <div class="display-6 fw-bold text-success mb-1">LKR {{ number_format($ongoingCoursesAmount ?? 0, 2) }}</div>
                            <div class="text-muted small">{{ number_format($ongoingCoursesCount ?? 0) }} ongoing registrations</div>
                        </div>
                        <div class="bg-success bg-opacity-10 text-success rounded-3 p-3">
                            <i class="bi bi-cash-coin fs-3"></i>
                        </div>
                    </div>

                    <label class="form-label small text-muted" for="ongoingCourseFilter">Filter by course</label>
                    <select class="form-select" id="ongoingCourseFilter" name="ongoing_course_id">
                        <option value="">All courses</option>
                        @foreach(($courses ?? []) as $course)
                            <option value="{{ $course->course_id }}" {{ (isset($selectedOngoingCourseId) && $selectedOngoingCourseId == $course->course_id) ? 'selected' : '' }}>{{ $course->course_name }}</option>
                        @endforeach
                    </select>
                    <small class="text-muted d-block mt-2">Select one course to recalculate the current month collected payment total.</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h6 class="fw-bold mb-0">📊 Course-wise Payment Summary</h6>
                        <small class="text-muted">Registrations, new registrations, and payments by course</small>
                    </div>
                </div>
                <div class="card-body pt-3 px-3 pb-2">
                    <div class="table-responsive">
                        <table class="table table-striped align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Course</th>
                                    <th>Location</th>
                                    <th class="text-end">Total Reg.</th>
                                    <th class="text-end">New Reg.</th>
                                    <th class="text-end">Ongoing</th>
                                    <th class="text-end">Pending Reg.</th>
                                    <th class="text-end">Paid Amount</th>
                                    <th class="text-end">Pending Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse(($courseWiseSummary ?? []) as $courseSummary)
                                    <tr>
                                        <td>{{ $courseSummary['course_name'] ?? 'N/A' }}</td>
                                        <td>{{ $courseSummary['location'] ?? 'N/A' }}</td>
                                        <td class="text-end">{{ number_format($courseSummary['total_registrations'] ?? 0) }}</td>
                                        <td class="text-end">{{ number_format($courseSummary['new_registrations'] ?? 0) }}</td>
                                        <td class="text-end">{{ number_format($courseSummary['ongoing_courses'] ?? 0) }}</td>
                                        <td class="text-end">{{ number_format($courseSummary['pending_registrations'] ?? 0) }}</td>
                                        <td class="text-end">LKR {{ number_format($courseSummary['paid_amount'] ?? 0, 2) }}</td>
                                        <td class="text-end">LKR {{ number_format($courseSummary['pending_amount'] ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">No course-wise payment summary available</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white border-0 pt-0 pb-3">
                    <div class="border rounded-3 bg-light p-3">
                        <div class="fw-semibold mb-2">Column guide</div>
                        <div class="row g-3 small text-muted">
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">Total Reg.</div>
                                Shows all registrations counted for that course in the selected filters.
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">New Reg.</div>
                                Shows registrations created in the current month for that course.
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">Ongoing</div>
                                Shows registrations currently marked as active or ongoing.
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">Pending Reg.</div>
                                Shows registrations that still need payment completion.
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">Paid Amount</div>
                                Shows the total paid amount collected for the course.
                            </div>
                            <div class="col-md-4">
                                <div class="fw-semibold text-dark">Pending Amount</div>
                                Shows the outstanding amount still waiting to be collected.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Revenue Trend --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <div>
                <h6 class="fw-bold mb-0">🏦 Pending SLT Loan Recoveries This Month</h6>
                <small class="text-muted">Students with SLT loan receivables expected this month.</small>
            </div>
            <span class="badge bg-warning">{{ $pendingSltLoanRecoveries->count() }} students</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Student Name</th>
                            <th>Course</th>
                            <th>Intake</th>
                            <th class="text-end">Loan Amount</th>
                            <th class="text-end">Installment Amount</th>
                            <th>Effective Date</th>
                            <th class="text-center">Update Records</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($pendingSltLoanRecoveries as $record)
                            <tr>
                                <td>{{ $record['student_name'] }}</td>
                                <td>{{ $record['course_name'] }}</td>
                                <td>{{ $record['intake'] }}</td>
                                <td class="text-end">LKR {{ number_format($record['loan_amount'], 2) }}</td>
                                <td class="text-end">LKR {{ number_format($record['installment_amount'], 2) }}</td>
                                <td>{{ $record['effective_date'] }}</td>
                                <td class="text-center">
                                    @if($record['student_id_value'] && $record['course_id'])
                                        <a href="{{ route('payment.index') }}?student_nic={{ urlencode($record['student_id_value']) }}&course_id={{ $record['course_id'] }}" class="btn btn-sm btn-primary">
                                            Update Records
                                        </a>
                                    @else
                                        <span class="text-muted">Not available</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">No pending SLT recoveries found for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>

<script nonce="{{ $cspNonce }}" src="{{ asset('libs/chartjs/chart.min.js') }}"></script>
<script nonce="{{ $cspNonce }}">
document.addEventListener("DOMContentLoaded", () => {
    const revenueByDay = @json($revenueByDay);

    const newRegistrationCourseFilter = document.getElementById('newRegistrationCourseFilter');
    const ongoingCourseFilter = document.getElementById('ongoingCourseFilter');

    function applyAnalyticsCourseFilters() {
        const url = new URL(window.location.href);

        if (newRegistrationCourseFilter) {
            if (newRegistrationCourseFilter.value) {
                url.searchParams.set('new_registration_course_id', newRegistrationCourseFilter.value);
            } else {
                url.searchParams.delete('new_registration_course_id');
            }
        }

        if (ongoingCourseFilter) {
            if (ongoingCourseFilter.value) {
                url.searchParams.set('ongoing_course_id', ongoingCourseFilter.value);
            } else {
                url.searchParams.delete('ongoing_course_id');
            }
        }

        if (newRegistrationCourseFilter || ongoingCourseFilter) {
            window.location.href = url.toString();
        }
    }

    if (newRegistrationCourseFilter) {
        newRegistrationCourseFilter.addEventListener('change', applyAnalyticsCourseFilters);
    }

    if (ongoingCourseFilter) {
        ongoingCourseFilter.addEventListener('change', applyAnalyticsCourseFilters);
    }

    // Revenue Trend Chart
    const revenueChartElement = document.getElementById('revenueChart');
    if (revenueChartElement) {
        new Chart(revenueChartElement, {
        type: 'bar',
        data: {
            labels: revenueByDay.map(r => r.date),
            datasets: [{
                label: 'Daily Revenue',
                data: revenueByDay.map(r => r.revenue),
                backgroundColor: 'rgba(102, 126, 234, 0.8)',
                borderColor: '#667eea',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            return 'Revenue: LKR ' + new Intl.NumberFormat().format(context.parsed.y);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: { color: 'rgba(0,0,0,0.05)' },
                    ticks: {
                        callback: function(value) {
                            return 'LKR ' + new Intl.NumberFormat().format(value);
                        }
                    }
                },
                x: {
                    grid: { display: false }
                }
            }
        }
    });
});
</script>

<style nonce="{{ $cspNonce }}">
.card {
    transition: all 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 0.75rem 1.5rem rgba(0, 0, 0, 0.15) !important;
}

.progress {
    background-color: rgba(0, 0, 0, 0.05);
}

.table-hover tbody tr:hover {
    background-color: rgba(102, 126, 234, 0.05);
}
</style>

@endsection