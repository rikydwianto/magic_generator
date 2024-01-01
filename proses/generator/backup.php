<?php
if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['submenu'])) {
    try {
        // Buat koneksi ke database menggunakan PDO
        $result = $pdo->query("SHOW TABLES");
        $tables = $result->fetchAll(PDO::FETCH_COLUMN);
        $waktu = date("Y-m-d h-i-s");
        // Buat file SQL
        $filename = $waktu . ' - ' . "generator-all-table.sql";
        $file_name = __DIR__ . "/../../FILE/sql/$filename";
        $sql_content = "-- Backup untuk semua tabel dalam database \n\n";

        // Loop melalui setiap tabel
        foreach ($tables as $table) {
            // Dapatkan struktur tabel
            $result = $pdo->query("SHOW CREATE TABLE $table");
            $row = $result->fetch(PDO::FETCH_ASSOC);
            $table_structure = $row['Create Table'];

            // Dapatkan data tabel
            $result = $pdo->query("SELECT * FROM $table");
            $data = $result->fetchAll(PDO::FETCH_ASSOC);

            // Tambahkan struktur dan data ke dalam file SQL
            $sql_content .= "-- Struktur Tabel $table\n\n$table_structure;\n\n-- Data Tabel $table\n";
            foreach ($data as $row) {
                $values = implode("', '", $row);
                $sql_content .= "INSERT INTO $table VALUES ('$values');\n";
            }

            $sql_content .= "\n";
        }

        // Simpan file SQL
        file_put_contents($file_name, $sql_content);


        echo "Backup untuk semua tabel berhasil. File SQL telah dibuat ";
        pindah($url . "download.php?filename=sql/" . $filename);
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}
