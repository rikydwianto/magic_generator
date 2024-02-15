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
            } else if ($menu == 'data_laporan') {
                $data = json_decode(file_get_contents('php://input'), true);
                cekLaporan($pdo, $data);
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