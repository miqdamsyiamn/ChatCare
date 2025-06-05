/**
 * ChatCare - Reports JavaScript
 * 
 * Handles report functionality and visualization
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get report chart element
    const reportChartElement = document.getElementById('reportChart');
    
    // Check if chart element exists
    if (reportChartElement) {
        // Get data from data attribute
        let reportData;
        try {
            reportData = JSON.parse(document.getElementById('report-data').getAttribute('data-emotions'));
        } catch (error) {
            console.error('Error parsing report data:', error);
            return;
        }
        
        // Create pie chart for emotion distribution
        const reportChart = new Chart(reportChartElement.getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Positif', 'Netral', 'Negatif'],
                datasets: [{
                    data: [
                        reportData.positif || 0,
                        reportData.netral || 0,
                        reportData.negatif || 0
                    ],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Distribusi Emosi Diskusi',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
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
            }
        });
    }
    
    // Get user contribution chart element
    const contributionChartElement = document.getElementById('contributionChart');
    
    // Check if contribution chart element exists
    if (contributionChartElement) {
        // Get data from data attribute
        let contributionData;
        try {
            contributionData = JSON.parse(document.getElementById('contribution-data').getAttribute('data-contributions'));
        } catch (error) {
            console.error('Error parsing contribution data:', error);
            return;
        }
        
        // Create horizontal bar chart for user contributions
        const contributionChart = new Chart(contributionChartElement.getContext('2d'), {
            type: 'bar',
            data: {
                labels: contributionData.users || [],
                datasets: [{
                    label: 'Jumlah Pesan',
                    data: contributionData.messages || [],
                    backgroundColor: 'rgba(245, 124, 0, 0.6)',
                    borderColor: 'rgba(245, 124, 0, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pesan'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Kontribusi Peserta',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10
                    }
                }
            }
        });
    }
    
    // Get emotion timeline chart element
    const timelineChartElement = document.getElementById('timelineChart');
    
    // Check if timeline chart element exists
    if (timelineChartElement) {
        // Get data from data attribute
        let timelineData;
        try {
            timelineData = JSON.parse(document.getElementById('timeline-data').getAttribute('data-timeline'));
        } catch (error) {
            console.error('Error parsing timeline data:', error);
            return;
        }
        
        // Create line chart for emotion timeline
        const timelineChart = new Chart(timelineChartElement.getContext('2d'), {
            type: 'line',
            data: {
                labels: timelineData.labels || [],
                datasets: [
                    {
                        label: 'Positif',
                        data: timelineData.positif || [],
                        backgroundColor: 'rgba(75, 192, 192, 0.2)',
                        borderColor: 'rgba(75, 192, 192, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Netral',
                        data: timelineData.netral || [],
                        backgroundColor: 'rgba(54, 162, 235, 0.2)',
                        borderColor: 'rgba(54, 162, 235, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Negatif',
                        data: timelineData.negatif || [],
                        backgroundColor: 'rgba(255, 99, 132, 0.2)',
                        borderColor: 'rgba(255, 99, 132, 1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah Pesan'
                        },
                        stacked: false
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Waktu'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Perkembangan Emosi Selama Diskusi',
                        font: {
                            size: 16,
                            weight: 'bold'
                        },
                        padding: {
                            top: 10,
                            bottom: 20
                        }
                    },
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.7)',
                        padding: 10,
                        mode: 'index',
                        intersect: false
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                }
            }
        });
    }
    
    // Handle print report button
    const printButton = document.getElementById('print-report');
    
    if (printButton) {
        printButton.addEventListener('click', function(e) {
            e.preventDefault();
            window.print();
        });
    }
    
    // Handle download report button
    const downloadButton = document.getElementById('download-report');
    
    if (downloadButton) {
        downloadButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const reportId = this.getAttribute('data-report-id');
            
            // Redirect to download endpoint
            window.location.href = `/reports/download?report_id=${reportId}`;
        });
    }
    
    // Handle share report button
    const shareButton = document.getElementById('share-report');
    
    if (shareButton) {
        shareButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const reportUrl = window.location.href;
            
            // Check if Web Share API is available
            if (navigator.share) {
                navigator.share({
                    title: 'Laporan Diskusi ChatCare',
                    url: reportUrl
                })
                .catch(error => {
                    console.error('Error sharing:', error);
                    // Fallback to copy to clipboard
                    copyToClipboard(reportUrl);
                });
            } else {
                // Fallback to copy to clipboard
                copyToClipboard(reportUrl);
            }
        });
        
        // Function to copy URL to clipboard
        function copyToClipboard(text) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            document.body.appendChild(textarea);
            textarea.select();
            
            try {
                document.execCommand('copy');
                alert('URL laporan telah disalin ke clipboard!');
            } catch (error) {
                console.error('Error copying to clipboard:', error);
                alert('Gagal menyalin URL. Silakan salin secara manual: ' + text);
            }
            
            document.body.removeChild(textarea);
        }
    }
    
    // Handle generate report button
    const generateButton = document.getElementById('generate-report');
    
    if (generateButton) {
        generateButton.addEventListener('click', function(e) {
            e.preventDefault();
            
            const sessionId = this.getAttribute('data-session-id');
            
            // Disable button during generation
            this.disabled = true;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Membuat Laporan...';
            
            // Send request to generate report
            fetch('/reports/generate', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: `session_id=${sessionId}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Redirect to view the new report
                    window.location.href = `/reports/view?report_id=${data.data.report_id}`;
                } else {
                    console.error('Error generating report:', data.message);
                    alert('Gagal membuat laporan. Silakan coba lagi.');
                    
                    // Reset button
                    this.disabled = false;
                    this.innerHTML = 'Buat Laporan';
                }
            })
            .catch(error => {
                console.error('Error generating report:', error);
                alert('Gagal membuat laporan. Silakan coba lagi.');
                
                // Reset button
                this.disabled = false;
                this.innerHTML = 'Buat Laporan';
            });
        });
    }
});
