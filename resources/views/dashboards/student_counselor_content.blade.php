    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>

    <style>
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
        }
        
        .kpi-card {
            border-left: 4px solid;
            transition: all 0.3s ease;
        }
        
        .kpi-card:hover {
            border-left-width: 6px;
        }
        
        .avatar-initial {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 16px;
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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-color: transparent;
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="bg-white p-4 rounded shadow-sm mb-4">
    <div class="page-title-box d-flex align-items-center justify-content-between">
        
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="avatar-initial">
                    <i class="fas fa-user-graduate"></i>
                </div>
            </div>
            <div>
                <h4 class="mb-1 fw-bold text-dark"> Student Counselor Dashboard</h4>
                <p class="text-muted mb-0">Monitor student intake and marketing effectiveness</p>
            </div>
        </div>

        <div class="d-flex align-items-center gap-2">
            <div class="input-group input-group-sm" style="width: 200px;">
                <input type="text" class="form-control" placeholder="Search students..." id="searchStudents">
                <button class="btn btn-outline-secondary" type="button">
                    <i class="fas fa-search"></i>
                </button>
            </div>

            <button class="btn btn-outline-primary btn-sm" onclick="refreshAllData()">
                <i class="fas fa-sync-alt me-1"></i> Refresh
            </button>
        </div>

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
                                <button class="time-filter-btn" onclick="setTimePeriod('quarter')">Last 3 Months</button>
                                <div class="d-inline-block ms-2">
                                    <input type="date" id="customDate" class="form-control form-control-sm" style="width: 140px;">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>