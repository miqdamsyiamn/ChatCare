<!DOCTYPE html>
<html lang="id" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? $title . ' - ChatCare' : 'ChatCare' ?></title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/css/main.css">
    <?php if (isset($css_file)): ?>
        <link rel="stylesheet" href="/css/<?= $css_file ?>.css">
    <?php endif; ?>

    <!-- Extra CSS -->
    <?php if (isset($extra_css) && is_array($extra_css)): ?>
        <?php foreach ($extra_css as $css): ?>
            <link rel="stylesheet" href="/css/<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- CSS Fixes -->
    <link rel="stylesheet" href="/css/fixes.css">

    <link rel="icon" type="image/x-icon" href="/img/logo/logochatcarebig.ico">

    <style type="text/tailwind">
        @layer base {
            :root {
                --color-primary: 245 124 0;
                --color-primary-light: 255 167 38;
                --color-primary-dark: 230 81 0;
            }
        }
    </style>
</head>

<body class="bg-gray-100">
    <div class="layout-wrapper min-h-screen flex flex-col">
        <?php if (isset($_SESSION['user_id'])): ?>
            <!-- Header -->
            <header class="bg-white shadow sticky top-0 z-50">
                <div class="container mx-auto px-4 py-3 flex justify-between items-center">
                    <a href="/dashboard" class="flex items-center">
                        <img src="/img/logo/logochatcarebig.png" alt="ChatCare Logo" style="height:48px;width:auto;display:inline-block;vertical-align:middle;margin-right:10px;">
                        <span class="text-2xl font-bold text-[#f57c00] align-middle" style="vertical-align:middle;">ChatCare</span>
                    </a>

                    <div class="flex items-center space-x-4">
                        <div class="flex items-center">
                            <div class="w-8 h-8 rounded-full bg-[#f57c00] text-white flex items-center justify-center mr-2">
                                <?= substr($_SESSION['username'], 0, 1) ?>
                            </div>
                            <span class="text-gray-700 font-medium hidden sm:inline"><?= $_SESSION['username'] ?></span>
                            <span class="ml-2 text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded-full"><?= ucfirst($_SESSION['role']) ?></span>
                        </div>
                        <a href="/logout" class="px-4 py-2 bg-gray-200 rounded-md text-gray-700 hover:bg-gray-300 transition flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                            <span class="hidden sm:inline">Logout</span>
                        </a>
                    </div>
                </div>

                <!-- Navigation -->
                <nav class="bg-white border-b border-gray-200 shadow-sm">
                    <div class="container mx-auto px-4">
                        <div class="flex space-x-1 overflow-x-auto">
                            <a href="/dashboard" class="px-4 py-3 text-gray-700 hover:text-[#f57c00] hover:border-b-2 hover:border-[#f57c00] transition-all whitespace-nowrap <?= strpos($_SERVER['REQUEST_URI'], '/dashboard') === 0 ? 'text-[#f57c00] border-b-2 border-[#f57c00] font-medium' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                                </svg>
                                Dashboard
                            </a>

                            <?php if ($_SESSION['role'] == 'admin'): ?>
                                <a href="/users" class="px-4 py-3 text-gray-700 hover:text-[#f57c00] hover:border-b-2 hover:border-[#f57c00] transition-all whitespace-nowrap <?= strpos($_SERVER['REQUEST_URI'], '/users') === 0 ? 'text-[#f57c00] border-b-2 border-[#f57c00] font-medium' : '' ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                                    </svg>
                                    Manajemen Pengguna
                                </a>
                            <?php endif; ?>

                            <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'leader'): ?>
                            <a href="/discussions" class="px-4 py-3 text-gray-700 hover:text-[#f57c00] hover:border-b-2 hover:border-[#f57c00] transition-all whitespace-nowrap <?= strpos($_SERVER['REQUEST_URI'], '/discussions') === 0 ? 'text-[#f57c00] border-b-2 border-[#f57c00] font-medium' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                                </svg>
                                Kelola Diskusi
                            </a>
                            <?php endif; ?>
                            <button type="button" onclick="openGuideModal()" class="px-4 py-3 text-gray-700 hover:text-[#f57c00] hover:border-b-2 hover:border-[#f57c00] transition-all whitespace-nowrap flex items-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 14h.01M16 10h.01M21 12c0 4.418-4.03 8-9 8s-9-3.582-9-8 4.03-8 9-8 9 3.582 9 8zm-9 4v.01" />
                            </svg>
                                Guide
                            </button>

                            <a href="/reports" class="px-4 py-3 text-gray-700 hover:text-[#f57c00] hover:border-b-2 hover:border-[#f57c00] transition-all whitespace-nowrap <?= strpos($_SERVER['REQUEST_URI'], '/reports') === 0 ? 'text-[#f57c00] border-b-2 border-[#f57c00] font-medium' : '' ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Laporan
                            </a>
                        </div>
                    </div>
                </nav>
            </header>
        <?php endif; ?>

        <!-- Main Content -->
        <main class="flex-grow">
            <!-- Success notifications are now handled in individual pages -->

            <?php if (isset($_SESSION['error_message'])): ?>
                <div class="notification notification-error container mx-auto mt-6 mb-6 max-w-2xl" role="alert">
                    <div class="notification-icon">
                        <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="notification-message">
                        <span class="font-medium text-base"><?= $_SESSION['error_message'] ?></span>
                    </div>
                    <button type="button" class="notification-close" onclick="this.parentElement.remove();">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <?php unset($_SESSION['error_message']); ?>
                </div>
            <?php endif; ?>

            <!-- Konten utama halaman -->
            <div class="content-wrapper">
                <?php echo $content; ?>
            </div>
        </main>

        <!-- Footer -->
        <footer class="bg-white py-6 shadow-inner mt-auto <?= !isset($_SESSION['user_id']) ? 'login-page-footer' : '' ?>">
            <div class="container mx-auto px-4">
                <div class="footer-content flex flex-col md:flex-row justify-between items-center <?= !isset($_SESSION['user_id']) ? 'login-footer' : '' ?>">
                    <div class="mb-4 md:mb-0">
                        <div class="flex items-center justify-center md:justify-start">
                            <span class="footer-logo text-xl font-bold text-[#f57c00]">ChatCare</span>
                        </div>
                        <p class="text-gray-600 text-sm mt-2">Platform Diskusi Digital dengan Deteksi Emosi</p>
                        <!-- Social Icons -->
                        <div class="flex space-x-4 mt-3">
                            <a href="#" class="social-icon text-gray-500 hover:text-[#f57c00] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z" />
                                </svg>
                            </a>
                            <a href="#" class="social-icon text-gray-500 hover:text-[#f57c00] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                                </svg>
                            </a>
                            <a href="#" class="social-icon text-gray-500 hover:text-[#f57c00] transition-all">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z" />
                                </svg>
                            </a>
                        </div>
                    </div>

                    <div class="text-center md:text-right">
                        <div class="mb-2">
                            <a href="#" class="text-gray-600 hover:text-[#f57c00] transition-all text-sm mr-4">Tentang Kami</a>
                            <a href="#" class="text-gray-600 hover:text-[#f57c00] transition-all text-sm mr-4">Kebijakan Privasi</a>
                            <a href="#" class="text-gray-600 hover:text-[#f57c00] transition-all text-sm">Bantuan</a>
                        </div>
                        <p class="text-gray-600 text-sm">&copy; <?= date('Y') ?> ChatCare. All rights reserved.</p>
                    </div>
                </div>
            </div>
            <div class="footer-wave"></div>
        </footer>
    </div>

    <!-- Notification Handler -->
    <script src="/js/notification-handler.js"></script>

    <!-- Custom JS -->
    <?php if (isset($js_file)): ?>
        <script src="/js/<?= $js_file ?>.js"></script>
    <?php endif; ?>

    <!-- Extra Scripts -->
    <?php if (isset($extra_scripts) && is_array($extra_scripts)): ?>
        <?php foreach ($extra_scripts as $script): ?>
            <script src="/js/<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
<!-- SweetAlert2 CDN -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function openGuideModal() {
    const userRole = "<?= isset($_SESSION['role']) ? $_SESSION['role'] : '' ?>";
    let htmlContent = '';
    let title = 'Panduan Penggunaan Aplikasi';

    const guideStyles = {
        container: 'space-y-3 text-left',
        item: 'p-3 bg-orange-50 rounded-lg border-l-4 border-orange-400',
        title: 'font-semibold text-orange-800',
        description: 'text-gray-600 text-sm mt-1'
    };

    if (userRole === 'admin') {
        title = 'Panduan untuk Admin';
        htmlContent = `
        <div class="${guideStyles.container}">
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Dashboard</h3>
                <p class="${guideStyles.description}">Pusat pemantauan untuk melihat seluruh aktivitas sistem secara menyeluruh.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Manajemen Pengguna</h3>
                <p class="${guideStyles.description}">Mengelola semua akun pengguna, termasuk menambah, mengubah, dan menghapus data pengguna.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Kelola Diskusi</h3>
                <p class="${guideStyles.description}">Memantau semua sesi diskusi yang ada, meninjau percakapan, dan juga dapat membuat sesi diskusi baru.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Laporan</h3>
                <p class="${guideStyles.description}">Mengakses laporan dari seluruh diskusi untuk analisis dan evaluasi.</p>
            </div>
        </div>`;
    } else if (userRole === 'leader') {
        title = 'Panduan untuk Leader';
        htmlContent = `
        <div class="${guideStyles.container}">
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Dashboard</h3>
                <p class="${guideStyles.description}">Melihat ringkasan diskusi dan aktivitas dari tim yang Anda pimpin.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Kelola Diskusi</h3>
                <p class="${guideStyles.description}">Membuat sesi diskusi baru, memulai atau mengakhiri sesi, serta memantau jalannya diskusi tim. Anda juga dapat melihat visualisasi emosi tim secara real-time.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Laporan</h3>
                <p class="${guideStyles.description}">Mengakses hasil laporan dari sesi diskusi yang telah Anda pimpin untuk evaluasi.</p>
            </div>
        </div>`;
    } else if (userRole === 'member') {
        title = 'Panduan untuk Member';
        htmlContent = `
        <div class="${guideStyles.container}">
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Dashboard</h3>
                <p class="${guideStyles.description}">Halaman utama untuk bergabung dan berpartisipasi aktif dalam sesi diskusi yang tersedia.</p>
            </div>
            <div class="${guideStyles.item}">
                <h3 class="${guideStyles.title}">Laporan</h3>
                <p class="${guideStyles.description}">Melihat riwayat atau ringkasan sederhana dari diskusi yang pernah Anda ikuti. Peran Anda fokus pada kontribusi dalam percakapan.</p>
            </div>
        </div>`;
    } else {
        htmlContent = '<p class="text-center text-gray-600">Panduan tidak tersedia. Silakan login terlebih dahulu.</p>';
    }

    Swal.fire({
        title: `<span style="color:#f57c00; font-weight: 600;">${title}</span>`,
        html: htmlContent,
        confirmButtonText: 'Mengerti',
        width: '32rem',
        padding: '1.5rem',
        background: '#fff',
        customClass: {
            popup: 'rounded-xl shadow-lg',
            title: 'text-2xl mb-4',
            htmlContainer: 'text-base',
            confirmButton: 'bg-[#f57c00] hover:bg-[#e67300] text-white font-bold py-2 px-4 rounded-lg transition-colors duration-300',
            closeButton: 'text-gray-400 hover:text-gray-600'
        },
        showCloseButton: true
    });
}
</script>
</body>

</html>