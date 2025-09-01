<?php $__env->startSection('title', 'NEBULA | Timetable Management'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center mb-4">Timetable Management</h2>
                <hr>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs mb-4" id="timetableTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="degree-tab" data-bs-toggle="tab"
                            data-bs-target="#degree-timetable" type="button" role="tab" aria-controls="degree-timetable"
                            aria-selected="true">
                            Degree Programs
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="certificate-tab" data-bs-toggle="tab"
                            data-bs-target="#certificate-timetable" type="button" role="tab"
                            aria-controls="certificate-timetable" aria-selected="false">
                            Certificate Programs
                        </button>
                    </li>
                </ul>

                <!-- Degree Programs Tab -->
                <div class="tab-content" id="timetableTabsContent">
                    <div class="tab-pane fade show active" id="degree-timetable" role="tabpanel"
                        aria-labelledby="degree-tab">
                        <!-- Degree Filters -->
                        <div id="degree-filters" class="mb-4">
                            <div class="mb-3 row align-items-center">
                                <label for="degree_location" class="col-sm-3 col-form-label fw-bold">Location<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="degree_location" name="location" required>
                                        <option value="" selected disabled>Select Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="degree_course" class="col-sm-3 col-form-label fw-bold">Course<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="degree_course" name="course_id" required>
                                        <option selected disabled value="">Select Course</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="degree_intake" class="col-sm-3 col-form-label fw-bold">Intake<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="degree_intake" name="intake_id" required>
                                        <option selected disabled value="">Select Intake</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="degree_semester" class="col-sm-3 col-form-label fw-bold">Semester<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="degree_semester" name="semester" required>
                                        <option selected disabled value="">Select Semester</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="degree_start_date" class="col-sm-3 col-form-label fw-bold">Semester Start
                                    Date<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="degree_start_date" name="start_date"
                                        required readonly>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="degree_end_date" class="col-sm-3 col-form-label fw-bold">End Date<span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="degree_end_date" name="end_date" required
                                        readonly>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="button" class="btn btn-primary" id="showTimetableBtn">Show Timetable</button>
                                    <div id="degree_download_buttons" style="display:none;display:inline-block;margin-left:8px;">
                                        <button type="button" class="btn btn-outline-secondary" id="downloadPdfBtn">Download PDF</button>
                                        <!-- Simplified actions: direct week/month PDF -->
                                        <button type="button" class="btn btn-success" id="downloadWeekPdfBtn" style="margin-left:12px;">Download Week PDF</button>
                                        <button type="button" class="btn btn-dark" id="downloadMonthPdfBtn" style="margin-left:8px;">Download Month PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- FullCalendar Container -->
                        <div class="mt-4" id="degreeTimetableSection" style="display:none;">
                            <div id="calendar"></div>
                        </div>
                    </div>
                    <!-- Certificate Programs Tab Pane (mirrors degree UI) -->
                    <div class="tab-pane fade" id="certificate-timetable" role="tabpanel" aria-labelledby="certificate-tab">
                        <div id="certificate-filters" class="mb-4">
                            <div class="mb-3 row align-items-center">
                                <label for="certificate_location" class="col-sm-3 col-form-label fw-bold">Location<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="certificate_location" name="location">
                                        <option value="" selected disabled>Select Location</option>
                                        <option value="Welisara">Nebula Institute of Technology - Welisara</option>
                                        <option value="Moratuwa">Nebula Institute of Technology - Moratuwa</option>
                                        <option value="Peradeniya">Nebula Institute of Technology - Peradeniya</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="certificate_course" class="col-sm-3 col-form-label fw-bold">Course<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="certificate_course" name="course_id">
                                        <option selected disabled value="">Select Course</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="certificate_intake" class="col-sm-3 col-form-label fw-bold">Intake<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="certificate_intake" name="intake_id">
                                        <option selected disabled value="">Select Intake</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="certificate_semester" class="col-sm-3 col-form-label fw-bold">Semester<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select" id="certificate_semester" name="semester">
                                        <option selected disabled value="">Select Semester</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="certificate_start_date" class="col-sm-3 col-form-label fw-bold">Semester Start Date<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="certificate_start_date" name="start_date" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <label for="certificate_end_date" class="col-sm-3 col-form-label fw-bold">End Date<span class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" id="certificate_end_date" name="end_date" readonly>
                                </div>
                            </div>

                            <div class="mb-3 row align-items-center">
                                <div class="col-sm-9 offset-sm-3">
                                    <button type="button" class="btn btn-primary" id="certificate_showTimetableBtn">Show Timetable</button>
                                    <div id="certificate_download_buttons" style="display:none;display:inline-block;margin-left:8px;">
                                        <button type="button" class="btn btn-outline-secondary" id="certificate_downloadPdfBtn">Download PDF</button>
                                        <button type="button" class="btn btn-success" id="certificate_downloadWeekPdfBtn" style="margin-left:12px;">Download Week PDF</button>
                                        <button type="button" class="btn btn-dark" id="certificate_downloadMonthPdfBtn" style="margin-left:8px;">Download Month PDF</button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4" id="certificateTimetableSection" style="display:none;">
                            <div id="certificate_calendar"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal for subject selection -->
    <div id="subjectSelectionModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="subjectModalLabel"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="subjectModalLabel">Select Subjects and Duration</h5>
                    <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <!-- Hidden input to store the selected date -->
                    <input type="hidden" id="selectedDate">

                    <!-- Display selected date -->
                    <div class="mb-3 row">
                        <label for="selected_date_display" class="col-sm-3 col-form-label fw-bold">Date</label>
                        <div class="col-sm-9">
                            <input type="text" class="form-control" id="selected_date_display" readonly>
                        </div>
                    </div>

                    <!-- Multi-subject and Duration Form -->
                    <div id="subjectList">
                        <!-- Each block groups subject + duration + time -->
                        <div class="subject-block">
                            <div class="mb-3 row align-items-center">
                                <label for="degree_subject_0" class="col-sm-3 col-form-label fw-bold">Subject <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <select class="form-select subject-select" id="degree_subject_0" name="subject_ids[]"
                                        required>
                                        <option selected disabled value="">Select Subject</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label for="degree_duration_0" class="col-sm-3 col-form-label fw-bold">Duration (Minutes) <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9">
                                    <input type="number" class="form-control duration-input" id="degree_duration_0"
                                        name="durations[]" required>
                                </div>
                            </div>
                            <div class="mb-3 row align-items-center">
                                <label for="degree_time_0" class="col-sm-3 col-form-label fw-bold">Time <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-9 d-flex">
                                    <input type="time" class="form-control time-input me-2" id="degree_time_0" name="times[]"
                                        required>
                                    <button type="button" class="btn btn-outline-danger btn-sm remove-subject-btn" style="display:none;">Remove</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="assignSubjectBtn">Assign Subjects</button>
                </div>
            </div>
        </div>
    </div>


    <!-- PDF Filter Modal -->
    <div id="downloadPdfModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="downloadPdfLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Download Timetable PDF (apply filters)</h5>
                        <button type="button" class="close" data-dismiss="modal" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
          <div class="modal-body">
            <div class="mb-3">
              <label class="form-label">Location</label>
              <select class="form-select" id="pdf_location">
                <option value="">All</option>
                <option value="Welisara">Welisara</option>
                <option value="Moratuwa">Moratuwa</option>
                <option value="Peradeniya">Peradeniya</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">Course</label>
              <select class="form-select" id="pdf_course">
                <option value="">All</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">From</label>
              <input type="date" class="form-control" id="pdf_from">
            </div>
            <div class="mb-3">
              <label class="form-label">To</label>
              <input type="date" class="form-control" id="pdf_to">
            </div>
          </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Close</button>
                        <button id="generatePdfBtn" type="button" class="btn btn-primary">Generate PDF</button>
                    </div>
        </div>
      </div>
    </div>

    <!-- FullCalendar v5 (modern build) -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet" />

    <!-- jQuery (kept for other UI code) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Moment.js (used in other parts of this page) -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <!-- FullCalendar v5 bundle -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>

    <!-- jsPDF for PDF export -->
    <script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>

    <!-- html2canvas (needed to snapshot table) -->
    <script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(document).ready(function () {
            var latestEventsRaw = []; // raw server rows
            var latestFcEvents = [];  // mapped fullcalendar events

            // build available periods (weeks/months) based on semester start/end
            function buildPeriodsFromSemester() {
                var s = $('#degree_start_date').val();
                var e = $('#degree_end_date').val();
                $('#tablePeriod').empty().append('<option value="">Select period</option>');
                if (!s || !e || !moment(s).isValid() || !moment(e).isValid()) return;
                var start = moment(s).startOf('day');
                var end = moment(e).endOf('day');
                var days = end.diff(start, 'days') + 1;
                // weeks
                var weeks = Math.ceil(days / 7);
                for (var i = 1; i <= weeks; i++) {
                    $('#tablePeriod').append('<option data-type="week" value="' + i + '">Week ' + i + '</option>');
                }
                // months
                var months = end.diff(start, 'months') + 1;
                for (var m = 1; m <= months; m++) {
                    $('#tablePeriod').append('<option data-type="month" value="' + m + '">Month ' + m + '</option>');
                }
                // auto-select first available period and mark UI
                var first = $('#tablePeriod option').not('[value=""]').first().val();
                if (first) {
                    $('#tablePeriod').val(first);
                    $('#tableViewType').val('week'); // default to week view for clarity
                }
            }

            // recalc periods when semester changes
            $(document).on('change', '#degree_semester, #degree_start_date, #degree_end_date', function () {
                buildPeriodsFromSemester();
            });

            // render week-grid view (Week N of semester)
            function renderWeekTable(weekIndex) {
                var s = $('#degree_start_date').val();
                if (!s) { alert('Set semester start date'); return; }
                var start = moment(s).startOf('day').add((weekIndex - 1) * 7, 'days');
                var days = [];
                for (var d = 0; d < 7; d++) days.push(start.clone().add(d, 'days'));
                var html = '<div id="tableViewWrapper" style="overflow:auto;"><table class="table table-bordered" style="min-width:100%;">';
                html += '<thead><tr><th style="width:100px">Time</th>';
                days.forEach(function (dt) { html += '<th style="text-align:center;">' + dt.format('dddd') + '</th>'; });
                html += '</tr></thead><tbody>';
                var startHour = 7, endHour = 18;
                for (var h = startHour; h <= endHour; h++) {
                    html += '<tr>';
                    html += '<td style="border:1px solid #000;padding:6px;font-weight:600;">' + moment({ hour: h }).format('HH:mm') + '</td>';
                    for (var c = 0; c < 7; c++) {
                        var day = days[c];
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            try {
                                var evStart = moment(ev.start);
                                var evEnd = ev.end ? moment(ev.end) : evStart.clone().add(1, 'minutes');
                                var rowStart = day.clone().hour(h).minute(0).second(0);
                                var rowEnd = rowStart.clone().add(1, 'hour');
                                return evStart.isBefore(rowEnd) && evEnd.isAfter(rowStart);
                            } catch (e) { return false; }
                        });
                        html += '<td style="border:1px solid #000;padding:6px;vertical-align:top;min-height:40px;">';
                        if (cellEvents.length) {
                            cellEvents.forEach(function (ce) {
                                var st = moment(ce.start).format('HH:mm'), en = ce.end ? moment(ce.end).format('HH:mm') : '';
                                html += '<div class="badge bg-primary text-white mb-1" style="display:block;">' + ce.title + '</div>';
                                html += '<div style="font-size:0.85em;color:#333;">' + st + (en ? ' - ' + en : '') + '</div>';
                            });
                        } else {
                            html += '&nbsp;';
                        }
                        html += '</td>';
                    }
                    html += '</tr>';
                }
                html += '</tbody></table></div>';
                $('#degreeTimetableSection').hide();
                // show a new container for table view (create if not exists)
                if (!$('#tableViewSection').length) {
                    $('<div id="tableViewSection" class="mt-4 card p-3"><h5 id="tableViewTitle"></h5><div id="tableViewContainer"></div></div>').insertAfter('#degreeTimetableSection');
                }
                $('#tableViewTitle').text('Week ' + weekIndex);
                $('#tableViewContainer').html(html);
                $('#tableViewSection').show();
            }

            // render month-grid view (Month N of semester)
            function renderMonthTable(monthIndex) {
                var s = $('#degree_start_date').val();
                if (!s) { alert('Set semester start date'); return; }
                var monthStart = moment(s).startOf('day').add((monthIndex - 1), 'months');
                var monthEnd = monthStart.clone().endOf('month');
                // build matrix weeks for this month
                var gridStart = monthStart.clone().startOf('week'); // sunday-start
                var gridEnd = monthEnd.clone().endOf('week');
                var curr = gridStart.clone();
                var html = '<div id="tableViewWrapper" style="overflow:auto;"><table class="table table-bordered" style="min-width:100%;">';
                // header Mon-Sun
                html += '<thead><tr><th style="width:100px">Week</th>';
                var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                days.forEach(function (d) { html += '<th style="text-align:center;">' + d + '</th>'; });
                html += '</tr></thead><tbody>';
                var weekNo = 1;
                while (curr.isSameOrBefore(gridEnd)) {
                    html += '<tr><td style="font-weight:600;">Week ' + weekNo + '</td>';
                    for (var i = 0; i < 7; i++) {
                        var cellDate = curr.clone();
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            return moment(ev.start).isSame(cellDate, 'day');
                        });
                        html += '<td style="vertical-align:top;min-width:140px;padding:6px;">';
                        if (cellEvents.length) {
                            cellEvents.forEach(function (ce) {
                                var st = moment(ce.start).format('HH:mm'), en = ce.end ? moment(ce.end).format('HH:mm') : '';
                                html += '<div class="badge bg-primary text-white mb-1" style="display:block;">' + ce.title + '</div>';
                                html += '<div style="font-size:0.85em;color:#333;">' + st + (en ? ' - ' + en : '') + '</div>';
                            });
                        } else {
                            html += '&nbsp;';
                        }
                        html += '</td>';
                        curr.add(1, 'day');
                    }
                    html += '</tr>';
                    weekNo++;
                }
                html += '</tbody></table></div>';
                $('#degreeTimetableSection').hide();
                if (!$('#tableViewSection').length) {
                    $('<div id="tableViewSection" class="mt-4 card p-3"><h5 id="tableViewTitle"></h5><div id="tableViewContainer"></div></div>').insertAfter('#degreeTimetableSection');
                }
                $('#tableViewTitle').text('Month ' + monthIndex);
                $('#tableViewContainer').html(html);
                $('#tableViewSection').show();
            }

            // when user clicks render table
            $('#renderTableBtn').on('click', function () {
                var sel = $('#tablePeriod').val();
                if (!sel) { alert('Choose a period'); return; }
                var dtype = $('#tablePeriod option:selected').data('type') || $('#tableViewType').val();
                if (dtype === 'week') renderWeekTable(parseInt(sel, 10));
                else renderMonthTable(parseInt(sel, 10));
            });

            // Download visible table as PDF
            $('#downloadTablePdfBtn').on('click', function () {
                var container = document.getElementById('tableViewContainer');
                if (!container || $('#tableViewSection').is(':hidden')) { alert('Render a table first'); return; }
                html2canvas(container, { scale: 2 }).then(function (canvas) {
                    var img = canvas.toDataURL('image/png');
                    const { jsPDF } = window.jspdf;
                    var pdf = new jsPDF('l', 'pt', 'a4');
                    var pw = pdf.internal.pageSize.getWidth() - 40;
                    var ph = pdf.internal.pageSize.getHeight() - 40;
                    var ih = canvas.height * (pw / canvas.width);
                    if (ih <= ph) {
                        pdf.addImage(img, 'PNG', 20, 20, pw, ih);
                    } else {
                        // split across pages
                        var ratio = pw / canvas.width;
                        var total = canvas.height;
                        var rendered = 0;
                        while (rendered < total) {
                            var tmpCanvas = document.createElement('canvas');
                            var tmpCtx = tmpCanvas.getContext('2d');
                            tmpCanvas.width = canvas.width;
                            tmpCanvas.height = Math.min(canvas.height - rendered, Math.floor(ph / ratio));
                            tmpCtx.drawImage(canvas, 0, rendered, canvas.width, tmpCanvas.height, 0, 0, tmpCanvas.width, tmpCanvas.height);
                            var tmpImg = tmpCanvas.toDataURL('image/png');
                            if (rendered > 0) pdf.addPage();
                            pdf.addImage(tmpImg, 'PNG', 20, 20, pw, tmpCanvas.height * ratio);
                            rendered += tmpCanvas.height;
                        }
                    }
                    var title = $('#tableViewTitle').text() || 'timetable';
                    pdf.save(title.replace(/\s+/g, '_') + '.pdf');
                }).catch(function (err) {
                    console.error(err); alert('Failed to generate PDF.');
                });
            });
            // — end additions
            // Fetch courses based on location
            $('#degree_location').change(function () {
                var location = $(this).val();
                if (location) {
                    $.ajax({
                        url: '/get-courses-by-location',
                        type: 'GET',
                        data: { location: location },
                        success: function (data) {
                            console.log("Courses data received:", data);
                            if (data.success) {
                                $('#degree_course').empty();
                                $('#degree_course').append('<option selected disabled value="">Select Course</option>');
                                if (data.courses && data.courses.length > 0) {
                                    $.each(data.courses, function (index, course) {
                                        $('#degree_course').append('<option value="' + course.course_id + '">' + course.course_name + '</option>');
                                    });
                                    $('#degree_course').prop('disabled', false);
                                } else {
                                    $('#degree_course').append('<option disabled>No courses found</option>');
                                    $('#degree_course').prop('disabled', true);
                                }
                            } else {
                                console.error('No courses available for the selected location.');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching courses:', error);
                        }
                    });
                }
            });

            // Certificate: Fetch courses based on certificate location
            $('#certificate_location').change(function () {
                var location = $(this).val();
                if (location) {
                    $.ajax({
                        url: '/get-courses-by-location',
                        type: 'GET',
                        data: { location: location },
                        success: function (data) {
                            $('#certificate_course').empty();
                            $('#certificate_course').append('<option selected disabled value="">Select Course</option>');
                            if (data && data.courses && data.courses.length > 0) {
                                $.each(data.courses, function (i, course) {
                                    $('#certificate_course').append('<option value="' + course.course_id + '">' + course.course_name + '</option>');
                                });
                                $('#certificate_course').prop('disabled', false);
                            } else {
                                $('#certificate_course').append('<option disabled>No courses found</option>');
                                $('#certificate_course').prop('disabled', true);
                            }
                        },
                        error: function () { console.error('Error fetching certificate courses'); }
                    });
                }
            });

            // Fetch intakes based on course and location
            $('#degree_course').change(function () {
                var courseId = $(this).val();
                var location = $('#degree_location').val();
                if (courseId && location) {
                    $.ajax({
                        url: '/get-intakes/' + courseId + '/' + location,
                        type: 'GET',
                        success: function (data) {
                            console.log("Intakes data received:", data); // Debug log for intakes

                            $('#degree_intake').empty();
                            $('#degree_intake').append('<option selected disabled value="">Select Intake</option>');

                            if (data.intakes && data.intakes.length > 0) {
                                $.each(data.intakes, function (index, intake) {
                                    $('#degree_intake').append('<option value="' + intake.intake_id + '">' + intake.batch + '</option>');
                                });
                                $('#degree_intake').prop('disabled', false);
                            } else {
                                $('#degree_intake').append('<option disabled>No intakes found</option>');
                                $('#degree_intake').prop('disabled', true);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching intakes:', error);
                        }
                    });
                }
            });

            // Certificate: Fetch intakes when certificate course changes
            $('#certificate_course').change(function () {
                var courseId = $(this).val();
                var location = $('#certificate_location').val();
                if (courseId && location) {
                    $.ajax({
                        url: '/get-intakes/' + courseId + '/' + location,
                        type: 'GET',
                        success: function (data) {
                            $('#certificate_intake').empty();
                            $('#certificate_intake').append('<option selected disabled value="">Select Intake</option>');
                            if (data.intakes && data.intakes.length > 0) {
                                $.each(data.intakes, function (index, intake) {
                                    $('#certificate_intake').append('<option value="' + intake.intake_id + '">' + intake.batch + '</option>');
                                });
                                $('#certificate_intake').prop('disabled', false);
                            } else {
                                $('#certificate_intake').append('<option disabled>No intakes found</option>');
                                $('#certificate_intake').prop('disabled', true);
                            }
                        },
                        error: function () { console.error('Error fetching certificate intakes'); }
                    });
                }
            });

            // Fetch semesters based on course and intake
            $('#degree_intake').change(function () {
                var intakeId = $(this).val();
                var courseId = $('#degree_course').val();
                if (intakeId && courseId) {
                    $.ajax({
                        url: '/timetable/get-semesters',
                        type: 'GET',
                        data: { course_id: courseId, intake_id: intakeId },
                        success: function (data) {
                            console.log("Semesters data received:", data); // Debug log for semesters

                            $('#degree_semester').empty();
                            $('#degree_semester').append('<option selected disabled value="">Select Semester</option>');

                            if (data.semesters && data.semesters.length > 0) {
                                $.each(data.semesters, function (index, semester) {
                                    // include start/end dates in option attributes so we can auto-fill date inputs
                                    $('#degree_semester').append('<option value="' + semester.id + '" data-start="' + (semester.start_date || '') + '" data-end="' + (semester.end_date || '') + '">' + semester.name + '</option>');
                                });
                                $('#degree_semester').prop('disabled', false);
                            } else {
                                $('#degree_semester').append('<option disabled>No semesters found</option>');
                                $('#degree_semester').prop('disabled', true);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching semesters:', error);
                        }
                    });
                }
            });

            // Certificate: Fetch semesters for certificate intake/course
            $('#certificate_intake').change(function () {
                var intakeId = $(this).val();
                var courseId = $('#certificate_course').val();
                if (intakeId && courseId) {
                    $.ajax({
                        url: '/timetable/get-semesters',
                        type: 'GET',
                        data: { course_id: courseId, intake_id: intakeId },
                        success: function (data) {
                            $('#certificate_semester').empty();
                            $('#certificate_semester').append('<option selected disabled value="">Select Semester</option>');
                            if (data.semesters && data.semesters.length > 0) {
                                $.each(data.semesters, function (index, semester) {
                                    $('#certificate_semester').append('<option value="' + semester.id + '" data-start="' + (semester.start_date || '') + '" data-end="' + (semester.end_date || '') + '">' + semester.name + '</option>');
                                });
                                $('#certificate_semester').prop('disabled', false);
                            } else {
                                $('#certificate_semester').append('<option disabled>No semesters found</option>');
                                $('#certificate_semester').prop('disabled', true);
                            }
                        },
                        error: function () { console.error('Error fetching certificate semesters'); }
                    });
                }
            });

            // Auto-fill semester start/end date when semester selected (uses data attributes above)
            $(document).on('change', '#degree_semester', function () {
                var selected = $(this).find('option:selected');
                var start = selected.data('start') || '';
                var end = selected.data('end') || '';
                // normalize to yyyy-mm-dd if moment can parse it
                if (start && moment(start).isValid()) start = moment(start).format('YYYY-MM-DD');
                if (end && moment(end).isValid()) end = moment(end).format('YYYY-MM-DD');
                $('#degree_start_date').val(start);
                $('#degree_end_date').val(end);
                // show degree download buttons when semester chosen
                if ($(this).val()) {
                    $('#degree_download_buttons').show();
                } else {
                    $('#degree_download_buttons').hide();
                }
            });

            // Certificate: autofill start/end and show download buttons when semester chosen
            $(document).on('change', '#certificate_semester', function () {
                var selected = $(this).find('option:selected');
                var start = selected.data('start') || '';
                var end = selected.data('end') || '';
                if (start && moment(start).isValid()) start = moment(start).format('YYYY-MM-DD');
                if (end && moment(end).isValid()) end = moment(end).format('YYYY-MM-DD');
                $('#certificate_start_date').val(start);
                $('#certificate_end_date').val(end);
                // show certificate download buttons now that semester is selected
                if ($(this).val()) {
                    $('#certificate_download_buttons').show();
                } else {
                    $('#certificate_download_buttons').hide();
                }
            });

            // Fetch available subjects based on semester
            $('#degree_semester').change(function () {
                var semesterId = $(this).val();
                var courseId = $('#degree_course').val();
                if (semesterId && courseId) {
                    $.ajax({
                        url: '/get-modules-by-semester',
                        type: 'GET',
                        data: { semester_id: semesterId, course_id: courseId },
                        success: function (data) {
                            console.log("Modules data received:", data); // Debug log for modules

                            if (data.modules && data.modules.length > 0) {
                                $('#degree_subject_0').empty();
                                $('#degree_subject_0').append('<option selected disabled value="">Select Subject</option>');
                                $.each(data.modules, function (index, module) {
                                    console.log("Appending subject:", module); // Debug log for each module being added
                                    $('#degree_subject_0').append('<option value="' + module.module_id + '">' + module.module_name + ' (' + module.module_code + ')</option>');
                                });
                                $('#degree_subject_0').prop('disabled', false);
                            } else {
                                $('#degree_subject_0').empty();
                                $('#degree_subject_0').append('<option value="" disabled>No subjects found</option>');
                                $('#degree_subject_0').prop('disabled', true);
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('Error fetching subjects:', error);
                        }
                    });
                }
            });

            // Certificate: when certificate semester selected, fetch modules into certificate subject selects if needed
            $('#certificate_semester').change(function () {
                var semesterId = $(this).val();
                var courseId = $('#certificate_course').val();
                if (semesterId && courseId) {
                    $.ajax({
                        url: '/get-modules-by-semester',
                        type: 'GET',
                        data: { semester_id: semesterId, course_id: courseId },
                        success: function (data) {
                            // if you have certificate-specific subject selects, populate them here
                            console.log('Certificate modules fetched', data);
                        },
                        error: function () { console.error('Error fetching certificate modules'); }
                    });
                }
            });

            // Initialize FullCalendar v5
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                allDaySlot: false,
                editable: true,
                selectable: true,
                // show hours from 07:00 to 18:00
                slotMinTime: '07:00:00',
                slotMaxTime: '18:00:00',
                slotDuration: '00:15:00',
                nowIndicator: true,
                expandRows: true,
                firstDay: 0, // Sunday
                events: [],
                eventDidMount: function(info) {
                    // custom styling similar to previous implementation
                    info.el.style.backgroundColor = '#007bff';
                    info.el.style.borderColor = '#0056b3';
                    info.el.style.color = '#fff';
                    info.el.style.opacity = '0.95';
                    info.el.querySelector('.fc-event-title')?.style && (info.el.querySelector('.fc-event-title').style.fontWeight = '600');
                },
                eventClick: function(info) {
                    var event = info.event;
                    if (event && event.start) {
                        var eventTime = moment(event.start).format('HH:mm');
                        $('#degree_time_0').val(eventTime);
                    }

                    if (confirm('Do you want to delete this timetable event?')) {
                        var id = event.id;
                        if (!id) { alert('Unable to determine event id to delete'); return; }

                        // remove visually first
                        try { event.remove(); } catch(e) { console.warn('client remove failed', e); }

                        $.ajax({
                            url: '/timetable/delete-event',
                            type: 'POST',
                            data: { id: id },
                            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
                            success: function (res) {
                                if (res && res.success) {
                                    calendar.refetchEvents();
                                } else {
                                    alert('Delete failed: ' + (res.message || 'unknown'));
                                    calendar.refetchEvents();
                                }
                            },
                            error: function (xhr) {
                                alert('Delete failed');
                                console.error('delete error', xhr);
                                calendar.refetchEvents();
                            }
                        });
                    }
                },
                select: function(selectionInfo) {
                    var startDate = moment(selectionInfo.start).format('YYYY-MM-DD');
                    var startTime = moment(selectionInfo.start).format('HH:mm');
                    $('#selectedDate').val(startDate);
                    $('#selected_date_display').val(startDate);
                    $('#degree_time_0').val(startTime);
                    $('#degree_duration_0').val('');
                    $('#degree_subject_0').val('');
                    $('#subjectSelectionModal').modal('show');
                    calendar.unselect();
                },
                dateClick: function(info) {
                    var d = moment(info.date).format('YYYY-MM-DD');
                    var t = moment(info.date).format('HH:mm');
                    $('#selectedDate').val(d);
                    $('#selected_date_display').val(d);
                    $('#degree_time_0').val(t);
                    $('#degree_duration_0').val('');
                    $('#degree_subject_0').val('');
                    $('#subjectSelectionModal').modal('show');
                },
                eventOverlap: true,
            });

            // attach calendar instance to window for debug access
            window.__nebulaCalendar = calendar;
            calendar.render();

            // Populate PDF modal course select when course list loads (reuse degree_course change)
            $('#degree_course').on('change', function () {
                // also update pdf_course options
                var courseId = $(this).val();
                // copy current options
                $('#pdf_course').empty().append('<option value="">All</option>');
                $('#degree_course option').each(function () {
                    var val = $(this).attr('value');
                    var txt = $(this).text();
                    if (val) $('#pdf_course').append('<option value="' + val + '">' + txt + '</option>');
                });
                if (courseId) $('#pdf_course').val(courseId);
            });

            // Show Timetable button click event to load events
            $('#showTimetableBtn').click(function () {
                var data = {
                    location: $('#degree_location').val(),
                    course_id: $('#degree_course').val(),
                    intake_id: $('#degree_intake').val(),
                    semester: $('#degree_semester').val(),
                    start_date: $('#degree_start_date').val(),
                    end_date: $('#degree_end_date').val()
                };
                console.log("Timetable data being sent:", data);

                $.ajax({
                    url: '/get-timetable-events',
                    type: 'GET',
                    data: data,
                    success: function (response) {
                        console.log("Timetable events received (raw):", response);

                        var eventsArray = [];
                        if (Array.isArray(response)) {
                            eventsArray = response;
                        } else if (response && Array.isArray(response.events)) {
                            eventsArray = response.events;
                        } else if (response && response.data && Array.isArray(response.data.events)) {
                            eventsArray = response.data.events;
                        } else if (response && response.data && Array.isArray(response.data)) {
                            eventsArray = response.data;
                        }

                        // show calendar section first so layout/scroll exists
                        $('#degreeTimetableSection').show();
                        // ensure calendar re-render (important when it was hidden)
                        setTimeout(function () { if (window.__nebulaCalendar) window.__nebulaCalendar.render(); }, 40);

                        // remove previous events safely
                        try {
                            if (window.__nebulaCalendar) {
                                try { window.__nebulaCalendar.removeAllEvents(); } catch(e) { console.warn('removeAllEvents failed', e); }
                            }
                        } catch (err) {
                            console.warn('Safe removeAllEvents error:', err);
                        }

                        if (!eventsArray || !eventsArray.length) {
                            console.info('No events returned.');
                            setTimeout(function () { if (window.__nebulaCalendar) window.__nebulaCalendar.render(); }, 50);
                            return;
                        }

                        // build robust FullCalendar events array, skip invalid rows
                        var fcEvents = [];
                        eventsArray.forEach(function (e, idx) {
                            var title = e.module_name || e.subject_name || e.title || 'Class';
                            var datePart = e.date || e.day || '';
                            var startTime = (e.time || '').toString().trim();
                            var endTime = (e.end_time || '').toString().trim();

                            // compute endTime from duration if needed
                            if (!endTime && e.duration) {
                                var mStartTmp = moment(startTime, ['HH:mm:ss','HH:mm','h:mm A']);
                                if (mStartTmp.isValid()) {
                                    endTime = mStartTmp.clone().add(parseInt(e.duration,10) || 0, 'minutes').format('HH:mm');
                                } else {
                                    endTime = startTime;
                                }
                            }

                            // require date and startTime to build ISO datetimes
                            if (!datePart || !startTime) {
                                console.warn('Skipping event missing date or start time', e);
                                return;
                            }

                            var mStart = moment(datePart + ' ' + startTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A', 'YYYY-MM-DD H:mm']);
                            var mEnd = null;
                            if (endTime) {
                                mEnd = moment(datePart + ' ' + endTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A', 'YYYY-MM-DD H:mm']);
                            } else {
                                // default duration 1 minute to avoid same-start/end problems
                                mEnd = mStart.clone().add(1, 'minutes');
                            }

                            if (!mStart.isValid() || !mEnd.isValid()) {
                                console.warn('Skipping invalid datetime event', e);
                                return;
                            }

                           // debug: log parsed start/end for each incoming row
                           console.log('Parsed event datetimes:', {
                               raw: e,
                               parsedStart: mStart.format('YYYY-MM-DD HH:mm:ss'),
                               parsedEnd: mEnd.format('YYYY-MM-DD HH:mm:ss')
                           });

                            // use local-formatted datetimes (no trailing Z) so FullCalendar places events correctly in week/day views
                            // use Date objects to avoid ISO parsing/timezone edge-cases in agendaWeek/Day
                            fcEvents.push({
                                id: (e.id !== undefined && e.id !== null) ? e.id : idx,
                                title: title,
                                // pass native Date objects to avoid parsing ambiguity
                                start: mStart.toDate(),
                                end: mEnd.toDate(),
                                allDay: false,
                                extendedProps: e,
                                overlap: true
                            });
                        });

                        // add events after short delay so calendar layout is ready (fix scrollTop errors)
                        setTimeout(function () {
                            console.log('Adding events to FullCalendar, count:', fcEvents.length, fcEvents);
                            // dedupe events by id (server may accidentally return duplicates)
                            var seen = {};
                            var uniqueFc = [];
                            fcEvents.forEach(function(ev){
                                var key = ev.id || (ev.start && ev.start.toString()) || JSON.stringify(ev);
                                if (!seen[key]) { seen[key] = true; uniqueFc.push(ev); }
                            });
                            console.log('Unique events after dedupe:', uniqueFc.length, uniqueFc);
                            try {
                                if (window.__nebulaCalendar) {
                                    try { window.__nebulaCalendar.removeAllEvents(); } catch(e) { console.warn('removeAllEvents failed', e); }
                                    try { var sources = window.__nebulaCalendar.getEventSources(); sources.forEach(function(s){ try { s.remove(); } catch(e){} }); } catch(e) { /* ignore */ }
                                }
                            } catch (ex) { console.warn('Error clearing previous events:', ex); }

                            try { if (window.__nebulaCalendar) window.__nebulaCalendar.addEventSource(uniqueFc); } catch(e) { console.warn('addEventSource failed', e); }
                            try { if (window.__nebulaCalendar) window.__nebulaCalendar.refetchEvents(); } catch(e) { /* ignore */ }

                            // debug: inspect what FullCalendar actually stored
                            setTimeout(function () {
                                try {
                                    var stored = (window.__nebulaCalendar) ? window.__nebulaCalendar.getEvents() : [];
                                    console.log('Client events in calendar (count):', stored.length);
                                    stored.forEach(function (ev, i) {
                                        try {
                                            var stType = ev.start ? Object.prototype.toString.call(ev.start) : 'null';
                                            var enType = ev.end ? Object.prototype.toString.call(ev.end) : 'null';
                                            var stStr = ev.start ? (moment(ev.start).isValid() ? moment(ev.start).format('YYYY-MM-DD HH:mm:ss') : ev.start.toString()) : 'null';
                                            var enStr = ev.end ? (moment(ev.end).isValid() ? moment(ev.end).format('YYYY-MM-DD HH:mm:ss') : ev.end.toString()) : 'null';
                                            console.log('Stored event', i, 'id:', ev.id, 'title:', ev.title, 'startType:', stType, 'endType:', enType, 'start:', stStr, 'end:', enStr, ev);
                                        } catch (inner) { console.warn('Error inspecting event', inner); }
                                    });
                                } catch (e) { console.warn('Failed to read client events', e); }
                            }, 150);
                        }, 120);

                        // keep raw server data
                        latestEventsRaw = eventsArray;
                        // keep fc events
                        latestFcEvents = fcEvents;

                        // AUTO UX: build periods and auto-render first period so users don't need to pick
                        buildPeriodsFromSemester();
                        // select first period option (if exists) and render automatically
                        var sel = $('#tablePeriod option').not('[value=""]').first().val();
                        if (sel) {
                            var dtype = $('#tablePeriod option:selected').data('type') || $('#tableViewType').val() || 'week';
                            $('#tablePeriod').val(sel);
                            // render selected period automatically
                            if (dtype === 'week') renderWeekTable(parseInt(sel, 10));
                            else renderMonthTable(parseInt(sel, 10));
                        }
                    },
                    error: function (xhr, status, error) {
                        alert('Error occurred while fetching the timetable');
                    }
                });
            });

            // Download PDF button shows filter modal
            $('#downloadPdfBtn').on('click', function () {
                // copy current values into modal
                $('#pdf_location').val($('#degree_location').val() || '');
                $('#pdf_course').empty().append('<option value="">All</option>');
                $('#degree_course option').each(function () {
                    var v = $(this).val();
                    var t = $(this).text();
                    if (v) $('#pdf_course').append('<option value="' + v + '">' + t + '</option>');
                });
                $('#pdf_course').val($('#degree_course').val() || '');
                // autofill date range if chosen
                $('#pdf_from').val($('#degree_start_date').val() || '');
                $('#pdf_to').val($('#degree_end_date').val() || '');
                $('#downloadPdfModal').modal('show');
            });

            // Generate PDF from filtered events (simple list PDF)
            $('#generatePdfBtn').off('click').on('click', function () {
                // include semester (fall back to main form) — server validation needs this
                var filters = {
                    location: $('#pdf_location').val() || $('#degree_location').val() || '',
                    course_id: $('#pdf_course').val() || $('#degree_course').val() || '',
                    intake_id: $('#pdf_intake').val() || $('#degree_intake').val() || '',
                    semester: $('#pdf_semester')?.val() || $('#degree_semester').val() || '',
                    start_date: $('#pdf_from').val() || $('#degree_start_date').val() || '',
                    end_date: $('#pdf_to').val() || $('#degree_end_date').val() || ''
                };

                // required validation client-side to avoid 422
                if (!filters.course_id || !filters.intake_id || !filters.semester) {
                    alert('Please select Course, Intake and Semester before generating PDF.');
                    return;
                }

                $('#downloadPdfModal').modal('hide');

                $.ajax({
                    url: '/get-timetable-events',
                    type: 'GET',
                    data: filters,
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
                    success: function (response) {
                        var events = [];
                        if (Array.isArray(response)) events = response;
                        else if (response && Array.isArray(response.events)) events = response.events;
                        else if (response && response.data && Array.isArray(response.data.events)) events = response.data.events;
                        else if (response && response.data && Array.isArray(response.data)) events = response.data;

                        if (!events || !events.length) {
                            alert('No events found for selected filters.');
                            return;
                        }

                        const { jsPDF } = window.jspdf;
                        var doc = new jsPDF({ unit: 'pt', format: 'a4' });
                        var y = 40;
                        doc.setFontSize(14);
                        doc.text('Timetable Export', 40, y);
                        doc.setFontSize(10);
                        y += 20;
                        events.forEach(function (ev, i) {
                            var date = ev.date || ev.day || '';
                            var start = ev.time || '';
                            var dur = ev.duration ? (ev.duration + ' min') : (ev.end_time ? (start + ' - ' + ev.end_time) : '');
                            var title = ev.module_name || ev.subject_name || ev.title || 'Class';
                            var line = (i+1) + '. ' + (date ? (date + ' ') : '') + (start ? (start + ' ') : '') + '- ' + title + ' (' + dur + ')';
                            var split = doc.splitTextToSize(line, 520);
                            doc.text(split, 40, y);
                            y += (split.length * 12) + 6;
                            if (y > 740) { doc.addPage(); y = 40; }
                        });

                        doc.save('timetable.pdf');
                    },
                    error: function (xhr) {
                        var msg = 'Failed to fetch events for PDF.';
                        try {
                            var json = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                            if (json && json.errors) {
                                var first = Object.values(json.errors)[0];
                                msg = Array.isArray(first) ? first[0] : first;
                            } else if (json && json.message) {
                                msg = json.message;
                            } else if (xhr.responseText) {
                                msg = xhr.responseText;
                            }
                        } catch (e) {}
                        alert(msg);
                        console.error('PDF fetch error:', xhr);
                    }
                });
            });

            // --- Simplified download: Week / Month PDF (no period selector) ---
            function getSemesterStart() {
                var s = $('#degree_start_date').val();
                if (!s || !moment(s).isValid()) return null;
                return moment(s).startOf('day');
            }

            function computeWeekIndexForDate(dtMoment) {
                var start = getSemesterStart();
                if (!start) return 1;
                var diffDays = dtMoment.startOf('day').diff(start, 'days');
                if (diffDays < 0) return 1;
                return Math.floor(diffDays / 7) + 1;
            }

            function computeMonthIndexForDate(dtMoment) {
                var start = getSemesterStart();
                if (!start) return 1;
                return dtMoment.startOf('month').diff(start.startOf('month'), 'months') + 1;
            }

            // build week table HTML (no raw dates in output); weekIndex = 1..N
            function buildWeekHtml(weekIndex) {
                var start = getSemesterStart();
                if (!start) { alert('Semester start date required'); return ''; }
                var weekStart = start.clone().add((weekIndex - 1) * 7, 'days');
                var days = [];
                for (var d = 0; d < 7; d++) days.push(weekStart.clone().add(d, 'days'));
                var html = '<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;"><h3 style="margin:0 0 10px 0;">Week ' + weekIndex + '</h3>';
                html += '<table style="width:100%;border-collapse:collapse;font-size:10pt;"><thead><tr>';
                html += '<th style="width:90px;border:1px solid #000;padding:6px;background:#f7f7f7;">Time</th>';
                days.forEach(function (dt) { html += '<th style="border:1px solid #000;padding:6px;text-align:center;background:#f7f7f7;">' + dt.format('dddd') + '</th>'; });
                html += '</tr></thead><tbody>';
                var startHour = 7, endHour = 18;
                for (var h = startHour; h <= endHour; h++) {
                    html += '<tr>';
                    html += '<td style="border:1px solid #000;padding:6px;font-weight:600;">' + moment({ hour: h }).format('HH:mm') + '</td>';
                    for (var c = 0; c < 7; c++) {
                        var day = days[c];
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            try {
                                var evStart = moment(ev.start);
                                var evEnd = ev.end ? moment(ev.end) : evStart.clone().add(1, 'minutes');
                                var rowStart = day.clone().hour(h).minute(0).second(0);
                                var rowEnd = rowStart.clone().add(1, 'hour');
                                return evStart.isBefore(rowEnd) && evEnd.isAfter(rowStart);
                            } catch (e) { return false; }
                        });
                        html += '<td style="border:1px solid #000;padding:6px;vertical-align:top;min-height:40px;">';
                        if (cellEvents.length) {
                            cellEvents.forEach(function (ce) {
                                var st = moment(ce.start).format('HH:mm'), en = ce.end ? moment(ce.end).format('HH:mm') : '';
                                html += '<div style="background:#2b8cff;color:#fff;padding:4px 6px;margin-bottom:4px;font-weight:600;">' + ce.title + '</div>';
                                html += '<div style="font-size:9pt;color:#222;margin-bottom:6px;">' + st + (en ? ' - ' + en : '') + '</div>';
                            });
                        } else {
                            html += '&nbsp;';
                        }
                        html += '</td>';
                    }
                    html += '</tr>';
                }
                html += '</tbody></table></div>';
                return html;
            }

            // build month table HTML (no raw dates) — monthIndex relative to semester start
            function buildMonthHtml(monthIndex) {
                var start = getSemesterStart();
                if (!start) { alert('Semester start date required'); return ''; }
                var monthStart = start.clone().add(monthIndex - 1, 'months').startOf('month');
                var monthEnd = monthStart.clone().endOf('month');
                var gridStart = monthStart.clone().startOf('week');
                var gridEnd = monthEnd.clone().endOf('week');
                var curr = gridStart.clone();
                var html = '<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;"><h3 style="margin:0 0 10px 0;">Month ' + monthIndex + '</h3>';
                html += '<table style="width:100%;border-collapse:collapse;font-size:10pt;"><thead><tr>';
                html += '<th style="width:90px;border:1px solid #000;padding:6px;background:#f7f7f7;">Week</th>';
                var days = ['Sun','Mon','Tue','Wed','Thu','Fri','Sat'];
                days.forEach(function (d) { html += '<th style="border:1px solid #000;padding:6px;background:#f7f7f7;text-align:center;">' + d + '</th>'; });
                html += '</tr></thead><tbody>';
                var weekNo = 1;
                while (curr.isSameOrBefore(gridEnd)) {
                    html += '<tr><td style="font-weight:600;">Week ' + weekNo + '</td>';
                    for (var i = 0; i < 7; i++) {
                        var cellDate = curr.clone();
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            return moment(ev.start).isSame(cellDate, 'day');
                        });
                        html += '<td style="vertical-align:top;min-width:140px;padding:6px;">';
                        if (cellEvents.length) {
                            cellEvents.forEach(function (ce) {
                                var st = moment(ce.start).format('HH:mm'), en = ce.end ? moment(ce.end).format('HH:mm') : '';
                                html += '<div class="badge bg-primary text-white mb-1" style="display:block;">' + ce.title + '</div>';
                                html += '<div style="font-size:0.85em;color:#333;">' + st + (en ? ' - ' + en : '') + '</div>';
                            });
                        } else {
                            html += '&nbsp;';
                        }
                        html += '</td>';
                        curr.add(1, 'day');
                    }
                    html += '</tr>';
                    weekNo++;
                }
                html += '</tbody></table></div>';
                return html;
            }

            function downloadHtmlAsA4Pdf(htmlContent, filename) {
                // create temp container
                var temp = $('<div></div>').css({ position: 'fixed', left: '-9999px', top: '0', width: '1122px' }).html(htmlContent);
                $('body').append(temp);
                html2canvas(temp.get(0), { scale: 2 }).then(function (canvas) {
                    const { jsPDF } = window.jspdf;
                    // A4 landscape dimensions in points
                    var pdf = new jsPDF('l', 'pt', 'a4');
                    var pageWidth = pdf.internal.pageSize.getWidth();
                    var pageHeight = pdf.internal.pageSize.getHeight();
                    var imgWidth = pageWidth - 40;
                    var imgHeight = canvas.height * (imgWidth / canvas.width);
                    var imgData = canvas.toDataURL('image/png');
                    if (imgHeight <= pageHeight - 40) {
                        pdf.addImage(imgData, 'PNG', 20, 20, imgWidth, imgHeight);
                    } else {
                        // split long image across pages
                        var ratio = imgWidth / canvas.width;
                        var totalHeight = canvas.height;
                        var rendered = 0;
                        while (rendered < totalHeight) {
                            var chunkHeight = Math.min(Math.floor((pageHeight - 40) / ratio), totalHeight - rendered);
                            var tmpCanvas = document.createElement('canvas');
                            tmpCanvas.width = canvas.width;
                            tmpCanvas.height = chunkHeight;
                            tmpCanvas.getContext('2d').drawImage(canvas, 0, rendered, canvas.width, chunkHeight, 0, 0, canvas.width, chunkHeight);
                            var tmpImg = tmpCanvas.toDataURL('image/png');
                            if (rendered > 0) pdf.addPage();
                            pdf.addImage(tmpImg, 'PNG', 20, 20, imgWidth, chunkHeight * ratio);
                            rendered += chunkHeight;
                        }
                    }
                    pdf.save(filename);
                    temp.remove();
                }).catch(function (err) {
                    console.error(err);
                    temp.remove();
                    alert('Failed to generate PDF.');
                });
            }

                // Build a generic grid HTML for an arbitrary date range (start..end inclusive)
                // This replaces the fixed-week/month builders for the PDF download so we can
                // export the calendar's visible range (next/selected week or month).
                function buildGridHtmlForRange(startMoment, endMoment, title) {
                    if (!startMoment || !endMoment || !startMoment.isValid() || !endMoment.isValid()) return '';
                    // build list of days from start to end (inclusive)
                    var days = [];
                    var cursor = startMoment.clone().startOf('day');
                    var last = endMoment.clone().startOf('day');
                    while (cursor.isSameOrBefore(last)) { days.push(cursor.clone()); cursor.add(1, 'day'); }

                    // compute dynamic hour range from latestFcEvents
                    var minHour = 23, maxHour = 0;
                    if (latestFcEvents && latestFcEvents.length) {
                        latestFcEvents.forEach(function(ev) {
                            try {
                                var ms = moment(ev.start);
                                var me = ev.end ? moment(ev.end) : null;
                                if (ms.isValid()) minHour = Math.min(minHour, ms.hour());
                                if (me && me.isValid()) maxHour = Math.max(maxHour, me.hour());
                            } catch (e) {}
                        });
                    }
                    // fallback default if no events or hours out of expected range
                    if (minHour > maxHour) { minHour = 7; maxHour = 18; }
                    // add padding rows for readability
                    minHour = Math.max(0, minHour - 1);
                    maxHour = Math.min(23, maxHour + 1);
                    // Ensure PDF grid covers at least 07:00 - 18:00 per requirement
                    minHour = Math.min(minHour, 7);
                    maxHour = Math.max(maxHour, 18);

                    var html = '<div style="padding:10px;font-family:Helvetica,Arial,sans-serif;"><h3 style="margin:0 0 10px 0;">' + (title || '') + '</h3>';
                    html += '<table style="width:100%;border-collapse:collapse;font-size:10pt;"><thead><tr>';
                    html += '<th style="width:90px;border:1px solid #000;padding:6px;background:#f7f7f7;">Time</th>';
                    days.forEach(function(dt){ html += '<th style="border:1px solid #000;padding:6px;background:#f7f7f7;text-align:center;">' + dt.format('ddd DD/MM') + '</th>'; });
                    html += '</tr></thead><tbody>';

                    // Precompute events per day and compute row spans so multi-hour events
                    // render as a single <td> with rowspan in the exported table.
                    var eventsByDay = [];
                    for (var i = 0; i < days.length; i++) eventsByDay.push([]);

                    latestFcEvents.forEach(function(ev) {
                        try {
                            var ms = moment(ev.start);
                            var me = ev.end ? moment(ev.end) : ms.clone().add(1,'minutes');
                            if (!ms.isValid() || !me.isValid()) return;
                            // find which day column(s) this event intersects
                            for (var di = 0; di < days.length; di++) {
                                var day = days[di].clone().startOf('day');
                                var dayStart = day.clone().hour(minHour).minute(0).second(0);
                                var dayEnd = day.clone().hour(maxHour+1).minute(0).second(0);
                                // check overlap with the visible day range
                                if (me.isAfter(dayStart) && ms.isBefore(dayEnd)) {
                                    // clamp to visible hours
                                    var evStartClamped = ms.isBefore(dayStart) ? dayStart.clone() : ms.clone();
                                    var evEndClamped = me.isAfter(dayEnd) ? dayEnd.clone() : me.clone();
                                    var fractionalStart = evStartClamped.hour() + (evStartClamped.minute()/60);
                                    var fractionalEnd = evEndClamped.hour() + (evEndClamped.minute()/60);
                                    var startRow = Math.floor(fractionalStart);
                                    var endRow = Math.ceil(fractionalEnd) - 1;
                                    // clamp within minHour..maxHour
                                    startRow = Math.max(minHour, Math.min(maxHour, startRow));
                                    endRow = Math.max(minHour, Math.min(maxHour, endRow));
                                    if (startRow <= endRow) {
                                        eventsByDay[di].push({
                                            id: ev.id || Math.random().toString(36).substr(2,6),
                                            title: ev.title || (ev.extendedProps && (ev.extendedProps.module_name || ev.extendedProps.subject_name)) || 'Class',
                                            startRow: startRow,
                                            rowSpan: (endRow - startRow + 1),
                                            startText: moment(ev.start).format('HH:mm'),
                                            endText: ev.end ? moment(ev.end).format('HH:mm') : ''
                                        });
                                    }
                                }
                            }
                        } catch (e) { /* ignore malformed event */ }
                    });

                    // For quick lookup of events that start at a given row per day
                    var eventsStartingAt = [];
                    for (var di = 0; di < days.length; di++) {
                        eventsStartingAt[di] = {};
                        // sort by startRow to make deterministic
                        eventsByDay[di].sort(function(a,b){ return a.startRow - b.startRow; });
                        eventsByDay[di].forEach(function(ev){
                            if (!eventsStartingAt[di][ev.startRow]) eventsStartingAt[di][ev.startRow] = [];
                            eventsStartingAt[di][ev.startRow].push(ev);
                        });
                    }

                    // track active rowspans per day (remaining rows covered)
                    var activeSpans = new Array(days.length).fill(0);

                    for (var h = minHour; h <= maxHour; h++) {
                        html += '<tr>';
                        html += '<td style="border:1px solid #000;padding:6px;font-weight:600;">' + moment({ hour: h }).format('HH:mm') + '</td>';
                        for (var c = 0; c < days.length; c++) {
                            if (activeSpans[c] > 0) {
                                // this cell is covered by a rowspan started earlier; decrement counter and skip adding a <td>
                                activeSpans[c] = activeSpans[c] - 1;
                                continue;
                            }

                            var evs = eventsStartingAt[c][h] || [];
                            if (evs.length) {
                                // compute rowspan as the maximum rows any of the events need
                                var rowspan = evs.reduce(function(acc, it){ return Math.max(acc, it.rowSpan || 1); }, 1);
                                if (rowspan > 1) activeSpans[c] = rowspan - 1;
                                html += '<td rowspan="' + rowspan + '" style="border:1px solid #000;padding:6px;vertical-align:top;min-height:40px;">';
                                // render events side-by-side inside the cell
                                var itemWidth = Math.floor(100 / evs.length) - 1; // percent, reserve small gap
                                html += '<div style="display:flex;gap:6px;align-items:flex-start;">';
                                evs.forEach(function(evst){
                                    html += '<div style="flex:1 1 0;min-width:0;background:#2b8cff;color:#fff;padding:6px;box-sizing:border-box;">';
                                    html += '<div style="font-weight:600;">' + evst.title + '</div>';
                                    html += '<div style="font-size:9pt;color:#222;margin-top:4px;">' + evst.startText + (evst.endText ? ' - ' + evst.endText : '') + '</div>';
                                    html += '</div>';
                                });
                                html += '</div>';
                                html += '</td>';
                            } else {
                                html += '<td style="border:1px solid #000;padding:6px;vertical-align:top;min-height:40px;">&nbsp;</td>';
                            }
                        }
                        html += '</tr>';
                    }

                    html += '</tbody></table></div>';
                    return html;
                }

            // click handlers
            $('#downloadWeekPdfBtn').on('click', function () {
                if (!latestFcEvents || !latestFcEvents.length) { alert('Please load timetable first.'); return; }
                try {
                    var start = null, end = null, title = 'Week Timetable';
                    if (window.__nebulaCalendar && window.__nebulaCalendar.view) {
                        // calendar.view.activeStart and activeEnd are Dates
                        start = moment(window.__nebulaCalendar.view.activeStart);
                        // activeEnd is exclusive; subtract 1ms to include previous day
                        end = moment(new Date(window.__nebulaCalendar.view.activeEnd.getTime() - 1));
                        title = 'Week ' + start.format('YYYY-MM-DD') + ' - ' + end.format('YYYY-MM-DD');
                    } else {
                        var today = moment();
                        start = today.clone().startOf('week');
                        end = today.clone().endOf('week');
                    }
                    var html = buildGridHtmlForRange(start, end, title);
                    downloadHtmlAsA4Pdf(html, 'Week_' + start.format('YYYY-MM-DD') + '_Timetable.pdf');
                } catch (e) {
                    console.error('Week PDF generation failed', e);
                    alert('Failed to generate week PDF');
                }
            });

            $('#downloadMonthPdfBtn').on('click', function () {
                if (!latestFcEvents || !latestFcEvents.length) { alert('Please load timetable first.'); return; }
                try {
                    var start = null, end = null, title = 'Month Timetable';
                    if (window.__nebulaCalendar && window.__nebulaCalendar.view) {
                        start = moment(window.__nebulaCalendar.view.activeStart);
                        end = moment(new Date(window.__nebulaCalendar.view.activeEnd.getTime() - 1));
                        title = 'Month ' + start.format('YYYY-MM');
                    } else {
                        var today = moment();
                        start = today.clone().startOf('month');
                        end = today.clone().endOf('month');
                    }
                    var html = buildGridHtmlForRange(start, end, title);
                    downloadHtmlAsA4Pdf(html, 'Month_' + start.format('YYYY-MM') + '_Timetable.pdf');
                } catch (e) {
                    console.error('Month PDF generation failed', e);
                    alert('Failed to generate month PDF');
                }
            });
            // --- end simplified download functions ---

            // safe: download-week button handler is attached below only if button exists
            (function () {
                var dlWeekBtn = document.getElementById('download-week-btn');
                if (!dlWeekBtn) return;
                dlWeekBtn.addEventListener('click', function () {
                    try {
                        var start = moment().format('YYYY-MM-DD'), end = start;
                        if (typeof calendar !== 'undefined' && calendar && calendar.view && calendar.view.activeStart) {
                            start = calendar.view.activeStart.toISOString().split('T')[0];
                            end = calendar.view.activeEnd ? new Date(calendar.view.activeEnd.getTime() - 1).toISOString().split('T')[0] : start;
                        }
                        fetch('/timetable/download-week-pdf', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({ start: start, end: end })
                        }).then(function (res) { return res.blob(); })
                          .then(function (blob) {
                             var url = window.URL.createObjectURL(blob);
                             var a = document.createElement('a');
                             a.href = url;
                             a.download = 'timetable_' + start + '_to_' + end + '.pdf';
                             a.click();
                             window.URL.revokeObjectURL(url);
                          }).catch(function (err) {
                             console.error('Week PDF download failed', err);
                          });
                    } catch (e) {
                        console.warn('Download-week handler skipped due to error', e);
                    }
                });
            })();

            // Add another subject block
            $('#addSubjectBtn').on('click', function () {
                // clone the first subject-block template
                var idx = $('#subjectList .subject-block').length;
                var $first = $('#subjectList .subject-block').first();
                var $clone = $first.clone();

                // update ids and values
                $clone.find('select.subject-select').each(function () {
                    var newId = 'degree_subject_' + idx;
                    $(this).attr('id', newId);
                    $(this).val('');
                });
                $clone.find('input.duration-input').each(function () {
                    var newId = 'degree_duration_' + idx;
                    $(this).attr('id', newId);
                    $(this).val('');
                });
                $clone.find('input.time-input').each(function () {
                    var newId = 'degree_time_' + idx;
                    $(this).attr('id', newId);
                    $(this).val('');
                });

                // show remove button on clones
                $clone.find('.remove-subject-btn').show().off('click').on('click', function () {
                    $(this).closest('.subject-block').remove();
                });

                $('#subjectList').append($clone);
            });

            // Remove button for dynamically created blocks (in case user added and wants to remove)
            $(document).on('click', '.remove-subject-btn', function () {
                $(this).closest('.subject-block').remove();
            });

            // Assign subjects handler - POST to server and refresh calendar
            $('#assignSubjectBtn').on('click', function () {
                var date = $('#selectedDate').val();
                if (!date) { alert('No date selected'); return; }

                var subject_ids = [], durations = [], times = [], end_times = [];
                var valid = true;

                $('#subjectList .subject-block').each(function () {
                    var subj = $(this).find('.subject-select').val();
                    var dur = $(this).find('.duration-input').val();
                    var timeVal = $(this).find('.time-input').val(); // "HH:mm"
                    if (!subj || !dur || !timeVal) {
                        valid = false;
                        return false; // break
                    }

                    // parse and normalize time; produce "h:mm A" for controller validation
                    var m = moment(timeVal, ['HH:mm','HH:mm:ss','h:mm A']);
                    if (!m.isValid()) {
                        valid = false;
                        return false;
                    }
                    // use 24-hour format to keep server/store consistent and easier to parse later
                    var startFormatted = m.format('HH:mm');
                    var endMoment = m.clone().add(parseInt(dur, 10) || 0, 'minutes');
                    var endFormatted = endMoment.format('HH:mm');

                    subject_ids.push(subj);
                    durations.push(parseInt(dur, 10));
                    times.push(startFormatted);
                    end_times.push(endFormatted);
                });

                if (!valid) { alert('Please fill subject, duration and valid time for all entries.'); return; }

                var payload = {
                    date: date,
                    subject_ids: subject_ids,
                    durations: durations,
                    times: times,
                    end_times: end_times,
                    location: $('#degree_location').val() || '',
                    course_id: $('#degree_course').val() || '',
                    intake_id: $('#degree_intake').val() || '',
                    semester: $('#degree_semester').val() || ''
                };

                $('#assignSubjectBtn').prop('disabled', true).text('Assigning...');

                $.ajax({
                    url: '/timetable/assign-subjects',
                    type: 'POST',
                    data: JSON.stringify(payload),
                    contentType: 'application/json',
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') || '' },
                    success: function (res) {
                        $('#assignSubjectBtn').prop('disabled', false).text('Assign Subjects');
                        $('#subjectSelectionModal').modal('hide');

                        // reload events from server so week/day views show timed events correctly
                        $('#showTimetableBtn').trigger('click');
                    },
                    error: function (xhr) {
                        $('#assignSubjectBtn').prop('disabled', false).text('Assign Subjects');
                        var msg = 'Failed to assign subjects.';
                        try {
                            var json = xhr.responseJSON || JSON.parse(xhr.responseText || '{}');
                            if (json && json.message) msg = json.message;
                        } catch (e) {}
                        alert(msg);
                        console.error('Assign error:', xhr);
                    }
                });
            });
            // === Certificate calendar & showTimetable handler ===
            var latestCertificateFcEvents = [];
            var latestCertificateEventsRaw = [];
            var certificateCalendarEl = document.getElementById('certificate_calendar');
            if (certificateCalendarEl) {
                var certificateCalendar = new FullCalendar.Calendar(certificateCalendarEl, {
                    initialView: 'timeGridWeek',
                    headerToolbar: { left: 'prev,next today', center: 'title', right: 'timeGridWeek,timeGridDay,dayGridMonth' },
                    height: 'auto',
                    selectable: true,
                    navLinks: true,
                    eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
                    events: [],
                    eventOverlap: true
                });
                window.__nebulaCertificateCalendar = certificateCalendar;
                certificateCalendar.render();
            }

            $('#certificate_showTimetableBtn').on('click', function () {
                var data = {
                    location: $('#certificate_location').val(),
                    course_id: $('#certificate_course').val(),
                    intake_id: $('#certificate_intake').val(),
                    semester: $('#certificate_semester').val(),
                    start_date: $('#certificate_start_date').val(),
                    end_date: $('#certificate_end_date').val()
                };

                $.ajax({
                    url: '/get-timetable-events',
                    type: 'GET',
                    data: data,
                    success: function (response) {
                        var eventsArray = [];
                        if (Array.isArray(response)) eventsArray = response;
                        else if (response && Array.isArray(response.events)) eventsArray = response.events;
                        else if (response && response.data && Array.isArray(response.data.events)) eventsArray = response.data.events;
                        else if (response && response.data && Array.isArray(response.data)) eventsArray = response.data;

                        // show certificate calendar section (must exist in DOM)
                        try { $('#certificateTimetableSection').show(); } catch (e) { /* ignore if not present */ }
                        setTimeout(function () { if (window.__nebulaCertificateCalendar) window.__nebulaCertificateCalendar.render(); }, 40);

                        try { if (window.__nebulaCertificateCalendar) { try { window.__nebulaCertificateCalendar.removeAllEvents(); } catch(e){} } } catch(e){}

                        if (!eventsArray || !eventsArray.length) {
                            alert('No events returned for certificate timetable.');
                            return;
                        }

                        var fcEvents = [];
                        eventsArray.forEach(function (e, idx) {
                            var title = e.module_name || e.subject_name || e.title || 'Class';
                            var datePart = e.date || e.day || '';
                            var startTime = (e.time || '').toString().trim();
                            var endTime = (e.end_time || '').toString().trim();

                            if (!endTime && e.duration) {
                                var mStartTmp = moment(startTime, ['HH:mm:ss','HH:mm','h:mm A']);
                                if (mStartTmp.isValid()) {
                                    endTime = mStartTmp.clone().add(parseInt(e.duration,10) || 0, 'minutes').format('HH:mm');
                                } else {
                                    endTime = startTime;
                                }
                            }

                            if (!datePart || !startTime) return;

                            var mStart = moment(datePart + ' ' + startTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A','YYYY-MM-DD H:mm']);
                            var mEnd = endTime ? moment(datePart + ' ' + endTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A','YYYY-MM-DD H:mm']) : mStart.clone().add(1,'minutes');
                            if (!mStart.isValid() || !mEnd.isValid()) return;

                            fcEvents.push({
                                id: (e.id !== undefined && e.id !== null) ? e.id : idx,
                                title: title,
                                start: mStart.toDate(),
                                end: mEnd.toDate(),
                                allDay: false,
                                extendedProps: e,
                                overlap: true
                            });
                        });

                        latestCertificateEventsRaw = eventsArray;
                        latestCertificateFcEvents = fcEvents;

                        try {
                            if (window.__nebulaCertificateCalendar) {
                                try { window.__nebulaCertificateCalendar.removeAllEvents(); } catch(e){}
                                window.__nebulaCertificateCalendar.addEventSource(fcEvents);
                                window.__nebulaCertificateCalendar.refetchEvents();
                            }
                        } catch (ex) { console.warn('Failed to add certificate events', ex); }

                        // show download buttons if semester selected
                        if ($('#certificate_semester').val()) $('#certificate_download_buttons').show();
                    },
                    error: function () {
                        alert('Error occurred while fetching certificate timetable.');
                    }
                });
            });

            // Copy certificate filters into PDF modal for simple PDF generation
            $('#certificate_downloadPdfBtn').on('click', function () {
                $('#pdf_location').val($('#certificate_location').val() || '');
                $('#pdf_course').empty().append('<option value="">All</option>');
                $('#certificate_course option').each(function () {
                    var v = $(this).val();
                    var t = $(this).text();
                    if (v) $('#pdf_course').append('<option value="' + v + '">' + t + '</option>');
                });
                $('#pdf_course').val($('#certificate_course').val() || '');
                $('#pdf_from').val($('#certificate_start_date').val() || '');
                $('#pdf_to').val($('#certificate_end_date').val() || '');
                $('#downloadPdfModal').modal('show');
            });

            // JS fallback for modal close buttons: some projects use Bootstrap 5 (data-bs-dismiss)
            // while others rely on data-dismiss; ensure both work by hooking clicks.
            $(document).on('click', '[data-bs-dismiss="modal"], [data-dismiss="modal"]', function (e) {
                var $btn = $(this);
                // find closest modal
                var $modal = $btn.closest('.modal');
                if ($modal.length) {
                    try {
                        // try Bootstrap 5 modal hide via JS
                        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
                            var modalInstance = bootstrap.Modal.getInstance($modal[0]);
                            if (!modalInstance) modalInstance = new bootstrap.Modal($modal[0]);
                            modalInstance.hide();
                        } else {
                            $modal.hide();
                            $modal.removeClass('show');
                            $('.modal-backdrop').remove();
                        }
                    } catch (ex) {
                        // fallback jQuery hide
                        $modal.hide();
                        $modal.removeClass('show');
                        $('.modal-backdrop').remove();
                    }
                }
            });

        });
    </script>
<?php $__env->stopPush(); ?>

    <style>
        /* Allow FullCalendar to handle positioning for agendaWeek/agendaDay.
           Do not override position/left/top; only adjust width if needed. */
        #calendar .fc-event {
            width: auto !important;
        }
    </style>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/Nebula finish/Nebula/resources/views/timetable.blade.php ENDPATH**/ ?>