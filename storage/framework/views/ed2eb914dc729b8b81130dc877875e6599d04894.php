<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Student Registration List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .container-fluid {
            width: 100%;
        }
        .card {
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .card-body {
            padding: 20px;
        }
        .text-center {
            text-align: center;
        }
        .mb-4 {
            margin-bottom: 1.5rem;
        }
        .mb-3 {
            margin-bottom: 1rem;
        }
        .mt-4 {
            margin-top: 1.5rem;
        }
        .mt-2 {
            margin-top: 0.5rem;
        }
        .my-4 {
            margin-top: 1.5rem;
            margin-bottom: 1.5rem;
        }
        hr {
            border: none;
            border-top: 1px solid #ddd;
            margin: 1rem 0;
        }
        h2 {
            font-size: 24px;
            font-weight: bold;
            margin: 0;
        }
        h4 {
            font-size: 18px;
            font-weight: bold;
            margin: 0;
        }
        .table-responsive {
            overflow-x: auto;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1rem;
        }
        .table-bordered {
            border: 1px solid #ddd;
        }
        .table-striped tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .table-light {
            background-color: #f8f9fa;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }
        .d-flex {
            display: flex;
        }
        .justify-content-end {
            justify-content: flex-end;
        }
        .fw-bold {
            font-weight: bold;
        }
        .filter-details {
            margin-bottom: 20px;
            font-size: 14px;
        }
        .filter-details strong {
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="card">
            <div class="card-body">
                <h2 class="text-center mb-4">Student Registration List</h2>
                <hr>

                <!-- Filter Details -->
                <div class="filter-details">
                    <strong>Location:</strong> <?php echo e($locationText); ?><br>
                    <strong>Course:</strong> <?php echo e($courseText); ?><br>
                    <strong>Batch:</strong> <?php echo e($intakeText); ?>

                </div>

                <!-- Student List Table -->
                <div class="mt-4">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Course Registration ID</th>
                                    <th>Student ID</th>
                                    <th>Student Name</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $registration): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php if($registration->student): ?>
                                        <tr>
                                            <td style="text-align: center;"><?php echo e($index + 1); ?></td>
                                            <td style="text-align: center;"><?php echo e($registration->course_registration_id); ?></td>
                                            <td style="text-align: center;"><?php echo e($registration->student->student_id); ?></td>
                                            <td><?php echo e($registration->student->name_with_initials); ?></td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="4" style="text-align: center;">No students found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex justify-content-end mt-2">
                        <span class="fw-bold">Total Students: <?php echo e($total_count); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> <?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/student_list_pdf.blade.php ENDPATH**/ ?>