<?php
$id = $_GET['id'];
$bulan = $_GET['bulan'];
$minggu = $_GET['minggu'];
$tahun = $_GET['tahun'];
$status = $_GET['status1'];
if (isset($_GET['status'])) {
    $status = $_GET['status'];
    $update = $pdo->prepare("UPDATE capaian_staff set status=:status where id_capaian_staff=:id  ");
    $update->bindParam(":status", $status);
    $update->bindParam(":id", $id);
    $update = $update->execute();
}
if (isset($_GET['hapus'])) {
    $delete = $pdo->prepare("delete from capaian_staff where id_capaian_staff=:id ;
    delete from detail_capaian_staff where id_capaian_staff=:id ; ");
    $delete->bindParam(":status", $status);
    $delete->bindParam(":id", $id);
    $delete = $delete->execute();
}

$bulan = $_GET['bulan'];
$minggu = $_GET['minggu'];
$tahun = $_GET['tahun'];
$status = $_GET['status1'];
pindah(menu_progress("laporan_regional/laporan_mingguan&status=$status&bulan=$bulan&minggu=$minggu&tahun=$tahun"));