<?php
// Definisikan variabel untuk layout
$css_file = 'users';
$js_file = 'users';

// Mulai output buffering
ob_start();
?>

<div class="container mx-auto px-4 py-6">
    <div class="flex items-center mb-6">
        <a href="/users" class="text-gray-600 hover:text-gray-900 mr-2 flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 14.707a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 1.414L7.414 9H15a1 1 0 110 2H7.414l2.293 2.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            <span class="ml-1">Kembali</span>
        </a>
        <h1 class="text-2xl font-bold ml-4">Edit Pengguna</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-2xl mx-auto">
        <div class="p-6">
            <form action="/users/update" method="POST">
                <input type="hidden" name="user_id" value="<?= $user_id ?>">
                
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 text-sm font-bold mb-2">Username</label>
                    <input type="text" name="username" id="username" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= !empty($username_err) ? 'border-red-500' : '' ?>" value="<?= $username ?? '' ?>" required>
                    <?php if (!empty($username_err)): ?>
                    <p class="text-red-500 text-xs italic"><?= $username_err ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="password" class="block text-gray-700 text-sm font-bold mb-2">Password (Kosongkan jika tidak ingin mengubah)</label>
                    <input type="password" name="password" id="password" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= !empty($password_err) ? 'border-red-500' : '' ?>">
                    <?php if (!empty($password_err)): ?>
                    <p class="text-red-500 text-xs italic"><?= $password_err ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-6">
                    <label for="role" class="block text-gray-700 text-sm font-bold mb-2">Role</label>
                    <select name="role" id="role" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= !empty($role_err) ? 'border-red-500' : '' ?>" required>
                        <option value="">Pilih Role</option>
                        <option value="admin" <?= (isset($role) && $role == 'admin') ? 'selected' : '' ?>>Admin</option>
                        <option value="leader" <?= (isset($role) && $role == 'leader') ? 'selected' : '' ?>>Leader</option>
                        <option value="member" <?= (isset($role) && $role == 'member') ? 'selected' : '' ?>>Member</option>
                    </select>
                    <?php if (!empty($role_err)): ?>
                    <p class="text-red-500 text-xs italic"><?= $role_err ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                        Simpan Perubahan
                    </button>
                    <a href="/users" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
// Simpan output buffering ke variabel $content
$content = ob_get_clean();

// Sertakan layout utama
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
