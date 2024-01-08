<?php

$aktifitas = urldecode($_GET['aktifitas']);
$singkatan = urldecode($_GET['singkatan']);
$tahun = $_GET['tahun'];

if (isset($_GET['id'])) {
    $id_target = $_GET['id'];


    $stmt = $pdo->prepare("DELETE FROM target WHERE id = ?");
    $stmt->execute([$id_target]);


    pindah($url . "index.php?menu=index&act=target&submenu=detail_target&aktifitas=" . urlencode($aktifitas) . "&singkatan=" . urlencode($singkatan) . "&tahun=" . $tahun);
} else {
}
