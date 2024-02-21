<?php
header("Access-Control-Allow-Origin: *");
// header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
// header("Access-Control-Allow-Headers: Content-Type, Authorization");
header('Content-Type: application/json');


require './../../vendor/autoload.php';
require './../../proses/global_fungsi.php';
include_once "./../../config/setting.php";
include_once "./../../config/koneksi.php";
require_once __DIR__ . '/proses/proses.php';



$method = $_SERVER['REQUEST_METHOD'];
$menu  = $_GET['action'];

$headers = apache_request_headers();
$token = explode("Bearer ", $headers['authorization']  ?? $headers['Authorization'])[1];
// var_dump($_SERVER);


// Menampilkan hasil verifikasi
if ($token == $secretKey) {

    switch ($method) {
        case 'GET':
            if ($menu == 'detail_capaian') {
                $id = $_GET['id'];
                detailCapaian($pdo, $id);
            } else  if ($menu == 'laporan_staff') {
                $id = $_GET['nik'];
                laporanPerStaff($pdo, $id);
            } else  if ($menu == 'laporan_cabang') {
                $cabang = $_GET['cabang'];
                $id = $_GET['id_staff'];
                laporanPerCabang($pdo, $cabang, $id);
            } else  if ($menu == 'cabang') {
                $id = $_GET['id_staff'];
                cariCabang($pdo, $id);
            }



            break;
        case 'DELETE':
            if ($menu == 'hapus_laporan') {
                $id = $_GET['id'];
                hapusLaporan($pdo, $id);
            } else if ($menu == 'hapus_laporan_cek') {
                $id = $_GET['id'];
                $cabang = $_GET['cabang'];
                $minggu = $_GET['minggu'];
                $bulan = $_GET['bulan'];
                $tahun = $_GET['tahun'];

                hapusLaporanCek($pdo, $id, $cabang, $minggu, $bulan, $tahun);
            }
            break;
        case 'PUT':
            if ($menu == 'update_ttd') {
                $data = json_decode(file_get_contents('php://input'), true);
                updateTTD($pdo, $data);
            } else if ($menu == 'update_capaian') {
                $data = json_decode(file_get_contents('php://input'), true);
                // echo json_encode($data);
                updateCapaian($pdo, $data);
            } else if ($menu == 'proses_approval') {
                $data = json_decode(file_get_contents('php://input'), true);
                // echo json_encode($data);
                prosesApproval($pdo, $data);
            } else if ($menu == 'updateFCM') {
                $data = json_decode(file_get_contents('php://input'), true);
                // echo json_encode($data);
                updateFCMToken($pdo, $data);
            }

            break;
        case 'POST':
            if ($menu == 'login_staff') {
                $data = json_decode(file_get_contents('php://input'), true);
                $nik = $data['nik'];
                $password = $data['password'];
                loginStaff($pdo, $nik, $password);
            } else if ($menu == 'login_admin') {
                $data = json_decode(file_get_contents('php://input'), true);
                $nik = $data['nik'];
                $password = $data['password'];
                loginAdmin($pdo, $nik, $password);
            } else if ($menu == 'ambil_staff') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id_staff'];

                DetailSL($pdo, $id);
            } else if ($menu == 'ambil_admin') {
                $data = json_decode(file_get_contents('php://input'), true);
                $id = $data['id_staff'];

                DetailAdmin($pdo, $id);
            } else if ($menu == 'data_laporan') {
                $data = json_decode(file_get_contents('php://input'), true);
                cekLaporan($pdo, $data);
            } else if ($menu == 'cek_progress_cabang') {
                $data = json_decode(file_get_contents('php://input'), true);
                cekProgresCabang($pdo, $data);
            }


            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Metode tidak diizinkan']);
            break;
    }
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}