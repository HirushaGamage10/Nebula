<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\CourseBadge;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;


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
        $courses = CourseRegistration::with(['course', 'intake'])
            ->where('student_id', $student->student_id)
            ->get()
            ->map(function ($c) {
                // 🔍 Always try to find matching badge manually
                $badge = \App\Models\CourseBadge::where('student_id', $c->student_id)
                    ->where('course_id', $c->course_id)
                    ->where('intake_id', $c->intake_id)
                    ->first();

                $c->badge = $badge; // attach badge to response
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




public function details($code)
{
    $badge = \App\Models\CourseBadge::with(['student','course','intake'])
        ->where('verification_code', $code)
        ->first();

    if (!$badge) {
        return response('<div class="text-danger text-center p-3 fw-bold">Badge not found.</div>', 404);
    }

    // If an image path exists, get public URL
    $imgUrl = $badge->badge_image_path 
        ? asset('storage/' . $badge->badge_image_path)
        : null;

    // Return HTML view snippet for modal body
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

        $registration->status = 'Completed';
        $registration->save();

        $uuid = Str::uuid();
        $badge = CourseBadge::create([
            'student_id'        => $registration->student_id,
            'course_id'         => $registration->course_id,
            'intake_id'         => $registration->intake_id,
            'badge_title'       => $course->course_name,
            'verification_code' => $uuid,
            'issued_date'       => now(),
            'status'            => 'active'
        ]);

        // ✅ Check if template exists
        $templatePath = public_path('images/badges/nebula_badge.png');
        if (!file_exists($templatePath)) {
            return response()->json(['success' => false, 'message' => 'Template image not found at '.$templatePath]);
        }

        $badgeImg = Image::make($templatePath);
        $studentName = $registration->student->first_name . ' ' . $registration->student->last_name;

        $badgeImg->text('Certificate of Completion', 400, 120, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(36);
            $font->color('#0d6efd');
            $font->align('center');
        });

        $badgeImg->text("Awarded to: {$studentName}", 400, 210, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(28);
            $font->color('#111');
            $font->align('center');
        });

        $badgeImg->text("For completing {$course->course_name}", 400, 270, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(24);
            $font->color('#333');
            $font->align('center');
        });

        $badgeImg->text("Nebula Institute of Technology", 400, 350, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(20);
            $font->color('#666');
            $font->align('center');
        });

        $badgeImg->text("Issued on " . now()->format('d M Y'), 400, 400, function($font) {
            $font->file(public_path('fonts/arial.ttf'));
            $font->size(18);
            $font->color('#999');
            $font->align('center');
        });

        $path = "badges/{$uuid}.png";
        $fullPath = storage_path("app/public/{$path}");
        $badgeImg->save($fullPath);

        $badge->update(['badge_image_path' => $path]);

        return response()->json([
            'success'          => true,
            'message'          => 'Course marked as completed and badge generated successfully.',
            'verification_url' => url('/verify-badge/'.$uuid)
        ]);

    } catch (\Throwable $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
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
