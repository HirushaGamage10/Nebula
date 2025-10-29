<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseRegistration;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SpecialApprovalController extends Controller
{
    /**
     * Approve a pending special-approval registration, optionally attaching a reason and a file.
     * This finalizes the registration (status -> Registered, approval_status -> Approved by manager)
     * and stores any uploaded document under storage/app/public/special_approvals.
     */
    public function approveWithAttachment(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:course_registration,id',
            'reason'          => 'nullable|string|max:2000',
            'attachment'      => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png,webp|max:5120',
        ]);

        $registration = CourseRegistration::find($request->registration_id);

        // Only allow approving items that are pending special approval
        if (!$registration || $registration->status !== 'Special approval required') {
            return response()->json([
                'success' => false,
                'message' => 'Registration not pending special approval.'
            ], 422);
        }

        // Store attachment if provided
        if ($request->hasFile('attachment')) {
            $pdfPath = $request->file('attachment')->store('special_approvals', 'public');
            $registration->special_approval_pdf = $pdfPath;
        }

        // Persist reason in remarks (append if existing)
        $reason = trim((string) $request->input('reason'));
        if ($reason !== '') {
            $registration->remarks = trim(($registration->remarks ? $registration->remarks . ' ' : '') . "[DGM Reason] " . $reason);
        }

        // Assign course_registration_id if missing using intake pattern
        if (empty($registration->course_registration_id) && $registration->intake_id) {
            $intake = \App\Models\Intake::find($registration->intake_id);
            if ($intake && $intake->course_registration_id_pattern) {
                $pattern = $intake->course_registration_id_pattern; // e.g. 2025/HND/SE/001
                if (preg_match('/^(.*?)(\d+)$/', $pattern, $matches)) {
                    $prefix = $matches[1];
                    $numberLength = strlen($matches[2]);
                    $latest = CourseRegistration::where('intake_id', $registration->intake_id)
                        ->where('course_registration_id', 'like', $prefix . '%')
                        ->whereNotNull('course_registration_id')
                        ->where('course_registration_id', '!=', '')
                        ->orderByDesc('course_registration_id')
                        ->first();
                    if ($latest && preg_match('/^(.*?)(\d+)$/', $latest->course_registration_id, $latestMatches)) {
                        $nextNumber = str_pad(((int)$latestMatches[2]) + 1, $numberLength, '0', STR_PAD_LEFT);
                    } else {
                        $nextNumber = str_pad(((int)$matches[2]), $numberLength, '0', STR_PAD_LEFT);
                    }
                    $registration->course_registration_id = $prefix . $nextNumber;
                }
            }
        }

        // Finalize approval + registration
        $registration->approval_status = 'Approved by manager';
        $registration->status = 'Registered';
        $registration->registration_date = now();
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Registration approved and finalized successfully.'
        ]);
    }

    /**
     * Reject a pending special-approval registration with a required reason.
     */
    public function rejectWithReason(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:course_registration,id',
            'reason'          => 'required|string|max:2000',
        ]);

        $registration = CourseRegistration::find($request->registration_id);
        if (!$registration || $registration->status !== 'Special approval required') {
            return response()->json([
                'success' => false,
                'message' => 'Registration not pending special approval.'
            ], 422);
        }

        $registration->approval_status = 'Rejected';
        $registration->remarks = trim(($registration->remarks ? $registration->remarks . ' ' : '') . "[Rejected Reason] " . $request->reason);
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Registration rejected successfully.'
        ]);
    }
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

    // Download special approval document
    public function downloadDocument($filename)
    {
        // Validate filename to prevent directory traversal
        if (!preg_match('/^[a-zA-Z0-9._-]+$/', $filename)) {
            abort(404, 'Invalid filename');
        }

        $filePath = 'special_approvals/' . $filename;
        
        // Check if file exists
        if (!Storage::disk('public')->exists($filePath)) {
            abort(404, 'Document not found');
        }

        // Get file info
        $file = Storage::disk('public')->get($filePath);
        $mimeType = Storage::disk('public')->mimeType($filePath);
        
        // Return file as response
        return response($file, 200, [
            'Content-Type' => $mimeType,
            'Content-Disposition' => 'inline; filename="' . $filename . '"'
        ]);
    }
} 