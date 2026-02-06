<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/setting.php';

$session = $_GET['session'] ?? '';
if ($session === '') {
    echo "<script>
        if (typeof Swal !== 'undefined') {
            Swal.fire({icon:'warning', title:'Session kosong', text:'Tidak ada session untuk diproses'}).then(() => {
                window.location.href = '" . $url . "index.php?menu=anal_bayar';
            });
        } else {
            alert('Session kosong');
            window.location.href = '" . $url . "index.php?menu=anal_bayar';
        }
    </script>";
    exit;
}

$php = defined('PHP_BINARY') && PHP_BINARY ? PHP_BINARY : 'php';
$script = __DIR__ . '/anal_bayar_exec.php';
$sessionArg = escapeshellarg($session);

if (stripos(PHP_OS, 'WIN') === 0) {
    $cmd = 'start /B "" ' . escapeshellarg($php) . ' ' . escapeshellarg($script) . ' ' . $sessionArg;
} else {
    $cmd = ($php) . ' ' . escapeshellarg($script) . ' ' . $sessionArg . ' > /dev/null 2>&1 &';
}
// echo $cmd;
// echo __DIR__;
shell_exec($cmd);
// exit;
?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Proses Analisa</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<script>
    Swal.fire({
        icon: 'success',
        title: 'Proses Dimulai',
        text: 'Analisa sedang diproses di background.',
        confirmButtonText: 'OK'
    }).then(function() {
        window.location.href = <?= json_encode($url . "index.php?menu=anal_bayar") ?>;
    });
</script>
</body>
</html>
