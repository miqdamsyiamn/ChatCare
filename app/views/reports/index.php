<?php
// Definisikan variabel untuk layout
$css_file = 'reports';
$js_file = 'reports';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6"><?= $title ?></h1>
    
    <div class="grid-layout gap-6">
        <!-- Daftar Laporan Diskusi -->
        <div class="col-span-12">
            <div class="report-content mb-6">
                <div class="report-header flex justify-between items-center">
                    <h2 class="report-title">Daftar Laporan Diskusi</h2>
                    <div class="flex items-center space-x-2">
                        <span class="text-sm text-gray-500"><?= count($reports) ?> laporan</span>
                    </div>
                </div>
                <div class="report-body">
                    <?php if (empty($reports)): ?>
                    <div class="flex flex-col items-center justify-center py-8">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <p class="text-gray-600 text-center">Belum ada laporan</p>
                        <?php if ($role == 'leader' || $role == 'admin'): ?>
                        <p class="text-gray-500 text-sm text-center mt-2">Buat laporan baru untuk diskusi yang telah selesai</p>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="report-table">
                            <thead>
                                <tr>
                                    <th>Diskusi</th>
                                    <th>Tanggal Diskusi</th>
                                    <th>Tanggal Laporan</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($reports as $report): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td>
                                        <div class="font-medium">Diskusi #<?= $report['session_id'] ?></div>
                                    </td>
                                    <td>
                                        <?php if (isset($report['start_time']) && $report['start_time']): ?>
                                            <div><?= date('d M Y', strtotime($report['start_time'])) ?></div>
                                            <div class="text-sm text-gray-500"><?= date('H:i', strtotime($report['start_time'])) ?></div>
                                        <?php else: ?>
                                            <div>Tanggal tidak tersedia</div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div><?= date('d M Y', strtotime($report['generated_at'])) ?></div>
                                        <div class="text-sm text-gray-500"><?= date('H:i', strtotime($report['generated_at'])) ?></div>
                                    </td>
                                    <td>
                                        <a href="/reports/view?id=<?= $report['report_id'] ?>" class="btn-primary inline-flex items-center justify-center px-4 py-2 transition-colors">
                                            <span>Lihat Laporan</span>
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                        </a>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Sección 'Buat Laporan Baru' eliminada ya que no se utiliza -->
    </div>
</div>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
