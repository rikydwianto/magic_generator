<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-file-import me-3"></i>Permintaan Disburse
                </h1>
                <p class="mb-0">Upload file XML permintaan pencairan untuk diproses</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-12 col-lg-12 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Upload File XML
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box mb-4">
                        <i class="fas fa-info-circle text-info"></i>
                        <div>
                            <strong>Petunjuk Upload:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload 1 file XML format .xml</li>
                                <li>File berisi data permintaan pencairan</li>
                                <li>Pastikan format file sesuai standar</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formPermintaan">
                        <div class="mb-4">
                            <label for="formFile" class="form-label fw-bold">
                                <i class="fas fa-file-code text-danger me-2"></i>File Permintaan Disburse (XML)
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file' accept=".xml" id="formFile">
                            <small class="text-muted">Format: .xml</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='xml-preview' class="btn btn-primary btn-lg" onclick="return confirmAction('Apakah data yang diupload sudah benar?', function() { showLoading('Sedang memproses file XML...'); })">
                                <i class="fas fa-check-circle me-2"></i>Proses Sekarang
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formPermintaan');
                        if (!form) return;
                        form.addEventListener('submit', function(e) {
                            var file = document.getElementById('formFile');
                            if (!file) return;
                            if (!file.value) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'File harus terisi',
                                        text: 'Silakan pilih file XML permintaan disburse.'
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
    </div>
</div>

<?php
if (isset($_POST['xml-preview'])) {
    libxml_use_internal_errors(true);

    if (empty($_FILES['file']['name']) || $_FILES['file']['error'] === UPLOAD_ERR_NO_FILE) {
        alert("File harus terisi");
        pindah("index.php?menu=permintaandisburse");
        return;
    }

    // Upload XML file
    $file = $_FILES['file']['tmp_name'];
    $file_name = $_FILES['file']['name'] ?? null;
    $xml = simplexml_load_file($file);

    if ($xml === false) {
        try {
            $log_stmt = $pdo->prepare("INSERT INTO log_permintaan_disburse (nama_cabang, file_name, total_row, keterangan, mulai, selesai, pesan)
                VALUES (:nama_cabang, :file_name, 0, 'gagal', :mulai, :selesai, :pesan)");
            $waktu = date("H:i:s");
            $log_stmt->execute([
                ':nama_cabang' => 'UNKNOWN',
                ':file_name' => $file_name,
                ':mulai' => $waktu,
                ':selesai' => $waktu,
                ':pesan' => 'Failed to load XML.',
            ]);
        } catch (PDOException $e) {
            // logging gagal, lanjutkan
        }
        echo "Failed to load XML.";
    } else {
        $log_id = null;
        $total_row = 0;
        try {
            // Ambil nama cabang dan tanggal dari atribut Report
            $reportAttribs = $xml->attributes();
            $textbox101 = (string)$reportAttribs['Textbox101'];
            $_Textbox102 = (string)$reportAttribs['Textbox102'];
            preg_match_all('/\d{4}-\d{2}-\d{2}/', $_Textbox102, $matches);

            if (!empty($matches[0])) {
                $tanggal_awal  = $matches[0][0] ?? date('Y-m-d'); // 2025-08-22
                $tanggal_akhir = isset($matches[0][1]) ? $matches[0][1] : $tanggal_awal; // 2025-08-29
            }


            // echo $_Textbox102;
            // exit;
            $textboxParts = explode(" ", $textbox101);

            $namaCabang = $textboxParts[1] ?? 'Unknown'; // Cabang
            // $tanggal = date("Y-m-d", strtotime($textboxParts[3])); // Tanggal

            if (cekCabangBlocked($pdo, $namaCabang, 'index.php?menu=permintaandisburse')) {
                return;
            }

            try {
                $log_stmt = $pdo->prepare("INSERT INTO log_permintaan_disburse (nama_cabang, file_name, tanggal_awal, tanggal_akhir, total_row, keterangan, mulai, pesan)
                    VALUES (:nama_cabang, :file_name, :tanggal_awal, :tanggal_akhir, 0, 'proses', :mulai, NULL)");
                $mulai = date("H:i:s");
                $log_stmt->execute([
                    ':nama_cabang' => $namaCabang,
                    ':file_name' => $file_name,
                    ':tanggal_awal' => $tanggal_awal,
                    ':tanggal_akhir' => $tanggal_akhir,
                    ':mulai' => $mulai,
                ]);
                $log_id = $pdo->lastInsertId();
            } catch (PDOException $e) {
                $log_id = null;
            }

            // Hapus data lama untuk cabang dan tanggal yang sama
            $pdo->query("DELETE FROM permintaan_disburse WHERE nama_cabang = '$namaCabang' AND tanggal BETWEEN '$tanggal_awal' AND '$tanggal_akhir'");


            // Ambil elemen Details_Collection
            $detailsCollection = $xml->Tablix1->Details_Collection->Details ?? null;

            if ($detailsCollection) {
            foreach ($detailsCollection as $detail) {
                // Ambil data dari atribut <Details>
                $centerID = (string)$detail['CenterID'];
                $clientID = (string)$detail['ClientID'];
                $clientName = (string)$detail['ClientName'];
                $officerName = (string)$detail['OfficerName'];
                $productName = (string)$detail['ProductName'];
                $gpPokok = (float)$detail['GpPokok'];
                $gpNishbah = (float)$detail['GpNishbah'];
                $period = (int)$detail['Period'];
                $pinjKe = (int)$detail['Pinjke'];
                $jenisTopUp = (string)$detail['JenisTopUp'];
                $loanAmount = (float)$detail['LoanAmount'];
                $osPokokTopUP = (float)$detail['OsPokokTopUP'];
                $netDisburse = (float)$detail['NetDisburse'];
                $DisbDate = $detail['DisbDate'];

                // Simpan data ke database
                $stmt = $pdo->prepare("INSERT INTO permintaan_disburse
                    (nama_cabang, tanggal, center_id, client_id, client_name, officer_name, product_name, gp_pokok, gp_nishbah, period, pinj_ke, jenis_top_up, loan_amount, os_pokok_top_up, net_disburse) 
                    VALUES (:nama_cabang, :tanggal, :center_id, :client_id, :client_name, :officer_name, :product_name, :gp_pokok, :gp_nishbah, :period, :pinj_ke, :jenis_top_up, :loan_amount, :os_pokok_top_up, :net_disburse)");

                $stmt->bindParam(':nama_cabang', $namaCabang);
                $stmt->bindParam(':tanggal', $DisbDate);
                $stmt->bindParam(':center_id', $centerID);
                $stmt->bindParam(':client_id', $clientID);
                $stmt->bindParam(':client_name', $clientName);
                $stmt->bindParam(':officer_name', $officerName);
                $stmt->bindParam(':product_name', $productName);
                $stmt->bindParam(':gp_pokok', $gpPokok);
                $stmt->bindParam(':gp_nishbah', $gpNishbah);
                $stmt->bindParam(':period', $period);
                $stmt->bindParam(':pinj_ke', $pinjKe);
                $stmt->bindParam(':jenis_top_up', $jenisTopUp);
                $stmt->bindParam(':loan_amount', $loanAmount);
                $stmt->bindParam(':os_pokok_top_up', $osPokokTopUP);
                $stmt->bindParam(':net_disburse', $netDisburse);

                if (!$stmt->execute()) {
                    echo "Error: " . implode(", ", $stmt->errorInfo());
                } else {
                    $total_row++;
                }
            }

            if ($log_id) {
                try {
                    $log_update = $pdo->prepare("UPDATE log_permintaan_disburse SET total_row = :total_row, selesai = :selesai, keterangan = 'selesai' WHERE id = :id");
                    $log_update->execute([
                        ':total_row' => $total_row,
                        ':selesai' => date("H:i:s"),
                        ':id' => $log_id,
                    ]);
                } catch (PDOException $e) {
                    // ignore logging error
                }
            }
            } // End of if ($detailsCollection)

            // tambah 1 hari tanggal awal
            $tanggal_awal = date("Y-m-d", strtotime($tanggal_awal . " +1 day"));
            echo "Data berhasil disimpan.";
            pindah("index.php?menu=permintaan_disburse_print&nama_cabang=$namaCabang&tanggal=$tanggal_awal&tanggal_akhir=$tanggal_akhir");
        } catch (Exception $e) {
            $error_message = $e->getMessage();
            if ($log_id) {
                try {
                    $log_update = $pdo->prepare("UPDATE log_permintaan_disburse SET selesai = :selesai, keterangan = 'gagal', pesan = :pesan WHERE id = :id");
                    $log_update->execute([
                        ':selesai' => date("H:i:s"),
                        ':pesan' => $error_message,
                        ':id' => $log_id,
                    ]);
                } catch (PDOException $logErr) {
                    // ignore logging error
                }
            }
            echo "Error: " . $error_message;
        }
    }
}


?>
