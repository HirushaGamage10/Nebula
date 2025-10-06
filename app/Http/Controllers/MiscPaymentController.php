<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PaymentDetail;
use App\Models\Student;
use Illuminate\Support\Facades\Validator;

class MiscPaymentController extends Controller
{
    public function index()
    {
        return view('finance.misc_payment');
    }

    public function store(Request $request)
{
    // 🔹 Validate base fields
    $validator = Validator::make($request->all(), [
        'student_id'      => 'required|string', // can be NIC or ID
        'misc_category'   => 'required|string|max:255',
        'amount'          => 'required|numeric|min:1',
        'payment_method'  => 'required|string|max:100',
        'transaction_id'  => 'nullable|string|max:255',
        'remarks'         => 'nullable|string|max:500',
    ]);

    if ($validator->fails()) {
        return response()->json(['success' => false, 'errors' => $validator->errors()]);
    }

    // 🔍 Try to find student by NIC or by student_id
    $student = \App\Models\Student::where('id_value', $request->student_id) // NIC check
        ->orWhere('student_id', $request->student_id)                       // direct ID check
        ->first();

    if (!$student) {
        return response()->json([
            'success' => false,
            'message' => 'No student found for the provided NIC or Student ID.'
        ]);
    }

    // ✅ Create payment
    $payment = PaymentDetail::create([
        'student_id'        => $student->student_id,
        'misc_category'     => $request->misc_category,
        'misc_reference'    => $request->misc_reference ?? null,
        'description'       => $request->remarks ?? null,
        'amount'            => $request->amount,
        'payment_method'    => $request->payment_method,
        'transaction_id'    => $request->transaction_id ?? null,
        'status'            => 'paid',
        'late_fee'          => 0,
        'approved_late_fee' => 0,
        'total_fee'         => $request->amount,
    ]);

    return response()->json([
        'success' => true,
        'message' => 'Miscellaneous payment recorded successfully.',
        'data'    => $payment,
    ]);
}


    public function fetchByStudent($input)
{
    // Try to resolve to actual student_id
    $student = \App\Models\Student::where('id_value', $input)
        ->orWhere('student_id', $input)
        ->first();

    if (!$student) {
        return response()->json(['success' => false, 'message' => 'Student not found.']);
    }

    $payments = PaymentDetail::where('student_id', $student->student_id)
        ->whereNull('course_registration_id')
        ->latest()
        ->get();

    return response()->json(['success' => true, 'payments' => $payments]);
}

}
