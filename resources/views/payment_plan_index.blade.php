@extends('inc.app')

@section('title', 'NEBULA | Payment Plans')

@section('content')
<div class="container-fluid">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card mb-3">
        <div class="card-body">
            <h2 class="mb-3">Payment Plans</h2>
            <form method="GET" action="{{ route('payment.plan.index') }}" class="row gy-2 gx-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select">
                        <option value="">All</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc }}" @selected(request('location')===$loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Course</label>
                    <select name="course_id" id="filter-course" class="form-select">
                        <option value="">All</option>
                        @foreach($courses as $c)
                            <option value="{{ $c->course_id }}" @selected((string)request('course_id')===(string)$c->course_id)>
                                {{ $c->course_name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Intake</label>
                    <select name="intake_id" id="filter-intake" class="form-select" @disabled(!request('course_id'))>
                        <option value="">All</option>
                        @foreach($intakes as $i)
                            <option value="{{ $i->intake_id }}" @selected((string)request('intake_id')===(string)$i->intake_id)>
                                {{ $i->intake_id }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-primary">Filter</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body table-responsive">
            <table class="table table-bordered align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Location</th>
                        <th>Course</th>
                        <th>Intake</th>
                        <th class="text-end">Reg. Fee (LKR)</th>
                        <th class="text-end">Local Fee (LKR)</th>
                        <th class="text-end">Franchise</th>
                        <th>Discount</th>
                        <th>Installments</th>
                        <th>Created</th>
                        <th style="width: 130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                @forelse($plans as $plan)
                    @php
                        // Ensure installments is an array even if stored as string
                        $items = is_array($plan->installments) ? $plan->installments : (json_decode($plan->installments, true) ?? []);
                        $count = count($items);

                        $firstDue = $count ? ($items[0]['due_date'] ?? null) : null;
                        $lastDue  = $count ? ($items[array_key_last($items)]['due_date'] ?? null) : null;

                        $totalLocal = 0; $totalIntl = 0;
                        foreach ($items as $it) {
                            $totalLocal += (float)($it['local_amount'] ?? 0);
                            $totalIntl  += (float)($it['international_amount'] ?? 0);
                        }
                    @endphp
                    <tr>
                        <td>{{ $plan->id }}</td>
                        <td>{{ $plan->location }}</td>
                        <td>{{ optional($plan->course)->course_name ?? '—' }}</td>
                        <td>{{ optional($plan->intake)->intake_id ?? '—' }}</td>
                        <td class="text-end">{{ number_format($plan->registration_fee, 2, '.', ',') }}</td>
                        <td class="text-end">{{ number_format($plan->local_fee, 2, '.', ',') }}</td>
                        <td class="text-end">
                            {{ number_format($plan->international_fee, 2, '.', ',') }}
                            <small class="text-muted">{{ $plan->international_currency }}</small>
                        </td>
                        <td>
                            @if($plan->apply_discount)
                                {{ rtrim(rtrim(number_format($plan->discount ?? 0, 2, '.', ''), '0'), '.') }}%
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            @if($plan->installment_plan)
                                <button class="btn btn-sm btn-outline-secondary"
                                        type="button"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#inst-{{ $plan->id }}">
                                    {{ $count }} items
                                </button>
                                <div class="small text-muted mt-1">
                                    @if($count)
                                        {{ $firstDue }} → {{ $lastDue }}
                                    @endif
                                </div>
                            @else
                                —
                            @endif
                        </td>
                        <td>
                            <div class="small text-muted">{{ $plan->created_at?->format('Y-m-d H:i') }}</div>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                {{-- Replace with your real routes if you have show/edit/delete --}}
                                {{-- <a class="btn btn-sm btn-primary" href="{{ route('payment.plan.index', array_merge(request()->query(), ['view' => $plan->id])) }}">View</a> --}}
                                <a href="{{ route('payment.plan.edit',$plan->id) }}" class="btn btn-sm btn-warning">Edit</a>

                                {{-- <a class="btn btn-sm btn-warning" href="{{ route('payment.plan.edit', $plan) }}">Edit</a> --}}
                                {{-- <form method="POST" action="{{ route('payment.plan.destroy', $plan) }}" onsubmit="return confirm('Delete this plan?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Delete</button>
                                </form> --}}
                            </div>
                        </td>
                    </tr>
                    @if($plan->installment_plan)
                        <tr class="collapse" id="inst-{{ $plan->id }}">
                            <td colspan="11">
                                <div class="table-responsive">
                                    <table class="table table-sm table-striped mb-2">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>#</th>
                                                <th>Due Date</th>
                                                <th class="text-end">Local (LKR)</th>
                                                <th class="text-end">International ({{ $plan->international_currency }})</th>
                                                <th>Tax?</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($items as $it)
                                                <tr>
                                                    <td>{{ $it['installment_number'] ?? '' }}</td>
                                                    <td>{{ $it['due_date'] ?? '' }}</td>
                                                    <td class="text-end">{{ number_format((float)($it['local_amount'] ?? 0), 2, '.', ',') }}</td>
                                                    <td class="text-end">{{ number_format((float)($it['international_amount'] ?? 0), 2, '.', ',') }}</td>
                                                    <td>
                                                        @if(!empty($it['apply_tax']))
                                                            <span class="badge bg-success">Yes</span>
                                                        @else
                                                            <span class="badge bg-secondary">No</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr><td colspan="5" class="text-center text-muted">No installments found</td></tr>
                                            @endforelse
                                        </tbody>
                                        <tfoot>
                                            <tr class="fw-semibold">
                                                <td colspan="2" class="text-end">Totals:</td>
                                                <td class="text-end">{{ number_format($totalLocal, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ number_format($totalIntl, 2, '.', ',') }}</td>
                                                <td></td>
                                            </tr>
                                            <tr class="small text-muted">
                                                <td colspan="2" class="text-end">Required:</td>
                                                <td class="text-end">{{ number_format((float)$plan->local_fee, 2, '.', ',') }}</td>
                                                <td class="text-end">{{ number_format((float)$plan->international_fee, 2, '.', ',') }}</td>
                                                <td></td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="11" class="text-center text-muted">No payment plans found.</td></tr>
                @endforelse
                </tbody>
            </table>

            <div class="d-flex justify-content-between align-items-center">
                <div class="small text-muted">
                    Showing {{ $plans->firstItem() ?? 0 }}–{{ $plans->lastItem() ?? 0 }} of {{ $plans->total() }}
                </div>
                {{ $plans->links() }}
            </div>
        </div>
    </div>
</div>

{{-- Optional: auto-load intakes when course filter changes (same UX as create page) --}}
<script>
document.getElementById('filter-course')?.addEventListener('change', function () {
    const courseId = this.value;
    const intakeSelect = document.getElementById('filter-intake');
    intakeSelect.disabled = true;
    intakeSelect.innerHTML = '<option value="">Loading...</option>';

    if (!courseId) {
        intakeSelect.innerHTML = '<option value="">All</option>';
        intakeSelect.disabled = false;
        return;
    }

    fetch("{{ route('intakes.byCourse') }}", {
        method: 'POST',
        headers: {
            'Content-Type':'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ course_id: courseId })
    })
    .then(res => res.json())
    .then(data => {
        intakeSelect.innerHTML = '<option value="">All</option>';
        if (data.success && data.data.length) {
            data.data.forEach(intake => {
                const opt = document.createElement('option');
                opt.value = intake.intake_id;
                opt.textContent = intake.intake_id; // keep raw ID
                intakeSelect.appendChild(opt);
            });
        }
        intakeSelect.disabled = false;
    })
    .catch(() => {
        intakeSelect.innerHTML = '<option value="">All</option>';
        intakeSelect.disabled = false;
    });
});

</script>
@endsection
