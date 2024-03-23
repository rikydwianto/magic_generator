<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");
if (isset($_GET['akses']) && $_GET['akses'] == 'android') {
    if (isset($_GET['key']) && $_GET['key'] == $secretKey) {
        $id_login = base64_decode($_GET['id_login']);

        $stmt = $pdo->prepare("SELECT users.*, cabang.*
                                        FROM users
                                        JOIN cabang ON users.id_cabang = cabang.id_cabang
                                        WHERE users.id = ?;
                                        ");
        $stmt->execute([$id_login]);
        $detailAkun = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($detailAkun) {
            $_SESSION["idLogin"] = ($detailAkun["id"]);
            $_SESSION["jenisAkun"] = ($detailAkun["jenis_akun"]);
            $_SESSION["id_cabang"] = ($detailAkun["id_cabang"]);
            $_SESSION["jabatan"] = ($detailAkun["jabatan"]);
            $_SESSION["regional"] = ($detailAkun["regional"]);
            pindah($url . "progress.php?menu=index");
        } else {
            echo "<h1>DATA TIDAK DITEMUKAN!</h1>";
        }
    }
}