<?php
// Definisikan variabel untuk layout
$css_file = 'mood';
$js_file = 'mood';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-8">
    <div class="flex items-center mb-6">
        <a href="/dashboard" class="text-gray-600 hover:text-gray-900 mr-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
        </a>
        <h1 class="text-2xl font-bold"><?= $title ?></h1>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Mood Summary -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold">Ringkasan Emosi</h2>
                </div>
                
                <div class="p-6">
                    <!-- Loading Indicator -->
                    <div id="loading-indicator" class="text-center py-2 mb-4 hidden">
                        <div class="inline-block animate-spin rounded-full h-6 w-6 border-t-2 border-b-2 border-[#f57c00]"></div>
                        <p class="text-sm text-gray-600 mt-1">Memperbarui data...</p>
                    </div>
                    
                    <!-- Current Mood -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Suasana Diskusi</h3>
                        <?php 
                        $moodEmoji = '😐';
                        $moodColor = 'bg-blue-100 text-blue-800';
                        $moodText = 'Netral';
                        
                        if (isset($mood_data['summary'])) {
                            $positif = $mood_data['summary']['positif'] ?? 0;
                            $netral = $mood_data['summary']['netral'] ?? 0;
                            $negatif = $mood_data['summary']['negatif'] ?? 0;
                            
                            if ($positif > $negatif && $positif > $netral) {
                                $moodEmoji = '😊';
                                $moodColor = 'bg-green-100 text-green-800';
                                $moodText = 'Positif';
                            } elseif ($negatif > $positif && $negatif > $netral) {
                                $moodEmoji = '😟';
                                $moodColor = 'bg-red-100 text-red-800';
                                $moodText = 'Negatif';
                            }
                        }
                        ?>
                        
                        <div class="mood-indicator flex items-center justify-center p-6 <?= $moodColor ?> rounded-lg">
                            <span class="mood-emoji text-5xl mr-3"><?= $moodEmoji ?></span>
                            <span class="mood-text text-xl font-bold"><?= $moodText ?></span>
                        </div>
                    </div>
                    
                    <!-- Mood Stats -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Statistik Emosi</h3>
                        
                        <?php
                        $positifCount = $mood_data['summary']['positif'] ?? 0;
                        $netralCount = $mood_data['summary']['netral'] ?? 0;
                        $negatifCount = $mood_data['summary']['negatif'] ?? 0;
                        
                        $total = $positifCount + $netralCount + $negatifCount;
                        $positifPercent = $total > 0 ? round(($positifCount / $total) * 100) : 0;
                        $netralPercent = $total > 0 ? round(($netralCount / $total) * 100) : 0;
                        $negatifPercent = $total > 0 ? round(($negatifCount / $total) * 100) : 0;
                        ?>
                        
                        <!-- Positif -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-green-800">Positif</span>
                                <span id="positif-count" class="text-sm font-medium text-green-800"><?= $positifPercent ?> (%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="positif-bar" class="bg-green-500 h-2.5 rounded-full" style="width: <?= $positifPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Netral -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-blue-800">Netral</span>
                                <span id="netral-count" class="text-sm font-medium text-blue-800"><?= $netralPercent ?> (%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="netral-bar" class="bg-blue-500 h-2.5 rounded-full" style="width: <?= $netralPercent ?>%"></div>
                            </div>
                        </div>
                        
                        <!-- Negatif -->
                        <div>
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-sm font-medium text-red-800">Negatif</span>
                                <span id="negatif-count" class="text-sm font-medium text-red-800"><?= $negatifPercent ?> (%)</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div id="negatif-bar" class="bg-red-500 h-2.5 rounded-full" style="width: <?= $negatifPercent ?>%"></div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($role == 'member' && $user_mood_data): ?>
                    <!-- Personal Mood (Only for members) -->
                    <div class="mb-6">
                        <h3 class="text-sm font-bold text-gray-700 mb-2">Emosi Pribadi Anda</h3>
                        
                        <?php
                        $userPositifCount = $user_mood_data['positif'] ?? 0;
                        $userNetralCount = $user_mood_data['netral'] ?? 0;
                        $userNegatifCount = $user_mood_data['negatif'] ?? 0;
                        
                        $userTotal = $userPositifCount + $userNetralCount + $userNegatifCount;
                        $userPositifPercent = $userTotal > 0 ? round(($userPositifCount / $userTotal) * 100) : 0;
                        $userNetralPercent = $userTotal > 0 ? round(($userNetralCount / $userTotal) * 100) : 0;
                        $userNegatifPercent = $userTotal > 0 ? round(($userNegatifCount / $userTotal) * 100) : 0;
                        
                        $userMoodText = 'Netral';
                        $userMoodColor = 'bg-blue-100 text-blue-800';
                        
                        if ($userPositifCount > $userNegatifCount && $userPositifCount > $userNetralCount) {
                            $userMoodText = 'Positif';
                            $userMoodColor = 'bg-green-100 text-green-800';
                        } elseif ($userNegatifCount > $userPositifCount && $userNegatifCount > $userNetralCount) {
                            $userMoodText = 'Negatif';
                            $userMoodColor = 'bg-red-100 text-red-800';
                        }
                        ?>
                        
                        <div class="p-4 <?= $userMoodColor ?> rounded-lg mb-3">
                            <div class="text-center font-bold"><?= $userMoodText ?></div>
                        </div>
                        
                        <div class="grid grid-cols-3 gap-2 text-center">
                            <div class="bg-green-100 p-2 rounded">
                                <div class="text-lg font-bold text-green-800"><?= $userPositifCount ?></div>
                                <div class="text-xs text-green-800">Positif</div>
                            </div>
                            <div class="bg-blue-100 p-2 rounded">
                                <div class="text-lg font-bold text-blue-800"><?= $userNetralCount ?></div>
                                <div class="text-xs text-blue-800">Netral</div>
                            </div>
                            <div class="bg-red-100 p-2 rounded">
                                <div class="text-lg font-bold text-red-800"><?= $userNegatifCount ?></div>
                                <div class="text-xs text-red-800">Negatif</div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- Quick Actions -->
                    <div>
                        <a href="/chatroom?session_id=<?= $session['session_id'] ?>" class="block w-full bg-[#f57c00] hover:bg-[#e65100] text-white text-center font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition mb-2">
                            Kembali ke Diskusi
                        </a>
                        
                        <?php if ($session['end_time'] === null && ($role == 'leader' || $role == 'admin')): ?>
                        <form action="/discussions/end" method="POST">
                            <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white text-center font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition" onclick="return confirm('Yakin ingin mengakhiri diskusi ini?')">
                                Akhiri Diskusi
                            </button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Mood Chart -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold">Grafik Emosi</h2>
                </div>
                
                <div class="p-6">
                    <div class="mb-6">
                        <canvas id="moodChart" width="400" height="200"></canvas>
                    </div>
                    
                    <!-- Bagian pie chart dihapus untuk menghindari bug -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Data from PHP
        const moodData = <?= json_encode($mood_data) ?>;
        
        // Fungsi untuk memperbarui tampilan mood berdasarkan data
        function updateMoodDisplay(summary) {
            const moodContainer = document.querySelector('.mood-indicator');
            const moodEmoji = document.querySelector('.mood-emoji');
            const moodText = document.querySelector('.mood-text');
            
            if (!moodContainer || !moodEmoji || !moodText) return;
            
            // Tentukan mood dominan
            let dominantMood = 'netral';
            let dominantValue = summary.netral;
            
            if (summary.positif > dominantValue) {
                dominantMood = 'positif';
                dominantValue = summary.positif;
            }
            
            if (summary.negatif > dominantValue) {
                dominantMood = 'negatif';
                dominantValue = summary.negatif;
            }
            
            // Update emoji dan teks
            if (dominantMood === 'positif') {
                moodEmoji.textContent = '😊';
                moodText.textContent = 'Positif';
                moodContainer.className = 'mood-indicator bg-green-100 text-green-800 px-4 py-3 rounded-lg text-center';
            } else if (dominantMood === 'negatif') {
                moodEmoji.textContent = '😟';
                moodText.textContent = 'Negatif';
                moodContainer.className = 'mood-indicator bg-red-100 text-red-800 px-4 py-3 rounded-lg text-center';
            } else {
                moodEmoji.textContent = '😐';
                moodText.textContent = 'Netral';
                moodContainer.className = 'mood-indicator bg-blue-100 text-blue-800 px-4 py-3 rounded-lg text-center';
            }
        }
        
        // Line Chart - Emotion Trends
        const lineCtx = document.getElementById('moodChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: moodData.labels,
                datasets: moodData.datasets
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
                        text: 'Tren Emosi Selama Diskusi'
                    },
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
        
        // Pie Chart dihapus untuk menghindari bug
        
        // Refresh data every 10 seconds if session is active
        <?php if ($session['end_time'] === null): ?>
        setInterval(function() {
            // Tambahkan indikator loading jika diperlukan
            const loadingIndicator = document.getElementById('loading-indicator');
            if (loadingIndicator) loadingIndicator.style.display = 'block';
            
            fetch('/mood/getData?session_id=<?= $session['session_id'] ?>', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.statusText);
                }
                return response.json();
            })
            .then(data => {
                // Sembunyikan indikator loading
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                
                if (data.success) {
                    // Update line chart
                    lineChart.data.labels = data.data.labels;
                    lineChart.data.datasets = data.data.datasets;
                    lineChart.update();
                    
                    // Update mood display
                    updateMoodDisplay(data.data.summary);
                    
                    // Update statistics
                    const positifCount = data.data.summary.positif || 0;
                    const netralCount = data.data.summary.netral || 0;
                    const negatifCount = data.data.summary.negatif || 0;
                    
                    const total = positifCount + netralCount + negatifCount;
                    const positifPercent = total > 0 ? Math.round((positifCount / total) * 100) : 0;
                    const netralPercent = total > 0 ? Math.round((netralCount / total) * 100) : 0;
                    const negatifPercent = total > 0 ? Math.round((negatifCount / total) * 100) : 0;
                    
                    // Update progress bars
                    document.getElementById('positif-count').textContent = positifPercent + ' (%)';
                    document.getElementById('positif-bar').style.width = positifPercent + '%';
                    
                    document.getElementById('netral-count').textContent = netralPercent + ' (%)';
                    document.getElementById('netral-bar').style.width = netralPercent + '%';
                    
                    document.getElementById('negatif-count').textContent = negatifPercent + ' (%)';
                    document.getElementById('negatif-bar').style.width = negatifPercent + '%';
                    
                    // Kode pembaruan pie chart dihapus untuk menghindari bug
                }
            })
            .catch(error => {
                // Sembunyikan indikator loading
                if (loadingIndicator) loadingIndicator.style.display = 'none';
                console.error('Error fetching mood data:', error);
            });
        }, 10000);
        <?php endif; ?>
    });
</script>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
