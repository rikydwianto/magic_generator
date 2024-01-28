<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');

require './../vendor/autoload.php';
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";

if (isset($_GET['id'])) {
    $id_soal = $_GET['id'];
    $ket = $_GET['ket'];

    $q = $pdo->prepare("SELECT url_gambar,soal from $ket where id_soal=:id_soal");
    $q->bindParam(":id_soal", $id_soal);
    $q->execute();
    $hasil = $q->fetch();
    if ($hasil) {
        $url_gambar =  $hasil['url_gambar'] ? $hasil['url_gambar'] : "dafdadad";

        $localImagePath = "./../assets/img/soal/$url_gambar";
        $url_assets = $url . 'assets/img/soal/';
        if (file_exists($localImagePath)) {
            $url_gambar = $url_assets . $url_gambar;
        } else {
            // echo "gakbar tidak ada";

            if (filter_var($url_gambar, FILTER_VALIDATE_URL)) {
                // Mendapatkan tipe MIME dari URL gambar
                $ext = getImageTypeFromUrl($url_gambar);
                if ($ext) {
                    $url_gambar = $url_gambar;
                } else $url_gambar = '';
            } else {
                $url_gambar = '';
            }
        }
        echo json_encode(array('hasil' => array('soal' => $hasil['soal'], 'url_gambar' => $url_gambar)));
    } else {
        echo json_encode(array('error' => 'tidak ada data'));
    }
}
