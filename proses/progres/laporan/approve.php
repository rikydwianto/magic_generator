<h2>Persetujuan Laporan Staff</h2>
<hr>
<?php
$minggu = isset($_GET['minggu']) ? $_GET['minggu'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : '';

?>
<form action="" method="get">
    <input type="hidden" name="menu" value="laporan/approve">
    <div class="row g-3 mb-3">

        <div class="col">
            <label for="minggu">Minggu:</label>
            <select class="form-control" id="minggu" name="minggu" required>
                <option value="">Minggu</option>
                <?php
                for ($i = 1; $i <= 5; $i++) {
                    $selming = $i == $minggu ? "selected" : "";
                    echo "<option $selming value='{$i}'>{$i}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col">
            <label for="bulan">Bulan:</label>
            <select class="form-control" id="bulan" name="bulan" required>
                <option value="">Pilih bulan</option>
                <?php
                $selbulan = "";
                foreach ($bulanArray as $kodeBulan => $namaBulan) {
                    if (($kodeBulan == date("m")) || ($kodeBulan == $bulan)) $selbulan = 'selected';
                    else $selbulan = "";
                    echo "<option $selbulan value='{$kodeBulan}'>{$kodeBulan} - {$namaBulan}</option>";
                }
                ?>
            </select>
        </div>
        <div class="col">
            <label for="tahun">Tahun:</label>
            <select class="form-control" id="tahun" name="tahun" required>
                <option value="">Pilih tahun</option>
                <?php
                for ($i = 2023; $i <= 2026; $i++) {
                    $selTahun = $i == date("Y") ? "selected" : "";
                    echo "<option $selTahun value='{$i}'>{$i}</option>";
                }
                ?>
            </select>
        </div>

    </div>
    <div class="row g-3">
        <div class="col">
            <button class="btn btn-success"><i class="fa fa-magnifying-glass"></i> Cari</button>
        </div>

    </div>

</form>
<?php
if ($minggu != "" && $bulan != "" && $tahun != "") {


    try {
        // Query untuk mengambil data dari tabel capaian_staff
        $query = "SELECT * FROM capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff where
         cabang_staff='$detailAkun[nama_cabang]' 
         and status='konfirmasi'
         and minggu=:minggu and bulan=:bulan and tahun=:tahun

           order by tahun,bulan,minggu desc";


        $stmt = $pdo->prepare($query);
        $stmt->bindParam(":minggu", $minggu);
        $stmt->bindParam(":bulan", $bulan);
        $stmt->bindParam(":tahun", $tahun);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Menampilkan data dalam bentuk tabel HTML
?>
<table class='table table-rensponsive' id='tabelCapaianStaff'>
    <tr>
        <th>NO</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Cabang</th>
        <th>Regional</th>
        <th>Minggu ke-</th>
        <th>Priode</th>
        <th>Keterangan</th>
        <th>created at</th>
        <th>#</th>
    </tr>
    <?php
            $no = 1;
            foreach ($result as $row) {
            ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $row['nik_staff'] ?></td>
        <td><?= $row['nama_staff'] ?></td>
        <td><?= $row['cabang_staff'] ?></td>
        <td>Reg.<?= $row['regional'] ?>/<?= $row['wilayah'] ?></td>
        <td><?= $row['minggu'] ?></td>
        <td><?= $bulanArray[$row['bulan']] ?> - <?= $row['tahun'] ?></td>
        <td><?= $row['keterangan'] ?></td>
        <td><?= $row['created_at'] ?></td>

        <td>
            <a href="<?= menu_progress("laporan/cek_laporan&id=$row[id_capaian_staff]") ?>"
                class="btn btn-success  text-white"><i class="fa fa-check"></i></a>



        </td>
    </tr>
    <?php
                $no++;
            }

            echo "</table>";
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    // Tutup koneksi ke database
    $pdo = null;
    ?>