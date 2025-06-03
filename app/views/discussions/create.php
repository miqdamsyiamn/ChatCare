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
        <h1 class="text-2xl font-bold ml-4">Buat Diskusi Baru</h1>
    </div>
    
    <div class="bg-white rounded-lg shadow-md overflow-hidden max-w-4xl mx-auto">
        <div class="p-6">
            <form action="/discussions/store" method="POST">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 text-sm font-bold mb-2">Judul Diskusi</label>
                    <input type="text" name="title" id="title" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= !empty($title_err) ? 'border-red-500' : '' ?>" value="<?= $discussion_title ?? '' ?>" required>
                    <?php if (!empty($title_err)): ?>
                    <p class="text-red-500 text-xs italic"><?= $title_err ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-4">
                    <label for="description" class="block text-gray-700 text-sm font-bold mb-2">Deskripsi</label>
                    <textarea name="description" id="description" rows="4" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline <?= !empty($description_err) ? 'border-red-500' : '' ?>" required><?= $description ?? '' ?></textarea>
                    <?php if (!empty($description_err)): ?>
                    <p class="text-red-500 text-xs italic"><?= $description_err ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Peserta</label>
                    <div class="bg-gray-50 p-4 rounded border">
                        <?php if (empty($users)): ?>
                        <p class="text-gray-500 text-center py-4">Belum ada pengguna yang tersedia</p>
                        <?php else: ?>
                        <div class="mb-4">
                            <div class="relative">
                                <input type="text" id="userSearch" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline pl-10" placeholder="Cari username...">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-2">
                            <div class="flex justify-between items-center">
                                <span class="text-sm font-medium text-gray-700">Peserta Terpilih</span>
                                <button type="button" id="clearAllBtn" class="text-sm text-red-600 hover:text-red-800">Hapus Semua</button>
                            </div>
                        </div>
                        
                        <div id="selectedUsers" class="mb-4 max-h-40 overflow-y-auto p-2 border rounded bg-white">
                            <p id="noSelectedUsers" class="text-gray-500 text-center py-2 text-sm">Belum ada peserta yang dipilih</p>
                            <div id="selectedUsersList"></div>
                        </div>
                        
                        <div class="mb-2">
                            <span class="text-sm font-medium text-gray-700">Hasil Pencarian</span>
                        </div>
                        
                        <div id="searchResults" class="max-h-40 overflow-y-auto border rounded bg-white">
                            <p id="noSearchResults" class="text-gray-500 text-center py-4 text-sm">Ketik untuk mencari pengguna</p>
                            <div id="userList" class="hidden"></div>
                        </div>
                        
                        <!-- Hidden input to store selected user IDs -->
                        <div id="hiddenParticipants"></div>
                        <?php endif; ?>
                    </div>
                    <?php if (!empty($participants_err)): ?>
                    <p class="text-red-500 text-xs italic mt-1"><?= $participants_err ?></p>
                    <?php endif; ?>
                </div>
                
                <script>
                document.addEventListener('DOMContentLoaded', function() {
                    // All available users
                    const users = [
                        <?php foreach ($users as $user): ?>
                        <?php if ($user['user_id'] != $_SESSION['user_id']): ?>
                        {
                            id: '<?= $user['user_id'] ?>',
                            username: '<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>',
                            role: '<?= $user['role'] ?>'
                        },
                        <?php endif; ?>
                        <?php endforeach; ?>
                    ];
                    
                    const selectedUsers = new Map();
                    const userSearch = document.getElementById('userSearch');
                    const searchResults = document.getElementById('searchResults');
                    const userList = document.getElementById('userList');
                    const noSearchResults = document.getElementById('noSearchResults');
                    const selectedUsersList = document.getElementById('selectedUsersList');
                    const noSelectedUsers = document.getElementById('noSelectedUsers');
                    const hiddenParticipants = document.getElementById('hiddenParticipants');
                    const clearAllBtn = document.getElementById('clearAllBtn');
                    
                    // Check if all required elements exist
                    if (!userSearch || !searchResults || !userList || !noSearchResults || 
                        !selectedUsersList || !noSelectedUsers || !hiddenParticipants || !clearAllBtn) {
                        console.error('Some required elements are missing from the DOM');
                        return;
                    }
                    
                    // Initialize with any pre-selected participants
                    <?php if (isset($participants) && !empty($participants)): ?>
                    <?php foreach ($participants as $participant_id): ?>
                    <?php 
                    foreach ($users as $user) {
                        if ($user['user_id'] == $participant_id) {
                            $escapedUsername = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
                            $escapedUsername = str_replace("'", "\\'", $escapedUsername);
                            echo "addSelectedUser('{$user['user_id']}', '{$escapedUsername}', '{$user['role']}');";
                        }
                    }
                    ?>
                    <?php endforeach; ?>
                    <?php endif; ?>
                    
                    // Search functionality
                    userSearch.addEventListener('input', function() {
                        const searchTerm = this.value.toLowerCase().trim();
                        
                        if (searchTerm === '') {
                            userList.classList.add('hidden');
                            noSearchResults.classList.remove('hidden');
                            return;
                        }
                        
                        // Filter users based on search term
                        const filteredUsers = users.filter(user => {
                            // Jika user yang login adalah leader, sembunyikan admin dari hasil pencarian
                            if ('<?= $_SESSION['role'] ?>' === 'leader' && user.role === 'admin') {
                                return false;
                            }
                            try {
                                return user && user.username && 
                                       user.username.toLowerCase().includes(searchTerm) && 
                                       !selectedUsers.has(user.id);
                            } catch (e) {
                                console.error('Error filtering user:', e);
                                return false;
                            }
                        });
                        
                        if (filteredUsers.length === 0) {
                            userList.classList.add('hidden');
                            noSearchResults.textContent = 'Tidak ada hasil yang ditemukan';
                            noSearchResults.classList.remove('hidden');
                            return;
                        }
                        
                        // Show results
                        userList.innerHTML = '';
                        filteredUsers.forEach(user => {
                            const userItem = document.createElement('div');
                            userItem.className = 'p-2 hover:bg-gray-50 cursor-pointer border-b last:border-b-0 flex items-center justify-between';
                            
                            let roleClass = '';
                            let roleName = '';
                            
                            if (user.role === 'admin') {
                                roleClass = 'bg-purple-100 text-purple-800';
                                roleName = 'Admin';
                            } else if (user.role === 'leader') {
                                roleClass = 'bg-blue-100 text-blue-800';
                                roleName = 'Leader';
                            } else {
                                roleClass = 'bg-green-100 text-green-800';
                                roleName = 'Member';
                            }
                            
                            userItem.innerHTML = `
                                <div>
                                    <span class="font-medium">${user.username}</span>
                                    <span class="text-xs px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleClass}">${roleName}</span>
                                </div>
                                <button type="button" class="text-[#f57c00] hover:text-[#e65100] text-sm font-medium">
                                    Tambah
                                </button>
                            `;
                            
                            userItem.addEventListener('click', function() {
                                addSelectedUser(user.id, user.username, user.role);
                                userSearch.value = '';
                                userList.classList.add('hidden');
                                noSearchResults.classList.remove('hidden');
                            });
                            
                            userList.appendChild(userItem);
                        });
                        
                        userList.classList.remove('hidden');
                        noSearchResults.classList.add('hidden');
                    });
                    
                    // Clear all selected users
                    clearAllBtn.addEventListener('click', function() {
                        selectedUsers.clear();
                        selectedUsersList.innerHTML = '';
                        hiddenParticipants.innerHTML = '';
                        noSelectedUsers.classList.remove('hidden');
                        updateUserList();
                    });
                    
                    // Add a selected user
                    function addSelectedUser(id, username, role) {
                        if (selectedUsers.has(id)) return;
                        
                        selectedUsers.set(id, { username, role });
                        
                        // Hide the no selected users message
                        noSelectedUsers.classList.add('hidden');
                        
                        // Create the user item
                        const userItem = document.createElement('div');
                        userItem.className = 'flex items-center justify-between p-2 mb-1 bg-gray-50 rounded';
                        userItem.dataset.id = id;
                        
                        let roleClass = '';
                        let roleName = '';
                        
                        if (role === 'admin') {
                            roleClass = 'bg-purple-100 text-purple-800';
                            roleName = 'Admin';
                        } else if (role === 'leader') {
                            roleClass = 'bg-blue-100 text-blue-800';
                            roleName = 'Leader';
                        } else {
                            roleClass = 'bg-green-100 text-green-800';
                            roleName = 'Member';
                        }
                        
                        userItem.innerHTML = `
                            <div>
                                <span class="font-medium">${username}</span>
                                <span class="text-xs px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${roleClass}">${roleName}</span>
                            </div>
                            <button type="button" class="text-red-600 hover:text-red-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        `;
                        
                        // Add remove functionality
                        const removeBtn = userItem.querySelector('button');
                        removeBtn.addEventListener('click', function() {
                            selectedUsers.delete(id);
                            userItem.remove();
                            
                            // Show the no selected users message if no users are selected
                            if (selectedUsers.size === 0) {
                                noSelectedUsers.classList.remove('hidden');
                            }
                            
                            // Update the hidden input
                            updateHiddenInput();
                            updateUserList();
                        });
                        
                        selectedUsersList.appendChild(userItem);
                        
                        // Update the hidden input
                        updateHiddenInput();
                        updateUserList();
                    }
                    
                    // Update the hidden input with selected user IDs
                    function updateHiddenInput() {
                        hiddenParticipants.innerHTML = '';
                        
                        selectedUsers.forEach((user, id) => {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = 'participants[]';
                            input.value = id;
                            hiddenParticipants.appendChild(input);
                        });
                    }
                    
                    // Update the user list to exclude selected users
                    function updateUserList() {
                        if (userSearch.value.trim() !== '') {
                            userSearch.dispatchEvent(new Event('input'));
                        }
                    }
                });
                </script>
                
                <div class="flex items-center justify-between">
                    <button type="submit" class="bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline transition">
                        Buat Diskusi
                    </button>
                    <a href="/discussions" class="inline-block align-baseline font-bold text-sm text-gray-500 hover:text-gray-800">
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
