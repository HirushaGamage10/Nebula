<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\ExamResult;
use App\Models\Qualification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class AcademicDetailsController extends Controller
{
    /**
     * Get academic details for a student
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function getAcademicDetails(Request $request): JsonResponse
    {
        try {
            $studentId = $request->input('student_id');
            
            if (!$studentId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Student ID is required'
                ], 400);
            }

            // Get student's O/L and A/L exam results
            $examResults = ExamResult::where('student_id', $studentId)
                ->with(['olSubjects', 'alSubjects'])
                ->first();

            // Get other qualifications
            $qualifications = Qualification::where('student_id', $studentId)
                ->orderBy('year', 'desc')
                ->get();

            // Format the response
            $response = [
                'success' => true,
                'exams' => [
                    'ol_exam' => null,
                    'al_exam' => null
                ],
                'other_qualifications' => []
            ];

            // Format O/L results
            if ($examResults && $examResults->olSubjects) {
                $response['exams']['ol_exam'] = [
                    'year' => $examResults->ol_year,
                    'subjects' => $examResults->olSubjects->map(function ($subject) {
                        return [
                            'subject' => $subject->subject_name,
                            'grade' => $subject->grade
                        ];
                    })
                ];
            }

            // Format A/L results
            if ($examResults && $examResults->alSubjects) {
                $response['exams']['al_exam'] = [
                    'year' => $examResults->al_year,
                    'stream' => $examResults->al_stream,
                    'subjects' => $examResults->alSubjects->map(function ($subject) {
                        return [
                            'subject' => $subject->subject_name,
                            'grade' => $subject->grade
                        ];
                    })
                ];
            }

            // Format other qualifications
            if ($qualifications->isNotEmpty()) {
                $response['other_qualifications'] = $qualifications->map(function ($qualification) {
                    return [
                        'title' => $qualification->title,
                        'institution' => $qualification->institution,
                        'year' => $qualification->year
                    ];
                });
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error fetching academic details: ' . $e->getMessage()
            ], 500);
        }
    }
} 