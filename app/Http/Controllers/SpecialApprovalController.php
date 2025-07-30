<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialApprovalController extends Controller
{
    // Handle special approval registration (PDF upload, status update)
    public function register(Request $request)
    {
        $request->validate([
            'student_nic' => 'required',
            'student_registration_number' => 'required',
            'special_approval_pdf' => 'required|file|mimes:pdf|max:2048',
            'payment_type' => 'required|in:Installment,Full',
        ]);

        // Find the course registration by student NIC
        $registration = CourseRegistration::whereHas('student', function($q) use ($request) {
            $q->where('id_value', $request->student_nic);
        })->where('status', 'Special approval required')
          ->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Student registration not found or not pending special approval.'], 404);
        }

        // Store the PDF
        $pdfPath = $request->file('special_approval_pdf')->store('special_approvals', 'public');

        // Update registration
        $registration->special_approval_pdf = $pdfPath;
        $registration->approval_status = 'Approved by manager';
        $registration->status = 'Registered';
        $registration->payment_type = $request->payment_type;
        $registration->registration_date = now();
        $registration->save();

        return response()->json(['success' => true, 'message' => 'Student approved and registered successfully.']);
    }

    // List for special approval page (with PDF URL for approved)
    public function list()
    {
        $students = CourseRegistration::with('student')
            ->where('status', 'Special approval required')
            ->orWhere('special_approval_pdf', '!=', null)
            ->get()
            ->map(function($reg) {
                return [
                    'registration_number' => $reg->student ? ($reg->student->registration_id ?? $reg->student->student_id) : $reg->id,
                    'name' => $reg->student ? $reg->student->full_name : 'Unknown Student',
                    'approval_status' => $reg->approval_status === 'Approved by manager' ? 1 : 0,
                    'pdf_url' => $reg->special_approval_pdf ? Storage::disk('public')->url($reg->special_approval_pdf) : null,
                    'student_id' => $reg->student_id,
                ];
            });
        return response()->json(['success' => true, 'students' => $students]);
    }
} 