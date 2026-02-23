<?php
// error_reporting(0);

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
                    <i class="fas fa-money-bill-wave me-3"></i>ANALISA PAR BAYAR
                </h1>
                <p class="mb-0">Upload banyak file Excel PAR untuk dianalisa sekaligus</p>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Upload Form Card -->
        <div class="col-md-6 mb-4">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #81ecec 0%, #74b9ff 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-microscope me-2"></i>Upload Multi File untuk Analisa
                    </h5>
                </div>
                <div class="card-body">
                    <div class="upload-info-box-success mb-4">
                        <i class="fas fa-info-circle text-success"></i>
                        <div>
                            <strong>Fitur Analisa:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Upload banyak file Excel (.xls/.xlsx)</li>
                                <li>Akan membandingkan setiap file nya lalu akan di cek apakah bayar atau tidak</li>
                                <li>Mengecek apakah ada pemasukan sukarela apa tidak</li>
                            </ul>
                        </div>
                    </div>

                    <form method="post" enctype="multipart/form-data" id="formAnalisaBayar">
                        <div class="mb-4">
                            <label for="formFileAnalisaMulti" class="form-label fw-bold">
                                <i class="fas fa-file-excel text-success me-2"></i>Pilih Banyak File Excel PAR
                            </label>
                            <input class="form-control form-control-lg" type="file" name='files[]' accept=".xls,.xlsx" id="formFileAnalisaMulti" multiple>
                            <small class="text-muted">Format: .xls atau .xlsx (minimal 2 file untuk perbandingan)</small>
                        </div>

                        <div class="d-grid">
                            <button type="submit" name='preview_multi' class="btn btn-success btn-lg" onclick="return confirmAction('Mulai proses analisa multi file ini?', function() { showLoading('Sedang menganalisa data...'); })">
                                <i class="fas fa-play-circle me-2"></i>Mulai Analisa
                            </button>
                        </div>
                    </form>
                    <script>
                    (function() {
                        var form = document.getElementById('formAnalisaBayar');
                        if (!form) return;
                        form.addEventListener('submit', function(e) {
                            var file = document.getElementById('formFileAnalisaMulti');
                            if (!file) return;
                            
                            // Validasi file kosong
                            if (!file.files || file.files.length === 0) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'warning',
                                        title: 'File Harus Terisi',
                                        text: 'Silakan pilih minimal 2 file Excel untuk perbandingan.'
                                    });
                                } else {
                                    alert('File harus terisi');
                                }
                                return false;
                            }
                            
                            // Validasi minimal 2 file untuk perbandingan
                            if (file.files.length < 2) {
                                e.preventDefault();
                                if (typeof Swal !== 'undefined') {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'File Tidak Mencukupi',
                                        text: 'Minimal 2 file diperlukan untuk melakukan perbandingan. Anda hanya memilih ' + file.files.length + ' file.',
                                        confirmButtonText: 'OK'
                                    });
                                } else {
                                    alert('Minimal 2 file diperlukan untuk perbandingan!');
                                }
                                return false;
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
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            <i class="fas fa-list-check me-2"></i>Antrian Analisa
                        </h5>
                        <button type="button" class="btn btn-sm btn-light" onclick="window.location.reload();">
                            <i class="fas fa-sync-alt me-1"></i>Refresh
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class='table table-hover'>
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th width="20%">Cabang</th>
                                    <th width="10%">Total File</th>
                                    <th width="15%">Status</th>
                                    <th width="15%">Dibuat</th>
                                    <th width="10%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody id="queueBody">
                                <?php
                              
                                $sql = "SELECT session, cabang, total_files, status, message, created_at, updated_at, output_file
                                        FROM exec_analisa_par_multi
                                        WHERE session ='$sesi'
                                        ORDER BY updated_at DESC";
                                $stmt = $pdo->query($sql);

                                $no = 1;
                                if ($stmt->rowCount() > 0) {
                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                    <tr>
                                        <td><?= $no++ ?></td>
                                        <td><?= $row['cabang'] ?? '-' ?></td>
                                        <td><?= $row['total_files'] ?></td>
                                        <td>
                                            <?php
                                            $status = $row['status'] ?? 'uploaded';
                                            $badge = 'secondary';
                                            $label = 'Uploaded';
                                            if ($status === 'processing') {
                                                $badge = 'info';
                                                $label = 'Processing';
                                            } elseif ($status === 'done') {
                                                $badge = 'success';
                                                $label = 'Done';
                                            } elseif ($status === 'failed') {
                                                $badge = 'danger';
                                                $label = 'Failed';
                                            } elseif ($status === 'proses_analisa') {
                                                $badge = 'warning';
                                                $label = 'Proses Analisa';
                                            } elseif ($status === 'selesai') {
                                                $badge = 'success';
                                                $label = 'Selesai';
                                            } elseif ($status === '') {
                                                $badge = 'secondary';
                                                $label = '-';
                                            }
                                            ?>
                                            <span class="badge bg-<?= $badge ?>"><?= $label ?></span>
                                        </td>
                                        <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                        <td>
                                            <?php if (($row['status'] ?? 'uploaded') === 'uploaded'): ?>
                                                <button type="button" class="btn btn-sm btn-primary" onclick="confirmProsesInsert('<?= htmlspecialchars($row['session'], ENT_QUOTES) ?>')">Proses</button>
                                            <?php elseif (($row['status'] ?? '') === 'done'): ?>
                                                <button type="button" class="btn btn-sm btn-warning" onclick="confirmProsesAnalisa('<?= htmlspecialchars($row['session'], ENT_QUOTES) ?>')">Proses Analisa</button>
                                            <?php elseif (($row['status'] ?? '') === 'selesai' && !empty($row['output_file'])): ?>
                                                <a class="btn btn-sm btn-success" href="<?= $url ?>download.php?filename=<?= urlencode($row['output_file']) ?>">Download</a>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted'>Tidak ada antrian analisa</td></tr>";
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

<script>
function confirmProsesInsert(session) {
    if (typeof Swal === 'undefined') {
        if (confirm('Yakin akan proses?')) {
            window.location.href = '<?= $url ?>proses/exec/proses_exec_analisa_par.php?session=' + encodeURIComponent(session);
        }
        return;
    }
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin akan proses insert?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= $url ?>proses/exec/proses_exec_analisa_par.php?session=' + encodeURIComponent(session);
        }
    });
}

function confirmProsesAnalisa(session) {
    if (typeof Swal === 'undefined') {
        if (confirm('Yakin akan proses analisa?')) {
            window.location.href = '<?= $url ?>proses/exec/proses_exec_analisa_par_analisa.php?session=' + encodeURIComponent(session);
        }
        return;
    }
    Swal.fire({
        title: 'Konfirmasi',
        text: 'Yakin akan proses analisa?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Proses',
        cancelButtonText: 'Batal',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= $url ?>proses/exec/proses_exec_analisa_par_analisa.php?session=' + encodeURIComponent(session);
        }
    });
}
</script>

<script>
(function() {
    var queueUrl = <?= json_encode($url . 'api/anal_bayar_queue.php') ?>;
    var downloadBase = <?= json_encode($url . 'download.php?filename=') ?>;
    var refreshInterval = 10000;

    function escapeHtml(str) {
        return String(str ?? '')
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/\"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function statusMeta(status) {
        var badge = 'secondary';
        var label = 'Uploaded';
        if (status === 'processing') {
            badge = 'info'; label = 'Processing';
        } else if (status === 'done') {
            badge = 'success'; label = 'Done';
        } else if (status === 'failed') {
            badge = 'danger'; label = 'Failed';
        } else if (status === 'proses_analisa') {
            badge = 'warning'; label = 'Proses Analisa';
        } else if (status === 'selesai') {
            badge = 'success'; label = 'Selesai';
        } else if (status === '') {
            badge = 'secondary'; label = '-';
        }
        return { badge: badge, label: label };
    }

    function renderQueue(rows) {
        var tbody = document.getElementById('queueBody');
        if (!tbody) return;

        if (!rows || !rows.length) {
            tbody.innerHTML = "<tr><td colspan='7' class='text-center text-muted'>Tidak ada antrian analisa</td></tr>";
            return;
        }

        var html = '';
        for (var i = 0; i < rows.length; i++) {
            var row = rows[i] || {};
            var status = row.status ?? 'uploaded';
            var meta = statusMeta(status);
            var cabang = escapeHtml(row.cabang ?? '-');
            var total = row.total_files ?? 0;
            var created = escapeHtml(row.created_at_fmt ?? row.created_at ?? '');
            var session = escapeHtml(row.session ?? '');
            var aksi = "<span class='text-muted'>-</span>";

            if (status === 'uploaded') {
                aksi = "<button type='button' class='btn btn-sm btn-primary' data-session='" + session + "' onclick='confirmProsesInsert(this.dataset.session)'>Proses</button>";
            } else if (status === 'done') {
                aksi = "<button type='button' class='btn btn-sm btn-warning' data-session='" + session + "' onclick='confirmProsesAnalisa(this.dataset.session)'>Proses Analisa</button>";
            } else if (status === 'selesai' && row.output_file) {
                aksi = "<a class='btn btn-sm btn-success' href='" + downloadBase + encodeURIComponent(row.output_file) + "'>Download</a>";
            }

            html += "<tr>" +
                "<td>" + (i + 1) + "</td>" +
                "<td>" + cabang + "</td>" +
                "<td>" + total + "</td>" +
                "<td><span class='badge bg-" + meta.badge + "'>" + meta.label + "</span></td>" +
                "<td>" + created + "</td>" +
                "<td>" + aksi + "</td>" +
            "</tr>";
        }
        tbody.innerHTML = html;
    }

    function fetchQueue() {
        fetch(queueUrl + '?t=' + Date.now(), { cache: 'no-store' })
            .then(function(res) { return res.json(); })
            .then(function(data) {
                if (data && data.ok) {
                    renderQueue(data.data || []);
                }
            })
            .catch(function() {});
    }

    fetchQueue();
    setInterval(fetchQueue, refreshInterval);
})();
</script>

<?php
if (isset($_POST['preview_multi'])) {
    if (empty($_FILES['files']['name']) || empty($_FILES['files']['name'][0])) {
        alert("File harus terisi");
        pindah("index.php?menu=anal_bayar");
        return;
    }

    $sesi_upload = $_SESSION['sesi'] ?? '';
    if ($sesi_upload === '') {
        $sesi_upload = time() . '-' . bin2hex(random_bytes(8));
        $_SESSION['sesi'] = $sesi_upload;
    }

    $upload_dir = "FILE/" . $sesi_upload . "/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $success = 0;
    $errors = [];
    $count = count($_FILES['files']['name']);
    $saved_files = [];

    for ($i = 0; $i < $count; $i++) {
        if ($_FILES['files']['error'][$i] !== UPLOAD_ERR_OK) {
            $errors[] = ($_FILES['files']['name'][$i] ?? 'File') . " gagal diupload";
            continue;
        }

        $original = basename($_FILES['files']['name'][$i]);
        $ext = strtolower(pathinfo($original, PATHINFO_EXTENSION));
        if (!in_array($ext, ['xls', 'xlsx'])) {
            $errors[] = $original . " bukan file Excel";
            continue;
        }

        $safe_name = preg_replace('/[^a-zA-Z0-9._-]/', '_', $original);
        $new_name = $safe_name;
        $target = $upload_dir . $new_name;

        if (file_exists($target) && !unlink($target)) {
            $errors[] = $original . " gagal ditimpa";
            continue;
        }

        if (move_uploaded_file($_FILES['files']['tmp_name'][$i], $target)) {
            $success++;
            $saved_files[] = $new_name;
        } else {
            $errors[] = $original . " gagal disimpan";
        }
    }

    if ($success === 0) {
        alert("Tidak ada file yang berhasil diupload");
        pindah("index.php?menu=anal_bayar");
        return;
    }

    $cabang_set = '';
    $cab_errors = [];
    foreach ($saved_files as $fname) {
        $path = $upload_dir . $fname;
        try {
            $reader = IOFactory::createReaderForFile($path);
            $objek = $reader->load($path);
            $ws = $objek->getActiveSheet();
            $text = ganti_karakter($ws->getCell("D2")->getValue());
            $regex = '/Cabang\s+([A-Z\s]+?)As/';
            preg_match($regex, $text, $matches);
            $cabang = isset($matches[1]) ? $matches[1] : '';
            $cabang = preg_replace('/As$/', '', $cabang);
            $cabang = trim($cabang);
        } catch (Exception $e) {
            $cabang = '';
        }

        if ($cabang === '') {
            $cab_errors[] = $fname . " tidak ditemukan nama cabang";
        } else {
            if ($cabang_set === '') {
                $cabang_set = $cabang;
            } elseif ($cabang_set !== $cabang) {
                $cab_errors[] = $fname . " cabang berbeda ($cabang)";
            }
        }
    }

    if (!empty($cab_errors)) {
        foreach ($saved_files as $fname) {
            $path = $upload_dir . $fname;
            if (file_exists($path)) {
                unlink($path);
            }
        }
        $pesan_error = "Cabang tidak sama:\n" . implode("\n", $cab_errors);
        alert($pesan_error);
        pindah("index.php?menu=anal_bayar");
        return;
    }

    if ($cabang_set !== '' && cekCabangBlocked($pdo, $cabang_set, $url . 'index.php?menu=anal_bayar')) {
        return;
    }

    try {
        $stmt_exist = $pdo->prepare("SELECT cabang FROM exec_analisa_par_multi WHERE session = :session LIMIT 1");
        $stmt_exist->execute([':session' => $sesi_upload]);
        $exist_row = $stmt_exist->fetch(PDO::FETCH_ASSOC);
        if ($exist_row && !empty($exist_row['cabang']) && $exist_row['cabang'] !== $cabang_set) {
            $new_session = $sesi_upload . '-' . date('His') . '-' . substr(bin2hex(random_bytes(2)), 0, 4);
            $new_dir = "FILE/" . $new_session . "/";
            if (!is_dir($new_dir)) {
                mkdir($new_dir, 0777, true);
            }
            foreach ($saved_files as $fname) {
                $old_path = $upload_dir . $fname;
                $new_path = $new_dir . $fname;
                if (file_exists($old_path)) {
                    rename($old_path, $new_path);
                }
            }
            $sesi_upload = $new_session;
            $upload_dir = $new_dir;
        }
    } catch (PDOException $e) {
        // ignore check error
    }

    $file_list = json_encode($saved_files);
    $waktu_sekarang = date("Y-m-d H:i:s");
    try {
        $stmt = $pdo->prepare("INSERT INTO exec_analisa_par_multi (session, cabang, file_list, total_files, status, message, uploaded_at)
            VALUES (:session, :cabang, :file_list, :total_files, 'uploaded', 'Upload selesai', :uploaded_at)
            ON DUPLICATE KEY UPDATE cabang = VALUES(cabang), file_list = VALUES(file_list), total_files = VALUES(total_files), status = 'uploaded', message = 'Upload diperbarui', uploaded_at = :uploaded_at_update");
        $stmt->execute([
            ':session' => $sesi_upload,
            ':cabang' => $cabang_set,
            ':file_list' => $file_list,
            ':total_files' => count($saved_files),
            ':uploaded_at' => $waktu_sekarang,
            ':uploaded_at_update' => $waktu_sekarang,
        ]);
    } catch (PDOException $e) {
        // ignore logging error
    }

    $pesan = "Berhasil upload $success file.";
    if (!empty($errors)) {
        $pesan .= "\\nSebagian gagal: " . implode(", ", $errors);
    }

    $exec_url = $url . "proses/exec/proses_exec_analisa_par.php?session=" . urlencode($sesi_upload);
    $redirect_url = $url . "index.php?menu=anal_bayar";
    
    echo "<script>
        window.addEventListener('load', function() {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Upload berhasil',
                    text: " . json_encode($pesan) . ",
                    showCancelButton: true,
                    confirmButtonText: 'Proses Sekarang',
                    cancelButtonText: 'Nanti Saja',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = " . json_encode($exec_url) . ";
                    } else {
                        window.location.href = " . json_encode($redirect_url) . ";
                    }
                });
            } else {
                if (confirm('Upload berhasil. Proses sekarang?')) {
                    window.location.href = " . json_encode($exec_url) . ";
                } else {
                    window.location.href = " . json_encode($redirect_url) . ";
                }
            }
        });
    </script>";
}
?>
