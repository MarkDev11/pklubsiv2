// Dashboard chart component
export default function initDashboardCharts() {
    // Check if we're on the dashboard page
    const chartContainers = document.querySelectorAll('[data-chart]');
    
    if (chartContainers.length === 0) return;

    chartContainers.forEach(container => {
        const chartType = container.dataset.chart;
        const ctx = container.getContext('2d');
        
        if (!ctx) return;

        // Common chart options
        const commonOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    grid: {
                        display: false,
                        drawBorder: false
                    },
                    ticks: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                    }
                },
                y: {
                    grid: {
                        color: document.documentElement.classList.contains('dark') ? 'rgba(75, 85, 99, 0.3)' : '#e5e7eb',
                        drawBorder: false
                    },
                    ticks: {
                        color: document.documentElement.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                    }
                }
            }
        };

        // Initialize different chart types
        switch(chartType) {
            case 'line':
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                        datasets: [{
                            label: 'Data',
                            data: [12, 19, 15, 25, 22, 30],
                            borderColor: '#2563eb',
                            backgroundColor: 'rgba(37, 99, 235, 0.1)',
                            tension: 0.4,
                            fill: true
                        }]
                    },
                    options: commonOptions
                });
                break;
                
            case 'bar':
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: ['A', 'B', 'C', 'D', 'E'],
                        datasets: [{
                            label: 'Data',
                            data: [65, 59, 80, 81, 56],
                            backgroundColor: '#2563eb',
                            borderRadius: 4
                        }]
                    },
                    options: commonOptions
                });
                break;
                
            case 'doughnut':
                new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: ['Completed', 'In Progress', 'Pending'],
                        datasets: [{
                            data: [65, 25, 10],
                            backgroundColor: ['#2563eb', '#3b82f6', '#60a5fa'],
                            borderWidth: 0
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        cutout: '75%',
                        plugins: {
                            legend: {
                                display: false
                            }
                        }
                    }
                });
                break;
        }
    });
}
