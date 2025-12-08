@extends('inc.app')

@section('title', 'NEBULA | Student Counselor Dashboard')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div id="pageContent" class="bg-gray-50">
        <!-- Header -->
        <div class="bg-white p-4 rounded-lg shadow-sm mb-4">
            <h1 class="text-3xl font-bold text-gray-800">👋 Student Counselor Dashboard</h1>
            <p class="text-gray-600 mt-1">Monitor student intake and marketing effectiveness</p>
        </div>

        <!-- KPI Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Total Registered</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2" id="totalRegistered">-</p>
                        <p class="text-sm text-gray-500 mt-1">Students</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Today's Registrations</p>
                        <p class="text-3xl font-bold text-blue-600 mt-2" id="todayRegistrations">-</p>
                        <p class="text-sm text-gray-500 mt-1">New today</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">This Week</p>
                        <p class="text-3xl font-bold text-green-600 mt-2" id="weekRegistrations">-</p>
                        <p class="text-sm text-gray-500 mt-1">Registrations</p>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow-sm border-l-4 border-yellow-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 uppercase tracking-wide">Pending</p>
                        <p class="text-3xl font-bold text-yellow-600 mt-2" id="pendingRegistrations">-</p>
                        <p class="text-sm text-gray-500 mt-1">Awaiting approval</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Marketing Survey Analysis -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-purple-500">
                    📊 Marketing Survey Analysis
                </h2>
                <div style="height: 350px;">
                    <canvas id="marketingSurveyChart"></canvas>
                </div>
            </div>

            <!-- Registration Trend -->
            <div class="bg-white p-6 rounded-lg shadow-sm">
                <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-purple-500">
                    📈 Daily Registration Trend
                </h2>
                <div style="height: 350px;">
                    <canvas id="dailyTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Recent Registrations -->
        <div class="bg-white p-6 rounded-lg shadow-sm">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 pb-3 border-b-2 border-purple-500">
                📋 Recent Registrations
            </h2>
            <div id="recentRegistrationsContainer">
                <div class="text-center py-10 text-gray-500">Loading registrations...</div>
            </div>
        </div>
    </div>

    <style>
        .registrations-table {
            width: 100%;
            border-collapse: collapse;
        }

        .registrations-table thead {
            background: #f8f9fa;
        }

        .registrations-table th {
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #333;
            font-size: 13px;
            border-bottom: 2px solid #e0e0e0;
        }

        .registrations-table td {
            padding: 12px;
            border-bottom: 1px solid #f0f0f0;
            font-size: 13px;
            color: #666;
        }

        .registrations-table tbody tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            padding: 4px 12px;
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

        .status-special {
            background: #d1ecf1;
            color: #0c5460;
        }
    </style>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Fetch overview metrics
        async function fetchOverviewMetrics() {
            try {
                const response = await fetch('/api/student-counselor/overview', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                document.getElementById('totalRegistered').textContent = data.total_registered;
                document.getElementById('todayRegistrations').textContent = data.today_registrations;
                document.getElementById('weekRegistrations').textContent = data.week_registrations;
                document.getElementById('pendingRegistrations').textContent = data.pending_registrations;
            } catch (error) {
                console.error('Error fetching overview metrics:', error);
            }
        }

        // Fetch and render marketing survey chart
        async function fetchMarketingSurveyData() {
            try {
                const response = await fetch('/api/student-counselor/marketing-survey', {
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
                            backgroundColor: 'rgba(102, 126, 234, 0.8)',
                            borderColor: 'rgba(102, 126, 234, 1)',
                            borderWidth: 2,
                            borderRadius: 5
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                borderRadius: 8
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

        // Fetch and render daily trend chart
        async function fetchDailyTrend() {
            try {
                const response = await fetch('/api/student-counselor/daily-trend', {
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                const ctx = document.getElementById('dailyTrendChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map(item => item.date),
                        datasets: [{
                            label: 'Registrations',
                            data: data.map(item => item.count),
                            borderColor: 'rgba(118, 75, 162, 1)',
                            backgroundColor: 'rgba(118, 75, 162, 0.1)',
                            borderWidth: 3,
                            fill: true,
                            tension: 0.4,
                            pointBackgroundColor: 'rgba(118, 75, 162, 1)',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: 'rgba(0, 0, 0, 0.8)',
                                padding: 12,
                                borderRadius: 8
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
                console.error('Error fetching daily trend:', error);
            }
        }

        // Fetch and render recent registrations
        async function fetchRecentRegistrations() {
            try {
                const response = await fetch('/api/student-counselor/recent-registrations', {
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
                                    <th>Counselor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                `;
                
                data.forEach(reg => {
                    const statusClass = reg.status === 'Registered' ? 'status-registered' : 
                                      reg.status === 'Pending' ? 'status-pending' : 'status-special';
                    
                    html += `
                        <tr>
                            <td><strong>${reg.student_name}</strong></td>
                            <td>${reg.course_name}</td>
                            <td>${reg.registration_date}</td>
                            <td>${reg.location}</td>
                            <td>${reg.counselor_name}</td>
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
            fetchDailyTrend();
            fetchRecentRegistrations();
            
            // Refresh data every 2 minutes
            setInterval(() => {
                fetchOverviewMetrics();
                fetchRecentRegistrations();
            }, 120000);
        });
    </script>
@endsection