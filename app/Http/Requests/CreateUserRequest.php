<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/',
            'email' => [
                'required',
                'email',
                'max:255',
                'unique:users,email',
                'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/'
            ],
            'employee_id' => 'required|string|max:255|unique:users,employee_id',
            'user_role' => [
                'required',
                'string',
                Rule::in([
                    'DGM',
                    'Program Administrator (level 01)',
                    'Program Administrator (level 02)',
                    'Student Counselor',
                    'Librarian',
                    'Hostel Manager',
                    'Bursar',
                    'Project Tutor',
                    'Marketing Manager',
                    'Developer'
                ])
            ],
            'user_location' => [
                'required',
                'string',
                Rule::in([
                    'Nebula Institute of Technology – Welisara',
                    'Nebula Institute of Technology – Moratuwa',
                    'Nebula Institute of Technology – Peradeniya'
                ])
            ],
            'password' => 'required|string|min:6|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'User name is required.',
            'name.string' => 'User name must be a valid string.',
            'name.max' => 'User name cannot exceed 255 characters.',
            'name.regex' => 'User name can only contain letters and spaces.',
            
            'email.required' => 'Email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email address cannot exceed 255 characters.',
            'email.unique' => 'This email address is already registered.',
            'email.regex' => 'Please enter a valid email address format.',
            
            'employee_id.required' => 'Employee ID is required.',
            'employee_id.string' => 'Employee ID must be a valid string.',
            'employee_id.max' => 'Employee ID cannot exceed 255 characters.',
            'employee_id.unique' => 'This Employee ID is already registered.',
            
            'user_role.required' => 'User role is required.',
            'user_role.string' => 'User role must be a valid string.',
            'user_role.in' => 'Please select a valid user role.',
            
            'user_location.required' => 'User location is required.',
            'user_location.string' => 'User location must be a valid string.',
            'user_location.in' => 'Please select a valid user location.',
            
            'password.required' => 'Password is required.',
            'password.string' => 'Password must be a valid string.',
            'password.min' => 'Password must be at least 6 characters long.',
            'password.max' => 'Password cannot exceed 255 characters.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'user name',
            'email' => 'email address',
            'employee_id' => 'employee ID',
            'user_role' => 'user role',
            'user_location' => 'user location',
            'password' => 'password',
        ];
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param  \Illuminate\Contracts\Validation\Validator  $validator
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        throw new \Illuminate\Validation\ValidationException($validator);
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Additional custom validation logic can be added here
            if ($this->has('email')) {
                // Check for common email format issues
                $email = $this->email;
                
                // Check for spaces in email
                if (strpos($email, ' ') !== false) {
                    $validator->errors()->add('email', 'Email address cannot contain spaces.');
                }
                
                // Check for multiple @ symbols
                if (substr_count($email, '@') > 1) {
                    $validator->errors()->add('email', 'Email address can only contain one @ symbol.');
                }
                
                // Check for valid domain format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $validator->errors()->add('email', 'Please enter a valid email address format.');
                }
            }
            
            if ($this->has('password')) {
                // Check password strength
                $password = $this->password;
                
                if (strlen($password) < 6) {
                    $validator->errors()->add('password', 'Password must be at least 6 characters long.');
                }
                
                // Optional: Add more password strength requirements
                if (!preg_match('/[A-Z]/', $password)) {
                    $validator->errors()->add('password', 'Password should contain at least one uppercase letter.');
                }
                
                if (!preg_match('/[a-z]/', $password)) {
                    $validator->errors()->add('password', 'Password should contain at least one lowercase letter.');
                }
                
                if (!preg_match('/[0-9]/', $password)) {
                    $validator->errors()->add('password', 'Password should contain at least one number.');
                }
            }
        });
    }
} 