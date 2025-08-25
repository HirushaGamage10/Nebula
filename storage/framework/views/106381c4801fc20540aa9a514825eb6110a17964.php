<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($courseType); ?> Timetable</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .title {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        
        .subtitle {
            font-size: 16px;
            color: #666;
            margin-bottom: 5px;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-grid {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-row {
            display: table-row;
        }
        
        .info-label {
            display: table-cell;
            font-weight: bold;
            width: 150px;
            padding: 5px;
            border: 1px solid #ddd;
            background-color: #f8f9fa;
        }
        
        .info-value {
            display: table-cell;
            padding: 5px;
            border: 1px solid #ddd;
        }
        
        .timetable-section {
            margin-top: 30px;
        }
        
        .timetable-title {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 15px;
            text-align: center;
            background-color: #f8f9fa;
            padding: 10px;
            border: 1px solid #ddd;
        }
        
        .timetable-grid {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }
        
        .timetable-grid th {
            background-color: #2c3e50;
            color: white;
            padding: 8px;
            text-align: center;
            border: 1px solid #ddd;
            font-size: 11px;
        }
        
        .timetable-grid td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
            vertical-align: middle;
            min-height: 40px;
        }
        
        .time-slot {
            font-weight: bold;
            background-color: #f8f9fa;
        }
        
        .module-cell {
            background-color: #e8f5e8;
            font-weight: bold;
        }
        
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        
        .modules-list {
            margin-top: 20px;
        }
        
        .modules-title {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 10px;
            background-color: #f8f9fa;
            padding: 8px;
            border: 1px solid #ddd;
        }
        
        .module-item {
            padding: 5px;
            border-bottom: 1px solid #eee;
        }
        
        .module-code {
            font-weight: bold;
            color: #2c3e50;
        }
        
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">NEBULA INSTITUTE OF TECHNOLOGY</div>
        <div class="title"><?php echo e($courseType); ?> Timetable</div>
        <div class="subtitle">Academic Year <?php echo e(date('Y')); ?></div>
    </div>

    <div class="info-section">
        <div class="info-grid">
            <div class="info-row">
                <div class="info-label">Course:</div>
                <div class="info-value"><?php echo e($courseName); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Location:</div>
                <div class="info-value"><?php echo e($location); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Intake:</div>
                <div class="info-value"><?php echo e($intake); ?></div>
            </div>
            <?php if(isset($semesterName)): ?>
            <div class="info-row">
                <div class="info-label">Semester:</div>
                <div class="info-value"><?php echo e($semesterName); ?> (<?php echo e($semesterStatus); ?>)</div>
            </div>
            <?php endif; ?>
            <div class="info-row">
                <div class="info-label">Period:</div>
                <div class="info-value"><?php echo e(date('d/m/Y', strtotime($startDate))); ?> - <?php echo e(date('d/m/Y', strtotime($endDate))); ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Generated:</div>
                <div class="info-value"><?php echo e($generatedAt); ?></div>
            </div>
        </div>
    </div>

    <div class="timetable-section">
        <div class="timetable-title">
            <?php if(isset($weekNumber)): ?>
                Week <?php echo e($weekNumber); ?> Timetable
            <?php else: ?>
                Weekly Timetable
            <?php endif; ?>
        </div>
        
        <table class="timetable-grid">
            <thead>
                <tr>
                    <th style="width: 15%;">Time</th>
                    <th style="width: 12%;">
                        Monday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Tuesday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDay()->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Wednesday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDays(2)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Thursday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDays(3)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Friday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDays(4)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Saturday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDays(5)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                    <th style="width: 12%;">
                        Sunday<br>
                        <?php if(isset($weekStartDate)): ?>
                            <small style="font-size: 10px; color: #666;"><?php echo e(\Carbon\Carbon::parse($weekStartDate)->addDays(6)->format('d M')); ?></small>
                        <?php endif; ?>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($timetableData) && is_array($timetableData) && count($timetableData) > 0): ?>
                    <?php $__currentLoopData = $timetableData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td class="time-slot"><?php echo e($row['time']); ?></td>
                            <td class="module-cell"><?php echo e($row['monday'] ? $row['monday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['tuesday'] ? $row['tuesday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['wednesday'] ? $row['wednesday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['thursday'] ? $row['thursday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['friday'] ? $row['friday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['saturday'] ? $row['saturday'] : '-'); ?></td>
                            <td class="module-cell"><?php echo e($row['sunday'] ? $row['sunday'] : '-'); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php else: ?>
                    <!-- No timetable data available -->
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: #666; font-style: italic;">
                            <strong>No Timetable Data Available</strong><br>
                            <small>
                                To generate a proper timetable PDF, please:<br>
                                1. Go to the Timetable page<br>
                                2. Select your course, intake, and semester<br>
                                3. Fill out the timetable with actual modules<br>
                                4. Save the timetable<br>
                                5. Then download the PDF again
                            </small>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(isset($modules) && count($modules) > 0): ?>
    <div class="modules-list">
        <div class="modules-title">Registered Modules for This Semester</div>
        <?php $__currentLoopData = $modules; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $module): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="module-item">
            <span class="module-code"><?php echo e($module['code']); ?></span> - <?php echo e($module['name']); ?>

        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>This timetable was generated on <?php echo e($generatedAt); ?> by NEBULA Institute of Technology</p>
        <p>For any queries, please contact the academic office</p>
    </div>
</body>
</html> <?php /**PATH C:\Users\thisali\Desktop\thisali\Nebula\resources\views/pdf/timetable.blade.php ENDPATH**/ ?>