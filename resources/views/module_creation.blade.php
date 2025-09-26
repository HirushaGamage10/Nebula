@extends('inc.app')

@section('title', 'NEBULA | Module Creation')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Create New Module</h2>
            <hr>
            <form id="moduleForm">
                @csrf
                <input type="hidden" id="module_id" name="module_id">
                <div class="mb-3 row mx-3">
                    <label for="module_name" class="col-sm-2 col-form-label">Module Name <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="module_name" name="module_name" placeholder="Enter module name" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="module_code" class="col-sm-2 col-form-label">Module Code <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" id="module_code" name="module_code" 
                               placeholder="e.g., CS101_Programming_001" 
                               pattern="^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$"
                               title="Module code must follow the pattern: program_name_specification_unit_code"
                               required>
                        <div class="form-text">
                            <small class="text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                Format: <code>programName_unitName_unitCode</code> (e.g., CS101_Programming_001)
                            </small>
                        </div>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="credits" class="col-sm-2 col-form-label">Credits <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="credits" name="credits" placeholder="Enter module credits" min="0" required>
                    </div>
                </div>
                <div class="mb-3 row mx-3">
                    <label for="module_type" class="col-sm-2 col-form-label">Module Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="module_type" name="module_type" required>
                            <option selected disabled value="">Choose a type...</option>
                            <option value="core">Core</option>
                            <option value="elective">Elective</option>
                            <option value="special_unit_compulsory">Special Unit Compulsory (S/U)</option>
                        </select>
                    </div>
                </div>
                <div class="d-grid mt-3">
                    <button type="submit" class="btn btn-primary" id="moduleSubmitBtn">Create Module</button>
                    <button type="button" class="btn btn-secondary mt-2" id="cancelEditBtn" style="display:none;">Cancel Edit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <h2 class="text-center mb-4">Existing Modules</h2>
            <hr>
            <div class="scrollable-table-container" style="max-height: 400px; overflow-y: auto; width: 100%;">
                <table class="table table-striped table-bordered scrollable-table" style="width: 100%;">
                    <thead style="position: sticky; top: 0; background: #fff; z-index: 2;">
                        <tr>
                            <th style="position: sticky; top: 0; background: #fff;">Module Name</th>
                            <th style="position: sticky; top: 0; background: #fff;">Module Code</th>
                            <th style="position: sticky; top: 0; background: #fff;">Credits</th>
                            <th style="position: sticky; top: 0; background: #fff;">Type</th>
                            <th style="position: sticky; top: 0; background: #fff;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="module-table-body">
                        @forelse($modules as $module)
                        <tr id="module-row-{{$module->module_id}}">
                            <td>{{ $module->module_name }}</td>
                            <td>{{ $module->module_code }}</td>
                            <td>{{ $module->credits }}</td>
                            <td>{{ $module->module_type }}</td>
                            <td>
                                <button class="btn btn-sm btn-primary edit-module-btn">Edit</button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center">No modules found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let editMode = false;
    let editingModuleId = null;

    // Handle Edit button click
    $(document).on('click', '.edit-module-btn', function() {
        const row = $(this).closest('tr');
        const moduleId = row.attr('id').replace('module-row-', '');
        const moduleName = row.find('td:eq(0)').text().trim();
        const moduleCode = row.find('td:eq(1)').text().trim();
        const credits = row.find('td:eq(2)').text().trim();
        const moduleType = row.find('td:eq(3)').text().trim();

        $('#module_id').val(moduleId);
        $('#module_name').val(moduleName);
        $('#module_code').val(moduleCode);
        $('#credits').val(credits);
        $('#module_type').val(moduleType);
        $('#moduleSubmitBtn').text('Update Module');
        $('#cancelEditBtn').show();
        editMode = true;
        editingModuleId = moduleId;
    });

    // Cancel edit
    $('#cancelEditBtn').on('click', function() {
        resetModuleForm();
    });

    // Module code pattern validation
    $('#module_code').on('input', function() {
        const moduleCode = $(this).val();
        const pattern = /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/;
        const isValid = pattern.test(moduleCode);
        
        // Remove existing validation classes
        $(this).removeClass('is-valid is-invalid');
        
        if (moduleCode.length > 0) {
            if (isValid) {
                $(this).addClass('is-valid');
                $(this).next('.form-text').find('.validation-message').remove();
            } else {
                $(this).addClass('is-invalid');
                if ($(this).next('.form-text').find('.validation-message').length === 0) {
                    $(this).next('.form-text').append('<div class="validation-message text-danger mt-1"><small><i class="ti ti-alert-circle me-1"></i>Invalid format. Use: program_name_specification_unit_code</small></div>');
                }
            }
        }
    });

    // Handle form submit
    $('#moduleForm').on('submit', function(e) {
        e.preventDefault();
        
        // Validate module code pattern before submission
        const moduleCode = $('#module_code').val();
        const pattern = /^[a-zA-Z0-9]+_[a-zA-Z0-9]+_[a-zA-Z0-9]+$/;
        
        if (!pattern.test(moduleCode)) {
            showToast('Module code must follow the pattern: program_name_specification_unit_code (e.g., CS101_Programming_001)', 'danger');
            $('#module_code').addClass('is-invalid');
            return;
        }
        
        const moduleId = $('#module_id').val();
        const formData = $(this).serialize();
        if (editMode && moduleId) {
            // Update existing module
            $.ajax({
                url: '/modules/' + moduleId,
                type: 'PATCH',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        // Update the row in the table
                        const module = response.module;
                        const row = $('#module-row-' + module.module_id);
                        row.find('td:eq(0)').text(module.module_name);
                        row.find('td:eq(1)').text(module.module_code);
                        row.find('td:eq(2)').text(module.credits);
                        row.find('td:eq(3)').text(module.module_type);
                        resetModuleForm();
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage += '<br>' + errors.join('<br>');
                        }
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        } else {
            // Create new module
            $.ajax({
                url: '{{ route("module.store") }}',
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showToast(response.message, 'success');
                        $('#moduleForm')[0].reset();
                        const module = response.module;
                        const newRow = `
                            <tr id="module-row-${module.module_id}">
                                <td>${module.module_name}</td>
                                <td>${module.module_code}</td>
                                <td>${module.credits}</td>
                                <td>${module.module_type}</td>
                                <td>
                                    <button class="btn btn-sm btn-primary edit-module-btn">Edit</button>
                                </td>
                            </tr>
                        `;
                        if ($('#module-table-body').find('td[colspan="5"]').length) {
                            $('#module-table-body').html(newRow);
                        } else {
                            $('#module-table-body').prepend(newRow);
                        }
                    } else {
                        showToast(response.message, 'danger');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'An error occurred.';
                    if (xhr.responseJSON) {
                        errorMessage = xhr.responseJSON.message;
                        if (xhr.responseJSON.errors) {
                            const errors = Object.values(xhr.responseJSON.errors).flat();
                            errorMessage += '<br>' + errors.join('<br>');
                        }
                    }
                    showToast(errorMessage, 'danger');
                }
            });
        }
    });

    function resetModuleForm() {
        $('#moduleForm')[0].reset();
        $('#module_id').val('');
        $('#moduleSubmitBtn').text('Create Module');
        $('#cancelEditBtn').hide();
        editMode = false;
        editingModuleId = null;
    }

    function showToast(message, type) {
        const toastHtml = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        $('.toast-container').append(toastHtml);
        const toastEl = $('.toast-container .toast').last();
        const toast = new bootstrap.Toast(toastEl, { delay: 5000 });
        toast.show();
    }
});
</script>
@endpush
