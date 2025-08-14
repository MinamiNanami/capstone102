<!-- Common Diseases Pie Chart -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">
        Common Diseases
    </h2>
    <div class="relative w-full h-64 flex items-center justify-center">
        <canvas id="diseasesChart"></canvas>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        fetch('/common-diseases')  // Use the API route for common diseases
            .then(response => response.json())
            .then(data => {
                const ctx = document.getElementById('diseasesChart').getContext('2d');
                const myPieChart = new Chart(ctx, {
                    type: 'pie',
                    data: {
                        labels: data.labels,      // disease names
                        datasets: [{
                            label: 'Common Diseases',
                            data: data.data,        // disease counts
                            backgroundColor: [
                                'rgba(255, 206, 86, 0.7)',
                                'rgba(255, 99, 132, 0.7)',
                                'rgba(54, 162, 235, 0.7)',
                                'rgba(75, 192, 192, 0.7)',
                                'rgba(153, 102, 255, 0.7)',
                                'rgba(255, 159, 64, 0.7)',
                                'rgba(201, 203, 207, 0.7)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 99, 132, 1)',
                                'rgba(54, 162, 235, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)',
                                'rgba(201, 203, 207, 1)'
                            ],
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        plugins: {
                            legend: {
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(tooltipItem) {
                                        let total = tooltipItem.dataset.data.reduce((a, b) => a + b, 0);
                                        let value = tooltipItem.raw;
                                        let percentage = Math.round((value / total) * 100);
                                        return tooltipItem.label + ': ' + value + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            })
            .catch(error => {
                console.error('Error fetching common diseases:', error);
            });
    });
</script>
