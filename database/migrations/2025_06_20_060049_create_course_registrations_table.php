<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('course_registration', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students', 'student_id')->onDelete('cascade');
            $table->foreignId('course_id')->constrained('courses', 'course_id')->onDelete('cascade');
            $table->foreignId('intake_id')->constrained('intakes', 'intake_id')->onDelete('cascade');
            
            $table->date('registration_date');
            $table->decimal('registration_fee', 10, 2)->default(0);

            $table->enum('status', ['Pending', 'Registered', 'Not eligible', 'Special approval required']);
            $table->enum('approval_status', ['Approved by manager', 'Sent to DGM', 'Rejected', 'Pending']);
            $table->enum('location', ['Welisara', 'Moratuwa', 'Peradeniya']);

            $table->boolean('slt_employee')->default(0);
            $table->string('employee_service_number')->nullable();

            $table->date('special_approval_date')->nullable();
            $table->text('remarks')->nullable();
            $table->string('special_approval_pdf')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('course_registration');
    }
};
