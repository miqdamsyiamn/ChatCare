/**
 * ChatCare - Charts JavaScript
 * 
 * Utility functions for creating and managing charts using Chart.js
 */

// Chart color schemes
const chartColors = {
    primary: '#f57c00',
    secondary: '#2196f3',
    success: '#4caf50',
    danger: '#f44336',
    warning: '#ff9800',
    info: '#00bcd4',
    light: '#f5f5f5',
    dark: '#212121',
    // Emotion colors
    positif: {
        fill: 'rgba(75, 192, 192, 0.2)',
        border: 'rgba(75, 192, 192, 1)'
    },
    netral: {
        fill: 'rgba(54, 162, 235, 0.2)',
        border: 'rgba(54, 162, 235, 1)'
    },
    negatif: {
        fill: 'rgba(255, 99, 132, 0.2)',
        border: 'rgba(255, 99, 132, 1)'
    }
};

// Common chart options
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
        legend: {
            position: 'bottom',
            labels: {
                usePointStyle: true,
                padding: 20,
                font: {
                    size: 12
                }
            }
        },
        tooltip: {
            backgroundColor: 'rgba(0, 0, 0, 0.7)',
            padding: 10,
            titleFont: {
                size: 14
            },
            bodyFont: {
                size: 13
            },
            displayColors: true
        }
    }
};

/**
 * Create a line chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createLineChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        },
        elements: {
            line: {
                tension: 0.3
            },
            point: {
                radius: 3,
                hoverRadius: 5
            }
        },
        interaction: {
            mode: 'index',
            intersect: false
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'line',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a bar chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createBarChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            y: {
                beginAtZero: true,
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        },
        barPercentage: 0.7,
        categoryPercentage: 0.7
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'bar',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a pie chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createPieChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        cutout: '0%',
        plugins: {
            ...chartDefaults.plugins,
            tooltip: {
                ...chartDefaults.plugins.tooltip,
                callbacks: {
                    label: function(context) {
                        const label = context.label || '';
                        const value = context.raw || 0;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = total > 0 ? Math.round((value / total) * 100) : 0;
                        return `${label}: ${value} (${percentage}%)`;
                    }
                }
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'pie',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a doughnut chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createDoughnutChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const defaultOptions = {
        cutout: '60%'
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return createPieChart(element, data, chartOptions);
}

/**
 * Create a horizontal bar chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createHorizontalBarChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const defaultOptions = {
        indexAxis: 'y'
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return createBarChart(element, data, chartOptions);
}

/**
 * Create a stacked bar chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createStackedBarChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const defaultOptions = {
        scales: {
            y: {
                stacked: true,
                beginAtZero: true
            },
            x: {
                stacked: true
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return createBarChart(element, data, chartOptions);
}

/**
 * Create a radar chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createRadarChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            r: {
                beginAtZero: true,
                angleLines: {
                    color: 'rgba(0, 0, 0, 0.1)'
                },
                grid: {
                    color: 'rgba(0, 0, 0, 0.05)'
                }
            }
        },
        elements: {
            line: {
                tension: 0.1
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'radar',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a polar area chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createPolarAreaChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            r: {
                beginAtZero: true
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'polarArea',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a bubble chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createBubbleChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            y: {
                beginAtZero: true
            },
            x: {
                beginAtZero: true
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'bubble',
        data: data,
        options: chartOptions
    });
}

/**
 * Create a scatter chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} data - Chart data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createScatterChart(element, data, options = {}) {
    if (!element || !data) return null;
    
    const ctx = element.getContext('2d');
    
    const defaultOptions = {
        ...chartDefaults,
        scales: {
            y: {
                beginAtZero: true
            },
            x: {
                beginAtZero: true
            }
        }
    };
    
    const chartOptions = { ...defaultOptions, ...options };
    
    return new Chart(ctx, {
        type: 'scatter',
        data: data,
        options: chartOptions
    });
}

/**
 * Update chart data
 * @param {Chart} chart - Chart instance
 * @param {Object} newData - New chart data
 */
function updateChartData(chart, newData) {
    if (!chart || !newData) return;
    
    chart.data = newData;
    chart.update();
}

/**
 * Create emotion distribution chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} emotionData - Emotion data (positif, netral, negatif counts)
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createEmotionChart(element, emotionData, options = {}) {
    if (!element || !emotionData) return null;
    
    const data = {
        labels: ['Positif', 'Netral', 'Negatif'],
        datasets: [{
            data: [
                emotionData.positif || 0,
                emotionData.netral || 0,
                emotionData.negatif || 0
            ],
            backgroundColor: [
                chartColors.positif.fill,
                chartColors.netral.fill,
                chartColors.negatif.fill
            ],
            borderColor: [
                chartColors.positif.border,
                chartColors.netral.border,
                chartColors.negatif.border
            ],
            borderWidth: 1
        }]
    };
    
    return createPieChart(element, data, options);
}

/**
 * Create emotion timeline chart
 * @param {HTMLElement} element - Canvas element
 * @param {Object} timelineData - Timeline data
 * @param {Object} options - Chart options
 * @returns {Chart} Chart instance
 */
function createEmotionTimelineChart(element, timelineData, options = {}) {
    if (!element || !timelineData) return null;
    
    const data = {
        labels: timelineData.labels || [],
        datasets: [
            {
                label: 'Positif',
                data: timelineData.positif || [],
                backgroundColor: chartColors.positif.fill,
                borderColor: chartColors.positif.border,
                borderWidth: 2,
                fill: true
            },
            {
                label: 'Netral',
                data: timelineData.netral || [],
                backgroundColor: chartColors.netral.fill,
                borderColor: chartColors.netral.border,
                borderWidth: 2,
                fill: true
            },
            {
                label: 'Negatif',
                data: timelineData.negatif || [],
                backgroundColor: chartColors.negatif.fill,
                borderColor: chartColors.negatif.border,
                borderWidth: 2,
                fill: true
            }
        ]
    };
    
    return createLineChart(element, data, options);
}

// Export functions
window.ChatCareCharts = {
    colors: chartColors,
    defaults: chartDefaults,
    createLineChart,
    createBarChart,
    createPieChart,
    createDoughnutChart,
    createHorizontalBarChart,
    createStackedBarChart,
    createRadarChart,
    createPolarAreaChart,
    createBubbleChart,
    createScatterChart,
    updateChartData,
    createEmotionChart,
    createEmotionTimelineChart
};
