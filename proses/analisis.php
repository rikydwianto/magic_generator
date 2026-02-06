<?php
error_reporting(0);

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;


?>

<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-analisa">
                <h1 class="page-title">
                    <i class="fas fa-chart-bar me-3"></i>ANALISA PAR
                </h1>
                <p class="mb-0">Analisis mendalam deliquency dan breakdown detail per loan & center</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-md-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #81ecec 0%, #74b9ff 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-microscope me-2"></i>Upload File untuk Analisa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box-success mb-4">
                        <i class="fas fa-info-circle text-success"></i>
                        <div>
                            <strong>Fitur Analisa:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Breakdown detail per loan dan center</li>
                                <li>Visualisasi data dengan grafik interaktif</li>
                                <li>Export hasil analisa ke Excel</li>
                                <li>Analisis trend dan pola PAR</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formAnalisa">
                        <div class="mb-4">
                            <label for="formFileAnalisa" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-2"></i>Pilih File Excel PAR
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file' accept=".xls,.xlsx" id="formFileAnalisa">
                            <small class="text-muted">Upload 1 file Excel format .xls atau .xlsx untuk dianalisa</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='preview' class="btn btn-success btn-lg" onclick="return confirmAction('Mulai proses analisa file ini?', function() { showLoading('Sedang menganalisa data...'); })">
                                <i class="fas fa-play-circle me-2"></i>Mulai Analisa
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formAnalisa');
                        if (!form) return;
                        form.addEventListener('submit', function(e) {
                            var file = document.getElementById('formFileAnalisa');
                            if (!file) return;
                            if (!file.value) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'File harus terisi',
                                        text: 'Silakan pilih file Excel yang akan dianalisa.'
                                    });
                                } else {
                                    alert('File harus terisi');
                                }
                            }
                        });
                    })();
                    </script>
                </div>
            </div>
        </div>

        <!-- Queue Card -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>Antrian Analisa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class='table table-hover'>
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Cabang</th>
                                    <th width="25%">Waktu Mulai</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM log_cek_par WHERE keterangan='proses-analisa' ORDER BY created_at DESC";
                                $stmt = $pdo->query($sql);

                                $no = 1;
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= $row['cabang'] ?></strong></td>
                                        <td><?= $row['mulai'] ?></td>
                                        <td><span class="badge bg-info"><i class="fas fa-spinner fa-spin me-1"></i>Analisa</span></td>
                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada antrian analisa</td></tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

if (isset($_POST['preview'])) {
    if (empty($_FILES['file']['name']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        alert("File harus terisi");
        pindah("index.php?menu=anal");
        return;
    }

    $file = $_FILES['file']['tmp_name'];
    $path = $file;
    $reader = IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $tgl_delin = "";
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s([^\s]+)/';
    $regex = '/Cabang\s+([A-Z\s]+?)As/';

    // Pencocokan regex
    preg_match($regex, $text, $matches);

    // Ambil hasil pencocokan (nama cabang)
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);

    $ce_blok = $pdo->query("SELECT * from block where LOWER(cabang)=LOWER('$namaCabang')");
    $ce_blok->execute();
    if ($ce_blok->rowCount() > 0) {
?>
<h1>Mohon maaf Proses tidak dilanjutkan, Status : Limited Branch Name</h1>
<?php
        exit;
    }

    if ($namaCabang != "") {

        try {
            $sql = "INSERT INTO log_cek_par (cabang, mulai, selesai, keterangan, created_at, edited_at)
                VALUES (:cabang, :mulai, :selesai, :keterangan, NOW(), NOW())";

            // Menyiapkan statement PDO
            $stmt = $pdo->prepare($sql);
            $mulai = date("H:i:s");
            $selesai = "";
            $keterangan = "proses-analisa";

            // Binding parameter ke statement PDO
            $stmt->bindParam(':cabang', $namaCabang);
            $stmt->bindParam(':mulai', $mulai);
            $stmt->bindParam(':selesai', $selesai);
            $stmt->bindParam(':keterangan', $keterangan);

            // Mengeksekusi statement PDO
            $stmt->execute();
            $id_log = $pdo->lastInsertId();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        alert("Ditolak, Bukan File Delin atau belum di save/save as");
        pindah("index.php?menu=anal");
    }


    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin =  $tgl[0];


    $row_os_par = $last_row - 1;
    $row_os_total = $last_row - 2;
    $os_total = ganti_karakter($ws->getCell("E" . $row_os_total)->getValue());
    $os_par = ganti_karakter($ws->getCell("E" . $row_os_par)->getValue());
    $persen_par  = ($os_par / $os_total);


    $spreadsheet = new Spreadsheet();

    // Buat lembar kerja pertama
    $sheet1 = $spreadsheet->getActiveSheet();
    $sheet1->setTitle('DATA PAR');

    // Isi data untuk lembar kerja pertama
    $judul = "DATA PAR CABANG $namaCabang\nPER $tgl_delin";
    $sheet1->setCellValue('A1', $judul);
    $sheet1->setCellValue('A2', 'NO');
    $sheet1->setCellValue('B2', 'ID');
    $sheet1->setCellValue('C2', 'NASABAH');
    $sheet1->setCellValue('D2', 'CENTER');
    $sheet1->setCellValue('E2', 'LOAN');
    $sheet1->setCellValue('F2', 'PEMB');
    $sheet1->setCellValue('G2', 'DISBURSE DATE');
    $sheet1->setCellValue('H2', 'KE');
    $sheet1->setCellValue('I2', 'RILL');
    $sheet1->setCellValue('J2', 'AMOUNT');
    $sheet1->setCellValue('K2', 'BALANCE');
    $sheet1->setCellValue('L2', 'CICILAN');
    $sheet1->setCellValue('M2', 'WAJIB');
    $sheet1->setCellValue('N2', 'SUKARELA');
    $sheet1->setCellValue('O2', 'PENSIUN');
    $sheet1->setCellValue('P2', 'HARI RAYA');
    $sheet1->setCellValue('Q2', 'TOTAL SIMPANAN');
    $sheet1->setCellValue('R2', 'PAR');
    // $sheet1->setCellValue('S2', '1 Angsuran');
    // $sheet1->setCellValue('T2', 'Tanpa Margin');
    $sheet1->setCellValue('S2', 'STAFF');
    $sheet1->setCellValue('T2', 'HARI');
    $sheet1->setCellValue('U2', 'PRIODE');
    $sheet1->setCellValue('V2', 'JENIS TOPUP');
    $sheet1->setCellValue('W2', 'KETERANGAN');

    $sheet1->mergeCells('A1:V1');
    $mergedCell = $sheet1->getCell('A1');
    $mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
    // Set teks di tengah
    $mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $barisPertama = $sheet1->getRowDimension(1);
    $barisPertama->setRowHeight(60);
    $mergedCell->getStyle()->getAlignment()->setWrapText(true);
    $sheet1->setAutoFilter('A2:X2');
    $sheet1->getStyle('A2:X2')->getFont()->setBold(true);

    foreach (range('A', 'Y') as $col) {
        $sheet1->getColumnDimension($col)->setAutoSize(true);
    }


    $styleArray = [
        'borders' => [
            'allBorders' => [
                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            ],
        ],
    ];



    // Buat lembar kerja kedua
    $sheet2 = $spreadsheet->createSheet();
    $sheet2->setTitle('SUKARELA');

    $judul = "DATA SIMPANAN SUKARELA UNTUK ANGSURAN\nCABANG $namaCabang\nDELIN TGL $tgl_delin";
    $sheet2->setCellValue('A1', $judul);
    $sheet2->mergeCells('A1:S1');
    $mergedCell = $sheet2->getCell('A1');
    $mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
    // Set teks di tengah
    $mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $barisPertama = $sheet2->getRowDimension(1);
    $barisPertama->setRowHeight(60);
    $mergedCell->getStyle()->getAlignment()->setWrapText(true);
    $barisA2 = $sheet2->getRowDimension(2);
    $barisA2->setRowHeight(30);
    for ($col = 'A'; $col <= 'S'; $col++) {

        $sheet2->getStyle($col . '2')->getAlignment()->setWrapText(true);
        $sheet2->getStyle($col . '2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    $sheet2->setAutoFilter('A2:S2');
    $sheet2->getStyle('A2:S2')->getFont()->setBold(true);
    $sheet2->setCellValue('A2', 'NO');
    $sheet2->setCellValue('B2', 'CENTER');
    $sheet2->setCellValue('C2', 'ID');
    $sheet2->setCellValue('D2', 'NASABAH');
    $sheet2->setCellValue('E2', 'PEMB');
    $sheet2->setCellValue('F2', 'KE');
    $sheet2->setCellValue('G2', 'RILL');
    $sheet2->setCellValue('H2', 'AMOUNT');
    $sheet2->setCellValue('I2', 'BALANCE');
    $sheet2->setCellValue('J2', 'CICILAN');
    $sheet2->setCellValue('K2', 'WAJIB');
    $sheet2->setCellValue('L2', 'SUKARELA');
    $sheet2->setCellValue('M2', "WEEK PASS \nDUE");
    $sheet2->setCellValue('N2', "MASUK \nANGSURAN ");
    $sheet2->setCellValue('O2', "MASUK ANGSURAN \nX CICILAN");
    $sheet2->setCellValue('P2', 'SISA SUKARELA ');
    $sheet2->setCellValue('Q2', 'KETERANGAN');
    $sheet2->setCellValue('R2', 'STAFF');
    $sheet2->setCellValue('S2', 'HARI');


    ///AKHIR HEADER SHEET 2/SUKARELA



    //AWAL SHEET3/PELUNASAN
    $sheet3 = $spreadsheet->createSheet();
    $sheet3->setTitle('PELUNASAN');

    $judul = "DATA PINJAMAN YANG BISA DILUNASI DARI SIMPANAN \nPERHITUNGAN TANPA MARGIN < 100000 \nCABANG $namaCabang DELIN TGL $tgl_delin";
    $sheet3->setCellValue('A1', $judul);
    $sheet3->mergeCells('A1:T1');
    $mergedCell = $sheet3->getCell('A1');
    $mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
    // Set teks di tengah
    $mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $barisPertama = $sheet3->getRowDimension(1);
    $barisPertama->setRowHeight(60);
    $mergedCell->getStyle()->getAlignment()->setWrapText(true);
    $barisA2 = $sheet3->getRowDimension(2);
    $barisA2->setRowHeight(30);
    for ($col = 'A'; $col <= 'T'; $col++) {

        $sheet3->getStyle($col . '2')->getAlignment()->setWrapText(true);
        $sheet3->getStyle($col . '2')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    }

    $sheet3->setAutoFilter('A2:T2');
    $sheet3->getStyle('A2:T2')->getFont()->setBold(true);
    $sheet3->setCellValue('A2', 'NO');
    $sheet3->setCellValue('B2', 'CENTER');
    $sheet3->setCellValue('C2', 'ID');
    $sheet3->setCellValue('D2', 'LOANNO');
    $sheet3->setCellValue('E2', 'NASABAH');
    $sheet3->setCellValue('F2', 'PEMB');
    $sheet3->setCellValue('G2', 'KE');
    $sheet3->setCellValue('H2', 'RILL');
    $sheet3->setCellValue('I2', 'AMOUNT');
    $sheet3->setCellValue('J2', 'BALANCE');
    $sheet3->setCellValue('K2', 'CICILAN');
    $sheet3->setCellValue('L2', 'WAJIB');
    $sheet3->setCellValue('M2', 'SUKARELA');
    $sheet3->setCellValue('N2', 'PENSIUN');
    $sheet3->setCellValue('O2', 'HARI RAYA');
    $sheet3->setCellValue('P2', 'TOTAL SIMPANAN');
    $sheet3->setCellValue('Q2', 'KURANG/LEBIH');
    $sheet3->setCellValue('R2', 'HARI');
    $sheet3->setCellValue('S2', 'STAFF');
    $sheet3->setCellValue('T2', 'KETERANGAN');

    foreach (range('A', 'Y') as $col) {
        $sheet3->getColumnDimension($col)->setAutoSize(true);
    }

    //AKHIR PELUNASAN


    //INFORMASI
    $sheet4 = $spreadsheet->createSheet();
    $sheet4->setTitle('INFORMASI');

    $judul = "INFORMASI PAR CABANG $namaCabang \nTGL $tgl_delin";
    $sheet4->setCellValue('A1', $judul);
    $sheet4->mergeCells('A1:S1');
    $mergedCell = $sheet4->getCell('A1');
    $mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
    // Set teks di tengah
    $mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $barisPertama = $sheet4->getRowDimension(1);
    $barisPertama->setRowHeight(50);
    $mergedCell->getStyle()->getAlignment()->setWrapText(true);


    foreach (range('A', 'Y') as $col) {
        $sheet4->getColumnDimension($col)->setAutoSize(true);
    }

    //AKHIR INFORMASI

    //SHEET 5 ANALISA TPK
    $sheet5 = $spreadsheet->createSheet();
    $sheet5->setTitle('ANALISA_TPK');
    $sheet5->mergeCells('A1:Q1');
    $mergedCell = $sheet5->getCell('A1');
    $mergedCell->getStyle()->getFont()->setBold(true)->setSize(15);
    // Set teks di tengah
    $mergedCell->getStyle()->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
    $mergedCell->getStyle()->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
    $barisPertama = $sheet5->getRowDimension(1);
    $barisPertama->setRowHeight(40);
    $mergedCell->getStyle()->getAlignment()->setWrapText(true);
    $sheet5->getStyle('A2:Q2')->getFont()->setBold(true);
    $judul = "ANALISA TPK UMUM DAN MIKROBISNIS CABANG $namaCabang\nper TANGGAL $tgl_delin";
    $sheet5->setCellValue('A1', $judul);
    $sheet5->setAutoFilter('A2:S2');

    // NO	LOAN	CENTER	ID AGT	ANGGOTA	RIll	DISBURSE	BALANCE	TOPUP	ANGSURAN	ANGSURAN	ANGSURAN	ANGSURAN	HARI	STAFF
    // 								BAL + 1%	25 + margin	50 + margin	75 + margin	100 + margin		

    $headerData = [
        'NO',
        'LOANNO',
        'CTR',
        'ID',
        'NAMA ANGGOTA',
        'PRODUK',
        'JK WAKTU',
        'RILL',
        'DISBURSE',
        'BALANCE',
        'TOTAL TOPUP',
        '25 minggu',
        '50 minggu',
        '75 minggu',
        '100 minggu',
        'HARI',
        'STAFF',
        'JENISTOPUP',
        'Keterangan',
    ];
    $column = 'A';
    foreach ($headerData as $header) {
        $sheet5->setCellValue($column . '2', $header);
        $column++;
    }
    //AKHIR ANALISA TPK



    $no = 1;
    $baris_baru = 3;
    $baris_2 = 3;
    $no2 = 1;
    $no3 = 1;
    $baris_3 = 3;

    //HITUNG INFORMASI
    $total_os_par = 0;
    $total_topup = 0;
    $total_bukan_topup = 0;
    $total_tpk = 0;

    $sql_delete = "DELETE FROM deliquency WHERE tgl_input='$tgl_delin' and cabang='$namaCabang'";
    $stmt = $pdo->query($sql_delete);

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
            // Cek apakah nilai adalah tanggal Excel (numeric)
            if (is_numeric($tgl_dis_raw)) {
                $tgl_dis = date("Y-m-d", Date::excelToDateTimeObject($tgl_dis_raw)->getTimestamp());
            } else {
                // Jika sudah string tanggal, parse langsung
                $tgl_dis = date("Y-m-d", strtotime($tgl_dis_raw));
            }
            $tanggal = date_create($tgl_dis)->format('m/d/Y');

            // SIMPANAN
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
            $tujuan =  ganti_karakter($ws->getCell("AE" . $row)->getValue());
            $hari =  ganti_karakter($ws->getCell("AF" . $row)->getValue());
            $staff =  ganti_karakter1($ws->getCell("AG" . $row)->getValue());
            $jenis_topup =  ganti_karakter($ws->getCell("AH" . $row)->getValue());

            $nama_nasabah = str_replace("'", " ", $nama_nasabah);
            $sql = "INSERT INTO deliquency (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session,jenis_topup) VALUES ('$loan_no', '$no_center', '$client_id', '$nama_nasabah', $disburse, $balance, $arreas, $wpd, '$tgl_delin', '', '$tgl_dis', '$namaCabang', $s_wajib, $s_sukarela, $s_pensiun, $s_hariraya, 0, $angsuran, '$hari', '$staff', $ke, $rill, $jk, '$jenis_produk', '$sesi','$jenis_topup')";

            if ($pdo->query($sql) == TRUE) {
                //  echo "Data berhasil dimasukkan ke tabel deliquency.";
            } else {
                // echo "$nama_nasabah :  Error: ".$pdo->error;
            }
            $total_os_par = $balance + $total_os_par;
            if ($jenis_topup == "NON TOP-UP") {
                $jenis_topup = "TIDAKTOPUP";
                $total_bukan_topup++;
            } else {
                $jenis_topup = $jenis_topup;
                if ($jenis_topup == 'KHUSUS') {
                    $total_tpk++;
                } else {
                    $total_topup++;
                }
            }
            if ($jk == $ke) {
                $ket_sh1 = "Pinj. Sudah jatuh tempo";
            } else {
                $ket_sh1 = "";
            }

            $total_simpanan = $s_wajib  + $s_sukarela + $s_pensiun + $s_hariraya;
            $sheet1->setCellValue('A' . $baris_baru, $no);
            $sheet1->setCellValue('B' . $baris_baru, $client_id);
            $sheet1->setCellValue('C' . $baris_baru, $nama_nasabah);
            $sheet1->setCellValue('D' . $baris_baru, no_center($no_center));
            $sheet1->setCellValue('E' . $baris_baru, $loan_no);
            $sheet1->setCellValue('F' . $baris_baru, $jenis_produk);
            $sheet1->setCellValue('G' . $baris_baru, $tanggal);
            $sheet1->setCellValue('H' . $baris_baru, $ke);
            $sheet1->setCellValue('I' . $baris_baru, $rill);
            $sheet1->setCellValue('J' . $baris_baru, $disburse);
            $sheet1->setCellValue('K' . $baris_baru, $balance);
            $sheet1->setCellValue('L' . $baris_baru, $angsuran);
            $sheet1->setCellValue('M' . $baris_baru, $s_wajib);
            $sheet1->setCellValue('N' . $baris_baru, $s_sukarela);
            $sheet1->setCellValue('O' . $baris_baru, $s_pensiun);
            $sheet1->setCellValue('P' . $baris_baru, $s_hariraya);
            $sheet1->setCellValue('Q' . $baris_baru, $total_simpanan);
            $sheet1->setCellValue('R' . $baris_baru, $wpd);
            $sheet1->setCellValue('S' . $baris_baru, $staff); //
            $sheet1->setCellValue('T' . $baris_baru,  $hari);
            $sheet1->setCellValue('U' . $baris_baru, $jk);
            $sheet1->setCellValue('V' . $baris_baru, $jenis_topup);
            $sheet1->setCellValue('W' . $baris_baru, $ket_sh1);


            // SHEET 2

            $sukarela = ($s_sukarela - 2000);
            if ($sukarela > $angsuran) {

                $cek_angsuran =  floor(($sukarela) / ($angsuran));
                $angsuran_masuk = $cek_angsuran * $angsuran;
                $sisa_sukarela = $s_sukarela - $angsuran_masuk;

                if ($cek_angsuran > $wpd) {
                    $ket = 'Turun PAR';
                } else {
                    $ket = '';
                }
                $sheet2->setCellValue('A' . $baris_2, $no2);
                $sheet2->setCellValue('B' . $baris_2, no_center($no_center));
                $sheet2->setCellValue('C' . $baris_2, $client_id);
                $sheet2->setCellValue('D' . $baris_2, $nama_nasabah);
                $sheet2->setCellValue('E' . $baris_2, $jenis_produk);
                $sheet2->setCellValue('F' . $baris_2, $ke);
                $sheet2->setCellValue('G' . $baris_2, $rill);
                $sheet2->setCellValue('H' . $baris_2, $disburse);
                $sheet2->setCellValue('I' . $baris_2, $balance);
                $sheet2->setCellValue('J' . $baris_2, $angsuran);
                $sheet2->setCellValue('K' . $baris_2, $s_wajib);
                $sheet2->setCellValue('L' . $baris_2, $s_sukarela);
                $sheet2->setCellValue('M' . $baris_2, $wpd);
                $sheet2->setCellValue('N' . $baris_2, $cek_angsuran);
                $sheet2->setCellValue('O' . $baris_2, $angsuran_masuk);
                $sheet2->setCellValue('P' . $baris_2, $sisa_sukarela);
                $sheet2->setCellValue('Q' . $baris_2, $ket);
                $sheet2->setCellValue('R' . $baris_2, $staff);
                $sheet2->setCellValue('S' . $baris_2, $hari);
                $baris_2++;
                $no2++;
            }


            //PELUNASAN


            $s_wajib = $s_wajib - 2000;
            $s_sukarela = $s_sukarela - 2000;
            $s_pensiun = $s_pensiun - 2000;
            $s_hariraya = ($s_hariraya > 2000) ? $s_hariraya - 2000 : 0;

            $total_simpanan = $s_wajib + $s_sukarela + $s_pensiun + $s_hariraya;


            $pelunasan = $balance - $total_simpanan;

            if ($pelunasan <= 100000) {
                if ($pelunasan < 0) {
                    $ket = "Pinjaman dapat dilunasi";
                } else {
                    $pel = round($pelunasan / 10000, 1);
                    $pel =  ceil($pel) * 10000;
                    $ket = "Kurang sekitar " . rupiah($pel) . " untuk dapat melunasi pinjaman ini";
                }
                $sheet3->setCellValue('A' . $baris_3, $no3);
                $sheet3->setCellValue('B' . $baris_3, no_center($no_center));
                $sheet3->setCellValue('C' . $baris_3, $client_id);
                $sheet3->setCellValue('D' . $baris_3, $loan_no);
                $sheet3->setCellValue('E' . $baris_3, $nama_nasabah);
                $sheet3->setCellValue('F' . $baris_3, $jenis_produk);
                $sheet3->setCellValue('G' . $baris_3, $ke);
                $sheet3->setCellValue('H' . $baris_3, $rill);
                $sheet3->setCellValue('I' . $baris_3, $disburse);
                $sheet3->setCellValue('J' . $baris_3, $balance);
                $sheet3->setCellValue('K' . $baris_3, $angsuran);
                $sheet3->setCellValue('L' . $baris_3, $s_wajib);
                $sheet3->setCellValue('M' . $baris_3, $s_sukarela);
                $sheet3->setCellValue('N' . $baris_3, $s_pensiun);
                $sheet3->setCellValue('O' . $baris_3, $s_hariraya);
                $sheet3->setCellValue('P' . $baris_3, $total_simpanan);
                $sheet3->setCellValue('Q' . $baris_3, $pelunasan);
                $sheet3->setCellValue('R' . $baris_3, $hari);
                $sheet3->setCellValue('S' . $baris_3, $staff);
                $sheet3->setCellValue('T' . $baris_3, $ket);



                $baris_3++;
                $no3++;
            }

            $no++;
            $baris_baru++;

            $baris[] = $row;
        }
    }


    $akhir = count($baris) + 2;
    $sheet1->getStyle('A2:X' . $akhir)->applyFromArray($styleArray);
    $kolomJhinggaT = range('J', 'X');
    foreach ($kolomJhinggaT as $column) {
        $sheet1->getStyle($column . '3:' . $column . $akhir)->getNumberFormat()->setFormatCode('#,##0');
    }
    $richText = new \PhpOffice\PhpSpreadsheet\RichText\RichText();
    $textRun = $richText->createTextRun('total tidak dikurangi 2000');
    $richText->addText($textRun);




    $comment = $sheet1->getComment('Q2');
    $comment->setAuthor('RIKYDWIANTO');
    $comment->setText($richText);

    //komen sheet3
    $commentP2 = new \PhpOffice\PhpSpreadsheet\RichText\RichText();

    $commentP2->createTextRun('sudah dikurangi 2rb per simpanan');

    $sheet3->getComment('P2')->setText($commentP2);



    $sheet1->getStyle("A2:Z" . $akhir)->getFont()->setSize(8);

    $sheet1->getStyle('G2:G' . $akhir)->getNumberFormat()->setFormatCode('dd/mm/yyyy');


    foreach (range('A', 'S') as $col) {
        $sheet2->getColumnDimension($col)->setAutoSize(true);
    }
    $kolomJhinggaT = range('H', 'P');
    foreach ($kolomJhinggaT as $column) {
        $sheet2->getStyle($column . '3:' . $column . $akhir)->getNumberFormat()->setFormatCode('#,##0');
    }
    //sheet3
    $kolomJhinggaT = range('I', 'Q');
    foreach ($kolomJhinggaT as $column) {
        $sheet3->getStyle($column . '3:' . $column . $akhir)->getNumberFormat()->setFormatCode('#,##0');
    }
    $sheet2->getStyle('A2:S' . $akhir)->applyFromArray($styleArray);
    $sheet2->getStyle("A2:S" . $akhir)->getFont()->setSize(8);

    $sheet3->getStyle('A2:T' . $baris_3)->applyFromArray($styleArray);
    $sheet3->getStyle("A2:T" . $baris_3)->getFont()->setSize(8);



    //SHEET4 / INFORMASI PAR
    $total_rek = count($baris);
    $sheet4->setCellValue('A2', 'KETERANGAN');
    $sheet4->setCellValue('B2', ':');
    $sheet4->setCellValue('C2', 'ISI');
    $sheet4->setCellValue('D2', 'PERSEN');
    $sheet4->setCellValue('E2', 'GENERATE WEB');

    $sheet4->setCellValue('A3', 'TOTAL OUTSTANDING');
    $sheet4->setCellValue('B3', ':');
    $sheet4->setCellValue('C3', $os_total);

    $sheet4->setCellValue('A4', 'TOTAL OS PAR');
    $sheet4->setCellValue('B4', ':');
    $sheet4->setCellValue('C4', $total_os_par);
    $sheet4->setCellValue('D4', $persen_par);
    $sheet4->setCellValue('E4', $os_par);


    $sheet4->setCellValue('A5', 'BUKAN TOPUP');
    $sheet4->setCellValue('B5', ':');
    $sheet4->setCellValue('C5', $total_bukan_topup);
    $sheet4->setCellValue('D5', $total_bukan_topup / $total_rek);

    $sheet4->setCellValue('A6', 'TOPUP REGULER');
    $sheet4->setCellValue('B6', ':');
    $sheet4->setCellValue('C6', $total_topup);
    $sheet4->setCellValue('D6', $total_topup / $total_rek);

    $sheet4->setCellValue('A7', 'TOPUP KHUSUS');
    $sheet4->setCellValue('B7', ':');
    $sheet4->setCellValue('C7', $total_tpk);
    $sheet4->setCellValue('D7', $total_tpk / $total_rek);

    $sheet4->setCellValue('A8', 'TOTAL REKENING PAR');
    $sheet4->setCellValue('B8', ':');
    $sheet4->setCellValue('C8', $total_rek);


    $sheet4->getStyle('C2:C10')->getNumberFormat()->setFormatCode('#,##0');
    $sheet4->getStyle('E2:E10')->getNumberFormat()->setFormatCode('#,##0');
    $sheet4->getStyle('D2:D10')->getNumberFormat()->setFormatCode(\PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_PERCENTAGE_00);

    $sheet4->getStyle('A2:E10')->applyFromArray($styleArray);



    // PROSES SHEET 5
    // 
    $sql_delin = "SELECT * FROM deliquency WHERE kode_pemb in('PINJAMAN UMUM','PINJAMAN MIKROBISNIS') and tgl_input = '$tgl_delin' AND cabang = '$namaCabang' order by staff,sisa_saldo asc";
    // echo $sql_delin;
    $stmt = $pdo->query($sql_delin);
    $no = 1;
    $baris_5 = 3;
    foreach ($stmt->fetchAll() as $row) {

        $sisa_saldo = $row['sisa_saldo'];
        $satu_persen = $sisa_saldo * 0;
        // $tpk = ceil($sisa_saldo / 50000) * 50000;
        $tpk = $sisa_saldo + 10_000 ;
        // $tpk = $tpk * 10000;
        // echo angka($sisa_saldo) . ' - ' . $row['nasabah'] . ' - ' . angka($tpk) . '<br/>';
        $setengah  = ($row['minggu_rill'] / $row['priode']) * 100;
        if ($setengah >= 50) {
            $margin = 0.06;
            $ket_setengah = 'Lewat Setengah Jk. Waktu';
        } else {
            $margin = 0.12;
            $ket_setengah = 'Belum Lewat Setengah Jk. Waktu';
        }
        $dualima   = round((($tpk + ($tpk * $margin)) / 25) / 1000, PHP_ROUND_HALF_UP) * 1000;
        $limapuluh = round((($tpk + ($tpk * $margin)) / 50) / 1000, PHP_ROUND_HALF_UP) * 1000;
        $tujuhlima = round((($tpk + ($tpk * $margin)) / 75) / 1000, PHP_ROUND_HALF_UP) * 1000;
        $seratus   = round((($tpk + ($tpk * $margin)) / 100) / 1000, PHP_ROUND_HALF_UP) * 1000;
        $bodyData = [
            $no,
            "$row[loan]",
            "$row[no_center]",
            "$row[id_detail_nasabah]",
            "$row[nasabah]",
            "$row[kode_pemb]",
            "$row[priode]",
            "$row[minggu_rill]",
            "$row[amount]",
            "$sisa_saldo",
            "$tpk",
            "$dualima",
            "$limapuluh",
            "$tujuhlima",
            "$seratus",
            "$row[hari]",
            "$row[staff]",
            "$row[jenis_topup]",
            $ket_setengah
        ];
        $column = 'A';
        foreach ($bodyData as $header) {
            $sheet5->setCellValue($column . $baris_5, $header);
            $column++;
        }

        $no++;
        $baris_5++;
    }

    //STYLE SHEET 5
    foreach (range('A', 'S') as $col) {
        $sheet5->getColumnDimension($col)->setAutoSize(true);
    }

    $batas_sh5 = $baris_5;
    $sheet5->getStyle('G3:O' . $batas_sh5)->getNumberFormat()->setFormatCode('#,##0');
    $sheet5->getStyle("A2:Z" . $batas_sh5)->getFont()->setSize(8);
    $sheet5->getStyle('A2:S' . $batas_sh5)->applyFromArray($styleArray); //INI UNTUK BORDER


    try {
        $sql = "UPDATE log_cek_par 
        SET selesai = :selesai, keterangan = :keterangan, edited_at = NOW() 
        WHERE id = :id";

        // Menyiapkan statement PDO
        $stmt = $pdo->prepare($sql);

        // Mengatur nilai parameter
        $selesai = date("H:i:s"); // Mengambil waktu saat ini
        $keterangan = "selesai"; // Mengubah keterangan menjadi "selesai"

        // Binding parameter ke statement PDO
        $stmt->bindParam(':selesai', $selesai);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->bindParam(':id', $id_log);

        // Mengeksekusi statement PDO
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Simpan sebagai file Excel
    $writer = new Xlsx($spreadsheet);
    $folder = "FILE/";
    $filename = "ANALISA PAR $namaCabang $tgl_delin.xlsx";
    $writer->save($folder . $filename);

    // Auto-download dan kembali ke halaman analisis
    ?>
    <script>
        // Trigger download via iframe
        var iframe = document.createElement('iframe');
        iframe.style.display = 'none';
        iframe.src = '<?= $url ?>download.php?filename=<?= urlencode($filename) ?>&cleanup=delin_tgl_cabang&tgl_input=<?= urlencode($tgl_delin) ?>&cabang=<?= urlencode($namaCabang) ?>';
        document.body.appendChild(iframe);
        
        // Redirect setelah 2 detik
        setTimeout(function() {
            window.location.href = '<?= $url ?>index.php?menu=anal';
        }, 2000);
    </script>
    <?php
}




?>
