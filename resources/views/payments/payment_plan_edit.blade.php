@extends('inc.app')
@section('title','Edit Payment Plan')
@section('content')

<div class="container-fluid">
    @php
        $planId = is_scalar($plan->id ?? null) ? (string) $plan->id : '0';
        $planCourseId = is_scalar($plan->course_id ?? null) ? (string) $plan->course_id : '';
        $planIntakeId = is_scalar($plan->intake_id ?? null) ? (string) $plan->intake_id : '';
        $planLocation = is_scalar($plan->location ?? null) ? (string) $plan->location : '';
        $planRegistrationFee = is_scalar($plan->registration_fee ?? null) ? (string) $plan->registration_fee : '';
        $planLocalFee = is_scalar($plan->local_fee ?? null) ? (string) $plan->local_fee : '';
        $planInternationalFee = is_scalar($plan->international_fee ?? null) ? (string) $plan->international_fee : '';
        $planInternationalCurrency = is_scalar($plan->international_currency ?? null) ? (string) $plan->international_currency : '';
        $planSsclTax = is_scalar($plan->sscl_tax ?? null) ? (string) $plan->sscl_tax : '';
        $planBankCharges = is_scalar($plan->bank_charges ?? null) ? (string) $plan->bank_charges : '';
        $planDiscount = is_scalar($plan->discount ?? null) ? (string) $plan->discount : '';
        $safeCourses = collect($courses ?? [])->map(function ($course) {
            return [
                'id' => is_scalar($course->course_id ?? null) ? (string) $course->course_id : '',
                'name' => is_scalar($course->course_name ?? null) ? (string) $course->course_name : '',
            ];
        });
        $safeIntakes = collect($intakes ?? [])->map(function ($intake) {
            return [
                'id' => is_scalar($intake->intake_id ?? null) ? (string) $intake->intake_id : '',
                'batch' => is_scalar($intake->batch ?? null) ? (string) $intake->batch : '',
            ];
        });
    @endphp
    @php
        $renderValue = function ($value) {
            if (is_scalar($value) || $value === null) {
                return (string) $value;
            }

            if ($value instanceof \Stringable) {
                return (string) $value;
            }

            return json_encode($value, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        };
    @endphp
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Edit Payment Plan</h2>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $renderValue($error) }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payment.plan.update', $planId) }}">
                @csrf
                @method('PUT')

                {{-- Location --}}
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select" required>
                        @foreach(['Welisara','Moratuwa','Peradeniya'] as $loc)
                            <option value="{{ $loc }}" @selected($planLocation == $loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Course --}}
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($safeCourses as $courseOption)
                            <option value="{{ $courseOption['id'] }}" @selected($planCourseId == $courseOption['id'])>{{ $courseOption['name'] }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Intake --}}
<div class="mb-3">
    <label class="form-label">Intake</label>
    <select name="intake_id" class="form-select">
        <option value="">None</option>
        @foreach($safeIntakes as $intakeOption)
            <option value="{{ $intakeOption['id'] }}" @selected($planIntakeId == $intakeOption['id'])>
                {{ $intakeOption['batch'] }}
            </option>
        @endforeach
    </select>
</div>


                {{-- Registration Fee --}}
                <div class="mb-3">
                    <label class="form-label">Registration Fee</label>
                    <input type="number" name="registration_fee" class="form-control" value="{{ $planRegistrationFee }}" required min="0" step="0.01">
                </div>

                {{-- Local Fee --}}
                <div class="mb-3">
                    <label class="form-label">Local Fee</label>
                    <input type="number" id="localFee" name="local_fee" class="form-control" value="{{ $planLocalFee }}" required min="0" step="0.01">
                </div>

                {{-- Franchise Fee --}}
                <div class="mb-3">
                    <label class="form-label">Franchise Fee</label>
                    <input type="number" id="internationalFee" name="international_fee" class="form-control" value="{{ $planInternationalFee }}" required min="0" step="0.01">
                </div>

                {{-- Currency --}}
                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" name="international_currency" class="form-control" value="{{ $planInternationalCurrency }}" required>
                </div>

                {{-- SSCL Tax --}}
                <div class="mb-3">
                    <label class="form-label">SSCL Tax</label>
                    <input type="number" name="sscl_tax" class="form-control" value="{{ $planSsclTax }}" min="0" step="0.01">
                </div>

                {{-- Bank Charges --}}
                <div class="mb-3">
                    <label class="form-label">Bank Charges</label>
                    <input type="number" name="bank_charges" class="form-control" value="{{ $planBankCharges }}" min="0" step="0.01">
                </div>

                {{-- Apply Discount --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" name="apply_discount" value="1" class="form-check-input" id="applyDiscountCheckbox"
                           {{ $plan->apply_discount ? 'checked' : '' }}>
                    <label class="form-check-label" for="applyDiscountCheckbox">Apply Full Payment Discount</label>
                </div>

                {{-- Full Payment Discount --}}
                <div class="mb-3">
                    <label class="form-label">Full Payment Discount (%)</label>
                    <input type="number" class="form-control" name="discount" value="{{ $planDiscount }}" min="0" step="0.01">
                </div>

                {{-- Installment Plan --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" name="installment_plan" value="1" class="form-check-input" id="installmentPlanCheckbox"
                           {{ $plan->installment_plan ? 'checked' : '' }}>
                    <label class="form-check-label" for="installmentPlanCheckbox">Enable Installment Plan</label>
                </div>

                {{-- Installments --}}
                <div class="mb-3">
                    <label class="form-label">Installments</label>

                    <table class="table table-bordered bg-white">
                        <thead class="table-light">
                            <tr>
                                <th>No.</th>
                                <th>Due Date</th>
                                <th>Local (LKR)</th>
                                <th>International ({{ $planInternationalCurrency }})</th>
                                <th>Tax?</th>
                            </tr>
                        </thead>
                        <tbody id="installmentsTableBody">
                            @forelse($installments as $i => $inst)
                                @php
                                    $dueDate = is_scalar($inst['due_date'] ?? null) ? (string) ($inst['due_date'] ?? '') : '';
                                    $localAmount = is_scalar($inst['local_amount'] ?? null) ? (string) ($inst['local_amount'] ?? '') : '';
                                    $internationalAmount = is_scalar($inst['international_amount'] ?? null) ? (string) ($inst['international_amount'] ?? '') : '';
                                @endphp
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td><input type="date" name="installments[{{ $i }}][due_date]" value="{{ $dueDate }}" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[{{ $i }}][local_amount]" value="{{ $localAmount }}" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[{{ $i }}][international_amount]" value="{{ $internationalAmount }}" class="form-control"></td>
                                    <td class="text-center">
                                        <input type="checkbox" name="installments[{{ $i }}][apply_tax]" value="1" @checked(!empty($inst['apply_tax']))>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No installments defined</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="small text-muted mb-2" id="installmentTotalsNote">
                        Installment totals will sync to the fee fields before save.
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-primary btn-add-installment-row">+ Add Row</button>
                        <button type="button" class="btn btn-sm btn-danger btn-remove-last-row">Remove Last</button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

{{-- JS for dynamic rows --}}
<script nonce="{{ $cspNonce ?? '' }}">
// Event delegation for buttons
document.addEventListener('click', function(e) {
    if (e.target.closest('.btn-add-installment-row')) {
        addInstallmentRow();
    } else if (e.target.closest('.btn-remove-last-row')) {
        removeLastRow();
    }
});

function addInstallmentRow() {
    let tbody = document.getElementById('installmentsTableBody');
    let index = tbody.rows.length;
    let row = tbody.insertRow();

    row.innerHTML = `
        <td>${index+1}</td>
        <td><input type="date" name="installments[${index}][due_date]" class="form-control"></td>
        <td><input type="number" step="0.01" name="installments[${index}][local_amount]" class="form-control"></td>
        <td><input type="number" step="0.01" name="installments[${index}][international_amount]" class="form-control"></td>
        <td class="text-center"><input type="checkbox" name="installments[${index}][apply_tax]" value="1"></td>
    `;

    syncFeeFieldsFromInstallments();
}

function removeLastRow() {
    let tbody = document.getElementById('installmentsTableBody');
    if (tbody.rows.length > 0) {
        tbody.deleteRow(tbody.rows.length - 1);
    }

    syncFeeFieldsFromInstallments();
}

function syncFeeFieldsFromInstallments() {
    const installmentPlanEnabled = document.getElementById('installmentPlanCheckbox')?.checked;
    if (!installmentPlanEnabled) {
        return;
    }

    let totalLocal = 0;
    let totalInternational = 0;

    document.querySelectorAll('#installmentsTableBody input[name$="[local_amount]"]').forEach(function (input) {
        totalLocal += parseFloat(input.value || '0') || 0;
    });

    document.querySelectorAll('#installmentsTableBody input[name$="[international_amount]"]').forEach(function (input) {
        totalInternational += parseFloat(input.value || '0') || 0;
    });

    const localFeeInput = document.getElementById('localFee');
    const internationalFeeInput = document.getElementById('internationalFee');

    if (localFeeInput) {
        localFeeInput.value = totalLocal.toFixed(2);
    }

    if (internationalFeeInput) {
        internationalFeeInput.value = totalInternational.toFixed(2);
    }
}

document.addEventListener('input', function (e) {
    if (e.target.closest('#installmentsTableBody')) {
        syncFeeFieldsFromInstallments();
    }
});

document.addEventListener('change', function (e) {
    if (e.target.id === 'installmentPlanCheckbox') {
        syncFeeFieldsFromInstallments();
    }
});

window.addEventListener('DOMContentLoaded', function () {
    syncFeeFieldsFromInstallments();
});
</script>

@endsection
