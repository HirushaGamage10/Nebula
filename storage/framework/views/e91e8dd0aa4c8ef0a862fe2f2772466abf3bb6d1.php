

<?php $__env->startSection('title', 'NEBULA | Student Other Information'); ?>

<?php $__env->startSection('content'); ?>
      <div class="container-fluid">
        <div class="row justify-content-center">
          <div class="col-md-11 mt-2">
            <div class="card">
                <div class="card-body">
              <h2 class="text-center mb-4">Student Other Information</h2>
              <hr style="margin-bottom: 30px;">
                    
                    <!-- Search Section -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form id="nicSearchForm">
                                <div class="mb-3 row mx-3 align-items-center">
                                    <label for="nicInput" class="col-sm-2 col-form-label">Student NIC<span class="text-danger">*</span></label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control bg-white" id="nicInput" name="nic" placeholder="Enter Student ID (NIC)" required>
                                    </div>
                                    <div class="col-sm-2">
                                        <button type="submit" class="btn btn-primary w-100">Search</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr style="margin-top: 30px;">

              <!-- Toast Container -->
              <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

              <!-- Spinner -->
                    <div id="spinner-overlay" style="display: none;">
                <div class="lds-ring">
                  <div></div>
                  <div></div>
                  <div></div>
                  <div></div>
                </div>
              </div>

              <!-- Message Display Modal -->
              <div class="modal fade" id="messageModal" tabindex="-1" aria-labelledby="messageModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                  <div class="modal-content">
                    <div class="modal-header">
                                    <h5 class="modal-title" id="messageModalLabel">Message</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                                <div class="modal-body" id="messageModalBody">
                                    <!-- Message content will be inserted here -->
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                  </div>
                </div>
              </div>

                    <!-- Student Other Information Form -->
                    <div id="studentOtherInformationForm" style="display: none;">
                        <form id="otherInformationForm" class="p-4 rounded w-100 bg-white mt-4">
                            <!-- Student Details Section -->
                            <div class="mb-4">
                                <h5 class="mb-3">Student Details</h5>
                                <div class="mb-3 row mx-3 align-items-center">
                                    <label for="studentNameInput" class="col-sm-3 col-form-label">Name</label>
                                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-white" id="studentNameInput" name="studentName" readonly>
                                    </div>
                                </div>
                                <div class="mb-3 row mx-3 align-items-center">
                                    <label for="studentIDInput" class="col-sm-3 col-form-label">Student ID</label>
                    <div class="col-sm-9">
                                        <input type="text" class="form-control bg-white" id="studentIDInput" name="studentID" readonly>
                                    </div>
                    </div>
                  </div>

                            <!-- Disciplinary Issues Section -->
                            <div class="mb-4">
                                <div class="mb-3 row mx-3 align-items-center">
                                    <label for="disciplinaryIssues" class="col-sm-3 col-form-label">Disciplinary Issues</label>
                  <div class="col-sm-9">
                                        <textarea class="form-control" id="disciplinaryIssues" name="disciplinaryIssues" placeholder="Enter disciplinary issues" rows="3"></textarea>
                                    </div>
                  </div>
                  <div class="mb-3 row mx-3 align-items-center">
                    <label for="disciplinary_issue_document" class="col-sm-3 col-form-label">Disciplinary Issue Document</label>
                    <div class="col-sm-9">
                        <input type="file" class="form-control" id="disciplinary_issue_document" name="disciplinary_issue_document" accept=".pdf,.doc,.docx,.jpg,.png">
                    </div>
                  </div>
                </div>
                            <hr>

                            <!-- Higher Studies Section -->
                            <div class="mb-4">
                <div class="mb-3 row mx-3 align-items-center">
                  <label class="col-sm-3 col-form-label">Continue to Higher Studies?</label>
                  <div class="col-sm-9">
                    <div class="form-check form-check-inline">
                      <input value="true" class="form-check-input" type="radio" id="continueYes" name="continueStudies" style="cursor: pointer;">
                      <label class="form-check-label" for="continueYes" style="cursor: pointer;">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input value="false" class="form-check-input" type="radio" id="continueNo" name="continueStudies" style="cursor: pointer;" checked>
                      <label class="form-check-label" for="continueNo" style="cursor: pointer;">No</label>
                    </div>
                  </div>
                </div>
                                
                                <!-- Institute and Field of Study Fields -->
                                <div id="higherStudiesContainer" class="mb-3 mx-5 bg-light-primary p-3 rounded" style="display: none;">
                                    <div class="mb-3 row align-items-center">
                    <label for="institute" class="col-sm-2 col-form-label">Institute<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                                            <input type="text" class="form-control bg-white" id="institute" name="institute" placeholder="Enter institute">
                    </div>
                  </div>
                                    <div class="mb-1 row align-items-center">
                    <label for="fieldOfStudy" class="col-sm-2 col-form-label">Field of Study<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                                            <input type="text" class="form-control bg-white" id="fieldOfStudy" name="fieldOfStudy" placeholder="Enter field of study">
                                        </div>
                    </div>
                  </div>
                </div>
                            <hr>

                            <!-- Employment Section -->
                            <div class="mb-4">
                <div class="mb-3 row mx-3 align-items-center">
                  <label class="col-sm-3 col-form-label">Currently an Employee?</label>
                  <div class="col-sm-9">
                    <div class="form-check form-check-inline">
                      <input value="true" class="form-check-input" type="radio" id="employeeYes" name="currentlyEmployee" style="cursor: pointer;">
                      <label class="form-check-label" for="employeeYes" style="cursor: pointer;">Yes</label>
                    </div>
                    <div class="form-check form-check-inline">
                      <input value="false" class="form-check-input" type="radio" id="employeeNo" name="currentlyEmployee" style="cursor: pointer;" checked>
                      <label class="form-check-label" for="employeeNo" style="cursor: pointer;">No</label>
                    </div>
                  </div>
                </div>
                                
                                <!-- Job Details Fields -->
                                <div id="employmentContainer" class="mb-3 mx-5 bg-light-primary p-3 rounded" style="display: none;">
                                    <div class="mb-3 row align-items-center">
                    <label for="jobTitle" class="col-sm-2 col-form-label">Job Title<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                                            <input type="text" class="form-control bg-white" id="jobTitle" name="jobTitle" placeholder="Enter job title">
                    </div>
                  </div>
                                    <div class="mb-1 row align-items-center">
                    <label for="workplace" class="col-sm-2 col-form-label">Workplace<span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                                            <input type="text" class="form-control bg-white" id="workplace" name="workplace" placeholder="Enter workplace">
                                        </div>
                    </div>
                  </div>
                </div>
                            <hr>
                            
                            <!-- Other Information Section -->
                            <div class="mb-4">
                                <div class="mb-3 row mx-3 align-items-center">
                                    <label for="otherInformation" class="col-sm-3 col-form-label">Other Information</label>
                  <div class="col-sm-9">
                                        <textarea class="form-control" id="otherInformation" name="otherInformation" placeholder="Enter other information" rows="3"></textarea>
                                    </div>
                  </div>
                </div>

                            <!-- Submit Button -->
                            <div class="text-center mt-4">
                <button id="dataSubmit" type="button" class="btn btn-primary w-100 mt-5">SAVE</button>
              </div>
            </form>
          </div>
        </div>
      </div>
              </div>
            </div>
          </div>

    <script>
    // Show/hide higher studies fields based on radio button selection
    document.querySelectorAll('input[name="continueStudies"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const container = document.getElementById('higherStudiesContainer');
            if (this.value === 'true') {
                container.style.display = 'block';
      } else {
                container.style.display = 'none';
            }
        });
    });

    // Show/hide employment fields based on radio button selection
    document.querySelectorAll('input[name="currentlyEmployee"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            const container = document.getElementById('employmentContainer');
            if (this.value === 'true') {
                container.style.display = 'block';
            } else {
                container.style.display = 'none';
            }
        });
    });

    // Remove old searchStudent() and replace with new NIC-only search

    document.getElementById('nicSearchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        var nic = document.getElementById('nicInput').value.trim();
        var studentOtherInformationForm = document.getElementById('studentOtherInformationForm');

        if (!nic) {
            showMessage('Warning', 'Please enter a NIC.');
            return;
        }

        // Show spinner
        document.getElementById('spinner-overlay').style.display = 'flex';

        // Make an AJAX request
        $.ajax({
            type: 'POST',
            url: '<?php echo e(route("retrieve.student.details")); ?>',
            data: {
                _token: '<?php echo e(csrf_token()); ?>',
                identificationType: 'nic',
                idValue: nic
            },
            success: function(response) {
                // Hide spinner
                document.getElementById('spinner-overlay').style.display = 'none';

                if (response.success) {
                    showToast('Success', response.message, '#ccffcc');

                    // Update input values
                    var studentNameInput = document.getElementById('studentNameInput');
                    var studentIdInput = document.getElementById('studentIDInput');
                    studentNameInput.value = response.data.student_name;
                    studentIdInput.value = response.data.student_id;

                    // Show the form
                    studentOtherInformationForm.style.display = 'block';
                } else {
                    studentOtherInformationForm.style.display = 'none';
                    showMessage('Warning', response.message);
                }
            },
            error: function(xhr, status, error) {
                // Hide spinner
                document.getElementById('spinner-overlay').style.display = 'none';
                studentOtherInformationForm.style.display = 'none';
                showMessage('Error', 'An error occurred while searching for the student.');
            }
        });
    });

    // Submit form data
    document.getElementById('dataSubmit').addEventListener('click', function() {
        var form = document.getElementById('otherInformationForm');
        var formData = new FormData(form);

        // Show spinner
        document.getElementById('spinner-overlay').style.display = 'flex';

        $.ajax({
            type: 'POST',
            url: '<?php echo e(route("store.other.informations")); ?>',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                // Hide spinner
                document.getElementById('spinner-overlay').style.display = 'none';

                if (response.success) {
                    showToast('Success', response.message, '#ccffcc');
                    // Reset form
                    form.reset();
                    document.getElementById('studentOtherInformationForm').style.display = 'none';
                    document.getElementById('studentNameInput').value = '';
                    document.getElementById('studentIDInput').value = '';
                } else {
                    showMessage('Error', response.message);
                }
            },
            error: function(xhr, status, error) {
                // Hide spinner
                document.getElementById('spinner-overlay').style.display = 'none';
                showMessage('Error', 'An error occurred while saving the data.');
            }
        });
    });

    function showMessage(title, message) {
        document.getElementById('messageModalLabel').textContent = title;
        document.getElementById('messageModalBody').textContent = message;
        new bootstrap.Modal(document.getElementById('messageModal')).show();
    }

    function showToast(title, message, backgroundColor) {
        const toastContainer = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.style.backgroundColor = backgroundColor;
        toast.innerHTML = `
            <div class="toast-header">
                <strong class="me-auto">${title}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        `;
        toastContainer.appendChild(toast);
        new bootstrap.Toast(toast).show();
        
        // Remove toast after it's hidden
        toast.addEventListener('hidden.bs.toast', function() {
            toast.remove();
        });
    }
</script>

<style>
    .lds-ring {
        display: inline-block;
        position: relative;
        width: 80px;
        height: 80px;
    }
    .lds-ring div {
        box-sizing: border-box;
        display: block;
        position: absolute;
        width: 64px;
        height: 64px;
        margin: 8px;
        border: 8px solid #fff;
        border-radius: 50%;
        animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
        border-color: #fff transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
        animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
        animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
        animation-delay: -0.15s;
    }
    @keyframes lds-ring {
        0% {
            transform: rotate(0deg);
        }
        100% {
            transform: rotate(360deg);
        }
    }
    #spinner-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 9999;
    }
    .bg-light-primary {
        background-color: #f1f5f9 !important;
    }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH E:\JOB\Projects\Nebula\resources\views/student_other_information.blade.php ENDPATH**/ ?>