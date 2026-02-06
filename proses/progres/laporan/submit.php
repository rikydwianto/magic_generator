<h2>Submit Laporan Ke Regional</h2>
<hr>
<?php
if (isset($_GET['error'])) {
    $bulan = $_GET['bulan'];
    $minggu = $_GET['minggu'];
    $tahun = $_GET['tahun'];
    $pesan = urldecode($_GET['pesan']);

?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <?= $pesan ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
</div>

<?php
}
?>
<div class="">
    <form action="" method="get">
        <input type="hidden" name="menu" value="laporan/submit_next">
        <div class="row g-3 mb-3">
            <?php
            if ($jabatan == 'Regional') {
            ?>
            <div class="col">
                <div class="form-group">
                    <label for="cabang">Cabang:</label>

                    <select class="form-control" id="cabang" required name="cabang">

                        <option value="">Pilih Cabang</option>
                        <?php
                            $query = "SELECT id_cabang, nama_cabang FROM cabang";
                            $result = $pdo->query($query);

                            // Loop untuk menampilkan setiap elemen dalam array sebagai opsi
                            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                                echo '<option value="' . $row['nama_cabang'] . '">' . $row['nama_cabang'] . '</option>';
                            }

                            ?>
                    </select>

                </div>
            </div>
            <?php
            }
            ?>

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
</div>