<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
require './../vendor/autoload.php'; // Impor library Dotenv
require './../proses/global_fungsi.php';
include_once "./../config/setting.php";
include_once "./../config/koneksi.php";
header('Content-Type: application/json');
if (isset($_GET['ambil_data'])) {
    // Query to select data from the 'nik_undi' table
    $sql = "SELECT nik, nama FROM nik_undi where (dapat is null or dapat ='') order by RAND()";
    $result = $pdo->query($sql);

    // Check if there is data
    if ($result) {
        // Fetch data as an associative array
        $data = [];
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $data[] = $row;
        }

        // Send data as JSON response

        echo json_encode($data);
    } else {
        // No data found
        echo "No data found";
    }
}

if (isset($_GET['update_dapat'])) {
    $nik = $_GET['nik'];
    $queryUpdate = "UPDATE nik_undi SET dapat = 'dapat' WHERE nik = :nik";
    $resultUpdate = $pdo->prepare($queryUpdate);
    $resultUpdate->bindParam(":nik", $nik);
    if ($resultUpdate->execute()) {
        $response = array('status' => 'success', 'message' => 'Data berhasil diupdate.');
    } else {
        $response = array('status' => 'error', 'message' => 'Gagal melakukan update.');
    }
    echo json_encode($response);
}

if (isset($_GET['ambil_nik'])) {
    $nik = $_GET['nik'];
    $queryUpdate = "SELECT * from nik_undi  WHERE nik = :nik";
    $resultUpdate = $pdo->prepare($queryUpdate);
    $resultUpdate->bindParam(":nik", $nik);
    $resultUpdate->execute();
    $encode = $resultUpdate->fetch(PDO::FETCH_ASSOC);
    $response = array('status' => 'success', 'data' => $encode);
    echo json_encode($response);
}
