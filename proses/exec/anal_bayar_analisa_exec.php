<?php
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../config/setting.php';
require_once __DIR__ . '/../../config/koneksi.php';
require_once __DIR__ . '/../global_fungsi.php';

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

function log_exec_error($message, $session = '')
{
    $log_file = __DIR__ . '/anal_bayar_exec.log';
    $prefix = date('Y-m-d H:i:s');
    $sess = $session !== '' ? " [session:$session]" : "";
    $line = $prefix . $sess . " " . $message . PHP_EOL;
    @file_put_contents($log_file, $line, FILE_APPEND | LOCK_EX);
}

function parse_cabang_tgl($path, $session)
{
    try {
        $reader = IOFactory::createReaderForFile($path);
        $objek = $reader->load($path);
    } catch (Exception $e) {
        log_exec_error("Gagal membaca file: " . basename($path) . " | " . $e->getMessage(), $session);
        return ['ok' => false, 'message' => 'Gagal membaca file'];
    }

    $ws = $objek->getActiveSheet();
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s+([A-Z\s]+?)As/';
    preg_match($regex, $text, $matches);
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);
    $namaCabang = trim($namaCabang);

    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin = $tgl[0] ?? '';

    if ($namaCabang === '' || $tgl_delin === '') {
        log_exec_error("Format file tidak sesuai: " . basename($path), $session);
        return ['ok' => false, 'message' => 'Format file tidak sesuai'];
    }

    return ['ok' => true, 'cabang' => $namaCabang, 'tgl' => $tgl_delin];
}

function safe_filename($text)
{
    $text = preg_replace('/\s+/', '_', $text);
    $text = preg_replace('/[^A-Za-z0-9_\-]/', '', $text);
    return trim($text, '_');
}

function to_number($val)
{
    if ($val === null) {
        return 0;
    }
    if (is_int($val) || is_float($val)) {
        return $val;
    }
    $str = (string)$val;
    $str = preg_replace('/[^0-9.\-]/', '', $str);
    if ($str === '' || $str === '-' || $str === '.') {
        return 0;
    }
    return (float)$str;
}

$session = $_GET['session'] ?? ($_SESSION['sesi'] ?? '');
if (php_sapi_name() === 'cli') {
    $session = $argv[1] ?? $session;
}

$processed = 0;
$errors = [];
$output_file = '';
$delete_warning = '';

$row = null;
if ($session !== '') {
    $stmt = $pdo->prepare("SELECT * FROM exec_analisa_par_multi WHERE session = :session LIMIT 1");
    $stmt->execute([':session' => $session]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($session === '' || !$row) {
    $errors[] = 'Session tidak ditemukan';
    log_exec_error('Session tidak ditemukan', $session);
} else {
    $status = $row['status'] ?? '';
    if ($status !== 'done' && $status !== 'failed') {
        $errors[] = 'Status belum siap untuk analisa';
        log_exec_error('Status belum siap untuk analisa: ' . $status, $session);
    } else {
        try {
            $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET status = 'proses_analisa', message = 'Memulai proses analisa' WHERE session = :session");
            $stmt->execute([':session' => $session]);
        } catch (PDOException $e) {
            // ignore
        }

        $file_list = json_decode($row['file_list'] ?? '[]', true);
        if (!is_array($file_list) || count($file_list) === 0) {
            $errors[] = 'Tidak ada file yang siap dianalisa';
            log_exec_error('Tidak ada file yang siap dianalisa', $session);
        } else {
            $folder = __DIR__ . '/../../FILE/' . $session;
            if (!is_dir($folder)) {
                $errors[] = 'Folder upload tidak ditemukan';
                log_exec_error('Folder upload tidak ditemukan: ' . $folder, $session);
            } else {
                $total = count($file_list);
                $idx = 0;
                $tgl_list = [];
                $cabang_set = $row['cabang'] ?? '';
                foreach ($file_list as $file_name) {
                    $idx++;
                    $path = $folder . '/' . $file_name;
                    if (!file_exists($path)) {
                        $errors[] = $file_name . ': file tidak ditemukan';
                        log_exec_error($file_name . ': file tidak ditemukan', $session);
                        continue;
                    }

                    try {
                        $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET message = :message WHERE session = :session");
                        $stmt->execute([
                            ':message' => "Analisa $idx/$total: $file_name",
                            ':session' => $session,
                        ]);
                    } catch (PDOException $e) {
                        // ignore
                    }

                    $header = parse_cabang_tgl($path, $session);
                    if (empty($header['ok'])) {
                        $errors[] = $file_name . ': ' . ($header['message'] ?? 'Format tidak sesuai');
                        continue;
                    }
                    $tgl_list[] = $header['tgl'];
                    if ($cabang_set === '' && !empty($header['cabang'])) {
                        $cabang_set = $header['cabang'];
                    }

                    try {
                        $stmt = $pdo->prepare("SELECT COUNT(*) FROM deliquency WHERE tgl_input = :tgl_input AND cabang = :cabang AND session = :session");
                        $stmt->execute([
                            ':tgl_input' => $header['tgl'],
                            ':cabang' => $header['cabang'],
                            ':session' => $session,
                        ]);
                        $count = (int)$stmt->fetchColumn();
                    } catch (PDOException $e) {
                        $count = 0;
                        log_exec_error('Gagal cek data: ' . $file_name . ' | ' . $e->getMessage(), $session);
                    }

                    if ($count <= 0) {
                        $errors[] = $file_name . ': data tidak ditemukan di DB';
                        log_exec_error($file_name . ': data tidak ditemukan di DB', $session);
                        continue;
                    }

                    $processed++;
                }

                if (empty($errors)) {
                    $tgl_list = array_values(array_unique($tgl_list));
                    sort($tgl_list);
                    $tgl_awal = $tgl_list[0] ?? '';
                    $tgl_akhir = $tgl_list[count($tgl_list) - 1] ?? '';

                    if ($tgl_awal === '' || $tgl_akhir === '') {
                        $errors[] = 'Tanggal awal/akhir tidak ditemukan';
                        log_exec_error('Tanggal awal/akhir tidak ditemukan', $session);
                    } else {
                        try {
                            $placeholders = implode(',', array_fill(0, count($tgl_list), '?'));
                            $sql = "SELECT loan, no_center, id_detail_nasabah, nasabah, kode_pemb, priode, minggu_rill, amount, tgl_input, sisa_saldo, sukarela
                                    FROM deliquency
                                    WHERE session = ?
                                      AND cabang = ?
                                      AND tgl_input IN ($placeholders)
                                    ORDER BY tgl_input ASC, staff ASC, no_center ASC, loan ASC";
                            $params = array_merge([$session, $cabang_set], $tgl_list);
                            $stmt = $pdo->prepare($sql);
                            $stmt->execute($params);
                            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        } catch (PDOException $e) {
                            $rows = [];
                            $errors[] = 'Gagal mengambil data untuk analisa';
                            log_exec_error('Gagal mengambil data untuk analisa: ' . $e->getMessage(), $session);
                        }

                        if (empty($rows)) {
                            $errors[] = 'Data analisa kosong';
                            log_exec_error('Data analisa kosong', $session);
                        } else {
                            $data_map = [];
                            foreach ($rows as $r) {
                                $loan = $r['loan'] ?? '';
                                if ($loan === '') {
                                    continue;
                                }
                                if (!isset($data_map[$loan])) {
                                    $data_map[$loan] = [
                                        'meta' => [],
                                        'balance' => [],
                                        'sukarela' => [],
                                    ];
                                }
                                if (($r['tgl_input'] ?? '') === $tgl_awal) {
                                    $data_map[$loan]['meta'] = [
                                        'loan' => $r['loan'] ?? '',
                                        'no_center' => $r['no_center'] ?? '',
                                        'id_detail_nasabah' => $r['id_detail_nasabah'] ?? '',
                                        'nasabah' => $r['nasabah'] ?? '',
                                        'kode_pemb' => $r['kode_pemb'] ?? '',
                                        'priode' => $r['priode'] ?? '',
                                        'minggu_rill' => $r['minggu_rill'] ?? '',
                                        'amount' => $r['amount'] ?? 0,
                                    ];
                                }
                                $tgl_key = $r['tgl_input'] ?? '';
                                if ($tgl_key !== '') {
                                    $data_map[$loan]['balance'][$tgl_key] = (float)($r['sisa_saldo'] ?? 0);
                                    $data_map[$loan]['sukarela'][$tgl_key] = (float)($r['sukarela'] ?? 0);
                                }
                            }

                            $spreadsheet = new Spreadsheet();
                            $sheet = $spreadsheet->getActiveSheet();
                            $sheet->setTitle('ANALISA');

                            $judul = "ANALISA PAR BAYAR CABANG $cabang_set\nPER $tgl_awal s/d $tgl_akhir";
                            $sheet->setCellValue('A1', $judul);
                            $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
                            $sheet->getStyle('A1')->getAlignment()
                                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER)
                                ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER)
                                ->setWrapText(true);
                            $sheet->getRowDimension(1)->setRowHeight(45);

                            $headers = [
                                'NO',
                                'LOANNO',
                                'CTR',
                                'ID',
                                'NAMA ANGGOTA',
                                'PRODUK',
                                'JK WAKTU',
                                'RILL',
                                'DISBURSE',
                            ];
                            $tgl_columns = $tgl_list;
                            array_shift($tgl_columns);
                            $headers[] = "TOTAL BALANCE $tgl_awal";
                            foreach ($tgl_columns as $tgl_col) {
                                $headers[] = "TOTAL BALANCE $tgl_col";
                                $headers[] = "DIFF TOTAL BALANCE $tgl_col";
                            }
                            $headers[] = "SUKARELA $tgl_awal";
                            foreach ($tgl_columns as $tgl_col) {
                                $headers[] = "SUKARELA $tgl_col";
                                $headers[] = "DIFF SUKARELA $tgl_col";
                            }
                            $col = 'A';
                            foreach ($headers as $header) {
                                $sheet->setCellValue($col . '2', $header);
                                $col++;
                            }
                            $last_col = Coordinate::stringFromColumnIndex(count($headers));
                            $sheet->mergeCells('A1:' . $last_col . '1');
                            $sheet->getStyle('A2:' . $last_col . '2')->getFont()->setBold(true);
                            $sheet->setAutoFilter('A2:' . $last_col . '2');

                            $no = 1;
                            $row_num = 3;
                            foreach ($data_map as $loan => $info) {
                                if (empty($info['meta'])) {
                                    continue;
                                }
                                $meta = $info['meta'];
                                $bal_map = $info['balance'];
                                $suk_map = $info['sukarela'];

                                $balance_awal = (float)($bal_map[$tgl_awal] ?? 0);
                                $sukarela_awal = (float)($suk_map[$tgl_awal] ?? 0);

                                $col_idx = 1;
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $no++);
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $meta['loan'] ?? '');
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $meta['no_center'] ?? '');
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $meta['id_detail_nasabah'] ?? '');
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $meta['nasabah'] ?? '');
                                $sheet->setCellValueByColumnAndRow($col_idx++, $row_num, $meta['kode_pemb'] ?? '');
                                $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($meta['priode'] ?? 0), DataType::TYPE_NUMERIC);
                                $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($meta['minggu_rill'] ?? 0), DataType::TYPE_NUMERIC);
                                $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($meta['amount'] ?? 0), DataType::TYPE_NUMERIC);

                                $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($balance_awal), DataType::TYPE_NUMERIC);
                                $prev_balance = $balance_awal;
                                foreach ($tgl_columns as $tgl_col) {
                                    $bal = (float)($bal_map[$tgl_col] ?? 0);
                                    $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($bal), DataType::TYPE_NUMERIC);
                                    $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($bal - $prev_balance), DataType::TYPE_NUMERIC);
                                    $prev_balance = $bal;
                                }

                                $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($sukarela_awal), DataType::TYPE_NUMERIC);
                                $prev_sukarela = $sukarela_awal;
                                foreach ($tgl_columns as $tgl_col) {
                                    $suk = (float)($suk_map[$tgl_col] ?? 0);
                                    $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($suk), DataType::TYPE_NUMERIC);
                                    $sheet->setCellValueExplicitByColumnAndRow($col_idx++, $row_num, to_number($suk - $prev_sukarela), DataType::TYPE_NUMERIC);
                                    $prev_sukarela = $suk;
                                }
                                $row_num++;
                            }

                            $total_cols = count($headers);
                            for ($i = 1; $i <= $total_cols; $i++) {
                                $col = Coordinate::stringFromColumnIndex($i);
                                $sheet->getColumnDimension($col)->setAutoSize(true);
                            }

                            $last_data_row = $row_num - 1;
                            if ($last_data_row >= 3) {
                                $start_col = Coordinate::stringFromColumnIndex(7); // JK WAKTU
                                $sheet->getStyle($start_col . '3:' . $last_col . $last_data_row)
                                    ->getNumberFormat()
                                    ->setFormatCode('#,##0');
                            }

                            $output_dir = __DIR__ . '/../../FILE/' . $session . '/';
                            if (!is_dir($output_dir)) {
                                @mkdir($output_dir, 0777, true);
                            }
                            $safe_cabang = $cabang_set !== '' ? safe_filename($cabang_set) : 'CABANG';
                            $filename = "ANALISA_PAR_BAYAR_{$safe_cabang}_{$tgl_awal}_{$tgl_akhir}.xlsx";
                            $filepath = $output_dir . $filename;

                            try {
                                $writer = new Xlsx($spreadsheet);
                                $writer->save($filepath);
                                $output_file = $session . '/' . $filename;
                            } catch (Exception $e) {
                                $errors[] = 'Gagal menyimpan file excel';
                                log_exec_error('Gagal menyimpan file excel: ' . $e->getMessage(), $session);
                            }

                            if ($output_file !== '') {
                                try {
                                    $stmt = $pdo->prepare("DELETE FROM deliquency WHERE session = :session  and cabang = :cabang");
                                    $stmt->execute([':session' => $session, ':cabang' => $cabang_set]);
                                } catch (PDOException $e) {
                                    $delete_warning = 'Gagal hapus data deliquency';
                                    log_exec_error('Gagal hapus data deliquency: ' . $e->getMessage(), $session);
                                }
                            }
                        }
                    }
                }
            }
        }

        $final_status = empty($errors) ? 'selesai' : 'failed';
        if (empty($errors)) {
            $final_message = "Analisa selesai ($processed file)";
            if ($output_file !== '') {
                $final_message .= " | File: $output_file";
            }
            if ($delete_warning !== '') {
                $final_message .= " | " . $delete_warning;
            }
        } else {
            $final_message = "Analisa selesai dengan error (" . count($errors) . ")";
        }
        try {
            $waktu_sekarang = date("Y-m-d H:i:s");
            $stmt = $pdo->prepare("UPDATE exec_analisa_par_multi SET status = :status, message = :message, output_file = :output_file, finish_time = :finish_time WHERE session = :session");
            $stmt->execute([
                ':status' => $final_status,
                ':message' => $final_message,
                ':output_file' => $output_file !== '' ? $output_file : null,
                ':finish_time' => $waktu_sekarang,
                ':session' => $session,
            ]);
        } catch (PDOException $e) {
            // ignore
        }
    }
}

$message = $processed > 0 ? "Berhasil analisa $processed file." : "Tidak ada file yang berhasil dianalisa.";
if (!empty($errors)) {
    $message .= "\n" . implode("\n", $errors);
}

if (php_sapi_name() === 'cli') {
    echo $message;
    exit;
}

?>
