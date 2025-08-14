<div class="bg-white p-6 rounded-lg shadow-md mt-4">
    <h2 class="text-xl font-bold mb-4">
        Monthly Sales and Clients
    </h2>

    <div class="flex flex-col md:flex-row gap-6">
        <!-- Monthly Clients Chart -->
        <div class="relative w-full md:w-1/2 h-64 mb-6 md:mb-0">
            <canvas id="monthlyClientsChart"></canvas>
        </div>

        <!-- Monthly Sales Chart -->
        <div class="relative w-full md:w-1/2 h-64">
            <canvas id="monthlySalesChart"></canvas>
        </div>
    </div>
</div>

<!-- Monthly Clients Chart Script -->
<!-- Monthly Clients Chart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/monthly-clients')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('monthlyClientsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels, // Ensure labels are month names
                        datasets: [{
                            label: 'Monthly Clients',
                            data: data.data,
                            backgroundColor: 'rgba(54, 162, 235, 0.2)',
                            borderColor: 'rgba(54, 162, 235, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.label}: ${context.raw} clients`
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'category',
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Clients'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching monthly clients:', error);
            });
    });
</script>

<!-- Monthly Sales Chart Script -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/monthly-sales')
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('monthlySalesChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.labels, // Ensure labels are month names
                        datasets: [{
                            label: 'Monthly Sales',
                            data: data.data,
                            backgroundColor: 'rgba(255, 99, 132, 0.2)',
                            borderColor: 'rgba(255, 99, 132, 1)',
                            borderWidth: 2,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: { position: 'top' },
                            tooltip: {
                                callbacks: {
                                    label: context => `${context.label}: ₱${context.raw.toLocaleString()}`
                                }
                            }
                        },
                        scales: {
                            x: {
                                type: 'category',
                                title: {
                                    display: true,
                                    text: 'Month'
                                }
                            },
                            y: {
                                beginAtZero: true,
                                title: {
                                    display: true,
                                    text: 'Sales (₱)'
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching monthly sales:', error);
            });
    });
</script>
