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
                            $1,200
                            <i class="fas fa-arrow-up text-green-500 ml-2">
                            </i>
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mt-6 mb-4">
                            Daily Clients
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            <i class="fas fa-user mr-2">
                            </i>
                            90
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
                            $8,400
                            <i class="fas fa-arrow-up text-green-500 ml-2">
                            </i>
                        </p>
                    </div>
                    <div>
                        <h2 class="text-xl font-bold mt-6 mb-4">
                            Weekly Clients
                        </h2>
                        <p class="text-gray-700 text-2xl">
                            <i class="fas fa-user mr-2">
                            </i>
                            630
                        </p>
                    </div>
                </div>
                <!-- Common Diseases Pie Chart -->
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <h2 class="text-xl font-bold mb-4">
                        Common Diseases
                    </h2>
                    <div class="relative w-full h-64">
                        <canvas id="diseasesChart">
                        </canvas>
                    </div>
                </div>
            </div>
            <!-- Monthly Sales and Monthly Clients Bar Graph -->
            <div class="bg-white p-6 rounded-lg shadow-md mt-4">
                <h2 class="text-xl font-bold mb-4">
                    Monthly Sales and Clients
                </h2>
                <div class="relative w-full h-64">
                    <canvas id="monthlyChart">
                    </canvas>
                </div>
            </div>
        </div>
        <script>
            const diseasesCtx = document.getElementById('diseasesChart').getContext('2d');
            const diseasesChart = new Chart(diseasesCtx, {
                type: 'pie',
                data: {
                    labels: ['Fleas and Ticks', 'Canine Parvovirus', 'Ear Infections', 'Dental Disease', 'Obesity'],
                    datasets: [{
                        label: 'Common Diseases',
                        data: [30, 20, 15, 25, 10],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.2)',
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 206, 86, 0.2)',
                            'rgba(75, 192, 192, 0.2)',
                            'rgba(153, 102, 255, 0.2)'
                        ],
                        borderColor: [
                            'rgba(255, 99, 132, 1)',
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 206, 86, 1)',
                            'rgba(75, 192, 192, 1)',
                            'rgba(153, 102, 255, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
            const monthlyChart = new Chart(monthlyCtx, {
                type: 'bar',
                data: {
                    labels: ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
                    datasets: [{
                            label: 'Monthly Sales ($)',
                            data: [12000, 15000, 13000, 17000, 14000, 16000, 18000, 19000, 20000, 21000, 22000, 23000],
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 1,
                            yAxisID: 'y'
                        },
                        {
                            label: 'Monthly Clients',
                            data: [1550, 1600, 1555, 1570, 1565, 1575, 1580, 1585, 1590, 1595, 1600, 1605],
                            backgroundColor: 'rgba(255, 206, 86, 0.2)',
                            borderColor: 'rgba(255, 206, 86, 1)',
                            borderWidth: 1,
                            yAxisID: 'y1'
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            position: 'left',
                            title: {
                                display: true,
                                text: 'Sales ($)'
                            }
                        },
                        y1: {
                            beginAtZero: true,
                            position: 'right',
                            title: {
                                display: true,
                                text: 'Clients'
                            },
                            grid: {
                                drawOnChartArea: false
                            }
                        }
                    }
                }
            });
        </script>
    </div>
</div>
@endsection