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
                                    <button type="button" class="btn btn-outline-secondary" id="downloadPdfBtn" style="margin-left:8px;">Download PDF</button>
                                    <!-- Simplified actions: direct week/month PDF -->
                                    <button type="button" class="btn btn-success" id="downloadWeekPdfBtn" style="margin-left:12px;">Download Week PDF</button>
                                    <button type="button" class="btn btn-dark" id="downloadMonthPdfBtn" style="margin-left:8px;">Download Month PDF</button>
                                </div>
                            </div>
                        </div>

                        <!-- FullCalendar Container -->
                        <div class="mt-4" id="degreeTimetableSection" style="display:none;">
                            <div id="calendar"></div>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
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
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <button id="generatePdfBtn" type="button" class="btn btn-primary">Generate PDF</button>
          </div>
        </div>
      </div>
    </div>

    <!-- FullCalendar CSS -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.0.1/dist/fullcalendar.min.css" rel="stylesheet" />

    <!-- jQuery (Required by FullCalendar) -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Moment.js (Required by FullCalendar) -->
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.1/moment.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.0.1/dist/fullcalendar.min.js"></script>

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
                var startHour = 8, endHour = 18;
                for (var h = startHour; h <= endHour; h++) {
                    html += '<tr><td style="font-weight:600;">' + moment({ hour: h }).format('HH:mm') + '</td>';
                    for (var c = 0; c < 7; c++) {
                        var day = days[c];
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            return moment(ev.start).isSame(day, 'day') && moment(ev.start).hour() === h;
                        });
                        html += '<td style="vertical-align:top;padding:6px;">';
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

            // Initialize FullCalendar
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,agendaWeek,agendaDay'
                },
                defaultView: 'agendaWeek',
                allDaySlot: false,
                editable: true,
                droppable: true,
                selectable: true,
                selectHelper: true,
                slotDuration: '00:15:00',
                minTime: "00:00:00",
                maxTime: "24:00:00",
                slotEventOverlap: true,
                eventOverlap: true,
                timezone: false,    // use local datetimes as-is (prevents UTC conversion)
                events: [],
                eventRender: function (event, element) {
                    element.css({
                        'background-color': '#007bff',
                        'border-color': '#0056b3',
                        'color': '#fff',
                        'opacity': '0.95'
                    });
                    element.find('.fc-title').css({
                        'font-weight': '600',
                        'font-size': '0.85em'
                    });
                },
                eventClick: function (event, jsEvent, view) {
                    if (event && event.start) {
                        var eventTime = moment(event.start).format('HH:mm');
                        $('#degree_time_0').val(eventTime);
                    }
                },
                select: function (start, end, jsEvent, view) {
                    var startDate = start.format('YYYY-MM-DD');
                    var startTime = start.format('HH:mm');
                    $('#selectedDate').val(startDate);
                    $('#selected_date_display').val(startDate);
                    $('#degree_time_0').val(startTime);
                    $('#degree_duration_0').val('');
                    $('#degree_subject_0').val('');
                    $('#subjectSelectionModal').modal('show');
                    $('#calendar').fullCalendar('unselect');
                },
                dayClick: function (date, jsEvent, view) {
                    var d = date.format('YYYY-MM-DD');
                    var t = date.format('HH:mm');
                    $('#selectedDate').val(d);
                    $('#selected_date_display').val(d);
                    $('#degree_time_0').val(t);
                    $('#degree_duration_0').val('');
                    $('#degree_subject_0').val('');
                    $('#subjectSelectionModal').modal('show');
                }
            });

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
                        setTimeout(function () { $('#calendar').fullCalendar('render'); }, 40);

                        // remove previous events safely
                        try {
                            if ($('#calendar').hasClass('fc')) {
                                $('#calendar').fullCalendar('removeEvents');
                            }
                        } catch (err) {
                            console.warn('Safe removeEvents error:', err);
                        }

                        if (!eventsArray || !eventsArray.length) {
                            console.info('No events returned.');
                            setTimeout(function () { $('#calendar').fullCalendar('render'); }, 50);
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
+
                            // use local-formatted datetimes (no trailing Z) so FullCalendar places events correctly in week/day views
                            // use Date objects to avoid ISO parsing/timezone edge-cases in agendaWeek/Day
                            fcEvents.push({
                                id: (e.id !== undefined && e.id !== null) ? 't' + e.id : 't' + idx,
                                title: title,
                                // give FullCalendar local ISO datetimes (no trailing Z) to avoid UTC shifts
                                start: mStart.format('YYYY-MM-DDTHH:mm:ss'),
                                end: mEnd.format('YYYY-MM-DDTHH:mm:ss'),
                                allDay: false,
                                extendedProps: e,
                                overlap: true
                            });
                        });

                        // add events after short delay so calendar layout is ready (fix scrollTop errors)
                        setTimeout(function () {
                            console.log('Adding events to FullCalendar, count:', fcEvents.length, fcEvents);
                            try {
                                // remove previous events/sources safely before adding new ones
                                if ($('#calendar').hasClass('fc')) {
                                    $('#calendar').fullCalendar('removeEvents');
                                    $('#calendar').fullCalendar('removeEventSources');
                                }
                            } catch (ex) { console.warn('Error clearing previous events:', ex); }

                            $('#calendar').fullCalendar('addEventSource', fcEvents);
                            $('#calendar').fullCalendar('rerenderEvents');
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
                var startHour = 8, endHour = 18;
                for (var h = startHour; h <= endHour; h++) {
                    html += '<tr>';
                    html += '<td style="border:1px solid #000;padding:6px;font-weight:600;">' + moment({ hour: h }).format('HH:mm') + '</td>';
                    for (var c = 0; c < 7; c++) {
                        var day = days[c];
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            return moment(ev.start).isSame(day, 'day') && moment(ev.start).hour() === h;
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
                    html += '<tr>';
                    html += '<td style="border:1px solid #000;padding:6px;font-weight:600;">Week ' + weekNo + '</td>';
                    for (var i = 0; i < 7; i++) {
                        var cellDate = curr.clone();
                        var cellEvents = latestFcEvents.filter(function (ev) {
                            return moment(ev.start).isSame(cellDate, 'day');
                        });
                        html += '<td style="border:1px solid #000;padding:6px;vertical-align:top;min-height:60px;">';
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

            // click handlers
            $('#downloadWeekPdfBtn').on('click', function () {
                if (!latestFcEvents || !latestFcEvents.length) { alert('Please load timetable first.'); return; }
                var today = moment();
                var weekIndex = computeWeekIndexForDate(today);
                var html = buildWeekHtml(weekIndex);
                downloadHtmlAsA4Pdf(html, 'Week_' + weekIndex + '_Timetable.pdf');
            });

            $('#downloadMonthPdfBtn').on('click', function () {
                if (!latestFcEvents || !latestFcEvents.length) { alert('Please load timetable first.'); return; }
                var today = moment();
                var monthIndex = computeMonthIndexForDate(today);
                var html = buildMonthHtml(monthIndex);
                downloadHtmlAsA4Pdf(html, 'Month_' + monthIndex + '_Timetable.pdf');
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
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/nebula/Nebula/resources/views/timetable.blade.php ENDPATH**/ ?>