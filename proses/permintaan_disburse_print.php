<div class="container-fluid px-4 py-3">
    <?php
    $namaCabang = aman($_GET['nama_cabang']);
    $tanggal = aman($_GET['tanggal']);
    $tanggal_akhir = aman($_GET['tanggal_akhir']);

    $cek  = "SELECT COUNT(*) AS hitung 
             FROM permintaan_disburse 
             WHERE nama_cabang='$namaCabang'";
    $hitung = $pdo->query($cek)->fetch()['hitung'];

    if ($hitung < 1) {
        pindah("index.php?menu=permintaandisburse");
    }
    ?>
    
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-disburse">
                <h1 class="page-title">
                    <i class="fas fa-money-check-alt me-3"></i>Laporan Permintaan Disburse
                </h1>
                <p class="mb-0">Cabang: <strong><?= strtoupper($namaCabang) ?></strong> | Periode: <strong><?= date('d M Y', strtotime($tanggal)) ?> - <?= date('d M Y', strtotime($tanggal_akhir)) ?></strong></p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card modern-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <span class="text-muted">Export laporan ke PDF atau cetak langsung</span>
                        </div>
                        <div>
                            <button onclick="exportToPDF()" class="btn btn-sm btn-danger me-2">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                            <button onclick="window.print()" class="btn btn-sm btn-primary">
                                <i class="fas fa-print me-2"></i>Print Laporan
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12" id="printArea">
            <?php
            // Ambil semua tanggal dalam range
            $qtgl = $pdo->query("
                SELECT tanggal 
                FROM permintaan_disburse 
                WHERE nama_cabang='$namaCabang' 
                AND tanggal BETWEEN '$tanggal' AND '$tanggal_akhir' 
                GROUP BY tanggal
                ORDER BY tanggal ASC
            ");

            while ($tgl = $qtgl->fetch()) {
                $tanggal_loop = $tgl['tanggal'];
            ?>
                <div class="card modern-card mb-4">
                    <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); color: white;">
                        <h5 class="mb-0 text-center">
                            <strong>PERMINTAAN DISBURSE CABANG <?= strtoupper($namaCabang) ?></strong><br>
                            <span style="font-size: 0.9rem;">Periode: <?= haritanggal($tanggal_loop) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered mb-0" style="border-collapse: collapse;">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 10%;">STAFF</th>
                                        <th style="width: 35%;">CENTER - ANGGOTA</th>
                                        <th style="width: 8%;">JUMLAH</th>
                                        <th style="width: 15%;">PINJAMAN</th>
                                        <th style="width: 15%;">TOPUP</th>
                                        <th style="width: 17%;">NETT DISBURSE</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Ambil data per officer di tanggal tertentu
                                    $query = "SELECT 
                                                officer_name, 
                                                COUNT(*) AS jumlah, 
                                                SUM(loan_amount) AS total_pinjaman,
                                                SUM(os_pokok_top_up) as os_pokok_top_up
                                            FROM permintaan_disburse
                                            WHERE nama_cabang = '$namaCabang' 
                                              AND tanggal = '$tanggal_loop'
                                            GROUP BY officer_name";
                                    $stmt = $pdo->query($query);
                                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                                    $grandTotalJumlah = 0;
                                    $grandTotalPinjaman = 0;
                                    $grandTotalTopup = 0;
                                    $grandNettDisburse = 0;

                                    foreach ($results as $row) {
                                        $sisaOS = $row['total_pinjaman'];
                                        $osPokokTopUp = $row['os_pokok_top_up'];
                                        $netDisburse = $sisaOS - $osPokokTopUp;

                                        $grandTotalJumlah += $row['jumlah'];
                                        $grandTotalPinjaman += $sisaOS;
                                        $grandTotalTopup += $osPokokTopUp;
                                        $grandNettDisburse += $netDisburse;

                                        // Detail per anggota
                                        $query_detail = "SELECT 
                                                    center_id,
                                                    client_id,
                                                    client_name,
                                                    loan_amount,
                                                    os_pokok_top_up,
                                                    jenis_top_up,
                                                    pinj_ke,
                                                    product_name
                                                FROM permintaan_disburse
                                                WHERE nama_cabang = '$namaCabang' 
                                                  AND tanggal = '$tanggal_loop' 
                                                  AND officer_name = '{$row['officer_name']}'";
                                        $sta = $pdo->query($query_detail)->fetchAll();

                                        echo "<tr style='background-color: #ffe5e5; font-weight: bold;'>
                                                <td colspan='2' style='padding: 10px;'>{$row['officer_name']}</td>
                                                <td style='text-align: center; padding: 10px;'>{$row['jumlah']}</td>
                                                <td style='text-align: right; padding: 10px;'>" . formatNumber($sisaOS) . "</td>
                                                <td style='text-align: right; padding: 10px;'>" . formatNumber($osPokokTopUp) . "</td>
                                                <td style='text-align: right; padding: 10px;'>" . formatNumber($netDisburse) . "</td>
                                              </tr>";

                                        foreach ($sta as $st) {
                                            $netDisbursedet = $st['loan_amount'] - $st['os_pokok_top_up'];
                                            echo "<tr>
                                                    <td style='padding: 8px; text-align: center;'>{$st['center_id']}</td>
                                                    <td style='padding: 8px;'>{$st['client_id']} - {$st['client_name']} <small>({$st['product_name']} - Ke-{$st['pinj_ke']})</small></td>
                                                    <td style='padding: 8px; text-align: center;'><span class='badge bg-info'>{$st['jenis_top_up']}</span></td>
                                                    <td style='padding: 8px; text-align: right;'>" . formatNumber($st['loan_amount']) . "</td>
                                                    <td style='padding: 8px; text-align: right;'>" . formatNumber($st['os_pokok_top_up']) . "</td>
                                                    <td style='padding: 8px; text-align: right; font-weight: bold;'>" . formatNumber($netDisbursedet) . "</td>
                                                  </tr>";
                                        }
                                    }
                                    ?>
                                    <tr style="background-color: #e9ecef; font-weight: bold; font-size: 1.05rem;">
                                        <td colspan="2" style="padding: 12px; text-align: center;">GRAND TOTAL</td>
                                        <td style="padding: 12px; text-align: center;"><?= $grandTotalJumlah ?></td>
                                        <td style="padding: 12px; text-align: right;"><?= formatNumber($grandTotalPinjaman) ?></td>
                                        <td style="padding: 12px; text-align: right;"><?= formatNumber($grandTotalTopup) ?></td>
                                        <td style="padding: 12px; text-align: right;"><?= formatNumber($grandNettDisburse) ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- page break untuk print per tanggal -->
                <div style="page-break-after: always;"></div>

            <?php
            }
            ?>

        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function exportToPDF() {
        const element = document.getElementById('printArea');
        const opt = {
            margin: 10,
            filename: 'Permintaan_Disburse_<?= strtoupper($namaCabang) ?>_<?= date("Y-m-d") ?>.pdf',
            image: { type: 'jpeg', quality: 0.98 },
            html2canvas: { scale: 2, useCORS: true },
            jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        
        html2pdf().set(opt).from(element).save();
    }
</script>