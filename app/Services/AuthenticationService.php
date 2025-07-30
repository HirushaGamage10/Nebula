<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthenticationService
{
    /**
     * Attempt to authenticate a user
     *
     * @param array $credentials
     * @return array
     */
    public function attemptLogin(array $credentials): array
    {
        try {
            // Check if user exists
            $user = User::where('email', $credentials['email'])->first();
            
            if (!$user) {
                return [
                    'success' => false,
                    'message' => 'Invalid username or password.',
                    'error_type' => 'invalid_credentials'
                ];
            }

            // Check if user is active
            if ($user->status != "1") {
                return [
                    'success' => false,
                    'message' => 'Your account is not active. Please contact administrator.',
                    'error_type' => 'account_inactive'
                ];
            }

            // Check if user has a valid role
            if (empty($user->user_role)) {
                return [
                    'success' => false,
                    'message' => 'Your account does not have a valid role assigned. Please contact administrator.',
                    'error_type' => 'no_role'
                ];
            }

            // Attempt authentication
            if (Auth::attempt($credentials)) {
                // Log successful login
                Log::info('User logged in successfully', [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'role' => $user->user_role,
                    'location' => $user->user_location,
                    'ip' => request()->ip(),
                    'user_agent' => request()->userAgent()
                ]);

                return [
                    'success' => true,
                    'user' => $user,
                    'message' => 'Login successful'
                ];
            }

            // Authentication failed
            Log::warning('Failed login attempt', [
                'email' => $credentials['email'],
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return [
                'success' => false,
                'message' => 'Invalid username or password.',
                'error_type' => 'invalid_credentials'
            ];

        } catch (\Exception $e) {
            Log::error('Authentication error: ' . $e->getMessage(), [
                'email' => $credentials['email'] ?? 'unknown',
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);

            return [
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'error_type' => 'server_error'
            ];
        }
    }

    /**
     * Validate login credentials
     *
     * @param array $credentials
     * @return array
     */
    public function validateCredentials(array $credentials): array
    {
        $errors = [];

        // Validate email
        if (empty($credentials['email'])) {
            $errors['email'] = 'Username is required.';
        } elseif (!filter_var($credentials['email'], FILTER_VALIDATE_EMAIL)) {
            $errors['email'] = 'Please enter a valid email address.';
        }

        // Validate password
        if (empty($credentials['password'])) {
            $errors['password'] = 'Password is required.';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Check if user account is locked
     *
     * @param string $email
     * @return bool
     */
    public function isAccountLocked(string $email): bool
    {
        // This can be extended to implement account locking logic
        // For now, we'll return false
        return false;
    }

    /**
     * Get login attempt count for an email
     *
     * @param string $email
     * @return int
     */
    public function getLoginAttempts(string $email): int
    {
        // This can be extended to track login attempts
        // For now, we'll return 0
        return 0;
    }

    /**
     * Log failed login attempt
     *
     * @param string $email
     * @return void
     */
    public function logFailedAttempt(string $email): void
    {
        Log::warning('Failed login attempt', [
            'email' => $email,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }

    /**
     * Log successful login
     *
     * @param User $user
     * @return void
     */
    public function logSuccessfulLogin(User $user): void
    {
        Log::info('User logged in successfully', [
            'user_id' => $user->user_id,
            'email' => $user->email,
            'role' => $user->user_role,
            'location' => $user->user_location,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()
        ]);
    }
} 