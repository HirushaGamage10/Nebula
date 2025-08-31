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
                                    <button type="button" class="btn btn-primary" id="showTimetableBtn">Show
                                        Timetable</button>
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
                        <!-- Add Time Picker -->
                        <div class="mb-3 row align-items-center">
                            <label for="degree_time_0" class="col-sm-3 col-form-label fw-bold">Time <span
                                    class="text-danger">*</span></label>
                            <div class="col-sm-9">
                                <input type="time" class="form-control time-input" id="degree_time_0" name="times[]"
                                    required>
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-secondary" id="addSubjectBtn">Add Another Subject</button>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="assignSubjectBtn">Assign Subjects</button>
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

    <?php $__env->startPush('scripts'); ?>
        <script>
            $(document).ready(function () {
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
                    defaultView: 'agendaWeek',           // show weekly timetable by default
                    allDaySlot: false,
                    editable: true,
                    droppable: true,
                    slotDuration: '00:15:00',
                    minTime: "00:00:00",
                    maxTime: "24:00:00",
                    // allow overlapping events in time-grid and control stacking
                    slotEventOverlap: true,
                    eventOverlap: true,
                    eventOrder: "start,-duration,title", // prefer earlier starts, longer events second
                    events: [],
                    dayClick: function (date, jsEvent, view) {
                        $('#selectedDate').val(date.format('YYYY-MM-DD'));
                        $('#selected_date_display').val(date.format('MMMM Do YYYY'));
                        $('#subjectSelectionModal').modal('show');
                    },
                    eventClick: function (event, jsEvent, view) {
                        var eventTime = event.start.format('HH:mm');
                        $('#degree_time_0').val(eventTime);
                    }
                });

                // Handle Subject Assignment
                $(document).on('click', '#assignSubjectBtn', function (e) {
                    e.preventDefault();

                    // collect arrays by name so markup structure changes won't break collection
                    var subjectIds = $('select[name="subject_ids[]"]').map(function () { return $(this).val(); }).get();
                    var durations = $('input[name="durations[]"]').map(function () { return $(this).val(); }).get();
                    var times = $('input[name="times[]"]').map(function () { return $(this).val(); }).get();

                    // Basic client validation
                    if (!subjectIds.length || !durations.length || !times.length) {
                        alert('Please add at least one subject, duration and time.');
                        return;
                    }

                    // Ensure no empty subject
                    for (var i = 0; i < subjectIds.length; i++) {
                        if (!subjectIds[i]) {
                            alert('Please select a subject for each row.');
                            return;
                        }
                    }

                    // Normalize times and compute end_times using moment
                    var endTimes = [];
                    for (var i = 0; i < times.length; i++) {
                        var raw = times[i] || '';
                        var dur = parseInt(durations[i], 10) || 0;
                        var m = null;

                        if (/^\d{1,2}:\d{2}\s?(AM|PM)$/i.test(raw)) {
                            m = moment(raw, 'h:mm A');
                        } else if (/^\d{1,2}:\d{2}$/.test(raw)) {
                            m = moment(raw, 'HH:mm');
                        } else {
                            m = moment(raw);
                        }

                        if (!m.isValid()) {
                            alert('Invalid time format for one of the rows.');
                            return;
                        }

                        var end = m.clone().add(dur, 'minutes');
                        times[i] = m.format('HH:mm');     // normalized start
                        endTimes.push(end.format('HH:mm'));
                    }

                    // Gather other fields from the correct inputs
                    var payload = {
                        _token: $('meta[name="csrf-token"]').attr('content') || $('input[name="_token"]').val(),
                        date: $('#selectedDate').val() || $('#selected_date_display').val() || '',
                        subject_ids: subjectIds,
                        durations: durations,
                        times: times,
                        end_times: endTimes,
                        location: $('#degree_location').val(),
                        course_id: $('#degree_course').val(),
                        intake_id: $('#degree_intake').val(),
                        semester: $('#degree_semester').val()
                    };

                    console.log('Timetable data being sent:', payload);

                    $.ajax({
                        url: '/assign-subject-to-timeslot',
                        method: 'POST',
                        data: payload,
                        success: function (res) {
                            console.log('Response:', res);
                            if (res.success) {
                                alert(res.message || 'Subjects assigned successfully');
                                $('#subjectSelectionModal').modal('hide');
                            } else {
                                alert(res.message || 'There was an error assigning subjects');
                            }
                        },
                        error: function (xhr, status, error) {
                            console.error('AJAX error - status:', status, 'error:', error);
                            console.error('xhr.status:', xhr.status);
                            console.error('xhr.responseJSON:', xhr.responseJSON);
                            console.error('xhr.responseText:', xhr.responseText);
                            var msg = (xhr.responseJSON && (xhr.responseJSON.message || xhr.responseJSON.errors)) ?
                                (xhr.responseJSON.message || JSON.stringify(xhr.responseJSON.errors)) :
                                xhr.responseText || 'There was an error. Check server logs.';
                            alert(msg);
                        }
                    });
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
                    console.log("Timetable data being sent:", data); // Debug the data

                    $.ajax({
                        url: '/get-timetable-events',
                        type: 'GET',
                        data: data,
                        success: function (response) {
                            console.log("Timetable events received (raw):", response);

                            // Support several possible response shapes
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

                            // always show calendar section first so fullcalendar can create scrollers
                            $('#degreeTimetableSection').show();

                            // clear old events / sources
                            try {
                                // only call if calendar has been initialised/rendered
                                if ($('#calendar').hasClass('fc')) {
                                    $('#calendar').fullCalendar('removeEvents');
                                    $('#calendar').fullCalendar('removeEventSource', 'localSource');
                                } else {
                                    console.warn('FullCalendar not initialised yet, skipping removeEvents.');
                                }
                            } catch (err) {
                                console.warn('FullCalendar removeEvents error (safe to ignore if first load):', err);
                            }

                            if (!eventsArray || !eventsArray.length) {
                                console.info('No events array found or it is empty.', response);
                                // still force a render to ensure scrollers are created
                                setTimeout(function () { $('#calendar').fullCalendar('render'); }, 50);
                                return;
                            }

                            var fcEvents = eventsArray.map(function (e, idx) {
                                var title = e.module_name || e.subject_name || e.title || 'Class';
                                var startTime = e.time || '00:00';
                                var endTime = e.end_time || '';

                                if (!endTime && e.duration) {
                                    var mStart = moment(startTime, ['HH:mm:ss','HH:mm','h:mm A']);
                                    if (mStart.isValid()) {
                                        endTime = mStart.clone().add(parseInt(e.duration, 10) || 0, 'minutes').format('HH:mm');
                                    } else {
                                        endTime = startTime;
                                    }
                                }

                                // Build robust ISO datetimes (include seconds) and guard missing date
                                var datePart = e.date || e.day || '';
                                var startIso = datePart && startTime ? moment(datePart + ' ' + startTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A']).format('YYYY-MM-DDTHH:mm:ss') : null;
                                var endIso = datePart && endTime ? moment(datePart + ' ' + endTime, ['YYYY-MM-DD HH:mm:ss','YYYY-MM-DD HH:mm','YYYY-MM-DD h:mm A']).format('YYYY-MM-DDTHH:mm:ss') : null;

                                // Fallback to date only if parsing failed
                                if (!startIso && datePart) {
                                    startIso = datePart + 'T00:00:00';
                                }
                                if (!endIso && datePart) {
                                    endIso = datePart + 'T00:00:00';
                                }

                                return {
                                    // ensure id is unique (prevents replacement when two events share same start)
                                    id: 't' + (e.id || idx) + '-' + idx,
                                    title: title,
                                    start: startIso,
                                    end: endIso,
                                    allDay: false,
                                    extendedProps: e
                                };
                            });

                            // add events after a short timeout so FullCalendar's layout exists (prevents scrollTop null)
                            setTimeout(function () {
                                console.log('Adding events to FullCalendar, count:', fcEvents.length, fcEvents.map(function(ev){ return ev.id; }));
                                // add as a local source so we can remove later if needed
                                $('#calendar').fullCalendar('addEventSource', fcEvents);
                                // force redraw
                                $('#calendar').fullCalendar('rerenderEvents');
                            }, 80);
                        },
                        error: function (xhr, status, error) {
                            alert('Error occurred while fetching the timetable');
                        }
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/nebula/Nebula/resources/views/timetable.blade.php ENDPATH**/ ?>