<?php
$namaCabang = $_GET['nama_cabang'];
$id_cabang = $sesi;
?>
<div class="container-fluid px-4 py-3">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header-center">
                <h1 class="page-title">
                    <i class="fas fa-calendar-alt me-3"></i>JADWAL CENTER MEETING
                </h1>
                <p class="mb-0">Cabang: <strong><?= strtoupper($namaCabang) ?></strong></p>
            </div>
        </div>
    </div>

    <?php
    $cek  = "select count(*) as hitung from center where nama_cabang='$namaCabang'";
    $hitung = $pdo->query($cek);
    $hitung = $hitung->fetch()['hitung'];
    if ($hitung < 1) {
        pindah("index.php?menu=center_meeting");
    }
    ?>
    <div class="row">
        <div class="col-12">
            <div class="card modern-card mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <i class="fas fa-info-circle text-info me-2"></i>
                            <span class="text-muted">Export jadwal ke PDF atau cetak langsung</span>
                        </div>
                        <div>
                            <button onclick="exportToPDF()" class="btn btn-sm btn-danger me-2">
                                <i class="fas fa-file-pdf me-2"></i>Export PDF
                            </button>
                            <button onclick="window.print()" class="btn btn-sm btn-primary">
                                <i class="fas fa-print me-2"></i>Print Jadwal
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card modern-card">
                <div class="card-body p-0">
                    <div class="table-responsive" id='printArea'>
                        <table border="1" class='table table-bordered mb-0' style="border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th colspan="20" style="text-align: center; padding: 15px; font-size: 16px;">
                                        <strong>JADWAL CENTER MEETING</strong><br>
                                        <strong>CABANG <?= strtoupper($namaCabang) ?></strong>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Get all staff
                                $qkar_all = "SELECT distinct c.staff from center c where c.nama_cabang='$namaCabang' order by c.staff asc";
                                $staff_list = $pdo->query($qkar_all)->fetchAll();

                                // Get all days
                                $qhari = "SELECT distinct hari from center where nama_cabang='$namaCabang' order by FIELD(hari,'senin','selasa','rabu','kamis','jumat') asc";
                                $hari_list = $pdo->query($qhari)->fetchAll();

                                foreach ($hari_list as $hari) {
                                    // Header row with day name and staff names
                                    echo "<tr>";
                                    echo "<td rowspan='2' style='padding: 10px; font-weight: bold; vertical-align: middle; text-align: center; background-color: #f8f9fa;'>" . strtoupper($hari['hari']) . "</td>";

                                    foreach ($staff_list as $staff) {
                                        $pecah_nama = explode(" ", strtoupper($staff['staff']));
                                        $nama_staff = $pecah_nama[0];
                                        if (strlen($nama_staff) < 3) {
                                            $nama_staff = $pecah_nama[0] . " " . ($pecah_nama[1] ?? '');
                                        } else {
                                            if (!empty($pecah_nama[1])) {
                                                $nama_staff = $nama_staff . " " . $pecah_nama[1][0];
                                            }
                                        }
                                        echo "<th style='padding: 8px; text-align: center; font-size: 12px; min-width: 80px;'>" . $nama_staff . "</th>";
                                    }
                                    echo "<th rowspan='2' style='padding: 10px; font-weight: bold; vertical-align: middle; text-align: center; background-color: #f8f9fa;'>TOTAL</th>";
                                    echo "</tr>";

                                    // Get max rows needed for this day
                                    $max_rows = 0;
                                    $staff_centers = [];
                                    foreach ($staff_list as $staff) {
                                        $qcenter = $pdo->query("SELECT no_center, member_center from center where nama_cabang='$namaCabang' and hari='" . $hari['hari'] . "' and staff='" . $staff['staff'] . "' order by jam_center asc");
                                        $centers = $qcenter->fetchAll();
                                        $staff_centers[$staff['staff']] = $centers;
                                        if (count($centers) > $max_rows) {
                                            $max_rows = count($centers);
                                        }
                                    }

                                    // Data row - centers
                                    echo "<tr>";
                                    foreach ($staff_list as $staff) {
                                        echo "<td style='vertical-align: top; text-align: left; padding: 8px;'>";
                                        $centers = $staff_centers[$staff['staff']];
                                        foreach ($centers as $center) {
                                            echo sprintf("%03d", $center['no_center']) . "|" . $center['member_center'] . "<br>";
                                        }
                                        echo "</td>";
                                    }
                                    echo "</tr>";

                                    // Total row per day
                                    echo "<tr>";
                                    echo "<th style='padding: 8px; background-color: #f8f9fa;'>TOTAL CENTER</th>";
                                    $total_hari = 0;
                                    foreach ($staff_list as $staff) {
                                        $count = count($staff_centers[$staff['staff']]);
                                        $total_hari += $count;
                                        echo "<td style='text-align: center; font-weight: bold; padding: 8px; background-color: #dcdedc;'>" . $count . "</td>";
                                    }
                                    echo "<th style='text-align: center; padding: 8px; background-color: #f8f9fa;'>" . $total_hari . "</th>";
                                    echo "</tr>";
                                }

                                // Grand total rows
                                echo "<tr>";
                                echo "<th style='padding: 10px; background-color: #e9ecef;'>TOTAL SEMUA STAFF<br>CENTER</th>";
                                $grand_total = 0;
                                foreach ($staff_list as $staff) {
                                    $qcenter = $pdo->query("SELECT count(no_center) as hitung_center from center where nama_cabang='$namaCabang' and staff='" . $staff['staff'] . "'");
                                    $result = $qcenter->fetch();
                                    $grand_total += $result['hitung_center'];
                                    echo "<th style='text-align: center; padding: 8px; background-color: #dcdedc;'>" . $result['hitung_center'] . "</th>";
                                }
                                echo "<th style='text-align: center; padding: 8px; background-color: #e9ecef;'>" . $grand_total . "</th>";
                                echo "</tr>";

                                echo "<tr>";
                                echo "<th style='padding: 10px; background-color: #e9ecef;'>MEMBER</th>";
                                $grand_total_member = 0;
                                foreach ($staff_list as $staff) {
                                    $qcenter = $pdo->query("SELECT sum(member_center) as member from center where nama_cabang='$namaCabang' and staff='" . $staff['staff'] . "'");
                                    $result = $qcenter->fetch();
                                    $member = $result['member'] ?? 0;
                                    $grand_total_member += $member;
                                    echo "<th style='text-align: center; padding: 8px; background-color: #dcdedc;'>" . $member . "</th>";
                                }
                                echo "<th style='text-align: center; padding: 8px; background-color: #e9ecef;'>" . $grand_total_member . "</th>";
                                echo "</tr>";
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php
            $query = "delete from center where nama_cabang='$namaCabang'";
            $pdo->query($query);
            ?>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
            <script>
                function exportToPDF() {
                    const element = document.getElementById('printArea');
                    const opt = {
                        margin: 10,
                        filename: 'Jadwal_Center_<?= strtoupper($namaCabang) ?>_<?= date("Y-m-d") ?>.pdf',
                        image: { type: 'jpeg', quality: 0.98 },
                        html2canvas: { scale: 2, useCORS: true },
                        jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
                    };
                    
                    html2pdf().set(opt).from(element).save();
                }

                function printCtr(elementId) {
                    // Menyimpan elemen yang akan dicetak
                    var printElement = document.getElementById(elementId);
                    var originalContents = document.body.innerHTML;

                    // Menyembunyikan semua elemen lain kecuali elemen yang akan dicetak
                    document.body.innerHTML = printElement.outerHTML;

                    // Memanggil metode print
                    window.print();

                    // Mengembalikan konten asli halaman setelah mencetak
                    document.body.innerHTML = originalContents;
                    location.reload(); // Reload halaman untuk mengembalikan e

                }
            </script>