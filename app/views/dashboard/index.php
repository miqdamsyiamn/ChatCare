<?php
// Definisikan variabel untuk layout
$css_file = 'dashboard';
$js_file = 'dashboard';

// Mulai output buffering
ob_start();
?>

<!-- Konten Dashboard -->
<div class="container mx-auto px-6 py-8">
    <!-- Dashboard Header -->
    <div class="flex flex-col md:flex-row justify-between items-center mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-800 mb-2"><?= $title ?></h1>
            <p class="text-gray-600">Selamat datang kembali, <span class="font-medium"><?= $_SESSION['name'] ?></span></p>
        </div>
        <div class="mt-4 md:mt-0 bg-white/80 backdrop-blur-sm rounded-lg shadow-md px-4 py-2 border border-gray-100">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-500 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span class="text-gray-700"><?= date('l, d F Y') ?></span>
            </div>
        </div>
    </div>
    
    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
        <!-- Card: Diskusi Aktif -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 transition-all duration-300 hover:shadow-lg hover:border-green-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Diskusi Aktif</h2>
                <div class="p-3 bg-green-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-bold text-green-600"><?= $active_sessions ?></p>
                <p class="text-gray-500 text-sm">diskusi</p>
            </div>
            <p class="text-gray-600 mt-2 text-sm">Diskusi yang sedang berlangsung</p>
        </div>
        
        <!-- Card: Total Diskusi -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 transition-all duration-300 hover:shadow-lg hover:border-orange-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Total Diskusi</h2>
                <div class="p-3 bg-orange-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-[#f57c00]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-bold text-[#f57c00]"><?= count($sessions) ?></p>
                <p class="text-gray-500 text-sm">diskusi</p>
            </div>
            <p class="text-gray-600 mt-2 text-sm">Jumlah seluruh diskusi</p>
        </div>
        
        <?php if (isset($total_users)): ?>
        <!-- Card: Total Pengguna (Admin only) -->
        <div class="bg-white rounded-xl shadow-md p-6 border border-gray-100 transition-all duration-300 hover:shadow-lg hover:border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-800">Total Pengguna</h2>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
            <div class="flex items-baseline space-x-2">
                <p class="text-3xl font-bold text-blue-600"><?= $total_users ?></p>
                <p class="text-gray-500 text-sm">pengguna</p>
            </div>
            <p class="text-gray-600 mt-2 text-sm">Jumlah pengguna terdaftar</p>
        </div>
        <?php endif; ?>
    </div>
    
    <!-- Diskusi Terbaru Section -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-8">
        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <div class="flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f57c00] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                </svg>
                <h2 class="text-xl font-bold text-gray-800">Diskusi Terbaru</h2>
            </div>
            <?php if (!empty($sessions) && count($sessions) > 5): ?>
            <a href="/discussions" class="flex items-center text-[#f57c00] hover:text-[#e65100] text-sm font-medium transition-colors duration-300">
                <span>Lihat semua</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </a>
            <?php endif; ?>
        </div>
        
        <div class="p-6">
            <?php if (empty($sessions)): ?>
            <div class="flex flex-col items-center justify-center py-12 bg-gray-50/50 rounded-lg border border-dashed border-gray-300">
                <div class="bg-white p-4 rounded-full shadow-md mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-2">Belum Ada Diskusi</h3>
                <p class="text-gray-500 text-center max-w-md mb-6">Belum ada diskusi yang tersedia saat ini. Mulai diskusi baru untuk memulai percakapan.</p>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'leader'): ?>
                <a href="/discussions/create" class="px-6 py-3 bg-gradient-to-r from-[#ff9800] to-[#f57c00] text-white rounded-lg shadow-md hover:shadow-lg transition-all duration-300 flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Buat Diskusi Baru
                </a>
                <?php endif; ?>
            </div>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 rounded-lg overflow-hidden">
                    <thead>
                        <tr class="bg-gray-50">
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Mulai</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach (array_slice($sessions, 0, 5) as $session): ?>
                        <?php $participants = json_decode($session['participants'], true); ?>
                        <tr class="hover:bg-gray-50 transition-all duration-200">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 mr-3">
                                        <div class="w-10 h-10 rounded-full bg-[#fff3e0] flex items-center justify-center">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f57c00]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                            </svg>
                                        </div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900"><?= $participants['title'] ?></div>
                                        <div class="text-sm text-gray-500"><?= substr($participants['description'], 0, 50) . (strlen($participants['description']) > 50 ? '...' : '') ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm text-gray-900"><?= date('d M Y', strtotime($session['start_time'])) ?></div>
                                <div class="text-sm text-gray-500 flex items-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <?= date('H:i', strtotime($session['start_time'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <?php if ($session['end_time'] === null): ?>
                                <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                    <span class="w-2 h-2 bg-green-500 rounded-full mr-1"></span>
                                    Aktif
                                </span>
                                <?php else: ?>
                                <span class="px-3 py-1 inline-flex items-center text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                    <span class="w-2 h-2 bg-gray-500 rounded-full mr-1"></span>
                                    Selesai
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 text-sm font-medium">
                                <div class="flex space-x-2">
                                    <?php if ($session['end_time'] === null): ?>
                                    <a href="/chatroom?session_id=<?= $session['session_id'] ?>" class="px-3 py-1.5 bg-gradient-to-r from-[#ff9800] to-[#f57c00] text-white rounded-md hover:shadow-md transition-all duration-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                        </svg>
                                        Gabung
                                    </a>
                                    <?php else: ?>
                                    <a href="/reports?session_id=<?= $session['session_id'] ?>" class="px-3 py-1.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-md hover:shadow-md transition-all duration-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                        Laporan
                                    </a>
                                    <?php endif; ?>
                                    
                                    <a href="/mood?session_id=<?= $session['session_id'] ?>" class="px-3 py-1.5 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-md hover:shadow-md transition-all duration-300 flex items-center">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Emosi
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($_SESSION['role'] == 'leader' || $_SESSION['role'] == 'admin'): ?>
    <!-- Quick Actions Section -->
    <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-xl border border-gray-200 p-6 mb-8">
        <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f57c00] mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
            Aksi Cepat
        </h3>
        <div class="flex flex-wrap gap-4">
            <a href="/discussions/create" class="flex items-center bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-5 rounded-lg shadow-sm hover:shadow transition-all duration-300 border border-gray-200 group">
                <div class="w-10 h-10 rounded-full bg-[#fff3e0] flex items-center justify-center mr-3 group-hover:bg-[#ffecb3] transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-[#f57c00]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                </div>
                <div>
                    <span class="block">Buat Diskusi Baru</span>
                    <span class="text-xs text-gray-500">Mulai sesi diskusi baru</span>
                </div>
            </a>
            
            <a href="/reports" class="flex items-center bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-5 rounded-lg shadow-sm hover:shadow transition-all duration-300 border border-gray-200 group">
                <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center mr-3 group-hover:bg-blue-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <div>
                    <span class="block">Lihat Laporan</span>
                    <span class="text-xs text-gray-500">Analisis hasil diskusi</span>
                </div>
            </a>
            
            <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="/users" class="flex items-center bg-white hover:bg-gray-50 text-gray-800 font-medium py-3 px-5 rounded-lg shadow-sm hover:shadow transition-all duration-300 border border-gray-200 group">
                <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center mr-3 group-hover:bg-purple-100 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <div>
                    <span class="block">Kelola Pengguna</span>
                    <span class="text-xs text-gray-500">Atur akun pengguna</span>
                </div>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>
    
    <!-- Profile Card -->
    <div class="bg-white rounded-xl shadow-md overflow-hidden border border-gray-100 mb-8">
        <div class="md:flex">
            <div class="md:flex-shrink-0 bg-gradient-to-br from-[#ff9800] to-[#e65100] p-6 text-white flex items-center justify-center md:w-48">
                <div class="text-center">
                    <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-sm flex items-center justify-center mx-auto mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="font-bold text-xl"><?= $_SESSION['name'] ?></h3>
                    <p class="text-white/80 capitalize"><?= $_SESSION['role'] ?></p>
                </div>
            </div>
            <div class="p-6 md:p-8 md:flex-1">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Informasi Profil</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Email</p>
                        <p class="font-medium"><?= $_SESSION['email'] ?></p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Bergabung Sejak</p>
                        <p class="font-medium"><?= date('d F Y', strtotime($_SESSION['created_at'] ?? date('Y-m-d'))) ?></p>
                    </div>
                </div>
                <div class="mt-6 flex space-x-3">
                    <a href="/profile" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-800 rounded-md transition-colors duration-300 text-sm font-medium flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                        Edit Profil
                    </a>
                    <a href="/auth/logout" class="px-4 py-2 bg-red-50 hover:bg-red-100 text-red-600 rounded-md transition-colors duration-300 text-sm font-medium flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Logout
                    </a>
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
