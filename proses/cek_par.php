<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

echo "<h1>SEDANG PERBAIKAN</h1>";
exit;
?>
<h1>CEK PAR</h1>
<h3>PENGECEKAN PAR NAIK ATAU TURUN DAN LAINNYA</h3>
<form method="post" enctype="multipart/form-data">
    <div class="col-md-4">
        <label for="formFile" class="form-label">SILAHKAN PILIH FILE SEBELUM(MINGGU/HARI KEMARIN) <br></label>
        <input class="form-control" required type="file" name='file' accept=".xls,.xlsx" id="formFile">
        <br />
        <br />
        <br />
        <br />
        <label for="formFile" class="form-label">SILAHKAN PILIH FILE PEMBANDING(MINGGU/HARI INI) <br></label>
        <input class="form-control" required type="file" name='file1' accept=".xls,.xlsx" id="formFile">



        <input type="submit" onclick="return confirm('yakin sudah benar?')" value="KONFIRMASI" class='btn btn-danger'
            name='preview'>
    </div>
</form>

<?php
if (isset($_POST['preview'])) {

    error_reporting(0);
    $file = $_FILES['file']['tmp_name'];
    $path = $file;
    $tgl = $_POST['tgl'];
    $reader = PHPExcel_IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $tgl_delin = "";
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s([^\s]+)/';

    // Pencocokan regex
    preg_match($regex, $text, $matches);

    // Ambil hasil pencocokan (nama cabang)
    $namaCabang = isset($matches[1]) ? $matches[1] : '';
    $namaCabang = preg_replace('/As$/', '', $namaCabang);



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
        $no_center =  ganti_karakter($ws->getCell("C" . $row)->getValue()) + 0;
        if ($no_center > 0) {

            $loan_no = ganti_karakter($ws->getCell("B" . $row)->getValue());
            $client_id = ganti_karakter1($ws->getCell("D" . $row)->getValue());
            $nama_nasabah = ganti_karakter($ws->getCell("E" . $row)->getValue());
            $jenis_produk = ganti_karakter($ws->getCell("G" . $row)->getValue());
            $disburse = ganti_karakter($ws->getCell("H" . $row)->getValue());
            $jk = ganti_karakter($ws->getCell("I" . $row)->getValue());
            $balance = ganti_karakter($ws->getCell("M" . $row)->getValue());
            $arreas = ganti_karakter($ws->getCell("N" . $row)->getValue());
            $wpd = ganti_karakter($ws->getCell("O" . $row)->getValue());
            $tgl_dis = ganti_karakter1($ws->getCell("J" . $row)->getValue());
            $tgl_dis = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($tgl_dis));
            $tanggal = date_create($tgl_dis)->format('m/d/Y');

            // SIMPANAN
            $s_wajib =  ganti_karakter($ws->getCell("U" . $row)->getValue());
            $s_sukarela =  ganti_karakter($ws->getCell("V" . $row)->getValue());
            $s_pensiun =  ganti_karakter($ws->getCell("W" . $row)->getValue());
            $s_hariraya =  ganti_karakter($ws->getCell("X" . $row)->getValue());
            $s_khusus =  ganti_karakter($ws->getCell("Y" . $row)->getValue());
            $s_qurban =  ganti_karakter($ws->getCell("Z" . $row)->getValue());
            $s_sipadan =  ganti_karakter($ws->getCell("AA" . $row)->getValue());

            $angsuran =  ganti_karakter($ws->getCell("AB" . $row)->getValue());
            $rill =  ganti_karakter($ws->getCell("AD" . $row)->getValue());
            $ke =  ganti_karakter($ws->getCell("AC" . $row)->getValue());
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
    $tgl = $_POST['tgl'];
    $reader = PHPExcel_IOFactory::createReaderForFile($path);
    $objek = $reader->load($path);
    $ws = $objek->getActiveSheet();
    $last_row = $ws->getHighestDataRow();
    $text = ganti_karakter($ws->getCell("D2")->getValue());

    $regex = '/Cabang\s([^\s]+)/';

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
        $no_center =  ganti_karakter($ws->getCell("C" . $row)->getValue()) + 0;
        if ($no_center > 0) {

            $loan_no = ganti_karakter($ws->getCell("B" . $row)->getValue());
            $client_id = ganti_karakter1($ws->getCell("D" . $row)->getValue());
            $nama_nasabah = ganti_karakter($ws->getCell("E" . $row)->getValue());
            $jenis_produk = ganti_karakter($ws->getCell("G" . $row)->getValue());
            $disburse = ganti_karakter($ws->getCell("H" . $row)->getValue());
            $jk = ganti_karakter($ws->getCell("I" . $row)->getValue());
            $balance = ganti_karakter($ws->getCell("M" . $row)->getValue());
            $arreas = ganti_karakter($ws->getCell("N" . $row)->getValue());
            $wpd = ganti_karakter($ws->getCell("O" . $row)->getValue());
            $tgl_dis = ganti_karakter1($ws->getCell("J" . $row)->getValue());
            $tgl_dis = date("Y-m-d", PHPExcel_Shared_Date::ExcelToPHP($tgl_dis));
            $tanggal = date_create($tgl_dis)->format('m/d/Y');

            // SIMPANAN
            $s_wajib =  ganti_karakter($ws->getCell("U" . $row)->getValue());
            $s_sukarela =  ganti_karakter($ws->getCell("V" . $row)->getValue());
            $s_pensiun =  ganti_karakter($ws->getCell("W" . $row)->getValue());
            $s_hariraya =  ganti_karakter($ws->getCell("X" . $row)->getValue());
            $s_khusus =  ganti_karakter($ws->getCell("Y" . $row)->getValue());
            $s_qurban =  ganti_karakter($ws->getCell("Z" . $row)->getValue());
            $s_sipadan =  ganti_karakter($ws->getCell("AA" . $row)->getValue());

            $angsuran =  ganti_karakter($ws->getCell("AB" . $row)->getValue());
            $rill =  ganti_karakter($ws->getCell("AD" . $row)->getValue());
            $ke =  ganti_karakter($ws->getCell("AC" . $row)->getValue());
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


    alert("KEDUA FILE BERHASIL DIUPLOAD, TUNGGU PROSES SELANJUTNYA UNTUK ANALISIS KEDUA FILE . . .");
    pindah($url . "index.php?menu=proses_delin&cabang=$namaCabang&tgl_delin=" . $tgl_delin . "&tgl_delin1=" . $tgl_delin1);
}