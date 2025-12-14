    <link rel="stylesheet" href="{{ asset('css/styles.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="{{ asset('js/tailwindcss.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <div id="pageContent" class="bg-gray-50">

        <!-- Navigation Tabs -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex space-x-1 py-3">
                    <button onclick="showTab('overview')" id="tab-overview"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-active">
                        <i class="fas fa-chart-line mr-2"></i>Overview
                    </button>
                    <button onclick="showTab('students')" id="tab-students"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-users mr-2"></i>Students
                    </button>
                    <button onclick="showTab('revenues')" id="tab-revenues"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-dollar-sign mr-2"></i>Revenues
                    </button>
                    <button onclick="showTab('outstanding')" id="tab-outstanding"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-exclamation-circle mr-2"></i>Outstanding
                    </button>
                    <button onclick="showTab('marketing')" id="tab-marketing"
                        class="px-4 py-2 rounded-lg text-sm font-medium tab-inactive">
                        <i class="fas fa-share-alt mr-2"></i>Marketing
                    </button>
                </div>
            </div>
        </nav>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Overview Tab -->
            <div id="content-overview" class="tab-content active">
                <!-- Key Metrics Cards -->
                <div class="flex gap-10">
                    <div class="stat-card bg-white p-2 rounded-xl shadow-sm border-4 border-sky-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Total Students</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2 p-2" id="totalStudents">-</p>
                                <p class="text-sm text-green-600" id="studentChange"></p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white p-2 rounded-xl shadow-sm border-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Yearly Revenue</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="yearlyRevenue">-</p>
                            </div>

                        </div>
                    </div>

                    <div class="stat-card bg-white p-2  rounded-xl shadow-sm border-4 border-orange-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Due this year</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="outstandingCurrentYear">-</p>
                            </div>
                        </div>
                    </div>

                    <div class="stat-card bg-white p-2  rounded-xl shadow-sm border-4 border-red-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-600">Outstanding</p>
                                <p class="text-2xl font-bold text-gray-900 pt-2" id="outstanding">-</p>
                            </div>
                        </div>
                    </div>


                </div>

                <!-- Quick Charts Grid -->
                <div class="grid gap-6">
                    <div class="bg-white p-6 rounded-xl shadow-sm">
                        <h3 class="text-lg font-semibold mb-4">Students by Location</h3>
                        <div style="height: 300px;">
                            <canvas id="studentsLocationChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Revenue Summary Table -->
                <div class="bg-white p-6 rounded-xl shadow-sm">
                    <h3 class="text-lg font-semibold mb-4">Revenue Summary</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Location
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ date('Y') }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                        {{ date('Y') - 1 }}
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Growth</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Outstanding
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="revenueSummaryBody" class="divide-y divide-gray-200">
                                <!-- JS will populate rows here -->
                            </tbody>
                            <tfoot class="bg-gray-50 border-t-2 border-gray-300">
                                <tr class="font-bold">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">Total</td>
                                    <td class="px-6 py-3 text-sm text-gray-900" id="totalCurrentYear">Rs. 0.00</td>
                                    <td class="px-6 py-3 text-sm text-gray-900" id="totalPreviousYear">Rs. 0.00</td>
                                    <td class="px-6 py-3 text-sm text-gray-900" id="totalGrowth">0%</td>
                                    <td class="px-6 py-3 text-sm text-gray-900" id="totalOutstanding">Rs. 0.00</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Students Tab -->
            <div id="content-students" class="tab-content">
                <!-- Filter Controls -->

                <div class="bg-white shadow-sm border-b">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-stretch">
                            <div class="filter-card">
                                <div class="flex items-center mb-1">
                                    <label class="block text-sm font-medium text-gray-700 mr-2">Year</label>
                                    <select id="yearSelect"
                                        class="border w-full border-gray-300 rounded-md px-3 py-2 bg-white text-sm">
                                        @for($y = date('Y'); $y >= 2010; $y--)
                                            <option value="{{ $y }}" {{ $y == date('Y') ? 'selected' : '' }}>{{ $y }}</option>
                                        @endfor
                                    </select>
                                </div>
                                <div class="flex flex-row gap-2 mb-6">
                                    <div class="filter-card p-2">
                                        <select id="studentMonthSelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Months</option>
                                            @for($m = 1; $m <= 12; $m++)
                                                <option value="{{ str_pad($m, 2, '0', STR_PAD_LEFT) }}">
                                                    {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                    <div class="filter-card p-2">
                                        <select id="studentDaySelect"
                                            class="w-full border border-gray-300 rounded-md px-2 py-1 bg-white text-xs">
                                            <option value="">All Days</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="filter-card flex flex-col gap-2">
                                <div class="filter-card flex flex-col gap-2">

                                    <!-- Compare and Range selectors at the top -->
                                    <div class="flex flex-row gap-2 mb-2">
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="compareToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Compare</span>
                                        </label>
                                        <label class="inline-flex items-center">
                                            <input type="checkbox" id="rangeSelectorToggle" class="mr-2">
                                            <span class="text-sm font-medium text-gray-700">Range</span>
                                        </label>
                                    </div>

