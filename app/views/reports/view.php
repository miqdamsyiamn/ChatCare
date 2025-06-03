<?php
// Definisikan variabel untuk layout
$css_file = 'reports';
$js_file = 'reports';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="/reports" class="text-gray-600 hover:text-gray-900 mr-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="ml-1">Kembali</span>
        </a>
        <h1 class="text-2xl font-bold ml-4"><?= $title ?></h1>
    </div>
    
    <div class="grid-layout gap-6">
        <!-- Report Content -->
        <div class="col-span-12 lg:col-span-8">
            <div class="report-content">
                <div class="report-header flex justify-between items-center">
                    <h2 class="report-title">Ringkasan Diskusi</h2>
                    <div class="text-sm text-gray-500">
                        Dibuat: <?= date('d M Y', strtotime($report['generated_at'])) ?>
                    </div>
                </div>
                
                <div class="report-body">
                    <div class="summary-content prose max-w-none bg-white rounded-lg shadow-sm p-6">
                        <?php
                        // Parse the structured summary
                        $summary = $report['summary'];
                        
                        // Remove any "Percakapan yang dianalisis:" section to avoid showing raw messages
                        $summary = preg_replace('/Percakapan yang dianalisis:[\s\S]*$/i', '', $summary);
                        
                        // Process each section separately to thoroughly clean up formatting
                        
                        // Extract sections
                        $ringkasanMatch = preg_match('/Ringkasan Percakapan:\s*([\s\S]*?)(?=\n\s*Poin-poin Penting:|$)/i', $summary, $ringkasanMatches);
                        $poinMatch = preg_match('/Poin-poin Penting:\s*([\s\S]*?)(?=\n\s*Suasana Diskusi:|$)/i', $summary, $poinMatches);
                        $suasanaMatch = preg_match('/Suasana Diskusi:\s*([\s\S]*?)(?=\n\s*$|$)/i', $summary, $suasanaMatches);
                        
                        // Function to clean text from asterisks and formatting
                        function cleanText($text) {
                            // Remove asterisks, bullet points, and other formatting markers
                            $text = preg_replace('/\*\*([^*]*)\*\*/', '$1', $text); // Remove **text**
                            $text = preg_replace('/^\s*[\*\-•]\s*/m', '', $text); // Remove bullet points
                            $text = preg_replace('/^\s*\d+\.\s*/m', '', $text); // Remove numbered lists
                            
                            // Remove any remaining special formatting and extra line breaks
                            $text = preg_replace('/\n{2,}/', "\n", $text); // Replace multiple line breaks with single
                            $text = trim($text); // Trim whitespace
                            
                            return $text;
                        }
                        
                        // Ringkasan Percakapan
                        echo '<div class="mb-6">';
                        echo '<h3 class="text-xl font-bold text-gray-800 mb-3">Ringkasan Percakapan</h3>';
                        if ($ringkasanMatch && !empty($ringkasanMatches[1])) {
                            $cleanRingkasan = cleanText($ringkasanMatches[1]);
                            echo '<div class="text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($cleanRingkasan)) . '</div>';
                        } else {
                            echo '<div class="text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars(cleanText($summary))) . '</div>';
                        }
                        echo '</div>';
                        
                        // Poin-poin Penting
                        if ($poinMatch && !empty($poinMatches[1])) {
                            echo '<div class="mb-6">';
                            echo '<h3 class="text-xl font-bold text-gray-800 mb-3">Poin-poin Penting</h3>';
                            $cleanPoin = cleanText($poinMatches[1]);
                            echo '<div class="text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($cleanPoin)) . '</div>';
                            echo '</div>';
                        }
                        
                        // Suasana Diskusi
                        if ($suasanaMatch && !empty($suasanaMatches[1])) {
                            echo '<div class="p-4 rounded-lg bg-gray-50 border border-gray-200">';
                            echo '<h3 class="text-xl font-bold text-gray-800 mb-2">Suasana Diskusi</h3>';
                            $cleanSuasana = cleanText($suasanaMatches[1]);
                            echo '<div class="text-gray-700 leading-relaxed">' . nl2br(htmlspecialchars($cleanSuasana)) . '</div>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Report Info -->
        <div class="col-span-12 lg:col-span-4">
            <div class="report-sidebar sticky top-4">
                <div class="report-header">
                    <h2 class="report-title">Informasi Laporan</h2>
                </div>
                
                <div class="report-body">
                    <div class="report-info-item">
                        <div class="report-info-label">Diskusi</div>
                        <div class="report-info-value font-medium">Diskusi #<?= $report['session_id'] ?></div>
                    </div>
                    
                    <div class="report-info-item mt-4">
                        <div class="report-info-label">Waktu Diskusi</div>
                        <div class="flex items-center space-x-2 mt-1">
                            <div class="bg-gray-100 px-3 py-1 rounded-md text-sm">
                                <?= date('d M Y H:i', strtotime($session['start_time'])) ?>
                            </div>
                            <span class="text-gray-400">→</span>
                            <div class="bg-gray-100 px-3 py-1 rounded-md text-sm">
                                <?= date('d M Y H:i', strtotime($session['end_time'])) ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="report-info-item mt-4">
                        <div class="report-info-label">Durasi Diskusi</div>
                        <div class="report-info-value">
                            <?php
                            $start = new DateTime($session['start_time']);
                            $end = new DateTime($session['end_time']);
                            $interval = $start->diff($end);
                            
                            $duration = '';
                            if ($interval->h > 0) {
                                $duration .= $interval->h . ' jam ';
                            }
                            if ($interval->i > 0) {
                                $duration .= $interval->i . ' menit';
                            }
                            echo $duration;
                            ?>
                        </div>
                    </div>
                    
                    <div class="report-info-item mt-4">
                        <div class="report-info-label">Laporan Dibuat</div>
                        <div class="report-info-value"><?= date('d M Y H:i', strtotime($report['generated_at'])) ?></div>
                    </div>
                    
                    <div class="report-actions mt-6 space-y-2">
                        <a href="/mood?session_id=<?= $session['session_id'] ?>" class="btn-primary w-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                            Lihat Analisis Emosi
                        </a>
                        
                        <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
                        <a href="/discussions/members?id=<?= $session['session_id'] ?>" class="btn-secondary w-full flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                            Lihat Peserta
                        </a>
                        <?php endif; ?>
                        
                        <!-- Botón de Riwayat Chat eliminado para proteger la privacidad de los usuarios -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
