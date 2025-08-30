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
                                <input type="time" class="form-control" id="degree_time_0" name="times[]" required>
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
                        if (data.success) {
                            $('#degree_course').empty();
                            $('#degree_course').append('<option selected disabled value="">Select Course</option>');
                            $.each(data.courses, function (index, course) {
                                $('#degree_course').append('<option value="' + course.course_id + '">' + course.course_name + '</option>');
                            });
                            $('#degree_course').prop('disabled', false);
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
                        $('#degree_intake').empty();
                        $('#degree_intake').append('<option value="" disabled selected>Select Intake</option>');
                        $.each(data.intakes, function (index, intake) {
                            $('#degree_intake').append('<option value="' + intake.intake_id + '">' + intake.batch + '</option>');
                        });
                        $('#degree_intake').prop('disabled', false);
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
                        $('#degree_semester').empty();
                        $('#degree_semester').append('<option value="" disabled selected>Select Semester</option>');
                        $.each(data.semesters, function (index, semester) {
                            $('#degree_semester').append('<option value="' + semester.id + '">' + semester.name + '</option>');
                        });
                        $('#degree_semester').prop('disabled', false);
                    },
                    error: function (xhr, status, error) {
                        console.error('Error fetching semesters:', error);
                    }
                });
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
                        if (data.modules && data.modules.length > 0) {
                            $('#degree_subject_0').empty();
                            $('#degree_subject_0').append('<option selected disabled value="">Select Subject</option>');
                            $.each(data.modules, function (index, module) {
                                $('#degree_subject_0').append('<option value="' + module.module_id + '">' + module.module_name + ' (' + module.module_code + ')</option>');
                            });
                        } else {
                            $('#degree_subject_0').empty();
                            $('#degree_subject_0').append('<option value="" disabled>No subjects found</option>');
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
            events: [], // Initially empty events
            editable: true,
            droppable: true,
            dayClick: function (date, jsEvent, view) {
                // Set the selected date in the hidden input and display it in the modal
                $('#selectedDate').val(date.format('YYYY-MM-DD'));
                $('#selected_date_display').val(date.format('MMMM Do YYYY'));  // Display date in readable format

                // Set the default time to the selected day (e.g., start at 9:00 AM)
                var defaultTime = "09:00";
                $('#degree_time_0').val(defaultTime);

                // Show the subject selection modal
                $('#subjectSelectionModal').modal('show');
            }
        });

        // Handle Subject Assignment
        $(document).on('click', '#assignSubjectBtn', function (e) {
            e.preventDefault();

            var subjectIds = [];
            var durations = [];
            var times = [];
            $('#subjectList .subject-select').each(function () {
                subjectIds.push($(this).val());
            });
            $('#subjectList .duration-input').each(function () {
                durations.push($(this).val());
            });
            $('#subjectList .time-input').each(function () {
                times.push($(this).val());
            });

            // Ensure at least one subject and duration are selected
            if (subjectIds.length === 0 || durations.length === 0 || times.length === 0) {
                alert('Please select at least one subject, duration, and time');
                return;
            }

            // Send the data to the server via AJAX
            $.ajax({
                url: '/assign-subject-to-timeslot',
                type: 'POST',
                data: {
                    date: $('#selectedDate').val(), // Selected date from FullCalendar
                    subject_ids: subjectIds,
                    durations: durations,
                    times: times,
                    location: $('#degree_location').val(),
                    course_id: $('#degree_course').val(),
                    intake_id: $('#degree_intake').val(),
                    semester: $('#degree_semester').val(),
                    _token: '<?php echo e(csrf_token()); ?>'
                },
                success: function (response) {
                    if (response.success) {
                        alert('Subjects assigned successfully!');
                        $('#subjectSelectionModal').modal('hide');
                    } else {
                        alert('Error assigning subjects');
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error assigning subjects:', error);
                    alert('Error occurred while assigning subjects.');
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

            $.ajax({
                url: '/get-timetable-events',
                type: 'GET',
                data: data,
                success: function (response) {
                    $('#calendar').fullCalendar('removeEvents');
                    $('#calendar').fullCalendar('addEventSource', response.events);
                    $('#degreeTimetableSection').show();
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