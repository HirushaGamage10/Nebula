<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class LoginThrottle
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $email = $request->input('email');
        $ip = $request->ip();
        
        // Check if this IP is blocked
        if ($this->isIpBlocked($ip)) {
            Log::warning('Blocked login attempt from IP', [
                'ip' => $ip,
                'email' => $email,
                'user_agent' => $request->userAgent()
            ]);
            
            return back()
                ->withErrors(['email' => 'Too many login attempts. Please try again later.'])
                ->withInput();
        }
        
        // Check if this email is blocked
        if ($email && $this->isEmailBlocked($email)) {
            Log::warning('Blocked login attempt for email', [
                'email' => $email,
                'ip' => $ip,
                'user_agent' => $request->userAgent()
            ]);
            
            return back()
                ->withErrors(['email' => 'Too many login attempts for this account. Please try again later.'])
                ->withInput();
        }
        
        $response = $next($request);
        
        // If login failed, increment counters
        if ($request->session()->has('errors')) {
            $this->incrementFailedAttempts($ip, $email);
        }
        
        return $response;
    }
    
    /**
     * Check if IP is blocked
     */
    private function isIpBlocked(string $ip): bool
    {
        $key = "login_attempts_ip_{$ip}";
        $attempts = Cache::get($key, 0);
        
        return $attempts >= 10; // Block after 10 attempts
    }
    
    /**
     * Check if email is blocked
     */
    private function isEmailBlocked(string $email): bool
    {
        $key = "login_attempts_email_{$email}";
        $attempts = Cache::get($key, 0);
        
        return $attempts >= 5; // Block after 5 attempts
    }
    
    /**
     * Increment failed login attempts
     */
    private function incrementFailedAttempts(string $ip, ?string $email): void
    {
        // Increment IP attempts
        $ipKey = "login_attempts_ip_{$ip}";
        $ipAttempts = Cache::get($ipKey, 0) + 1;
        Cache::put($ipKey, $ipAttempts, 300); // 5 minutes
        
        // Increment email attempts
        if ($email) {
            $emailKey = "login_attempts_email_{$email}";
            $emailAttempts = Cache::get($emailKey, 0) + 1;
            Cache::put($emailKey, $emailAttempts, 900); // 15 minutes
        }
        
        Log::warning('Failed login attempt', [
            'ip' => $ip,
            'email' => $email,
            'ip_attempts' => $ipAttempts,
            'email_attempts' => $emailAttempts ?? 0
        ]);
    }
} 