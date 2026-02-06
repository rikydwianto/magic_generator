<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/setting.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../global_fungsi.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

function log_exec_error($message, $session = '')
{
    $log_file = __DIR__ . '/anal_bayar_exec.log';
    $prefix = date('Y-m-d H:i:s');
    $sess = $session !== '' ? " [session:$session]" : "";
    $line = $prefix . $sess . " " . $message . PHP_EOL;
    @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}
file_put_contents('anal_bayar_exec.log', date('Y-m-d H:i:s') . " - Proses insert to DB started\n", FILE_APPEND);

function update_exec_status($pdo, $sesi, $file_name, $status, $message = null, $cabang = null, $tgl_input = null)
{
    try {
        $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi 
            SET status = :status, message = :message, cabang = :cabang, tgl_input = :tgl_input 
            WHERE session = :session AND file_name = :file_name");
        $stmt->execute([
            ':status' => $status,
            ':message' => $message,
            ':cabang' => $cabang,
            ':tgl_input' => $tgl_input,
            ':session' => $sesi,
            ':file_name' => $file_name,
        ]);
    } catch (PDOException $e) {
        // ignore logging error
    }
}

function process_deliquency_from_file($pdo, $path, $sesi)
{
    try {
        $reader = IOFactory::createReaderForFile($path);
        $objek = $reader->load($path);
    } catch (Exception $e) {
        log_exec_error("Gagal membaca file: " . basename($path) . " | " . $e->getMessage(), $sesi);
        return ['ok' => false, 'message' => 'Gagal membaca file'];
    }

    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s+([A-Z\s]+?)As/';
    preg_match($regex, $text, $matches);
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);

    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin = $tgl[0] ?? '';

    if ($namaCabang === '' || $tgl_delin === '') {
        log_exec_error("Format file tidak sesuai: " . basename($path), $sesi);
        return ['ok' => false, 'message' => 'Format file tidak sesuai'];
    }

    try {
        $pdo->beginTransaction();
        $delete_stmt = $pdo->prepare("DELETE FROM deliquency WHERE tgl_input = :tgl_input AND cabang = :cabang");
        $delete_stmt->execute([
            ':tgl_input' => $tgl_delin,
            ':cabang' => $namaCabang,
        ]);

        $insert_sql = "INSERT INTO deliquency (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session, jenis_topup)
            VALUES (:loan, :no_center, :id_detail_nasabah, :nasabah, :amount, :sisa_saldo, :tunggakan, :minggu, :tgl_input, :id_cabang, :tgl_disburse, :cabang, :wajib, :sukarela, :pensiun, :hariraya, :lainlain, :cicilan, :hari, :staff, :minggu_ke, :minggu_rill, :priode, :kode_pemb, :session, :jenis_topup)";
        $insert_stmt = $pdo->prepare($insert_sql);

        for ($row = 3; $row <= $last_row; $row++) {
            $no_center =  floatval(ganti_karakter($ws->getCell("C" . $row)->getValue()));
            if ($no_center > 0) {
                $loan_no = ganti_karakter($ws->getCell("B" . $row)->getValue());
                $client_id = ganti_karakter1($ws->getCell("D" . $row)->getValue());
                $nama_nasabah = ganti_karakter($ws->getCell("E" . $row)->getValue());
                $jenis_produk = ganti_karakter($ws->getCell("G" . $row)->getValue());
                $disburse = floatval(ganti_karakter($ws->getCell("H" . $row)->getValue()));
                $jk = ganti_karakter($ws->getCell("I" . $row)->getValue());
                $balance = floatval(ganti_karakter($ws->getCell("M" . $row)->getValue()));
                $arreas = floatval(ganti_karakter($ws->getCell("N" . $row)->getValue()));
                $wpd = floatval(ganti_karakter($ws->getCell("O" . $row)->getValue()));
                $tgl_dis_raw = $ws->getCell("J" . $row)->getValue();
                if (is_numeric($tgl_dis_raw)) {
                    $tgl_dis = date("Y-m-d", Date::excelToDateTimeObject($tgl_dis_raw)->getTimestamp());
                } else {
                    $tgl_dis = date("Y-m-d", strtotime($tgl_dis_raw));
                }

                $s_wajib =  floatval(ganti_karakter($ws->getCell("U" . $row)->getValue()));
                $s_sukarela =  floatval(ganti_karakter($ws->getCell("V" . $row)->getValue()));
                $s_pensiun =  floatval(ganti_karakter($ws->getCell("W" . $row)->getValue()));
                $s_hariraya =  floatval(ganti_karakter($ws->getCell("X" . $row)->getValue()));
                $s_khusus =  floatval(ganti_karakter($ws->getCell("Y" . $row)->getValue()));
                $s_qurban =  floatval(ganti_karakter($ws->getCell("Z" . $row)->getValue()));
                $s_sipadan =  floatval(ganti_karakter($ws->getCell("AA" . $row)->getValue()));

                $angsuran =  floatval(ganti_karakter($ws->getCell("AB" . $row)->getValue()));
                $rill =  floatval(ganti_karakter($ws->getCell("AD" . $row)->getValue()));
                $ke =  floatval(ganti_karakter($ws->getCell("AC" . $row)->getValue()));
                $hari =  ganti_karakter($ws->getCell("AF" . $row)->getValue());
                $staff =  ganti_karakter1($ws->getCell("AG" . $row)->getValue());
                $jenis_topup =  ganti_karakter($ws->getCell("AH" . $row)->getValue());

                $nama_nasabah = str_replace("'", " ", $nama_nasabah);
                $insert_stmt->execute([
                    ':loan' => $loan_no,
                    ':no_center' => $no_center,
                    ':id_detail_nasabah' => $client_id,
                    ':nasabah' => $nama_nasabah,
                    ':amount' => $disburse,
                    ':sisa_saldo' => $balance,
                    ':tunggakan' => $arreas,
                    ':minggu' => $wpd,
                    ':tgl_input' => $tgl_delin,
                    ':id_cabang' => '',
                    ':tgl_disburse' => $tgl_dis,
                    ':cabang' => $namaCabang,
                    ':wajib' => $s_wajib,
                    ':sukarela' => $s_sukarela,
                    ':pensiun' => $s_pensiun,
                    ':hariraya' => $s_hariraya,
                    ':lainlain' => 0,
                    ':cicilan' => $angsuran,
                    ':hari' => $hari,
                    ':staff' => $staff,
                    ':minggu_ke' => $ke,
                    ':minggu_rill' => $rill,
                    ':priode' => $jk,
                    ':kode_pemb' => $jenis_produk,
                    ':session' => $sesi,
                    ':jenis_topup' => $jenis_topup,
                ]);
            }
        }

        $pdo->commit();
    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        log_exec_error("Gagal menyimpan data dari file: " . basename($path) . " | " . $e->getMessage(), $sesi);
        return ['ok' => false, 'message' => 'Gagal menyimpan data'];
    }

    return ['ok' => true, 'cabang' => $namaCabang, 'tgl' => $tgl_delin];
}

$sesi = $_GET['session'] ?? ($_SESSION['sesi'] ?? '');
if (php_sapi_name() === 'cli') {
    $sesi = $argv[1] ?? $sesi;
}
$folder = __DIR__ . '/../../FILE/' . $sesi;

$processed = 0;
$errors = [];

$row = null;
if ($sesi !== '') {
    $stmt = $pdo->prepare("SELECT * FROM exec_analisa_par_multi WHERE session = :session LIMIT 1");
    $stmt->execute([':session' => $sesi]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($sesi === '' || !$row) {
    $errors[] = 'Session tidak ditemukan';
    log_exec_error('Session tidak ditemukan', $sesi);
} else {
    if (!is_dir($folder)) {
        $errors[] = 'Folder upload tidak ditemukan';
        log_exec_error('Folder upload tidak ditemukan: ' . $folder, $sesi);
    } else {
        $file_list = json_decode($row['file_list'] ?? '[]', true);
        if (!is_array($file_list) || count($file_list) === 0) {
            $errors[] = 'Tidak ada file yang siap diproses';
            log_exec_error('Tidak ada file yang siap diproses', $sesi);
        } else {
            try {
                $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET status = 'processing', message = 'Memulai proses insert' WHERE session = :session");
                $stmt->execute([':session' => $sesi]);
            } catch (PDOException $e) {
                // ignore
            }

            $total = count($file_list);
            $idx = 0;
            foreach ($file_list as $file_name) {
                $idx++;
                $path = $folder . '/' . $file_name;
                if (!file_exists($path)) {
                    $errors[] = $file_name . ': file tidak ditemukan';
                    log_exec_error($file_name . ': file tidak ditemukan', $sesi);
                    continue;
                }

                try {
                    $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET message = :message WHERE session = :session");
                    $stmt->execute([
                        ':message' => "Memproses $idx/$total: $file_name",
                        ':session' => $sesi,
                    ]);
                } catch (PDOException $e) {
                    // ignore
                }

                $result = process_deliquency_from_file($pdo, $path, $sesi);
                if (!empty($result['ok'])) {
                    $processed++;
                    // unlink($path);
                } else {
                    $errors[] = $file_name . ': ' . ($result['message'] ?? 'Gagal diproses');
                    log_exec_error($file_name . ': ' . ($result['message'] ?? 'Gagal diproses'), $sesi);
                }
            }

            $final_status = empty($errors) ? 'done' : 'failed';
            $final_message = empty($errors) ? "Selesai insert $processed file" : "Selesai insert dengan error (" . count($errors) . ")";
            try {
                $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET status = :status, message = :message WHERE session = :session");
                $stmt->execute([
                    ':status' => $final_status,
                    ':message' => $final_message,
                    ':session' => $sesi,
                ]);
            } catch (PDOException $e) {
                // ignore
            }
        }
    }
}

$message = $processed > 0 ? "Berhasil memproses $processed file." : "Tidak ada file yang berhasil diproses.";
if (!empty($errors)) {
    $message .= "\\n" . implode("\\n", $errors);
}

if (php_sapi_name() === 'cli') {
    echo $message;
    exit;
}

?>
