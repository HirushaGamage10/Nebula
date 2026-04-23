<?php

namespace App\Http\Controllers;

use App\Services\AuthenticationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    private AuthenticationService $authenticationService;

    public function __construct(AuthenticationService $authenticationService)
    {
        $this->authenticationService = $authenticationService;
    }

    // Method to show the login view
    public function showLogin()
    {
        return view('login');
    }

    // Method to handle the login form submission
    public function authenticate(Request $request)
    {
        try {
            $credentials = $request->only('email', 'password');
            $validation = $this->authenticationService->validateCredentials($credentials);

            if (!$validation['valid']) {
                return back()
                    ->withErrors($validation['errors'])
                    ->withInput()
                    ->with('popup', true);
            }

            $result = $this->authenticationService->attemptLogin($credentials);

            if (!($result['success'] ?? false)) {
                return back()
                    ->withErrors(['email' => $result['message'] ?? 'Invalid username or password.'])
                    ->withInput()
                    ->with('popup', true);
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));

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
