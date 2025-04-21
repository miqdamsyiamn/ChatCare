# ChatCare - Asisten Emosi untuk Tim Belajar Daring

ChatCare adalah aplikasi web interaktif yang dirancang untuk memfasilitasi diskusi tim belajar daring dengan memperhatikan aspek emosional peserta diskusi.

## Fitur Utama

- 💬 **Chat Real-time**: Diskusi langsung antar anggota tim
- 🎭 **Deteksi Emosi**: Analisis otomatis suasana diskusi berdasarkan teks
- 📊 **Visualisasi Mood**: Pantau suasana tim secara real-time
- 💡 **Rekomendasi Cerdas**: Saran otomatis untuk menjaga suasana positif
- 📈 **Laporan Refleksi**: Analisis statistik emosi setelah diskusi

## Struktur Proyek

```
ChatCare/
├── assets/
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   │   └── script.js
│   └── img/
├── index.html
├── diskusi.html
├── laporan.html
├── tentang.html
├── kontak.html
└── README.md
```

## Teknologi yang Digunakan

- HTML5
- CSS3
- JavaScript (Vanilla)
- LocalStorage untuk penyimpanan data

## Cara Menjalankan

1. Buka file `index.html` di browser web modern
2. Klik tombol "Mulai Diskusi" untuk memulai sesi chat
3. Kirim pesan dan lihat analisis emosi secara real-time
4. Lihat laporan di halaman "Laporan" untuk analisis lengkap

## Penyimpanan Data

Aplikasi ini menggunakan localStorage browser untuk menyimpan:
- Pesan-pesan chat
- Data emosi
- Statistik diskusi

Data akan tersimpan di browser pengguna dan dapat diakses kembali selama cache browser tidak dihapus.

## Pengembangan Selanjutnya

- Implementasi backend untuk penyimpanan data permanen
- Integrasi dengan API analisis sentimen yang lebih canggih
- Fitur ekspor laporan dalam format PDF
- Dukungan untuk berbagi media dalam chat
- Sistem notifikasi real-time

## Lisensi

Proyek ini adalah bagian dari tugas kuliah dan bersifat open-source untuk tujuan pembelajaran.
