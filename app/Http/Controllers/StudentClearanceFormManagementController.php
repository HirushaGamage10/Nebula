<?php

namespace App\Http\Controllers;

use App\Models\Batch;
use App\Models\CampusLocation;
use App\Models\CourseLocation;
use App\Models\CourseResult;
use App\Models\Courses;
use App\Models\Library;
use App\Models\Students;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StudentClearanceFormManagementController extends Controller
{
    // Method to show the student clearance form management view
    public function showStudentClearanceFormManagement()
    {


        // Pass campus locations, courses, batches, and locationCourseBatch to the view
        return view('student_clearance_form_management');
    }

    public function store(Request $request)
{
    try {
        // Validate the request data
        $validated = $request->validate([
            'student_id' => 'required|string|max:255',
            'student_name' => 'required|string|max:255',
            'name_of_the_book' => 'nullable|string|max:255',
            'fine_amount' => 'nullable|numeric',
            'date_taken' => 'required|date',
            //'date_received' => 'nullable|date',
            'is_cleared' => 'required|boolean',
        ]);

        // Create a new library record
        Library::create($validated);

        // Redirect back with a success message
        return redirect()->back()->with('success', 'Student details saved successfully!');
    } catch (\Exception $e) {
        // Handle any unexpected errors and redirect back with an error message
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}


    public function getStudentDetails(Request $request)
    {
        $studentId = $request->get('student_id');

        // Fetch the student by student_id
        $student = Students::where('student_id', $studentId)->first();

        if ($student) {
            // Redirect to the student profile page
            return redirect()->route('student.profile', ['studentId' => $student->student_id]);
        }

        // If not found, redirect back with an error message
        return redirect()->back()->with('error', 'Student not found.');
    }

    public function updateReceivedDate(Request $request)
{
    try {
        // Validate the request data
        $validated = $request->validate([
            'student_id' => 'required|string|max:255', // Ensure student_id is provided
            'name_of_the_book' => 'required|string|max:255', // Ensure book name is provided
            'date_received' => 'required|date', // Ensure date_received is provided and valid
            'is_cleared' => 'required|boolean', // Ensure is_cleared is a boolean
        ]);

        // Find the record for the given student_id and book
        $record = Library::where('student_id', $validated['student_id'])
            ->where('name_of_the_book', $validated['name_of_the_book'])
            ->first();

        // Check if the record exists
        if (!$record) {
            return redirect()->back()->with('error', 'Record not found for the specified student and book.');
        }

        // Update the record with the new values
        $record->update([
            'date_received' => $validated['date_received'],
            'is_cleared' => $validated['is_cleared'],
        ]);

        // Redirect with a success message
        return redirect()->back()->with('success', 'Received date and clearance status updated successfully!');
    } catch (\Exception $e) {
        // Handle unexpected errors
        return redirect()->back()->with('error', 'An error occurred: ' . $e->getMessage());
    }
}



    public function search(Request $request)
    {
        $studentId = $request->get('student_id');
        $records = Library::where('student_id', $studentId)->get();
        return view('student_clearance_form_management', compact('records', 'studentId'));
    }





    public function getCourseData($courseID)
    {
        // Fetch the relevant data based on the courseID
        // Assuming you have a Course model to fetch the data
        $data = Courses::find($courseID); // Modify this based on your actual data structure

        // Decode the JSON field
        $jsonData = json_decode($data->duration, true); // replace 'your_json_field' with the actual field name

        // Get the years
        $years = $jsonData['years'];
        $semesters = $data->semester;

        $response = [
            'years' => $years,
            'semesters' => $semesters,
        ];

        // Return the response as JSON
        return response()->json($response);
    }
}
