<?php
// Script untuk menghasilkan gambar PNG dari teks ASCII

// Baca file teks ASCII
$ascii_text = file_get_contents(__DIR__ . '/chatcare_workflow.txt');
$lines = explode("\n", $ascii_text);

// Tentukan ukuran font dan gambar
$font_file = __DIR__ . '/arial.ttf'; // Jika tidak ada, akan menggunakan font default
$font_size = 12;
$line_height = $font_size + 4;
$padding = 20;

// Hitung ukuran gambar
$max_width = 0;
foreach ($lines as $line) {
    $box = imagettfbbox($font_size, 0, $font_file, $line);
    $width = $box[2] - $box[0];
    $max_width = max($max_width, $width);
}

// Jika imagettfbbox gagal, gunakan pendekatan alternatif
if ($max_width <= 0) {
    $max_width = 0;
    foreach ($lines as $line) {
        $max_width = max($max_width, strlen($line) * 7); // Perkiraan 7 piksel per karakter
    }
}

$width = $max_width + ($padding * 2);
$height = count($lines) * $line_height + ($padding * 2);

// Buat gambar
$image = imagecreatetruecolor($width, $height);
$bg_color = imagecolorallocate($image, 255, 255, 255); // Putih
$text_color = imagecolorallocate($image, 0, 0, 0); // Hitam
$title_color = imagecolorallocate($image, 0, 102, 204); // Biru
$highlight_color = imagecolorallocate($image, 255, 102, 0); // Oranye

// Isi background
imagefilledrectangle($image, 0, 0, $width, $height, $bg_color);

// Tambahkan judul
$title = "ChatCare System Workflow";
$title_font_size = 20;
$title_y = $padding + $title_font_size;

// Coba gunakan imagettftext jika font tersedia
if (function_exists('imagettftext') && file_exists($font_file)) {
    imagettftext($image, $title_font_size, 0, $padding, $title_y, $title_color, $font_file, $title);
} else {
    // Fallback ke imagestring jika imagettf tidak tersedia
    imagestring($image, 5, $padding, $padding, $title, $title_color);
    $title_y = $padding + 20; // Sesuaikan untuk font default
}

// Tambahkan teks ASCII
$y = $title_y + 20; // Mulai di bawah judul

foreach ($lines as $line) {
    // Deteksi judul bagian untuk diwarnai berbeda
    $color = $text_color;
    if (strpos($line, 'WORKFLOW:') !== false || 
        strpos($line, 'USER SENDS MESSAGE:') !== false ||
        strpos($line, 'POLLING FOR NEW MESSAGES:') !== false ||
        strpos($line, 'AUTO-REPLY MECHANISM:') !== false ||
        strpos($line, 'DUPLICATE PREVENTION:') !== false) {
        $color = $highlight_color;
    }
    
    // Coba gunakan imagettftext jika font tersedia
    if (function_exists('imagettftext') && file_exists($font_file)) {
        imagettftext($image, $font_size, 0, $padding, $y, $color, $font_file, $line);
    } else {
        // Fallback ke imagestring jika imagettf tidak tersedia
        imagestring($image, 3, $padding, $y - $font_size, $line, $color);
    }
    
    $y += $line_height;
}

// Simpan gambar
imagepng($image, __DIR__ . '/chatcare_workflow.png');
imagedestroy($image);

echo "Gambar berhasil dibuat: " . __DIR__ . '/chatcare_workflow.png';
