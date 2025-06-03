<?php
// Definisikan variabel untuk layout
$css_file = 'discussions';
$js_file = 'discussions';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold"><?= $title ?></h1>
        <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
        <a href="/discussions/create" class="bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-2 px-6 rounded-lg transition-colors flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Buat Diskusi Baru
        </a>
        <?php endif; ?>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Selesai</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($sessions)): ?>
                    <tr>
                        <td colspan="5" class="px-6 py-4 text-center text-gray-500">Belum ada diskusi</td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($sessions as $session): ?>
                    <?php $participants = json_decode($session['participants'], true); ?>
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900"><?= $participants['title'] ?></div>
                            <div class="text-sm text-gray-500"><?= substr($participants['description'], 0, 50) . (strlen($participants['description']) > 50 ? '...' : '') ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($session['start_time'])) ?></div>
                            <div class="text-sm text-gray-500"><?= date('H:i', strtotime($session['start_time'])) ?></div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($session['end_time'] === null): ?>
                            <div class="text-sm text-gray-500">-</div>
                            <?php else: ?>
                            <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($session['end_time'])) ?></div>
                            <div class="text-sm text-gray-500"><?= date('H:i', strtotime($session['end_time'])) ?></div>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <?php if ($session['end_time'] === null): ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Aktif
                            </span>
                            <?php else: ?>
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Selesai
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
                            <a href="/discussions/members?id=<?= $session['session_id'] ?>" class="text-blue-600 hover:text-blue-900 mr-2">Peserta</a>
                            <?php endif; ?>
                            
                            <?php if ($session['end_time'] === null): ?>
                            <a href="/chatroom?session_id=<?= $session['session_id'] ?>" class="text-[#f57c00] hover:text-[#e65100] mr-2">Gabung</a>
                            
                            <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
                            <form action="/discussions/end" method="POST" class="inline-block">
                                <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                                <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin mengakhiri diskusi ini?')">Akhiri</button>
                            </form>
                            <?php endif; ?>
                            
                            <?php else: ?>
                            <a href="/reports?session_id=<?= $session['session_id'] ?>" class="px-3 py-1 bg-blue-500 text-white rounded-md hover:bg-blue-600 transition-colors inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Laporan
                            </a>
                            <?php endif; ?>
                            
                            <a href="/mood?session_id=<?= $session['session_id'] ?>" class="px-3 py-1 bg-indigo-500 text-white rounded-md hover:bg-indigo-600 transition-colors ml-2 inline-flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                                </svg>
                                Emosi
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
