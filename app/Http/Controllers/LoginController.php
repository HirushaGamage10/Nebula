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
                return $this->failedLoginResponse($credentials, $validation['errors'], 422);
            }

            $result = $this->authenticationService->attemptLogin($credentials);

            if (!($result['success'] ?? false)) {
                return $this->failedLoginResponse(
                    $credentials,
                    ['email' => $result['message'] ?? 'Invalid username or password.'],
                    422
                );
            }

            $request->session()->regenerate();

            return redirect()->intended(route('dashboard'));

        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent()
            ]);

            return $this->failedLoginResponse(
                $credentials ?? ['email' => $request->email],
                ['email' => 'An error occurred during login. Please try again.'],
                500
            );
        }
    }

    private function failedLoginResponse(array $credentials, array $errors, int $status)
    {
        return response()->view('login', [
            'loginErrors' => $errors,
            'submittedEmail' => $credentials['email'] ?? null,
            'popup' => true,
        ], $status);
    }
}
