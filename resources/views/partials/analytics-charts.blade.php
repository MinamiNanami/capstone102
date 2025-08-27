<!-- Common Diseases Pie Chart -->
<div class="bg-white p-6 rounded-lg shadow-md">
    <h2 class="text-xl font-bold mb-4">
        Common Diseases
    </h2>
    <div class="flex flex-col md:flex-row items-center">
        <!-- Pie Chart -->
        <div class="relative w-full md:w-1/2 h-64 flex items-center justify-center">
            <canvas id="diseasesChart"></canvas>
        </div>

        <!-- Legend -->
        <div class="w-full md:w-1/2 mt-6 md:mt-0 md:pl-6">
            <ul id="diseasesLegend" class="space-y-2"></ul>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    fetch('/common-diseases')  // API endpoint returning {labels: [], data: []}
        .then(response => response.json())
        .then(data => {
            const ctx = document.getElementById('diseasesChart').getContext('2d');

            const total = data.data.reduce((a, b) => a + b, 0);

            const colors = [
                'rgba(255, 206, 86, 0.7)',
                'rgba(255, 99, 132, 0.7)',
                'rgba(54, 162, 235, 0.7)',
                'rgba(75, 192, 192, 0.7)',
                'rgba(153, 102, 255, 0.7)',
                'rgba(255, 159, 64, 0.7)',
                'rgba(201, 203, 207, 0.7)'
            ];

            const myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: data.labels,
                    datasets: [{
                        label: 'Common Diseases',
                        data: data.data,
                        backgroundColor: colors,
                        borderColor: colors.map(c => c.replace('0.7', '1')),
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false // disable default legend
                        }
                    }
                }
            });

            // Build custom legend list
            const legendContainer = document.getElementById('diseasesLegend');
            data.labels.forEach((label, index) => {
                const count = data.data[index];
                const percentage = ((count / total) * 100).toFixed(1);

                const li = document.createElement('li');
                li.classList.add('flex', 'items-center', 'space-x-2');

                li.innerHTML = `
                    <span class="w-4 h-4 inline-block rounded" style="background-color:${colors[index]}"></span>
                    <span class="font-medium">${label}</span>
                    <span class="ml-auto text-gray-600">${count} (${percentage}%)</span>
                `;

                legendContainer.appendChild(li);
            });
        })
        .catch(error => {
            console.error('Error fetching common diseases:', error);
        });
});
</script>
