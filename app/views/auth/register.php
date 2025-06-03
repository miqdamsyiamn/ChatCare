<?php
// Define variables for the layout
$title = 'Daftar Akun';
$css_file = 'auth';
$js_file = 'auth';

// Add the animation scripts
$extra_scripts = ['register-animation.js'];

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
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-800">Daftar Akun</h2>
                    <p class="text-gray-600 mt-2">Bergabung dengan ChatCare</p>
                </div>
                
                <form action="/register" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <input type="text" id="username" name="username" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" value="<?= isset($data['username']) ? $data['username'] : ''; ?>" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['username_err']) ? $data['username_err'] : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-gray-700 font-medium mb-2">Email</label>
                        <input type="email" id="email" name="email" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" value="<?= isset($data['email']) ? $data['email'] : ''; ?>" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['email_err']) ? $data['email_err'] : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['password_err']) ? $data['password_err'] : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Konfirmasi Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['confirm_password_err']) ? $data['confirm_password_err'] : ''; ?></span>
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
