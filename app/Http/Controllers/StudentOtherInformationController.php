<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentOtherInformation;
use App\Models\CourseRegistration;
use Illuminate\Http\Response;
use Illuminate\Database\QueryException;

class StudentOtherInformationController extends Controller
{
    // Method to show the student other information view
    public function showStudentOtherInformation()
    {
        return view('student_other_information');
    }

    public function getStudentDetails(Request $request)
    {
        try {
            $identificationType = $request->input('identificationType');
            $idValue = $request->input('idValue');

            // Find the student by identification type
            if ($identificationType === 'nic') {
                // Find the student by NIC number (id_value field)
                $student = Student::where('id_value', $idValue)->first();
            } elseif ($identificationType === 'registration_number') {
                // Find the student by registration number (join with course_registration table)
                $student = Student::join('course_registration', 'students.student_id', '=', 'course_registration.student_id')
                    ->where('course_registration.id', $idValue)
                    ->select('students.*')
                    ->first();
            } else {
                // Handle other identification types if necessary
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid identification type'
                ]);
            }

            // If student found, return the details
            if ($student) {
                return response()->json([
                    'success' => true,
                    'message' => 'Student found',
                    'data' => [
                        'student_id' => $student->student_id,
                        'student_name' => $student->full_name
                    ]
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Student not found'
                ]);
            }
        } catch (\Exception $e) {
            // Handle any database errors
            return response()->json([
                'success' => false,
                'message' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }

    public function storeOtherInformations(Request $request)
    {
        try {
            // Validate request data
            $request->validate([
                'studentName' => 'required|string',
                'studentID' => 'required|string',
                'disciplinaryIssues' => 'nullable|string',
                'continueStudies' => 'required|in:true,false',
                'institute' => 'nullable|string',
                'fieldOfStudy' => 'nullable|string',
                'currentlyEmployee' => 'required|in:true,false',
                'jobTitle' => 'nullable|string',
                'workplace' => 'nullable|string',
                'otherInformation' => 'nullable|string',
            ]);

            // Check if studentID and studentName match in the Student model
            $student = Student::where('student_id', $request->input('studentID'))
                ->where('full_name', $request->input('studentName'))
                ->first();

            $student_id = $request->input('studentID');

            if (!$student) {
                // Return a response indicating that student information does not exist
                return response()->json(['success' => false, 'message' => 'Student information does not exist'], Response::HTTP_BAD_REQUEST);
            }

            // Handle disciplinary issue document upload
            $disciplinaryIssueDocumentPath = null;
            if ($request->hasFile('disciplinary_issue_document')) {
                $file = $request->file('disciplinary_issue_document');
                if (in_array($file->getClientOriginalExtension(), ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'])) {
                    $disciplinaryIssueDocumentPath = $file->store('public/disciplinary_issues');
                }
            }

            // Prepare data for updating or creating the record
            $data = [
                'student_id' => $request->input('studentID'),
                'disciplinary_issues' => $request->input('disciplinaryIssues'),
                'disciplinary_issue_document' => $disciplinaryIssueDocumentPath,
                'continue_higher_studies' => $request->input('continueStudies') === 'true',
                'institute' => $request->input('institute'),
                'field_of_study' => $request->input('fieldOfStudy'),
                'currently_employee' => $request->input('currentlyEmployee') === 'true',
                'job_title' => $request->input('jobTitle'),
                'workplace' => $request->input('workplace'),
                'other_information' => $request->input('otherInformation'),
            ];

            // Update or create the record based on student_id
            $studentOtherInformation = StudentOtherInformation::updateOrCreate(
                ['student_id' => $request->input('studentID')],
                $data
            );

            // Return a success response with redirect
            return response()->json(['success' => true, 'message' => 'Data stored successfully', 'redirect' => route('student.other.information')], Response::HTTP_OK);
        } catch (QueryException $e) {
            // Return a response indicating failure due to database error
            return response()->json(['success' => false, 'message' => 'Database error: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        } catch (\Exception $e) {
            // Return a response indicating general failure
            return response()->json(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
