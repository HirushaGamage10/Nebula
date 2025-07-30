<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('students', function (Blueprint $table) {
            $table->id('student_id');
            $table->enum('title', [
                'Mr','Mrs','Miss','Dr','Rev','Ms','Sir','Madam','Prof',
                'Capt','Cmdr','Lt','Lt Col','Col','Gen','Adm','Sgt','Cpl',
                'Dr.(Mrs)','Dr.(Ms)','Dr.(Rev)','Dr.(Sir)','Dr.(Madam)','Dr.(Prof)'
            ]);
            $table->string('name_with_initials');
            $table->string('full_name');
            $table->enum('gender', ['Male', 'Female']);
            $table->enum('id_type', ['National id', 'Postal id', 'Passport', 'Driving Licence']);
            $table->string('id_value')->unique();
            $table->text('address')->nullable();
            $table->string('email')->nullable();
            $table->string('mobile_phone', 20)->nullable();
            $table->string('home_phone', 20)->nullable();
            $table->string('whatsapp_phone', 20)->nullable();
            $table->date('birthday')->nullable();
            $table->enum('institute_location', ['Welisara', 'Mathara', 'Peradeniya']);
            $table->boolean('foundation_program')->default(0);
            $table->text('special_needs')->nullable();
            $table->text('extracurricular_activities')->nullable();
            $table->text('future_potentials')->nullable();
            $table->text('other_document_upload')->nullable();
            $table->text('remarks')->nullable();
            $table->enum('status', ['Married','Unmarried']);
            $table->boolean('btec_completed')->default(0);
            $table->enum('marketing_survey', ['LinkedIn', 'Facebook', 'Radio Advertisement', 'TV advertisement'])->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('students');
    }
};
