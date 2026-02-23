<?php
// error_reporting(0);
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../config/setting.php';
require_once __DIR__ . '/../config/koneksi.php';

header('Content-Type: application/json; charset=utf-8');

try {
    $stmt = $pdo->query("SELECT session, cabang, total_files, status, message, created_at, updated_at, output_file FROM exec_analisa_par_multi where session='$sesi' ORDER BY updated_at DESC");
    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $row['created_at_fmt'] = $row['created_at'] ? date('d M Y H:i', strtotime($row['created_at'])) : '';
        $rows[] = $row;
    }
    echo json_encode(['ok' => true, 'data' => $rows]);
} catch (PDOException $e) {
    echo json_encode(['ok' => false, 'error' => 'db']);
}
?>
