<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// echo "<h1>SEDANG PERBAIKAN</h1>";
// exit;
?>
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-chart-line me-3"></i>CEK PAR
                </h1>
                <p class="mb-0">Bandingkan dua file Excel PAR untuk melihat perubahan, kenaikan atau penurunan</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-12 col-lg-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Upload File Excel
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box mb-4">
                        <i class="fas fa-info-circle text-info"></i>
                        <div>
                            <strong>Petunjuk Upload:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload 2 file Excel format .xls atau .xlsx</li>
                                <li>File pertama: Data minggu/hari sebelumnya</li>
                                <li>File kedua: Data minggu/hari ini (pembanding)</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formCekPar">
                        <div class="mb-4">
                            <label for="formFile1" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-2"></i>File Sebelum (Minggu/Hari Kemarin)
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file' accept=".xls,.xlsx" id="formFile1">
                            <small class="text-muted">Format: .xls atau .xlsx</small>
                        </div>

                        <div class="mb-4">
                            <label for="formFile2" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-primary me-2"></i>File Pembanding (Minggu/Hari Ini)
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file1' accept=".xls,.xlsx" id="formFile2">
                            <small class="text-muted">Format: .xls atau .xlsx</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='preview' class="btn btn-primary btn-lg" onclick="return confirmAction('Apakah data yang diupload sudah benar?', function() { showLoading('Sedang memproses file Excel...'); })">
                                <i class="fas fa-check-circle me-2"></i>Proses Sekarang
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formCekPar');
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
                                        text: 'Silakan pilih kedua file yang dibutuhkan.'
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
                        <i class="fas fa-clock me-2"></i>Antrian Proses
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
                                $sql = "SELECT cabang, mulai, keterangan, created_at FROM log_cek_par WHERE keterangan='proses' ORDER BY created_at DESC";
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
if (isset($_POST['preview'])) {
    if (
        empty($_FILES['file']['name']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE ||
        empty($_FILES['file1']['name']) || $_FILES['file1']['error'] === UPLOAD_ERR_NO_FILE
    ) {
        alert("File harus terisi");
        pindah("index.php?menu=cek_par");
        return;
    }

    $upload_dir = "uploads/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }


    // error_reporting(0);
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
        pindah("index.php?menu=cek_par");
    }


    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin =  $tgl[0];

    $row_os_par = $last_row - 1;
    $row_os_total = $last_row - 2;
    $os_total = ganti_karakter($ws->getCell("E" . $row_os_total)->getValue());
    $os_par = ganti_karakter($ws->getCell("E" . $row_os_par)->getValue());
    $persen_par  = ($os_par / $os_total);
    $delete_sql = "DELETE FROM deliquency WHERE tgl_input = :tgl_input AND cabang = :cabang";
    $delete_stmt = $pdo->prepare($delete_sql);

    $insert_sql = "INSERT INTO deliquency (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session, jenis_topup)
        VALUES (:loan, :no_center, :id_detail_nasabah, :nasabah, :amount, :sisa_saldo, :tunggakan, :minggu, :tgl_input, :id_cabang, :tgl_disburse, :cabang, :wajib, :sukarela, :pensiun, :hariraya, :lainlain, :cicilan, :hari, :staff, :minggu_ke, :minggu_rill, :priode, :kode_pemb, :session, :jenis_topup)";
    $insert_stmt = $pdo->prepare($insert_sql);

    try {
        $pdo->beginTransaction();
        $delete_stmt->execute([
            ':tgl_input' => $tgl_delin,
            ':cabang' => $namaCabang,
        ]);

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
        echo "Error: " . $e->getMessage();
    }

    //FILE KE DUA

    $file = $_FILES['file1']['tmp_name'];
    $path = $file;
    $reader = IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s([^\s]+)/';
    $regex = '/Cabang\s+([A-Z\s]+?)As/';


    // Pencocokan regex
    preg_match($regex, $text, $matches);

    // Ambil hasil pencocokan (nama cabang)
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);




    $pattern = '/\b\d{4}-\d{2}-\d{2}\b/';
    preg_match($pattern, $text, $tgl);
    $tgl_delin1 =  $tgl[0];
    $tgl_delin1 = $tgl_delin1;

    $row_os_par = $last_row - 1;
    $row_os_total = $last_row - 2;
    $os_total = ganti_karakter($ws->getCell("E" . $row_os_total)->getValue());
    $os_par = ganti_karakter($ws->getCell("E" . $row_os_par)->getValue());
    $persen_par  = ($os_par / $os_total);

    try {
        $pdo->beginTransaction();
        $delete_stmt->execute([
            ':tgl_input' => $tgl_delin1,
            ':cabang' => $namaCabang,
        ]);

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
                $insert_stmt->execute([
                    ':loan' => $loan_no,
                    ':no_center' => $no_center,
                    ':id_detail_nasabah' => $client_id,
                    ':nasabah' => $nama_nasabah,
                    ':amount' => $disburse,
                    ':sisa_saldo' => $balance,
                    ':tunggakan' => $arreas,
                    ':minggu' => $wpd,
                    ':tgl_input' => $tgl_delin1,
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
        echo "Error: " . $e->getMessage();
    }

    try {

        $cek_center = "SELECT COUNT(DISTINCT staff) AS staff_count FROM deliquency WHERE tgl_input=:tgl_awal and cabang=:cabang and session=:sesi";
        $cek_center = $pdo->prepare($cek_center);
        $cek_center->bindParam("tgl_awal", $tgl_delin1);
        $cek_center->bindParam("cabang", $namaCabang);
        $cek_center->bindParam("sesi", $sesi);
        $cek_center->execute();
        $hitung_staff = (int)$cek_center->fetchColumn();
        if ($hitung_staff > 1) {
            //staff ada, tidak melakukan update
        } else {
            $center_awal = "SELECT DISTINCT no_center, staff FROM deliquency WHERE tgl_input=:tgl_awal and cabang=:cabang and session=:sesi";
            $center_awal = $pdo->prepare($center_awal);
            $center_awal->bindParam("tgl_awal", $tgl_delin);
            $center_awal->bindParam("cabang", $namaCabang);
            $center_awal->bindParam("sesi", $sesi);
            $center_awal->execute();
            $center_awal = $center_awal->fetchAll();
            foreach ($center_awal as $center_awal) {

                $center_no = $center_awal['no_center'];
                $nama_staff = $center_awal['staff'];
                $update_staff = "UPDATE deliquency set staff=:nama_staff WHERE no_center=:no_center and tgl_input=:tgl_akhir and cabang=:cabang and session=:sesi";
                $update_staff = $pdo->prepare($update_staff);
                $update_staff->bindParam("nama_staff", $nama_staff);
                $update_staff->bindParam("no_center", $center_no);
                $update_staff->bindParam("tgl_akhir", $tgl_delin1);
                $update_staff->bindParam("cabang", $namaCabang);
                $update_staff->bindParam("sesi", $sesi);
                $update_staff->execute();
                // if ($update_staff) {
                //     echo "Berhasil mengupdate nama staff";
                // } else {
                //     echo "gagal mengupdate nama staff";
                // }
            }
        }



        $sql = "UPDATE log_cek_par 
        SET priode_dari=:tgl_satu,priode_sampai=:tgl_dua, selesai = :selesai, keterangan = :keterangan, edited_at = NOW() 
        WHERE id = :id";

        // Menyiapkan statement PDO
        $stmt = $pdo->prepare($sql);

        // Mengatur nilai parameter
        $selesai = date("H:i:s"); // Mengambil waktu saat ini
        $keterangan = "selesai"; // Mengubah keterangan menjadi "selesai"

        // Binding parameter ke statement PDO
        $stmt->bindParam(':selesai', $selesai);
        $stmt->bindParam(':keterangan', $keterangan);
        $stmt->bindParam(':tgl_satu', $tgl_delin);
        $stmt->bindParam(':tgl_dua', $tgl_delin1);
        $stmt->bindParam(':id', $id_log);

        // Mengeksekusi statement PDO
        $stmt->execute();
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    alert("KEDUA FILE BERHASIL DIUPLOAD, TUNGGU PROSES SELANJUTNYA UNTUK ANALISIS KEDUA FILE . . .");
    pindah($url . "index.php?menu=proses_delin&cabang=$namaCabang&tgl_delin=" . $tgl_delin . "&tgl_delin1=" . $tgl_delin1);
}
