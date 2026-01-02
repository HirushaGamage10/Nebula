@extends('inc.app')

@section('title', 'NEBULA | System Diagnostics')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center mt-4">
        <div class="col-md-10">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0"><i class="ti ti-stethoscope me-2"></i>System Diagnostics</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <strong>Purpose:</strong> This page helps diagnose why features work in development but not for users.
                        Share this information when reporting issues.
                    </div>

                    <!-- Environment Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-server me-2"></i>Environment Information</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">Environment:</th>
                                    <td><span class="badge bg-{{ config('app.env') === 'production' ? 'success' : 'warning' }}">{{ strtoupper(config('app.env')) }}</span></td>
                                </tr>
                                <tr>
                                    <th>Debug Mode:</th>
                                    <td><span class="badge bg-{{ config('app.debug') ? 'danger' : 'success' }}">{{ config('app.debug') ? 'ENABLED' : 'DISABLED' }}</span></td>
                                </tr>
                                <tr>
                                    <th>PHP Version:</th>
                                    <td>{{ PHP_VERSION }}</td>
                                </tr>
                                <tr>
                                    <th>Laravel Version:</th>
                                    <td>{{ app()->version() }}</td>
                                </tr>
                                <tr>
                                    <th>Session Driver:</th>
                                    <td>{{ config('session.driver') }}</td>
                                </tr>
                                <tr>
                                    <th>Session Lifetime:</th>
                                    <td>{{ config('session.lifetime') }} minutes</td>
                                </tr>
                                <tr>
                                    <th>Cache Driver:</th>
                                    <td>{{ config('cache.default') }}</td>
                                </tr>
                                <tr>
                                    <th>Database Connection:</th>
                                    <td>{{ config('database.default') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- User Information -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-user me-2"></i>Current User</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">User ID:</th>
                                    <td>{{ Auth::id() ?? 'Not authenticated' }}</td>
                                </tr>
                                <tr>
                                    <th>Username:</th>
                                    <td>{{ Auth::user()->username ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ Auth::user()->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Role:</th>
                                    <td><span class="badge bg-primary">{{ Auth::user()->role ?? 'N/A' }}</span></td>
                                </tr>
                                <tr>
                                    <th>Session ID:</th>
                                    <td><code>{{ session()->getId() }}</code></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Browser Tests -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-browser me-2"></i>Browser & JavaScript Tests</h5>
                        </div>
                        <div class="card-body">
                            <table class="table table-sm">
                                <tr>
                                    <th width="30%">User Agent:</th>
                                    <td id="userAgent">Loading...</td>
                                </tr>
                                <tr>
                                    <th>Screen Resolution:</th>
                                    <td id="screenResolution">Loading...</td>
                                </tr>
                                <tr>
                                    <th>JavaScript Enabled:</th>
                                    <td><span class="badge bg-success">YES (you're seeing this!)</span></td>
                                </tr>
                                <tr>
                                    <th>jQuery Loaded:</th>
                                    <td id="jqueryStatus">Checking...</td>
                                </tr>
                                <tr>
                                    <th>Local Storage:</th>
                                    <td id="localStorageStatus">Checking...</td>
                                </tr>
                                <tr>
                                    <th>Cookies Enabled:</th>
                                    <td id="cookiesStatus">Checking...</td>
                                </tr>
                                <tr>
                                    <th>CSRF Token:</th>
                                    <td id="csrfToken">Loading...</td>
                                </tr>
                            </table>

                            <div class="mt-3">
                                <button class="btn btn-primary btn-sm" onclick="testAjax()">
                                    <i class="ti ti-refresh me-1"></i>Test AJAX Connection
                                </button>
                                <button class="btn btn-secondary btn-sm" onclick="testCsrfRefresh()">
                                    <i class="ti ti-shield me-1"></i>Test CSRF Refresh
                                </button>
                                <button class="btn btn-info btn-sm" onclick="checkConsole()">
                                    <i class="ti ti-terminal me-1"></i>Check Console Errors
                                </button>
                            </div>
                            <div id="testResults" class="mt-3"></div>
                        </div>
                    </div>

                    <!-- Cache Status -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-database me-2"></i>Cache Status</h5>
                        </div>
                        <div class="card-body">
                            <p>Check if caches need clearing:</p>
                            <div class="d-flex flex-wrap gap-2">
                                <form method="POST" action="{{ route('diagnostics.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" name="cache_type" value="all" class="btn btn-warning btn-sm">
                                        <i class="ti ti-trash me-1"></i>Clear All Caches
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('diagnostics.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" name="cache_type" value="view" class="btn btn-outline-warning btn-sm">
                                        View Cache
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('diagnostics.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" name="cache_type" value="config" class="btn btn-outline-warning btn-sm">
                                        Config Cache
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('diagnostics.clear-cache') }}" class="d-inline">
                                    @csrf
                                    <button type="submit" name="cache_type" value="route" class="btn btn-outline-warning btn-sm">
                                        Route Cache
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Recent Errors -->
                    <div class="card mb-4">
                        <div class="card-header bg-light">
                            <h5 class="mb-0"><i class="ti ti-alert-triangle me-2"></i>Recent Log Entries</h5>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-warning">
                                <strong>Note:</strong> Check <code>storage/logs/laravel.log</code> for detailed errors.
                            </div>
                            @php
                                $logFile = storage_path('logs/laravel.log');
                                $recentLogs = [];
                                if (file_exists($logFile)) {
                                    $logs = file($logFile);
                                    $recentLogs = array_slice(array_reverse($logs), 0, 10);
                                }
                            @endphp
                            @if(count($recentLogs) > 0)
                                <div class="log-viewer" style="max-height: 300px; overflow-y: auto; background: #f8f9fa; padding: 10px; border-radius: 5px; font-family: monospace; font-size: 12px;">
                                    @foreach($recentLogs as $log)
                                        <div>{{ $log }}</div>
                                    @endforeach
                                </div>
                            @else
                                <p class="text-muted">No recent log entries found.</p>
                            @endif
                        </div>
                    </div>

                    <!-- Recommendations -->
                    <div class="card">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0"><i class="ti ti-checklist me-2"></i>Recommendations</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group">
                                @if(config('app.debug') && config('app.env') === 'production')
                                    <li class="list-group-item list-group-item-danger">
                                        <strong>⚠️ Critical:</strong> APP_DEBUG is enabled in production! Set APP_DEBUG=false in .env
                                    </li>
                                @endif
                                @if(config('session.lifetime') < 240)
                                    <li class="list-group-item list-group-item-warning">
                                        <strong>⚠️ Warning:</strong> Session lifetime is short ({{ config('session.lifetime') }} min). Consider increasing to 480 minutes.
                                    </li>
                                @endif
                                @if(!extension_loaded('opcache'))
                                    <li class="list-group-item list-group-item-info">
                                        <strong>ℹ️ Info:</strong> OPcache is not enabled. Enable it for better performance.
                                    </li>
                                @endif
                                <li class="list-group-item list-group-item-success">
                                    <strong>✓ Tip:</strong> Have users try clearing browser cache (Ctrl+Shift+Delete) if they experience issues.
                                </li>
                                <li class="list-group-item list-group-item-success">
                                    <strong>✓ Tip:</strong> Ask users to check browser console (F12) for JavaScript errors when reporting bugs.
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script nonce="{{ $cspNonce }}">
$(document).ready(function() {
    // Fill browser information
    $('#userAgent').text(navigator.userAgent);
    $('#screenResolution').text(screen.width + 'x' + screen.height);
    $('#jqueryStatus').html('<span class="badge bg-success">YES (v' + $.fn.jquery + ')</span>');
    
    // Check localStorage
    try {
        localStorage.setItem('test', 'test');
        localStorage.removeItem('test');
        $('#localStorageStatus').html('<span class="badge bg-success">ENABLED</span>');
    } catch(e) {
        $('#localStorageStatus').html('<span class="badge bg-danger">DISABLED</span>');
    }
    
    // Check cookies
    $('#cookiesStatus').html(navigator.cookieEnabled ? 
        '<span class="badge bg-success">ENABLED</span>' : 
        '<span class="badge bg-danger">DISABLED</span>');
    
    // Show CSRF token
    const token = $('meta[name="csrf-token"]').attr('content');
    $('#csrfToken').html('<code>' + (token || 'NOT FOUND!') + '</code>');
});

function testAjax() {
    $('#testResults').html('<div class="alert alert-info">Testing AJAX connection...</div>');
    $.ajax({
        url: '/refresh-csrf',
        method: 'GET',
        success: function(data) {
            $('#testResults').html('<div class="alert alert-success"><strong>✓ Success!</strong> AJAX is working. Response: ' + JSON.stringify(data) + '</div>');
        },
        error: function(xhr) {
            $('#testResults').html('<div class="alert alert-danger"><strong>✗ Failed!</strong> Status: ' + xhr.status + ' - ' + xhr.statusText + '</div>');
        }
    });
}

function testCsrfRefresh() {
    $('#testResults').html('<div class="alert alert-info">Testing CSRF token refresh...</div>');
    const oldToken = $('meta[name="csrf-token"]').attr('content');
    
    $.get('/refresh-csrf', function(data) {
        const newToken = data.token;
        if (newToken && newToken !== oldToken) {
            $('#testResults').html('<div class="alert alert-success"><strong>✓ Success!</strong> CSRF token refreshed successfully!</div>');
            $('#csrfToken').html('<code>' + newToken + '</code>');
        } else {
            $('#testResults').html('<div class="alert alert-warning"><strong>⚠ Warning:</strong> Token returned but unchanged.</div>');
        }
    }).fail(function(xhr) {
        $('#testResults').html('<div class="alert alert-danger"><strong>✗ Failed!</strong> Could not refresh token. Status: ' + xhr.status + '</div>');
    });
}

function checkConsole() {
    $('#testResults').html('<div class="alert alert-info"><strong>Check your browser console (F12)</strong> for any JavaScript errors or warnings. Copy any errors you see and include them when reporting issues.</div>');
    console.log('%c=== DIAGNOSTIC CHECK ===', 'color: blue; font-size: 16px; font-weight: bold');
    console.log('If you see any RED errors above this message, copy them when reporting issues!');
    debugUtils.info();
}
</script>
@endsection
