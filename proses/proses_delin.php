<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;

$nama_cabang = $_GET['cabang'];
$tgl_delin_awal = $_GET['tgl_delin'];
$tgl_delin_akhir = $_GET['tgl_delin1'];
$ce_blok = $pdo->query("SELECT * from block where LOWER(cabang)=LOWER('$nama_cabang')");
$ce_blok->execute();
if ($ce_blok->rowCount() > 0) {
?>
    <h1>Mohon maaf Proses tidak dilanjutkan, Status : Limited Branch Name</h1>
<?php
    exit;
}

$cek_center = $pdo->query("SELECT no_center, staff from deliquency where  tgl_input = '$tgl_delin_awal' AND cabang = '$nama_cabang' group by no_center ");
$cek_center->execute();
$cek_center = $cek_center->fetchAll(PDO::FETCH_ASSOC);
foreach ($cek_center as $center) {
    $no_center =  $center['no_center'];
    $update = $pdo->query("UPDATE deliquency set staff='$center[staff]' where  tgl_input = '$tgl_delin_akhir' AND cabang = '$nama_cabang' and no_center='$center[no_center]' ");
    $update->execute();
}
$by_staff = array();

?>
<div class="container-fluid">
    <div class="row">
        <h1>SEDANG PROSES DELIN CABANG <?= $nama_cabang ?></h1>
        <h2>Harap tunggu . . .</h2>
    </div>
</div>


<?php
$spreadsheet = new Spreadsheet();


$styleArray = [
    'borders' => [
        'allBorders' => [
            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
        ],
    ],
];
// Buat lembar kerja pertama
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('NAIKTURUN PAR');
$sheet1->mergeCells('A1:O1');
$mergedCell = $sheet1->getCell('A1');
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
// Set teks di tengah
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$barisPertama = $sheet1->getRowDimension(1);
$barisPertama->setRowHeight(40);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet1->getStyle('A2:O2')->getFont()->setBold(true);




$headerData = [
    'NO',
    'LOAN',
    'CENTER',
    'ID AGT',
    'ANGGOTA',
    'TANGGAL DISBURSE',
    'DISBURSE',
    'BALANCE',
    'ARREAS',
    'WEEK PAS',
    'RILL',
    'JANGKA WAKTU',
    'JENIS TOPUP',
    'HARI',
    'STAFF'
];

// Menuliskan header ke dalam sheet
$column = 'A';
foreach ($headerData as $header) {
    $sheet1->setCellValue($column . '2', $header);
    $column++;
}




$judul = "KENAIKAN PAR dari $tgl_delin_awal s/d $tgl_delin_akhir \nCABANG $nama_cabang";
$sheet1->setCellValue('A1', $judul);

// AKHIR SHEET 1/KENAIKAN

//SHEET 3/PENGURANGAN OS PAR
$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('PENURUNANOSPAR');
$sheet3->mergeCells('A1:N1');
$mergedCell = $sheet3->getCell('A1');
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
// Set teks di tengah
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$barisPertama = $sheet3->getRowDimension(1);
$barisPertama->setRowHeight(40);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet3->getStyle('A2:O2')->getFont()->setBold(true);
$judul = "PENGURANGAN OS PAR dari $tgl_delin_awal s/d $tgl_delin_akhir \nCABANG $nama_cabang";
$sheet3->setCellValue('A1', $judul);
$sheet3->setAutoFilter('A2:N2');
$headerData = [
    'NO',
    'LOAN',
    'CENTER',
    'ID AGT',
    'ANGGOTA',
    'TGL DISBURSE',
    'DISBURSE',
    'BALANCE SEBELUM',
    'BALANCE SESUDAH',
    'BERKURANG',
    'WPD',
    'JENIS TOPUP',
    'HARI',
    'STAFF'
];

// Menuliskan header ke dalam sheet pada baris ke-2
$column = 'A';
foreach ($headerData as $header) {
    $sheet3->setCellValue($column . '2', $header);
    $column++;
}


//AKHIR SHEET 3 / PENGURANGAN OS PAR



// REKAP PAR
$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('REKAP PAR');
$sheet4->mergeCells('A1:K1');
$mergedCell = $sheet4->getCell('A1');
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
// Set teks di tengah
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$barisPertama = $sheet4->getRowDimension(1);
$barisPertama->setRowHeight(40);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet4->getStyle('A2:L2')->getFont()->setBold(true);
$judul = "REKAP SEMUA PAR dari $tgl_delin_awal s/d $tgl_delin_akhir \nCABANG $nama_cabang";
$sheet4->setCellValue('A1', $judul);
$sheet4->setAutoFilter('A2:K2');

$headerData = [
    'NO',
    'STAFF',
    'CTR PAR',
    'AGT PAR',
    'REK PAR',
    'PAR NAIK',
    'PAR TURUN',
    'PENGURANGAN OS PAR',
    'TOTAL PENURUNAN',
    'PERUBAHAN',
    'BALANCE PAR',
];
$column = 'A';
foreach ($headerData as $header) {
    $sheet4->setCellValue($column . '2', $header);
    $column++;
}
// AKHIR HEADER REKAP PAR
//SHEET5 ANGGOTA TIDAK ADA PEMBAYARAN


$sheet5 = $spreadsheet->createSheet();
$sheet5->setTitle('ANGGOTA_TIDAK_BAYAR');
$sheet5->mergeCells('A1:S1');
$mergedCell = $sheet5->getCell('A1');
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
// Set teks di tengah
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$barisPertama = $sheet5->getRowDimension(1);
$barisPertama->setRowHeight(40);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet5->getStyle('A2:S2')->getFont()->setBold(true);
$judul = "ANALISA ANGGOTA TIDAK BAYAR $tgl_delin_awal s/d $tgl_delin_akhir \nCABANG $nama_cabang";
$sheet5->setCellValue('A1', $judul);
$sheet5->setAutoFilter('A2:S2');
$headerData = array(
    'NO',
    'LOAN',
    'CTR',
    'CLIENT ID',
    'NASABAH',
    'PRODUK',
    'JENIS TOPUP',
    'DISBURSE',
    'BALANCE BEFORE',
    'BALANCE AFTER',
    'WAJIB BEFORE',
    'WAJIB AFTER',
    'KET WAJIB',
    'SUKARELA BEFORE',
    'SUKARELA AFTER',
    'SELISIH SUKARELA',
    'KET SUKARELA',
    'HARI',
    'STAFF'
);
$column = 'A';
foreach ($headerData as $header) {
    $sheet5->setCellValue($column . '2', $header);
    $column++;
}
//AKHIR SHEET5


$no = 1;
$baris_1 = 3;
$sql_naik = "SELECT * FROM deliquency WHERE `loan` NOT IN (SELECT loan FROM deliquency WHERE tgl_input='$tgl_delin_awal') AND tgl_input='$tgl_delin_akhir' AND cabang='$nama_cabang' order by staff";
$stmt = $pdo->query($sql_naik);
$hitung_naik = $stmt->rowCount();
if ($hitung_naik > 0) {
    foreach ($stmt->fetchAll() as $row) {

        $isiData = [
            $no,
            $row['loan'],
            $row['no_center'],
            $row['id_detail_nasabah'],
            $row['nasabah'],
            $row['tgl_disburse'],
            $row['amount'],
            $row['sisa_saldo'],
            $row['tunggakan'],
            $row['minggu'],
            $row['minggu_rill'],
            $row['priode'],
            $row['jenis_topup'],
            $row['hari'],
            $row['staff']
        ];

        $sql_update = "update deliquency set keterangan='naik' where id='$row[id]'";
        $upd = $pdo->query($sql_update);
        $by_staff['naik'][$row['staff']][] = $row['sisa_saldo'];
        // Menuliskan isi ke dalam sheet
        $column = 'A';
        foreach ($isiData as $isi) {
            $sheet1->setCellValue($column . $baris_1, $isi);
            $column++;
        }
        $baris_1++;
        $no++;
    }
    $sheet1->mergeCells('A' . $baris_1 . ':F' . $baris_1);
    $sheet1->setCellValue('A' . $baris_1, "TOTAL");
    foreach (range('A', 'O') as $col) {
        $sheet1->getStyle($col . $baris_1)->applyFromArray($styleArray); //INI UNTUK BORDER
    }
    $kurang_1 = $baris_1 - 1;
    foreach (range('G', 'I') as $col) {
        $sheet1->setCellValue($col . $baris_1, "=SUM(" . $col . "3:" . $col . $kurang_1 . ")"); //INI UNTUK BORDER
    }

    // echo $baris_1;
}



// PENURUNAN

$baris_baru = $baris_1 + 3;

$header_baru = $baris_baru + 1;

$judul = "PENURUNAN PAR dari $tgl_delin_awal s/d $tgl_delin_akhir \nCABANG $nama_cabang";
$sheet1->setCellValue('A' . $baris_baru, $judul);

$headerData = [
    'NO',
    'LOAN',
    'CENTER',
    'ID AGT',
    'ANGGOTA',
    'TANGGAL DISBURSE',
    'DISBURSE',
    'BALANCE',
    'ARREAS',
    'WEEK PAS',
    'RILL',
    'JANGKA WAKTU',
    'JENIS TOPUP',
    'HARI',
    'STAFF'
];

// Menuliskan header ke dalam sheet
$column = 'A';
$no = 1;
foreach ($headerData as $header) {
    $sheet1->setCellValue($column . $header_baru, $header);
    $column++;
}
$sheet1->mergeCells('A' . $baris_baru . ':O' . $baris_baru);
$mergedCell = $sheet1->getCell('A' . $baris_baru);
$mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
// Set teks di tengah
$mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
$mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
$barisPertama = $sheet1->getRowDimension($baris_baru);
$barisPertama->setRowHeight(40);
$mergedCell->getStyle()->getAlignment()->setWrapText(true);
$sheet1->getStyle('A' . $header_baru . ':O' . $header_baru)->getFont()->setBold(true);


$baris_turun = $header_baru + 1;
$sql_turun = "SELECT * FROM deliquency WHERE `loan` NOT IN (SELECT loan FROM deliquency WHERE tgl_input = '$tgl_delin_akhir') AND tgl_input = '$tgl_delin_awal'AND cabang = '$nama_cabang' order by staff";
$stmt = $pdo->query($sql_turun);
$hitung_turun = $stmt->rowCount();
if ($hitung_turun > 0) {
    foreach ($stmt->fetchAll() as $row) {

        $isiData = [
            $no,
            $row['loan'],
            $row['no_center'],
            $row['id_detail_nasabah'],
            $row['nasabah'],
            $row['tgl_disburse'],
            $row['amount'],
            $row['sisa_saldo'],
            $row['tunggakan'],
            $row['minggu'],
            $row['minggu_rill'],
            $row['priode'],
            $row['jenis_topup'],
            $row['hari'],
            $row['staff']
        ];

        $by_staff['turun'][$row['staff']][] = $row['sisa_saldo'];
        $sql_update = "update deliquency set keterangan='turun' where id='$row[id]'";
        $upd = $pdo->query($sql_update);
        // Menuliskan isi ke dalam sheet
        $column = 'A';
        foreach ($isiData as $isi) {
            $sheet1->setCellValue($column . $baris_turun, $isi);
            $column++;
        }
        $baris_turun++;
        $no++;
    }
    $sheet1->mergeCells('A' . $baris_turun . ':F' . $baris_turun);
    $sheet1->setCellValue('A' . $baris_turun, "TOTAL");
    foreach (range('A', 'O') as $col) {
        $sheet1->getStyle($col . $baris_turun)->applyFromArray($styleArray); //INI UNTUK BORDER
    }
    $kurang_1 = $baris_turun - 1;
    foreach (range('G', 'I') as $col) {
        $sheet1->setCellValue($col . $baris_turun, "=SUM(" . $col . $header_baru . ":" . $col . $kurang_1 . ")"); //INI UNTUK BORDER
    }

    // echo $baris_1;
}

//PENURUNAN OS

$sql_kurang = "SELECT * FROM deliquency WHERE `loan` IN (SELECT loan FROM deliquency WHERE tgl_input = '$tgl_delin_awal') AND tgl_input = '$tgl_delin_akhir'AND cabang = '$nama_cabang' order by staff asc,hari desc";
$stmt = $pdo->query($sql_kurang);
// echo $sql_kurang;
$total_berkurang = 0;
$baris_3 = 3;
$no = 1;


foreach ($stmt->fetchAll() as $row) {
    $sq_saldo = "select sisa_saldo from deliquency where loan='$row[loan]' and tgl_input='$tgl_delin_awal' and cabang='$nama_cabang'";
    $saldo_banding = $pdo->query($sq_saldo);
    $saldo_banding = $saldo_banding->fetch()['sisa_saldo'];
    $saldo_sebelum = $row['sisa_saldo'];
    $saldo_berkurang =   $saldo_banding - $saldo_sebelum;


    if ($saldo_berkurang > 0) {
        $isidata = [
            $no,
            $row['loan'],
            $row['no_center'],
            $row['id_detail_nasabah'],
            $row['nasabah'],
            $row['tgl_disburse'],
            $row['amount'],
            $saldo_banding,
            $saldo_sebelum,
            $saldo_berkurang,
            $row['minggu'],
            $row['jenis_topup'],
            $row['hari'],
            $row['staff']
        ];
        $by_staff['turunos'][$row['staff']][] = $saldo_berkurang;

        // Menuliskan header ke dalam sheet pada baris ke-2
        $sql_update = "update deliquency set keterangan='turunos',perubahan='$saldo_berkurang' where id='$row[id]'";
        $upd = $pdo->query($sql_update);
        $column = 'A';
        foreach ($isidata as $isi) {
            $sheet3->setCellValue($column . $baris_3, $isi);
            $column++;
        }
        $baris_3++;
        $no++;
    }
}

// $keyToSum = "06419/08/23 - PUTRI CAHYANI EKA SAFITRI";



$sheet3->mergeCells('A' . $baris_3 . ':F' . $baris_3);
$sheet3->setCellValue('A' . $baris_3, "TOTAL");
$kurang_1 = $baris_3 - 1;
foreach (range('G', 'J') as $col) {
    $sheet3->setCellValue($col . $baris_3, "=SUM(" . $col . 3 . ":" . $col . $kurang_1 . ")"); //INI UNTUK BORDER
}
//AKHIR PENURUNAN OS


// SHEET 4 REKAP SEMUA PAR

$no = 1;
$sql_rekap = "SELECT staff, COUNT(DISTINCT no_center) AS ctr_par,SUM(sisa_saldo) AS saldo,COUNT(DISTINCT id_detail_nasabah) AS agt_par,COUNT(id_detail_nasabah) AS rek_par FROM deliquency WHERE tgl_input='$tgl_delin_akhir' AND cabang='$nama_cabang' GROUP BY staff order by staff asc";
$stmt = $pdo->query($sql_rekap);
$baris_4 = 3;
foreach ($stmt->fetchAll() as $row) {
    $keyToSum = $row['staff'];

    $par_naik = jumlah_staff_cab($pdo, 'naik', $tgl_delin_akhir, $keyToSum, $nama_cabang);
    $par_turun = jumlah_staff_cab($pdo, 'turun', $tgl_delin_awal, $keyToSum, $nama_cabang);;
    $turun_os = jumlah_staff_cab($pdo, 'turunos', $tgl_delin_akhir, $keyToSum, $nama_cabang);;
    $total_penurunan = $turun_os + $par_turun;

    $total_perubahan =  $par_naik - $total_penurunan;
    $headerData = [
        $no,
        $row['staff'],
        $row['ctr_par'],
        $row['agt_par'],
        $row['rek_par'],
        $par_naik,
        $par_turun,
        $turun_os,
        $total_penurunan,
        $total_perubahan,
        $row['saldo'],
    ];
    $column = 'A';
    foreach ($headerData as $header) {
        $sheet4->setCellValue($column . $baris_4, $header);
        $column++;
    }
    if ($total_perubahan > 0) {
        $sheet4->getStyle('A' . $baris_4 . ':K' . $baris_4)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('FFB7B7'); // Warna MERAH
    }
    if ($total_perubahan < 0) {
        $sheet4->getStyle('A' . $baris_4 . ':K' . $baris_4)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('8DFB90'); // Warna HIJAU
    }
    if ($total_perubahan == 0) {
        $sheet4->getStyle('A' . $baris_4 . ':K' . $baris_4)
            ->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('DFE1E5'); // Warna MERAH
    }

    $no++;
    $baris_4++;
}
$sheet4->mergeCells('A' . $baris_4 . ':B' . $baris_4);
$sheet4->setCellValue('A' . $baris_4, "TOTAL");
$kurang_1 = $baris_4 - 1;
foreach (range('C', 'K') as $col) {
    $sheet4->setCellValue($col . $baris_4, "=SUM(" . $col . 3 . ":" . $col . $kurang_1 . ")"); //INI UNTUK BORDER
}


// $jumlah = array_sum($by_staff['turunos'][$keyToSum]);
//AKHIR SHEET4 REKAP SEMUA


//SHEET 5 ANGGOTA TIDAK BAYAR

$no = 1;
$sql_tdk_bayar = "SELECT * FROM deliquency WHERE `loan` IN (SELECT loan FROM deliquency WHERE tgl_input = '$tgl_delin_akhir') AND tgl_input = '$tgl_delin_awal' AND cabang = '$nama_cabang' order by staff asc,hari desc";
$stmt = $pdo->query($sql_tdk_bayar);
$baris_5 = 3;
foreach ($stmt->fetchAll() as $row) {
    $sql_banding = "SELECT * FROM deliquency WHERE loan ='$row[loan]' AND tgl_input = '$tgl_delin_akhir' AND cabang = '$nama_cabang' ";
    $smt = $pdo->query($sql_banding);
    $hitung = $smt->rowCount();
    $banding = $smt->fetch();
    $saldo_after = $banding['sisa_saldo'];
    $saldo_before = $row['sisa_saldo'];
    if ($saldo_after == $saldo_before) {
        $wajib_before = $row['wajib'];
        $wajib_after = $banding['wajib'];
        $sukarela_before = $row['sukarela'];
        $sukarela_after = $banding['sukarela'];
        $banding_sukarela = $sukarela_after - $sukarela_before;

        if ($banding_sukarela > 0) $ket_sukarela = "bertambah";
        else if ($banding_sukarela < 0) $ket_sukarela = "berkurang";
        else $ket_sukarela = "tidakberubah";


        if ($wajib_after == $wajib_before) $ket_wajib = "tidakberubah";
        else $ket_wajib = "berubah";

        $headerData = array(
            $no,
            $row['loan'],
            $row['no_center'],
            $row['id_detail_nasabah'],
            $row['nasabah'],
            $row['kode_pemb'],
            $row['jenis_topup'],
            $row['amount'],
            $saldo_before,
            $saldo_after,
            $wajib_before,
            $wajib_after,
            $ket_wajib,
            $sukarela_before,
            $sukarela_after,
            $banding_sukarela,
            $ket_sukarela,
            $row['hari'],
            $banding['staff']
        );
        $column = 'A';
        foreach ($headerData as $header) {
            $sheet5->setCellValue($column . $baris_5, $header);
            $column++;
        }

        $no++;
        $baris_5++;
    }
}

//SHEET 5 ANGGOTA TIDAK BAYAR


//SETING SHEET 1/KENAIKAN
$batas_sh1 = $hitung_naik + 3;
$sheet1->getStyle('G3:L' . $batas_sh1)->getNumberFormat()->setFormatCode('#,##0');
$sheet1->getStyle("A2:Z" . $batas_sh1)->getFont()->setSize(8);
$sheet1->getStyle('A2:O' . $batas_sh1)->applyFromArray($styleArray); //INI UNTUK BORDER
foreach (range('A', 'O') as $col) {
    $sheet1->getColumnDimension($col)->setAutoSize(true);
}
//PENURUNAN
$batas_sh2 = $header_baru;
$akhir_batas_sh2 = $hitung_turun + $batas_sh2 + 1;
$sheet1->getStyle('G' . $batas_sh2 . ':L' . $akhir_batas_sh2)->getNumberFormat()->setFormatCode('#,##0');
$sheet1->getStyle('A' . $batas_sh2 . ':Z' . $akhir_batas_sh2)->getFont()->setSize(8);
$sheet1->getStyle('A' . $batas_sh2 . ':O' . $akhir_batas_sh2)->applyFromArray($styleArray); //INI UNTUK BORDER

//SHEET 3/ pengurangan oS par

$batas_sh3 = $baris_3;
$sheet3->getStyle('G3:J' . $batas_sh3)->getNumberFormat()->setFormatCode('#,##0');
$sheet3->getStyle("A2:Z" . $batas_sh3)->getFont()->setSize(8);
$sheet3->getStyle('A2:N' . $batas_sh3)->applyFromArray($styleArray); //INI UNTUK BORDER
// Mengatur lebar kolom secara otomatis
foreach (range('A', 'N') as $col) {
    $sheet3->getColumnDimension($col)->setAutoSize(true);
}

//SHEET4
foreach (range('A', 'L') as $col) {
    $sheet4->getColumnDimension($col)->setAutoSize(true);
}
$batas_sh4 = $baris_4;
$sheet4->getStyle('C3:K' . $batas_sh4)->getNumberFormat()->setFormatCode('#,##0');
$sheet4->getStyle("A2:Z" . $batas_sh4)->getFont()->setSize(8);
$sheet4->getStyle('A2:K' . $batas_sh4)->applyFromArray($styleArray); //INI UNTUK BORDER


//SHEET5
foreach (range('A', 'S') as $col) {
    $sheet5->getColumnDimension($col)->setAutoSize(true);
}
$batas_sh5 = $baris_5;
$sheet5->getStyle('C3:S' . $batas_sh5)->getNumberFormat()->setFormatCode('#,##0');
$sheet5->getStyle("A2:Z" . $batas_sh5)->getFont()->setSize(8);
$sheet5->getStyle('A2:S' . $batas_sh5)->applyFromArray($styleArray); //INI UNTUK BORDER




$writer = new Xlsx($spreadsheet);
$folder = "FILE/";
$filename = "CEK PAR $nama_cabang $tgl_delin_awal - $tgl_delin_akhir.xlsx";
$writer->save($folder . $filename);

//hapus delin
$hapus_delin = $pdo->query("DELETE FROM deliquency WHERE cabang='$nama_cabang' AND (tgl_input='$tgl_delin_awal' OR tgl_input='$tgl_delin_akhir') ");
$hapus_delin->execute();

// Auto-download dan kembali ke halaman cek_par
?>
<script>
    // Trigger download via iframe
    var iframe = document.createElement('iframe');
    iframe.style.display = 'none';
    iframe.src = 'download.php?filename=<?= urlencode($filename) ?>&cleanup=delin_session&session=<?= urlencode((string)($sesi ?? '')) ?>';
    document.body.appendChild(iframe);
    
    // Redirect setelah 2 detik
    setTimeout(function() {
        window.location.href = 'index.php?menu=cek_par';
    }, 2000);
</script>
<?php
