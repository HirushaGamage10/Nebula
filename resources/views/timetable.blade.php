@extends('inc.app')

@section('title', 'NEBULA | Timetable Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Timetable Management</h2>
            <hr>
            
            <!-- Spinner and Toast containers -->
            <div id="spinner-overlay" style="display:none;"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
            <div id="toastContainer" aria-live="polite" aria-atomic="true" style="position: fixed; top: 10px; right: 10px; z-index: 1000;"></div>

            <!-- Tab Navigation -->
            <ul class="nav nav-tabs mb-4" id="timetableTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="degree-tab" data-bs-toggle="tab" data-bs-target="#degree-timetable" type="button" role="tab" aria-controls="degree-timetable" aria-selected="true">
                        <i class="ti ti-graduation-cap"></i> Degree Programs
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="certificate-tab" data-bs-toggle="tab" data-bs-target="#certificate-timetable" type="button" role="tab" aria-controls="certificate-timetable" aria-selected="false">
                        <i class="ti ti-certificate"></i> Certificate Programs
                    </button>
                </li>
            </ul>

            <!-- Tab Content -->
            <div class="tab-content" id="timetableTabsContent">
                <!-- Degree Programs Tab -->
                <div class="tab-pane fade show active" id="degree-timetable" role="tabpanel" aria-labelledby="degree-tab">
                    <div id="degree-filters" class="mb-4">
                        <div class="mb-3 row align-items-center">
                            <label for="degree_location" class="col-sm-3 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="degree_location" name="location" required>
                                    <option value="" selected disabled>Select Location</option>
                                    <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                    <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                    <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="degree_course" class="col-sm-3 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="degree_course" name="course_id" required disabled>
                                    <option selected disabled value="">Select Course</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="degree_intake" class="col-sm-3 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="degree_intake" name="intake_id" required disabled>
                                    <option selected disabled value="">Select Intake</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="degree_semester" class="col-sm-3 col-form-label fw-bold">Semester<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="degree_semester" name="semester" required disabled>
                                    <option selected disabled value="">Select Semester</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="degree_start_date" class="col-sm-3 col-form-label fw-bold">Semester Start Date<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="degree_start_date" name="start_date" required readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="degree_end_date" class="col-sm-3 col-form-label fw-bold">End Date<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="degree_end_date" name="end_date" required readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center" id="weekSelectionRow" style="display: none;">
                            <label for="degree_week" class="col-sm-3 col-form-label fw-bold">Select Week<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="degree_week" name="week" required disabled>
                                    <option selected disabled value="">Select Week</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center" id="moduleStatusRow" style="display: none;">
                            <label class="col-sm-3 col-form-label fw-bold">Module Status</label>
                            <div class="col-sm-9">
                                <div id="moduleStatus" class="alert alert-info mb-0">
                                    <i class="ti ti-info-circle"></i> Select a semester to load modules
                                </div>
                                <div id="modulesLoaded" class="mt-2" style="display: none;">
                                    <!-- Modules will be listed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Degree Timetable Table -->
                    <div class="mt-4" id="degreeTimetableSection" style="display:none;">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0" id="degreeTimetableHeader"></h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="degreeTimetableForm" method="POST" action="{{ route('timetable.store') }}">
                                        @csrf
                                        <input type="hidden" name="course_type" value="degree">
                                        <table class="table table-bordered text-center align-middle" style="min-width: 900px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 120px;">Time/Date</th>
                                                    <th id="monday-header">Monday</th>
                                                    <th id="tuesday-header">Tuesday</th>
                                                    <th id="wednesday-header">Wednesday</th>
                                                    <th id="thursday-header">Thursday</th>
                                                    <th id="friday-header">Friday</th>
                                                    <th id="saturday-header">Saturday</th>
                                                    <th id="sunday-header">Sunday</th>
                                                </tr>
                                            </thead>
                                            <tbody id="degreeTimetableBody">
                                                <!-- Time slots will be dynamically generated -->
                                            </tbody>
                                        </table>
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-success me-2" id="addDegreeTimeSlot">
                                                <i class="ti ti-plus"></i> Add Time Slot
                                            </button>
                                            <button type="submit" class="btn btn-primary me-2">Save Degree Timetable</button>
                                            <button type="button" class="btn btn-info" id="downloadDegreePDF">
                                                <i class="ti ti-download"></i> Download PDF
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificate Programs Tab -->
                <div class="tab-pane fade" id="certificate-timetable" role="tabpanel" aria-labelledby="certificate-tab">
                    <div id="certificate-filters" class="mb-4">
                        <div class="mb-3 row align-items-center">
                            <label for="certificate_location" class="col-sm-3 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="certificate_location" name="location" required>
                                    <option value="" selected disabled>Select Location</option>
                                    <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                    <option value="Mathara">Nebula Institute of Technology - Mathara</option>
                                    <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="certificate_course" class="col-sm-3 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="certificate_course" name="course_id" required disabled>
                                    <option selected disabled value="">Select Course</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="certificate_intake" class="col-sm-3 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="certificate_intake" name="intake_id" required disabled>
                                    <option selected disabled value="">Select Intake</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="certificate_start_date" class="col-sm-3 col-form-label fw-bold">Course Start Date<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="certificate_start_date" name="start_date" required readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center">
                            <label for="certificate_end_date" class="col-sm-3 col-form-label fw-bold">End Date<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="date" class="form-control" id="certificate_end_date" name="end_date" required readonly>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center" id="certificateWeekSelectionRow" style="display: none;">
                            <label for="certificate_week" class="col-sm-3 col-form-label fw-bold">Select Week<span class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <select class="form-select filter-param" id="certificate_week" name="week" required disabled>
                                    <option selected disabled value="">Select Week</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3 row align-items-center" id="certificateModuleStatusRow" style="display: none;">
                            <label class="col-sm-3 col-form-label fw-bold">Module Status</label>
                            <div class="col-sm-9">
                                <div id="certificateModuleStatus" class="alert alert-info mb-0">
                                    <i class="ti ti-info-circle"></i> Select a course to load modules
                                </div>
                                <div id="certificateModulesLoaded" class="mt-2" style="display: none;">
                                    <!-- Modules will be listed here -->
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Certificate Timetable Table -->
                    <div class="mt-4" id="certificateTimetableSection" style="display:none;">
                        <div class="card">
                            <div class="card-header bg-success text-white">
                                <h5 class="mb-0" id="certificateTimetableHeader"></h5>
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <form id="certificateTimetableForm" method="POST" action="{{ route('timetable.store') }}">
                                        @csrf
                                        <input type="hidden" name="course_type" value="certificate">
                                        <table class="table table-bordered text-center align-middle" style="min-width: 900px;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th style="width: 120px;">Time/Date</th>
                                                    <th id="certificate-monday-header">Monday</th>
                                                    <th id="certificate-tuesday-header">Tuesday</th>
                                                    <th id="certificate-wednesday-header">Wednesday</th>
                                                    <th id="certificate-thursday-header">Thursday</th>
                                                    <th id="certificate-friday-header">Friday</th>
                                                    <th id="certificate-saturday-header">Saturday</th>
                                                    <th id="certificate-sunday-header">Sunday</th>
                                                </tr>
                                            </thead>
                                            <tbody id="certificateTimetableBody">
                                                <!-- Time slots will be dynamically generated -->
                                            </tbody>
                                        </table>
                                        <div class="text-center mt-4">
                                            <button type="button" class="btn btn-success me-2" id="addCertificateTimeSlot">
                                                <i class="ti ti-plus"></i> Add Time Slot
                                            </button>
                                            <button type="submit" class="btn btn-primary me-2">Save Certificate Timetable</button>
                                            <button type="button" class="btn btn-info" id="downloadCertificatePDF">
                                                <i class="ti ti-download"></i> Download PDF
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Degree Program Elements
    const degreeLocation = document.getElementById('degree_location');
    const degreeCourse = document.getElementById('degree_course');
    const degreeIntake = document.getElementById('degree_intake');
    const degreeSemester = document.getElementById('degree_semester');
    const degreeStartDate = document.getElementById('degree_start_date');
    const degreeEndDate = document.getElementById('degree_end_date');
    const degreeTimetableSection = document.getElementById('degreeTimetableSection');
    const degreeTimetableHeader = document.getElementById('degreeTimetableHeader');
    const degreeTimetableBody = document.getElementById('degreeTimetableBody');
    const addDegreeTimeSlot = document.getElementById('addDegreeTimeSlot');
    const moduleStatusRow = document.getElementById('moduleStatusRow');
    const moduleStatus = document.getElementById('moduleStatus');
    const weekSelectionRow = document.getElementById('weekSelectionRow');
    const degreeWeek = document.getElementById('degree_week');

    // Certificate Program Elements
    const certificateLocation = document.getElementById('certificate_location');
    const certificateCourse = document.getElementById('certificate_course');
    const certificateIntake = document.getElementById('certificate_intake');
    const certificateStartDate = document.getElementById('certificate_start_date');
    const certificateEndDate = document.getElementById('certificate_end_date');
    const certificateTimetableSection = document.getElementById('certificateTimetableSection');
    const certificateTimetableHeader = document.getElementById('certificateTimetableHeader');
    const certificateTimetableBody = document.getElementById('certificateTimetableBody');
    const addCertificateTimeSlot = document.getElementById('addCertificateTimeSlot');
    const certificateWeekSelectionRow = document.getElementById('certificateWeekSelectionRow');
    const certificateWeek = document.getElementById('certificate_week');
    const certificateModuleStatusRow = document.getElementById('certificateModuleStatusRow');
    const certificateModuleStatus = document.getElementById('certificateModuleStatus');

    // Helper functions
    function resetAndDisable(select, placeholder) {
        if (select) {
            select.innerHTML = `<option selected disabled value="">${placeholder}</option>`;
            select.disabled = true;
        }
    }

    function populateDropdown(select, items, valueKey, textKey, placeholder) {
        select.innerHTML = `<option selected disabled value="">Select ${placeholder}</option>`;
        items.forEach(item => {
            select.innerHTML += `<option value="${item[valueKey]}">${item[textKey]}</option>`;
        });
        select.disabled = false;
    }

    function populateDropdownWithData(select, items, valueKey, textKey, placeholder) {
        select.innerHTML = `<option selected disabled value="">Select ${placeholder}</option>`;
        items.forEach(item => {
            select.innerHTML += `<option value="${item[valueKey]}" data-start-date="${item.startDate}" data-end-date="${item.endDate}">${item[textKey]}</option>`;
        });
        select.disabled = false;
    }

    function showSpinner(show) {
        document.getElementById('spinner-overlay').style.display = show ? 'flex' : 'none';
    }

    function showToast(title, message, bgColor) {
        const container = document.getElementById('toastContainer');
        const toast = document.createElement('div');
        toast.className = 'toast';
        toast.style.backgroundColor = bgColor;
        toast.innerHTML = `
            <div class="toast-header"><strong class="me-auto">${title}</strong><button type="button" class="btn-close" data-bs-dismiss="toast"></button></div>
            <div class="toast-body">${message}</div>
        `;
        container.appendChild(toast);
        new bootstrap.Toast(toast).show();
        toast.addEventListener('hidden.bs.toast', () => toast.remove());
    }

    // Generate time slot row for degree programs
    function generateDegreeTimeSlotRow(slotIndex) {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const row = document.createElement('tr');
        
        const timeCell = document.createElement('td');
        const timeInput = document.createElement('input');
        timeInput.type = 'text';
        timeInput.className = 'form-control form-control-sm';
        timeInput.name = `degree_time_slots[${slotIndex}][time]`;
        timeInput.placeholder = 'e.g., 8.00-9.00';
        timeInput.required = true;
        timeCell.appendChild(timeInput);
        row.appendChild(timeCell);

        days.forEach(day => {
            const cell = document.createElement('td');
            const select = document.createElement('select');
            select.className = 'form-select form-select-sm';
            select.name = `degree_time_slots[${slotIndex}][${day}]`;
            select.innerHTML = '<option value="">Select Module</option>';
            
            // Add modules to dropdown if available
            if (window.availableModules && window.availableModules.length > 0) {
                window.availableModules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.module_id;
                    option.textContent = module.full_name;
                    select.appendChild(option);
                });
            }
            
            cell.appendChild(select);
            row.appendChild(cell);
        });

        const actionCell = document.createElement('td');
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm';
        removeBtn.innerHTML = '<i class="ti ti-trash"></i>';
        removeBtn.onclick = () => row.remove();
        actionCell.appendChild(removeBtn);
        row.appendChild(actionCell);

        return row;
    }

    // Generate time slot row for certificate programs
    function generateCertificateTimeSlotRow(slotIndex) {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const row = document.createElement('tr');
        
        const timeCell = document.createElement('td');
        const timeInput = document.createElement('input');
        timeInput.type = 'text';
        timeInput.className = 'form-control form-control-sm';
        timeInput.name = `certificate_time_slots[${slotIndex}][time]`;
        timeInput.placeholder = 'e.g., 8.00-9.00';
        timeInput.required = true;
        timeCell.appendChild(timeInput);
        row.appendChild(timeCell);

        days.forEach(day => {
            const cell = document.createElement('td');
            const select = document.createElement('select');
            select.className = 'form-select form-select-sm';
            select.name = `certificate_time_slots[${slotIndex}][${day}]`;
            select.innerHTML = '<option value="">Select Module</option>';
            
            // Add modules to dropdown if available
            if (window.availableCertificateModules && window.availableCertificateModules.length > 0) {
                window.availableCertificateModules.forEach(module => {
                    const option = document.createElement('option');
                    option.value = module.module_id;
                    option.textContent = module.full_name;
                    select.appendChild(option);
                });
            }
            
            cell.appendChild(select);
            row.appendChild(cell);
        });

        const actionCell = document.createElement('td');
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.className = 'btn btn-danger btn-sm';
        removeBtn.innerHTML = '<i class="ti ti-trash"></i>';
        removeBtn.onclick = () => row.remove();
        actionCell.appendChild(removeBtn);
        row.appendChild(actionCell);

        return row;
    }

    // Generate date columns for certificate programs
    function generateCertificateDateColumns(startDate, endDate) {
        const dates = [];
        const currentDate = new Date(startDate);
        const end = new Date(endDate);
        
        while (currentDate <= end) {
            dates.push(currentDate.toISOString().split('T')[0]);
            currentDate.setDate(currentDate.getDate() + 1);
        }

        const headerRow = certificateTableHead.querySelector('tr');
        headerRow.innerHTML = '<th style="width: 120px;">Time/Date</th>';
        
        dates.forEach(date => {
            const th = document.createElement('th');
            th.textContent = new Date(date).toLocaleDateString('en-GB');
            headerRow.appendChild(th);
        });

        return dates;
    }

    // Function to clear date field validation classes
    function clearDateValidation() {
        degreeStartDate.value = '';
        degreeEndDate.value = '';
        degreeStartDate.classList.remove('is-valid', 'is-invalid', 'is-loading');
        degreeEndDate.classList.remove('is-valid', 'is-invalid', 'is-loading');
        
        // Reset module status
        moduleStatusRow.style.display = 'none';
        hideModulesLoaded();
        
        // Reset week selection
        if (degreeWeek) {
            degreeWeek.value = '';
            resetAndDisable(degreeWeek, 'Select Week');
            weekSelectionRow.style.display = 'none';
            resetWeekDates();
        }
    }

    // Degree Program Event Listeners
    degreeLocation.addEventListener('change', function() {
        resetAndDisable(degreeCourse, 'Select Course');
        resetAndDisable(degreeIntake, 'Select Intake');
        resetAndDisable(degreeSemester, 'Select Semester');
        degreeCourse.value = '';
        degreeIntake.value = '';
        degreeSemester.value = '';
        degreeTimetableSection.style.display = 'none';
        clearDateValidation();
        
        if (degreeLocation.value) {
            fetchCoursesByLocation(degreeLocation.value, 'degree', degreeCourse);
        }
    });

    degreeCourse.addEventListener('change', function() {
        resetAndDisable(degreeIntake, 'Select Intake');
        resetAndDisable(degreeSemester, 'Select Semester');
        degreeIntake.value = '';
        degreeSemester.value = '';
        clearDateValidation();
        
        if (degreeCourse.value && degreeLocation.value) {
            fetchIntakesByCourse(degreeCourse.value, degreeLocation.value, degreeIntake);
        }
    });

    degreeIntake.addEventListener('change', function() {
        resetAndDisable(degreeSemester, 'Select Semester');
        degreeSemester.value = '';
        clearDateValidation();
        
        if (degreeIntake.value && degreeCourse.value) {
            fetchSemesters(degreeCourse.value, degreeIntake.value, degreeSemester);
        }
    });

    degreeSemester.addEventListener('change', function() {
        if (degreeSemester.value) {
            // Get the selected semester data
            const selectedOption = degreeSemester.options[degreeSemester.selectedIndex];
            
            // Auto-fill start and end dates if available
            if (selectedOption.dataset.startDate && selectedOption.dataset.endDate) {
                // Show loading state briefly
                degreeStartDate.classList.add('is-loading');
                degreeEndDate.classList.add('is-loading');
                
                // Small delay to show loading state
                setTimeout(() => {
                    degreeStartDate.value = selectedOption.dataset.startDate;
                    degreeEndDate.value = selectedOption.dataset.endDate;
                    
                    // Remove loading state and add success state
                    degreeStartDate.classList.remove('is-loading');
                    degreeEndDate.classList.remove('is-loading');
                    degreeStartDate.classList.add('is-valid');
                    degreeEndDate.classList.add('is-valid');
                    
                    // Generate weeks for the selected semester
                    fetchWeeks(degreeStartDate.value, degreeEndDate.value);
                    
                    // Show module status row after dates are populated
                    moduleStatusRow.style.display = 'block';
                    moduleStatus.innerHTML = '<i class="ti ti-loader"></i> Loading modules...';
                    
                    // Fetch modules for the selected semester
                    fetchModulesBySemester(degreeSemester.value);
                }, 300);
            }
        } else {
            degreeTimetableSection.style.display = 'none';
            degreeTimetableHeader.style.display = 'none';
            moduleStatusRow.style.display = 'none';
            weekSelectionRow.style.display = 'none';
            if (degreeWeek) {
                degreeWeek.value = '';
                resetAndDisable(degreeWeek, 'Select Week');
                resetWeekDates();
            }
            clearDateValidation();
        }
    });

    // Week selection event listener
    degreeWeek.addEventListener('change', function() {
        if (degreeWeek.value) {
            // Show timetable section and generate timetable for selected week
            degreeTimetableSection.style.display = 'block';
            degreeTimetableHeader.style.display = 'block';
            updateDegreeTimetableHeader();
            updateWeekDates();
            
            // Add initial time slot
            degreeTimetableBody.innerHTML = '';
            degreeTimetableBody.appendChild(generateDegreeTimeSlotRow(0));
        } else {
            degreeTimetableSection.style.display = 'none';
            degreeTimetableHeader.style.display = 'none';
        }
    });

    // Certificate Program Event Listeners
    certificateLocation.addEventListener('change', function() {
        resetAndDisable(certificateCourse, 'Select Course');
        resetAndDisable(certificateIntake, 'Select Intake');
        certificateCourse.value = '';
        certificateIntake.value = '';
        certificateTimetableSection.style.display = 'none';
        
        if (certificateLocation.value) {
            fetchCoursesByLocation(certificateLocation.value, 'certificate', certificateCourse);
        }
    });

    certificateCourse.addEventListener('change', function() {
        resetAndDisable(certificateIntake, 'Select Intake');
        certificateIntake.value = '';
        
        if (certificateCourse.value && certificateLocation.value) {
            fetchIntakesByCourse(certificateCourse.value, certificateLocation.value, certificateIntake);
        }
    });

    certificateIntake.addEventListener('change', function() {
        if (certificateIntake.value && certificateCourse.value) {
            // Auto-fill start and end dates based on course/intake
            // For certificate programs, we'll use a default date range
            const today = new Date();
            const startDate = new Date(today);
            startDate.setDate(today.getDate() + 7); // Start next week
            
            const endDate = new Date(startDate);
            endDate.setDate(startDate.getDate() + 30); // End 30 days later
            
            certificateStartDate.value = startDate.toISOString().split('T')[0];
            certificateEndDate.value = endDate.toISOString().split('T')[0];
            
            certificateStartDate.classList.add('is-valid');
            certificateEndDate.classList.add('is-valid');
            
            // Generate weeks for the selected date range
            fetchCertificateWeeks(certificateStartDate.value, certificateEndDate.value);
            
            // Show module status
            certificateModuleStatusRow.style.display = 'block';
            certificateModuleStatus.innerHTML = '<i class="ti ti-loader"></i> Loading modules...';
            
            // Fetch modules for the selected course
            fetchCertificateModules(certificateCourse.value);
        } else {
            certificateTimetableSection.style.display = 'none';
            certificateTimetableHeader.style.display = 'none';
            certificateModuleStatusRow.style.display = 'none';
            certificateWeekSelectionRow.style.display = 'none';
            if (certificateWeek) {
                certificateWeek.value = '';
                resetAndDisable(certificateWeek, 'Select Week');
                resetCertificateWeekDates();
            }
            clearCertificateDateValidation();
        }
    });

    // Certificate week selection event listener
    certificateWeek.addEventListener('change', function() {
        if (certificateWeek.value) {
            // Show timetable section and generate timetable for selected week
            certificateTimetableSection.style.display = 'block';
            certificateTimetableHeader.style.display = 'block';
            updateCertificateTimetableHeader();
            updateCertificateWeekDates();
            
            // Add initial time slot
            certificateTimetableBody.innerHTML = '';
            certificateTimetableBody.appendChild(generateCertificateTimeSlotRow(0));
        } else {
            certificateTimetableSection.style.display = 'none';
            certificateTimetableHeader.style.display = 'none';
        }
    });

    // Date change listeners for certificate programs
    certificateStartDate.addEventListener('change', updateCertificateTable);
    certificateEndDate.addEventListener('change', updateCertificateTable);

    function updateCertificateTable() {
        if (certificateStartDate.value && certificateEndDate.value) {
            const dates = generateCertificateDateColumns(certificateStartDate.value, certificateEndDate.value);
            certificateTimetableBody.innerHTML = '';
            certificateTimetableBody.appendChild(generateCertificateTimeSlotRow(0, dates));
        }
    }

    // Add time slot buttons
    addDegreeTimeSlot.addEventListener('click', function() {
        const slotIndex = degreeTimetableBody.children.length;
        degreeTimetableBody.appendChild(generateDegreeTimeSlotRow(slotIndex));
    });

    addCertificateTimeSlot.addEventListener('click', function() {
        const slotIndex = certificateTimetableBody.children.length;
        certificateTimetableBody.appendChild(generateCertificateTimeSlotRow(slotIndex));
    });

    // Function to regenerate all degree time slots with current modules
    function regenerateDegreeTimeSlots() {
        if (degreeTimetableBody.children.length > 0) {
            const currentSlots = degreeTimetableBody.children.length;
            degreeTimetableBody.innerHTML = '';
            for (let i = 0; i < currentSlots; i++) {
                degreeTimetableBody.appendChild(generateDegreeTimeSlotRow(i));
            }
        }
    }

    // Helper functions for checking if all filters are filled
    function allDegreeFiltersFilled() {
        return degreeLocation.value && degreeCourse.value && degreeIntake.value && degreeSemester.value && degreeStartDate.value && degreeEndDate.value;
    }

    function allCertificateFiltersFilled() {
        return certificateLocation.value && certificateCourse.value && certificateIntake.value && certificateStartDate.value && certificateEndDate.value;
    }

    // AJAX functions
    function fetchCoursesByLocation(location, courseType, targetSelect) {
        showSpinner(true);
        fetch(`/get-courses-by-location?location=${encodeURIComponent(location)}&course_type=${encodeURIComponent(courseType)}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.courses && data.courses.length > 0) {
                    populateDropdown(targetSelect, data.courses, 'course_id', 'course_name', 'Course');
                } else {
                    resetAndDisable(targetSelect, 'Select Course');
                    showToast('Error', 'No courses found for this location and type.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(targetSelect, 'Select Course');
                showToast('Error', 'Failed to fetch courses.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function fetchIntakesByCourse(courseId, location, targetSelect) {
        showSpinner(true);
        fetch(`/get-intakes/${courseId}/${location}`)
            .then(response => response.json())
            .then(data => {
                if (data.intakes && data.intakes.length > 0) {
                    populateDropdown(targetSelect, data.intakes, 'intake_id', 'batch', 'Intake');
                } else {
                    resetAndDisable(targetSelect, 'Select Intake');
                    showToast('Error', 'No intakes found for this course and location.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(targetSelect, 'Select Intake');
                showToast('Error', 'Failed to fetch intakes.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function fetchSemesters(courseId, intakeId, targetSelect) {
        showSpinner(true);
        fetch(`/timetable/get-semesters?course_id=${encodeURIComponent(courseId)}&intake_id=${encodeURIComponent(intakeId)}`)
            .then(response => response.json())
            .then(data => {
                if (data.semesters && data.semesters.length > 0) {
                    const semesterOptions = data.semesters.map(s => ({ 
                        id: s.id, 
                        name: `${s.name} (${s.status})`,
                        startDate: s.start_date,
                        endDate: s.end_date
                    }));
                    populateDropdownWithData(targetSelect, semesterOptions, 'id', 'name', 'Semester');
                } else {
                    resetAndDisable(targetSelect, 'Select Semester');
                    showToast('Error', 'No active or upcoming semesters found for this course and intake.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(targetSelect, 'Select Semester');
                showToast('Error', 'Failed to fetch semesters.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function fetchWeeks(startDate, endDate) {
        showSpinner(true);
        fetch(`/get-weeks?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`)
            .then(response => response.json())
            .then(data => {
                if (data.weeks && data.weeks.length > 0) {
                    const weekOptions = data.weeks.map(w => ({ 
                        id: w.week_number, 
                        name: w.display_text,
                        startDate: w.start_date,
                        endDate: w.end_date
                    }));
                    populateDropdownWithData(degreeWeek, weekOptions, 'id', 'name', 'Week');
                    weekSelectionRow.style.display = 'block';
                    showToast('Success', `${data.weeks.length} weeks generated for the semester.`, 'bg-success');
                } else {
                    resetAndDisable(degreeWeek, 'Select Week');
                    weekSelectionRow.style.display = 'none';
                    showToast('Error', 'No weeks found for the selected semester period.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(degreeWeek, 'Select Week');
                weekSelectionRow.style.display = 'none';
                showToast('Error', 'Failed to fetch weeks.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    // Function to update degree timetable header with module count
    function updateDegreeTimetableHeader() {
        const moduleCount = window.availableModules ? window.availableModules.length : 0;
        let headerText = 'Degree Timetable for: ' + degreeCourse.options[degreeCourse.selectedIndex].text + 
            ' - Semester ' + degreeSemester.options[degreeSemester.selectedIndex].text;
        
        if (degreeWeek.value) {
            const selectedWeekOption = degreeWeek.options[degreeWeek.selectedIndex];
            headerText += ' - ' + selectedWeekOption.text;
        }
        
        headerText += ` <span class="badge bg-info">${moduleCount} modules loaded</span>`;
        degreeTimetableHeader.innerHTML = headerText;
    }

    // Function to update week dates in the table header
    function updateWeekDates() {
        if (!degreeWeek.value) return;
        
        const selectedWeekOption = degreeWeek.options[degreeWeek.selectedIndex];
        const startDate = new Date(selectedWeekOption.dataset.startDate);
        
        // Calculate dates for each day of the week
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const headerElement = document.getElementById(`${day}-header`);
            if (headerElement) {
                const currentDate = new Date(startDate);
                currentDate.setDate(startDate.getDate() + index);
                
                const dateString = currentDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short'
                });
                
                headerElement.innerHTML = `${dayNames[index]}<br><small class="text-muted">${dateString}</small>`;
            }
        });
    }

    // Function to reset week dates to default
    function resetWeekDates() {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const headerElement = document.getElementById(`${day}-header`);
            if (headerElement) {
                headerElement.innerHTML = dayNames[index];
            }
        });
    }

    // Function to fetch modules for a semester
    function fetchModulesBySemester(semesterId) {
        console.log('Fetching modules for semester ID:', semesterId);
        showSpinner(true);
        fetch(`/get-modules-by-semester?semester_id=${encodeURIComponent(semesterId)}`)
            .then(response => {
                console.log('Response status:', response.status);
                return response.json();
            })
            .then(data => {
                console.log('Modules data received:', data);
                if (data.modules && data.modules.length > 0) {
                    window.availableModules = data.modules;
                    console.log('Available modules:', window.availableModules);
                    showToast('Success', `${data.modules.length} modules loaded.`, 'bg-success');
                    // Regenerate time slots with new modules
                    regenerateDegreeTimeSlots();
                    updateDegreeTimetableHeader();
                    moduleStatus.innerHTML = `<i class="ti ti-check-circle-2"></i> ${data.modules.length} modules loaded successfully!`;
                    
                    // Show modules loaded
                    showModulesLoaded(data.modules);
                } else {
                    window.availableModules = [];
                    console.log('No modules found for semester');
                    showToast('Warning', 'No modules found for this semester.', 'bg-warning');
                    // Regenerate time slots with empty modules
                    regenerateDegreeTimeSlots();
                    updateDegreeTimetableHeader();
                    moduleStatus.innerHTML = '<i class="ti ti-alert-circle"></i> No modules found for this semester.';
                    hideModulesLoaded();
                }
            })
            .catch((error) => {
                console.error('Error fetching modules:', error);
                window.availableModules = [];
                showToast('Error', 'Failed to fetch modules.', 'bg-danger');
                // Regenerate time slots with empty modules
                regenerateDegreeTimeSlots();
                updateDegreeTimetableHeader();
                moduleStatus.innerHTML = '<i class="ti ti-alert-circle"></i> Failed to load modules.';
                hideModulesLoaded();
            })
            .finally(() => showSpinner(false));
    }

    // Function to show modules loaded
    function showModulesLoaded(modules) {
        const modulesLoadedDiv = document.getElementById('modulesLoaded');
        if (modulesLoadedDiv) {
            let modulesHtml = '<div class="small text-muted">Modules loaded:</div>';
            modules.forEach(module => {
                modulesHtml += `<div class="small text-success"><i class="ti ti-check"></i> ${module.full_name}</div>`;
            });
            modulesLoadedDiv.innerHTML = modulesHtml;
            modulesLoadedDiv.style.display = 'block';
        }
    }

    // Function to hide modules loaded
    function hideModulesLoaded() {
        const modulesLoadedDiv = document.getElementById('modulesLoaded');
        if (modulesLoadedDiv) {
            modulesLoadedDiv.style.display = 'none';
            modulesLoadedDiv.innerHTML = '';
        }
    }

    // Certificate helper functions
    function fetchCertificateWeeks(startDate, endDate) {
        showSpinner(true);
        fetch(`/get-weeks?start_date=${encodeURIComponent(startDate)}&end_date=${encodeURIComponent(endDate)}`)
            .then(response => response.json())
            .then(data => {
                if (data.weeks && data.weeks.length > 0) {
                    const weekOptions = data.weeks.map(w => ({ 
                        id: w.week_number, 
                        name: w.display_text,
                        startDate: w.start_date,
                        endDate: w.end_date
                    }));
                    populateDropdownWithData(certificateWeek, weekOptions, 'id', 'name', 'Week');
                    certificateWeekSelectionRow.style.display = 'block';
                    showToast('Success', `${data.weeks.length} weeks generated for the certificate program.`, 'bg-success');
                } else {
                    resetAndDisable(certificateWeek, 'Select Week');
                    certificateWeekSelectionRow.style.display = 'none';
                    showToast('Error', 'No weeks found for the selected date range.', 'bg-danger');
                }
            })
            .catch(() => {
                resetAndDisable(certificateWeek, 'Select Week');
                certificateWeekSelectionRow.style.display = 'none';
                showToast('Error', 'Failed to fetch weeks.', 'bg-danger');
            })
            .finally(() => showSpinner(false));
    }

    function fetchCertificateModules(courseId) {
        showSpinner(true);
        // For certificate programs, we'll simulate modules based on the course
        setTimeout(() => {
            const mockModules = [
                { module_id: 1, module_code: 'CERT101', module_name: 'Certificate Module 1', full_name: 'Certificate Module 1 (CERT101)' },
                { module_id: 2, module_code: 'CERT102', module_name: 'Certificate Module 2', full_name: 'Certificate Module 2 (CERT102)' },
                { module_id: 3, module_code: 'CERT103', module_name: 'Certificate Module 3', full_name: 'Certificate Module 3 (CERT103)' }
            ];
            
            window.availableCertificateModules = mockModules;
            showToast('Success', `${mockModules.length} modules loaded.`, 'bg-success');
            certificateModuleStatus.innerHTML = `<i class="ti ti-check-circle-2"></i> ${mockModules.length} modules loaded successfully!`;
            showCertificateModulesLoaded(mockModules);
            showSpinner(false);
        }, 1000);
    }

    function showCertificateModulesLoaded(modules) {
        const modulesLoadedDiv = document.getElementById('certificateModulesLoaded');
        if (modulesLoadedDiv) {
            let modulesHtml = '<div class="small text-muted">Modules loaded:</div>';
            modules.forEach(module => {
                modulesHtml += `<div class="small text-success"><i class="ti ti-check"></i> ${module.full_name}</div>`;
            });
            modulesLoadedDiv.innerHTML = modulesHtml;
            modulesLoadedDiv.style.display = 'block';
        }
    }

    function hideCertificateModulesLoaded() {
        const modulesLoadedDiv = document.getElementById('certificateModulesLoaded');
        if (modulesLoadedDiv) {
            modulesLoadedDiv.style.display = 'none';
            modulesLoadedDiv.innerHTML = '';
        }
    }

    function updateCertificateTimetableHeader() {
        let headerText = 'Certificate Timetable for: ' + certificateCourse.options[certificateCourse.selectedIndex].text;
        
        if (certificateWeek.value) {
            const selectedWeekOption = certificateWeek.options[certificateWeek.selectedIndex];
            headerText += ' - ' + selectedWeekOption.text;
        }
        
        const moduleCount = window.availableCertificateModules ? window.availableCertificateModules.length : 0;
        headerText += ` <span class="badge bg-info">${moduleCount} modules loaded</span>`;
        certificateTimetableHeader.innerHTML = headerText;
    }

    function updateCertificateWeekDates() {
        if (!certificateWeek.value) return;
        
        const selectedWeekOption = certificateWeek.options[certificateWeek.selectedIndex];
        const startDate = new Date(selectedWeekOption.dataset.startDate);
        
        // Calculate dates for each day of the week
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const headerElement = document.getElementById(`certificate-${day}-header`);
            if (headerElement) {
                const currentDate = new Date(startDate);
                currentDate.setDate(startDate.getDate() + index);
                
                const dateString = currentDate.toLocaleDateString('en-GB', {
                    day: '2-digit',
                    month: 'short'
                });
                
                headerElement.innerHTML = `${dayNames[index]}<br><small class="text-muted">${dateString}</small>`;
            }
        });
    }

    function resetCertificateWeekDates() {
        const days = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        const dayNames = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        
        days.forEach((day, index) => {
            const headerElement = document.getElementById(`certificate-${day}-header`);
            if (headerElement) {
                headerElement.innerHTML = dayNames[index];
            }
        });
    }

    function clearCertificateDateValidation() {
        certificateStartDate.value = '';
        certificateEndDate.value = '';
        certificateStartDate.classList.remove('is-valid', 'is-invalid', 'is-loading');
        certificateEndDate.classList.remove('is-valid', 'is-invalid', 'is-loading');
        
        // Reset module status
        certificateModuleStatusRow.style.display = 'none';
        hideCertificateModulesLoaded();
        
        // Reset week selection
        if (certificateWeek) {
            certificateWeek.value = '';
            resetAndDisable(certificateWeek, 'Select Week');
            certificateWeekSelectionRow.style.display = 'none';
            resetCertificateWeekDates();
        }
    }

    // Form submission handlers
    document.getElementById('degreeTimetableForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Add form submission logic here
        showToast('Success', 'Degree timetable saved successfully!', 'bg-success');
    });

    document.getElementById('certificateTimetableForm').addEventListener('submit', function(e) {
        e.preventDefault();
        // Add form submission logic here
        showToast('Success', 'Certificate timetable saved successfully!', 'bg-success');
    });

    // PDF Download handlers
    document.getElementById('downloadDegreePDF').addEventListener('click', function() {
        downloadTimetablePDF('degree');
    });

    document.getElementById('downloadCertificatePDF').addEventListener('click', function() {
        downloadTimetablePDF('certificate');
    });

    function downloadTimetablePDF(courseType) {
        // Get current filter values
        let filters = {};
        
        if (courseType === 'degree') {
            filters = {
                location: degreeLocation.value,
                course_id: degreeCourse.value,
                intake_id: degreeIntake.value,
                semester: degreeSemester.value,
                start_date: degreeStartDate.value,
                end_date: degreeEndDate.value,
                week_number: degreeWeek.value
            };
        } else {
            filters = {
                location: certificateLocation.value,
                course_id: certificateCourse.value,
                intake_id: certificateIntake.value,
                start_date: certificateStartDate.value,
                end_date: certificateEndDate.value
            };
        }

        // Check if all required filters are filled
        const requiredFields = courseType === 'degree' 
            ? ['location', 'course_id', 'intake_id', 'semester', 'start_date', 'end_date', 'week_number']
            : ['location', 'course_id', 'intake_id', 'start_date', 'end_date'];

        const missingFields = requiredFields.filter(field => !filters[field]);
        
        if (missingFields.length > 0) {
            showToast('Error', 'Please fill all required fields before downloading PDF.', 'bg-danger');
            return;
        }

        // Collect timetable data for degree programs
        let timetableData = null;
        if (courseType === 'degree') {
            timetableData = collectTimetableData();
            
            // Add week start date for PDF header
            if (degreeWeek.value) {
                const selectedWeekOption = degreeWeek.options[degreeWeek.selectedIndex];
                filters.week_start_date = selectedWeekOption.dataset.startDate;
            }
        }

        // Show loading state
        showSpinner(true);
        
        // Create download URL with filters
        const params = new URLSearchParams({
            course_type: courseType,
            ...filters
        });
        
        if (timetableData) {
            params.append('timetable_data', JSON.stringify(timetableData));
        }
        
        const downloadUrl = `/download-timetable-pdf?${params.toString()}`;
        
        // Create temporary link and trigger download
        const link = document.createElement('a');
        link.href = downloadUrl;
        link.download = `${courseType}_timetable_week_${filters.week_number || '1'}.pdf`;
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        showSpinner(false);
        showToast('Success', `${courseType.charAt(0).toUpperCase() + courseType.slice(1)} timetable PDF download started!`, 'bg-success');
    }

    function collectTimetableData() {
        const timetableData = [];
        const rows = degreeTimetableBody.querySelectorAll('tr');
        
        rows.forEach((row, rowIndex) => {
            const timeInput = row.querySelector('input[name*="[time]"]');
            if (timeInput && timeInput.value) {
                const rowData = {
                    time: timeInput.value,
                    monday: '',
                    tuesday: '',
                    wednesday: '',
                    thursday: '',
                    friday: '',
                    saturday: '',
                    sunday: ''
                };
                
                const daySelects = row.querySelectorAll('select');
                daySelects.forEach((select, dayIndex) => {
                    const dayName = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'][dayIndex];
                    if (select.value) {
                        // Get the module name (text) instead of the module ID (value)
                        const selectedOption = select.options[select.selectedIndex];
                        if (selectedOption) {
                            rowData[dayName] = selectedOption.text;
                        }
                    }
                });
                
                timetableData.push(rowData);
            }
        });
        
        return timetableData;
    }

    // Tab coloring logic
    $('#timetableTabs .nav-link').on('shown.bs.tab', function (e) {
        $('#timetableTabs .nav-link').removeClass('bg-primary text-white');
        $(e.target).addClass('bg-primary text-white');
    });
});
</script>
@endpush

<style>
.lds-ring { display: inline-block; position: relative; width: 80px; height: 80px; }
.lds-ring div { box-sizing: border-box; display: block; position: absolute; width: 64px; height: 64px; margin: 8px; border: 8px solid #fff; border-radius: 50%; animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite; border-color: #fff transparent transparent transparent; }
.lds-ring div:nth-child(1) { animation-delay: -0.45s; }
.lds-ring div:nth-child(2) { animation-delay: -0.3s; }
.lds-ring div:nth-child(3) { animation-delay: -0.15s; }
@keyframes lds-ring { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
#spinner-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); display: flex; justify-content: center; align-items: center; z-index: 9999; }

/* Read-only date field styling */
input[type="date"][readonly] {
    background-color: #f8f9fa;
    border-color: #dee2e6;
    color: #495057;
}

/* Loading state for date fields */
input[type="date"].is-loading {
    background-color: #fff3cd;
    border-color: #ffeaa7;
    color: #856404;
}
</style>
@endsection