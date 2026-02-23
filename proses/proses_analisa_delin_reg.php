<?php
error_reporting(0);
ini_set('memory_limit', '-1');
ini_set('max_execution_time', '0');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Aktifkan cell caching untuk hemat memory
\PhpOffice\PhpSpreadsheet\Settings::setCache(
    new \PhpOffice\PhpSpreadsheet\Collection\Memory\SimpleCache1()
);

if (!isset($_GET['cabang'], $_GET['tgl_delin1'])) {
    echo "<h3>Parameter proses analisa regional belum lengkap.</h3>";
    exit;
}

$nama_regional = $_GET['cabang'];
$tgl_delin     = $_GET['tgl_delin1'];

$spreadsheet = new Spreadsheet();

$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];

// ============================================================
// SHEET 1 - DATA PAR REGIONAL
// ============================================================
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('DATA PAR REGIONAL');

$judul = "DATA PAR REGIONAL $nama_regional\nPER $tgl_delin";
$sheet1->setCellValue('A1', $judul);
$sheet1->mergeCells('A1:W1');
$mergedCell = $sheet1->getCell('A1');
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet1->getRowDimension(1)->setRowHeight(60);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet1->setAutoFilter('A2:W2');
$sheet1->getStyle('A2:W2')->getFont()->setBold(true);
foreach (range('A', 'W') as $col) {
    $sheet1->getColumnDimension($col)->setAutoSize(true);
}

$headers1 = [
    'NO', 'CABANG', 'LOAN', 'CENTER', 'CLIENT ID', 'NASABAH', 'PRODUK',
    'DISBURSE', 'TGL DISBURSE', 'JANGKA WAKTU', 'BALANCE', 'TUNGGAKAN',
    'WEEK PAST DUE', 'WAJIB', 'SUKARELA', 'PENSIUN', 'HARI RAYA',
    'CICILAN', 'MINGGU KE', 'MINGGU RILL',
    'HARI', 'STAFF', 'JENIS TOPUP'
];
$col = 'A';
foreach ($headers1 as $h) {
    $sheet1->setCellValue($col . '2', $h);
    $col++;
}

// ============================================================
// SHEET 2 - SUKARELA
// ============================================================
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('SUKARELA');

$judul2 = "DATA SIMPANAN SUKARELA UNTUK ANGSURAN\nREGIONAL $nama_regional\nDELIN TGL $tgl_delin";
$sheet2->setCellValue('A1', $judul2);
$sheet2->mergeCells('A1:S1');
$mergedCell2 = $sheet2->getCell('A1');
$mergedCell2->getStyle()->getFont()->setBold(true)->setSize(14);
$mergedCell2->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell2->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet2->getRowDimension(1)->setRowHeight(60);
$mergedCell2->getStyle()->getAlignment()->setWrapText(true);
$sheet2->setAutoFilter('A2:S2');
$sheet2->getStyle('A2:S2')->getFont()->setBold(true);
foreach (range('A', 'S') as $col) {
    $sheet2->getColumnDimension($col)->setAutoSize(true);
}

$headers2 = [
    'NO', 'CABANG', 'CENTER', 'CLIENT ID', 'NASABAH', 'PRODUK',
    'MINGGU KE', 'MINGGU RILL', 'BALANCE', 'CICILAN',
    'WAJIB', 'SUKARELA', 'WEEK PAST DUE',
    'MASUK ANGSURAN', 'MASUK ANGSURAN x CICILAN',
    'SISA SUKARELA', 'KETERANGAN', 'HARI', 'STAFF'
];
$col = 'A';
foreach ($headers2 as $h) {
    $sheet2->setCellValue($col . '2', $h);
    $col++;
}

// ============================================================
// SHEET PTM - PELUNASAN
// ============================================================
$sheetPTM = $spreadsheet->createSheet();
$sheetPTM->setTitle('PELUNASAN (PTM)');

$judulPTM = "DATA PINJAMAN YANG BISA DILUNASI DARI SIMPANAN\nPERHITUNGAN DIKURANGI 2000 PER SIMPANAN < 100RB\nREGIONAL $nama_regional  TGL $tgl_delin";
$sheetPTM->setCellValue('A1', $judulPTM);
$sheetPTM->mergeCells('A1:U1');
$mPTM = $sheetPTM->getCell('A1');
$mPTM->getStyle()->getFont()->setBold(true)->setSize(14);
$mPTM->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mPTM->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheetPTM->getRowDimension(1)->setRowHeight(60);
$mPTM->getStyle()->getAlignment()->setWrapText(true);
$sheetPTM->setAutoFilter('A2:U2');
$sheetPTM->getStyle('A2:U2')->getFont()->setBold(true);
foreach (range('A', 'U') as $col) {
    $sheetPTM->getColumnDimension($col)->setAutoSize(true);
}

$headersPTM = [
    'NO', 'CABANG', 'CENTER', 'CLIENT ID', 'NASABAH', 'PRODUK',
    'MINGGU KE', 'MINGGU RILL', 'BALANCE',
    'CICILAN', 'WAJIB', 'SUKARELA', 'PENSIUN', 'HARI RAYA',
    'TOTAL SIMPANAN (NET)', 'KURANG/LEBIH', 'HARI', 'STAFF', 'JENIS TOPUP',
    'NOMINAL KURANG', 'KETERANGAN'
];
$col = 'A';
foreach ($headersPTM as $h) {
    $sheetPTM->setCellValue($col . '2', $h);
    $col++;
}

// ============================================================
// SHEET 3 - REKAP PER CABANG
// ============================================================
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('REKAP PER CABANG');

$judul3 = "REKAP PAR PER CABANG\nREGIONAL $nama_regional\nPER $tgl_delin";
$sheet3->setCellValue('A1', $judul3);
$sheet3->mergeCells('A1:K1');
$mergedCell3 = $sheet3->getCell('A1');
$mergedCell3->getStyle()->getFont()->setBold(true)->setSize(14);
$mergedCell3->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell3->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet3->getRowDimension(1)->setRowHeight(50);
$mergedCell3->getStyle()->getAlignment()->setWrapText(true);
$sheet3->getStyle('A2:K2')->getFont()->setBold(true);
foreach (range('A', 'K') as $col) {
    $sheet3->getColumnDimension($col)->setAutoSize(true);
}

$headers3 = [
    'NO', 'CABANG', 'CTR PAR', 'AGT PAR', 'REK PAR',
    'BALANCE PAR', 'TOTAL WAJIB', 'TOTAL SUKARELA',
    'TOTAL CICILAN', 'STAFF TERBANYAK', 'JENIS TOPUP TERBANYAK'
];
$col = 'A';
foreach ($headers3 as $h) {
    $sheet3->setCellValue($col . '2', $h);
    $col++;
}

// ============================================================
// SHEET 4 - INFORMASI
// ============================================================
$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('INFORMASI');

$judul4 = "INFORMASI PAR REGIONAL $nama_regional\nTGL $tgl_delin";
$sheet4->setCellValue('A1', $judul4);
$sheet4->mergeCells('A1:D1');
$sheet4->getCell('A1')->getStyle()->getFont()->setBold(true)->setSize(14);
$sheet4->getCell('A1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet4->getRowDimension(1)->setRowHeight(50);
$sheet4->getCell('A1')->getStyle()->getAlignment()->setWrapText(true);
$sheet4->getStyle('A2:D2')->getFont()->setBold(true);
foreach (range('A', 'D') as $col) {
    $sheet4->getColumnDimension($col)->setAutoSize(true);
}

$sheet4->setCellValue('A2', 'KETERANGAN');
$sheet4->setCellValue('B2', ':');
$sheet4->setCellValue('C2', 'NILAI');
$sheet4->setCellValue('D2', 'PERSEN');

// ============================================================
// AMBIL DATA DARI DB + ISI SHEET 1 & 2
// ============================================================
$sql_data = "SELECT * FROM deliquency_regional
             WHERE regional = :regional AND tgl_input = :tgl
             ORDER BY cabang ASC, staff ASC, tunggakan DESC";
$stmt_data = $pdo->prepare($sql_data);
$stmt_data->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);
$all_data = $stmt_data->fetchAll(PDO::FETCH_ASSOC);

$no        = 1;
$baris1    = 3;
$baris2    = 3;
$no2       = 1;
$barisPTM  = 3;
$noPTM     = 1;

// Deteksi Client ID yang duplikat
$id_count      = array_count_values(array_column($all_data, 'id_detail_nasabah'));
$duplicate_ids = array_filter($id_count, fn($c) => $c > 1);

// Tracking baris duplikat per sheet untuk pewarnaan merah
$baris2_dup   = [];
$barisPTM_dup = [];

$total_os_par      = 0;
$total_wajib       = 0;
$total_sukarela    = 0;
$total_cicilan     = 0;
$total_topup       = 0;
$total_bukan_topup = 0;
$total_rek         = 0;

foreach ($all_data as $row) {
    $total_os_par   += (float)$row['sisa_saldo'];
    $total_wajib    += (float)$row['wajib'];
    $total_sukarela += (float)$row['sukarela'];
    $total_cicilan  += (float)$row['cicilan'];
    $total_rek++;

    if (strtoupper($row['jenis_topup']) === 'NON TOP-UP' || $row['jenis_topup'] === '') {
        $total_bukan_topup++;
    } else {
        $total_topup++;
    }

    // SHEET 1
    $sheet1->setCellValue('A' . $baris1, $no);
    $sheet1->setCellValue('B' . $baris1, $row['cabang']);
    $sheet1->setCellValue('C' . $baris1, $row['loan']);
    $sheet1->setCellValue('D' . $baris1, $row['no_center']);
    $sheet1->setCellValue('E' . $baris1, $row['id_detail_nasabah']);
    $sheet1->setCellValue('F' . $baris1, $row['nasabah']);
    $sheet1->setCellValue('G' . $baris1, $row['kode_pemb']);
    $sheet1->setCellValue('H' . $baris1, (float)$row['amount']);
    $sheet1->setCellValue('I' . $baris1, $row['tgl_disburse']);
    $sheet1->setCellValue('J' . $baris1, (int)$row['priode']);
    $sheet1->setCellValue('K' . $baris1, (float)$row['sisa_saldo']);
    $sheet1->setCellValue('L' . $baris1, (float)$row['tunggakan']);
    $sheet1->setCellValue('M' . $baris1, (int)$row['minggu']);
    $sheet1->setCellValue('N' . $baris1, (float)$row['wajib']);
    $sheet1->setCellValue('O' . $baris1, (float)$row['sukarela']);
    $sheet1->setCellValue('P' . $baris1, (float)$row['pensiun']);
    $sheet1->setCellValue('Q' . $baris1, (float)$row['hariraya']);
    $sheet1->setCellValue('R' . $baris1, (float)$row['cicilan']);
    $sheet1->setCellValue('S' . $baris1, (int)$row['minggu_ke']);
    $sheet1->setCellValue('T' . $baris1, (int)$row['minggu_rill']);
    $sheet1->setCellValue('U' . $baris1, $row['hari']);
    $sheet1->setCellValue('V' . $baris1, $row['staff']);
    $sheet1->setCellValue('W' . $baris1, $row['jenis_topup']);

    // Warnai merah jika Client ID duplikat
    if (isset($duplicate_ids[$row['id_detail_nasabah']]) && $row['id_detail_nasabah'] !== '') {
        $sheet1->getStyle('A' . $baris1 . ':W' . $baris1)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFB7B7');
    }
    $baris1++;
    $no++;

    // SHEET PTM - Pelunasan dari simpanan
    $s_wajib_ptm    = max(0, (float)$row['wajib']    - 2000);
    $s_sukarela_ptm = max(0, (float)$row['sukarela'] - 2000);
    $s_pensiun_ptm  = max(0, (float)$row['pensiun']  - 2000);
    $s_hariraya_ptm = ((float)$row['hariraya'] > 2000) ? (float)$row['hariraya'] - 2000 : 0;
    $total_simp_ptm = $s_wajib_ptm + $s_sukarela_ptm + $s_pensiun_ptm + $s_hariraya_ptm;
    $selisih_ptm    = (float)$row['sisa_saldo'] - $total_simp_ptm;

    if ($selisih_ptm <= 500000) {
        if ($selisih_ptm < 0) {
            $nominal_ptm = '-';
            $ket_ptm     = 'Pinjaman dapat dilunasi';
        } elseif ($selisih_ptm <= 100000) {
            $nominal_ptm = 'Rp ' . number_format($selisih_ptm, 0, ',', '.');
            $ket_ptm     = 'Kurang < 100rb';
        } elseif ($selisih_ptm <= 200000) {
            $nominal_ptm = 'Rp ' . number_format($selisih_ptm, 0, ',', '.');
            $ket_ptm     = 'Kurang 100rb-200rb';
        } elseif ($selisih_ptm <= 300000) {
            $nominal_ptm = 'Rp ' . number_format($selisih_ptm, 0, ',', '.');
            $ket_ptm     = 'Kurang 200rb-300rb';
        } elseif ($selisih_ptm <= 400000) {
            $nominal_ptm = 'Rp ' . number_format($selisih_ptm, 0, ',', '.');
            $ket_ptm     = 'Kurang 300rb-400rb';
        } else {
            $nominal_ptm = 'Rp ' . number_format($selisih_ptm, 0, ',', '.');
            $ket_ptm     = 'Kurang 400rb-500rb';
        }
        $sheetPTM->setCellValue('A'  . $barisPTM, $noPTM);
        $sheetPTM->setCellValue('B'  . $barisPTM, $row['cabang']);
        $sheetPTM->setCellValue('C'  . $barisPTM, $row['no_center']);
        $sheetPTM->setCellValue('D'  . $barisPTM, $row['id_detail_nasabah']);
        $sheetPTM->setCellValue('E'  . $barisPTM, $row['nasabah']);
        $sheetPTM->setCellValue('F'  . $barisPTM, $row['kode_pemb']);
        $sheetPTM->setCellValue('G'  . $barisPTM, (int)$row['minggu_ke']);
        $sheetPTM->setCellValue('H'  . $barisPTM, (int)$row['minggu_rill']);
        $sheetPTM->setCellValue('I'  . $barisPTM, (float)$row['sisa_saldo']);
        $sheetPTM->setCellValue('J'  . $barisPTM, (float)$row['cicilan']);
        $sheetPTM->setCellValue('K'  . $barisPTM, $s_wajib_ptm);
        $sheetPTM->setCellValue('L'  . $barisPTM, $s_sukarela_ptm);
        $sheetPTM->setCellValue('M'  . $barisPTM, $s_pensiun_ptm);
        $sheetPTM->setCellValue('N'  . $barisPTM, $s_hariraya_ptm);
        $sheetPTM->setCellValue('O'  . $barisPTM, $total_simp_ptm);
        $sheetPTM->setCellValue('P'  . $barisPTM, $selisih_ptm);
        $sheetPTM->setCellValue('Q'  . $barisPTM, $row['hari']);
        $sheetPTM->setCellValue('R'  . $barisPTM, $row['staff']);
        $sheetPTM->setCellValue('S'  . $barisPTM, $row['jenis_topup']);
        $sheetPTM->setCellValue('T'  . $barisPTM, $nominal_ptm);
        $sheetPTM->setCellValue('U'  . $barisPTM, $ket_ptm);
        if (isset($duplicate_ids[$row['id_detail_nasabah']]) && $row['id_detail_nasabah'] !== '') {
            $barisPTM_dup[] = $barisPTM;
        }
        $barisPTM++;
        $noPTM++;
    }

    // SHEET 2 - Sukarela bisa digunakan untuk angsuran
    $sukarela_net = (float)$row['sukarela'] - 2000;
    $cicilan      = (float)$row['cicilan'];
    if ($cicilan > 0 && $sukarela_net > $cicilan) {
        $jml_angsuran  = floor($sukarela_net / $cicilan);
        $angsuran_masuk = $jml_angsuran * $cicilan;
        $sisa_sukarela  = (float)$row['sukarela'] - $angsuran_masuk;
        $ket_suk        = ($jml_angsuran >= (int)$row['minggu']) ? 'Turun PAR' : '';

        $sheet2->setCellValue('A' . $baris2, $no2);
        $sheet2->setCellValue('B' . $baris2, $row['cabang']);
        $sheet2->setCellValue('C' . $baris2, $row['no_center']);
        $sheet2->setCellValue('D' . $baris2, $row['id_detail_nasabah']);
        $sheet2->setCellValue('E' . $baris2, $row['nasabah']);
        $sheet2->setCellValue('F' . $baris2, $row['kode_pemb']);
        $sheet2->setCellValue('G' . $baris2, (int)$row['minggu_ke']);
        $sheet2->setCellValue('H' . $baris2, (int)$row['minggu_rill']);
        $sheet2->setCellValue('I' . $baris2, (float)$row['sisa_saldo']);
        $sheet2->setCellValue('J' . $baris2, $cicilan);
        $sheet2->setCellValue('K' . $baris2, (float)$row['wajib']);
        $sheet2->setCellValue('L' . $baris2, (float)$row['sukarela']);
        $sheet2->setCellValue('M' . $baris2, (int)$row['minggu']);
        $sheet2->setCellValue('N' . $baris2, $jml_angsuran);
        $sheet2->setCellValue('O' . $baris2, $angsuran_masuk);
        $sheet2->setCellValue('P' . $baris2, $sisa_sukarela);
        $sheet2->setCellValue('Q' . $baris2, $ket_suk);
        $sheet2->setCellValue('R' . $baris2, $row['hari']);
        $sheet2->setCellValue('S' . $baris2, $row['staff']);
        if (isset($duplicate_ids[$row['id_detail_nasabah']]) && $row['id_detail_nasabah'] !== '') {
            $baris2_dup[] = $baris2;
        }
        $baris2++;
        $no2++;
    }
}

// ============================================================
// SHEET 3 - REKAP PER CABANG (query GROUP BY)
// ============================================================
$sql_rekap = "SELECT
                cabang,
                COUNT(DISTINCT no_center)         AS ctr_par,
                COUNT(DISTINCT id_detail_nasabah) AS agt_par,
                COUNT(id_detail_nasabah)          AS rek_par,
                SUM(sisa_saldo)                   AS total_balance,
                SUM(wajib)                        AS total_wajib,
                SUM(sukarela)                     AS total_sukarela,
                SUM(cicilan)                      AS total_cicilan
              FROM deliquency_regional
              WHERE regional = :regional AND tgl_input = :tgl
              GROUP BY cabang
              ORDER BY cabang ASC";
$stmt_rekap = $pdo->prepare($sql_rekap);
$stmt_rekap->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);

$baris3 = 3;
$no3    = 1;
foreach ($stmt_rekap->fetchAll(PDO::FETCH_ASSOC) as $r) {
    $stmt_staff = $pdo->prepare("SELECT staff, COUNT(*) AS cnt FROM deliquency_regional
        WHERE regional=:reg AND tgl_input=:tgl AND cabang=:cab GROUP BY staff ORDER BY cnt DESC LIMIT 1");
    $stmt_staff->execute([':reg' => $nama_regional, ':tgl' => $tgl_delin, ':cab' => $r['cabang']]);
    $top_staff = $stmt_staff->fetchColumn() ?: '-';

    $stmt_topup = $pdo->prepare("SELECT jenis_topup, COUNT(*) AS cnt FROM deliquency_regional
        WHERE regional=:reg AND tgl_input=:tgl AND cabang=:cab GROUP BY jenis_topup ORDER BY cnt DESC LIMIT 1");
    $stmt_topup->execute([':reg' => $nama_regional, ':tgl' => $tgl_delin, ':cab' => $r['cabang']]);
    $top_topup = $stmt_topup->fetchColumn() ?: '-';

    $sheet3->setCellValue('A' . $baris3, $no3);
    $sheet3->setCellValue('B' . $baris3, $r['cabang']);
    $sheet3->setCellValue('C' . $baris3, (int)$r['ctr_par']);
    $sheet3->setCellValue('D' . $baris3, (int)$r['agt_par']);
    $sheet3->setCellValue('E' . $baris3, (int)$r['rek_par']);
    $sheet3->setCellValue('F' . $baris3, (float)$r['total_balance']);
    $sheet3->setCellValue('G' . $baris3, (float)$r['total_wajib']);
    $sheet3->setCellValue('H' . $baris3, (float)$r['total_sukarela']);
    $sheet3->setCellValue('I' . $baris3, (float)$r['total_cicilan']);
    $sheet3->setCellValue('J' . $baris3, $top_staff);
    $sheet3->setCellValue('K' . $baris3, $top_topup);
    $no3++;
    $baris3++;
}

// Baris total sheet3
$sheet3->mergeCells('A' . $baris3 . ':E' . $baris3);
$sheet3->setCellValue('A' . $baris3, 'TOTAL');
$sheet3->getStyle('A' . $baris3)->getFont()->setBold(true);
$kurang1 = $baris3 - 1;
foreach (['F', 'G', 'H', 'I'] as $c) {
    $sheet3->setCellValue($c . $baris3, "=SUM({$c}3:{$c}{$kurang1})");
}

// ============================================================
// SHEET 4 - INFORMASI
// ============================================================
$sheet4->setCellValue('A3',  'REGIONAL');      $sheet4->setCellValue('B3',  ':'); $sheet4->setCellValue('C3',  $nama_regional);
$sheet4->setCellValue('A4',  'TANGGAL DELIN'); $sheet4->setCellValue('B4',  ':'); $sheet4->setCellValue('C4',  $tgl_delin);
$sheet4->setCellValue('A5',  'TOTAL REKENING PAR'); $sheet4->setCellValue('B5', ':'); $sheet4->setCellValue('C5', $total_rek);
$sheet4->setCellValue('A6',  'TOTAL OS PAR');  $sheet4->setCellValue('B6',  ':'); $sheet4->setCellValue('C6',  $total_os_par);
$sheet4->setCellValue('A7',  'TOTAL WAJIB');   $sheet4->setCellValue('B7',  ':'); $sheet4->setCellValue('C7',  $total_wajib);
$sheet4->setCellValue('A8',  'TOTAL SUKARELA');$sheet4->setCellValue('B8',  ':'); $sheet4->setCellValue('C8',  $total_sukarela);
$sheet4->setCellValue('A9',  'TOTAL CICILAN'); $sheet4->setCellValue('B9',  ':'); $sheet4->setCellValue('C9',  $total_cicilan);
$sheet4->setCellValue('A10', 'TOP-UP');        $sheet4->setCellValue('B10', ':'); $sheet4->setCellValue('C10', $total_topup);
if ($total_rek > 0) $sheet4->setCellValue('D10', $total_topup / $total_rek);
$sheet4->setCellValue('A11', 'BUKAN TOP-UP');  $sheet4->setCellValue('B11', ':'); $sheet4->setCellValue('C11', $total_bukan_topup);
if ($total_rek > 0) $sheet4->setCellValue('D11', $total_bukan_topup / $total_rek);

$sheet4->getStyle('C5:C11')->getNumberFormat()->setFormatCode('#,##0');
$sheet4->getStyle('D10:D11')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);
$sheet4->getStyle('A2:D11')->applyFromArray($styleArray);

// ============================================================
// STYLING SHEET 1
// ============================================================
$akhir1 = $baris1 - 1;
$sheet1->getStyle('A2:W' . $akhir1)->applyFromArray($styleArray);
$sheet1->getStyle('A2:W' . $akhir1)->getFont()->setSize(8);
foreach (['H', 'K', 'L', 'N', 'O', 'P', 'Q', 'R'] as $c) {
    $sheet1->getStyle($c . '3:' . $c . $akhir1)->getNumberFormat()->setFormatCode('#,##0');
}

// STYLING SHEET 2
$akhir2 = $baris2 - 1;
$sheet2->getStyle('A2:S' . $akhir2)->applyFromArray($styleArray);
$sheet2->getStyle('A2:S' . $akhir2)->getFont()->setSize(8);
foreach (['I', 'J', 'K', 'L', 'N', 'O', 'P'] as $c) {
    $sheet2->getStyle($c . '3:' . $c . $akhir2)->getNumberFormat()->setFormatCode('#,##0');
}
// Warnai merah duplikat di SUKARELA
foreach ($baris2_dup as $br2) {
    $sheet2->getStyle('A' . $br2 . ':S' . $br2)
        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setRGB('FFB7B7');
}

// STYLING SHEET PTM
$akhirPTM = $barisPTM - 1;
$sheetPTM->getStyle('A2:U' . $akhirPTM)->applyFromArray($styleArray);
$sheetPTM->getStyle('A2:U' . $akhirPTM)->getFont()->setSize(8);
foreach (['I', 'J', 'K', 'L', 'M', 'N', 'O', 'P'] as $c) {
    $sheetPTM->getStyle($c . '3:' . $c . $akhirPTM)->getNumberFormat()->setFormatCode('#,##0');
}
// Warnai merah duplikat di PTM
foreach ($barisPTM_dup as $brPTM) {
    $sheetPTM->getStyle('A' . $brPTM . ':U' . $brPTM)
        ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
        ->getStartColor()->setRGB('FFB7B7');
}

// STYLING SHEET 3
$sheet3->getStyle('A2:K' . $baris3)->applyFromArray($styleArray);
$sheet3->getStyle('A2:K' . $baris3)->getFont()->setSize(9);
foreach (['F', 'G', 'H', 'I'] as $c) {
    $sheet3->getStyle($c . '3:' . $c . $baris3)->getNumberFormat()->setFormatCode('#,##0');
}

// ============================================================
// SHEET 5 - REKAP PER STAFF
// ============================================================
$sheet5 = $spreadsheet->createSheet();
$sheet5->setTitle('REKAP PER STAFF');

$judul5 = "REKAP PAR PER STAFF\nREGIONAL $nama_regional\nPER $tgl_delin";
$sheet5->setCellValue('A1', $judul5);
$sheet5->mergeCells('A1:J1');
$sheet5->getCell('A1')->getStyle()->getFont()->setBold(true)->setSize(14);
$sheet5->getCell('A1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet5->getCell('A1')->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet5->getRowDimension(1)->setRowHeight(50);
$sheet5->getCell('A1')->getStyle()->getAlignment()->setWrapText(true);
$sheet5->getStyle('A2:J2')->getFont()->setBold(true);
foreach (range('A', 'J') as $col) {
    $sheet5->getColumnDimension($col)->setAutoSize(true);
}
$headers5 = [
    'NO', 'CABANG', 'STAFF', 'REK PAR', 'BALANCE PAR',
    'TOTAL WAJIB', 'TOTAL SUKARELA', 'TOTAL CICILAN',
    'PRODUK TERBANYAK', 'JENIS TOPUP TERBANYAK'
];
$col = 'A';
foreach ($headers5 as $h) {
    $sheet5->setCellValue($col . '2', $h);
    $col++;
}

$sql_staff_all = "SELECT cabang, staff,
                        COUNT(*) AS rek_par,
                        SUM(sisa_saldo) AS total_balance,
                        SUM(wajib) AS total_wajib,
                        SUM(sukarela) AS total_sukarela,
                        SUM(cicilan) AS total_cicilan
                  FROM deliquency_regional
                  WHERE regional = :regional AND tgl_input = :tgl
                  GROUP BY cabang, staff
                  ORDER BY cabang ASC, rek_par DESC";
$stmt_staff_all = $pdo->prepare($sql_staff_all);
$stmt_staff_all->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);

$baris5 = 3;
$no5    = 1;
foreach ($stmt_staff_all->fetchAll(PDO::FETCH_ASSOC) as $rs) {
    $stmt_prod5 = $pdo->prepare("SELECT kode_pemb, COUNT(*) AS cnt FROM deliquency_regional
        WHERE regional=:reg AND tgl_input=:tgl AND cabang=:cab AND staff=:stf
        GROUP BY kode_pemb ORDER BY cnt DESC LIMIT 1");
    $stmt_prod5->execute([':reg' => $nama_regional, ':tgl' => $tgl_delin, ':cab' => $rs['cabang'], ':stf' => $rs['staff']]);
    $top_prod5 = $stmt_prod5->fetchColumn() ?: '-';

    $stmt_top5 = $pdo->prepare("SELECT jenis_topup, COUNT(*) AS cnt FROM deliquency_regional
        WHERE regional=:reg AND tgl_input=:tgl AND cabang=:cab AND staff=:stf
        GROUP BY jenis_topup ORDER BY cnt DESC LIMIT 1");
    $stmt_top5->execute([':reg' => $nama_regional, ':tgl' => $tgl_delin, ':cab' => $rs['cabang'], ':stf' => $rs['staff']]);
    $top_topup5 = $stmt_top5->fetchColumn() ?: '-';

    $sheet5->setCellValue('A' . $baris5, $no5);
    $sheet5->setCellValue('B' . $baris5, $rs['cabang']);
    $sheet5->setCellValue('C' . $baris5, $rs['staff']);
    $sheet5->setCellValue('D' . $baris5, (int)$rs['rek_par']);
    $sheet5->setCellValue('E' . $baris5, (float)$rs['total_balance']);
    $sheet5->setCellValue('F' . $baris5, (float)$rs['total_wajib']);
    $sheet5->setCellValue('G' . $baris5, (float)$rs['total_sukarela']);
    $sheet5->setCellValue('H' . $baris5, (float)$rs['total_cicilan']);
    $sheet5->setCellValue('I' . $baris5, $top_prod5);
    $sheet5->setCellValue('J' . $baris5, $top_topup5);
    $no5++;
    $baris5++;
}
// Baris total sheet5
$sheet5->mergeCells('A' . $baris5 . ':D' . $baris5);
$sheet5->setCellValue('A' . $baris5, 'TOTAL');
$sheet5->getStyle('A' . $baris5)->getFont()->setBold(true);
$kurang5 = $baris5 - 1;
foreach (['E', 'F', 'G', 'H'] as $c) {
    $sheet5->setCellValue($c . $baris5, "=SUM({$c}3:{$c}{$kurang5})");
}
$sheet5->getStyle('A2:J' . $baris5)->applyFromArray($styleArray);
$sheet5->getStyle('A2:J' . $baris5)->getFont()->setSize(9);
foreach (['E', 'F', 'G', 'H'] as $c) {
    $sheet5->getStyle($c . '3:' . $c . $baris5)->getNumberFormat()->setFormatCode('#,##0');
}

// ============================================================
// SHEET 6 - ANALISA (per Produk & per Jenis Topup)
// ============================================================
$sheet6 = $spreadsheet->createSheet();
$sheet6->setTitle('ANALISA');

$judul6 = "ANALISA PAR REGIONAL $nama_regional\nPER $tgl_delin";
$sheet6->setCellValue('A1', $judul6);
$sheet6->mergeCells('A1:F1');
$sheet6->getCell('A1')->getStyle()->getFont()->setBold(true)->setSize(14);
$sheet6->getCell('A1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet6->getCell('A1')->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet6->getRowDimension(1)->setRowHeight(40);
$sheet6->getCell('A1')->getStyle()->getAlignment()->setWrapText(true);
foreach (range('A', 'F') as $col) {
    $sheet6->getColumnDimension($col)->setAutoSize(true);
}

// --- Seksi 1: Per Produk ---
$sheet6->setCellValue('A2', 'ANALISA PER PRODUK PEMBIAYAAN');
$sheet6->mergeCells('A2:F2');
$sheet6->getStyle('A2')->getFont()->setBold(true)->setSize(11);
$sheet6->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet6->getStyle('A2')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BDD7EE');

foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
    $sheet6->getStyle($col . '3')->getFont()->setBold(true);
    $sheet6->getStyle($col . '3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
}
$sheet6->setCellValue('A3', 'NO');
$sheet6->setCellValue('B3', 'PRODUK');
$sheet6->setCellValue('C3', 'JUMLAH REK');
$sheet6->setCellValue('D3', 'BALANCE PAR');
$sheet6->setCellValue('E3', 'TOTAL CICILAN');
$sheet6->setCellValue('F3', '% REK');

$sql_prod6 = "SELECT kode_pemb, COUNT(*) AS rek, SUM(sisa_saldo) AS balance, SUM(cicilan) AS cicilan
              FROM deliquency_regional
              WHERE regional=:regional AND tgl_input=:tgl
              GROUP BY kode_pemb ORDER BY rek DESC";
$stmt_prod6 = $pdo->prepare($sql_prod6);
$stmt_prod6->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);

$baris6    = 4;
$no6       = 1;
foreach ($stmt_prod6->fetchAll(PDO::FETCH_ASSOC) as $pr) {
    $persen_rek6 = $total_rek > 0 ? $pr['rek'] / $total_rek : 0;
    $sheet6->setCellValue('A' . $baris6, $no6);
    $sheet6->setCellValue('B' . $baris6, $pr['kode_pemb']);
    $sheet6->setCellValue('C' . $baris6, (int)$pr['rek']);
    $sheet6->setCellValue('D' . $baris6, (float)$pr['balance']);
    $sheet6->setCellValue('E' . $baris6, (float)$pr['cicilan']);
    $sheet6->setCellValue('F' . $baris6, $persen_rek6);
    $no6++;
    $baris6++;
}
// Total baris produk
$sheet6->mergeCells('A' . $baris6 . ':B' . $baris6);
$sheet6->setCellValue('A' . $baris6, 'TOTAL');
$sheet6->getStyle('A' . $baris6)->getFont()->setBold(true);
$p1_end = $baris6 - 1;
$sheet6->setCellValue('C' . $baris6, "=SUM(C4:C{$p1_end})");
$sheet6->setCellValue('D' . $baris6, "=SUM(D4:D{$p1_end})");
$sheet6->setCellValue('E' . $baris6, "=SUM(E4:E{$p1_end})");
$sheet6->getStyle('A2:F' . $baris6)->applyFromArray($styleArray);
$sheet6->getStyle('D4:E' . $baris6)->getNumberFormat()->setFormatCode('#,##0');
$sheet6->getStyle('F4:F' . $p1_end)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

$baris6 += 2;

// --- Seksi 2: Per Jenis Topup ---
$sec2_title = $baris6;
$sheet6->setCellValue('A' . $sec2_title, 'ANALISA PER JENIS TOPUP');
$sheet6->mergeCells('A' . $sec2_title . ':F' . $sec2_title);
$sheet6->getStyle('A' . $sec2_title)->getFont()->setBold(true)->setSize(11);
$sheet6->getStyle('A' . $sec2_title)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet6->getStyle('A' . $sec2_title)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('BDD7EE');

$sec2_header = $baris6 + 1;
foreach (['A', 'B', 'C', 'D', 'E', 'F'] as $col) {
    $sheet6->getStyle($col . $sec2_header)->getFont()->setBold(true);
    $sheet6->getStyle($col . $sec2_header)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('D9E1F2');
}
$sheet6->setCellValue('A' . $sec2_header, 'NO');
$sheet6->setCellValue('B' . $sec2_header, 'JENIS TOPUP');
$sheet6->setCellValue('C' . $sec2_header, 'JUMLAH REK');
$sheet6->setCellValue('D' . $sec2_header, 'BALANCE PAR');
$sheet6->setCellValue('E' . $sec2_header, 'TOTAL CICILAN');
$sheet6->setCellValue('F' . $sec2_header, '% REK');

$sql_topup6 = "SELECT jenis_topup, COUNT(*) AS rek, SUM(sisa_saldo) AS balance, SUM(cicilan) AS cicilan
               FROM deliquency_regional
               WHERE regional=:regional AND tgl_input=:tgl
               GROUP BY jenis_topup ORDER BY rek DESC";
$stmt_topup6 = $pdo->prepare($sql_topup6);
$stmt_topup6->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);

$baris6      = $sec2_header + 1;
$no6         = 1;
$tp_data_start = $baris6;
foreach ($stmt_topup6->fetchAll(PDO::FETCH_ASSOC) as $tp) {
    $persen_tp6 = $total_rek > 0 ? $tp['rek'] / $total_rek : 0;
    $sheet6->setCellValue('A' . $baris6, $no6);
    $sheet6->setCellValue('B' . $baris6, $tp['jenis_topup'] !== '' ? $tp['jenis_topup'] : 'NON TOP-UP');
    $sheet6->setCellValue('C' . $baris6, (int)$tp['rek']);
    $sheet6->setCellValue('D' . $baris6, (float)$tp['balance']);
    $sheet6->setCellValue('E' . $baris6, (float)$tp['cicilan']);
    $sheet6->setCellValue('F' . $baris6, $persen_tp6);
    $no6++;
    $baris6++;
}
// Total baris topup
$sheet6->mergeCells('A' . $baris6 . ':B' . $baris6);
$sheet6->setCellValue('A' . $baris6, 'TOTAL');
$sheet6->getStyle('A' . $baris6)->getFont()->setBold(true);
$p2_end = $baris6 - 1;
$sheet6->setCellValue('C' . $baris6, "=SUM(C{$tp_data_start}:C{$p2_end})");
$sheet6->setCellValue('D' . $baris6, "=SUM(D{$tp_data_start}:D{$p2_end})");
$sheet6->setCellValue('E' . $baris6, "=SUM(E{$tp_data_start}:E{$p2_end})");
$sheet6->getStyle('A' . $sec2_title . ':F' . $baris6)->applyFromArray($styleArray);
$sheet6->getStyle('D' . $tp_data_start . ':E' . $p2_end)->getNumberFormat()->setFormatCode('#,##0');
$sheet6->getStyle('F' . $tp_data_start . ':F' . $p2_end)->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

// ============================================================
// SHEET 7 - ANALISA TOPUP
// ============================================================
$sheet7 = $spreadsheet->createSheet();
$sheet7->setTitle('ANALISA TOPUP');

$judul7 = "ANALISA TOPUP REGIONAL $nama_regional\nPER $tgl_delin";
$sheet7->setCellValue('A1', $judul7);
$sheet7->mergeCells('A1:S1');
$sheet7->getCell('A1')->getStyle()->getFont()->setBold(true)->setSize(14);
$sheet7->getCell('A1')->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$sheet7->getCell('A1')->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$sheet7->getRowDimension(1)->setRowHeight(40);
$sheet7->getCell('A1')->getStyle()->getAlignment()->setWrapText(true);
$sheet7->setAutoFilter('A2:S2');
$sheet7->getStyle('A2:S2')->getFont()->setBold(true);
foreach (range('A', 'S') as $col) {
    $sheet7->getColumnDimension($col)->setAutoSize(true);
}

$headers7 = [
    'NO', 'LOAN', 'CENTER', 'CLIENT ID', 'NASABAH', 'PRODUK',
    'JK WAKTU', 'RILL', 'DISBURSE', 'BALANCE', 'TOTAL TOPUP',
    '25 MINGGU', '50 MINGGU', '75 MINGGU', '100 MINGGU',
    'HARI', 'STAFF', 'JENIS TOPUP', 'KETERANGAN'
];
$col = 'A';
foreach ($headers7 as $h) {
    $sheet7->setCellValue($col . '2', $h);
    $col++;
}

$sql_tpk = "SELECT * FROM deliquency_regional
            WHERE regional = :regional AND tgl_input = :tgl
              AND jenis_topup != '' AND jenis_topup != 'NON TOP-UP'
            ORDER BY cabang ASC, staff ASC, sisa_saldo ASC";
$stmt_tpk = $pdo->prepare($sql_tpk);
$stmt_tpk->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);

$baris7 = 3;
$no7    = 1;
foreach ($stmt_tpk->fetchAll(PDO::FETCH_ASSOC) as $rt) {
    $sisa_saldo = (float)$rt['sisa_saldo'];
    $tpk        = $sisa_saldo + 10000;
    $priode     = (int)$rt['priode'];
    $rill       = (int)$rt['minggu_rill'];

    $setengah = $priode > 0 ? ($rill / $priode) * 100 : 0;
    if ($setengah >= 50) {
        $margin         = 0.06;
        $ket_setengah   = 'Lewat Setengah Jk. Waktu';
    } else {
        $margin         = 0.12;
        $ket_setengah   = 'Belum Lewat Setengah Jk. Waktu';
    }

    $dualima   = round((($tpk + ($tpk * $margin)) / 25)  / 1000, 0, PHP_ROUND_HALF_UP) * 1000;
    $limapuluh = round((($tpk + ($tpk * $margin)) / 50)  / 1000, 0, PHP_ROUND_HALF_UP) * 1000;
    $tujuhlima = round((($tpk + ($tpk * $margin)) / 75)  / 1000, 0, PHP_ROUND_HALF_UP) * 1000;
    $seratus   = round((($tpk + ($tpk * $margin)) / 100) / 1000, 0, PHP_ROUND_HALF_UP) * 1000;

    $sheet7->setCellValue('A' . $baris7, $no7);
    $sheet7->setCellValue('B' . $baris7, $rt['loan']);
    $sheet7->setCellValue('C' . $baris7, $rt['no_center']);
    $sheet7->setCellValue('D' . $baris7, $rt['id_detail_nasabah']);
    $sheet7->setCellValue('E' . $baris7, $rt['nasabah']);
    $sheet7->setCellValue('F' . $baris7, $rt['kode_pemb']);
    $sheet7->setCellValue('G' . $baris7, $priode);
    $sheet7->setCellValue('H' . $baris7, $rill);
    $sheet7->setCellValue('I' . $baris7, (float)$rt['amount']);
    $sheet7->setCellValue('J' . $baris7, $sisa_saldo);
    $sheet7->setCellValue('K' . $baris7, $tpk);
    $sheet7->setCellValue('L' . $baris7, $dualima);
    $sheet7->setCellValue('M' . $baris7, $limapuluh);
    $sheet7->setCellValue('N' . $baris7, $tujuhlima);
    $sheet7->setCellValue('O' . $baris7, $seratus);
    $sheet7->setCellValue('P' . $baris7, $rt['hari']);
    $sheet7->setCellValue('Q' . $baris7, $rt['staff']);
    $sheet7->setCellValue('R' . $baris7, $rt['jenis_topup']);
    $sheet7->setCellValue('S' . $baris7, $ket_setengah);

    // Warnai merah jika Client ID duplikat
    if (isset($duplicate_ids[$rt['id_detail_nasabah']]) && $rt['id_detail_nasabah'] !== '') {
        $sheet7->getStyle('A' . $baris7 . ':S' . $baris7)
            ->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFB7B7');
    }

    $no7++;
    $baris7++;
}

$akhir7 = $baris7 - 1;
$sheet7->getStyle('A2:S' . $akhir7)->applyFromArray($styleArray);
$sheet7->getStyle('A2:S' . $akhir7)->getFont()->setSize(8);
foreach (['I', 'J', 'K', 'L', 'M', 'N', 'O'] as $c) {
    $sheet7->getStyle($c . '3:' . $c . $akhir7)->getNumberFormat()->setFormatCode('#,##0');
}

// ============================================================
// UPDATE LOG
// ============================================================
try {
    $sqlUpdateLog = "UPDATE log_cek_par
                     SET selesai = :selesai, keterangan = :keterangan, edited_at = :edited_at
                     WHERE cabang = :cabang AND keterangan = 'proses'
                     ORDER BY id DESC LIMIT 1";
    $stmtLog = $pdo->prepare($sqlUpdateLog);
    $selesaiNow = date("H:i:s");
    $editedAt   = date("Y-m-d H:i:s");
    $ket        = 'selesai';
    $stmtLog->bindParam(':selesai',    $selesaiNow);
    $stmtLog->bindParam(':keterangan', $ket);
    $stmtLog->bindParam(':edited_at',  $editedAt);
    $stmtLog->bindParam(':cabang',     $nama_regional);
    $stmtLog->execute();
} catch (PDOException $e) {
    // silent
}
//hapus file sementara
if (file_exists($temp_file)) {
    unlink($temp_file);
}
//hapus data mysql
try {
    $sqlDelete = "DELETE FROM deliquency_regional WHERE regional = :regional AND tgl_input = :tgl";
    $stmtDelete = $pdo->prepare($sqlDelete);
    $stmtDelete->execute([':regional' => $nama_regional, ':tgl' => $tgl_delin]);
} catch (PDOException $e) {
    // silent
}

// ============================================================
// SAVE & AUTO DOWNLOAD
// ============================================================
$writer   = new Xlsx($spreadsheet);
$folder   = "FILE/";
$filename = "ANALISA DELIN REGIONAL $nama_regional $tgl_delin.xlsx";
$writer->save($folder . $filename);
?>
<h2>SELESAI - ANALISA DELIN REGIONAL <?= htmlspecialchars($nama_regional) ?></h2>
<p>File berhasil dibuat. Download akan dimulai otomatis...</p>
<script>
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = '<?= $url ?>download.php?filename=<?= urlencode($filename) ?>';
    document.body.appendChild(iframe);

    setTimeout(function () {
        window.location.href = '<?= $url ?>index.php?menu=analisa_delin_reg';
    }, 2000);
</script>