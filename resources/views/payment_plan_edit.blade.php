@extends('inc.app')
@section('title','Edit Payment Plan')
@section('content')

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Edit Payment Plan</h2>

            {{-- Validation errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('payment.plan.update', $plan->id) }}">
                @csrf
                @method('PUT')

                {{-- Location --}}
                <div class="mb-3">
                    <label class="form-label">Location</label>
                    <select name="location" class="form-select" required>
                        @foreach(['Welisara','Moratuwa','Peradeniya'] as $loc)
                            <option value="{{ $loc }}" @selected($plan->location==$loc)>{{ $loc }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Course --}}
                <div class="mb-3">
                    <label class="form-label">Course</label>
                    <select name="course_id" class="form-select" required>
                        @foreach($courses as $c)
                            <option value="{{ $c->course_id }}" @selected($plan->course_id==$c->course_id)>{{ $c->course_name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Intake --}}
                <div class="mb-3">
                    <label class="form-label">Intake</label>
                    <select name="intake_id" class="form-select">
                        <option value="">None</option>
                        @foreach($intakes as $i)
                            <option value="{{ $i->intake_id }}" @selected($plan->intake_id==$i->intake_id)>{{ $i->intake_id }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Registration Fee --}}
                <div class="mb-3">
                    <label class="form-label">Registration Fee</label>
                    <input type="number" name="registration_fee" class="form-control" value="{{ $plan->registration_fee }}" required min="0" step="0.01">
                </div>

                {{-- Local Fee --}}
                <div class="mb-3">
                    <label class="form-label">Local Fee</label>
                    <input type="number" name="local_fee" class="form-control" value="{{ $plan->local_fee }}" required min="0" step="0.01">
                </div>

                {{-- Franchise Fee --}}
                <div class="mb-3">
                    <label class="form-label">Franchise Fee</label>
                    <input type="number" name="international_fee" class="form-control" value="{{ $plan->international_fee }}" required min="0" step="0.01">
                </div>

                {{-- Currency --}}
                <div class="mb-3">
                    <label class="form-label">Currency</label>
                    <input type="text" name="international_currency" class="form-control" value="{{ $plan->international_currency }}" required>
                </div>

                {{-- SSCL Tax --}}
                <div class="mb-3">
                    <label class="form-label">SSCL Tax</label>
                    <input type="number" name="sscl_tax" class="form-control" value="{{ $plan->sscl_tax }}" min="0" step="0.01">
                </div>

                {{-- Bank Charges --}}
                <div class="mb-3">
                    <label class="form-label">Bank Charges</label>
                    <input type="number" name="bank_charges" class="form-control" value="{{ $plan->bank_charges }}" min="0" step="0.01">
                </div>

                {{-- Apply Discount --}}
                <div class="mb-3 form-check">
                    <input type="checkbox" name="apply_discount" value="1" class="form-check-input" id="applyDiscountCheckbox"
                           {{ $plan->apply_discount ? 'checked' : '' }}>
                    <label class="form-check-label" for="applyDiscountCheckbox">Apply Full Payment Discount</label>
                </div>

                {{-- Discount --}}
                <div class="mb-3">
                    <label class="form-label">Discount(%)</label>
                    <input type="number" class="form-control" name="discount" value="{{ $plan->discount }}" min="0" step="0.01">
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
                                <th>International ({{ $plan->international_currency }})</th>
                                <th>Tax?</th>
                            </tr>
                        </thead>
                        <tbody id="installmentsTableBody">
                            @forelse($installments as $i => $inst)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td><input type="date" name="installments[{{ $i }}][due_date]" value="{{ $inst['due_date'] ?? '' }}" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[{{ $i }}][local_amount]" value="{{ $inst['local_amount'] ?? '' }}" class="form-control"></td>
                                    <td><input type="number" step="0.01" name="installments[{{ $i }}][international_amount]" value="{{ $inst['international_amount'] ?? '' }}" class="form-control"></td>
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

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-sm btn-primary" onclick="addInstallmentRow()">+ Add Row</button>
                        <button type="button" class="btn btn-sm btn-danger" onclick="removeLastRow()">Remove Last</button>
                    </div>
                </div>

                {{-- Submit --}}
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
</div>

{{-- JS for dynamic rows --}}
<script>
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
}

function removeLastRow() {
    let tbody = document.getElementById('installmentsTableBody');
    if (tbody.rows.length > 0) {
        tbody.deleteRow(tbody.rows.length - 1);
    }
}
</script>

@endsection
