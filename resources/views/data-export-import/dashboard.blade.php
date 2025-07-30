@extends('inc.app')

@section('title', 'NEBULA | Data Export/Import')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Data Export/Import Dashboard</h2>
            <hr>
            
            <p class="text-center mb-4">
                Export and import data in various formats (CSV, Excel, JSON) for students, courses, attendance, and exam results.
            </p>

            <!-- Export Section -->
            <h5 class="mb-3">Data Export</h5>
            
            <div class="row mb-3">
                <label for="exportType" class="col-sm-2 col-form-label">Data Type<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select" id="exportType" required>
                        <option value="">Select Data Type</option>
                        <option value="students">Students</option>
                        <option value="courses">Courses</option>
                        <option value="attendance">Attendance</option>
                        <option value="exam_results">Exam Results</option>
                    </select>
                </div>
                <label for="exportFormat" class="col-sm-2 col-form-label">Export Format</label>
                <div class="col-sm-4">
                    <select class="form-select" id="exportFormat">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
            </div>

            <!-- Export Filters -->
            <div id="exportFilters" style="display: none;">
                <h6 class="mb-3">Export Filters</h6>
                <div class="row mb-3">
                    <label for="exportStartDate" class="col-sm-2 col-form-label">Start Date</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" id="exportStartDate">
                    </div>
                    <label for="exportEndDate" class="col-sm-2 col-form-label">End Date</label>
                    <div class="col-sm-4">
                        <input type="date" class="form-control" id="exportEndDate">
                    </div>
                </div>
                <div class="row mb-3">
                    <label for="exportLocation" class="col-sm-2 col-form-label">Location</label>
                    <div class="col-sm-4">
                        <select class="form-select" id="exportLocation">
                            <option value="">All Locations</option>
                            <option value="Welisara">Welisara</option>
                            <option value="Moratuwa">Moratuwa</option>
                            <option value="Peradeniya">Peradeniya</option>
                        </select>
                    </div>
                    <label for="exportCourse" class="col-sm-2 col-form-label">Course</label>
                    <div class="col-sm-4">
                        <select class="form-select" id="exportCourse">
                            <option value="">All Courses</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-10 offset-sm-2">
                    <button type="button" class="btn btn-primary me-2" id="exportData">
                        <i class="bx bx-download me-1"></i>Export Data
                    </button>
                    <button type="button" class="btn btn-secondary" id="clearExportFilters">
                        <i class="bx bx-refresh me-1"></i>Clear Filters
                    </button>
                </div>
            </div>

            <hr class="my-4">

            <!-- Import Section -->
            <h5 class="mb-3">Data Import</h5>
            
            <div class="row mb-3">
                <label for="importType" class="col-sm-2 col-form-label">Data Type<span class="text-danger">*</span></label>
                <div class="col-sm-4">
                    <select class="form-select" id="importType" required>
                        <option value="">Select Data Type</option>
                        <option value="students">Students</option>
                        <option value="courses">Courses</option>
                        <option value="attendance">Attendance</option>
                        <option value="exam_results">Exam Results</option>
                    </select>
                </div>
                <label for="importFormat" class="col-sm-2 col-form-label">Import Format</label>
                <div class="col-sm-4">
                    <select class="form-select" id="importFormat">
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                        <option value="json">JSON</option>
                    </select>
                </div>
            </div>

            <div class="row mb-3">
                <label for="importFile" class="col-sm-2 col-form-label">File<span class="text-danger">*</span></label>
                <div class="col-sm-10">
                    <input type="file" class="form-control" id="importFile" accept=".csv,.xlsx,.xls,.json">
                    <small class="text-muted">Maximum file size: 10MB</small>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-sm-10 offset-sm-2">
                    <button type="button" class="btn btn-success me-2" id="importData">
                        <i class="bx bx-upload me-1"></i>Import Data
                    </button>
                    <button type="button" class="btn btn-info me-2" id="downloadTemplate">
                        <i class="bx bx-file me-1"></i>Download Template
                    </button>
                </div>
            </div>

            <hr class="my-4">

            <!-- Statistics Section -->
            <h5 class="mb-3">Export Statistics</h5>
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-primary">
                        <div class="card-body text-center">
                            <i class="bx bx-user text-primary" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Total Students</h6>
                            <h4 id="totalStudents">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-success">
                        <div class="card-body text-center">
                            <i class="bx bx-book-open text-success" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Total Courses</h6>
                            <h4 id="totalCourses">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-info">
                        <div class="card-body text-center">
                            <i class="bx bx-id text-info" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Attendance Records</h6>
                            <h4 id="totalAttendance">0</h4>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card border-warning">
                        <div class="card-body text-center">
                            <i class="bx bx-file-blank text-warning" style="font-size: 2rem;"></i>
                            <h6 class="mt-2">Exam Results</h6>
                            <h4 id="totalExamResults">0</h4>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Exports -->
            <hr class="my-4">
            <h5 class="mb-3">Recent Exports</h5>
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Format</th>
                                    <th>Records</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="recentExports">
                                <tr>
                                    <td colspan="5" class="text-center text-muted">No recent exports</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Loading Spinner -->
            <div id="loadingSpinner" style="display: none;" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2">Processing...</p>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load statistics
    loadExportStats();
    loadCourses();

    // Handle export type change
    $('#exportType').change(function() {
        const exportType = $(this).val();
        if (exportType) {
            $('#exportFilters').show();
        } else {
            $('#exportFilters').hide();
        }
    });

    // Export Data
    $('#exportData').click(function() {
        exportData();
    });

    // Import Data
    $('#importData').click(function() {
        importData();
    });

    // Download Template
    $('#downloadTemplate').click(function() {
        downloadTemplate();
    });

    // Clear Export Filters
    $('#clearExportFilters').click(function() {
        clearExportFilters();
    });

    function loadExportStats() {
        $.ajax({
            url: '/data-export-import/stats',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    $('#totalStudents').text(response.data.students || 0);
                    $('#totalCourses').text(response.data.courses || 0);
                    $('#totalAttendance').text(response.data.attendance || 0);
                    $('#totalExamResults').text(response.data.exam_results || 0);
                }
            },
            error: function(xhr) {
                console.error('Failed to load statistics:', xhr);
            }
        });
    }

    function loadCourses() {
        $.ajax({
            url: '/course-management/get-courses',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const courseSelect = $('#exportCourse');
                    courseSelect.find('option:not(:first)').remove();
                    
                    response.courses.forEach(function(course) {
                        courseSelect.append(`<option value="${course.course_id}">${course.course_name}</option>`);
                    });
                }
            },
            error: function(xhr) {
                console.error('Failed to load courses:', xhr);
            }
        });
    }

    function exportData() {
        const exportType = $('#exportType').val();
        if (!exportType) {
            alert('Please select a data type to export.');
            return;
        }

        const format = $('#exportFormat').val();
        const filters = {
            start_date: $('#exportStartDate').val(),
            end_date: $('#exportEndDate').val(),
            location: $('#exportLocation').val(),
            course_id: $('#exportCourse').val()
        };

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: `/data-export-import/export/${exportType}`,
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                format: format,
                filters: filters
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    // Create download link
                    const link = document.createElement('a');
                    link.href = response.data.download_url;
                    link.download = response.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    
                    alert('Export completed successfully!');
                    loadExportStats();
                } else {
                    alert('Export failed: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Export failed:', xhr);
                alert('Export failed. Please try again.');
            }
        });
    }

    function importData() {
        const importType = $('#importType').val();
        const format = $('#importFormat').val();
        const file = $('#importFile')[0].files[0];

        if (!importType) {
            alert('Please select a data type to import.');
            return;
        }

        if (!file) {
            alert('Please select a file to import.');
            return;
        }

        const formData = new FormData();
        formData.append('file', file);
        formData.append('type', importType);
        formData.append('format', format);

        // Show loading spinner
        $('#loadingSpinner').show();

        $.ajax({
            url: '/data-export-import/import',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                $('#loadingSpinner').hide();
                if (response.success) {
                    alert(`Import completed successfully! ${response.data.imported} records imported.`);
                    $('#importFile').val('');
                    loadExportStats();
                } else {
                    alert('Import failed: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Import failed:', xhr);
                alert('Import failed. Please check your file format and try again.');
            }
        });
    }

    function downloadTemplate() {
        const importType = $('#importType').val();
        const format = $('#importFormat').val();

        if (!importType) {
            alert('Please select a data type first.');
            return;
        }

        $.ajax({
            url: '/data-export-import/template',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                type: importType,
                format: format
            },
            success: function(response) {
                if (response.success) {
                    // Create download link
                    const link = document.createElement('a');
                    link.href = response.data.download_url;
                    link.download = response.data.filename;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    alert('Template download failed: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Template download failed:', xhr);
                alert('Template download failed. Please try again.');
            }
        });
    }

    function clearExportFilters() {
        $('#exportStartDate').val('');
        $('#exportEndDate').val('');
        $('#exportLocation').val('');
        $('#exportCourse').val('');
    }
});
</script>
@endsection 