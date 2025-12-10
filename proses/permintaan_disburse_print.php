<div class="container-fluid">
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
    <div class="row">
        <p>Jika ingin diprint, klik kanan -> print</p>
        <hr>
        <div class="col-12">

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
                <div class="printArea">
                    <table border="1" class="table table-bordered" style="width:100%; margin-bottom:30px;">
                        <tr>
                            <th class="text-center p-1" colspan="6">
                                <h4>Permintaan Disburse Cabang <?= $namaCabang ?></h4>
                                <h4>Periode <?= haritanggal($tanggal_loop) ?></h4>
                            </th>
                        </tr>

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
                        $grandNettDisburse = 0;
                        ?>

                        <h4>Rincian</h4>
                        <table border="1" style="width:100%;" class="table-bordered">
                            <thead>
                                <tr>
                                    <th>STAFF</th>
                                    <th>CENTER - ANGGOTA</th>
                                    <th>JUMLAH</th>
                                    <th>PINJAMAN</th>
                                    <th>TOPUP</th>
                                    <th>NETT DISBURSE</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($results as $row) {
                                    $sisaOS = $row['total_pinjaman'];
                                    $osPokokTopUp = $row['os_pokok_top_up'];
                                    $netDisburse = $sisaOS - $osPokokTopUp;

                                    $grandTotalJumlah += $row['jumlah'];
                                    $grandTotalPinjaman += $sisaOS;
                                    $grandNettDisburse += $netDisburse;

                                    // Detail per anggota
                                    $query = "SELECT 
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
                                    $sta = $pdo->query($query)->fetchAll();

                                    echo "<tr style='background-color:#FADADA;'>
                                            <th colspan=2>{$row['officer_name']}</th>
                                            <th>{$row['jumlah']}</th>
                                            <th>" . formatNumber($sisaOS) . "</th>
                                            <th>" . formatNumber($osPokokTopUp) . "</th>
                                            <th>" . formatNumber($netDisburse) . "</th>
                                          </tr>";

                                    foreach ($sta as $st) {
                                        $netDisbursedet = $st['loan_amount'] - $st['os_pokok_top_up'];
                                        echo "<tr>
                                                <td>{$st['center_id']}</td>
                                                <td>{$st['client_id']} - {$st['client_name']} ({$st['product_name']} - {$st['pinj_ke']})</td>
                                                <td>{$st['jenis_top_up']}</td>
                                                <td>" . formatNumber($st['loan_amount']) . "</td>
                                                <td>" . formatNumber($st['os_pokok_top_up']) . "</td>
                                                <td>" . formatNumber($netDisbursedet) . "</td>
                                              </tr>";
                                    }
                                }
                                ?>
                                <tr>
                                    <td colspan="2"><strong>Grand Total</strong></td>
                                    <td><strong><?= $grandTotalJumlah ?></strong></td>
                                    <td><strong><?= formatNumber($grandTotalPinjaman) ?></strong></td>
                                    <td></td>
                                    <td><strong><?= formatNumber($grandNettDisburse) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                    </table>
                </div>

                <!-- page break untuk print per tanggal -->
                <div style="page-break-after: always;"></div>

            <?php
            }
            ?>

        </div>
    </div>
</div>

<style>
    @media print {
        body * {
            visibility: hidden;
        }

        .printArea,
        .printArea * {
            visibility: visible;
        }

        .printArea {
            position: relative;
            width: 100%;
        }
    }
</style>