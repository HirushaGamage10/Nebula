@extends('inc.app')

@section('title', 'NEBULA | Reporting Dashboard')

@section('content')
<div class="container-fluid">
    <div class="card">
        <div class="card-body">
            <h2 class="text-center mb-4">Reporting Dashboard</h2>
            <hr>
                        
                        <!-- Report Type Selection -->
                        <h5 class="mb-3">Report Configuration</h5>
                        <div class="row mb-3">
                            <label for="reportType" class="col-sm-2 col-form-label">Report Type<span class="text-danger">*</span></label>
                            <div class="col-sm-4">
                                <select class="form-select" id="reportType" required>
                                    <option value="">Select Report Type</option>
                                    <option value="enrollment">Student Enrollment Report</option>
                                    <option value="performance">Course Performance Report</option>
                                    <option value="attendance">Attendance Report</option>
                                    <option value="financial">Financial Report</option>
                                    <option value="module">Module Assignment Report</option>
                                </select>
                            </div>
                            <label for="exportFormat" class="col-sm-2 col-form-label">Export Format</label>
                            <div class="col-sm-4">
                                <select class="form-select" id="exportFormat">
                                    <option value="json">JSON</option>
                                    <option value="pdf">PDF</option>
                                    <option value="excel">Excel</option>
                                    <option value="csv">CSV</option>
                                </select>
                            </div>
                        </div>

                        <!-- Filters Section -->
                        <h5 class="mb-3">Report Filters</h5>
                        <div class="row mb-3">
                            <label for="startDate" class="col-sm-2 col-form-label">Start Date</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <label for="endDate" class="col-sm-2 col-form-label">End Date</label>
                            <div class="col-sm-4">
                                <input type="date" class="form-control" id="endDate">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <label for="location" class="col-sm-2 col-form-label">Location</label>
                            <div class="col-sm-4">
                                <select class="form-select" id="location">
                                    <option value="">All Locations</option>
                                    <option value="Welisara">Welisara</option>
                                    <option value="Moratuwa">Moratuwa</option>
                                    <option value="Peradeniya">Peradeniya</option>
                                </select>
                            </div>
                            <label for="courseId" class="col-sm-2 col-form-label">Course</label>
                            <div class="col-sm-4">
                                <select class="form-select" id="courseId">
                                    <option value="">All Courses</option>
                                </select>
                            </div>
                        </div>

                        <!-- Semester Filter (for specific reports) -->
                        <div class="row mb-3" id="semesterFilter" style="display: none;">
                            <label for="semester" class="col-sm-2 col-form-label">Semester</label>
                            <div class="col-sm-4">
                                <select class="form-select" id="semester">
                                    <option value="">All Semesters</option>
                                    <option value="1">Semester 1</option>
                                    <option value="2">Semester 2</option>
                                    <option value="3">Semester 3</option>
                                    <option value="4">Semester 4</option>
                                    <option value="5">Semester 5</option>
                                    <option value="6">Semester 6</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <!-- Action Buttons -->
                        <div class="row mb-4">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" class="btn btn-primary me-2" id="generateReport">
                                    <i class="bx bx-bar-chart-alt-2 me-1"></i>Generate Report
                                </button>
                                <button type="button" class="btn btn-success me-2" id="exportReport">
                                    <i class="bx bx-download me-1"></i>Export Report
                                </button>
                                <button type="button" class="btn btn-secondary" id="clearFilters">
                                    <i class="bx bx-refresh me-1"></i>Clear Filters
                                </button>
                            </div>
                        </div>

                        <!-- Report Results -->
                        <div id="reportResults" style="display: none;">
                            <hr class="my-4">
                            <h5 class="mb-3">Report Results</h5>
                            <div class="card">
                                <div class="card-body">
                                    <div id="reportContent"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Loading Spinner -->
                        <div id="loadingSpinner" style="display: none;" class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-2">Generating report...</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load courses for filter
    loadCourses();

    // Handle report type change
    $('#reportType').change(function() {
        const reportType = $(this).val();
        
        // Show/hide semester filter for specific reports
        if (reportType === 'performance' || reportType === 'module') {
            $('#semesterFilter').show();
        } else {
            $('#semesterFilter').hide();
            $('#semester').val('');
        }
    });

    // Generate Report
    $('#generateReport').click(function() {
        generateReport();
    });

    // Export Report
    $('#exportReport').click(function() {
        exportReport();
    });

    // Clear Filters
    $('#clearFilters').click(function() {
        clearFilters();
    });

    function loadCourses() {
        $.ajax({
            url: '/course-management/get-courses',
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    const courseSelect = $('#courseId');
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

    function generateReport() {
        const reportType = $('#reportType').val();
        if (!reportType) {
            alert('Please select a report type.');
            return;
        }

        const filters = {
            start_date: $('#startDate').val(),
            end_date: $('#endDate').val(),
            location: $('#location').val(),
            course_id: $('#courseId').val(),
            semester: $('#semester').val(),
            format: $('#exportFormat').val()
        };

        // Show loading spinner
        $('#loadingSpinner').show();
        $('#reportResults').hide();

        $.ajax({
            url: `/reporting/${reportType}`,
            method: 'POST',
            data: filters,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                $('#loadingSpinner').hide();
                
                if (response.success) {
                    displayReport(response.data, reportType);
                    $('#reportResults').show();
                } else {
                    alert('Failed to generate report: ' + response.message);
                }
            },
            error: function(xhr) {
                $('#loadingSpinner').hide();
                console.error('Report generation failed:', xhr);
                alert('Failed to generate report. Please try again.');
            }
        });
    }

    function exportReport() {
        const reportType = $('#reportType').val();
        if (!reportType) {
            alert('Please select a report type.');
            return;
        }

        const filters = {
            start_date: $('#startDate').val(),
            end_date: $('#endDate').val(),
            location: $('#location').val(),
            course_id: $('#courseId').val(),
            semester: $('#semester').val()
        };

        const format = $('#exportFormat').val();

        $.ajax({
            url: '/reporting/export',
            method: 'POST',
            data: {
                report_type: reportType,
                format: format,
                filters: filters
            },
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response.success) {
                    alert('Report exported successfully!');
                    // In a real implementation, you would trigger a download here
                } else {
                    alert('Failed to export report: ' + response.message);
                }
            },
            error: function(xhr) {
                console.error('Report export failed:', xhr);
                alert('Failed to export report. Please try again.');
            }
        });
    }

    function displayReport(data, reportType) {
        let html = '';
        
        switch (reportType) {
            case 'enrollment':
                html = displayEnrollmentReport(data);
                break;
            case 'performance':
                html = displayPerformanceReport(data);
                break;
            case 'attendance':
                html = displayAttendanceReport(data);
                break;
            case 'financial':
                html = displayFinancialReport(data);
                break;
            case 'module':
                html = displayModuleReport(data);
                break;
        }

        $('#reportContent').html(html);
    }

    function displayEnrollmentReport(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h3>${data.total_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Male Students</h5>
                            <h3>${data.male_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Female Students</h5>
                            <h3>${data.female_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Generated At</h5>
                            <small>${data.generated_at}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Location statistics
        if (data.location_stats) {
            html += '<h6 class="mt-4">Location Statistics</h6>';
            html += '<div class="table-responsive"><table class="table table-striped">';
            html += '<thead><tr><th>Location</th><th>Total</th><th>Male</th><th>Female</th></tr></thead><tbody>';
            
            Object.keys(data.location_stats).forEach(location => {
                const stats = data.location_stats[location];
                html += `<tr>
                    <td>${location}</td>
                    <td>${stats.count}</td>
                    <td>${stats.male}</td>
                    <td>${stats.female}</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
        }

        return html;
    }

    function displayPerformanceReport(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Courses</h5>
                            <h3>${data.total_courses}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h3>${data.total_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Generated At</h5>
                            <small>${data.generated_at}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Course performance
        if (data.course_performance) {
            html += '<h6 class="mt-4">Course Performance</h6>';
            html += '<div class="table-responsive"><table class="table table-striped">';
            html += '<thead><tr><th>Course</th><th>Students</th><th>Avg Attendance</th><th>Avg Score</th><th>Completion Rate</th></tr></thead><tbody>';
            
            Object.keys(data.course_performance).forEach(courseId => {
                const course = data.course_performance[courseId];
                html += `<tr>
                    <td>${course.course_name}</td>
                    <td>${course.total_students}</td>
                    <td>${course.average_attendance}%</td>
                    <td>${course.average_exam_score}</td>
                    <td>${course.completion_rate}%</td>
                </tr>`;
            });
            
            html += '</tbody></table></div>';
        }

        return html;
    }

    function displayAttendanceReport(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Sessions</h5>
                            <h3>${data.total_sessions}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Present</h5>
                            <h3>${data.present_sessions}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5>Absent</h5>
                            <h3>${data.absent_sessions}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Attendance Rate</h5>
                            <h3>${data.overall_attendance_rate}%</h3>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function displayFinancialReport(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Revenue</h5>
                            <h3>Rs. ${data.total_revenue.toLocaleString()}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h3>${data.total_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Average Payment</h5>
                            <h3>Rs. ${data.average_payment.toLocaleString()}</h3>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function displayModuleReport(data) {
        let html = `
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Assignments</h5>
                            <h3>${data.total_assignments}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Unique Students</h5>
                            <h3>${data.unique_students}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Unique Modules</h5>
                            <h3>${data.unique_modules}</h3>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Generated At</h5>
                            <small>${data.generated_at}</small>
                        </div>
                    </div>
                </div>
            </div>
        `;

        return html;
    }

    function clearFilters() {
        $('#reportType').val('');
        $('#startDate').val('');
        $('#endDate').val('');
        $('#location').val('');
        $('#courseId').val('');
        $('#semester').val('');
        $('#exportFormat').val('json');
        $('#reportResults').hide();
        $('#semesterFilter').hide();
    }
});
</script>
@endsection 