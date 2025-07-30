<?php $__env->startSection('title', 'NEBULA | Special Approval List'); ?>

<?php $__env->startSection('content'); ?>
<style>
/* Success Message Styles */
.success-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.success-message.show {
    transform: translateX(0);
}

.success-message .success-icon {
    margin-right: 10px;
    font-size: 18px;
}

/* Error Message Styles */
.error-message {
    position: fixed;
    top: 20px;
    right: 20px;
    z-index: 9999;
    background: linear-gradient(135deg, #dc3545, #e74c3c);
    color: white;
    padding: 15px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
    font-weight: 500;
    font-size: 14px;
    max-width: 400px;
    transform: translateX(100%);
    transition: transform 0.3s ease-in-out;
    border-left: 4px solid #fff;
}

.error-message.show {
    transform: translateX(0);
}

.error-message .error-icon {
    margin-right: 10px;
    font-size: 18px;
}
</style>
<style>
.nav-tabs .nav-link {
    border: none;
    border-bottom: 3px solid transparent;
    color: #6c757d;
    font-weight: 500;
    padding: 12px 20px;
    transition: all 0.3s ease;
}

.nav-tabs .nav-link:hover {
    border-color: #dee2e6;
    color: #495057;
}

.nav-tabs .nav-link.active {
    border-bottom-color: #0d6efd;
    color: #0d6efd;
    background-color: transparent;
}

.nav-tabs .nav-link i {
    font-size: 1.1rem;
}

.tab-content {
    padding-top: 20px;
}

.franchise-payment-table th {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    font-weight: 600;
}

.status-badge {
    font-size: 0.75rem;
    padding: 4px 8px;
}
</style>

<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Special Approval List</h2>
            <hr>
            
            <!-- Tab Navigation -->
            <ul class="nav nav-tabs" id="specialApprovalTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="student-registration-tab" data-bs-toggle="tab" data-bs-target="#student-registration" type="button" role="tab" aria-controls="student-registration" aria-selected="true">
                        <i class="ti ti-user me-2"></i>Student Registration
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="franchise-payment-tab" data-bs-toggle="tab" data-bs-target="#franchise-payment" type="button" role="tab" aria-controls="franchise-payment" aria-selected="false">
                        <i class="ti ti-currency-dollar me-2"></i>Franchise Payment Delays
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="specialApprovalTabContent">
                <!-- Student Registration Tab -->
                <div class="tab-pane fade show active" id="student-registration" role="tabpanel" aria-labelledby="student-registration-tab">
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Student Registration Approvals</strong>
                            <p class="mb-0 mt-2">Review and approve student registration requests that require special approval.</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead class="table-light">
                                    <tr>
                                        <th>Registration Number</th>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Document</th>
                                        <th>Remarks</th>
                                        <th>DGM Comment</th>
                                        <th>Approval Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="specialApprovalTableBody">
                                    <!-- Rows will be added here dynamically -->
                                </tbody>
                            </table>
                        </div>

                        <!-- Registration Section (same as eligibility page) -->
                        <div id="registerSection" class="card mb-4 shadow-sm" style="display:none;">
                            <div class="card-body bg-light">
                                <h5 class="mb-3 text-center">Student Register For Course</h5>
                                <form id="registerForm">
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Student NIC</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="inlineStudentNIC" name="nic" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Student Registration Number</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="inlineStudentRegNo" name="registration_number" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Intake</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="intake" name="intake" readonly>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <label class="col-sm-4 col-form-label">Course Registration ID</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="inlineCourseRegId" name="course_registration_id" readonly>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="btn btn-primary px-5 w-100">Register</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Franchise Payment Delays Tab -->
                <div class="tab-pane fade" id="franchise-payment" role="tabpanel" aria-labelledby="franchise-payment-tab">
                    <div class="mt-4">
                        <div class="alert alert-info">
                            <i class="ti ti-info-circle me-2"></i>
                            <strong>Franchise Payment Delays</strong>
                            <p class="mb-0 mt-2">Review and approve franchise payment delay requests that require special approval.</p>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="table table-bordered franchise-payment-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Franchise Name</th>
                                        <th>Student Name</th>
                                        <th>Course</th>
                                        <th>Due Date</th>
                                        <th>Days Delayed</th>
                                        <th>Amount Due</th>
                                        <th>Reason</th>
                                        <th>DGM Comment</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody id="franchisePaymentTableBody">
                                    <tr>
                                        <td colspan="10" class="text-center text-muted py-4">
                                            <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2 mb-0">No franchise payment delay requests found</p>
                                            <small class="text-muted">This feature will be implemented in future updates</small>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DGM Comment Edit Modal -->
<div class="modal fade" id="editCommentModal" tabindex="-1" aria-labelledby="editCommentModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCommentModalLabel">Edit DGM Comment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="editCommentForm">
                    <input type="hidden" id="editCommentRegistrationId">
                    <div class="mb-3">
                        <label for="editCommentText" class="form-label">DGM Comment</label>
                        <textarea class="form-control" id="editCommentText" rows="4" 
                            placeholder="Enter your comment for this special approval request..."></textarea>
                        <small class="text-muted">Maximum 1000 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="saveDgmCommentBtn">Save Comment</button>
            </div>
        </div>
    </div>
</div>

<?php $__env->startPush('scripts'); ?>
<script>
// Success and Error Message Functions
function showSuccessMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const successDiv = document.createElement('div');
    successDiv.className = 'success-message';
    successDiv.innerHTML = `
        <i class="ti ti-check-circle success-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(successDiv);
    
    // Show the message
    setTimeout(() => successDiv.classList.add('show'), 100);
    
    // Hide after 4 seconds
    setTimeout(() => {
        successDiv.classList.remove('show');
        setTimeout(() => successDiv.remove(), 300);
    }, 4000);
}

function showErrorMessage(message) {
    // Remove any existing messages
    const existingMessages = document.querySelectorAll('.success-message, .error-message');
    existingMessages.forEach(msg => msg.remove());

    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.innerHTML = `
        <i class="ti ti-alert-circle error-icon"></i>
        ${message}
    `;
    
    document.body.appendChild(errorDiv);
    
    // Show the message
    setTimeout(() => errorDiv.classList.add('show'), 100);
    
    // Hide after 5 seconds
    setTimeout(() => {
        errorDiv.classList.remove('show');
        setTimeout(() => errorDiv.remove(), 300);
    }, 5000);
}

document.addEventListener('DOMContentLoaded', function() {
    const tableBody = document.getElementById('specialApprovalTableBody');
    const registerSection = document.getElementById('registerSection');
    const registerForm = document.getElementById('registerForm');
    const franchiseTableBody = document.getElementById('franchisePaymentTableBody');

    // Tab switching functionality
    const tabs = document.querySelectorAll('[data-bs-toggle="tab"]');
    tabs.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (event) {
            const targetTab = event.target.getAttribute('data-bs-target');
            console.log('Switched to tab:', targetTab);
            
            // Load data based on active tab
            if (targetTab === '#student-registration') {
                loadStudentRegistrationData();
            } else if (targetTab === '#franchise-payment') {
                loadFranchisePaymentData();
            }
        });
    });

    // Load student registration data
    function loadStudentRegistrationData() {
        console.log('Loading student registration data...');
        fetch('/get-special-approval-list')
            .then(response => response.json())
            .then(data => {
                if (data.success && data.students) {
                    renderSpecialApprovalTable(data.students);
                } else {
                    tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No students found.</td></tr>';
                }
            })
            .catch(error => {
                console.error('Error fetching special approval list:', error);
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">Error loading data.</td></tr>';
            });
    }

    // Load franchise payment data (placeholder for future implementation)
    function loadFranchisePaymentData() {
        console.log('Loading franchise payment data...');
        // This is a placeholder - you can implement the actual API call later
        franchiseTableBody.innerHTML = `
            <tr>
                <td colspan="10" class="text-center text-muted py-4">
                    <i class="ti ti-inbox" style="font-size: 2rem;"></i>
                    <p class="mt-2 mb-0">No franchise payment delay requests found</p>
                    <small class="text-muted">This feature will be implemented in future updates</small>
                </td>
            </tr>
        `;
    }

    // Handle registration form submission
    registerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(registerForm);
        
        // Get the course_id from the current student data
        const currentStudentData = window.currentStudentData;
        if (!currentStudentData || !currentStudentData.course_id) {
            showErrorMessage('Course information not available. Please try again.');
            return;
        }

        // Add course_id to form data
        formData.append('course_id', currentStudentData.course_id);

        fetch('/register-eligible-student', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccessMessage(data.message || 'Student registered successfully!');
                
                // Hide registration section after success
                setTimeout(() => {
                    registerSection.style.display = 'none';
                    window.location.reload(); // Reload to refresh the table
                }, 2000);
            } else {
                showErrorMessage(data.message || 'Registration failed.');
            }
        })
        .catch((error) => {
            console.error('Error:', error);
            showErrorMessage('Registration failed. Please try again.');
        });
    });

    // Render table with approve buttons for pending students
    function renderSpecialApprovalTable(students) {
        tableBody.innerHTML = '';
        students.forEach((student, index) => {
            let actionHtml = '';
            if (student.approval_status == 1) {
                actionHtml = '<span class="badge bg-success status-badge">Approved</span>';
            } else {
                // Ensure NIC is properly handled
                const nic = student.nic && student.nic !== 'N/A' ? student.nic : '';
                console.log('NIC for student:', student.student_id, 'is:', nic);
                
                actionHtml = `<button class="btn btn-success btn-sm approve-btn" 
                    data-student-id="${student.student_id}" 
                    data-student-nic="${nic}" 
                    data-student-name="${student.name || ''}" 
                    data-course-id="${student.course_id || ''}"
                    data-registration-number="${student.registration_number || ''}"
                    data-intake="${student.intake || ''}">Approve</button>`;
            }
            
            // Document display
            let documentHtml = '<span class="text-muted">No document</span>';
            if (student.document_path) {
                const fileName = student.document_path.split('/').pop();
                const downloadUrl = `/special-approval-document/${fileName}`;
                documentHtml = `<a href="${downloadUrl}" target="_blank" class="btn btn-outline-primary btn-sm">
                    <i class="ti ti-download"></i> View Document
                </a>`;
            }
            
            // Remarks display
            const remarks = student.remarks || 'No remarks';
            const remarksDisplay = remarks.length > 50 ? remarks.substring(0, 50) + '...' : remarks;
            
            // DGM Comment display
            const dgmComment = student.dgm_comment || 'No DGM comment';
            const dgmCommentDisplay = dgmComment.length > 50 ? dgmComment.substring(0, 50) + '...' : dgmComment;
            
            const row = `<tr>
                <td>${student.registration_number || ''}</td>
                <td>${student.name || ''}</td>
                <td>${student.course_name || ''}</td>
                <td>${documentHtml}</td>
                <td title="${remarks}">${remarksDisplay}</td>
                <td title="${dgmComment}">
                    <div class="d-flex align-items-center">
                        <span class="me-2">${dgmCommentDisplay}</span>
                        <button class="btn btn-sm btn-outline-primary edit-comment-btn" 
                            data-registration-id="${student.registration_id}" 
                            data-current-comment="${dgmComment}">
                            <i class="ti ti-edit"></i>
                        </button>
                    </div>
                </td>
                <td>${student.approval_status == 1 ? '<span class="badge bg-success status-badge">Approved</span>' : '<span class="badge bg-warning status-badge">Pending</span>'}</td>
                <td>${actionHtml}</td>
            </tr>`;
            tableBody.insertAdjacentHTML('beforeend', row);
        });
    }

    // Handle approve button clicks
    tableBody.addEventListener('click', function(e) {
        if (e.target.classList.contains('approve-btn')) {
            const studentId = e.target.getAttribute('data-student-id');
            const studentNic = e.target.getAttribute('data-student-nic');
            const studentName = e.target.getAttribute('data-student-name');
            const courseId = e.target.getAttribute('data-course-id');
            const registrationNumber = e.target.getAttribute('data-registration-number');
            const intake = e.target.getAttribute('data-intake');

            console.log('Approve button clicked with data:', {
                studentId,
                studentNic,
                studentName,
                courseId,
                registrationNumber,
                intake
            });

            // Store current student data for registration
            window.currentStudentData = {
                student_id: studentId,
                nic: studentNic,
                name: studentName,
                course_id: courseId,
                registration_number: registrationNumber,
                intake: intake
            };

            // Show registration section and populate fields
            populateRegistrationForm(studentNic, registrationNumber, courseId, intake);
            registerSection.style.display = 'block';
            
            // Scroll to registration section
            registerSection.scrollIntoView({ behavior: 'smooth' });
        }
        
        // Handle edit comment button clicks
        if (e.target.closest('.edit-comment-btn')) {
            const editBtn = e.target.closest('.edit-comment-btn');
            const registrationId = editBtn.getAttribute('data-registration-id');
            const currentComment = editBtn.getAttribute('data-current-comment');
            
            // Populate modal
            document.getElementById('editCommentRegistrationId').value = registrationId;
            document.getElementById('editCommentText').value = currentComment === 'No DGM comment' ? '' : currentComment;
            
            // Show modal
            const modal = new bootstrap.Modal(document.getElementById('editCommentModal'));
            modal.show();
        }
    });

    // Function to populate registration form
    function populateRegistrationForm(nic, registrationNumber, courseId, intake) {
        console.log('Populating form with:', { nic, registrationNumber, courseId, intake });
        
        // Populate basic fields
        document.getElementById('inlineStudentNIC').value = nic || '';
        document.getElementById('inlineStudentRegNo').value = registrationNumber || '';
        document.getElementById('intake').value = intake || '2025-September';
        
        console.log('Form fields after population:', {
            nic: document.getElementById('inlineStudentNIC').value,
            registrationNumber: document.getElementById('inlineStudentRegNo').value,
            intake: document.getElementById('intake').value
        });
        
        // Generate course registration ID
        generateCourseRegistrationId(courseId);
    }

    // Function to generate course registration ID
    function generateCourseRegistrationId(courseId) {
        // Get the intake_id from the form
        const intakeElement = document.getElementById('intake');
        const intakeValue = intakeElement ? intakeElement.value : '';
        
        // Extract intake_id from intake value (e.g., "2025-July" -> intake_id 3)
        let intakeId = 3; // Default to intake 3 for 2025-July
        
        if (intakeValue === '2025-July') {
            intakeId = 3;
        } else if (intakeValue === '2025-August') {
            intakeId = 2;
        } else if (intakeValue === '2025-September') {
            intakeId = 1;
        }
        
        console.log('DEBUG: Using intake_id for course registration ID:', intakeId);
        
        fetch(`/get-next-course-registration-id?intake_id=${intakeId}`, {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            }
        })
        .then(response => response.json())
        .then(data => {
            console.log('Course registration ID response:', data);
            if (data.success) {
                document.getElementById('inlineCourseRegId').value = data.next_id;
                console.log('Set course registration ID to:', data.next_id);
            } else {
                // Fallback registration ID
                document.getElementById('inlineCourseRegId').value = '2025/HND/SE/001';
                console.log('Failed to get course registration ID:', data.message || 'Unknown error');
            }
        })
        .catch(error => {
            console.error('Error generating registration ID:', error);
            // Fallback registration ID
            document.getElementById('inlineCourseRegId').value = '2025/HND/SE/001';
        });
    }

    // Handle DGM comment form submission
    document.getElementById('saveDgmCommentBtn').addEventListener('click', function() {
        const registrationId = document.getElementById('editCommentRegistrationId').value;
        const comment = document.getElementById('editCommentText').value;
        
        if (!registrationId) {
            showErrorMessage('Registration ID is required.');
            return;
        }
        
        // Show loading state
        const saveBtn = this;
        saveBtn.disabled = true;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        
        fetch('/update-dgm-comment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: JSON.stringify({
                registration_id: registrationId,
                dgm_comment: comment
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('editCommentModal'));
                modal.hide();
                
                // Refresh the table to show updated comment
                location.reload();
            } else {
                showErrorMessage(data.message || 'Failed to update comment.');
            }
        })
        .catch(error => {
            console.error('Error updating comment:', error);
            showErrorMessage('An error occurred while updating the comment.');
        })
        .finally(() => {
            // Reset button state
            saveBtn.disabled = false;
            saveBtn.innerHTML = 'Save Comment';
        });
    });

    // Load initial data for student registration tab
    loadStudentRegistrationData();
});
</script>
<?php $__env->stopPush(); ?>
<?php $__env->stopSection(); ?> 
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH C:\Users\thisali\Desktop\Github\Nebula-Project\Code\resources\views/Special_approval_list.blade.php ENDPATH**/ ?>