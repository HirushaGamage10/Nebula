/**
 * Global JavaScript Utilities and Error Handling
 * Include this file in your main layout (app.blade.php)
 */

(function() {
    'use strict';

    // ==================== CSRF Token Management ====================
    
    /**
     * Setup AJAX to always include CSRF token
     */
    function setupCsrfToken() {
        const token = document.querySelector('meta[name="csrf-token"]');
        if (token && typeof $ !== 'undefined') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': token.getAttribute('content')
                }
            });
        }
    }

    /**
     * Refresh CSRF token every hour to prevent expiration
     */
    function startCsrfRefresh() {
        if (typeof $ === 'undefined') return;

        setInterval(function() {
            $.ajax({
                url: '/refresh-csrf',
                method: 'GET',
                success: function(data) {
                    if (data.token) {
                        $('meta[name="csrf-token"]').attr('content', data.token);
                        // Update ajaxSetup with new token
                        $.ajaxSetup({
                            headers: {
                                'X-CSRF-TOKEN': data.token
                            }
                        });
                        console.log('CSRF token refreshed successfully');
                    }
                },
                error: function(xhr) {
                    console.error('Failed to refresh CSRF token:', xhr.status);
                }
            });
        }, 3600000); // Refresh every 1 hour (3600000 ms)
    }

    // ==================== Global Error Handlers ====================

    /**
     * Handle JavaScript errors globally
     */
    window.onerror = function(message, source, lineno, colno, error) {
        const errorData = {
            message: message,
            file: source,
            line: lineno,
            column: colno,
            stack: error ? error.stack : '',
            url: window.location.href,
            timestamp: new Date().toISOString()
        };

        console.error('JavaScript Error:', errorData);

        // Send to server for logging (if jQuery available and CSRF token exists)
        if (typeof $ !== 'undefined' && $('meta[name="csrf-token"]').length) {
            $.ajax({
                url: '/log-js-error',
                method: 'POST',
                data: errorData,
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            }).fail(function() {
                console.error('Failed to log JavaScript error to server');
            });
        }

        // Don't suppress default error handling
        return false;
    };

    /**
     * Handle unhandled promise rejections
     */
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled Promise Rejection:', event.reason);
        
        if (typeof $ !== 'undefined' && $('meta[name="csrf-token"]').length) {
            $.ajax({
                url: '/log-js-error',
                method: 'POST',
                data: {
                    message: 'Unhandled Promise Rejection: ' + event.reason,
                    file: 'Promise',
                    line: 0,
                    url: window.location.href
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        }
    });

    // ==================== AJAX Error Handling ====================

    /**
     * Setup global AJAX error handlers
     */
    function setupAjaxErrorHandlers() {
        if (typeof $ === 'undefined') return;

        // Global AJAX error handler
        $(document).ajaxError(function(event, xhr, settings, thrownError) {
            console.error('AJAX Error:', {
                url: settings.url,
                status: xhr.status,
                statusText: xhr.statusText,
                response: xhr.responseText
            });

            // Handle specific error codes
            switch(xhr.status) {
                case 419: // CSRF token mismatch / session expired
                    alert('Your session has expired. The page will reload.');
                    location.reload();
                    break;
                    
                case 401: // Unauthorized
                    alert('You are not authenticated. Redirecting to login...');
                    window.location.href = '/login';
                    break;
                    
                case 403: // Forbidden
                    alert('You do not have permission to perform this action.');
                    break;
                    
                case 404: // Not found
                    console.error('Resource not found:', settings.url);
                    break;
                    
                case 500: // Server error
                    alert('A server error occurred. Please try again later.');
                    break;
                    
                case 503: // Service unavailable
                    alert('The service is temporarily unavailable. Please try again later.');
                    break;
            }
        });

        // Show loading indicator for AJAX requests
        $(document).ajaxStart(function() {
            // Add your loading indicator logic here
            console.log('AJAX request started...');
        });

        $(document).ajaxStop(function() {
            // Remove loading indicator
            console.log('AJAX request completed.');
        });
    }

    // ==================== Browser Compatibility Checks ====================

    /**
     * Check for required browser features
     */
    function checkBrowserCompatibility() {
        const warnings = [];

        // Check for localStorage
        if (typeof(Storage) === "undefined") {
            warnings.push("Local Storage is not supported in your browser.");
        }

        // Check for fetch API
        if (!window.fetch) {
            warnings.push("Fetch API is not supported. Some features may not work.");
        }

        // Check for Promise
        if (!window.Promise) {
            warnings.push("Promises are not supported. Please update your browser.");
        }

        // Display warnings if any
        if (warnings.length > 0) {
            console.warn('Browser Compatibility Issues:', warnings);
        }

        return warnings.length === 0;
    }

    // ==================== Session Activity Tracker ====================

    /**
     * Track user activity to prevent session timeout
     */
    function trackUserActivity() {
        let activityTimeout;
        const INACTIVITY_TIME = 15 * 60 * 1000; // 15 minutes

        function resetTimer() {
            clearTimeout(activityTimeout);
            activityTimeout = setTimeout(function() {
                console.warn('User has been inactive for 15 minutes');
                // You can show a warning modal here
            }, INACTIVITY_TIME);
        }

        // Track various user activities
        ['mousedown', 'mousemove', 'keypress', 'scroll', 'touchstart'].forEach(function(event) {
            document.addEventListener(event, resetTimer, true);
        });

        resetTimer();
    }

    // ==================== Console Utilities ====================

    /**
     * Add helpful debugging utilities to console
     */
    window.debugUtils = {
        // Check if user is authenticated
        isAuthenticated: function() {
            return document.querySelector('meta[name="user-id"]') !== null;
        },

        // Get current CSRF token
        getCsrfToken: function() {
            const meta = document.querySelector('meta[name="csrf-token"]');
            return meta ? meta.getAttribute('content') : null;
        },

        // Test AJAX connectivity
        testAjax: function(url) {
            if (typeof $ === 'undefined') {
                console.error('jQuery is not loaded');
                return;
            }
            
            $.ajax({
                url: url || '/refresh-csrf',
                method: 'GET',
                success: function(data) {
                    console.log('✓ AJAX test successful:', data);
                },
                error: function(xhr) {
                    console.error('✗ AJAX test failed:', xhr.status, xhr.statusText);
                }
            });
        },

        // Check CSP errors
        checkCsp: function() {
            const cspMeta = document.querySelector('meta[http-equiv="Content-Security-Policy"]');
            if (cspMeta) {
                console.log('CSP Policy:', cspMeta.getAttribute('content'));
            } else {
                console.log('No CSP meta tag found (policy may be set via headers)');
            }
        },

        // Display environment info
        info: function() {
            console.group('Application Debug Info');
            console.log('URL:', window.location.href);
            console.log('User Agent:', navigator.userAgent);
            console.log('CSRF Token:', this.getCsrfToken());
            console.log('Authenticated:', this.isAuthenticated());
            console.log('jQuery Loaded:', typeof $ !== 'undefined');
            console.log('Local Storage Available:', typeof(Storage) !== "undefined");
            console.groupEnd();
        }
    };

    // ==================== Initialize Everything ====================

    /**
     * Initialize all utilities when DOM is ready
     */
    function init() {
        console.log('Initializing global utilities...');
        
        // Check browser compatibility
        checkBrowserCompatibility();
        
        // Setup CSRF handling
        setupCsrfToken();
        startCsrfRefresh();
        
        // Setup AJAX error handlers
        setupAjaxErrorHandlers();
        
        // Track user activity
        trackUserActivity();

        console.log('✓ Global utilities initialized');
        console.log('Type debugUtils.info() in console for debug information');
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
