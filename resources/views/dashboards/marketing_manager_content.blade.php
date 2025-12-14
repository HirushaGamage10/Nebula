    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        .gradient-border {
            border-image: linear-gradient(90deg, #667eea 0%, #764ba2 100%) 1;
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .status-badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
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
            padding: 4px 10px;
            background: #e3f2fd;
            color: #1976d2;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 500;
        }
        
        .chart-container {
            position: relative;
            height: 300px;
        }
        
        .kpi-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover {
            border-left-width: 6px;
        }
        
        .time-filter-btn {
            padding: 6px 16px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.2s ease;
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .time-filter-btn:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
        }
        
        .time-filter-btn.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .chart-toggle-btn {
            padding: 8px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
            background: white;
            color: #6c757d;
            transition: all 0.2s ease;
        }
        
        .chart-toggle-btn:hover {
            background: #f8f9fa;
        }
        
        .chart-toggle-btn.active {
            background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(102, 126, 234, 0.05);
        }
        
        .avatar-initial {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 14px;
        }
        
        .data-loading {
            opacity: 0.6;
            pointer-events: none;
        }
        
        .pulse-animation {
            animation: pulse 1.5s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div class="bg-white p-4 rounded shadow-sm mb-3">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <div class="avatar-initial">
                <i class="fas fa-bullseye"></i>
            </div>
        </div>
        <div>
            <h4 class="mb-1 fw-bold text-dark">🎯 Marketing Manager Dashboard</h4>
            <p class="text-muted mb-0">Track campaign performance and student acquisition metrics</p>
        </div>
    </div>
</div>

                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-primary btn-sm" onclick="refreshAllData()">
                            <i class="fas fa-sync-alt me-1"></i> Refresh
                        </button>
                        <button class="btn btn-primary btn-sm" onclick="exportDashboard()">
                            <i class="fas fa-download me-1"></i> Export
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Filter -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-body py-3">
                        <div class="d-flex flex-wrap align-items-center">
                            <span class="me-3 text-muted"><i class="fas fa-calendar-alt me-1"></i> Time Period:</span>
                            <div class="d-flex flex-wrap">
                                <button class="time-filter-btn active" onclick="setTimePeriod('today')">Today</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('week')">This Week</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('month')">This Month</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('quarter')">This Quarter</button>
                                <button class="time-filter-btn" onclick="setTimePeriod('year')">This Year</button>
                                <div class="d-inline-block ms-2">
                                    <input type="date" id="customDate" class="form-control form-control-sm" style="width: 140px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- KPI Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card kpi-card card-hover border-left-primary">
                    <div class="card-body">