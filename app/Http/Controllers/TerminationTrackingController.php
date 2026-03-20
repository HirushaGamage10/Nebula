<?php

namespace App\Http\Controllers;

use App\Models\ClearanceRequest;
use App\Models\CourseRegistration;
use App\Models\SemesterRegistration;
use App\Models\Student;
use App\Models\StudentStatusHistory;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class TerminationTrackingController extends Controller
{
    public function index()
    {
        $processes = Student::where('academic_status', Student::ACADEMIC_TERMINATED)
            ->orderByDesc('updated_at')
            ->get()
            ->map(function (Student $student) {
                return $this->buildProcessRow($student);
            })
            ->values();

        $summary = [
            'total' => $processes->count(),
            'clearance_in_progress' => $processes->filter(function ($process) {
                return in_array($process['overall_status']['key'], ['not_started', 'awaiting_clearances', 'clearance_rejected'], true);
            })->count(),
            'awaiting_dgm' => $processes->filter(function ($process) {
                return $process['overall_status']['key'] === 'awaiting_dgm';
            })->count(),
            'completed' => $processes->filter(function ($process) {
                return in_array($process['overall_status']['key'], ['completed', 'dgm_approved'], true);
            })->count(),
        ];

        $filters = [
            'locations' => $this->uniqueProcessValues($processes, 'location'),
            'courses' => $this->uniqueProcessValues($processes, 'course_name'),
            'intakes' => $this->uniqueProcessValues($processes, 'intake_name'),
        ];

        return view('student_management.termination_tracking', [
            'processes' => $processes,
            'summary' => $summary,
            'filters' => $filters,
        ]);
    }

    private function uniqueProcessValues(Collection $processes, string $key): Collection
    {
        return $processes
            ->map(function (array $process) use ($key) {
                return $process[$key] ?? null;
            })
            ->filter()
            ->unique()
            ->sortBy(function (string $value) {
                return strtolower($value);
            })
            ->values();
    }

    private function buildProcessRow(Student $student): array
    {
        $terminationHistory = StudentStatusHistory::where('student_id', $student->student_id)
            ->where('to_status', Student::ACADEMIC_TERMINATED)
            ->with('user')
            ->latest('created_at')
            ->first();

        $latestRegistration = CourseRegistration::where('student_id', $student->student_id)
            ->with(['course', 'intake'])
            ->orderByDesc('registration_date')
            ->orderByDesc('id')
            ->first();

        $clearances = collect(ClearanceRequest::getClearanceTypes())
            ->map(function ($label, $type) use ($student) {
                $request = ClearanceRequest::where('student_id', $student->student_id)
                    ->where('clearance_type', $type)
                    ->with(['approvedBy', 'course', 'intake'])
                    ->orderByDesc('requested_at')
                    ->orderByDesc('id')
                    ->first();

                if (!$request) {
                    return [
                        'type' => $type,
                        'label' => $label,
                        'status_key' => 'not_requested',
                        'status_label' => 'Not requested',
                        'badge_class' => 'bg-secondary',
                        'requested_at' => null,
                        'approved_at' => null,
                        'remarks' => null,
                        'approved_by' => null,
                        'course_name' => null,
                        'intake_name' => null,
                        'clearance_slip_url' => null,
                    ];
                }

                return [
                    'type' => $type,
                    'label' => $label,
                    'status_key' => $request->status,
                    'status_label' => ucfirst($request->status),
                    'badge_class' => $this->statusBadgeClass($request->status),
                    'requested_at' => optional($request->requested_at)->format('Y-m-d H:i'),
                    'approved_at' => optional($request->approved_at)->format('Y-m-d H:i'),
                    'remarks' => $request->remarks,
                    'approved_by' => optional($request->approvedBy)->name ?? optional($request->approvedBy)->full_name,
                    'course_name' => optional($request->course)->course_name,
                    'intake_name' => optional($request->intake)->batch,
                    'clearance_slip_url' => $this->publicFileUrl($request->clearance_slip),
                ];
            })
            ->values();

        $clearanceSummary = [
            'approved' => $clearances->where('status_key', ClearanceRequest::STATUS_APPROVED)->count(),
            'pending' => $clearances->where('status_key', ClearanceRequest::STATUS_PENDING)->count(),
            'rejected' => $clearances->where('status_key', ClearanceRequest::STATUS_REJECTED)->count(),
            'not_requested' => $clearances->where('status_key', 'not_requested')->count(),
            'requested' => $clearances->filter(function ($clearance) {
                return $clearance['status_key'] !== 'not_requested';
            })->count(),
        ];

        $dgmRequest = SemesterRegistration::where('student_id', $student->student_id)
            ->where(function ($query) {
                $query->where('desired_status', 'registered')
                    ->orWhereIn('approval_status', ['pending', 'approved', 'rejected']);
            })
            ->with(['course', 'intake', 'semester'])
            ->orderByDesc('approval_requested_at')
            ->orderByDesc('approval_decided_at')
            ->orderByDesc('updated_at')
            ->first();

        $dgmStatus = $this->buildDgmStatus($dgmRequest);
        $overallStatus = $this->buildOverallStatus($clearanceSummary, $dgmStatus);

        return [
            'student_id' => $student->student_id,
            'student_name' => $student->full_name,
            'student_nic' => $student->id_value,
            'location' => $student->institute_location,
            'academic_status' => $student->academic_status,
            'termination_reason' => $terminationHistory->reason ?? $student->academic_status_reason,
            'terminated_at' => optional($terminationHistory->created_at ?? $student->academic_status_changed_at)->format('Y-m-d H:i'),
            'terminated_by' => optional($terminationHistory?->user)->name ?? optional($terminationHistory?->user)->full_name,
            'termination_document_url' => $this->publicFileUrl($terminationHistory->document ?? $student->academic_status_document),
            'course_name' => optional($latestRegistration?->course)->course_name,
            'intake_name' => optional($latestRegistration?->intake)->batch,
            'clearances' => $clearances,
            'clearance_summary' => $clearanceSummary,
            'dgm_status' => $dgmStatus,
            'overall_status' => $overallStatus,
            'profile_url' => url('/student/profile/' . $student->student_id),
        ];
    }

    private function buildDgmStatus(?SemesterRegistration $request): array
    {
        if (!$request) {
            return [
                'required' => false,
                'status_key' => 'not_required',
                'status_label' => 'Not required',
                'badge_class' => 'bg-secondary',
                'course_name' => null,
                'intake_name' => null,
                'semester_name' => null,
                'reason' => null,
                'comment' => null,
                'requested_at' => null,
                'decided_at' => null,
                'document_url' => null,
            ];
        }

        $statusKey = in_array($request->approval_status, ['pending', 'approved', 'rejected'], true)
            ? $request->approval_status
            : 'not_required';

        return [
            'required' => $statusKey !== 'not_required',
            'status_key' => $statusKey,
            'status_label' => match ($statusKey) {
                'pending' => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                default => 'Not required',
            },
            'badge_class' => $statusKey === 'not_required' ? 'bg-secondary' : $this->statusBadgeClass($statusKey),
            'course_name' => optional($request->course)->course_name,
            'intake_name' => optional($request->intake)->batch,
            'semester_name' => optional($request->semester)->name,
            'reason' => $request->approval_reason,
            'comment' => $request->approval_dgm_comment,
            'requested_at' => optional($request->approval_requested_at)->format('Y-m-d H:i'),
            'decided_at' => optional($request->approval_decided_at)->format('Y-m-d H:i'),
            'document_url' => $this->publicFileUrl($request->approval_file_path),
        ];
    }

    private function buildOverallStatus(array $clearanceSummary, array $dgmStatus): array
    {
        if ($clearanceSummary['requested'] === 0) {
            return [
                'key' => 'not_started',
                'label' => 'No clearances requested',
                'badge_class' => 'bg-secondary',
            ];
        }

        if ($clearanceSummary['rejected'] > 0) {
            return [
                'key' => 'clearance_rejected',
                'label' => 'Clearance rejected',
                'badge_class' => 'bg-danger',
            ];
        }

        if ($clearanceSummary['pending'] > 0 || $clearanceSummary['not_requested'] > 0) {
            return [
                'key' => 'awaiting_clearances',
                'label' => 'Awaiting clearances',
                'badge_class' => 'bg-warning text-dark',
            ];
        }

        if ($dgmStatus['status_key'] === 'pending') {
            return [
                'key' => 'awaiting_dgm',
                'label' => 'Awaiting DGM approval',
                'badge_class' => 'bg-info text-dark',
            ];
        }

        if ($dgmStatus['status_key'] === 'rejected') {
            return [
                'key' => 'dgm_rejected',
                'label' => 'DGM rejected',
                'badge_class' => 'bg-danger',
            ];
        }

        if ($dgmStatus['status_key'] === 'approved') {
            return [
                'key' => 'dgm_approved',
                'label' => 'DGM approved',
                'badge_class' => 'bg-success',
            ];
        }

        return [
            'key' => 'completed',
            'label' => 'Clearances completed',
            'badge_class' => 'bg-success',
        ];
    }

    private function statusBadgeClass(string $status): string
    {
        return match ($status) {
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'pending' => 'bg-warning text-dark',
            default => 'bg-secondary',
        };
    }

    private function publicFileUrl(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        $normalizedPath = preg_replace('#^public/#', '', $path);

        return Storage::disk('public')->url($normalizedPath);
    }
}