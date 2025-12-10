<?php
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel']['tmp_name'];

    // **1. Buka file Excel**
    $spreadsheet = IOFactory::load($file);
    $sheet = $spreadsheet->getActiveSheet();

    $totalDana = 47345800;
    $minPerOrang = 2000;

    // **2. Hitung jumlah baris dari data yang diinput (baris terakhir yang berisi data)**
    $highestRow = $sheet->getHighestRow(); // Mendapatkan baris terakhir dengan data
    $jumlahOrang = $highestRow - 1; // Karena data mulai dari baris ke-2

    // **3. Pastikan ada data**
    if ($jumlahOrang <= 0) {
        die("File Excel tidak memiliki data yang cukup.");
    }

    // **4. Hitung sisa dana untuk distribusi acak**
    $totalMinimum = $jumlahOrang * $minPerOrang;
    $sisaDana = $totalDana - $totalMinimum;

    // **5. Buat angka acak & total bobot**
    $randomWeights = [];
    $totalWeight = 0;
    for ($i = 2; $i <= $highestRow; $i++) {
        $randomValue = mt_rand(1, 100);
        $randomWeights[$i] = $randomValue;
        $totalWeight += $randomValue;
    }

    // **6. Hitung pembagian dana & update ke Kolom O**
    $totalDistribusi = 0;
    for ($i = 2; $i <= $highestRow; $i++) {
        $bagian = round(($randomWeights[$i] / $totalWeight) * $sisaDana);
        $jumlahAkhir = $bagian + $minPerOrang;

        $sheet->setCellValue('O' . $i, $jumlahAkhir);
        $totalDistribusi += $jumlahAkhir;
    }

    // **7. Koreksi jika total tidak pas**
    $selisih = $totalDana - $totalDistribusi;
    if ($selisih != 0) {
        $sheet->setCellValue('O2', $sheet->getCell('O2')->getValue() + $selisih);
    }

    // **8. Simpan kembali sebagai file baru**
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $newFile = 'output.xlsx';
    $writer->save($newFile);

    echo "File berhasil diproses! <a href='$newFile'>Download hasil</a>";
}
?>

<!-- Form Upload -->
<form action="" method="post" enctype="multipart/form-data">
    <input type="file" name="file_excel" required>
    <button type="submit">Upload & Proses</button>
</form>