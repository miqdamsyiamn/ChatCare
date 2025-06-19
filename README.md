# ChatCare

## Deskripsi Proyek

ChatcareFix adalah aplikasi berbasis web yang dirancang untuk memfasilitasi diskusi kelompok dengan analisis emosi secara real-time. Aplikasi ini menggunakan teknologi analisis sentimen untuk mengidentifikasi dan melacak emosi pengguna selama diskusi, memungkinkan pemimpin diskusi untuk memantau suasana hati tim dan mengambil tindakan yang tepat.

## Struktur Folder

Berikut adalah struktur folder utama dari proyek ChatcareFix:

```
ChatcareFix/
├── chatcare/                  # Folder utama aplikasi
│   ├── app/                   # Logika aplikasi (MVC)
│   │   ├── controllers/       # Controller untuk menangani request
│   │   ├── core/              # Core framework
│   │   ├── models/            # Model untuk interaksi dengan database
│   │   └── views/             # View untuk tampilan
│   ├── config/                # File konfigurasi
│   ├── public/                # File publik (CSS, JS, gambar)
│   │   ├── css/               # File CSS
│   │   ├── js/                # File JavaScript
│   │   └── index.php          # Entry point aplikasi
│   ├── routes/                # Definisi routing
│   ├── storage/               # Penyimpanan file (upload, log, dll)
│   └── .htaccess              # Konfigurasi Apache
└── chatcare_db.sql            # Struktur dan data database
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
