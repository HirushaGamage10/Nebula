<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Method to show the login view
    public function showLogin()
    {
        return view('login');
    }

    // Method to handle the login form submission
    public function authenticate(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ], [
                'email.required' => 'Username is required.',
                'email.email' => 'Please enter a valid email address.',
                'password.required' => 'Password is required.',
            ]);

            // Print the request data
            Log::info('Authentication Request:', $request->all());

            $credentials = $request->only('email', 'password');

            // Attempt to authenticate the user
            if (Auth::attempt($credentials)) {
                // Get the authenticated user
                $user = Auth::user();
                
                // Check if user is active
                if ($user->status != "1") {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Your account is not active. Please contact administrator.'])
                        ->withInput()
                        ->with('popup', true);
                }
                
                // Check if user has a valid role
                if (empty($user->user_role)) {
                    Auth::logout();
                    return back()
                        ->withErrors(['email' => 'Your account does not have a valid role assigned. Please contact administrator.'])
                        ->withInput()
                        ->with('popup', true);
                }
                
                // Authentication passed
                $request->session()->regenerate();
                
                // Log successful login with role information
                Log::info('User logged in successfully', [
                    'user_id' => $user->user_id,
                    'email' => $user->email,
                    'role' => $user->user_role,
                    'location' => $user->user_location
                ]);
                
                // Redirect to intended page or dashboard
                return redirect()->intended(route('dashboard'));
            } else {
                // Authentication failed
                return back()
                    ->withErrors(['email' => 'Invalid username or password.'])
                    ->withInput()
                    ->with('popup', true);
            }

        } catch (ValidationException $e) {
            // Handle validation errors
            return back()
                ->withErrors($e->errors())
                ->withInput()
                ->with('popup', true);
        } catch (\Exception $e) {
            // Handle any other exceptions
            Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return back()
                ->withErrors(['email' => 'An error occurred during login. Please try again.'])
                ->withInput()
                ->with('popup', true);
        }
    }
}
