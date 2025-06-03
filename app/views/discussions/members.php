<?php
// Definisikan variabel untuk layout
$css_file = 'discussions';
$js_file = 'discussions';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="/discussions" class="text-gray-600 hover:text-gray-900 mr-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="ml-1">Kembali</span>
        </a>
        <h1 class="text-2xl font-bold ml-4">Peserta Diskusi</h1>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-12 gap-6">
        <!-- Informasi Diskusi -->
        <div class="md:col-span-4">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 px-6 py-4 border-b">
                    <h2 class="text-xl font-semibold">Informasi Diskusi</h2>
                </div>
                <div class="p-6">
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-1">Judul</h3>
                        <p class="text-gray-900"><?= $session_title ?></p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-1">Deskripsi</h3>
                        <p class="text-gray-900"><?= $session_description ?></p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-1">Mulai</h3>
                        <p class="text-gray-900"><?= date('d M Y H:i', strtotime($session['start_time'])) ?></p>
                    </div>
                    <div class="mb-4">
                        <h3 class="text-sm font-bold text-gray-700 mb-1">Status</h3>
                        <?php if ($session['end_time'] === null): ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                            Aktif
                        </span>
                        <?php else: ?>
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                            Selesai
                        </span>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($session['end_time'] === null): ?>
                    <div class="mt-6">
                        <form action="/discussions/start" method="POST">
                            <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                            <button type="submit" class="w-full bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                                Masuk ke Diskusi
                            </button>
                        </form>
                    </div>
                    
                    <div class="mt-3">
                        <form action="/discussions/end" method="POST">
                            <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                            <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition" onclick="return confirm('Yakin ingin mengakhiri diskusi ini?')">
                                Akhiri Diskusi
                            </button>
                        </form>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Daftar Peserta -->
        <div class="md:col-span-8">
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="bg-gray-100 px-6 py-4 border-b flex justify-between items-center">
                    <h2 class="text-xl font-semibold">Daftar Peserta</h2>
                    <?php if ($session['end_time'] === null): ?>
                    <button type="button" id="addMemberBtn" class="bg-[#f57c00] hover:bg-[#e65100] text-white text-sm font-bold py-1 px-3 rounded focus:outline-none focus:shadow-outline transition">
                        Tambah Peserta
                    </button>
                    <?php endif; ?>
                </div>
                <div class="p-6">
                    <?php if (empty($participants)): ?>
                    <p class="text-gray-500 text-center py-4">Belum ada peserta</p>
                    <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <?php if ($session['end_time'] === null): ?>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    <?php endif; ?>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($participants as $participant): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900"><?= $participant['username'] ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php if ($participant['role'] == 'admin'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-purple-100 text-purple-800">
                                            Admin
                                        </span>
                                        <?php elseif ($participant['role'] == 'leader'): ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                            Leader
                                        </span>
                                        <?php else: ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                            Member
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <?php if ($session['end_time'] === null): ?>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <?php if ($participant['user_id'] != $_SESSION['user_id']): ?>
                                        <form action="/discussions/remove-member" method="POST" class="inline-block">
                                            <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                                            <input type="hidden" name="user_id" value="<?= $participant['user_id'] ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900" onclick="return confirm('Yakin ingin menghapus peserta ini?')">Hapus</button>
                                        </form>
                                        <?php endif; ?>
                                    </td>
                                    <?php endif; ?>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Peserta -->
<?php if ($session['end_time'] === null): ?>
<div id="addMemberModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-lg shadow-lg max-w-md w-full">
        <div class="flex justify-between items-center px-6 py-4 border-b">
            <h3 class="text-lg font-semibold">Tambah Peserta</h3>
            <button type="button" id="closeModalBtn" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <div class="p-6">
            <form action="/discussions/add-member" method="POST">
                <input type="hidden" name="session_id" value="<?= $session['session_id'] ?>">
                
                <div class="mb-4">
                    <label for="user_id" class="block text-gray-700 text-sm font-bold mb-2">Pilih Pengguna</label>
                    <select name="user_id" id="user_id" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" required>
                        <option value="">Pilih Pengguna</option>
                        <?php foreach ($all_users as $user): ?>
                        <?php
                        // Skip if user is already a participant
                        $isParticipant = false;
                        foreach ($participants as $participant) {
                            if ($participant['user_id'] == $user['user_id']) {
                                $isParticipant = true;
                                break;
                            }
                        }
                        if ($isParticipant) continue;
                        
                        // Jika user yang login adalah leader, sembunyikan opsi admin
                        if ($_SESSION['role'] == 'leader' && $user['role'] == 'admin') continue;
                        ?>
                        <option value="<?= $user['user_id'] ?>"><?= $user['username'] ?> (<?= ucfirst($user['role']) ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="flex justify-end">
                    <button type="button" id="cancelBtn" class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition mr-2">
                        Batal
                    </button>
                    <button type="submit" class="bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                        Tambah
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Modal functionality
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addMemberModal');
        const openBtn = document.getElementById('addMemberBtn');
        const closeBtn = document.getElementById('closeModalBtn');
        const cancelBtn = document.getElementById('cancelBtn');
        
        openBtn.addEventListener('click', function() {
            modal.classList.remove('hidden');
        });
        
        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        cancelBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });
        
        // Close modal when clicking outside
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.classList.add('hidden');
            }
        });
    });
</script>
<?php endif; ?>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Load layout
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
