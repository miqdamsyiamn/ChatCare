# ChatcareFix

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

## Relasi Antar Folder

### 1. Pola Arsitektur MVC

Aplikasi ini menggunakan pola arsitektur Model-View-Controller (MVC) yang terstruktur sebagai berikut:

- **Model (`app/models/`)**: Bertanggung jawab untuk logika bisnis dan interaksi dengan database. Setiap model merepresentasikan entitas atau konsep dalam aplikasi (seperti User, Message, Emotion).

- **View (`app/views/`)**: Menangani tampilan yang dilihat oleh pengguna. View diorganisir berdasarkan fitur atau halaman (auth, chatroom, dashboard, dll).

- **Controller (`app/controllers/`)**: Menghubungkan Model dan View, menangani request dari pengguna, memproses data dari Model, dan mengirimkan data ke View.

### 2. Alur Aplikasi

1. **Entry Point**: Semua request masuk melalui `public/index.php` yang kemudian di-routing ke controller yang sesuai.

2. **Routing**: File `routes/web.php` mendefinisikan semua route yang tersedia dalam aplikasi, menghubungkan URL dengan controller dan method yang sesuai.

3. **Database**: Konfigurasi database berada di `config/database.php`, menggunakan MySQL dengan database bernama `chatcare_db`.

4. **API Gemini**: Aplikasi menggunakan Google Gemini API untuk analisis emosi, dengan konfigurasi di `config/gemini.php`.

### 3. Komponen Utama

#### Core Framework (`app/core/`)

Berisi komponen inti dari framework custom yang digunakan:
- **Autoloader**: Menangani loading class secara otomatis
- **App**: Class utama yang menjalankan aplikasi
- **Controller**: Base class untuk semua controller
- **Database**: Menangani koneksi dan operasi database

#### Controllers (`app/controllers/`)

Menangani logika aplikasi berdasarkan fitur:
- **AuthController**: Autentikasi (login, logout)
- **ChatroomController**: Pengelolaan chatroom dan pesan
- **DashboardController**: Tampilan dashboard
- **DiscussionController**: Pengelolaan diskusi
- **MoodController**: Visualisasi mood/emosi
- **ReportController**: Pembuatan laporan
- **UserController**: Manajemen pengguna

#### Models (`app/models/`)

Menangani interaksi dengan database:
- **EmotionModel**: Operasi terkait dataset emosi
- **MessageModel**: Pengelolaan pesan
- **MoodModel**: Analisis dan visualisasi mood
- **ReportModel**: Pembuatan laporan
- **SessionModel**: Pengelolaan sesi diskusi
- **UserModel**: Manajemen pengguna

#### Views (`app/views/`)

Diorganisir berdasarkan fitur:
- **auth/**: Tampilan login dan autentikasi
- **chatroom/**: Tampilan chatroom
- **dashboard/**: Tampilan dashboard
- **discussions/**: Tampilan pengelolaan diskusi
- **layouts/**: Template layout umum
- **mood/**: Visualisasi mood/emosi
- **reports/**: Tampilan laporan
- **users/**: Manajemen pengguna

#### Public (`public/`)

Berisi file yang dapat diakses langsung oleh browser:
- **css/**: File stylesheet
- **js/**: File JavaScript, termasuk `charts.js` untuk visualisasi data
- **index.php**: Entry point aplikasi

## Database

Database `chatcare_db` memiliki beberapa tabel utama:

1. **dataset_emosi**: Berisi data training untuk analisis emosi dengan kolom:
   - `id`: ID unik
   - `text`: Teks pesan
   - `label`: Label emosi (positif, netral, negatif)

2. **user**: Menyimpan data pengguna

3. **message_log**: Menyimpan riwayat pesan dengan label emosi

4. **team_mood**: Menyimpan data mood tim selama sesi diskusi

## Fitur Utama

1. **Autentikasi**: Login dan manajemen pengguna
2. **Diskusi**: Pembuatan dan pengelolaan sesi diskusi
3. **Chatroom**: Komunikasi real-time antar pengguna
4. **Analisis Emosi**: Analisis sentimen pesan menggunakan Google Gemini API
5. **Visualisasi Mood**: Grafik dan chart untuk memvisualisasikan mood tim
6. **Laporan**: Pembuatan laporan hasil diskusi

## Teknologi yang Digunakan

- **Backend**: PHP (Custom MVC Framework)
- **Frontend**: HTML, CSS, JavaScript
- **Database**: MySQL
- **Visualisasi**: Chart.js
- **Analisis Emosi**: Google Gemini API

## Alur Kerja Aplikasi

1. Pengguna login ke sistem
2. Pemimpin diskusi membuat sesi diskusi baru
3. Anggota bergabung dengan sesi diskusi
4. Pesan yang dikirim dianalisis untuk menentukan emosi
5. Emosi divisualisasikan dalam bentuk grafik
6. Pemimpin diskusi dapat melihat mood tim secara real-time
7. Laporan dapat dibuat setelah sesi diskusi berakhir
