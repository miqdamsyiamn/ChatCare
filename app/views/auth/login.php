<?php
// Define variables for the layout
$title = 'Login';
$css_file = 'auth';
$js_file = 'auth';

// Add the animation scripts
$extra_scripts = ['login-animation.js'];

// Start output buffer to capture the content
ob_start();
ob_clean();
?>

<div class="flex min-h-screen bg-gradient-to-br from-[#fff3e0] to-[#ffecb3]">
    <div class="m-auto w-full max-w-4xl p-4">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden flex flex-col md:flex-row">
            <!-- Left side - Illustration -->
            <div class="w-full md:w-1/2 bg-gradient-to-br from-[#ff9800] to-[#f57c00] p-8 flex flex-col justify-center items-center text-white">
                <div class="illustration-container mb-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-64 w-64 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4" />
                    </svg>
                </div>
                
                <h3 class="text-2xl font-bold mb-4">Selamat Datang Kembali</h3>
                
                <p class="text-center mb-6">Platform Monitoring Emosi Mahasiswa dan Dosen</p>
                
                <div class="features space-y-4">
                    <div class="feature flex items-center space-x-3">
                        <div class="icon-circle bg-white/20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <span>Monitoring Real-time</span>
                    </div>
                    
                    <div class="feature flex items-center space-x-3">
                        <div class="icon-circle bg-white/20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                        <span>Sistem Chatbot</span>
                    </div>
                    
                    <div class="feature flex items-center space-x-3">
                        <div class="icon-circle bg-white/20 p-2 rounded-full">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <span>Laporan Refleksi</span>
                    </div>
                </div>
            </div>
            
            <!-- Right side - Form -->
            <div class="w-full md:w-1/2 p-8 relative">
                <a href="/" class="absolute top-4 right-4 text-gray-500 hover:text-gray-800 transition-colors">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </a>
                <div class="mb-8 text-center">
                    <h2 class="text-3xl font-bold text-gray-800">Login ChatCare</h2>
                    <p class="text-gray-600 mt-2">Masuk ke akun Anda</p>
                </div>
                
                <form action="/login" method="POST" class="space-y-4">
                    <div>
                        <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                        <input type="text" id="username" name="username" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" value="<?= isset($data['username']) ? $data['username'] : ''; ?>" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['username_err']) ? $data['username_err'] : ''; ?></span>
                        </div>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-3 border rounded-lg focus:outline-none focus:ring-2 focus:ring-[#f57c00]" required>
                        <div class="error-container" style="min-height: 24px;">
                            <span class="text-red-500 text-sm"><?= isset($data['password_err']) ? $data['password_err'] : ''; ?></span>
                        </div>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember_me" name="remember_me" type="checkbox" class="h-4 w-4 text-[#f57c00] focus:ring-[#f57c00] border-gray-300 rounded">
                            <label for="remember_me" class="ml-2 block text-sm text-gray-700">Ingat saya</label>
                        </div>
                        
                        <a href="#" class="text-sm text-[#f57c00] hover:text-[#e65100]">Lupa password?</a>
                    </div>
                    
                    <div class="pt-2">
                        <button type="submit" class="w-full bg-[#f57c00] hover:bg-[#e65100] text-white font-bold py-3 px-4 rounded-lg focus:outline-none focus:shadow-outline transition-all">
                            Login
                        </button>
                    </div>
                </form>
                
                <div class="mt-6 text-center">
                    <p class="text-gray-600">Belum punya akun? <a href="/register" class="text-[#f57c00] hover:text-[#e65100] font-medium">Daftar disini</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
// Capturar o conteúdo do buffer e limpar
$content = ob_get_clean();

// Incluir o layout principal
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
