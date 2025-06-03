<?php
// Define variables for the layout
$title = 'Selamat Datang';
$css_file = 'landing';
$js_file = 'landing';

// Add the animation scripts
$extra_scripts = ['intro-animation.js', 'swiper.min.js'];

// Add extra CSS
$extra_css = ['swiper.min.css'];

// Start output buffer to capture the content
ob_start();
ob_clean();
?>

<style>
    /* Animation Delays */
    .animation-delay-1000 {
        animation-delay: 1s;
    }
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-3000 {
        animation-delay: 3s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    .delay-100 { animation-delay: 0.1s; }
    .delay-200 { animation-delay: 0.2s; }
    .delay-300 { animation-delay: 0.3s; }
    
    /* Fade In Animation */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fadeIn {
        animation: fadeIn 1s ease-out forwards;
    }
    
    /* Blob Animation */
    @keyframes blob {
        0% { transform: scale(1); }
        33% { transform: scale(1.1); }
        66% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
    .animate-blob {
        animation: blob 7s infinite;
    }
    
    /* Float Animation */
    @keyframes float {
        0% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
        100% { transform: translateY(0px); }
    }
    .animate-float {
        animation: float 6s ease-in-out infinite;
    }
    
    /* Subtle Rotation Animation */
    @keyframes subtle-rotate {
        0% { transform: rotate(0deg); }
        25% { transform: rotate(1deg); }
        75% { transform: rotate(-1deg); }
        100% { transform: rotate(0deg); }
    }
    .animate-subtle-rotate {
        animation: subtle-rotate 10s ease-in-out infinite;
    }


.bg-pattern {
  background-image: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
}
</style>

<div class="landing-page">
    <!-- Floating Navigation Menu -->
    <div class="fixed top-0 left-0 right-0 z-50 bg-white/10 backdrop-blur-lg shadow-md py-3 animate-fadeIn">
        <div class="container mx-auto px-6">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-2xl font-bold text-white">Chat<span class="text-yellow-300">Care</span></span>
                </a>
                
                <!-- Navigation Links (Desktop) -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="#" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium">Beranda</a>
                    <a href="#fitur" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium">Fitur</a>
                    <a href="#cara-kerja" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium">Cara Kerja</a>
                </nav>
                
                <!-- Auth Buttons -->
                <div class="flex items-center space-x-4">
                    <a href="/login" class="hidden md:inline-flex text-white hover:text-yellow-200 transition-colors duration-300 font-medium">Login</a>
                    <a href="/register" class="bg-white text-[#f57c00] hover:bg-yellow-50 transition-all duration-300 py-2 px-4 rounded-lg font-medium shadow-md hover:shadow-lg transform hover:scale-105">Daftar</a>
                    
                    <!-- Mobile Menu Button -->
                    <button id="mobile-menu-button" class="md:hidden text-white focus:outline-none">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                </div>
            </div>
            
            <!-- Mobile Navigation Menu (Hidden by default) -->
            <div id="mobile-menu" class="hidden md:hidden mt-4 bg-white/20 backdrop-blur-lg rounded-lg p-4 animate-fadeIn">
                <nav class="flex flex-col space-y-3">
                    <a href="#" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium py-2 px-3 rounded-lg hover:bg-white/10">Beranda</a>
                    <a href="#fitur" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium py-2 px-3 rounded-lg hover:bg-white/10">Fitur</a>
                    <a href="#cara-kerja" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium py-2 px-3 rounded-lg hover:bg-white/10">Cara Kerja</a>
                    <a href="#daftar" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium py-2 px-3 rounded-lg hover:bg-white/10">Daftar</a>
                    <a href="/login" class="text-white hover:text-yellow-200 transition-colors duration-300 font-medium py-2 px-3 rounded-lg hover:bg-white/10">Login</a>
                </nav>
            </div>
        </div>
    </div>
    
    <!-- Hero Section with Modern Design (CTA as Hero) -->
    <div class="hero-section bg-gradient-to-br from-[#ff9800] via-[#f57c00] to-[#e65100] relative overflow-hidden py-28 md:py-36 mt-0">
        <!-- Decorative Elements -->
        <div class="absolute top-0 left-0 w-full h-full">
            <div class="absolute top-10 left-10 w-64 h-64 rounded-full bg-white opacity-10 animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-80 h-80 rounded-full bg-white opacity-10 animate-pulse animation-delay-2000"></div>
            <div class="absolute top-1/3 left-1/4 w-32 h-32 rounded-full bg-white opacity-10 animate-pulse animation-delay-1000"></div>
            <div class="absolute top-2/3 right-1/4 w-24 h-24 rounded-full bg-white opacity-10 animate-pulse animation-delay-3000"></div>
            <!-- Diagonal Lines -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -left-20 top-0 w-[600px] h-[2px] bg-white transform rotate-45"></div>
                <div class="absolute -right-20 top-0 w-[600px] h-[2px] bg-white transform -rotate-45"></div>
            </div>
        </div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row items-center justify-between bg-white/10 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/20">
                <div class="md:w-1/2 mb-10 md:mb-0 md:pr-8">
                    <h1 class="text-4xl md:text-5xl font-bold mb-4 text-white leading-tight">Selamat Datang di <span class="relative inline-block">ChatCare
                        <span class="absolute bottom-0 left-0 w-full h-1 bg-yellow-300/50"></span>
                    </span></h1>
                    <p class="text-xl md:text-2xl mb-8 text-white/90 leading-relaxed">Platform Monitoring Emosi Mahasiswa dan Dosen yang Profesional</p>
                    <div class="flex flex-wrap gap-4">
                        <a href="#fitur" class="bg-white text-[#f57c00] font-bold py-3 px-8 rounded-lg hover:bg-yellow-50 transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                            Lihat Fitur
                        </a>
                        <a href="/register" class="bg-[#e65100] text-white font-bold py-3 px-8 rounded-lg hover:bg-[#d84315] transition-all duration-300 transform hover:scale-105 shadow-lg flex items-center justify-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                            </svg>
                            Mulai Sekarang
                        </a>
                    </div>
                    
                    <div class="mt-8 flex items-center">
                        <div class="animate-bounce opacity-70 hover:opacity-100 transition-opacity">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                            </svg>
                        </div>
                        <p class="text-white/80 ml-3">Scroll untuk menjelajahi</p>
                    </div>
                </div>
                
                <!-- Hero Illustration -->
                <div class="md:w-1/2 flex justify-center items-center">
                    <div class="relative w-full max-w-md transform transition-all duration-700 hover:scale-105">
                        <!-- Illustration SVG -->
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-full h-auto" viewBox="0 0 500 500" fill="none">
                            <!-- Background Circle -->
                            <circle cx="250" cy="250" r="200" fill="white" fill-opacity="0.1"/>
                            
                            <!-- Person with Speech Bubble -->
                            <g class="animate-float">
                                <!-- Speech Bubble -->
                                <path d="M150 180C150 169 159 160 170 160H330C341 160 350 169 350 180V250C350 261 341 270 330 270H240L210 300L180 270H170C159 270 150 261 150 250V180Z" fill="white" fill-opacity="0.2" stroke="white" stroke-width="2"/>
                                
                                <!-- Emoji Icons in Bubble -->
                                <circle cx="200" cy="215" r="15" fill="#FFD700"/>
                                <path d="M195 210L205 220M195 220L205 210" stroke="#333" stroke-width="2" stroke-linecap="round"/>
                                
                                <circle cx="250" cy="215" r="15" fill="#FFD700"/>
                                <path d="M245 218C245 218 247 222 250 222C253 222 255 218 255 218" stroke="#333" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="247" cy="212" r="2" fill="#333"/>
                                <circle cx="253" cy="212" r="2" fill="#333"/>
                                
                                <circle cx="300" cy="215" r="15" fill="#FFD700"/>
                                <path d="M295 212C295 212 297 208 300 208C303 208 305 212 305 212" stroke="#333" stroke-width="2" stroke-linecap="round"/>
                                <circle cx="297" cy="218" r="2" fill="#333"/>
                                <circle cx="303" cy="218" r="2" fill="#333"/>
                            </g>
                            
                            <!-- Person Silhouette -->
                            <path d="M250 350C277.614 350 300 327.614 300 300C300 272.386 277.614 250 250 250C222.386 250 200 272.386 200 300C200 327.614 222.386 350 250 350Z" fill="white" fill-opacity="0.2"/>
                            <path d="M180 400C180 372.386 212.386 350 250 350C287.614 350 320 372.386 320 400" stroke="white" stroke-width="3" stroke-linecap="round"/>
                            
                            <!-- Decorative Elements -->
                            <circle cx="150" cy="150" r="10" fill="#FFD700" fill-opacity="0.6"/>
                            <circle cx="350" cy="150" r="15" fill="#FFD700" fill-opacity="0.6"/>
                            <circle cx="150" cy="350" r="15" fill="#FFD700" fill-opacity="0.6"/>
                            <circle cx="350" cy="350" r="10" fill="#FFD700" fill-opacity="0.6"/>
                            
                            <!-- Pulse Animation Rings -->
                            <circle cx="250" cy="250" r="220" stroke="white" stroke-opacity="0.2" stroke-width="2" class="animate-pulse"/>
                            <circle cx="250" cy="250" r="240" stroke="white" stroke-opacity="0.1" stroke-width="1" class="animate-pulse animation-delay-1000"/>
                        </svg>
                        
                        <!-- Floating Badges -->
                        <div class="absolute top-10 right-10 bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full text-white text-sm font-medium shadow-lg transform -rotate-12 animate-float animation-delay-1000">
                            Kesehatan Mental
                        </div>
                        <div class="absolute bottom-20 left-0 bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-full text-white text-sm font-medium shadow-lg transform rotate-12 animate-float animation-delay-2000">
                            Monitoring Emosi
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- App Introduction Section -->
    <!-- About Section with Modern Design -->
    <div class="about-section py-24 bg-gradient-to-b from-gray-50 to-white relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute left-0 top-0 w-72 h-72 bg-orange-50 rounded-full filter blur-3xl opacity-40"></div>
        <div class="absolute right-0 bottom-0 w-96 h-96 bg-orange-50 rounded-full filter blur-3xl opacity-40"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="flex flex-col md:flex-row items-center gap-12 md:gap-16">
                <!-- Left Column: Image/Icon -->
                <div class="md:w-1/2 flex justify-center animate-fadeIn">
                    <div class="relative w-72 h-72 md:w-96 md:h-96">
                        <div class="absolute inset-0 bg-gradient-to-br from-gray-100 to-gray-200 rounded-full transform rotate-12"></div>
                        <div class="absolute inset-2 bg-white rounded-full shadow-inner flex items-center justify-center">
                            <div class="absolute inset-0 bg-gradient-to-br from-orange-50 to-orange-100 rounded-full opacity-70"></div>
                            <div class="relative z-10 p-6">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-40 w-40 md:h-56 md:w-56 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Right Column: Text -->
                <div class="md:w-1/2 text-left">
                    <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4 shadow-sm animate-fadeIn">TENTANG KAMI</span>
                    <h2 class="text-3xl md:text-5xl font-bold text-gray-800 mb-6 animate-fadeIn delay-100">Kenali <span class="text-[#ff9800] relative inline-block">
                        ChatCare
                        <span class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#ff9800] to-[#f57c00]"></span>
                    </span></h2>
                    
                    <div class="space-y-6 animate-fadeIn delay-200">
                        <p class="text-gray-600 text-lg md:text-xl leading-relaxed">ChatCare adalah platform digital inovatif yang dirancang khusus untuk memantau dan meningkatkan kesehatan emosional mahasiswa dan dosen dalam lingkungan akademik.</p>
                        
                        <p class="text-gray-600 text-lg md:text-xl leading-relaxed">Dengan pendekatan yang profesional dan berbasis data, kami membantu institusi pendidikan menciptakan lingkungan yang lebih sehat secara mental dan emosional.</p>
                        
                        <div class="pt-4">
                            <a href="#fitur" class="inline-flex items-center text-[#f57c00] font-bold hover:text-[#e65100] transition-all duration-300 group text-lg">
                                <span>Lihat Fitur Kami</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Features Section with Modern Cards -->
    <div id="fitur" class="features-section py-24 bg-white relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute left-0 bottom-0 w-64 h-64 bg-orange-50 rounded-full filter blur-3xl opacity-50"></div>
        <div class="absolute right-0 top-0 w-64 h-64 bg-orange-50 rounded-full filter blur-3xl opacity-50"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-20">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4 shadow-sm">FITUR UTAMA</span>
                <h2 class="text-3xl md:text-5xl font-bold text-gray-800 mb-6">Layanan <span class="text-[#ff9800] relative">ChatCare
                    <span class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-[#ff9800] to-[#f57c00]"></span>
                </span></h2>
                <div class="w-32 h-1.5 bg-gradient-to-r from-[#ff9800] to-[#f57c00] mx-auto rounded-full mb-8 hidden"></div>
                <p class="text-gray-600 max-w-3xl mx-auto text-xl leading-relaxed">Platform inovatif yang dirancang untuk membantu memantau dan meningkatkan kesehatan emosional dalam lingkungan akademik</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 md:gap-12 max-w-6xl mx-auto">
                <!-- Feature 1 -->
                <div class="feature-card group hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-100">
                    <div class="relative overflow-hidden h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                        <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Monitoring Real-time</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">Pantau emosi secara real-time dengan visualisasi interaktif untuk memahami kondisi emosional mahasiswa dan dosen.</p>
                        <a href="#" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                            <span>Pelajari lebih lanjut</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Feature 2 -->
                <div class="feature-card group hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-200">
                    <div class="relative overflow-hidden h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                        <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Sistem Chatbot</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">Bagikan perasaan dan dapatkan dukungan yang diperlukan melalui sistem chat yang aman, nyaman, dan responsif.</p>
                        <a href="#" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                            <span>Pelajari lebih lanjut</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
                
                <!-- Feature 3 -->
                <div class="feature-card group hover:shadow-2xl hover:-translate-y-3 transition-all duration-500 bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-300">
                    <div class="relative overflow-hidden h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                        <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                    <div class="p-8 relative z-10">
                        <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Laporan Refleksi</h3>
                        <p class="text-gray-600 leading-relaxed mb-6">Generate laporan detail untuk analisis mendalam tentang pola emosi dan perkembangan kesehatan mental Anda.</p>
                        <a href="#" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                            <span>Pelajari lebih lanjut</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- How It Works Section with Modern Design -->    
    <div id="cara-kerja" class="how-it-works py-28 bg-gradient-to-br from-gray-50 via-white to-gray-50 relative overflow-hidden">
        <!-- Decorative Elements -->
        <div class="absolute left-0 top-1/4 w-96 h-96 bg-orange-100 rounded-full filter blur-3xl opacity-30 animate-blob animation-delay-4000"></div>
        <div class="absolute right-0 bottom-1/4 w-96 h-96 bg-orange-200 rounded-full filter blur-3xl opacity-20 animate-blob"></div>
        <div class="absolute left-1/4 bottom-1/4 w-48 h-48 bg-orange-100 rounded-full filter blur-3xl opacity-20 animate-blob animation-delay-2000"></div>
        <div class="absolute right-1/4 top-1/4 w-48 h-48 bg-orange-200 rounded-full filter blur-3xl opacity-20 animate-blob animation-delay-3000"></div>
        
        <div class="container mx-auto px-6 relative z-10">
            <div class="text-center mb-20">
                <span class="inline-block px-4 py-1.5 bg-orange-100 text-orange-700 rounded-full text-sm font-semibold mb-4 shadow-sm">CARA KERJA</span>
                <h2 class="text-3xl md:text-5xl font-bold text-gray-800 mb-6">Bagaimana <span class="text-[#ff9800] relative inline-block">
                    ChatCare
                    <span class="absolute bottom-0 left-0 w-full h-1.5 bg-gradient-to-r from-[#ff9800] to-[#f57c00]"></span>
                </span> Bekerja</h2>
                <p class="text-gray-600 max-w-3xl mx-auto text-xl leading-relaxed">Tiga langkah sederhana untuk memulai perjalanan kesehatan emosional Anda</p>
            </div>
            
            <div class="relative max-w-6xl mx-auto">
                <!-- Connection Line (only visible on md screens and up) -->
                <div class="hidden md:block absolute top-36 left-0 right-0 h-2 bg-gradient-to-r from-orange-100 via-[#ff9800] to-orange-100 z-0 rounded-full opacity-70"></div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 md:gap-16 relative z-10">
                    <!-- Step 1 -->
                    <div class="group bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-100 transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl">
                        <div class="relative h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                            <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-gradient-to-r from-[#ff9800] to-[#f57c00] text-white flex items-center justify-center font-bold text-lg shadow-lg z-10">1</div>
                            <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Buat Akun</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">Daftar dengan mudah dan buat profil Anda untuk mulai menggunakan layanan ChatCare. Proses pendaftaran yang cepat dan aman.</p>
                            <a href="/register" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                                <span>Daftar Sekarang</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="group bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-200 transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl">
                        <div class="relative h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                            <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-gradient-to-r from-[#ff9800] to-[#f57c00] text-white flex items-center justify-center font-bold text-lg shadow-lg z-10">2</div>
                            <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Mulai Berbagi</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">Bagikan perasaan dan emosi Anda melalui sistem chat yang aman dan nyaman. Dapatkan dukungan yang Anda butuhkan.</p>
                            <a href="#" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                                <span>Pelajari Lebih Lanjut</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="group bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100 animate-fadeIn delay-300 transform transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl">
                        <div class="relative h-48 bg-gradient-to-br from-orange-50 to-orange-100 flex items-center justify-center overflow-hidden">
                            <div class="absolute -top-10 -right-10 w-40 h-40 bg-gradient-to-br from-orange-100 to-orange-200 rounded-full opacity-70"></div>
                            <div class="absolute top-4 left-4 w-12 h-12 rounded-full bg-gradient-to-r from-[#ff9800] to-[#f57c00] text-white flex items-center justify-center font-bold text-lg shadow-lg z-10">3</div>
                            <div class="relative z-10 transform transition-transform duration-500 group-hover:scale-110">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24 text-[#f57c00] drop-shadow-lg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                        </div>
                        <div class="p-8">
                            <h3 class="text-2xl font-bold mb-4 text-gray-800 group-hover:text-[#f57c00] transition-all duration-300">Lihat Perkembangan</h3>
                            <p class="text-gray-600 leading-relaxed mb-4">Dapatkan laporan dan analisis tentang perkembangan kesehatan emosional Anda dengan visualisasi yang mudah dipahami.</p>
                            <a href="#" class="text-[#f57c00] font-medium flex items-center group-hover:text-[#e65100] transition-all duration-300 mt-auto">
                                <span>Pelajari Lebih Lanjut</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 ml-2 group-hover:ml-3 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Call to Action Section with Modern Design -->
    
    
    <!-- End of content -->
    <!-- The footer is provided by the main layout template -->
</div>

<!-- JavaScript for Mobile Menu Toggle -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        // Toggle mobile menu
        mobileMenuButton.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
        
        // Close mobile menu when clicking on a link
        const mobileLinks = mobileMenu.querySelectorAll('a');
        mobileLinks.forEach(link => {
            link.addEventListener('click', function() {
                mobileMenu.classList.add('hidden');
            });
        });
        
        // Add scroll event to change navbar background
        const navbar = document.querySelector('.fixed');
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                navbar.classList.add('bg-[#f57c00]/80');
                navbar.classList.remove('bg-white/10');
            } else {
                navbar.classList.add('bg-white/10');
                navbar.classList.remove('bg-[#f57c00]/80');
            }
        });
    });
</script>

<?php
// Capture the content of the buffer and clean
$content = ob_get_clean();

// Include the layout principal
require_once BASE_PATH . '/app/views/layouts/main.php';
?>
