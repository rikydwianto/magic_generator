<?php
// Nama file dan path
$filename = $_GET['filename'];
$filepath = 'FILE/' . $filename;

// Mengecek apakah file ada
if (file_exists($filepath)) {
    // Set header untuk pengalihan dan mengatur nama file
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    // Membaca file dan mengirimkan isinya ke output
    readfile($filepath);
    // unlink($filepath);

    exit; // Keluar untuk mencegah konten lainnya ditambahkan
} else {
    echo "File tidak ditemukan.";
}