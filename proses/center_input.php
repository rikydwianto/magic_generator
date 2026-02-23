<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-calendar-alt me-3"></i>Input Center Meeting
                </h1>
                <p class="mb-0">Upload file XML jadwal pertemuan center untuk diproses</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-12 col-lg-12 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload me-2"></i>Upload File XML Center
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box mb-4">
                        <i class="fas fa-info-circle text-info"></i>
                        <div>
                            <strong>Petunjuk Upload:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload 1 file XML format .xml</li>
                                <li>File berisi jadwal center meeting</li>
                                <li>Data akan menggantikan jadwal lama</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formCenterInput">
                        <div class="mb-4">
                            <label for="formFile" class="form-label fw-bold">
                                <i class="fas fa-file-code text-warning me-2"></i>File Jadwal Center Meeting (XML)
                            </label>
                            <input class="form-control form-control-lg" type="file" name='file' accept=".xml" id="formFile">
                            <small class="text-muted">Format: .xml</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='xml-preview' class="btn btn-primary btn-lg" onclick="return confirmAction('Apakah data yang diupload sudah benar?', function() { showLoading('Sedang memproses jadwal center...'); })">
                                <i class="fas fa-check-circle me-2"></i>Proses Sekarang
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formCenterInput');
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
                                        text: 'Silakan pilih file XML center.'
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
        pindah("index.php?menu=center_meeting");
        return;
    }

    $file = $_FILES['file']['tmp_name'];
    $path = $file;
    $file_name = $_FILES['file']['name'] ?? null;
    $xml = simplexml_load_file($path);

    if ($xml === false) {
        try {
            $log_stmt = $pdo->prepare("INSERT INTO log_center_meeting (nama_cabang, id_cabang, file_name, total_center, total_hari, keterangan, mulai, selesai, pesan)
                VALUES (:nama_cabang, :id_cabang, :file_name, 0, 0, 'gagal', :mulai, :selesai, :pesan)");
            $waktu = date("H:i:s");
            $log_stmt->execute([
                ':nama_cabang' => 'UNKNOWN',
                ':id_cabang' => $sesi,
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
        $validate = $xml['Name'];
        if ($validate == "CenterMeeting") {
            //   echo ($xml[0]->Tablix2['HARI_Collection']);
            $xml = new SimpleXMLElement($path, 0, true);

            $raw = $xml;
            $executionTime = $raw->attributes()->ExecutionTime;
            $pecah = explode(",", $executionTime);
            $namaCabang = $pecah[0];

            if (cekCabangBlocked($pdo, $namaCabang, 'index.php?menu=center_meeting')) {
                return;
            }

            $log_id = null;
            try {
                $log_stmt = $pdo->prepare("INSERT INTO log_center_meeting (nama_cabang, id_cabang, file_name, total_center, total_hari, keterangan, mulai, pesan)
                    VALUES (:nama_cabang, :id_cabang, :file_name, 0, 0, 'proses', :mulai, NULL)");
                $mulai = date("H:i:s");
                $log_stmt->execute([
                    ':nama_cabang' => $namaCabang,
                    ':id_cabang' => $sesi,
                    ':file_name' => $file_name,
                    ':mulai' => $mulai,
                ]);
                $log_id = $pdo->lastInsertId();
            } catch (PDOException $e) {
                $log_id = null;
            }

            try {
                $hapus_center = "delete from center where nama_cabang='$namaCabang' or id_cabang='$sesi'";
                $pdo->query($hapus_center);

                $xml = ($xml->Tablix2->HARI_Collection);
                $hari = $xml->HARI;

                $total_center = 0;
                $hitung_hari = count($hari);

                foreach ($hari as $day) {
                    // Ambil nama hari dan ubah menjadi huruf kecil
                    $days = strtolower($day["HARI1"] ?? '');

                    foreach ($day->OfficerName_Collection as $hari_staff) {
                        foreach ($hari_staff->OfficerName as $staff) {
                            // Ambil nama staff dengan memisahkan string berdasarkan "Total"
                            $nama_staff = explode("Total ", $staff['OfficerName1'] ?? '')[0];


                            foreach ($staff->CenterID_Collection->CenterID  as $ctr_staf) {
                                foreach ($ctr_staf->CenterName as $center) {
                                    // Ambil ID Center
                                    $no_center = $ctr_staf['CenterID'] ?? '';
                                    $nama_center = $center['CenterName'] ?? '';

                                    // Ambil detail center
                                    $detail_center = $center->Details_Collection->Details ?? null;
                                    // var_dump($detail_center);
                                    // Ambil atribut dari detail
                                    $jam = rubahkata($detail_center['MeetingTime'] ?? '');
                                    $agt = $detail_center['Textbox128'] ?? '0';
                                    $client = $detail_center['JumlahClient'] ?? '0';
                                    $desa = aman(ganti_karakter(rubahkata($detail_center['DusunName'] ?? '')));
                                    $kecamatan = aman(ganti_karakter(rubahkata($detail_center['KecamatanName'] ?? '')));
                                    $kab = aman(ganti_karakter(rubahkata($detail_center['KabupatenName'] ?? '')));
                                    // Siapkan query SQL
                                    $qtxt = "
                            INSERT INTO `center` (
                                `id_center`,
                                `no_center`,
                                `doa_center`,
                                `hari`,
                                `status_center`,
                                `member_center`,
                                `anggota_center`,
                                `center_bayar`,
                                `id_cabang`,
                                `id_karyawan`,
                                `id_laporan`,
                                `jam_center`,
                                `latitude`,
                                `longitude`,
                                `doortodoor`,
                                `blacklist`,
                                `konfirmasi`,
                                `staff`,
                                `desa`,
                                `kecamatan`,
                                `kabupaten`,
                                `anggota_hadir`,
                                `nama_cabang`,
                                `nama_center`
                            ) VALUES (
                                NULL,
                                :no_center,
                                'y',
                                :days,
                                'hijau',
                                :agt,
                                :client,
                                :client_bayar,
                                :id_cabang,
                                '0',
                                '0',
                                :jam,
                                NULL,
                                NULL,
                                't',
                                't',
                                't',
                                :nama_staff,
                                :desa,
                                :kecamatan,
                                :kabupaten,
                                :anggota_hadir,
                                :nama_cabang,
                                :nama_center
                            );
                        ";

                                    // Debug query jika diperlukan
                                    // echo $qtxt . '<br>';

                                    // Eksekusi query menggunakan prepared statements
                                    $arr =  [
                                        ':no_center' => $no_center,
                                        ':days' => $days,
                                        ':agt' => $agt,
                                        ':client' => $client,
                                        ':client_bayar' => $client,
                                        ':id_cabang' => $sesi,
                                        ':jam' => $jam,
                                        ':nama_staff' => $nama_staff,
                                        ':desa' => $desa,
                                        ':kecamatan' => $kecamatan,
                                        ':kabupaten' => $kab,
                                        ':anggota_hadir' => $agt,
                                        ':nama_cabang' => $namaCabang,
                                        ':nama_center' => $nama_center
                                    ];
                                    $stmt = $pdo->prepare($qtxt);
                                    $stmt->execute($arr);
                                    // var_dump($arr);
                                    // Tambahkan ke total center
                                    $total_center++;
                                }
                            }
                        }
                    }
                }
                if ($log_id) {
                    try {
                        $log_update = $pdo->prepare("UPDATE log_center_meeting SET total_center = :total_center, total_hari = :total_hari, selesai = :selesai, keterangan = 'selesai' WHERE id = :id");
                        $log_update->execute([
                            ':total_center' => $total_center,
                            ':total_hari' => $hitung_hari,
                            ':selesai' => date("H:i:s"),
                            ':id' => $log_id,
                        ]);
                    } catch (PDOException $e) {
                        // ignore logging error
                    }
                }

                echo "Total center processed: $total_center";



                pindah("index.php?menu=center_proses&nama_cabang=$namaCabang");
            } catch (Exception $e) {
                $error_message = $e->getMessage();
                if ($log_id) {
                    try {
                        $log_update = $pdo->prepare("UPDATE log_center_meeting SET selesai = :selesai, keterangan = 'gagal', pesan = :pesan WHERE id = :id");
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
        } else {
            try {
                $log_stmt = $pdo->prepare("INSERT INTO log_center_meeting (nama_cabang, id_cabang, file_name, total_center, total_hari, keterangan, mulai, selesai, pesan)
                    VALUES (:nama_cabang, :id_cabang, :file_name, 0, 0, 'gagal', :mulai, :selesai, :pesan)");
                $waktu = date("H:i:s");
                $log_stmt->execute([
                    ':nama_cabang' => 'UNKNOWN',
                    ':id_cabang' => $sesi,
                    ':file_name' => $file_name,
                    ':mulai' => $waktu,
                    ':selesai' => $waktu,
                    ':pesan' => 'DITOLAK, BUKAN FILE CENTER MEETING XML',
                ]);
            } catch (PDOException $e) {
                // logging gagal, lanjutkan
            }
            alert("DITOLAK, BUKAN FILE CENTER MEETING XML");
        }
        //   echo var_dump($xml);
    }
}

?>
