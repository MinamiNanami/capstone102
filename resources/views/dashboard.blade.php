@extends('layouts.default-layout')
@section('content')
<div class="w-full p-4 ">
    <!-- Main content goes here -->
    <div class="bg-gray-100 font-roboto">
        <div class="container mx-auto p-4">
            <h1 class="text-3xl font-bold mb-4">
                Analytics Dashboard
            </h1>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <!-- Daily Sale and Daily Clients -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-bold mb-4">
                            Daily Sale
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            ₱ {{ $totalDailySales }}
                            @if($totalDailySales > $totalPreviousDaySales)
                            <i class="fas fa-arrow-up text-green-500 ml-2">
                            </i>
                            @else
                            <i class="fas fa-arrow-down text-red-500 ml-2">
                            </i>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mt-6 mb-4">
                            Daily Clients
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            <i class="fas fa-user mr-2">
                            </i>
                            {{ $totalDailyClients }}
                        </p>
                    </div>
                </div>
                <!-- Weekly Sale and Weekly Clients -->
                <div class="bg-white p-6 rounded-lg shadow-md flex flex-col justify-between">
                    <div>
                        <h2 class="text-xl font-bold mb-4">
                            Weekly Sale
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            ₱ {{ $totalWeeklySales }}
                            @if($totalWeeklySales > $totalPreviousWeekSales)
                            <i class="fas fa-arrow-up text-green-500 ml-2">
                            </i>
                            @else
                            <i class="fas fa-arrow-down text-red-500 ml-2">
                            </i>
                            @endif
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mt-6 mb-4">
                            Weekly Clients
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            <i class="fas fa-user mr-2">
                            </i>
                            {{ $totalWeeklyClients }}
                        </p>
                    </div>
                </div>
                @include('partials.analytics-charts')
            </div>
            <!-- Monthly Sales and Monthly Clients Bar Graph -->
            @include('partials.sales-and-clients-charts')
        </div>

    </div>
</div>
@endsection