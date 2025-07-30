<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash; // Import the Hash facade
use Illuminate\Validation\Rules\Password; // Import the Password rule
use App\Models\User;
use App\Http\Requests\CreateUserRequest;
use Illuminate\Support\Facades\Log;

class UserProfileController extends Controller
{
    public function showUserProfile()
    {
        // Get the authenticated user's ID
        $user_id = Auth::id();

        // Fetch user details by user_id from the User model
        $user = User::findOrFail($user_id);

        // Get the current user's user_location
        $current_user_location = $user->user_location ?? 0;

        // Fetch users based only on user_location
        $users = User::where('user_location', $current_user_location)->get();

        // Prepare the array with user details
        $usersArray = $users->map(function ($user) {
            return [
                'user_id' => $user->user_id,
                'user_name' => $user->name,
                'email' => $user->email,
                'employee_id' => $user->employee_id,
                'user_role' => $user->user_role,
                'status' => ($user->status == "1" ? "Active" : ($user->status == "0" ? "Inactive" : ($user->status == "2" ? "Suspended" : "Unknown"))),
                'user_location' => $user->user_location ?? 'Unknown'
            ];
        });

        // Prepare user data to pass to the view
        $userData = [
            'user_name' => $user->name,
            'email' => $user->email,
            'employee_id' => $user->employee_id,
            'user_role' => $user->user_role,
            'status' => ($user->status == "1" ? "Active" : ($user->status == "0" ? "Inactive" : ($user->status == "2" ? "Suspended" : "Unknown"))),
            'user_location' => $user->user_location ?? 'Unknown'
        ];

        // Fetch all user roles from RoleHelper
        $userRoles = array_keys(\App\Helpers\RoleHelper::getRoles());

        // Add hardcoded locations array for dropdown
        $locations = [
            'Nebula Institute of Technology – Welisara',
            'Nebula Institute of Technology – Moratuwa',
            'Nebula Institute of Technology – Peradeniya'
        ];

        //dd($usersArray);

        // Pass user data and users array to the view
        return view('user_profile', [
            'userData' => $userData,
            'usersArray' => $usersArray->toArray(),
            'userRoles' => $userRoles,
            'locations' => $locations,
        ]);
    }

    public function deleteUser(Request $request)
{
    $user = User::find($request->user_id);

    if (!$user) {
        return response()->json(['success' => false, 'message' => 'User not found.']);
    }

    // Perform actual deletion or soft delete
    $user->delete();

    return response()->json(['success' => true, 'message' => 'User deleted successfully.']);
}
    
public function updateUserStatus(Request $request)
{
    // Validate the request data
    $request->validate([
        'user_id' => 'required|exists:users,user_id',
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255|unique:users,email,' . $request->user_id . ',user_id',
        'user_role' => 'required|string|in:"DGM","Program Administrator (level 01)","Program Administrator (level 02)","Student Counselor","Librarian","Hostel Manager","Bursar","Project Tutor","Marketing Manager","Developer"', // 0 (Inactive), 1 (Active), 2 (Suspended)
        'status' => 'required|in:"0","1","2"', // 0 (Inactive), 1 (Active), 2 (Suspended)
        'user_location' => 'required|string', // Validate location as string
    ]);

    try {
        // Find the user by user_id
        $user = User::findOrFail($request->user_id);

        // Update user information
        $user->name = $request->name;
        $user->email = $request->email;
        $user->employee_id = $request->employee_id;
        $user->user_role = $request->user_role;
        $user->user_location = $request->user_location; // Update user location
        $user->status = $request->status;
        $user->save();

        // Return a success response
        return response()->json(['success' => true, 'message' => 'User information updated successfully']);
    } catch (\Exception $e) {
        // Return an error response if something goes wrong
        return response()->json(['success' => false, 'message' => 'Failed to update user information', 'error' => $e->getMessage()], 500);
    }
}





    // Method to create a new user
    // Remove the createNewUser and showCreateUserForm methods if not used elsewhere
    // Only keep user listing, edit, and delete logic for DGM user management

    public function changePassword(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'current_password' => 'required',
                'new_password' => ['required', 'min:6', 'confirmed'],
            ]);

            // Get the authenticated user's ID
            $userId = Auth::id();

            // Retrieve the user by ID
            $user = User::findOrFail($userId);

            // Check if the current password matches
            if (!Hash::check($request->current_password, $user->password)) {
                return response()->json(['success' => false, 'message' => 'Current password is incorrect'], 400);
            }

            // Update the user's password
            $user->password = Hash::make($request->new_password);
            $user->save();

            // Return a success response
            return response()->json(['success' => true, 'message' => 'Password changed successfully']);
        } catch (\Exception $e) {
            // Return an error response
            return response()->json(['success' => false, 'message' => 'An error occurred while changing the password', 'error' => $e->getMessage()], 500);
        }
    }

    /**
     * Reset a user's password (admin action)
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,user_id',
            'new_password' => ['required', 'string', 'min:6'],
        ]);

        $admin = Auth::user();
        if (!in_array($admin->user_role, ['DGM', 'Program Administrator (level 01)', 'Developer'])) {
            abort(403, 'Unauthorized');
        }

        $user = User::findOrFail($request->user_id);
        $user->password = Hash::make($request->new_password);
        $user->save();

        return redirect()->back()->with('success', 'Password reset successfully for ' . $user->name . '.');
    }

    // New method for DGM user management
    public function showDGMUserManagement()
    {
        // Get the authenticated user
        $currentUser = Auth::user();
        
        // Check if user is Program Administrator (level 01) or Developer
        if (!in_array($currentUser->user_role, ['Program Administrator (level 01)', 'Developer'])) {
            abort(403, 'Access denied. Only Program Administrator (level 01) and Developer can access this page.');
        }

        // Fetch all users for management
        $users = User::all();

        // Prepare the array with user details
        $usersArray = $users->map(function ($user) {
            return [
                'user_id' => $user->user_id,
                'user_name' => $user->name,
                'email' => $user->email,
                'employee_id' => $user->employee_id,
                'user_role' => $user->user_role,
                'status' => ($user->status == "1" ? "Active" : ($user->status == "0" ? "Inactive" : ($user->status == "2" ? "Suspended" : "Unknown"))),
                'user_location' => $user->user_location ?? 'Unknown',
                'created_at' => $user->created_at ? $user->created_at->format('Y-m-d H:i:s') : 'N/A',
                'updated_at' => $user->updated_at ? $user->updated_at->format('Y-m-d H:i:s') : 'N/A'
            ];
        });

        // Fetch all user roles from RoleHelper
        $userRoles = array_keys(\App\Helpers\RoleHelper::getRoles());

        // Add hardcoded locations array for dropdown
        $locations = [
            'Nebula Institute of Technology – Welisara',
            'Nebula Institute of Technology – Moratuwa',
            'Nebula Institute of Technology – Peradeniya'
        ];

        return view('user_management', [
            'usersArray' => $usersArray->toArray(),
            'userRoles' => $userRoles,
            'locations' => $locations,
        ]);
    }

    // Show the Create User form (GET)
    public function showCreateUserForm()
    {
        $currentUser = Auth::user();
        if (!in_array($currentUser->user_role, ['DGM', 'Program Administrator (level 01)', 'Developer'])) {
            abort(403, 'Access denied. Only DGM, Program Administrator (level 01), and Developer can access this page.');
        }
        $userRoles = array_keys(\App\Helpers\RoleHelper::getRoles());
        $locations = [
            'Nebula Institute of Technology – Welisara',
            'Nebula Institute of Technology – Moratuwa',
            'Nebula Institute of Technology – Peradeniya'
        ];
        return view('create_user', [
            'userRoles' => $userRoles,
            'locations' => $locations,
        ]);
    }

    // Method to get user details for editing
    public function getUserDetails(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);
            
            return response()->json([
                'success' => true,
                'user' => [
                    'user_id' => $user->user_id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'employee_id' => $user->employee_id,
                    'user_role' => $user->user_role,
                    'status' => $user->status,
                    'user_location' => $user->user_location,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'User not found'], 404);
        }
    }

    // Handle user creation (POST)
    public function createUser(CreateUserRequest $request)
    {
        try {
            // The validation is already handled by CreateUserRequest
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->employee_id = $request->employee_id;
            $user->user_role = $request->user_role;
            $user->status = 1; // Always Active
            $user->user_location = $request->user_location;
            $user->password = Hash::make($request->password);
            $user->save();

            // Log the user creation
            Log::info('User created successfully', [
                'created_by' => Auth::user()->user_id,
                'new_user_id' => $user->user_id,
                'new_user_email' => $user->email,
                'new_user_role' => $user->user_role,
                'new_user_location' => $user->user_location
            ]);

            return response()->json(['success' => true, 'message' => 'User created successfully.']);
        } catch (\Exception $e) {
            Log::error('Failed to create user', [
                'error' => $e->getMessage(),
                'created_by' => Auth::user()->user_id,
                'request_data' => $request->except(['password'])
            ]);

            return response()->json(['success' => false, 'message' => 'Failed to create user.', 'error' => $e->getMessage()], 500);
        }
    }
}
