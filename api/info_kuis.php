<?php
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Mendapatkan data dari AJAX
    $unik = $_POST["data"];
    $id_kuis = $_POST["id_kuis"];
    try {

        // Pernyataan SQL untuk mencari data berdasarkan unique_id
        $sql = "SELECT * FROM kuis_jawab WHERE unique_id = :unique_id and id_kuis=:id_kuis";
        // echo $unik;
        // Persiapkan dan jalankan pernyataan SQL
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':unique_id', $unik, PDO::PARAM_STR);
        $stmt->bindParam(':id_kuis', $id_kuis, PDO::PARAM_STR);
        $stmt->execute();

        // Ambil hasil pencarian
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Mengubah hasil ke format JSON
        $jsonResult = json_encode($result);

        // Menampilkan hasil sebagai respons JSON
        header('Content-Type: application/json');
        echo $jsonResult;
    } catch (PDOException $e) {
        // Menampilkan pesan kesalahan
        echo json_encode(array('error' => $e->getMessage()));
    }
}
