/**
 * ChatCare - Dashboard JavaScript
 * 
 * Handles dashboard functionality and statistics
 */

document.addEventListener('DOMContentLoaded', function() {
    // Get dashboard statistics elements
    const statsChartElement = document.getElementById('statsChart');
    
    // Check if chart element exists
    if (statsChartElement) {
        // Get data from data attribute
        let statsData;
        try {
            statsData = JSON.parse(document.getElementById('dashboard-data').getAttribute('data-stats'));
        } catch (error) {
            console.error('Error parsing dashboard data:', error);
            return;
        }
        
        // Create bar chart for statistics
        const statsChart = new Chart(statsChartElement.getContext('2d'), {
            type: 'bar',
            data: {
                labels: statsData.labels || [],
                datasets: [{
                    label: 'Diskusi',
                    data: statsData.sessions || [],
                    backgroundColor: 'rgba(245, 124, 0, 0.6)',
                    borderColor: 'rgba(245, 124, 0, 1)',
                    borderWidth: 1
                }, {
                    label: 'Pesan',
                    data: statsData.messages || [],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Jumlah'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Tanggal'
                        }
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Aktivitas 7 Hari Terakhir',
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
                }
            }
        });
    }
    
    // Get emotion distribution chart element
    const emotionChartElement = document.getElementById('emotionChart');
    
    // Check if emotion chart element exists
    if (emotionChartElement) {
        // Get data from data attribute
        let emotionData;
        try {
            emotionData = JSON.parse(document.getElementById('emotion-data').getAttribute('data-emotions'));
        } catch (error) {
            console.error('Error parsing emotion data:', error);
            return;
        }
        
        // Create doughnut chart for emotion distribution
        const emotionChart = new Chart(emotionChartElement.getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Positif', 'Netral', 'Negatif'],
                datasets: [{
                    data: [
                        emotionData.positif || 0,
                        emotionData.netral || 0,
                        emotionData.negatif || 0
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
                        text: 'Distribusi Emosi Keseluruhan',
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
    
    // Handle quick action buttons
    const quickActionButtons = document.querySelectorAll('.quick-action-btn');
    
    quickActionButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const action = this.getAttribute('data-action');
            const url = this.getAttribute('href');
            
            if (action === 'new-discussion') {
                // Redirect to new discussion page
                window.location.href = url;
            } else if (action === 'view-reports') {
                // Redirect to reports page
                window.location.href = url;
            } else if (action === 'manage-users') {
                // Redirect to users management page
                window.location.href = url;
            }
        });
    });
    
    // Handle recent sessions click
    const sessionItems = document.querySelectorAll('.session-item');
    
    sessionItems.forEach(item => {
        item.addEventListener('click', function() {
            const sessionId = this.getAttribute('data-session-id');
            const sessionUrl = `/chatroom?session_id=${sessionId}`;
            
            // Redirect to chatroom
            window.location.href = sessionUrl;
        });
    });
    
    // Handle recent reports click
    const reportItems = document.querySelectorAll('.report-item');
    
    reportItems.forEach(item => {
        item.addEventListener('click', function() {
            const reportId = this.getAttribute('data-report-id');
            const reportUrl = `/reports/view?report_id=${reportId}`;
            
            // Redirect to report view
            window.location.href = reportUrl;
        });
    });
    
    // Handle search functionality
    const searchForm = document.getElementById('search-form');
    const searchInput = document.getElementById('search-input');
    const searchResults = document.getElementById('search-results');
    
    if (searchForm && searchInput) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const query = searchInput.value.trim();
            if (!query) return;
            
            // Disable form during search
            searchInput.disabled = true;
            
            // Show loading indicator
            searchResults.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Mencari...</p></div>';
            searchResults.classList.remove('hidden');
            
            // Perform search
            fetch(`/dashboard/search?q=${encodeURIComponent(query)}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Display search results
                    displaySearchResults(data.data);
                } else {
                    searchResults.innerHTML = '<div class="text-center py-4">Terjadi kesalahan saat mencari. Silakan coba lagi.</div>';
                }
            })
            .catch(error => {
                console.error('Error searching:', error);
                searchResults.innerHTML = '<div class="text-center py-4">Terjadi kesalahan saat mencari. Silakan coba lagi.</div>';
            })
            .finally(() => {
                // Re-enable form
                searchInput.disabled = false;
                searchInput.focus();
            });
        });
        
        // Function to display search results
        function displaySearchResults(results) {
            if (!results || (results.sessions.length === 0 && results.users.length === 0)) {
                searchResults.innerHTML = '<div class="text-center py-4">Tidak ada hasil yang ditemukan.</div>';
                return;
            }
            
            let html = '<div class="p-4">';
            
            // Display session results
            if (results.sessions.length > 0) {
                html += '<h3 class="text-lg font-bold mb-2">Diskusi</h3>';
                html += '<ul class="mb-4">';
                
                results.sessions.forEach(session => {
                    html += `
                        <li class="mb-2">
                            <a href="/chatroom?session_id=${session.session_id}" class="block p-3 bg-white rounded-lg shadow-sm hover:bg-gray-50">
                                <div class="font-medium">${session.title || 'Diskusi #' + session.session_id}</div>
                                <div class="text-sm text-gray-600">
                                    <span class="mr-3">Peserta: ${session.participants}</span>
                                    <span>Tanggal: ${new Date(session.start_time).toLocaleDateString()}</span>
                                </div>
                            </a>
                        </li>
                    `;
                });
                
                html += '</ul>';
            }
            
            // Display user results (admin only)
            if (results.users && results.users.length > 0) {
                html += '<h3 class="text-lg font-bold mb-2">Pengguna</h3>';
                html += '<ul class="mb-4">';
                
                results.users.forEach(user => {
                    html += `
                        <li class="mb-2">
                            <a href="/users/edit?user_id=${user.user_id}" class="block p-3 bg-white rounded-lg shadow-sm hover:bg-gray-50">
                                <div class="font-medium">${user.username}</div>
                                <div class="text-sm text-gray-600">
                                    <span>Peran: ${user.role}</span>
                                </div>
                            </a>
                        </li>
                    `;
                });
                
                html += '</ul>';
            }
            
            html += '</div>';
            
            searchResults.innerHTML = html;
        }
        
        // Close search results when clicking outside
        document.addEventListener('click', function(e) {
            if (!searchForm.contains(e.target) && !searchResults.contains(e.target)) {
                searchResults.classList.add('hidden');
            }
        });
    }
});
