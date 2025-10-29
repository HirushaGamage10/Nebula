<?php $__env->startSection('title', 'NEBULA | DGMDashboard'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.min.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <div id="pageContent" class="bg-gray-50">

        <!-- Navigation Tabs -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-1 py-3">
                    <button onclick="showTab('overview')" id="tab-overview"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-active">
                        <i class="fas fa-chart-line mr-2"></i>Overview
                    </button>
                    <button onclick="showTab('students')" id="tab-students"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-users mr-2"></i>Students
                    </button>
                    <button onclick="showTab('revenues')" id="tab-revenues"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-dollar-sign mr-2"></i>Revenues
                    </button>
                    <button onclick="showTab('marketing')" id="tab-marketing"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-share-alt mr-2"></i>Marketing
                    </button>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content active">
                <!-- Key Metrics Cards -->
                <div class="flex gap-10">
                    <div class="stat-card bg-white p-2 rounded-xl shadow-sm border-4 border-sky-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Students</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2 p-2" id="totalStudents">-</p>
                                <p class="text-sm text-green-600" id="studentChange"></p>
                            </div>                           
                        </div>
                    </div>

                    <div class="stat-card bg-white p-2 rounded-xl shadow-sm border-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Yearly Revenue</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="yearlyRevenue">-</p>                                
                            </div>
                            
                        </div>
                    </div>

                    <div class="stat-card bg-white p-2 rounded-xl shadow-sm border-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Due this year</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="outstandingCurrentYear">-</p>
                            </div>                            
                        </div>
                    </div>

                    <div class="stat-card bg-white p-2  rounded-xl shadow-sm border-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Outstanding</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="outstanding">-</p>
                            </div>                            
                        </div>
                    </div>

                    
                </div>

                <!-- Quick Charts Grid -->
                <div class="grid gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold mb-4">Students by Location</h3>
                        <div style="height: 300px;">
                            <canvas id="studentsLocationChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue Summary Table -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Revenue Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <?php echo e(date('Y')); ?>

                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        <?php echo e(date('Y') - 1); ?>

                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Growth</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outstanding
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="revenueSummaryBody" class="divide-y divide-gray-200">
                                <!-- JS will populate rows here -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Students Tab -->
            <div id="content-students" class="tab-content">
                <!-- Filter Controls -->

                <div class="bg-white shadow-sm border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-stretch">
                            <div class="filter-card">
                                <div class="flex items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700 mr-2">Year</label>
                                    <select id="yearSelect"
                                        class="border w-full border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                        <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                            <option value="<?php echo e($y); ?>" <?php echo e($y == date('Y') ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="flex flex-row gap-2 mb-6">
                                    <div class="filter-card p-2">
                                        <select id="studentMonthSelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Months</option>
                                            <?php for($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?php echo e(str_pad($m, 2, '0', STR_PAD_LEFT)); ?>">
                                                    <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="filter-card p-2">
                                        <select id="studentDaySelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-card flex flex-col gap-2">
                                <div class="filter-card flex flex-col gap-2">

                                    <!-- Compare and Range selectors at the top -->
                                    <div class="flex flex-row gap-2 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="compareToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Compare</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="rangeSelectorToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Range</span>
                                        </label>
                                    </div>

                                    <!-- Compare year fields -->
                                    <div class="flex flex-row gap-2 mb-2" id="compareFields">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                            <select id="fromYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                            <select id="toYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Range year fields -->
                                    <div class="flex flex-row gap-2 mb-2" id="rangeFields" style="display:none;">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                            <select id="rangeStartYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                            <select id="rangeEndYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>

                                </div>
                            </div>

                            <div class="filter-card md:col-span-2 flex flex-col justify-between">

                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select id="locationSelect"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                    <option value="all">All Locations</option>
                                    <option value="Welisara">Welisara</option>
                                    <option value="Moratuwa">Moratuwa</option>
                                    <option value="Peradeniya">Peradeniya</option>
                                </select>

                                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                <select id="courseSelect"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                    <option value="all">All Courses</option>
                                    <?php $__currentLoopData = \App\Models\Course::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>

                        </div>
                        <div class="mt-4 flex justify-end">
                            <button onclick="loadStudentsData()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                Apply Filters
                            </button>
                        </div>

                    </div>
                </div>


                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h4 class="font-semibold text-gray-900 mb-2">SLT MNIT Welisara</h4>
                        <p class="text-2xl font-bold text-blue-600" id="welisaraStudents">-</p>
                        <p class="text-sm text-gray-600">Students enrolled</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h4 class="font-semibold text-gray-900 mb-2">SLT MNIT Moratuwa</h4>
                        <p class="text-2xl font-bold text-blue-600" id="MoratuwaStudents">-</p>
                        <p class="text-sm text-gray-600">Students enrolled</p>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h4 class="font-semibold text-gray-900 mb-2">SLT MNIT Peradeniya</h4>
                        <p class="text-2xl font-bold text-blue-600" id="peradeniyaStudents">-</p>
                        <p class="text-sm text-gray-600">Students enrolled</p>
                    </div>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm mb-6">
                    <h3 class="text-lg font-semibold mb-4">Students by Location and Course</h3>
                    <div class="relative" style="height: 400px;">
                        <canvas id="chartCombined"></canvas>
                    </div>
                </div>

                <!-- Students Tab Upload/Download -->
                <div class="flex gap-2 mb-4">
                    <button class="px-3 py-2 bg-green-600 text-white rounded" onclick="downloadStudentTemplate()">Download
                        Student Excel Template</button>
                    <button class="px-3 py-2 bg-blue-600 text-white rounded"
                        onclick="showModal('studentUploadModal')">Upload Student Data</button>
                </div>

                <div id="studentUploadModal" style="display:none;"
                    class="fixed inset-0 flex items-center justify-center z-50 modal-overlay" aria-hidden="true">
                    <form id="studentUploadForm" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-96"
                        method="POST" action="<?php echo e(route('bulk.student.upload')); ?>">
                        <?php echo csrf_field(); ?>
                        <h3 class="mb-4 font-bold text-lg">Upload Student Excel</h3>
                        <input type="file" name="student_excel" accept=".xlsx,.xls,.csv" required class="mb-4">
                        <div class="flex gap-2 justify-end">
                            <button type="button" onclick="hideModal('studentUploadModal')"
                                class="px-3 py-1 bg-gray-400 text-white rounded">Cancel</button>
                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Upload</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Revenues Tab -->
            <div id="content-revenues" class="tab-content">
                <!-- Filter Controls -->
                <div class="bg-white shadow-sm border-b mb-6">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-stretch">
                            <div class="filter-card">
                                <div class="flex items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700 mr-2">Year</label>
                                    <select id="revenueYearSelect"
                                        class="border w-full border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                        <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                            <option value="<?php echo e($y); ?>" <?php echo e($y == date('Y') ? 'selected' : ''); ?>><?php echo e($y); ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                                <div class="flex flex-row gap-2 mb-6">
                                    <div class="filter-card p-2">
                                        <select id="revenueMonthSelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Months</option>
                                            <?php for($m = 1; $m <= 12; $m++): ?>
                                                <option value="<?php echo e(str_pad($m, 2, '0', STR_PAD_LEFT)); ?>">
                                                    <?php echo e(date('F', mktime(0, 0, 0, $m, 1))); ?>

                                                </option>
                                            <?php endfor; ?>
                                        </select>
                                    </div>
                                    <div class="filter-card p-2">
                                        <select id="revenueDaySelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-card flex flex-col gap-2">
                                <div class="filter-card flex flex-col gap-2">
                                    <div class="flex flex-row gap-2 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="revenueCompareToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Compare</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="revenueRangeSelectorToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Range</span>
                                        </label>
                                    </div>
                                    <!-- Compare year fields -->
                                    <div class="flex flex-row gap-2 mb-2" id="revenueCompareFields" style="display:none;">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                                            <select id="revenueFromYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                                            <select id="revenueToYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <!-- Range year fields -->
                                    <div class="flex flex-row gap-2 mb-2" id="revenueRangeFields" style="display:none;">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Start Year</label>
                                            <select id="revenueRangeStartYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">End Year</label>
                                            <select id="revenueRangeEndYearSelect"
                                                class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-sm"
                                                disabled>
                                                <?php for($y = date('Y'); $y >= 2010; $y--): ?>
                                                    <option value="<?php echo e($y); ?>"><?php echo e($y); ?></option>
                                                <?php endfor; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-card md:col-span-2 flex flex-col justify-between">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                                <select id="revenueLocationSelect"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                    <option value="all">All Locations</option>
                                    <option value="Welisara">Welisara</option>
                                    <option value="Moratuwa">Moratuwa</option>
                                    <option value="Peradeniya">Peradeniya</option>
                                </select>

                                <label class="block text-sm font-medium text-gray-700 mb-1">Course</label>
                                <select id="revenueCourseSelect"
                                    class="w-full border border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                    <option value="all">All Courses</option>
                                    <?php $__currentLoopData = \App\Models\Course::all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $course): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($course->course_id); ?>"><?php echo e($course->course_name); ?></option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end">
                            <button onclick="loadRevenueData()"
                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 text-sm font-medium">
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
                <div class="grid  mb-6 gap-4">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold mb-4">Revenue</h3>
                        <div style="height: 500px;">
                            <canvas id="revenueYearChart"></canvas>
                        </div>
                    </div>
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold mb-4">Outstanding</h3>
                        <div style="height: 300px;">
                            <canvas id="outstandingYearChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Revenues Tab Upload/Download -->
                <div class="flex gap-2 mb-4">
                    <button class="px-3 py-2 bg-green-600 text-white rounded" onclick="downloadRevenueTemplate()">Download
                        Revenue Excel Template</button>
                    <button class="px-3 py-2 bg-blue-600 text-white rounded"
                        onclick="showModal('revenueUploadModal')">Upload Revenue Data</button>
                </div>

                <div id="revenueUploadModal" style="display:none;"
                    class="fixed inset-0 flex items-center justify-center z-50 modal-overlay" aria-hidden="true">
                    <form id="revenueUploadForm" enctype="multipart/form-data" class="bg-white p-6 rounded shadow w-96"
                        method="POST" action="<?php echo e(route('bulk.revenue.upload')); ?>">
                        <?php echo csrf_field(); ?>
                        <h3 class="mb-4 font-bold text-lg">Upload Revenue Excel</h3>
                        <input type="file" name="revenue_excel" accept=".xlsx,.xls,.csv" required class="mb-4">
                        <div class="flex gap-2 justify-end">
                            <button type="button" onclick="hideModal('revenueUploadModal')"
                                class="px-3 py-1 bg-gray-400 text-white rounded">Cancel</button>
                            <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Upload</button>
                        </div>
                    </form>
                </div>
            </div>


            <!-- Marketing Tab -->
            <div id="content-marketing" class="tab-content">
                <!-- Filter Controls -->

                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Marketing Survey Analysis</h3>
                    <div style="height: 400px;">
                        <canvas id="marketingSurveyChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .tab-active {
            background-color: #2563eb !important;
            color: white !important;
        }

        .tab-inactive {
            background-color: #e5e7eb !important;
            color: #374151 !important;
        }

        .tab-inactive:hover {
            background-color: #d1d5db !important;
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
        }

        .filter-card {
            background-color: white;
            padding: 0.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e7eb;
            width: 100%;
        }

        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        select:disabled {
            background-color: #f3f4f6 !important;
            color: #9ca3af !important;
            cursor: not-allowed;
        }

        .modal-overlay {
            display: none;
            align-items: center;
            justify-content: center;
            background: rgba(255, 255, 255, 0);
            /* transparent so no darkening */
            -webkit-backdrop-filter: blur(6px);
            backdrop-filter: blur(6px);
            transition: opacity 120ms ease;
            padding: 1.25rem;

        }

        .modal-overlay.show {
            display: flex !important;
            opacity: 1;

        }

        .modal-overlay form {
            z-index: 60;
        }
    </style>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        let currentCharts = {};
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

        function downloadStudentTemplate() {
            window.location.href = "<?php echo e(route('bulk.student.template')); ?>";
        }
        function downloadRevenueTemplate() {
            window.location.href = "<?php echo e(route('bulk.revenue.template')); ?>";
        }
        // Tab switching
        function showTab(tabName) {
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });

            document.getElementById('content-' + tabName)?.classList.add('active');

            document.querySelectorAll('[id^="tab-"]').forEach(button => {
                button.classList.remove('tab-active');
                button.classList.add('tab-inactive');
            });

            const activeButton = document.getElementById('tab-' + tabName);
            if (activeButton) {
                activeButton.classList.remove('tab-inactive');
                activeButton.classList.add('tab-active');
            }

            setTimeout(() => initializeChartsForTab(tabName), 100);
        }

        function showModal(id) {
            const modal = document.getElementById(id);
            const page = document.getElementById('pageContent');
            if (modal) {
                modal.classList.add('show');
                modal.style.display = 'flex';
                modal.classList.add('modal-overlay');
            }
            if (page) page.classList.add('blurred');
        }

        function hideModal(id) {
            const modal = document.getElementById(id);
            const page = document.getElementById('pageContent');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
            if (page) page.classList.remove('blurred');
        }

        function initializeChartsForTab(tabName) {
            switch (tabName) {
                case 'overview':
                    loadOverviewData();
                    break;
                case 'students':
                    loadStudentsData();
                    break;
                case 'revenues':
                    loadRevenueData();
                    loadOutstandingData();
                    break;
                case 'marketing':
                    loadMarketingData();
                    break;
            }
        }

        // Load Overview Data
        async function loadOverviewData() {
            const params = {
                year: new Date().getFullYear(),
                location: 'all',
                course: 'all'
            };

            try {
                const response = await fetch(`/api/dashboard/overview?${new URLSearchParams(params)}`);
                const data = await response.json();

                document.getElementById('totalStudents').textContent = data.totalStudents;
                document.getElementById('yearlyRevenue').textContent = 'Rs. ' + data.yearlyRevenue;
                document.getElementById('outstanding').textContent = 'Rs. ' + data.outstanding;
                if (document.getElementById('outstandingCurrentYear')) {
                    document.getElementById('outstandingCurrentYear').textContent = 'Rs. ' + (data.outstandingCurrentYear ?? '0.00');
                }


                // Populate Revenue Summary Table
                const tbody = document.getElementById('revenueSummaryBody');
                tbody.innerHTML = '';
                if (data.locationSummary) {
                    data.locationSummary.forEach(row => {
                        tbody.innerHTML += `
                                                                                                                                                                                                                                        <tr>
                                                                                                                                                                                                                                            <td class="px-6 py-4 text-sm font-medium text-gray-900">${row.location}</td>
                                                                                                                                                                                                                                            <td class="px-6 py-4 text-sm text-gray-900">Rs. ${row.current_year}</td>
                                                                                                                                                                                                                                            <td class="px-6 py-4 text-sm text-gray-900">Rs. ${row.previous_year}</td>
                                                                                                                                                                                                                                            <td class="px-6 py-4 text-sm ${row.growth >= 0 ? 'text-green-600' : 'text-red-600'}">${row.growth >= 0 ? '+' : ''}${row.growth}%</td>
                                                                                                                                                                                                                                            <td class="px-6 py-4 text-sm text-gray-900">Rs. ${row.outstanding}</td>
                                                                                                                                                                                                                                        </tr>
                                                                                                                                                                                                                                    `;
                    });
                }

                // Fetch students by location and show in chart + numbers
                const studentsResponse = await fetch(`/api/dashboard/students-by-location?year=${params.year}`);
                const studentsData = await studentsResponse.json();

                // Show numbers below chart (add these spans in your blade if you want)
                // Example:
                // <div class="flex gap-4 mt-2 justify-center">
                //   <span id="studentsWelisara"></span>
                //   <span id="studentsMoratuwa"></span>
                //   <span id="studentsPeradeniya"></span>
                // </div>
                if (document.getElementById('studentsWelisara')) {
                    document.getElementById('studentsWelisara').textContent =
                        `Welisara: ${studentsData.find(d => d.institute_location === 'Welisara')?.count ?? 0}`;
                }
                if (document.getElementById('studentsMoratuwa')) {
                    document.getElementById('studentsMoratuwa').textContent =
                        `Moratuwa: ${studentsData.find(d => d.institute_location === 'Moratuwa')?.count ?? 0}`;
                }
                if (document.getElementById('studentsPeradeniya')) {
                    document.getElementById('studentsPeradeniya').textContent =
                        `Peradeniya: ${studentsData.find(d => d.institute_location === 'Peradeniya')?.count ?? 0}`;
                }

                // Draw chart
                const canvas = document.getElementById('studentsLocationChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.studentsLocation) {
                        currentCharts.studentsLocation.destroy();
                    }
                    currentCharts.studentsLocation = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: studentsData.map(d => d.institute_location),
                            datasets: [{
                                data: studentsData.map(d => d.count),
                                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }

                loadMonthlyTrend();
                loadLocationBreakdown();
            } catch (error) {
                console.error('Error loading overview data:', error);
            }
        }

        // Load Location Breakdown
        async function loadLocationBreakdown() {
            const params = getFilterParams();

            try {
                const response = await fetch(`/api/dashboard/students-by-location?${new URLSearchParams(params)}`);
                const data = await response.json();

                const canvas = document.getElementById('studentsLocationChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.studentsLocation) {
                        currentCharts.studentsLocation.destroy();
                    }
                    // Always show all locations, even if count is zero
                    const allLocations = ['Welisara', 'Moratuwa', 'Peradeniya'];
                    const chartData = allLocations.map(loc => {
                        const found = studentsData.find(d => d.institute_location === loc);
                        return found ? found.count : 0;
                    });
                    currentCharts.studentsLocation = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: allLocations,
                            datasets: [{
                                data: chartData,
                                backgroundColor: ['#3B82F6', '#10B981', '#F59E0B'],
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'bottom' }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading location breakdown:', error);
            }
        }

        // Load Students Data
        async function loadStudentsData() {
            const params = getFilterParams();

            try {
                const response = await fetch(`/api/dashboard/students-data?${new URLSearchParams(params)}`);
                const data = await response.json();

                // Update location cards
                const welisara = data.filter(d => d.institute_location === 'Welisara').reduce((sum, d) => sum + d.count, 0);
                const Moratuwa = data.filter(d => d.institute_location === 'Moratuwa').reduce((sum, d) => sum + d.count, 0);
                const peradeniya = data.filter(d => d.institute_location === 'Peradeniya').reduce((sum, d) => sum + d.count, 0);

                document.getElementById('welisaraStudents').textContent = welisara;
                document.getElementById('MoratuwaStudents').textContent = Moratuwa;
                document.getElementById('peradeniyaStudents').textContent = peradeniya;

                // Create combined chart
                const years = [...new Set(data.map(d => d.year))].sort();
                const locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
                const colors = ['#3B82F6', '#10B981', '#F59E0B'];

                const datasets = locations.map((loc, idx) => ({
                    label: loc,
                    data: years.map(year => {
                        const found = data.find(d => d.year === year && d.institute_location === loc);
                        return found ? found.count : 0;
                    }),
                    backgroundColor: colors[idx]
                }));

                const canvas = document.getElementById('chartCombined');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.combined) {
                        currentCharts.combined.destroy();
                    }
                    currentCharts.combined = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: years,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            },
                            scales: {
                                y: { beginAtZero: true }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading students data:', error);
            }
        }

        // Load Revenue Data
        async function loadRevenueData() {
            const params = getRevenueFilterParams();

            try {
                const res = await fetch(`/api/dashboard/revenue-by-year-course?${new URLSearchParams(params)}`);
                const data = await res.json(); // [{year, location, course_name, revenue}, ...]

                // Get all years in data
                let years = [...new Set(data.map(d => d.year))].sort();

                // Filter years for compare/range
                if (params.compare && params.from_year && params.to_year) {
                    // Only show the two selected years
                    years = [parseInt(params.from_year), parseInt(params.to_year)].sort();
                } else if (params.range && params.range_start_year && params.range_end_year) {
                    // Show all years in the range
                    const start = parseInt(params.range_start_year);
                    const end = parseInt(params.range_end_year);
                    years = [];
                    for (let y = start; y <= end; y++) years.push(y);
                }

                // Get all locations and courses
                const locations = [...new Set(data.map(d => d.location))];
                const courses = [...new Set(data.map(d => d.course_name))];
                const colors = [
                    '#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#6366F1', '#EC4899', '#22D3EE', '#A3E635'
                ];

                // Build all course+location combinations
                const combos = [];
                locations.forEach(loc => {
                    courses.forEach(course => {
                        combos.push({ loc, course });
                    });
                });

                // Build datasets: one per course+location, data for each year
                const datasets = combos.map((combo, idx) => ({
                    label: `${combo.course} (${combo.loc})`,
                    data: years.map(year => {
                        const found = data.find(d => d.year == year && d.location === combo.loc && d.course_name === combo.course);
                        return found ? found.revenue : 0;
                    }),
                    backgroundColor: colors[idx % colors.length]
                }));

                // Chart labels: years
                const labels = years;

                // Draw chart
                const canvas = document.getElementById('revenueYearChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.revenueYearCourse) {
                        currentCharts.revenueYearCourse.destroy();
                    }
                    currentCharts.revenueYearCourse = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: datasets
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'top' } },
                            scales: {
                                x: { stacked: false },
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return 'Rs. ' + (value / 1000000).toFixed(1) + 'M';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading revenue data:', error);
            }
        }

        // Load Future Projections
        async function loadFutureProjections() {
            try {
                const response = await fetch('/api/dashboard/future-projections');
                const data = await response.json();

                const actual = data.filter(d => d.type === 'actual');
                const projected = data.filter(d => d.type === 'projected');

                const canvas = document.getElementById('futureRevenueChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.futureRevenue) {
                        currentCharts.futureRevenue.destroy();
                    }
                    currentCharts.futureRevenue = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: data.map(d => d.label),
                            datasets: [
                                {
                                    label: 'Actual/Projected Revenue',
                                    data: data.map(d => d.revenue),
                                    borderColor: '#3B82F6',
                                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                },
                                {
                                    label: 'Conservative Estimate',
                                    data: data.map(d => d.conservative || d.revenue),
                                    borderColor: '#10B981',
                                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                                    tension: 0.4,
                                    fill: true
                                }
                            ]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { position: 'top' } },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        callback: function (value) {
                                            return 'Rs. ' + (value / 1000).toFixed(0) + 'K';
                                        }
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading future projections:', error);
            }
        }

        async function loadOutstandingData() {
            const params = getRevenueFilterParams();

            try {
                const res = await fetch(`/api/dashboard/outstanding-by-year-course?${new URLSearchParams(params)}`);
                const data = await res.json(); // [{year, location, outstanding}, ...]

                // Get all years in data
                let years = [...new Set(data.map(d => d.year))].sort();

                // Filter years for compare/range
                if (params.compare && params.from_year && params.to_year) {
                    years = [parseInt(params.from_year), parseInt(params.to_year)].sort();
                } else if (params.range && params.range_start_year && params.range_end_year) {
                    const start = parseInt(params.range_start_year);
                    const end = parseInt(params.range_end_year);
                    years = [];
                    for (let y = start; y <= end; y++) years.push(y);
                }

                // Get all locations
                const locations = ['Welisara', 'Moratuwa', 'Peradeniya'];
                const colors = ['#EF4444', '#6366F1', '#10B981'];

                // Sum outstanding for each location across selected years
                const locationOutstanding = locations.map(loc => {
                    return data
                        .filter(d => years.includes(d.year) && d.location === loc)
                        .reduce((sum, d) => sum + (d.outstanding || 0), 0);
                });

                // Draw pie chart
                const canvas = document.getElementById('outstandingYearChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.outstandingYearChart) {
                        currentCharts.outstandingYearChart.destroy();
                    }
                    currentCharts.outstandingYearChart = new Chart(ctx, {
                        type: 'pie',
                        data: {
                            labels: locations,
                            datasets: [{
                                data: locationOutstanding,
                                backgroundColor: colors,
                                borderWidth: 2,
                                borderColor: '#fff'
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: 'top' }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading outstanding data:', error);
            }
        }

        async function loadMarketingData() {
            const year = new Date().getFullYear();
            try {
                const res = await fetch(`/api/dashboard/marketing-data?year=${year}`);
                const data = await res.json();

                const canvas = document.getElementById('marketingSurveyChart');
                if (canvas) {
                    const ctx = canvas.getContext('2d');
                    if (currentCharts.marketingSurvey) {
                        currentCharts.marketingSurvey.destroy();
                    }
                    currentCharts.marketingSurvey = new Chart(ctx, {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Responses',
                                data: data.counts,
                                backgroundColor: [
                                    '#1877F2', '#E4405F', '#F59E0B', '#EF4444', '#6366F1', '#10B981', '#A3E635'
                                ],
                                borderWidth: 2,
                                borderColor: '#fff',
                                borderRadius: {
                                    topLeft: 12,
                                    topRight: 12,
                                    bottomLeft: 0,
                                    bottomRight: 0
                                }
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { display: false }
                            },
                            scales: {
                                y: {
                                    beginAtZero: true,
                                    ticks: {
                                        stepSize: 1
                                    }
                                }
                            }
                        }
                    });
                }
            } catch (error) {
                console.error('Error loading marketing data:', error);
            }
        }

        // Get filter parameters
        function getFilterParams() {
            const compareToggle = document.getElementById('compareToggle').checked;
            const rangeToggle = document.getElementById('rangeSelectorToggle').checked;

            if (rangeToggle) {
                return {
                    range: true,
                    range_start_year: document.getElementById('rangeStartYearSelect').value,
                    range_end_year: document.getElementById('rangeEndYearSelect').value,
                    month: document.getElementById('studentMonthSelect').value,
                    date: document.getElementById('studentDaySelect').value,
                    location: document.getElementById('locationSelect').value,
                    course: document.getElementById('courseSelect').value
                };
            } else if (compareToggle) {
                return {
                    compare: true,
                    from_year: document.getElementById('fromYearSelect').value,
                    to_year: document.getElementById('toYearSelect').value,
                    month: document.getElementById('studentMonthSelect').value,
                    date: document.getElementById('studentDaySelect').value,
                    location: document.getElementById('locationSelect').value,
                    course: document.getElementById('courseSelect').value
                };
            } else {
                return {
                    year: document.getElementById('yearSelect').value,
                    month: document.getElementById('studentMonthSelect').value,
                    date: document.getElementById('studentDaySelect').value,
                    location: document.getElementById('locationSelect').value,
                    course: document.getElementById('courseSelect').value
                };
            }
        }

        function getRevenueFilterParams() {
            const compareToggle = document.getElementById('revenueCompareToggle').checked;
            const rangeToggle = document.getElementById('revenueRangeSelectorToggle').checked;

            if (rangeToggle) {
                return {
                    location: document.getElementById('revenueLocationSelect').value,
                    course: document.getElementById('revenueCourseSelect').value,
                    range: true,
                    range_start_year: document.getElementById('revenueRangeStartYearSelect').value,
                    range_end_year: document.getElementById('revenueRangeEndYearSelect').value
                };
            } else if (compareToggle) {
                return {
                    location: document.getElementById('revenueLocationSelect').value,
                    course: document.getElementById('revenueCourseSelect').value,
                    compare: true,
                    from_year: document.getElementById('revenueFromYearSelect').value,
                    to_year: document.getElementById('revenueToYearSelect').value
                };
            } else {
                return {
                    year: document.getElementById('revenueYearSelect').value,
                    month: document.getElementById('revenueMonthSelect').value,
                    date: document.getElementById('revenueDaySelect').value,
                    location: document.getElementById('revenueLocationSelect').value,
                    course: document.getElementById('revenueCourseSelect').value
                };
            }
        }




        document.addEventListener('DOMContentLoaded', function () {
            const compareToggle = document.getElementById('compareToggle');
            const rangeToggle = document.getElementById('rangeSelectorToggle');
            const yearSelect = document.getElementById('yearSelect');
            const studentMonthSelect = document.getElementById('studentMonthSelect');
            const studentDaySelect = document.getElementById('studentDaySelect');
            const compareFields = document.getElementById('compareFields');
            const rangeFields = document.getElementById('rangeFields');
            const fromYearSelect = document.getElementById('fromYearSelect');
            const toYearSelect = document.getElementById('toYearSelect');
            const rangeStartYearSelect = document.getElementById('rangeStartYearSelect');
            const rangeEndYearSelect = document.getElementById('rangeEndYearSelect');
            const revenueCompareToggle = document.getElementById('revenueCompareToggle');
            const revenueRangeToggle = document.getElementById('revenueRangeSelectorToggle');
            const revenueYearSelect = document.getElementById('revenueYearSelect');
            const revenueMonthSelect = document.getElementById('revenueMonthSelect');
            const revenueDaySelect = document.getElementById('revenueDaySelect');
            const revenueCompareFields = document.getElementById('revenueCompareFields');
            const revenueRangeFields = document.getElementById('revenueRangeFields');
            const revenueFromYearSelect = document.getElementById('revenueFromYearSelect');
            const revenueToYearSelect = document.getElementById('revenueToYearSelect');
            const revenueRangeStartYearSelect = document.getElementById('revenueRangeStartYearSelect');
            const revenueRangeEndYearSelect = document.getElementById('revenueRangeEndYearSelect');

            function updateSelectors() {
                if (rangeToggle.checked) {
                    yearSelect.disabled = true;
                    studentMonthSelect.disabled = true;
                    studentDaySelect.disabled = true;
                    compareFields.style.display = 'none';
                    rangeFields.style.display = 'flex';
                    rangeStartYearSelect.disabled = false;
                    rangeEndYearSelect.disabled = false;
                    fromYearSelect.disabled = true;
                    toYearSelect.disabled = true;
                } else if (compareToggle.checked) {
                    yearSelect.disabled = true;
                    studentMonthSelect.disabled = true;
                    studentDaySelect.disabled = true;
                    compareFields.style.display = 'flex';
                    rangeFields.style.display = 'none';
                    fromYearSelect.disabled = false;
                    toYearSelect.disabled = false;
                    rangeStartYearSelect.disabled = true;
                    rangeEndYearSelect.disabled = true;
                } else {
                    yearSelect.disabled = false;
                    studentMonthSelect.disabled = false;
                    studentDaySelect.disabled = false;
                    compareFields.style.display = 'none';
                    rangeFields.style.display = 'none';
                    fromYearSelect.disabled = true;
                    toYearSelect.disabled = true;
                    rangeStartYearSelect.disabled = true;
                    rangeEndYearSelect.disabled = true;
                }
            }

            compareToggle.addEventListener('change', function () {
                if (compareToggle.checked) rangeToggle.checked = false;
                updateSelectors();
            });
            rangeToggle.addEventListener('change', function () {
                if (rangeToggle.checked) compareToggle.checked = false;
                updateSelectors();
            });

            updateSelectors();

            function updateRevenueSelectors() {
                if (revenueRangeToggle.checked) {
                    revenueYearSelect.disabled = true;
                    revenueMonthSelect.disabled = true;
                    revenueDaySelect.disabled = true;
                    revenueCompareFields.style.display = 'none';
                    revenueRangeFields.style.display = 'flex';
                    revenueRangeStartYearSelect.disabled = false;
                    revenueRangeEndYearSelect.disabled = false;
                    revenueFromYearSelect.disabled = true;
                    revenueToYearSelect.disabled = true;
                } else if (revenueCompareToggle.checked) {
                    revenueYearSelect.disabled = true;
                    revenueMonthSelect.disabled = true;
                    revenueDaySelect.disabled = true;
                    revenueCompareFields.style.display = 'flex';
                    revenueRangeFields.style.display = 'none';
                    revenueFromYearSelect.disabled = false;
                    revenueToYearSelect.disabled = false;
                    revenueRangeStartYearSelect.disabled = true;
                    revenueRangeEndYearSelect.disabled = true;
                } else {
                    revenueYearSelect.disabled = false;
                    revenueMonthSelect.disabled = false;
                    revenueDaySelect.disabled = false;
                    revenueCompareFields.style.display = 'none';
                    revenueRangeFields.style.display = 'none';
                    revenueFromYearSelect.disabled = true;
                    revenueToYearSelect.disabled = true;
                    revenueRangeStartYearSelect.disabled = true;
                    revenueRangeEndYearSelect.disabled = true;
                }
            }

            revenueCompareToggle.addEventListener('change', function () {
                if (revenueCompareToggle.checked) revenueRangeToggle.checked = false;
                updateRevenueSelectors();
            });
            revenueRangeToggle.addEventListener('change', function () {
                if (revenueRangeToggle.checked) revenueCompareToggle.checked = false;
                updateRevenueSelectors();
            });

            updateRevenueSelectors();

            // wire month/year change events to populate days and enable/disable day select
            studentMonthSelect.addEventListener('change', () => {
                populateDays('studentDaySelect', 'yearSelect', 'studentMonthSelect');
                // update selectors in case month cleared
                updateSelectors();
            });
            yearSelect.addEventListener('change', () => {
                populateDays('studentDaySelect', 'yearSelect', 'studentMonthSelect');
            });

            revenueMonthSelect.addEventListener('change', () => {
                populateDays('revenueDaySelect', 'revenueYearSelect', 'revenueMonthSelect');
                updateRevenueSelectors();
            });
            revenueYearSelect.addEventListener('change', () => {
                populateDays('revenueDaySelect', 'revenueYearSelect', 'revenueMonthSelect');
            });

            // populate on load (will disable day selects if no month)
            populateDays('studentDaySelect', 'yearSelect', 'studentMonthSelect');
            populateDays('revenueDaySelect', 'revenueYearSelect', 'revenueMonthSelect');

        });


        function populateDays(daySelectId, yearSelectId, monthSelectId) {
            const daySelect = document.getElementById(daySelectId);
            const yearEl = document.getElementById(yearSelectId);
            const monthEl = document.getElementById(monthSelectId);
            if (!daySelect || !yearEl || !monthEl) return;

            const year = yearEl.value || new Date().getFullYear();
            const month = monthEl.value;

            // Reset options
            daySelect.innerHTML = '<option value="">All Days</option>';

            if (month) {
                // month is in "MM" format; JS Date expects month index for next month, so pass parseInt(month)
                const daysInMonth = new Date(year, parseInt(month), 0).getDate();
                for (let d = 1; d <= daysInMonth; d++) {
                    const dayStr = d.toString().padStart(2, '0');
                    daySelect.innerHTML += `<option value="${dayStr}">${dayStr}</option>`;
                }
                daySelect.disabled = false;
            } else {
                // No month selected -> disable day selector
                daySelect.disabled = true;
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            function bindUpload(formId, modalId, onSuccess) {
                const form = document.getElementById(formId);
                if (!form) return;
                form.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    const formData = new FormData(form);
                    try {
                        const res = await fetch(form.action, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': csrfToken,
                                'Accept': 'application/json'
                            },
                            body: formData,
                            credentials: 'same-origin'
                        });

                        // Handle validation errors (422) with useful messages
                        if (res.status === 422) {
                            const body = await res.json().catch(() => null);
                            const errs = (body && body.errors) ? body.errors : null;
                            if (errs) {
                                const messages = [];
                                Object.values(errs).forEach(arr => {
                                    if (Array.isArray(arr)) messages.push(...arr);
                                    else messages.push(arr);
                                });
                                throw new Error(messages.join(' ; ') || body.message || 'Validation failed');
                            }
                            throw new Error(body?.message || 'Validation failed');
                        }

                        if (!res.ok) {
                            // try to parse JSON error message, otherwise text/html
                            const contentType = res.headers.get('content-type') || '';
                            if (contentType.includes('application/json')) {
                                const body = await res.json().catch(() => null);
                                throw new Error(body?.message || JSON.stringify(body) || `Upload failed (${res.status})`);
                            } else {
                                const text = await res.text();
                                // If HTML returned, give a concise hint and log details to console
                                console.error('Server response (non-JSON):', text);
                                throw new Error(`Server error (${res.status}). See console/network tab for details.`);
                            }
                        }

                        const ct = res.headers.get('content-type') || '';
                        if (!ct.includes('application/json')) {
                            const text = await res.text();
                            throw new Error(text || 'Server did not return JSON');
                        }

                        const json = await res.json();
                        if (json.success) {
                            if (modalId) document.getElementById(modalId).style.display = 'none';
                            if (onSuccess) onSuccess(json);
                            alert('Uploaded ' + (json.inserted ?? 0) + ' rows.');
                        } else {
                            throw new Error(json.message || 'Upload failed');
                        }
                    } catch (err) {
                        console.error('Upload error details:', err);
                        alert('Upload error: ' + (err.message || err));
                    }
                });
            }

            bindUpload('studentUploadForm', 'studentUploadModal', () => loadStudentsData());
            bindUpload('revenueUploadForm', 'revenueUploadModal', () => { loadRevenueData(); loadOutstandingData(); });
        });

        // Download exports (actual uploaded table data)
        function downloadStudentExport() {
            window.location.href = "<?php echo e(route('bulk.student.export')); ?>";
        }
        function downloadRevenueExport() {
            window.location.href = "<?php echo e(route('bulk.revenue.export')); ?>";
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function () {
            showTab('overview');
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Applications/XAMPP/xamppfiles/htdocs/nebula final/Nebula/resources/views/dgmdashboard.blade.php ENDPATH**/ ?>