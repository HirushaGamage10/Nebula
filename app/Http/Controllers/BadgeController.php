<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\CourseBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class BadgeController extends Controller
{
    public function index()
    {
        return view('badges.generate');
    }

    public function searchStudent(Request $request)
    {
        $student = Student::where('student_id', $request->input('student_id'))
            ->orWhere('id_value', $request->input('student_id'))
            ->first();

        if (!$student) {
            return response()->json(['success' => false, 'message' => 'Student not found']);
        }

        try {
            $courses = CourseRegistration::with(['student', 'course', 'intake'])
                ->where('student_id', $student->student_id)
                ->get()
                ->map(function ($c) {
                    $badge = \App\Models\CourseBadge::where('student_id', $c->student_id)
                        ->where('course_id', $c->course_id)
                        ->where('intake_id', $c->intake_id)
                        ->first();

                    $c->badge = $badge;
                    return $c;
                });

            return response()->json([
                'success' => true,
                'student' => $student,
                'courses' => $courses
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }


    public function searchByCourse(Request $request)
    {
        $query = CourseRegistration::with(['student', 'course', 'intake']);

        // Apply filters dynamically
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('intake_id')) {
            $query->where('intake_id', $request->intake_id);
        }

        if ($request->filled('mode')) {
            $query->whereHas('intake', function ($q) use ($request) {
                $q->where('intake_mode', $request->mode);
            });
        }

        $registrations = $query->get()->map(function ($c) {
            $badge = \App\Models\CourseBadge::where('student_id', $c->student_id)
                ->where('course_id', $c->course_id)
                ->where('intake_id', $c->intake_id)
                ->first();
            $c->badge = $badge;
            return $c;
        });

        if ($registrations->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'No students found for this course.']);
        }

        return response()->json([
            'success' => true,
            'data' => $registrations
        ]);
    }


    public function details($code)
    {
        $badge = \App\Models\CourseBadge::with(['student','course','intake'])
            ->where('verification_code', $code)
            ->first();

        if (!$badge) {
            return response('<div class="text-danger text-center p-3 fw-bold">Badge not found.</div>', 404);
        }

        $imgUrl = $badge->badge_image_path 
            ? asset('storage/' . $badge->badge_image_path)
            : null;

        $html = "
        <div class='text-start'>
            <h5 class='text-primary mb-3 fw-bold'>{$badge->badge_title}</h5>
            <table class='table table-bordered'>
                <tr><th>ID</th><td>{$badge->id}</td></tr>
                <tr><th>Student ID</th><td>{$badge->student_id}</td></tr>
                <tr><th>Course</th><td>{$badge->course->course_name}</td></tr>
                <tr><th>Intake</th><td>{$badge->intake->batch}</td></tr>
                <tr><th>Verification Code</th><td><code>{$badge->verification_code}</code></td></tr>
                <tr><th>Issued Date</th><td>{$badge->issued_date}</td></tr>
                <tr><th>Status</th><td><span class='badge bg-success'>{$badge->status}</span></td></tr>
            </table>";

        if ($imgUrl) {
            $html .= "
            <div class='text-center mt-3'>
                <img src='{$imgUrl}' alt='Badge Image' class='img-fluid rounded shadow' style='max-height:300px;'>
            </div>";
        }

        $html .= "</div>";

        return response($html);
    }

public function completeCourse(Request $request)
{
    try {
        $registration = CourseRegistration::with(['course', 'intake', 'student'])->find($request->id);

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found']);
        }

        $course = $registration->course;
        $intake = $registration->intake;

        if (!$course || !$intake) {
            return response()->json(['success' => false, 'message' => 'Missing course or intake details.']);
        }

        if ($course->course_type !== 'certificate' || $intake->intake_mode !== 'Online') {
            return response()->json(['success' => false, 'message' => 'Only Online Certificate Courses are eligible for badges.']);
        }

        // ✅ Mark course as completed
        $registration->status = 'Completed';
        $registration->save();

        $uuid = Str::uuid();

        // ✅ Create DB record first
        $badge = CourseBadge::create([
            'student_id'        => $registration->student_id,
            'course_id'         => $registration->course_id,
            'intake_id'         => $registration->intake_id,
            'badge_title'       => $course->course_name,
            'verification_code' => $uuid,
            'issued_date'       => now(),
            'status'            => 'active'
        ]);

        // ✅ Load base badge template
        $templatePath = public_path('images/badges/nebula_badge.png');
        if (!file_exists($templatePath)) {
            return response()->json(['success' => false, 'message' => 'Badge template not found.']);
        }

        $image = imagecreatefrompng($templatePath);

        // Colors
        $black = imagecolorallocate($image, 0, 0, 0);
        $blue  = imagecolorallocate($image, 13, 110, 253);
        $gray  = imagecolorallocate($image, 102, 102, 102);

        // ✅ Smart Font Detection (no manual download needed)
        $localFont  = public_path('fonts/arial.ttf');
        $systemFont = '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf';

        if (file_exists($localFont)) {
            $fontPath = $localFont;
        } elseif (file_exists($systemFont)) {
            $fontPath = $systemFont;
        } else {
            $fontPath = null; // fallback to built-in GD text later
        }

        $studentName = $registration->student->full_name 
        ?? $registration->student->name_with_initials 
        ?? 'Unknown Student';


        if ($fontPath) {
            // ✅ Use TTF font if available
            imagettftext($image, 30, 0, 180, 150, $blue, $fontPath, 'Certificate of Completion');
            imagettftext($image, 24, 0, 180, 220, $black, $fontPath, "Awarded to: {$studentName}");
            imagettftext($image, 20, 0, 180, 270, $black, $fontPath, "For completing {$course->course_name}");
            imagettftext($image, 18, 0, 180, 320, $gray, $fontPath, "Nebula Institute of Technology");
            imagettftext($image, 16, 0, 180, 370, $gray, $fontPath, "Issued on " . now()->format('d M Y'));
        } else {
            // ⚠️ Fallback: use simple GD text if no font file found
            imagestring($image, 5, 180, 150, 'Certificate of Completion', $blue);
            imagestring($image, 4, 180, 200, "Awarded to: {$studentName}", $black);
            imagestring($image, 4, 180, 250, "For completing {$course->course_name}", $black);
            imagestring($image, 3, 180, 300, "Nebula Institute of Technology", $gray);
            imagestring($image, 2, 180, 340, "Issued on " . now()->format('d M Y'), $gray);
        }

        // ✅ Save image
        $path = "badges/{$uuid}.png";
        $fullPath = storage_path("app/public/{$path}");
        imagepng($image, $fullPath);
        imagedestroy($image);

        // ✅ Update DB record
        $badge->update(['badge_image_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'Course marked as completed and badge generated successfully.',
            'verification_url' => url('/verify-badge/' . $uuid)
        ]);
    } catch (\Throwable $e) {
        return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}


    public function cancelBadge(Request $request)
    {
        $badge = null;
        if ($request->badge_id) {
            $badge = CourseBadge::find($request->badge_id);
        }

        $registration = CourseRegistration::find($request->registration_id);

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'Registration not found.']);
        }

        if ($badge) {
            if ($badge->badge_image_path && \Storage::disk('public')->exists($badge->badge_image_path)) {
                \Storage::disk('public')->delete($badge->badge_image_path);
            }
            $badge->delete();
        }

        $registration->status = 'Pending';
        $registration->save();

        return response()->json([
            'success' => true,
            'message' => 'Certificate cancelled and course reverted to pending status.'
        ]);
    }

    public function verify($code)
    {
        $badge = CourseBadge::where('verification_code', $code)->with(['student','course','intake'])->first();

        if (!$badge) {
            abort(404, 'Invalid badge link.');
        }

        return view('badges.verify', compact('badge'));
    }
}
