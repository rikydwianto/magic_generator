<?php
error_reporting(0);
@ini_set('display_errors', '0');
// Nama file dan path
$filename = $_GET['filename'] ?? '';
$cleanup = $_GET['cleanup'] ?? '';

// basic sanitization for header safety
$filename = str_replace(["\0", "\r", "\n"], '', $filename);
$filepath = 'FILE/' . $filename;

function read_env_file($path)
{
    $vars = [];
    if (!is_file($path)) {
        return $vars;
    }
    $lines = @file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines === false) {
        return $vars;
    }
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }
        $pos = strpos($line, '=');
        if ($pos === false) {
            continue;
        }
        $key = trim(substr($line, 0, $pos));
        $val = trim(substr($line, $pos + 1));
        if ($key !== '') {
            $vars[$key] = $val;
        }
    }
    return $vars;
}

function get_env_value($key, $env)
{
    $val = getenv($key);
    if ($val !== false && $val !== '') {
        return $val;
    }
    return $env[$key] ?? '';
}

function run_cleanup($cleanup)
{
    if ($cleanup === '') {
        return;
    }

    $env = read_env_file(__DIR__ . '/.env');
    $dbHost = get_env_value('DB_HOST', $env);
    $dbName = get_env_value('DB_NAME', $env);
    $dbUser = get_env_value('DB_USER', $env);
    $dbPass = get_env_value('DB_PASS', $env);

    if ($dbHost === '' || $dbName === '' || $dbUser === '') {
        return;
    }

    try {
        $pdo = new PDO(
            "mysql:host=$dbHost;dbname=$dbName",
            $dbUser,
            $dbPass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        return;
    }

    try {
        if ($cleanup === 'delin_session') {
            $session = $_GET['session'] ?? '';
            if ($session !== '') {
                $stmt = $pdo->prepare("DELETE FROM deliquency WHERE session = :session");
                $stmt->execute([':session' => $session]);
            }
        } elseif ($cleanup === 'delin_reg_session') {
            $session = $_GET['session'] ?? '';
            if ($session !== '') {
                $stmt = $pdo->prepare("DELETE FROM deliquency_regional WHERE session = :session");
                $stmt->execute([':session' => $session]);
            }
        } elseif ($cleanup === 'delin_tgl_cabang') {
            $tgl_input = $_GET['tgl_input'] ?? '';
            $cabang = $_GET['cabang'] ?? '';
            if ($tgl_input !== '' && $cabang !== '') {
                $stmt = $pdo->prepare("DELETE FROM deliquency WHERE tgl_input = :tgl_input AND cabang = :cabang");
                $stmt->execute([
                    ':tgl_input' => $tgl_input,
                    ':cabang' => $cabang,
                ]);
            }
        }
    } catch (PDOException $e) {
        // ignore cleanup error
    }
}

// Mengecek apakah file ada
if (file_exists($filepath)) {
    // Set header untuk pengalihan dan mengatur nama file
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    // Membaca file dan mengirimkan isinya ke output
    @ob_end_clean();
    readfile($filepath);
    unlink($filepath);

    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        @flush();
    }
    run_cleanup($cleanup);

    exit; // Keluar untuk mencegah konten lainnya ditambahkan
} else {
    echo "File tidak ditemukan.";
}
?>
