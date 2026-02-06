<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

// echo "<h1>SEDANG PERBAIKAN</h1>";
// exit;
?>
<div class="container-fluid">
    <h1>CEK PAR</h1>
    <div class="row">

        <div class="col-6">

            <h3>PENGECEKAN PAR NAIK ATAU TURUN DAN LAINNYA</h3>
            <form method="post" enctype="multipart/form-data">
                <label for="formFile" class="form-label">SILAHKAN PILIH FILE SEBELUM(MINGGU/HARI KEMARIN) <br></label>
                <input class="form-control" required type="file" name='file' accept=".xls,.xlsx" id="formFile">
                <br />
                <br />
                <label for="formFile" class="form-label">SILAHKAN PILIH FILE PEMBANDING(MINGGU/HARI INI) <br></label>
                <input class="form-control" required type="file" name='file1' accept=".xls,.xlsx" id="formFile">


                <br>
                <input type="submit" onclick="return confirm('yakin sudah benar?')" value="KONFIRMASI"
                    class='btn btn-danger' name='preview'>

            </form>

        </div>
        <div class="col-6">
            <h3>ANTRIAN</h3>
            <table border='1' class='table table-bordered table-hovered'>
                <tr>
                    <th>NO</th>
                    <th>Cabang</th>
                    <th>Mulai</th>
                    <th>Keterangan</th>
                    <th>Dibuat Pada</th>
                </tr>

                <?php
                $sql = "SELECT * FROM log_cek_par where keterangan='proses'";
                $stmt = $pdo->query($sql);

                $no = 1;
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $row['cabang'] ?></td>
                        <td><?= $row['mulai'] ?></td>
                        <td><?= $row['keterangan'] ?></td>
                        <td><?= $row['created_at'] ?></td>
                    </tr>

                <?php
                }


                ?>
            </table>
        </div>
    </div>
</div>

<?php
if (isset($_POST['preview'])) {
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
    $sql_delete_awal = "delete from deliquency where tgl_input='$tgl_delin' and cabang='$namaCabang'";
    $pdo->query($sql_delete_awal);
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
                //    echo " $nama_nasabah : Error: ".$pdo->error;
            }
        }
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

    $sql_delete_akhir = "delete from deliquency where tgl_input='$tgl_delin1' and cabang='$namaCabang'";
    $pdo->query($sql_delete_akhir);

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
            $sql = "INSERT INTO deliquency (loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu, tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya, lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session,jenis_topup) VALUES ('$loan_no', '$no_center', '$client_id', '$nama_nasabah', $disburse, $balance, $arreas, $wpd, '$tgl_delin1', '', '$tgl_dis', '$namaCabang', $s_wajib, $s_sukarela, $s_pensiun, $s_hariraya, 0, $angsuran, '$hari', '$staff', $ke, $rill, $jk, '$jenis_produk', '$sesi','$jenis_topup')";

            if ($pdo->query($sql) == TRUE) {
                //  echo "Data berhasil dimasukkan ke tabel deliquency.";
            } else {
                // echo "$nama_nasabah :  Error: ".$pdo->error;
            }
        }
    }

    try {

        $cek_center = "SELECT DISTINCT staff FROM deliquency WHERE tgl_input=:tgl_awal and cabang=:cabang and session=:sesi";
        $cek_center = $pdo->prepare($cek_center);
        $cek_center->bindParam("tgl_awal", $tgl_delin1);
        $cek_center->bindParam("cabang", $namaCabang);
        $cek_center->bindParam("sesi", $sesi);
        $cek_center->execute();
        $hitung_staff = $cek_center->rowCount();
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
