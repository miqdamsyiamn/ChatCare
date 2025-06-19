# ChatCare

## Deskripsi Proyek

ChatCare adalah aplikasi web diskusi kelompok dengan analisis emosi real-time. Setiap pesan dianalisis menggunakan Google Gemini API untuk mendeteksi emosi (positif, negatif, netral), sehingga pemimpin diskusi dapat memantau suasana tim dan mengambil tindakan yang tepat. Aplikasi ini juga menyediakan auto-feedback AI, visualisasi mood, dan laporan diskusi.

## Fitur Utama

- **Autentikasi & Manajemen Pengguna**: Login, register, dan pengelolaan user (admin/leader/member)
- **Diskusi & Chatroom**: Pembuatan sesi diskusi, chat real-time, polling pesan otomatis
- **Analisis Emosi Otomatis**: Setiap pesan dianalisis AI (Google Gemini) untuk label emosi
- **Auto-Feedback AI**: Sistem memberikan feedback otomatis jika terdeteksi emosi negatif beruntun
- **Visualisasi Mood**: Grafik mood tim secara real-time (Chart.js)
- **Laporan Diskusi**: Generate laporan hasil diskusi dan mood
- **Keamanan**: Validasi session, filter duplikasi pesan, logging error

## Struktur Folder & Penjelasan

```
ChatCare/
├── app/
│   ├── controllers/   # Controller utama: AuthController, ChatroomController, DashboardController, DiscussionController, HomeController, MoodController, ReportController, UserController, ApiController
│   ├── core/          # Core framework: App, Autoloader, Controller, Database, GeminiService
│   ├── models/        # Model: UserModel, MessageModel, EmotionModel, MoodModel, ReportModel, SessionModel
│   ├── services/      # AIChatService (fitur chat AI pribadi)
│   └── views/         # View: auth, chatroom, dashboard, discussions, home, layouts, mood, reports, users
├── config/            # Konfigurasi database & Gemini API
├── public/            # Asset publik (css, js, img, index.php)
├── routes/            # Routing aplikasi (web.php)
├── storage/           # File workflow, log, dan upload (chatcare_workflow.dot/png/txt, generate_workflow_image.php)
├── logs/              # Log error PHP (php-errors.log)
└── chatcare_db.sql    # Struktur & data database
```

### Penjelasan File/Fitur Penting

- **app/controllers/**: Semua logika request aplikasi (login, chat, diskusi, mood, laporan, API)
- **app/core/**: Inti framework custom (routing, autoload, koneksi DB, integrasi GeminiService)
- **app/models/**: Interaksi database (user, pesan, mood, laporan, sesi)
- **app/services/AIChatService.php**: Fitur chat AI pribadi (opsional)
- **app/views/**: Template tampilan web (autentikasi, chatroom, dashboard, diskusi, mood, laporan, user)
- **config/database.php**: Konfigurasi koneksi database
- **config/gemini.php**: Konfigurasi Google Gemini API
- **public/**: Semua file yang diakses browser (CSS, JS, gambar, index.php)
- **routes/web.php**: Semua routing aplikasi (web & API)
- **storage/**: File workflow aplikasi (diagram, txt, dot, png)
- **logs/php-errors.log**: Log error aplikasi
- **chatcare_db.sql**: Struktur dan data awal database

## Workflow Aplikasi

```
User → Browser → chatroom.js → AJAX → ChatroomController → MessageModel
→ Emotion Detection (GeminiService) → Database → JSON Response → chatroom.js → View Update

Polling pesan baru: chatroom.js → ChatroomController → MessageModel → Database → chatroom.js
Auto-feedback: MessageModel → GeminiService → Simpan feedback → chatroom.js → View
```

## Integrasi AI & API

- **GeminiService** (`app/core/GeminiService.php`):
  - Menganalisis emosi pesan via Google Gemini API (positif/negatif/netral)
  - Generate feedback otomatis untuk pesan negatif
- **AIChatService** (`app/services/AIChatService.php`):
  - Fitur chat AI pribadi (opsional)
- **Konfigurasi API**: `config/gemini.php`

## Endpoint & Routing

Semua route didefinisikan di `routes/web.php`.
Contoh endpoint API:

- `/api/messages` (GET): Ambil pesan chatroom
- `/api/send-message` (POST): Kirim pesan
- `/api/mood-data` (GET): Data mood untuk chart
- `/api/generate-feedback` (POST): Feedback AI

## Database

- Konfigurasi: `config/database.php`
- Tabel utama: `user`, `message_log`, `team_mood`, `dataset_emosi`
- Struktur lengkap: lihat `chatcare_db.sql`

## Requirement

- PHP >= 7.4
- MySQL/MariaDB
- Ekstensi PHP: cURL, PDO
- Koneksi internet (untuk Gemini API)

## Cara Instalasi & Menjalankan

1. Clone repo & import `chatcare_db.sql` ke MySQL
2. Atur `config/database.php` sesuai environment
3. Atur `config/gemini.php` dengan API key Gemini
4. Jalankan server lokal (XAMPP/Laragon/Apache)
5. Akses via browser ke `public/index.php`

## Troubleshooting & Logging

- Error aplikasi dicatat di `logs/php-errors.log`
- Cek koneksi API Gemini jika analisis emosi gagal
- Pastikan session & permission folder `storage/` dan `logs/` writeable

## Pengembangan & Kontribusi

- Ikuti pola MVC custom
- Tambahkan fitur baru di controller/model/view terkait
- Pull request & issue welcome

## Lisensi

Aplikasi ini untuk keperluan edukasi.

## Kontak

Untuk pertanyaan/kendala, hubungi pengembang utama atau buat issue di repository ini.
