<?php
if (($_SESSION['jenisAkun'] ?? '') !== 'superuser') {
    echo "Akses ditolak.";
    return;
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || !isset($_GET['id'])) {
    echo "Invalid Request.";
    return;
}

$id = (int)$_GET['id'];
if ($id <= 0) {
    alert("ID tidak valid");
    pindah($url . "index.php?menu=index&act=cabang_blockir");
    return;
}

try {
    $stmt = $pdo->prepare("DELETE FROM block WHERE id = ?");
    $stmt->execute([$id]);
    if ($stmt->rowCount() > 0) {
        alert("Cabang berhasil dihapus dari daftar blokir");
    } else {
        alert("Cabang tidak ditemukan di daftar blokir");
    }
    pindah($url . "index.php?menu=index&act=cabang_blockir");
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
