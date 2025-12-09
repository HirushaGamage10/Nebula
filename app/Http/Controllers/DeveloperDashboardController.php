<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DeveloperDashboardController extends Controller
{
    public function index()
    {
        return view('dashboards.developer_dashboard');
    }
}
