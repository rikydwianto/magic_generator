<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Shared\Date;

function ambilInfoRegionalDariHeader($ws)
{
    $text = ganti_karakter($ws->getCell("C2")->getValue());

    preg_match('/Report\s+([A-Z\s]+?)As/', $text, $matches);
    $namaCabang = isset($matches[1]) ? trim(preg_replace('/As$/', '', $matches[1])) : '';

    preg_match('/\b\d{4}-\d{2}-\d{2}\b/', $text, $tgl);
    $tglDelin = isset($tgl[0]) ? $tgl[0] : '';

    return [$namaCabang, $tglDelin];
}

function simpanDataRegionalDariSheet($ws, $lastRow, $tglInput, $namaCabang, $pdo, $sesi)
{
    $sqlDelete = "DELETE FROM deliquency_regional WHERE tgl_input='$tglInput' AND regional='$namaCabang'";
    $pdo->query($sqlDelete);

    for ($row = 3; $row <= $lastRow; $row++) {
        $noCenter = (int) ganti_karakter($ws->getCell("D" . $row)->getValue());
        if ($noCenter <= 0) {
            continue;
        }

        $cabang      = ganti_karakter($ws->getCell("B" . $row)->getValue());
        $loanNo      = ganti_karakter($ws->getCell("C" . $row)->getValue());
        $clientId    = ganti_karakter1($ws->getCell("E" . $row)->getValue());
        $namaNasabah = str_replace("'", " ", ganti_karakter($ws->getCell("F" . $row)->getValue()));
        $jenisProduk = ganti_karakter($ws->getCell("H" . $row)->getValue());
        $disburse    = (float) ganti_karakter($ws->getCell("I" . $row)->getValue());
        $jangkaWaktu = (int)   ganti_karakter($ws->getCell("J" . $row)->getValue());
        $rawTglDisburse = $ws->getCell("K" . $row)->getValue();
        if (is_numeric($rawTglDisburse) && $rawTglDisburse > 0) {
            $tglDisburse = Date::excelToDateTimeObject((float) $rawTglDisburse)->format('Y-m-d');
        } else {
            $tglDisburse = ganti_karakter($rawTglDisburse);
        }
        $balance     = (float) ganti_karakter($ws->getCell("N" . $row)->getValue());
        $arreas      = (float) ganti_karakter($ws->getCell("O" . $row)->getValue());
        $weekPastDue = (int)   ganti_karakter($ws->getCell("P" . $row)->getValue());

        $simpWajib    = (float) ganti_karakter($ws->getCell("V"  . $row)->getValue());
        $simpSukarela = (float) ganti_karakter($ws->getCell("W"  . $row)->getValue());
        $simpPensiun  = (float) ganti_karakter($ws->getCell("X"  . $row)->getValue());
        $simpHariRaya = (float) ganti_karakter($ws->getCell("Y"  . $row)->getValue());
        $simpQurban   = (float) ganti_karakter($ws->getCell("AA" . $row)->getValue());
        $simpSipadan  = (float) ganti_karakter($ws->getCell("AB" . $row)->getValue());

        $cicilan   = (float) ganti_karakter($ws->getCell("AC" . $row)->getValue());
        $mingguKe  = (int)   ganti_karakter($ws->getCell("AD" . $row)->getValue());
        $mingguReal = (int)  ganti_karakter($ws->getCell("AE" . $row)->getValue());
        $minggon   = ganti_karakter($ws->getCell("AG" . $row)->getValue());
        $staff     = ganti_karakter1($ws->getCell("AH" . $row)->getValue());
        $jenisTopup = ganti_karakter($ws->getCell("AI" . $row)->getValue());

        // Kolom baru format regional (dibaca, belum disimpan karena kolom DB belum ada)
        $transPinjTerakhir = ganti_karakter($ws->getCell("AJ" . $row)->getValue());
        $transTabTerakhir  = ganti_karakter($ws->getCell("AK" . $row)->getValue());

        $sqlInsert = "INSERT INTO deliquency_regional (
            loan, no_center, id_detail_nasabah, nasabah, amount, sisa_saldo, tunggakan, minggu,
            tgl_input, id_cabang, tgl_disburse, cabang, wajib, sukarela, pensiun, hariraya,
            lainlain, cicilan, hari, staff, minggu_ke, minggu_rill, priode, kode_pemb, session,
            jenis_topup, regional
        ) VALUES (
            '$loanNo', '$noCenter', '$clientId', '$namaNasabah', $disburse, $balance, $arreas, $weekPastDue,
            '$tglInput', '', '$tglDisburse', '$cabang', $simpWajib, $simpSukarela, $simpPensiun, $simpHariRaya,
            0, $cicilan, '$minggon', '$staff', $mingguKe, $mingguReal, $jangkaWaktu, '$jenisProduk', '$sesi',
            '$jenisTopup', '$namaCabang'
        )";

        $pdo->query($sqlInsert);
    }
}
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-analisa">
                <h1 class="page-title">
                    <i class="fas fa-chart-bar me-3"></i>ANALISA DELIN REGIONAL
                </h1>
                <p class="mb-0">Single upload regional, tanpa pembanding manual (mirip anal_bayar)</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #81ecec 0%, #74b9ff 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-microscope me-2"></i>Upload File Regional untuk Analisa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box-success mb-4">
                        <i class="fas fa-info-circle text-success"></i>
                        <div>
                            <strong>Fitur Analisa:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload 1 file Excel Regional (.xls/.xlsx)</li>
                                <li>Kolom format regional: A sampai AK</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formAnalisaDelinReg">
                        <div class="mb-4">
                            <label for="formFileRegional" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-2"></i>Pilih File Excel Regional
                            </label>
                            <input class="form-control form-control-lg" type="file" name="file" accept=".xls,.xlsx" id="formFileRegional">
                            <small class="text-muted">Format: .xls atau .xlsx</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name="preview" class="btn btn-success btn-lg" onclick="return confirmAction('Mulai proses analisa regional?', function() { showLoading('Sedang menganalisa data regional...'); })">
                                <i class="fas fa-play-circle me-2"></i>Mulai Analisa
                            </button>
                        </div>
                    </form>
                    <script>
                        (function() {
                            var form = document.getElementById('formAnalisaDelinReg');
                            if (!form) return;
                            form.addEventListener('submit', function(e) {
                                var file = document.getElementById('formFileRegional');
                                if (!file) return;

                                if (!file.value) {
                                    e.preventDefault();
                                    if (typeof Swal !== 'undefined') {
                                        Swal.fire({
                                            icon: 'warning',
                                            title: 'File Harus Terisi',
                                            text: 'Silakan pilih file Excel regional.'
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

        <div class="col-12 col-lg-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-list-check me-2"></i>Antrian Analisa Regional
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
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
                                $sqlQueue = "SELECT * FROM log_cek_par WHERE keterangan='proses' ORDER BY created_at DESC";
                                $stmtQueue = $pdo->query($sqlQueue);
                                $no = 1;
                                if ($stmtQueue->rowCount() > 0) {
                                    while ($row = $stmtQueue->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= $row['cabang'] ?></strong></td>
                                            <td><?= $row['mulai'] ?></td>
                                            <td><span class="badge bg-info"><i class="fas fa-spinner fa-spin me-1"></i>Proses</span></td>
                                            <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                        </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='5' class='text-center text-muted'>Tidak ada antrian analisa regional</td></tr>";
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
ini_set('memory_limit', '-1');
ini_set('upload_max_filesize', '100M');
ini_set('post_max_size', '150M');
ini_set('max_execution_time', 0);

/**
 * ReadFilter: hanya muat kolom B-AK (kolom yang dipakai format regional) untuk hemat memory.
 */
class AnalisaRegReadFilter implements \PhpOffice\PhpSpreadsheet\Reader\IReadFilter
{
    public function readCell($columnAddress, $row, $worksheetName = '')
    {
        $cols = ['B','C','D','E','F','G','H','I','J','K',
                 'L','M','N','O','P','Q','R','S','T','U',
                 'V','W','X','Y','Z',
                 'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK'];
        return in_array($columnAddress, $cols);
    }
}

if (isset($_POST['preview'])) {
    if (empty($_FILES['file']['name']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        alert("File harus terisi");
        pindah("index.php?menu=analisa_delin_reg");
        return;
    }

    try {
        $workspaceRoot = dirname(__DIR__);
        $uploadDir = $workspaceRoot . DIRECTORY_SEPARATOR . 'FILE' . DIRECTORY_SEPARATOR . 'ANALISA_REG' . DIRECTORY_SEPARATOR;
        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0777, true)) {
            throw new Exception('Gagal membuat folder FILE/ANALISA_REG');
        }

        $originalName = basename($_FILES['file']['name']);
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, ['xls', 'xlsx'])) {
            throw new Exception('File harus berformat .xls atau .xlsx');
        }

        $safeName = preg_replace('/[^a-zA-Z0-9._-]/', '_', pathinfo($originalName, PATHINFO_FILENAME));
        $newFileName = date('Ymd_His') . '_' . substr(md5(uniqid('', true)), 0, 8) . '_' . $safeName . '.' . $extension;
        $fileRegional = $uploadDir . $newFileName;

        if (!move_uploaded_file($_FILES['file']['tmp_name'], $fileRegional)) {
            throw new Exception('Gagal memindahkan file ke folder FILE/ANALISA_REG');
        }

        $pdo->beginTransaction();

        $readerRegional = IOFactory::createReaderForFile($fileRegional);
        $readerRegional->setReadDataOnly(true);
        $readerRegional->setReadEmptyCells(false);
        $readerRegional->setReadFilter(new AnalisaRegReadFilter());
        $objRegional = $readerRegional->load($fileRegional);
        $wsRegional = $objRegional->getActiveSheet();
        $lastRowRegional = $wsRegional->getHighestDataRow();

        list($namaCabang, $tglAkhir) = ambilInfoRegionalDariHeader($wsRegional);

        if ($namaCabang == '' || $tglAkhir == '') {
            $isiC2 = ganti_karakter($wsRegional->getCell('C2')->getValue());
            throw new Exception('Format header file regional tidak valid. Isi C2: [' . $isiC2 . ']');
        }

        $sqlLog = "INSERT INTO log_cek_par (cabang, mulai, selesai, keterangan, created_at, edited_at)
                   VALUES (:cabang, :mulai, :selesai, :keterangan, :created_at, :edited_at)";
        $stmtLog = $pdo->prepare($sqlLog);
        $mulai = date("H:i:s");
        $selesai = "";
        $keterangan = "proses";
        $waktuSekarang = date("Y-m-d H:i:s");
        $stmtLog->bindParam(':cabang', $namaCabang);
        $stmtLog->bindParam(':mulai', $mulai);
        $stmtLog->bindParam(':selesai', $selesai);
        $stmtLog->bindParam(':keterangan', $keterangan);
        $stmtLog->bindParam(':created_at', $waktuSekarang);
        $stmtLog->bindParam(':edited_at', $waktuSekarang);
        $stmtLog->execute();
        $idLog = $pdo->lastInsertId();

        simpanDataRegionalDariSheet($wsRegional, $lastRowRegional, $tglAkhir, $namaCabang, $pdo, $sesi);

        $sqlUpdate = "UPDATE log_cek_par
                      SET selesai = :selesai, keterangan = :keterangan, edited_at = :edited_at
                      WHERE id = :id";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $selesaiNow = date("H:i:s");
        $keteranganDone = "selesai";
        $editedAt = date("Y-m-d H:i:s");
        $stmtUpdate->bindParam(':selesai', $selesaiNow);
        $stmtUpdate->bindParam(':keterangan', $keteranganDone);
        $stmtUpdate->bindParam(':edited_at', $editedAt);
        $stmtUpdate->bindParam(':id', $idLog);
        $stmtUpdate->execute();

        $pdo->commit();

        alert("FILE BERHASIL DIUPLOAD, PROSES ANALISA REGIONAL AKAN DILANJUTKAN . . .");
        pindah($url . "index.php?menu=proses_analisa_delin_reg&cabang=$namaCabang&tgl_delin1=" . $tglAkhir);
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $errMsg = addslashes($e->getMessage());
?>
        <script>
            Swal.fire({
                icon: 'error',
                title: 'Gagal Memproses',
                html: '<b>Error:</b> <?= $errMsg ?>',
                confirmButtonText: 'OK'
            }).then(function() {
                window.location.href = 'index.php?menu=analisa_delin_reg';
            });
        </script>
<?php
    }
}
?>