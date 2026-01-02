<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class DiagnosticsController extends Controller
{
    /**
     * Show the diagnostics page
     */
    public function index()
    {
        // Only allow admins or developers to access
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'Developer'])) {
            abort(403, 'Unauthorized access to diagnostics');
        }

        return view('diagnostics');
    }

    /**
     * Clear various caches
     */
    public function clearCache(Request $request)
    {
        if (!Auth::check() || !in_array(Auth::user()->role, ['Admin', 'Developer'])) {
            abort(403, 'Unauthorized');
        }

        $cacheType = $request->input('cache_type', 'all');

        try {
            switch ($cacheType) {
                case 'view':
                    Artisan::call('view:clear');
                    $message = 'View cache cleared successfully!';
                    break;

                case 'config':
                    Artisan::call('config:clear');
                    $message = 'Config cache cleared successfully!';
                    break;

                case 'route':
                    Artisan::call('route:clear');
                    $message = 'Route cache cleared successfully!';
                    break;

                case 'all':
                default:
                    Artisan::call('cache:clear');
                    Artisan::call('config:clear');
                    Artisan::call('route:clear');
                    Artisan::call('view:clear');
                    $message = 'All caches cleared successfully!';
                    break;
            }

            return redirect()
                ->route('diagnostics.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->route('diagnostics.index')
                ->with('error', 'Failed to clear cache: ' . $e->getMessage());
        }
    }
}
