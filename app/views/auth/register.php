<?php
// Define variables for the layout
$title = 'Daftar Akun';
$css_file = 'auth';
$js_file = 'auth';

// Add the animation scripts and notification styles
$extra_scripts = ['register-animation.js', 'notification-handler.js', 'enhanced-notifications.js'];
$extra_css = ['notifications.css'];

// Start output buffer to capture the content
ob_start();
ob_clean();
?>

<div class="flex min-h-screen bg-gradient-to-br from-[#fff3e0] to-[#ffecb3]">
    <div class="m-auto w-full max-w-4xl p-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
            <!-- Left side - Form -->
            <div class="w-full md:w-1/2 p-8 relative">
                <a href="/" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                <div class="mb-6 text-center">
                    <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
                    <p class="text-gray-600 mt-2">Bergabung dengan ChatCare</p>
                </div>
                
                <!-- Success notification -->                
                <?php if (isset($_SESSION['success_message'])): ?>
                <div class="notification notification-success mb-6 relative overflow-hidden" role="alert">
                    <div class="notification-icon success-icon">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="notification-message">
                        <span class="font-medium text-base"><?= $_SESSION['success_message'] ?></span>
                        <?php if (strpos($_SESSION['success_message'], 'Registrasi berhasil') !== false): ?>
                        <div class="text-sm mt-1 text-green-700">Selamat bergabung di ChatCare! Silakan login untuk melanjutkan.</div>
                        <?php endif; ?>
                    </div>
                    <div class="confetti-container"></div>
                    <button type="button" class="notification-close" onclick="this.parentElement.remove();">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
                <?php endif; ?>
                
                <!-- Error notification area for form-wide messages -->
                <?php if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($data['username_err']) || isset($data['email_err']) || isset($data['password_err']) || isset($data['confirm_password_err']))): ?>
                <div class="notification notification-error mb-4">
                    <div class="notification-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="notification-message">
                        <span class="font-medium">Mohon periksa kembali data yang dimasukkan</span>
                        <ul class="mt-1 ml-5 text-xs list-disc">
                            <?php if(isset($data['username_err']) && !empty($data['username_err'])): ?>
                                <li><?= htmlspecialchars($data['username_err']) ?></li>
                            <?php endif; ?>
                            <?php if(isset($data['email_err']) && !empty($data['email_err'])): ?>
                                <li><?= htmlspecialchars($data['email_err']) ?></li>
                            <?php endif; ?>
                            <?php if(isset($data['password_err']) && !empty($data['password_err'])): ?>
                                <li><?= htmlspecialchars($data['password_err']) ?></li>
                            <?php endif; ?>
                            <?php if(isset($data['confirm_password_err']) && !empty($data['confirm_password_err'])): ?>
                                <li><?= htmlspecialchars($data['confirm_password_err']) ?></li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                <?php endif; ?>
                
                <form action="/register" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <input type="text" id="username" name="username" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00] <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['username_err'])) ? 'input-error' : ''; ?>" value="<?= isset($data['username']) ? $data['username'] : ''; ?>" required>
                        <div class="field-error <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['username_err'])) ? 'active' : ''; ?>">
                            <span class="field-error-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span><?= isset($data['username_err']) ? htmlspecialchars($data['username_err']) : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00] <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['email_err'])) ? 'input-error' : ''; ?>" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required>
                        <div class="field-error <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['email_err'])) ? 'active' : ''; ?>">
                            <span class="field-error-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span><?= isset($data['email_err']) ? htmlspecialchars($data['email_err']) : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00] <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['password_err'])) ? 'input-error' : ''; ?>" required>
                        <div class="field-error <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['password_err'])) ? 'active' : ''; ?>">
                            <span class="field-error-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span><?= isset($data['password_err']) ? htmlspecialchars($data['password_err']) : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00] <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['confirm_password_err'])) ? 'input-error' : ''; ?>" required>
                        <div class="field-error <?= ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($data['confirm_password_err'])) ? 'active' : ''; ?>">
                            <span class="field-error-icon">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </span>
                            <span><?= isset($data['confirm_password_err']) ? htmlspecialchars($data['confirm_password_err']) : ''; ?></span>
                        </div>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-all">
                            Daftar Sekarang
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Sudah punya akun? <a href="/login" class="text-[#f57c00] hover:text-[#e65100] font-medium">Login disini</a></p>
                </div>
            </div>
            
            <!-- Right side - Illustration -->
            <div class="w-full md:w-1/2 bg-gradient-to-br from-[#ff9800] to-[#f57c00] p-8 flex flex-col justify-center items-center text-white">
                <div class="illustration-container mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold mb-4">Bergabung dengan ChatCare</h3>
                
                <ul class="space-y-4">
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Pantau emosi secara real-time</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Dapatkan dukungan melalui sistem chat</span>
                    </li>
                    <li class="flex items-start">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span>Akses laporan dan analisis emosi</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php
// Capture the content of the buffer and clean
$content = ob_get_clean();

// Include the layout principal
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
