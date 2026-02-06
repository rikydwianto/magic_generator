<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-area me-3"></i>Cek Deliquency Regional
                </h1>
                <p class="mb-0">Bandingkan dua file Excel PAR Regional untuk melihat perubahan tingkat regional</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Upload File Excel Regional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box mb-4">
                        <i class="fas fa-info-circle text-info"></i>
                        <div>
                            <strong>Petunjuk Upload:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload 2 file Excel format .xls atau .xlsx</li>
                                <li>File pertama: Data minggu/hari sebelumnya (Regional)</li>
                                <li>File kedua: Data minggu/hari ini pembanding (Regional)</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formDelinReg">
                        <div class="mb-4">
                            <label for="formFile1" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-2"></i>File Sebelum (Minggu/Hari Kemarin) - Regional
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file' accept=".xls,.xlsx" id="formFile1">
                            <small class="text-muted">Format: .xls atau .xlsx</small>
                        </div>

                        <div class="mb-4">
                            <label for="formFile2" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-primary me-2"></i>File Pembanding (Minggu/Hari Ini) - Regional
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file1' accept=".xls,.xlsx" id="formFile2">
                            <small class="text-muted">Format: .xls atau .xlsx</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='preview' class="btn btn-primary btn-lg" onclick="return confirmAction('Apakah data yang diupload sudah benar?', function() { showLoading('Sedang memproses file Excel Regional...'); })">
                                <i class="fas fa-check-circle me-2"></i>Proses Sekarang
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formDelinReg');
                        if (!form) return;
                        form.addEventListener('submit', function(e) {
                            var file1 = document.getElementById('formFile1');
                            var file2 = document.getElementById('formFile2');
                            if (!file1 || !file2) return;
                            if (!file1.value || !file2.value) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'File harus terisi',
                                        text: 'Silakan pilih kedua file Excel regional.'
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
                <div class="card-header" style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-clock me-2"></i>Antrian Proses Regional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class='table table-hover'>
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="25%">Regional</th>
                                    <th width="25%">Waktu Mulai</th>
                                    <th width="20%">Status</th>
                                    <th width="25%">Dibuat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sql = "SELECT * FROM log_cek_par WHERE keterangan='proses' ORDER BY created_at DESC";
                                $stmt = $pdo->query($sql);

                                $no = 1;
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><strong><?= $row['cabang'] ?></strong></td>
                                        <td><?= $row['mulai'] ?></td>
                                        <td><span class="badge bg-warning"><i class="fas fa-spinner fa-spin me-1"></i><?= ucfirst($row['keterangan']) ?></span></td>
                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada antrian</td></tr>";
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
$pdo->beginTransaction();
ini_set('upload_max_filesize', '100M');

// Set ukuran maksimum data POST yang diterima (dalam bytes)
ini_set('post_max_size', '150M');

// Set waktu maksimum eksekusi skrip (dalam detik)
ini_set('max_execution_time', 500);

if (isset($_POST['preview'])) {
    if (
        empty($_FILES['file']['name']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE ||
        empty($_FILES['file1']['name']) || $_FILES['file1']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        alert("File harus terisi");
        pindah("index.php?menu=delin_reg");
        return;
    }

    error_reporting(0);
    $file = $_FILES['file']['tmp_name'];
    $path = $file;

    $reader = PHPExcel_IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $tgl_delin = "";
    $text = ganti_karakter($ws->getCell("C2")->getValue());

    $regex = '/Report\s([^\s]+)/';
    $regex = '/Report\s+([A-Z\s]+?)As/';


    // Pencocokan regex
    preg_match($regex, $text, $matches);


    // Ambil hasil pencocokan (nama cabang)
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);
    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin =  $tgl[0];

    if ($namaCabang != "") {

        try {
            $sql = "INSERT INTO log_cek_par (cabang, mulai, selesai, keterangan, created_at, edited_at)
                VALUES (:cabang, :mulai, :selesai, :keterangan, NOW(), NOW())";

            // Menyiapkan statement PDO
            $stmt = $pdo->prepare($sql);
            $mulai = date("H:i:s");
            $selesai = "";
            $keterangan = "proses";

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
        pindah("index.php?menu=delin_reg");
    }




    $row_os_par = $last_row - 1;
    $row_os_total = $last_row - 2;
    $os_total = ganti_karakter($ws->getCell("F" . $row_os_total)->getValue());
    $os_par = ganti_karakter($ws->getCell("F" . $row_os_par)->getValue());
    $persen_par  = ($os_par / $os_total);
    $sql_delete_awal = "delete from deliquency_regional where tgl_input='$tgl_delin' and regional='$namaCabang'";
    $pdo->query($sql_delete_awal);
    for ($row = 3; $row <= $last_row; $row++) {
        $no_center =  ganti_karakter($ws->getCell("D" . $row)->getValue()) + 0;
        if ($no_center > 0) {

            $cabang = ganti_karakter($ws->getCell("B" . $row)->getValue());
            $loan_no = ganti_karakter($ws->getCell("C" . $row)->getValue());
            $client_id = ganti_karakter1($ws->getCell("E" . $row)->getValue());
            $nama_nasabah = ganti_karakter($ws->getCell("F" . $row)->getValue());
            $jenis_produk = ganti_karakter($ws->getCell("H" . $row)->getValue());
            $disburse = ganti_karakter($ws->getCell("I" . $row)->getValue());
            $jk = ganti_karakter($ws->getCell("J" . $row)->getValue());
            $balance = ganti_karakter($ws->getCell("N" . $row)->getValue());
            $arreas = ganti_karakter($ws->getCell("O" . $row)->getValue());
            $wpd = ganti_karakter($ws->getCell("P" . $row)->getValue());
            $tgl_dis = ganti_karakter1($ws->getCell("K" . $row)->getValue());
            $tgl_dis = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($tgl_dis));
            $tanggal = date_create($tgl_dis)->format('m/d/Y');

            // SIMPANAN
            $s_wajib =  ganti_karakter($ws->getCell("V" . $row)->getValue());
            $s_sukarela =  ganti_karakter($ws->getCell("W" . $row)->getValue());
            $s_pensiun =  ganti_karakter($ws->getCell("X" . $row)->getValue());
            $s_hariraya =  ganti_karakter($ws->getCell("Y" . $row)->getValue());
            $s_khusus =  ganti_karakter($ws->getCell("Z" . $row)->getValue());
            $s_qurban =  ganti_karakter($ws->getCell("AA" . $row)->getValue());
            $s_sipadan =  ganti_karakter($ws->getCell("AB" . $row)->getValue());

            $angsuran =  ganti_karakter($ws->getCell("AC" . $row)->getValue());
            $rill =  ganti_karakter($ws->getCell("AE" . $row)->getValue());
            $ke =  ganti_karakter($ws->getCell("AD" . $row)->getValue());
            $tujuan =  ganti_karakter($ws->getCell("AF" . $row)->getValue());
            $hari =  ganti_karakter($ws->getCell("AG" . $row)->getValue());
            $staff =  ganti_karakter1($ws->getCell("AH" . $row)->getValue());
            $jenis_topup =  ganti_karakter($ws->getCell("AI" . $row)->getValue());

            $nama_nasabah = str_replace("'", " ", $nama_nasabah);
            $sql = "INSERT INTO deliquency_regional (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session,jenis_topup,regional) 
            VALUES ('$loan_no', '$no_center', '$client_id', '$nama_nasabah', $disburse, $balance, $arreas, $wpd, '$tgl_delin', '', '$tgl_dis', '$cabang', $s_wajib, $s_sukarela, $s_pensiun, $s_hariraya, 0, $angsuran, '$hari', '$staff', $ke, $rill, $jk, '$jenis_produk', '$sesi','$jenis_topup','$namaCabang')";


            $pdo->query($sql);
        }
    }

    //FILE KE DUA

    $file = $_FILES['file1']['tmp_name'];
    $path = $file;
    $reader = PHPExcel_IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $text = ganti_karakter($ws->getCell("C2")->getValue());

    $regex = '/Report\s([^\s]+)/';
    $regex = '/Report\s+([A-Z\s]+?)As/';


    // Pencocokan regex
    preg_match($regex, $text, $matches);


    // Ambil hasil pencocokan (nama cabang)
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);
    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin1 =  $tgl[0];

    $row_os_par = $last_row - 1;
    $row_os_total = $last_row - 2;
    $os_total = ganti_karakter($ws->getCell("E" . $row_os_total)->getValue());
    $os_par = ganti_karakter($ws->getCell("E" . $row_os_par)->getValue());
    $persen_par  = ($os_par / $os_total);

    $sql_delete_akhir = "delete from deliquency_regional where tgl_input='$tgl_delin1' and regional='$namaCabang'";
    $pdo->query($sql_delete_akhir);
    for ($row = 3; $row <= $last_row; $row++) {
        $no_center =  ganti_karakter($ws->getCell("D" . $row)->getValue()) + 0;
        if ($no_center > 0) {


            $cabang = ganti_karakter($ws->getCell("B" . $row)->getValue());
            $loan_no = ganti_karakter($ws->getCell("C" . $row)->getValue());
            $client_id = ganti_karakter1($ws->getCell("E" . $row)->getValue());
            $nama_nasabah = ganti_karakter($ws->getCell("F" . $row)->getValue());
            $jenis_produk = ganti_karakter($ws->getCell("H" . $row)->getValue());
            $disburse = ganti_karakter($ws->getCell("I" . $row)->getValue());
            $jk = ganti_karakter($ws->getCell("J" . $row)->getValue());
            $balance = ganti_karakter($ws->getCell("N" . $row)->getValue());
            $arreas = ganti_karakter($ws->getCell("O" . $row)->getValue());
            $wpd = ganti_karakter($ws->getCell("P" . $row)->getValue());
            $tgl_dis = ganti_karakter1($ws->getCell("K" . $row)->getValue());
            $tgl_dis = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($tgl_dis));
            $tanggal = date_create($tgl_dis)->format('m/d/Y');

            // SIMPANAN
            $s_wajib =  ganti_karakter($ws->getCell("V" . $row)->getValue());
            $s_sukarela =  ganti_karakter($ws->getCell("W" . $row)->getValue());
            $s_pensiun =  ganti_karakter($ws->getCell("X" . $row)->getValue());
            $s_hariraya =  ganti_karakter($ws->getCell("Y" . $row)->getValue());
            $s_khusus =  ganti_karakter($ws->getCell("Z" . $row)->getValue());
            $s_qurban =  ganti_karakter($ws->getCell("AA" . $row)->getValue());
            $s_sipadan =  ganti_karakter($ws->getCell("AB" . $row)->getValue());

            $angsuran =  ganti_karakter($ws->getCell("AC" . $row)->getValue());
            $rill =  ganti_karakter($ws->getCell("AE" . $row)->getValue());
            $ke =  ganti_karakter($ws->getCell("AD" . $row)->getValue());
            $tujuan =  ganti_karakter($ws->getCell("AF" . $row)->getValue());
            $hari =  ganti_karakter($ws->getCell("AG" . $row)->getValue());
            $staff =  ganti_karakter1($ws->getCell("AH" . $row)->getValue());
            $jenis_topup =  ganti_karakter($ws->getCell("AI" . $row)->getValue());

            $nama_nasabah = str_replace("'", " ", $nama_nasabah);
            $sql = "INSERT INTO deliquency_regional (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session,jenis_topup,regional) 
            VALUES ('$loan_no', '$no_center', '$client_id', '$nama_nasabah', $disburse, $balance, $arreas, $wpd, '$tgl_delin1', '', '$tgl_dis', '$cabang', $s_wajib, $s_sukarela, $s_pensiun, $s_hariraya, 0, $angsuran, '$hari', '$staff', $ke, $rill, $jk, '$jenis_produk', '$sesi','$jenis_topup','$namaCabang')";

            $query_ke2 = $pdo->query($sql);
        }
    }

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

    $pdo->commit();
    alert("KEDUA FILE BERHASIL DIUPLOAD, TUNGGU PROSES SELANJUTNYA UNTUK ANALISIS KEDUA FILE . . .");
    pindah($url . "index.php?menu=proses_delin_reg&cabang=$namaCabang&tgl_delin=" . $tgl_delin . "&tgl_delin1=" . $tgl_delin1);
}
