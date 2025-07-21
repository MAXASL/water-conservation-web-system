document.addEventListener('DOMContentLoaded', function () {
    // Select all canvas elements
    const chartElements = document.querySelectorAll('canvas');

    // Iterate through each canvas element
    chartElements.forEach(canvas => {
        const labels = JSON.parse(canvas.dataset.labels || '[]'); // Default to empty array
        const data = JSON.parse(canvas.dataset.data || '[]'); // Default to empty array

        // Determine chart type based on id or use a default
        const chartType = canvas.id.includes('Summary') ? 'doughnut' : 'line';

        new Chart(canvas.getContext('2d'), {
            type: chartType,
            data: {
                labels: labels,
                datasets: [{
                    label: canvas.id.includes('Summary') ? 'Usage Summary' : 'Water Usage (Liters)',
                    data: data,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: chartType === 'line' ? 'rgba(75, 192, 192, 0.2)' : [
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 99, 132, 0.2)'
                    ],
                    borderWidth: 1,
                    fill: chartType === 'line'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                },
                scales: chartType === 'line' ? {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Values'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Time'
                        }
                    }
                } : {}
            }
        });

        // Add custom summary text based on chart data
        if (canvas.id === 'waterUsageChart1') {
            const totalUsage = data.reduce((sum, value) => sum + value, 0);
            const averageUsage = totalUsage / data.length;
            document.getElementById('usageSummaryChart1').innerHTML = `Total Water Usage: ${totalUsage} Liters. Average Usage: ${averageUsage.toFixed(2)} Liters.`;
        } else if (canvas.id === 'usageSummaryChart1') {
            const [normal, excessive] = data;
            const totalUsage = normal + excessive;
            document.getElementById('usageSummaryChart1').innerHTML = `Normal Usage: ${normal} Liters. Excessive Usage: ${excessive} Liters. Total Usage: ${totalUsage} Liters.`;
        } else if (canvas.id === 'waterUsageChart3') {
            const total = data.reduce((sum, value) => sum + value, 0);
            const average = total / data.length;
            document.getElementById('usageSummaryChart3').innerHTML = `Total: ${total} Units. Average: ${average.toFixed(2)} Units.`;
        } else if (canvas.id === 'waterUsageChart4') {
            const total = data.reduce((sum, value) => sum + value, 0);
            const average = total / data.length;
            document.getElementById('usageSummaryChart4').innerHTML = `Total: ${total} Units. Average: ${average.toFixed(2)} Units.`;
        } else if (canvas.id === 'waterUsageChart5') {
            const total = data.reduce((sum, value) => sum + value, 0);
            const average = total / data.length;
            document.getElementById('usageSummaryChart5').innerHTML = `Total: ${total} Units. Average: ${average.toFixed(2)} Units.`;
        }
    });
});
