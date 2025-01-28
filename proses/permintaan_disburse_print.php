<div class="container-fluid">
    <?php
    $namaCabang = aman($_GET['nama_cabang']);
    $tanggal = aman($_GET['tanggal']);
    $cek  = "SELECT COUNT(*) AS hitung FROM permintaan_disburse WHERE nama_cabang='$namaCabang'";
    $hitung = $pdo->query($cek);
    $hitung = $hitung->fetch()['hitung'];
    if ($hitung < 1) {
        pindah("index.php?menu=permintaandisburse");
    }
    ?>
    <div class="row">
        <p>Jika ingin diprint, klik kanan -> print</p>

        <hr>
        <div class="col-12">
            <div id="printArea">
                <table border="1" class="table table-bordered">
                    <tr>
                        <th class="text-center p-1" colspan="5">
                            <h4>Permintaan Disburse Cabang <?= $namaCabang ?></h4>
                            <h4>Priode <?= haritanggal($tanggal) ?></h4>
                        </th>
                    </tr>

                    <?php
                    // Query untuk pivot jenis topup
                    $query = "SELECT 
                                CASE 
                                    WHEN jenis_top_up = '' THEN 'Bukan Topup'
                                    ELSE CONCAT('TOPUP ',jenis_top_up)
                                END AS rincian_topup,
                                COUNT(*) AS total_pencairan,
                                SUM(loan_amount) AS sum_of_jumlah_pinjaman,
                                SUM(loan_amount) * 0.05 AS sum_of_5_percent,
                                SUM(loan_amount) - SUM(loan_amount) * 0.05 AS sum_of_after_5_percent
                            FROM permintaan_disburse
                            WHERE nama_cabang = '$namaCabang' AND tanggal = '$tanggal'
                            GROUP BY rincian_topup";

                    $stmt = $pdo->query($query);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // echo "<table border='1' style='width: 100%; margin-bottom: 20px;' class='table-bordered'>";
                    // echo "<tr>
                    //         <th>Rincian Topup</th>
                    //         <th>Total Pencairan</th>
                    //         <th>Sum of Jumlah Pinjaman</th>
                    //         <th>Sum of 5%</th>
                    //         <th>Sum of AFTER 5%</th>
                    //     </tr>";
                    // foreach ($results as $row) {
                    //     echo "<tr>
                    //             <td>{$row['rincian_topup']}</td>
                    //             <td class='text-center'>{$row['total_pencairan']}</td>
                    //             <td>" . formatNumber($row['sum_of_jumlah_pinjaman']) . "</td>
                    //             <td>" . formatNumber($row['sum_of_5_percent']) . "</td>
                    //             <td>" . formatNumber($row['sum_of_after_5_percent']) . "</td>
                    //         </tr>";
                    // }
                    // echo "</table>";

                    // Query untuk data staff dan anggota
                    $query = "SELECT 
                                officer_name, 
                                client_name AS member_name,
                                COUNT(*) AS jumlah, 
                                SUM(loan_amount) AS total_pinjaman,sum(os_pokok_top_up) as os_pokok_top_up
                            FROM permintaan_disburse
                            WHERE nama_cabang = '$namaCabang' AND tanggal = '$tanggal'
                            GROUP BY officer_name";

                    $stmt = $pdo->query($query);
                    $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                    ?>
                    <h1>Rincian</h1>
                    <table border="1" style="width: 100%; margin-bottom: 20px;" class='table-bordered'>
                        <thead>
                            <tr>
                                <th>STAFF</th>
                                <th>CENTER - ANGGOTA</th>
                                <th>JUMLAH</th>
                                <th>PINJAMAN</th>
                                <th>TOPUP</th>
                                <th>NETT DISBURSE</th>
                                <!-- <th>- AFTER 5 %</th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grandTotalJumlah = 0;
                            $grandTotalPinjaman = 0;
                            $grandLimaPersen = 0;
                            $grandAfter5 = 0;
                            $grandNettDisburse = 0;

                            foreach ($results as $row) {
                                $sisaOS = $row['total_pinjaman'];
                                $minus5 = $sisaOS * 0.05;
                                $after5 = $sisaOS - $minus5;

                                $netDisburse = 0;
                                $grandTotalJumlah += $row['jumlah'];
                                $grandTotalPinjaman += $sisaOS;
                                $grandLimaPersen += $minus5;
                                $grandAfter5 += $after5;
                                $osPokokTopUp = $row['os_pokok_top_up'];
                                $netDisburse = $row['total_pinjaman'] - $osPokokTopUp;
                                $grandNettDisburse += $netDisburse;

                                $query = "SELECT 
                                            (loan_amount) AS total_pinjaman, center_id,client_name,os_pokok_top_up,client_id,jenis_top_up,pinj_ke
                                        FROM permintaan_disburse
                                        WHERE nama_cabang = '$namaCabang' AND tanggal = '$tanggal' AND officer_name = '{$row['officer_name']}'";
                                $sta = $pdo->query($query);
                                $sta = $sta->fetchAll();

                                echo "<tr style='background-color: #FADADAFF;'>
                                        <th colspan=2>{$row['officer_name']}</th>
                                        <th>{$row['jumlah']}</th>
                                        <th>" . formatNumber($sisaOS) . "</th>
                                        <th>" . formatNumber($osPokokTopUp) . "</th>
                                        <th>" . formatNumber($netDisburse) . "</th>
                                    </tr>";
                                $jenis_top_up = '';
                                foreach ($sta as $st) {
                                    $pinj_ke = $st['pinj_ke'];
                                    $center_id = $st['center_id'];
                                    $osPokokTopUpdet = $st['os_pokok_top_up'];
                                    $netDisbursedet = $st['total_pinjaman'] - $osPokokTopUpdet;
                                    $cleint_id = $st['client_id'];
                                    $jenis_top_up = $st['jenis_top_up'];
                                    echo "<tr>
                                            <td>$center_id</td>
                                            <td>$cleint_id - {$st['client_name']}($pinj_ke)</td>
                                            <td>$jenis_top_up</td>
                                            <td>" . formatNumber($st['total_pinjaman']) . "</td>
                                            <td>" . formatNumber($osPokokTopUpdet) . "</td>
                                            <td>" . formatNumber($netDisbursedet) . "</td>
                                        </tr>";
                                }
                            }
                            ?>

                            <tr>
                                <td colspan="2"><strong>Grand Total</strong></td>
                                <td><strong><?php echo $grandTotalJumlah; ?></strong></td>
                                <td><strong><?php echo formatNumber($grandTotalPinjaman); ?></strong></td>
                                <td><strong><?= formatNumber($grandLimaPersen) ?></strong></td>
                                <td><strong><?= formatNumber($grandNettDisburse) ?></strong></td>
                            </tr>
                        </tbody>
                    </table>
                </table>
            </div> <!-- end #printArea -->
        </div>
    </div>
</div>

<style>
    @media print {

        /* Menyembunyikan elemen selain printArea */
        body * {
            visibility: hidden;
        }

        #printArea,
        #printArea * {
            visibility: visible;
        }

        #printArea {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Memperbaiki border dan padding tabel */
        table,
        tr,
        th,
        td {
            border: 1px solid #6b6c6e;
            /* Warna border yang jelas */
            padding: 8px 12px;
            /* Menambah padding untuk kenyamanan */
            /* Menetapkan teks ke kiri untuk setiap kolom */
        }

        /* Mengatur posisi angka di kanan */
        td,
        th {
            /* Angka di kanan untuk kolom selain teks */
        }

        /* Khusus untuk kolom JUMLAH di tengah */
        td.jumlah {
            text-align: center;
        }

        /* Membuat header tabel menjadi lebih besar dan tebal */
        th {
            font-size: 16px;
            /* Ukuran font header lebih besar */
            font-weight: bold;
            background-color: #f2f2f2;
            /* Memberi warna latar belakang pada header */
        }

        /* Mengatur baris alternatif menjadi sedikit berbeda */
        tr:nth-child(even) {
            background-color: #f9f9f9;
            /* Memberikan warna latar belakang alternatif pada baris genap */
        }

        /* Menambahkan ruang antar bagian untuk kesan yang lebih rapi */
        .container-fluid {
            margin-top: 20px;
        }

        /* Mengatur font yang lebih besar dan lebih jelas pada saat print */
        body {
            font-size: 14px;
            font-family: Arial, sans-serif;
        }
    }
</style>