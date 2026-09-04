<?php

namespace Tests\Unit;

use App\Http\Controllers\AllClearanceController;
use App\Models\ClearanceRequest;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class AllClearanceControllerSearchMethodsTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        if (!Schema::hasTable('clearance_requests')) {
            Schema::create('clearance_requests', function (Blueprint $table) {
                $table->id();
                $table->string('student_id')->nullable();
                $table->string('clearance_type')->nullable();
                $table->datetime('requested_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function test_legacy_search_methods_do_not_crash_when_clearance_model_is_used(): void
    {
        $controller = new AllClearanceController();

        $libraryResponse = $controller->librarysearch(new Request(['student_id' => 'STU-999']));
        $paymentResponse = $controller->paymentsearch(new Request(['student_id' => 'STU-999']));
        $hostelResponse = $controller->hostelsearch(new Request(['student_id' => 'STU-999']));
        $projectResponse = $controller->projectsearch(new Request(['student_id' => 'STU-999']));

        $this->assertInstanceOf(\Illuminate\View\View::class, $libraryResponse);
        $this->assertInstanceOf(\Illuminate\View\View::class, $paymentResponse);
        $this->assertInstanceOf(\Illuminate\View\View::class, $hostelResponse);
        $this->assertInstanceOf(\Illuminate\View\View::class, $projectResponse);
        $this->assertSame('STU-999', $libraryResponse->getData()['studentIdLibrary']);
        $this->assertSame('STU-999', $paymentResponse->getData()['studentIdPayment']);
        $this->assertSame('STU-999', $hostelResponse->getData()['studentId']);
        $this->assertSame('STU-999', $projectResponse->getData()['studentIdProject']);
    }
}
