@extends('inc.app')

@section('title', 'NEBULA | Course Management')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
              <h2 class="text-center mb-4">Course Management</h2>
            <hr>
            <form id="courseForm">
                @csrf
                <div class="mb-3 row mx-3">
                    <label for="location" class="col-sm-2 col-form-label">Location <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="location" name="location" required>
                            <option selected disabled value="">Choose a location...</option>
                            <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                            <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                            <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                        </select>
                    </div>
                </div>
              
                <div class="mb-3 row mx-3">
                    <label for="course_type" class="col-sm-2 col-form-label">Course Type <span class="text-danger">*</span></label>
                    <div class="col-sm-10">
                        <select class="form-select" id="course_type" name="course_type" required>
                            <option selected disabled value="">Choose course type...</option>
                            <option value="degree">Degree Program</option>
                            <option value="diploma">Diploma Program</option>
                            <option value="certificate">Certificate Program</option>
                        </select>
                    </div>
                </div>
              
                <!-- Degree Program Fields -->
                <div id="degree_program_fields" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="course_name" class="col-sm-2 col-form-label">Course Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="course_name" name="course_name" placeholder="Enter the course name" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="course_medium" class="col-sm-2 col-form-label">Course Medium <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="course_medium" name="course_medium" required>
                                <option selected disabled value="">Choose a medium...</option>
                                <option value="Sinhala">Sinhala</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                    </div>
                    <!-- Specialization Field (Degree Only) -->
                    <div class="mb-3 row mx-3 align-items-center">
                        <label class="col-sm-2 col-form-label">Specialization</label>
                        <div class="col-sm-10 d-flex align-items-center gap-3">
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_specialization" id="specializationYes" value="yes">
                                <label class="form-check-label" for="specializationYes">Yes</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="has_specialization" id="specializationNo" value="no" checked>
                                <label class="form-check-label" for="specializationNo">No</label>
                            </div>
                        </div>
                    </div>
                    <div id="specializationFields" style="display: none;">
                        <div class="mb-3 row mx-3 align-items-center">
                            <label class="col-sm-2 col-form-label">Specialization Name(s)</label>
                            <div class="col-sm-10" id="specializationInputs">
                                <div class="input-group mb-2">
                                    <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">
                                    <button type="button" class="btn btn-outline-secondary remove-specialization" style="display:none;">Remove</button>
                                </div>
                            </div>
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-sm btn-success" id="addSpecializationBtn">Add Another Specialization</button>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="conducted_by" class="col-sm-2 col-form-label">Conducted by <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="conducted_by" name="conducted_by" required>
                                <option selected disabled value="">Select who conducts</option>
                                <option value="SLT-MOBITEL Nebula Institute of Technology">SLT-MOBITEL Nebula Institute of Technology</option>
                                <option value="Pearson">Pearson</option>
                                <option value="University of Hertfordshire">University of Hertfordshire</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="other_conducted_by" name="other_conducted_by" placeholder="Please specify" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Duration <span class="text-danger">*</span></label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="duration_years" name="duration_years" placeholder="Years" min="0" required>
                            <input type="number" class="form-control" id="duration_months" name="duration_months" placeholder="Months" min="0" max="11" required>
                            <input type="number" class="form-control" id="duration_days" name="duration_days" placeholder="Days" min="0" max="30" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="no_of_semesters" class="col-sm-2 col-form-label">Semesters</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="no_of_semesters" name="no_of_semesters" placeholder="Enter the number of total semesters">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Training Period</label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="training_years" name="training_years" placeholder="Years" min="0">
                            <input type="number" class="form-control" id="training_months" name="training_months" placeholder="Months" min="0" max="11">
                            <input type="number" class="form-control" id="training_days" name="training_days" placeholder="Days" min="0" max="30">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="min_credits" class="col-sm-2 col-form-label">Minimum Credits</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" id="min_credits" name="min_credits" placeholder="Enter the minimum credits">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="entry_qualification" class="col-sm-2 col-form-label">Entry Qualification <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="entry_qualification" name="entry_qualification" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
              
                <!-- Certificate Program Fields -->
                <div id="certificate_program_fields" style="display: none;">
                    <div class="mb-3 row mx-3">
                        <label for="cert_course_name" class="col-sm-2 col-form-label">Course Name <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control" id="cert_course_name" name="course_name" placeholder="Enter the course name" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_course_medium" class="col-sm-2 col-form-label">Course Medium <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="cert_course_medium" name="course_medium" required>
                                <option selected disabled value="">Choose a medium...</option>
                                <option value="Sinhala">Sinhala</option>
                                <option value="English">English</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_conducted_by" class="col-sm-2 col-form-label">Conducted by <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <select class="form-select" id="cert_conducted_by" name="conducted_by" required>
                                <option selected disabled value="">Select who conducts</option>
                                <option value="SLT-MOBITEL Nebula Institute of Technology">SLT-MOBITEL Nebula Institute of Technology</option>
                                <option value="Pearson">Pearson</option>
                                <option value="University of Hertfordshire">University of Hertfordshire</option>
                                <option value="Other">Other</option>
                            </select>
                            <input type="text" class="form-control mt-2" id="cert_other_conducted_by" name="other_conducted_by" placeholder="Please specify" style="display: none;">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Duration <span class="text-danger">*</span></label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="cert_duration_years" name="duration_years" placeholder="Years" min="0" required>
                            <input type="number" class="form-control" id="cert_duration_months" name="duration_months" placeholder="Months" min="0" max="11" required>
                            <input type="number" class="form-control" id="cert_duration_days" name="duration_days" placeholder="Days" min="0" max="30" required>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label class="col-sm-2 col-form-label">Training Period</label>
                        <div class="col-sm-10 d-flex gap-2">
                            <input type="number" class="form-control" id="cert_training_years" name="training_years" placeholder="Years" min="0">
                            <input type="number" class="form-control" id="cert_training_months" name="training_months" placeholder="Months" min="0" max="11">
                            <input type="number" class="form-control" id="cert_training_days" name="training_days" placeholder="Days" min="0" max="30">
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="course_content" class="col-sm-2 col-form-label">Course Content <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="course_content" name="course_content" rows="4" placeholder="Enter the course content" required></textarea>
                        </div>
                    </div>
                    <div class="mb-3 row mx-3">
                        <label for="cert_entry_qualification" class="col-sm-2 col-form-label">Entry Qualification <span class="text-danger">*</span></label>
                        <div class="col-sm-10">
                            <textarea class="form-control" id="cert_entry_qualification" name="entry_qualification" rows="3" required></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="submit" class="btn btn-primary" id="submitBtn">Submit</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2 class="mb-0">Existing Courses</h2>
                <div class="d-flex gap-2">
                    <button class="btn btn-sm btn-outline-danger" id="bulkDeleteCourseBtn" style="display:none;">
                        <i class="ti ti-trash"></i> Delete Selected
                    </button>
                    <button class="btn btn-sm btn-outline-success" id="exportCourseBtn">
                        <i class="ti ti-download"></i> Export CSV
                    </button>
                </div>
            </div>
            <hr>
            
            <!-- Table Controls -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-search"></i></span>
                        <input type="text" class="form-control" id="searchCourseInput" placeholder="Search courses...">
                    </div>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterCourseType">
                        <option value="">All Types</option>
                        <option value="degree">Degree</option>
                        <option value="diploma">Diploma</option>
                        <option value="certificate">Certificate</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="filterLocation">
                        <option value="">All Locations</option>
                        <option value="Welisara">Welisara</option>
                        <option value="Moratuwa">Moratuwa</option>
                        <option value="Peradeniya">Peradeniya</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <select class="form-select" id="perPageCourseSelect">
                        <option value="10">10 per page</option>
                        <option value="25" selected>25 per page</option>
                        <option value="50">50 per page</option>
                        <option value="all">Show All</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-outline-secondary w-100" id="clearCourseFiltersBtn">
                        <i class="ti ti-filter-off"></i> Clear
                    </button>
                </div>
            </div>

            <!-- Results Info -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <small class="text-muted" id="courseResultsInfo">Showing 0 courses</small>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="selectAllCourses">
                    <label class="form-check-label" for="selectAllCourses">
                        <small>Select All</small>
                    </label>
                </div>
            </div>

            <!-- Table -->
            <div class="table-responsive" style="max-height: 500px; overflow-y: auto; overflow-x: auto; width: 100%;">
                <table class="table table-striped table-bordered table-hover" style="table-layout: fixed; width: max-content; min-width: 900px;">
                    <thead style="position: sticky; top: 0; background: #fff; z-index: 2;">
                        <tr>
                            <th style="position: sticky; top: 0; background: #fff; width: 40px;">
                                <input type="checkbox" id="selectAllCoursesHeader" class="form-check-input">
                            </th>
                            <th class="sortable-course" data-column="course_name" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Course Name <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-course" data-column="course_type" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Course Type <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-course" data-column="location" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Location <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-course" data-column="duration" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Duration <i class="ti ti-selector"></i>
                            </th>
                            <th class="sortable-course" data-column="course_medium" style="position: sticky; top: 0; background: #fff; cursor: pointer;">
                                Medium <i class="ti ti-selector"></i>
                            </th>
                            <th style="position: sticky; top: 0; background: #fff; width: 180px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="existingCoursesTableBody">
                        @forelse($courses as $course)
                        <tr data-course-id="{{ $course->course_id }}">
                            <td>
                                <input type="checkbox" class="form-check-input course-checkbox" data-course-id="{{ $course->course_id }}">
                            </td>
                            <td class="course-name">{{ $course->course_name }}</td>
                            <td class="course-type" data-type="{{ $course->course_type }}">
                                @if($course->course_type == 'degree')
                                    <span class="badge bg-primary">Degree Program</span>
                                @elseif($course->course_type == 'diploma')
                                    <span class="badge bg-info">Diploma Program</span>
                                @elseif($course->course_type == 'certificate')
                                    <span class="badge bg-success">Certificate Program</span>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="course-location">{{ $course->location }}</td>
                            <td class="course-duration">{{ $course->duration_formatted }}</td>
                            <td class="course-medium">{{ $course->course_medium }}</td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    <button class="btn btn-outline-primary edit-course-btn" data-course-id="{{ $course->course_id }}" title="Edit">
                                        <i class="ti ti-edit"></i>
                                    </button>
                                    <button class="btn btn-outline-danger delete-course-btn" data-course-id="{{ $course->course_id }}" title="Delete">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr id="no-courses-row">
                            <td colspan="7" class="text-center">No courses found.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav aria-label="Course pagination" class="mt-3">
                <ul class="pagination pagination-sm justify-content-center" id="coursePagination">
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Toast Container -->
<div class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteCourseModal" tabindex="-1" aria-labelledby="deleteCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="deleteCourseModalLabel">Delete Course</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this course?</p>
            <input type="hidden" id="delete_course_id">
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmDeleteCourseBtn">Delete</button>
        </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    let allCourses = [];
    let filteredCourses = [];
    let currentCoursePage = 1;
    let perCoursePage = 25;
    let sortCourseColumn = 'course_name';
    let sortCourseDirection = 'asc';
    let isFormDirty = false;

    // Initialize courses array from table
    function initializeCourses() {
        allCourses = [];
        $('#existingCoursesTableBody tr[data-course-id]').each(function() {
            const row = $(this);
            const course = {
                course_id: row.data('course-id'),
                course_name: row.find('.course-name').text().trim(),
                course_type: row.find('.course-type').data('type'),
                location: row.find('.course-location').text().trim(),
                duration: row.find('.course-duration').text().trim(),
                course_medium: row.find('.course-medium').text().trim()
            };
            allCourses.push(course);
        });
        filteredCourses = [...allCourses];
        renderCourseTable();
    }

    initializeCourses();

    // Apply all filters function
    function applyCourseFilters() {
        const searchTerm = $('#searchCourseInput').val().toLowerCase();
        const filterType = $('#filterCourseType').val();
        const filterLoc = $('#filterLocation').val();
        
        filteredCourses = allCourses.filter(course => {
            const matchesSearch = !searchTerm || 
                course.course_name.toLowerCase().includes(searchTerm) ||
                course.location.toLowerCase().includes(searchTerm) ||
                course.duration.toLowerCase().includes(searchTerm) ||
                course.course_medium.toLowerCase().includes(searchTerm);
            
            const matchesType = !filterType || course.course_type === filterType;
            const matchesLocation = !filterLoc || course.location === filterLoc;
            
            return matchesSearch && matchesType && matchesLocation;
        });
        
        currentCoursePage = 1;
        renderCourseTable();
    }

    // Auto-filter when course_type changes in form
    $('#course_type').on('change', function() {
        const selectedType = $(this).val();
        
        // Handle form field visibility (existing logic)
        if (selectedType === 'degree') {
            $('#degree_program_fields').show().find('input, select, textarea').prop('disabled', false);
            $('#certificate_program_fields').hide().find('input, select, textarea').prop('disabled', true);
            $('#duration_years').val(3);
            $('#duration_months').val(0);
            $('#duration_days').val(0);
        } else if (selectedType === 'diploma') {
            $('#degree_program_fields').show().find('input, select, textarea').prop('disabled', false);
            $('#certificate_program_fields').hide().find('input, select, textarea').prop('disabled', true);
            $('#duration_years').val(2);
            $('#duration_months').val(0);
            $('#duration_days').val(0);
        } else if (selectedType === 'certificate') {
            $('#certificate_program_fields').show().find('input, select, textarea').prop('disabled', false);
            $('#degree_program_fields').hide().find('input, select, textarea').prop('disabled', true);
            $('#cert_duration_years').val(1);
            $('#cert_duration_months').val(0);
            $('#cert_duration_days').val(0);
        } else {
            $('#degree_program_fields, #certificate_program_fields').hide().find('input, select, textarea').prop('disabled', true);
        }
        
        // Auto-filter the table
        if (selectedType) {
            $('#filterCourseType').val(selectedType);
            applyCourseFilters();
            showToast(`Table filtered to show only ${selectedType} programs`, 'info');
        }
    });

    // Initially disable all fields
    $('#degree_program_fields, #certificate_program_fields').find('input, select, textarea').prop('disabled', true);

    // Search functionality
    $('#searchCourseInput').on('keyup', function() {
        applyCourseFilters();
    });

    // Filter by type
    $('#filterCourseType').on('change', function() {
        applyCourseFilters();
    });

    // Filter by location
    $('#filterLocation').on('change', function() {
        applyCourseFilters();
    });

    // Clear filters button
    $('#clearCourseFiltersBtn').on('click', function() {
        $('#searchCourseInput').val('');
        $('#filterCourseType').val('');
        $('#filterLocation').val('');
        filteredCourses = [...allCourses];
        currentCoursePage = 1;
        renderCourseTable();
        showToast('Filters cleared', 'info');
    });

    // Per page selection
    $('#perPageCourseSelect').on('change', function() {
        perCoursePage = $(this).val() === 'all' ? filteredCourses.length : parseInt($(this).val());
        currentCoursePage = 1;
        renderCourseTable();
    });

    // Sorting
    $('.sortable-course').on('click', function() {
        const column = $(this).data('column');
        if (sortCourseColumn === column) {
            sortCourseDirection = sortCourseDirection === 'asc' ? 'desc' : 'asc';
        } else {
            sortCourseColumn = column;
            sortCourseDirection = 'asc';
        }
        
        $('.sortable-course i').attr('class', 'ti ti-selector');
        $(this).find('i').attr('class', sortCourseDirection === 'asc' ? 'ti ti-sort-ascending' : 'ti ti-sort-descending');
        
        sortCourses();
        renderCourseTable();
    });

    function sortCourses() {
        filteredCourses.sort((a, b) => {
            let aVal = a[sortCourseColumn] || '';
            let bVal = b[sortCourseColumn] || '';
            
            aVal = aVal.toString().toLowerCase();
            bVal = bVal.toString().toLowerCase();
            
            if (aVal < bVal) return sortCourseDirection === 'asc' ? -1 : 1;
            if (aVal > bVal) return sortCourseDirection === 'asc' ? 1 : -1;
            return 0;
        });
    }

    // Render table
    function renderCourseTable() {
        const start = (currentCoursePage - 1) * perCoursePage;
        const end = start + perCoursePage;
        const pageCourses = filteredCourses.slice(start, end);
        
        $('#existingCoursesTableBody').empty();
        
        if (pageCourses.length === 0) {
            $('#existingCoursesTableBody').html('<tr><td colspan="7" class="text-center">No courses found.</td></tr>');
            $('#courseResultsInfo').text('Showing 0 courses');
        } else {
            pageCourses.forEach(course => {
                let badgeClass = 'primary';
                let badgeText = 'Degree Program';
                if (course.course_type === 'diploma') {
                    badgeClass = 'info';
                    badgeText = 'Diploma Program';
                } else if (course.course_type === 'certificate') {
                    badgeClass = 'success';
                    badgeText = 'Certificate Program';
                }
                
                const row = `
                    <tr id="course-row-${course.course_id}" data-course-id="${course.course_id}">
                        <td>
                            <input type="checkbox" class="form-check-input course-checkbox" data-course-id="${course.course_id}">
                        </td>
                        <td class="course-name">${course.course_name}</td>
                        <td class="course-type" data-type="${course.course_type}">
                            <span class="badge bg-${badgeClass}">${badgeText}</span>
                        </td>
                        <td class="course-location">${course.location}</td>
                        <td class="course-duration">${course.duration}</td>
                        <td class="course-medium">${course.course_medium}</td>
                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <button class="btn btn-outline-primary edit-course-btn" data-course-id="${course.course_id}" title="Edit">
                                    <i class="ti ti-edit"></i>
                                </button>
                                <button class="btn btn-outline-danger delete-course-btn" data-course-id="${course.course_id}" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                `;
                $('#existingCoursesTableBody').append(row);
            });
            
            const showing = filteredCourses.length > perCoursePage ? 
                `Showing ${start + 1}-${Math.min(end, filteredCourses.length)} of ${filteredCourses.length} courses` :
                `Showing ${filteredCourses.length} courses`;
            $('#courseResultsInfo').text(showing);
        }
        
        renderCoursePagination();
    }

    // Render pagination
    function renderCoursePagination() {
        const totalPages = Math.ceil(filteredCourses.length / perCoursePage);
        $('#coursePagination').empty();
        
        if (totalPages <= 1) return;
        
        $('#coursePagination').append(`
            <li class="page-item ${currentCoursePage === 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentCoursePage - 1}">Previous</a>
            </li>
        `);
        
        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentCoursePage - 2 && i <= currentCoursePage + 2)) {
                $('#coursePagination').append(`
                    <li class="page-item ${i === currentCoursePage ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${i}">${i}</a>
                    </li>
                `);
            } else if (i === currentCoursePage - 3 || i === currentCoursePage + 3) {
                $('#coursePagination').append(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
            }
        }
        
        $('#coursePagination').append(`
            <li class="page-item ${currentCoursePage === totalPages ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${currentCoursePage + 1}">Next</a>
            </li>
        `);
    }

    // Pagination click
    $(document).on('click', '#coursePagination a', function(e) {
        e.preventDefault();
        const page = parseInt($(this).data('page'));
        if (page > 0 && page <= Math.ceil(filteredCourses.length / perCoursePage)) {
            currentCoursePage = page;
            renderCourseTable();
            $('.table-responsive').scrollTop(0);
        }
    });

    // Select all checkboxes
    $('#selectAllCourses, #selectAllCoursesHeader').on('change', function() {
        const isChecked = $(this).prop('checked');
        $('#selectAllCourses, #selectAllCoursesHeader').prop('checked', isChecked);
        $('.course-checkbox').prop('checked', isChecked);
        updateBulkDeleteCourseButton();
    });

    $(document).on('change', '.course-checkbox', function() {
        updateBulkDeleteCourseButton();
        const total = $('.course-checkbox').length;
        const checked = $('.course-checkbox:checked').length;
        $('#selectAllCourses, #selectAllCoursesHeader').prop('checked', total === checked);
    });

    function updateBulkDeleteCourseButton() {
        const checkedCount = $('.course-checkbox:checked').length;
        if (checkedCount > 0) {
            $('#bulkDeleteCourseBtn').show().text(`Delete Selected (${checkedCount})`);
        } else {
            $('#bulkDeleteCourseBtn').hide();
        }
    }

    // Bulk delete
    $('#bulkDeleteCourseBtn').on('click', function() {
        const selectedIds = [];
        $('.course-checkbox:checked').each(function() {
            selectedIds.push($(this).data('course-id'));
        });
        
        if (selectedIds.length === 0) return;
        
        if (!confirm(`Are you sure you want to delete ${selectedIds.length} course(s)?`)) return;
        
        let completed = 0;
        selectedIds.forEach(id => {
            $.ajax({
                url: '/api/courses/' + id,
                type: 'DELETE',
                data: { _token: '{{ csrf_token() }}' },
                success: function(response) {
                    completed++;
                    allCourses = allCourses.filter(c => c.course_id != id);
                    if (completed === selectedIds.length) {
                        showToast(`Successfully deleted ${selectedIds.length} course(s)`, 'success');
                        applyCourseFilters();
                        updateBulkDeleteCourseButton();
                        $('#selectAllCourses, #selectAllCoursesHeader').prop('checked', false);
                    }
                },
                error: function() {
                    showToast('Error deleting some courses', 'danger');
                }
            });
        });
    });

    // Export to CSV
    $('#exportCourseBtn').on('click', function() {
        const csv = [];
        csv.push(['Course Name', 'Course Type', 'Location', 'Duration', 'Medium'].join(','));
        
        filteredCourses.forEach(course => {
            const typeText = course.course_type === 'degree' ? 'Degree Program' : 
                           course.course_type === 'diploma' ? 'Diploma Program' : 'Certificate Program';
            csv.push([
                `"${course.course_name}"`,
                `"${typeText}"`,
                `"${course.location}"`,
                `"${course.duration}"`,
                `"${course.course_medium}"`
            ].join(','));
        });
        
        const blob = new Blob([csv.join('\n')], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `courses_export_${new Date().toISOString().split('T')[0]}.csv`;
        a.click();
        window.URL.revokeObjectURL(url);
        showToast('Courses exported successfully', 'success');
    });

    // Form submission
    $('#courseForm').on('submit', function(e) {
        e.preventDefault();
        
        const courseType = $('#course_type').val();
        if (!courseType) {
            showToast('Please select a course type.', 'warning');
            return;
        }
        
        const formData = new FormData(this);
        
        if (courseType === 'degree') {
            if ($('#conducted_by').val() === 'Other') {
                formData.set('conducted_by', $('#other_conducted_by').val());
            }
        } else if (courseType === 'certificate') {
            if ($('#cert_conducted_by').val() === 'Other') {
                formData.set('conducted_by', $('#cert_other_conducted_by').val());
            }
        }
        formData.delete('other_conducted_by');

        const editCourseId = getQueryParam('course_id');
        const isEditMode = editCourseId !== null;
        
        const url = isEditMode ? `/api/courses/update/${editCourseId}` : '{{ route("course.store") }}';
        const method = 'POST';

        $.ajax({
            url: url,
            type: method,
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    showToast(response.message, 'success');

                    if (!isEditMode && response.course) {
                        const course = response.course;
                        allCourses.unshift({
                            course_id: course.course_id,
                            course_name: course.course_name,
                            course_type: course.course_type,
                            location: course.location,
                            duration: course.duration_formatted ?? 'N/A',
                            course_medium: course.course_medium
                        });
                        
                        $('#courseForm')[0].reset();
                        $('#degree_program_fields, #certificate_program_fields').hide();
                        applyCourseFilters();
                        
                        setTimeout(() => {
                            const row = $(`#course-row-${course.course_id}`);
                            if (row.length) {
                                $('html, body').animate({
                                    scrollTop: row.offset().top - 150
                                }, 800);
                                row.addClass('table-success');
                                setTimeout(() => row.removeClass('table-success'), 2500);
                            }
                        }, 300);
                    } else if (isEditMode && response.course) {
                        const course = response.course;
                        const index = allCourses.findIndex(c => c.course_id == course.course_id);
                        if (index !== -1) {
                            allCourses[index] = {
                                course_id: course.course_id,
                                course_name: course.course_name,
                                course_type: course.course_type,
                                location: course.location,
                                duration: course.duration_formatted ?? 'N/A',
                                course_medium: course.course_medium
                            };
                        }
                        applyCourseFilters();
                        
                        const url = new URL(window.location.href);
                        url.searchParams.delete('course_id');
                        window.history.replaceState({}, document.title, url.toString());
                        $('#submitBtn').text('Submit');
                    }
                }
            },
            error: function(xhr) {
                let message = 'An error occurred.';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }
                showToast(message, 'danger');
            }
        });
    });

    // Edit button click
    $(document).on('click', '.edit-course-btn', function() {
        const courseId = $(this).data('course-id');
        window.location.href = '/course-management?course_id=' + courseId;
    });

    // Delete button click
    $(document).on('click', '.delete-course-btn', function() {
        const courseId = $(this).data('course-id');
        $('#delete_course_id').val(courseId);
        $('#deleteCourseModal').modal('show');
    });

    // Confirm delete
    $('#confirmDeleteCourseBtn').on('click', function() {
        const courseId = $('#delete_course_id').val();
        $.ajax({
            url: '/api/courses/' + courseId,
            type: 'DELETE',
            data: {_token: '{{ csrf_token() }}'},
            success: function(response) {
                if (response.success) {
                    showToast('Course deleted successfully', 'success');
                    allCourses = allCourses.filter(c => c.course_id != courseId);
                    applyCourseFilters();
                    $('#deleteCourseModal').modal('hide');
                } else {
                    showToast('Failed to delete course', 'danger');
                }
            },
            error: function() {
                showToast('Error deleting course', 'danger');
            }
        });
    });

    // On page load, check for course_id param and prefill form
    function getQueryParam(name) {
        const url = new URL(window.location.href);
        return url.searchParams.get(name);
    }

    const editCourseId = getQueryParam('course_id');
    if (editCourseId) {
        $('#submitBtn').text('Update Course');
        
        $.ajax({
            url: '/api/courses/' + editCourseId,
            type: 'GET',
            success: function(response) {
                if (response.success && response.course) {
                    const course = response.course;
                    
                    setSelectValue('#location', course.location);
                    setSelectValue('#course_type', course.course_type);
                    $('#course_type').trigger('change');

                    if (course.course_type === 'degree') {
                        $('#course_name').val(course.course_name);
                        $('#duration_years').val(course.duration.years);
                        $('#duration_months').val(course.duration.months);
                        $('#duration_days').val(course.duration.days);
                        setSelectValue('#course_medium', course.course_medium);

                        const conductedBy = course.conducted_by;
                        const conductedBySelect = $('#conducted_by');
                        const otherConductedByInput = $('#other_conducted_by');
                        if (conductedBySelect.find(`option[value="${conductedBy}"]`).length > 0) {
                            conductedBySelect.val(conductedBy);
                        } else {
                            conductedBySelect.val('Other');
                            otherConductedByInput.val(conductedBy).show().prop('required', true);
                        }

                        $('#no_of_semesters').val(course.no_of_semesters).trigger('input');
                        $('#training_years').val(course.training_period.years);
                        $('#training_months').val(course.training_period.months);
                        $('#training_days').val(course.training_period.days);
                        $('#min_credits').val(course.min_credits);
                        $('#entry_qualification').val(course.entry_qualification);
                    } else if (course.course_type === 'certificate') {
                        $('#cert_course_name').val(course.course_name);
                        $('#cert_duration_years').val(course.duration.years);
                        $('#cert_duration_months').val(course.duration.months);
                        $('#cert_duration_days').val(course.duration.days);
                        setSelectValue('#cert_course_medium', course.course_medium);
                        
                        const conductedBy = course.conducted_by;
                        const conductedBySelect = $('#cert_conducted_by');
                        const otherConductedByInput = $('#cert_other_conducted_by');
                        if (conductedBySelect.find(`option[value="${conductedBy}"]`).length > 0) {
                            conductedBySelect.val(conductedBy);
                        } else {
                            conductedBySelect.val('Other');
                            otherConductedByInput.val(conductedBy).show().prop('required', true);
                        }
                        
                        $('#cert_training_years').val(course.training_period.years);
                        $('#cert_training_months').val(course.training_period.months);
                        $('#cert_training_days').val(course.training_period.days);
                        $('#course_content').val(course.course_content);
                        $('#cert_entry_qualification').val(course.entry_qualification);
                    }
                } else {
                    showToast('Failed to fetch course details for editing', 'danger');
                }
            },
            error: function(xhr, status, error) {
                showToast('Error fetching course details for editing', 'danger');
            }
        });
    }

    $('#conducted_by, #cert_conducted_by').on('change', function() {
        const $otherInput = $(this).parent().find('input[name="other_conducted_by"]');
        if ($(this).val() === 'Other') {
            $otherInput.show().prop('required', true);
        } else {
            $otherInput.hide().prop('required', false).val('');
        }
    });

    function setSelectValue(selectId, value) {
        const $select = $(selectId);
        if ($select.find(`option[value='${value}']`).length > 0) {
            $select.val(value);
        } else {
            $select.find('option').each(function() {
                if ($(this).text().trim() === value.trim()) {
                    $select.val($(this).val());
                }
            });
        }
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

    // Specialization logic
    $('input[name="has_specialization"]').on('change', function() {
        if ($(this).val() === 'yes') {
            $('#specializationFields').show();
        } else {
            $('#specializationFields').hide();
            $('#specializationInputs').html('<div class="input-group mb-2">\
                <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">\
                <button type="button" class="btn btn-outline-secondary remove-specialization" style="display:none;">Remove</button>\
            </div>');
        }
    });

    $('#addSpecializationBtn').on('click', function() {
        $('#specializationInputs').append('<div class="input-group mb-2">\
            <input type="text" class="form-control specialization-input" name="specializations[]" placeholder="Enter specialization name">\
            <button type="button" class="btn btn-outline-secondary remove-specialization">Remove</button>\
        </div>');
        updateRemoveButtons();
    });

    $('#specializationInputs').on('click', '.remove-specialization', function() {
        $(this).closest('.input-group').remove();
        updateRemoveButtons();
    });

    function updateRemoveButtons() {
        var count = $('#specializationInputs .input-group').length;
        $('#specializationInputs .remove-specialization').each(function(i, btn) {
            $(btn).toggle(count > 1);
        });
    }
    updateRemoveButtons();
});
</script>
@endpush