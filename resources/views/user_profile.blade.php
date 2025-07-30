@extends('inc.app')

@section('title', 'NEBULA | User Profile')

@section('content')
 <style>
  .nav-tabs .nav-link.active {
    background-color: #6c8cff !important;
    color: #fff !important;
    border-color: #6c8cff #6c8cff #fff !important;
    font-weight: 500;
  }
  .nav-tabs .nav-link {
    color: #6c8cff;
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-bottom: none;
    margin-right: 5px;
    border-radius: 4px 4px 0 0;
    padding: 10px 20px;
    transition: all 0.3s ease;
  }
  .nav-tabs .nav-link:not(.active) {
    background-color: #f8f9fa !important;
    color: #6c8cff !important;
    border-color: #dee2e6 #dee2e6 #fff !important;
    }
  </style>
<div class="container mt-5">
            <div class="p-4 rounded shadow w-100 bg-white mt-4">
              <h2 class="text-center mb-4">User Profile</h2>
              <hr style="margin-bottom: 30px;">
              <div class="mb-3 text-center position-relative">
                <div class="d-flex justify-content-end">
                  <div class="rounded-circle overflow-hidden mx-auto mb-3 position-relative" style="width: 150px; height: 150px; border: 2px solid #ccc;">
                    <img src="{{asset('images/profile/user-1.jpg') }}" alt="User Profile" class="w-100 h-100 object-cover" id="profilePicture">
                  </div>
                </div>
                <input type="file" class="form-control visually-hidden" id="profilePicture" accept="image/*">
                <div class="d-flex justify-content-end mx-4">
                  <button type="button" class="btn btn-sm btn-primary align-self-end" data-bs-toggle="modal" data-bs-target="#editPictureModal">Edit Picture</button>
                </div>
              </div>
              <!-- Edit Picture Modal -->
              <div class="modal fade" id="editPictureModal" tabindex="-1" role="dialog" aria-labelledby="editPictureModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                  <div class="modal-content">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editPictureModalLabel">Edit Profile Picture</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                      <div class="mb-3">
                        <label for="newProfilePicture" class="form-label fw-bold">New Profile Picture</label>
                        <input type="file" class="form-control" id="newProfilePicture" accept="image/*">
                      </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button type="button" class="btn btn-primary">Save changes</button>
                    </div>
                  </div>
                </div>
              </div>
              <div class="container mt-5">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                  <li class="nav-item">
          <a class="nav-link" id="profile-tab" data-bs-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="true">Profile</a>
                    </li>
                    <li class="nav-item">
          <a class="nav-link" id="settings-tab" data-bs-toggle="tab" href="#settings" role="tab" aria-controls="settings" aria-selected="false">Settings</a>
                  </li>
                </ul>
                <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <div class="mb-3 row align-items-center mx-3 mt-4">
                      <label for="adminEmail" class="col-sm-2 col-form-label fw-bold">Email</label>
                      <div class="col-sm-10">
                        <input type="email" class="form-control" value="{{ $userData['email'] }}" id="adminEmail" placeholder="User email" readonly>
                      </div>
                    </div>
          <div class="mb-3 row align-items-center mx-3">
            <label for="employeeId" class="col-sm-2 col-form-label fw-bold">Employee ID</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="employeeId" value="{{ $userData['employee_id'] ?? '' }}" placeholder="Employee ID" readonly>
            </div>
          </div>
          <div class="mb-3 row align-items-center mx-3">
            <label for="userName" class="col-sm-2 col-form-label fw-bold">User Name</label>
            <div class="col-sm-10">
              <input type="text" class="form-control" id="userName" value="{{ $userData['user_name'] ?? '' }}" placeholder="User Name" readonly>
            </div>
          </div>
                    <div class="mb-3 row align-items-center mx-3">
            <label for="userRole" class="col-sm-2 col-form-label fw-bold">User Role</label>
                      <div class="col-sm-10">
              <input type="text" class="form-control" id="userRole" value="{{ $userData['user_role'] ?? '' }}" placeholder="User Role" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
            <label for="userLocation" class="col-sm-2 col-form-label fw-bold">User Location</label>
                      <div class="col-sm-10">
              <input type="text" class="form-control" id="userLocation" value="{{ $userData['user_location'] ?? '' }}" placeholder="User Location" readonly>
                      </div>
                    </div>
                    <div class="mb-3 row align-items-center mx-3">
                      <label for="userStatus" class="col-sm-2 col-form-label fw-bold">Status</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" id="userStatus" value="{{ $userData['status'] }}" placeholder="User status" readonly>
                      </div>
                    </div>
                  </div>
        <div class="tab-pane fade" id="settings" role="tabpanel" aria-labelledby="settings-tab">
          <div class="card mt-4">
            <div class="card-body">
              <h4 class="mb-3 text-center">Change Password</h4>
              <form id="changePasswordForm" method="POST" action="{{ route('user.changePassword') }}">
                @csrf
                <div class="mb-3">
                  <label for="current_password" class="form-label">Current Password</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" required>
                        </div>
                <div class="mb-3">
                  <label for="new_password" class="form-label">New Password</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" required minlength="6">
                                </div>
                <div class="mb-3">
                  <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                  <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required minlength="6">
                              </div>
                <button type="submit" class="btn btn-primary w-100">Change Password</button>
                            </form>
                          </div>
                        </div>
          <div class="toast-container position-fixed bottom-0 end-0 p-3"></div>
                        <script>
                          document.addEventListener('DOMContentLoaded', function() {
              const form = document.getElementById('changePasswordForm');
              function showToast(message, type) {
                const toastHtml = `
                  <div class=\"toast align-items-center text-white bg-${type} border-0\" role=\"alert\" aria-live=\"assertive\" aria-atomic=\"true\">
                    <div class=\"d-flex\">
                      <div class=\"toast-body\">${message}</div>
                      <button type=\"button\" class=\"btn-close btn-close-white me-2 m-auto\" data-bs-dismiss=\"toast\" aria-label=\"Close\"></button>
                    </div>
                  </div>`;
                document.querySelector('.toast-container').insertAdjacentHTML('beforeend', toastHtml);
                const toastEl = document.querySelector('.toast-container .toast:last-child');
                const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
                toast.show();
                return toast;
              }
              if (form) {
                form.addEventListener('submit', function(e) {
                  e.preventDefault();
                              const formData = new FormData(form);
                              fetch(form.action, {
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
                      form.reset();
                            } else {
                      showToast(data.message || 'Error changing password', 'danger');
                            }
                          })
                          .catch(error => {
                    showToast('Error: ' + error.message, 'danger');
                  });
                });
              }
                });
              </script>
        </div>
      </div>
      <script>
        document.addEventListener('DOMContentLoaded', function() {
          var triggerTabList = [].slice.call(document.querySelectorAll('#myTab a'));
          triggerTabList.forEach(function(triggerEl) {
            var tabTrigger = new bootstrap.Tab(triggerEl);
            triggerEl.addEventListener('click', function (event) {
              event.preventDefault();
              tabTrigger.show();
            });
          });
          // Activate the first tab by default
          bootstrap.Tab.getOrCreateInstance(document.querySelector('#myTab a')).show();
        });
      </script>
            </div>
          </div>
        </div>
@endsection
