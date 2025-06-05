/**
 * ChatCare - Mood Visualization JavaScript
 * 
 * Handles mood data visualization using Chart.js
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get session ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const sessionId = urlParams.get('session_id');
    
    // Get chart elements
    const lineChartElement = document.getElementById('moodChart');
    const pieChartElement = document.getElementById('moodPieChart');
    
    // Check if chart elements exist
    if (!lineChartElement || !pieChartElement) return;
    
    // Get mood data from the data attribute
    let moodData;
    try {
        moodData = JSON.parse(document.getElementById('mood-data').getAttribute('data-mood'));
    } catch (error) {
        console.error('Error parsing mood data:', error);
        return;
    }
    
    // Create line chart for emotion trends
    const lineChart = new Chart(lineChartElement.getContext('2d'), {
        type: 'line',
        data: {
            labels: moodData.labels || [],
            datasets: moodData.datasets || []
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
                    }
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
                    text: 'Tren Emosi Selama Diskusi',
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
                    titleFont: {
                        size: 14
                    },
                    bodyFont: {
                        size: 13
                    },
                    displayColors: true
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            },
            elements: {
                line: {
                    tension: 0.3 // Smooth curves
                },
                point: {
                    radius: 3,
                    hoverRadius: 5
                }
            }
        }
    });
    
    // Create pie chart for emotion distribution
    const pieChart = new Chart(pieChartElement.getContext('2d'), {
        type: 'pie',
        data: {
            labels: ['Positif', 'Netral', 'Negatif'],
            datasets: [{
                data: [
                    moodData.summary?.positif || 0,
                    moodData.summary?.netral || 0,
                    moodData.summary?.negatif || 0
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
                    text: 'Distribusi Emosi',
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
    
    // Function to update charts with new data
    function updateCharts(newMoodData) {
        if (!newMoodData) return;
        
        // Update line chart
        if (newMoodData.labels && newMoodData.datasets) {
            lineChart.data.labels = newMoodData.labels;
            lineChart.data.datasets = newMoodData.datasets;
            lineChart.update();
        }
        
        // Update pie chart
        if (newMoodData.summary) {
            pieChart.data.datasets[0].data = [
                newMoodData.summary.positif || 0,
                newMoodData.summary.netral || 0,
                newMoodData.summary.negatif || 0
            ];
            pieChart.update();
        }
    }
    
    // Function to update mood summary display
    function updateMoodSummary(newMoodData) {
        if (!newMoodData || !newMoodData.summary) return;
        
        const positifCount = newMoodData.summary.positif || 0;
        const netralCount = newMoodData.summary.netral || 0;
        const negatifCount = newMoodData.summary.negatif || 0;
        
        const total = positifCount + netralCount + negatifCount;
        const positifPercent = total > 0 ? Math.round((positifCount / total) * 100) : 0;
        const netralPercent = total > 0 ? Math.round((netralCount / total) * 100) : 0;
        const negatifPercent = total > 0 ? Math.round((negatifCount / total) * 100) : 0;
        
        // Determine overall mood
        let moodText = 'Netral';
        let moodEmoji = '😐';
        let moodColor = 'bg-blue-100 text-blue-800';
        
        if (positifCount > negatifCount && positifCount > netralCount) {
            moodText = 'Positif';
            moodEmoji = '😊';
            moodColor = 'bg-green-100 text-green-800';
        } else if (negatifCount > positifCount && negatifCount > netralCount) {
            moodText = 'Negatif';
            moodEmoji = '😟';
            moodColor = 'bg-red-100 text-red-800';
        }
        
        // Update mood display
        const moodDisplay = document.querySelector('.current-mood');
        if (moodDisplay) {
            moodDisplay.className = `flex items-center justify-center p-6 ${moodColor} rounded-lg current-mood`;
            moodDisplay.innerHTML = `
                <span class="text-5xl mr-3">${moodEmoji}</span>
                <span class="text-xl font-bold">${moodText}</span>
            `;
        }
        
        // Update progress bars
        const positifProgressElement = document.querySelector('.positif-progress');
        const netralProgressElement = document.querySelector('.netral-progress');
        const negatifProgressElement = document.querySelector('.negatif-progress');
        
        if (positifProgressElement) positifProgressElement.style.width = `${positifPercent}%`;
        if (netralProgressElement) netralProgressElement.style.width = `${netralPercent}%`;
        if (negatifProgressElement) negatifProgressElement.style.width = `${negatifPercent}%`;
        
        // Update count and percentage text
        const positifCountElement = document.querySelector('.positif-count');
        const netralCountElement = document.querySelector('.netral-count');
        const negatifCountElement = document.querySelector('.negatif-count');
        
        if (positifCountElement) positifCountElement.textContent = `${positifCount} (${positifPercent}%)`;
        if (netralCountElement) netralCountElement.textContent = `${netralCount} (${netralPercent}%)`;
        if (negatifCountElement) negatifCountElement.textContent = `${negatifCount} (${negatifPercent}%)`;
    }
    
    // Check if session is active and set up polling if it is
    const isSessionActive = document.getElementById('session-status').getAttribute('data-active') === 'true';
    
    if (isSessionActive) {
        // Poll for updated mood data every 10 seconds
        setInterval(function() {
            fetch(`/mood/getData?session_id=${sessionId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update charts and summary with new data
                    updateCharts(data.data.mood_data);
                    updateMoodSummary(data.data.mood_data);
                    
                    // Update user mood data if available (for members)
                    if (data.data.user_mood_data) {
                        updateUserMoodData(data.data.user_mood_data);
                    }
                }
            })
            .catch(error => {
                console.error('Error fetching mood data:', error);
            });
        }, 10000);
    }
    
    // Function to update user mood data (for members)
    function updateUserMoodData(userMoodData) {
        if (!userMoodData) return;
        
        const userPositifCount = userMoodData.positif || 0;
        const userNetralCount = userMoodData.netral || 0;
        const userNegatifCount = userMoodData.negatif || 0;
        
        const userTotal = userPositifCount + userNetralCount + userNegatifCount;
        
        // Determine user mood
        let userMoodText = 'Netral';
        let userMoodColor = 'bg-blue-100 text-blue-800';
        
        if (userPositifCount > userNegatifCount && userPositifCount > userNetralCount) {
            userMoodText = 'Positif';
            userMoodColor = 'bg-green-100 text-green-800';
        } else if (userNegatifCount > userPositifCount && userNegatifCount > userNetralCount) {
            userMoodText = 'Negatif';
            userMoodColor = 'bg-red-100 text-red-800';
        }
        
        // Update user mood display
        const userMoodDisplay = document.querySelector('.personal-mood-status');
        if (userMoodDisplay) {
            userMoodDisplay.className = `p-4 ${userMoodColor} rounded-lg mb-3 personal-mood-status`;
            userMoodDisplay.textContent = userMoodText;
        }
        
        // Update user mood counts
        const userPositifCountElement = document.querySelector('.user-positif-count');
        const userNetralCountElement = document.querySelector('.user-netral-count');
        const userNegatifCountElement = document.querySelector('.user-negatif-count');
        
        if (userPositifCountElement) userPositifCountElement.textContent = userPositifCount;
        if (userNetralCountElement) userNetralCountElement.textContent = userNetralCount;
        if (userNegatifCountElement) userNegatifCountElement.textContent = userNegatifCount;
    }
});
