@extends('inc.app')

@section('title', 'NEBULA | User Management')

@section('content')
<style nonce="{{ $cspNonce }}">
    .user-mgmt-card {
        max-width: 1100px;
        margin: 40px auto 0 auto;
        border-radius: 18px;
        box-shadow: 0 4px 24px 0 rgba(60, 72, 100, 0.08);
        background: #fff;
        padding: 2.5rem 2rem 2rem 2rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }
    .user-mgmt-title {
        font-size: 2.1rem;
        font-weight: 700;
        color: #222b45;
        margin-bottom: 2.5rem;
        letter-spacing: -1px;
        text-align: center;
        width: 100%;
    }
    .user-mgmt-table-wrap {
        margin-top: 1.5rem;
        width: 100%;
    }
    .table thead th {
        position: sticky;
        top: 0;
        background: #e8f0fe !important;
        color: #23408e !important;
        z-index: 2;
        font-weight: 600;
        font-size: 0.95rem;
        border-bottom: 2px solid #dbeafe;
    }
    .table td, .table th {
        vertical-align: middle;
        font-size: 0.93rem;
        padding: 0.7rem 0.6rem;
    }
    .dataTables_wrapper .row {
        margin-bottom: 0.5rem;
    }
    .dataTables_length, .dataTables_filter {
        margin-bottom: 1rem;
    }
    .dataTables_length {
        float: left;
    }
    .dataTables_filter {
        float: right;
        text-align: right;
    }
    .dataTables_length label,
    .dataTables_filter label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0;
    }
    .dataTables_length select {
        width: auto;
        display: inline-block;
    }
    .dataTables_filter input {
        width: auto;
        display: inline-block;
        margin-left: 0.5rem;
    }
    @media (max-width: 991px) {
        .user-mgmt-card { padding: 1.2rem 0.5rem; }
        .user-mgmt-title { font-size: 1.4rem; }
    }
    @media (max-width: 600px) {
        .user-mgmt-title { font-size: 1.1rem; }
    }
</style>

<div class="container mt-5">
    <div class="p-4 rounded shadow w-100 bg-white mt-4">
        <h3 class="text-center mb-4">User Management</h3>
        <div class="user-mgmt-table-wrap">
            <table class="table table-striped table-bordered" id="usersTable" style="width: 100%;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Employee ID</th>
                        <th>Roles</th>
                        <th>Location</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($usersArray as $user)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $user['user_name'] }}</td>
                        <td>{{ $user['email'] }}</td>
                        <td>{{ $user['employee_id'] }}</td>
                        <td>{{ $user['user_role'] }}</td>
                        <td>{{ $user['user_location'] }}</td>
                        <td>{{ $user['created_at'] }}</td>
                        <td>
                            <button class="btn btn-sm btn-primary btn-edit-user" data-user-id="{{ $user['user_id'] }}">Edit</button>
                            <button class="btn btn-sm btn-danger ms-1 btn-delete-user" data-user-id="{{ $user['user_id'] }}" data-user-name="{{ $user['user_name'] }}">Delete</button>
                            <button class="btn btn-sm btn-warning ms-1 btn-reset-password" data-user-id="{{ $user['user_id'] }}" data-user-name="{{ $user['user_name'] }}">Reset Password</button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editUserForm" method="POST" action="{{ route('user.updateStatus') }}">
                    @csrf
                    <input type="hidden" id="edit_user_id" name="user_id">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_name" class="form-label">Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="edit_email" name="email" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_employee_id" class="form-label">Employee ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="edit_employee_id" name="employee_id" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_user_roles" class="form-label">Roles <span class="text-danger">*</span></label>
                                <div class="dropdown">
                                    <button class="form-control text-start dropdown-toggle"
                                            type="button"
                                            id="editRoleDropdownButton"
                                            data-bs-toggle="dropdown"
                                            aria-expanded="false">
                                        Select role(s)
                                    </button>
                                    <div class="dropdown-menu w-100 p-2" aria-labelledby="editRoleDropdownButton" style="max-height: 260px; overflow-y: auto;">
                                        @foreach ($userRoles as $index => $role)
                                            <div class="form-check">
                                                <input class="form-check-input edit-role-option" type="checkbox" value="{{ $role }}" id="edit_role_{{ $index }}">
                                                <label class="form-check-label" for="edit_role_{{ $index }}">{{ $role }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <select class="d-none" id="edit_user_roles" name="user_roles[]" multiple required>
                                    @foreach ($userRoles as $role)
                                        <option value="{{ $role }}">{{ $role }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">You can select more than one role from the dropdown.</small>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_user_location" class="form-label">Location <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_user_location" name="user_location" required>
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location }}">{{ $location }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_status" class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control" id="edit_status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Inactive</option>
                                    <option value="2">Suspended</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="editUserForm" class="btn btn-primary">Update User</button>
            </div>
        </div>
    </div>
}</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1" aria-labelledby="resetPasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="resetPasswordModalLabel">Reset Password for <span id="resetUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="resetPasswordForm" method="POST" action="{{ route('user.resetPassword') }}">
                @csrf
                <input type="hidden" id="reset_user_id" name="user_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="password" class="form-control password-toggle" id="new_password" name="new_password" required>
                            <button class="btn btn-outline-secondary toggle-password" type="button" tabindex="-1">
                                <i class="ti ti-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Include DataTables CSS and JS -->
<link nonce="{{ $cspNonce }}" rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
<script nonce="{{ $cspNonce }}" type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script nonce="{{ $cspNonce }}" type="text/javascript" src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>

<script nonce="{{ $cspNonce }}">
document.addEventListener('DOMContentLoaded', function() {
    const editRoleSelect = document.getElementById('edit_user_roles');
    const editRoleDropdownButton = document.getElementById('editRoleDropdownButton');
    const editRoleCheckboxes = document.querySelectorAll('.edit-role-option');

    function getRoleButtonText(selectedRoles) {
        if (selectedRoles.length === 0) {
            return 'Select role(s)';
        }

        if (selectedRoles.length <= 2) {
            return selectedRoles.join(', ');
        }

        return `${selectedRoles.length} roles selected`;
    }

    function syncEditRoleSelection() {
        const selectedRoles = [];

        editRoleCheckboxes.forEach((checkbox) => {
            const option = Array.from(editRoleSelect.options).find((opt) => opt.value === checkbox.value);

            if (option) {
                option.selected = checkbox.checked;
            }

            if (checkbox.checked) {
                selectedRoles.push(checkbox.value);
            }
        });

        editRoleDropdownButton.textContent = getRoleButtonText(selectedRoles);
    }

    window.setEditRoleSelection = function(selectedRoles) {
        editRoleCheckboxes.forEach((checkbox) => {
            checkbox.checked = selectedRoles.includes(checkbox.value);
        });

        syncEditRoleSelection();
    };

    editRoleCheckboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', syncEditRoleSelection);
    });

    syncEditRoleSelection();

    // Event delegation for action buttons (CSP compliant)
    document.addEventListener('click', function(e) {
        if (e.target.closest('.btn-edit-user')) {
            const btn = e.target.closest('.btn-edit-user');
            editUser(btn.dataset.userId);
        }
        if (e.target.closest('.btn-delete-user')) {
            const btn = e.target.closest('.btn-delete-user');
            deleteUser(btn.dataset.userId, btn.dataset.userName);
        }
        if (e.target.closest('.btn-reset-password')) {
            const btn = e.target.closest('.btn-reset-password');
            showResetPasswordModal(btn.dataset.userId, btn.dataset.userName);
        }
    });
    
    // Initialize DataTable
    $('#usersTable').DataTable({
        responsive: true,
        order: [], // No initial ordering
        pageLength: 25,
        language: {
            search: "Search users:",
            lengthMenu: "Show _MENU_ users per page",
            info: "Showing _START_ to _END_ of _TOTAL_ users"
        },
        dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        columnDefs: [
            { orderable: false, targets: 0 }, // Disable sorting for serial number column
            { orderable: false, targets: [1,2,3,4,5,6,7] } // Disable sorting for all other columns
        ]
    });

    // Handle edit user form submission
    const editUserForm = document.getElementById('editUserForm');
    if (editUserForm) {
        editUserForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            
            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    $('#editUserModal').modal('hide');
                    // Reload page to show updated data
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Error updating user', 'danger');
                }
            })
            .catch(error => {
                showToast('Error: ' + error.message, 'danger');
            });
        });
    }
});

function editUser(userId) {
    // Fetch user details
    fetch('/user/get-details', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ user_id: userId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const user = data.user;
            document.getElementById('edit_user_id').value = user.user_id;
            document.getElementById('edit_name').value = user.name;
            document.getElementById('edit_email').value = user.email;
            document.getElementById('edit_employee_id').value = user.employee_id;
            const selectedRoles = Array.isArray(user.user_roles) && user.user_roles.length > 0
                ? user.user_roles
                : (user.user_role ? [user.user_role] : []);
            if (typeof window.setEditRoleSelection === 'function') {
                window.setEditRoleSelection(selectedRoles);
            }
            document.getElementById('edit_user_location').value = user.user_location;
            document.getElementById('edit_status').value = user.status;
            
            $('#editUserModal').modal('show');
        } else {
            showToast(data.message || 'Error fetching user details', 'danger');
        }
    })
    .catch(error => {
        showToast('Error: ' + error.message, 'danger');
    });
}

function deleteUser(userId, userName) {
    if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
        fetch('/user/delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast(data.message, 'success');
                // Reload page to show updated data
                setTimeout(() => location.reload(), 1500);
            } else {
                showToast(data.message || 'Error deleting user', 'danger');
            }
        })
        .catch(error => {
            showToast('Error: ' + error.message, 'danger');
        });
    }
}

function showResetPasswordModal(userId, userName) {
    document.getElementById('reset_user_id').value = userId;
    document.getElementById('resetUserName').textContent = userName;
    document.getElementById('new_password').value = '';
    var modal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    modal.show();
}

// Show/hide password toggle for all password fields
$(document).on('click', '.toggle-password', function() {
    var input = $(this).siblings('input');
    var icon = $(this).find('i');
    if (input.attr('type') === 'password') {
        input.attr('type', 'text');
        icon.removeClass('ti-eye').addClass('ti-eye-off');
        $(this).attr('title', 'Hide password');
    } else {
        input.attr('type', 'password');
        icon.removeClass('ti-eye-off').addClass('ti-eye');
        $(this).attr('title', 'Show password');
    }
});

function showToast(message, type) {
    const toastHtml = `
        <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>`;
    document.querySelector('.toast-container').insertAdjacentHTML('beforeend', toastHtml);
    const toastEl = document.querySelector('.toast-container .toast:last-child');
    const toast = new bootstrap.Toast(toastEl, { delay: 3000 });
    toast.show();
    return toast;
}
</script>

@endsection
