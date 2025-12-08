<?php $__env->startSection('title', 'NEBULA | Marketing Manager Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <link rel="stylesheet" href="<?php echo e(asset('css/styles.min.css')); ?>">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div id="pageContent" class="bg-gray-50">
        <!-- Header -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
            <h1 class="text-3xl font-bold text-gray-800">🎯 Marketing Manager Dashboard</h1>
            <p class="text-gray-600 mt-1">Track campaign performance and student acquisition metrics</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-sky-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Total Registered Students</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2" id="totalRegistered">-</p>
                        <p class="text-sm text-gray-500 mt-1">Active registrations</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">This Month</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2" id="thisMonth">-</p>
                        <p class="text-sm text-gray-500 mt-1">New registrations</p>
                        <div id="growthIndicator" style="display:none;" class="mt-2 inline-block px-3 py-1 rounded-full text-xs font-semibold">
                            <span id="growthValue">-</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Total Students</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2" id="totalStudents">-</p>
                        <p class="text-sm text-gray-500 mt-1">In database</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-red-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Last Month</p>
                        <p class="text-3xl font-bold text-gray-900 mt-2" id="lastMonth">-</p>
                        <p class="text-sm text-gray-500 mt-1">Registrations</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Marketing Survey Analysis (Full Width) -->
        <div class="bg-white p-6 rounded-lg shadow-sm mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                📊 Marketing Survey Analysis - Channel Performance
            </h2>
            <div style="height: 450px;">
                <canvas id="marketingSurveyChart"></canvas>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Monthly Trend -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                    📈 12-Month Registration Trend
                </h2>
                <div style="height: 350px;">
                    <canvas id="monthlyTrendChart"></canvas>
                </div>
            </div>

            <!-- Location Distribution -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                    📍 Registrations by Location
                </h2>
                <div style="height: 350px;">
                    <canvas id="locationChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Courses & Marketing ROI -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                    🏆 Top Performing Courses
                </h2>
                <div style="height: 350px;">
                    <canvas id="topCoursesChart"></canvas>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                    💰 Marketing ROI by Source
                </h2>
                <div style="height: 350px;">
                    <canvas id="roiChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-gradient">
                📋 Recent Registrations
            </h2>
            <div id="recentRegistrationsContainer">
                <div class="text-center py-10 text-gray-500">Loading registrations...</div>
            </div>
        </div>
    </div>

    <style>
        .border-gradient {
            border-image: linear-gradient(90deg, #f093fb 0%, #f5576c 100%) 1;
        }

        .growth-positive {
            background: #d4edda;
            color: #155724;
        }

        .growth-negative {
            background: #f8d7da;
            color: #721c24;
        }

        .registrations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .registrations-table thead {
            background: linear-gradient(90deg, #f093fb 0%, #f5576c 100%);
        }

        .registrations-table th {
            padding: 14px;
            text-align: left;
            font-weight: 600;
            color: white;
            font-size: 13px;
        }

        .registrations-table td {
            padding: 14px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            color: #666;
        }

        .registrations-table tbody tr:hover {
            background: #fff5f8;
        }

        .status-badge {
            padding: 5px 14px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }

        .status-registered {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .source-tag {
            display: inline-block;
            padding: 3px 10px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 10px;
            font-size: 11px;
            margin: 2px;
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fetch overview metrics
        async function fetchOverviewMetrics() {
            try {
                const response = await fetch('/api/marketing-manager/overview', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                document.getElementById('totalRegistered').textContent = data.total_registered;
                document.getElementById('thisMonth').textContent = data.this_month_registrations;
                document.getElementById('lastMonth').textContent = data.last_month_registrations;
                document.getElementById('totalStudents').textContent = data.total_students;
                
                // Show growth indicator
                const growthIndicator = document.getElementById('growthIndicator');
                const growthValue = document.getElementById('growthValue');
                if (data.growth_percentage !== 0) {
                    growthIndicator.style.display = 'inline-block';
                    growthIndicator.className = 'mt-2 inline-block px-3 py-1 rounded-full text-xs font-semibold ' + 
                        (data.growth_percentage > 0 ? 'growth-positive' : 'growth-negative');
                    growthValue.textContent = (data.growth_percentage > 0 ? '+' : '') + data.growth_percentage + '% vs last month';
                }
            } catch (error) {
                console.error('Error fetching overview metrics:', error);
            }
        }

        // Fetch and render marketing survey chart
        async function fetchMarketingSurveyData() {
            try {
                const response = await fetch('/api/marketing-manager/marketing-survey', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('marketingSurveyChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.source),
                        datasets: [{
                            label: 'Number of Students',
                            data: data.map(item => item.count),
                            backgroundColor: data.map((_, index) => {
                                const colors = [
                                    'rgba(240, 147, 251, 0.8)',
                                    'rgba(245, 87, 108, 0.8)',
                                    'rgba(100, 181, 246, 0.8)',
                                    'rgba(129, 199, 132, 0.8)',
                                    'rgba(255, 167, 38, 0.8)',
                                    'rgba(171, 71, 188, 0.8)'
                                ];
                                return colors[index % colors.length];
                            }),
                            borderWidth: 0,
                            borderRadius: 8
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 15,
                                borderRadius: 8,
                                callbacks: {
                                    label: function(context) {
                                        const item = data[context.dataIndex];
                                        return [
                                            'Students: ' + item.count,
                                            'Percentage: ' + item.percentage + '%'
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 },
                                grid: { color: 'rgba(0, 0, 0, 0.05)' }
                            },
                            x: { grid: { display: false } }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching marketing survey data:', error);
            }
        }

        // Fetch and render monthly trend
        async function fetchMonthlyTrend() {
            try {
                const response = await fetch('/api/marketing-manager/monthly-trend', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('monthlyTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.month),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.count),
                            borderColor: 'rgba(245, 87, 108, 1)',
                            backgroundColor: 'rgba(245, 87, 108, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(245, 87, 108, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 5,
                            pointHoverRadius: 7
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching monthly trend:', error);
            }
        }

        // Fetch and render location chart
        async function fetchLocationData() {
            try {
                const response = await fetch('/api/marketing-manager/location-data', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('locationChart').getContext('2d');
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: data.map(item => item.location),
                        datasets: [{
                            data: data.map(item => item.count),
                            backgroundColor: [
                                'rgba(240, 147, 251, 0.8)',
                                'rgba(245, 87, 108, 0.8)',
                                'rgba(100, 181, 246, 0.8)'
                            ],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { position: 'bottom' } }
                    }
                });
            } catch (error) {
                console.error('Error fetching location data:', error);
            }
        }

        // Fetch and render top courses
        async function fetchTopCourses() {
            try {
                const response = await fetch('/api/marketing-manager/top-courses', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('topCoursesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.course_name.substring(0, 30) + '...'),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.registrations),
                            backgroundColor: 'rgba(100, 181, 246, 0.8)',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        indexAxis: 'y',
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: { legend: { display: false } },
                        scales: {
                            x: {
                                beginAtZero: true,
                                ticks: { stepSize: 1 }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching top courses:', error);
            }
        }

        // Fetch and render ROI chart
        async function fetchROIData() {
            try {
                const response = await fetch('/api/marketing-manager/roi-data', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('roiChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: data.map(item => item.source),
                        datasets: [{
                            label: 'Conversion Rate %',
                            data: data.map(item => item.conversion_rate),
                            backgroundColor: 'rgba(129, 199, 132, 0.8)',
                            borderRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const item = data[context.dataIndex];
                                        return [
                                            'Students: ' + item.students,
                                            'Registrations: ' + item.registrations,
                                            'Conversion: ' + item.conversion_rate + '%'
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                ticks: {
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            }
                        }
                    }
                });
            } catch (error) {
                console.error('Error fetching ROI data:', error);
            }
        }

        // Fetch and render recent registrations
        async function fetchRecentRegistrations() {
            try {
                const response = await fetch('/api/marketing-manager/recent-registrations', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const container = document.getElementById('recentRegistrationsContainer');
                
                if (data.length === 0) {
                    container.innerHTML = '<div class="text-center py-10 text-gray-500 italic">No registrations found</div>';
                    return;
                }
                
                let html = `
                    <div class="overflow-x-auto">
                        <table class="registrations-table">
                            <thead>
                                <tr>
                                    <th>Student Name</th>
                                    <th>Course</th>
                                    <th>Date</th>
                                    <th>Location</th>
                                    <th>Marketing Source</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.forEach(reg => {
                    const statusClass = reg.status === 'Registered' ? 'status-registered' : 'status-pending';
                    
                    html += `
                        <tr>
                            <td><strong>${reg.student_name}</strong></td>
                            <td>${reg.course_name}</td>
                            <td>${reg.registration_date}</td>
                            <td>${reg.location}</td>
                            <td><span class="source-tag">${reg.marketing_source}</span></td>
                            <td><span class="status-badge ${statusClass}">${reg.status}</span></td>
                        </tr>
                    `;
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } catch (error) {
                console.error('Error fetching recent registrations:', error);
                document.getElementById('recentRegistrationsContainer').innerHTML = 
                    '<div class="text-center py-10 text-red-500">Error loading registrations</div>';
            }
        }

        // Initialize dashboard
        document.addEventListener('DOMContentLoaded', function() {
            fetchOverviewMetrics();
            fetchMarketingSurveyData();
            fetchMonthlyTrend();
            fetchLocationData();
            fetchTopCourses();
            fetchROIData();
            fetchRecentRegistrations();
            
            // Refresh data every 3 minutes
            setInterval(() => {
                fetchOverviewMetrics();
                fetchRecentRegistrations();
            }, 180000);
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('inc.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\SLT\Welisara\Nebula\resources\views/marketing_manager_dashboard.blade.php ENDPATH**/ ?>