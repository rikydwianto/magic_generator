<h1>Buat Laporan</h1>
<div class=" ">
    <form action="" method="post">
        <div class="row">
            <div class="col-lg-6 ">
                <div class="mb-3">
                    <label for="nik_staff" class="form-label">NIK Staff:</label>
                    <input type="text" readonly class="form-control" value='<?= $detailAkun['nik_staff'] ?>' name="nik_staff" required>
                </div>

                <div class="mb-3">
                    <label for="nama_staff" class="form-label">Nama Staff:</label>
                    <input type="text" readonly class="form-control" value='<?= $detailAkun['nama_staff'] ?>' name="nama_staff" required>
                </div>

                <div class="mb-3">
                    <label for="cabang_staff" class="form-label">Cabang Staff:</label>
                    <input type="text" readonly class="form-control" value='<?= $detailAkun['cabang'] ?>' name="cabang_staff" required>
                </div>


            </div>
            <div class="col-lg-6 ">
                <div class="row g-3">
                    <div class="col">

                        <div class="mb-3">
                            <label for="regional" class="form-label">Regional:</label>
                            <input type="text" readonly class="form-control" value='<?= $detailAkun['regional'] ?>' name="regional" required>
                        </div>

                    </div>
                    <div class="col">
                        <label for="wilayah" class="form-label">Wilayah:</label>
                        <input type="number" readonly class="form-control" value='<?= $detailAkun['wilayah'] ?>' name="wilayah" required>
                    </div>
                </div>
                <div class="form-group mt-2">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="minggu">Minggu:</label>
                                <select class="form-control" id="minggu" name="minggu" required>
                                    <option value="">Minggu</option>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        echo "<option value='{$i}'>{$i}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="bulan">Bulan:</label>
                            <select class="form-control" id="bulan" name="bulan" required>
                                <option value="">Pilih bulan</option>
                                <?php
                                $selbulan = "";
                                foreach ($bulanArray as $kodeBulan => $namaBulan) {
                                    if ($kodeBulan == date("m")) $selbulan = 'selected';
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


                </div>


                <button type="submit" name='tambah' class="btn btn-primary mt-3">Lanjut <i class="fa fa-arrow-right"></i></button>
            </div>
        </div>



    </form>
</div>
<h1>Laporan Pending</h1>
<?php
// proses_capaian_staff.php
$id_capaian_staff = "";
if (isset($_POST['tambah'])) {
    try {
        // Tangkap data dari formulir
        $nik_staff = $_POST['nik_staff'];
        $nama_staff = $_POST['nama_staff'];
        $cabang_staff = $_POST['cabang_staff'];
        $regional = $_POST['regional'];
        $wilayah = $_POST['wilayah'];
        $bulan = $_POST['bulan'];
        $minggu = $_POST['minggu'];
        $tahun = $_POST['tahun'];
        // ... (tangkap data untuk kolom-kolom lainnya)

        // Lakukan query SELECT untuk memeriksa apakah data sudah ada
        $sqlCheck = "SELECT id_capaian_staff FROM capaian_staff
             WHERE nik_staff = ? AND cabang_staff = ? AND bulan = ? AND minggu = ? AND tahun= ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$nik_staff, $cabang_staff, $bulan, $minggu, $tahun]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);

        // Jika data sudah ada, ambil ID-nya
        if ($result) {
            $id_capaian_staff = $result['id_capaian_staff'];
        } else {
            // Jika data belum ada, lakukan operasi INSERT
            $sqlInsert = "INSERT INTO capaian_staff (nik_staff, nama_staff, cabang_staff, regional, wilayah, bulan, minggu, tahun,status)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?,?)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$nik_staff, $nama_staff, $cabang_staff, $regional, $wilayah, $bulan, $minggu, $tahun, 'pending']);

            // Dapatkan ID yang di-insert
            $id_capaian_staff = $pdo->lastInsertId();
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if ($id_capaian_staff != "") {
    pindah(menu_sl("laporan/capaian&id=$id_capaian_staff"));
}
?>


<?php
try {
    // Query untuk mengambil data dari tabel capaian_staff
    $query = "SELECT * FROM capaian_staff where nik_staff='$nik_staff' and status='pending' order by id_capaian_staff desc limit 0,5";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan data dalam bentuk tabel HTML
?>
    <table class='table table-rensponsive'>
        <tr>
            <th>NO</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Cabang</th>
            <th>Regional</th>
            <th>Priode</th>
            <th>Minggu ke-</th>
            <th>Status</th>
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
                <td><?= $bulanArray[$row['bulan']] ?> - <?= $row['tahun'] ?></td>
                <td><?= $row['minggu'] ?></td>
                <td>
                    <?php
                    if ($row['status'] == 'pending') badge("Pending", "danger");
                    else if ($row['status'] == 'konfirmasi') badge("Konfirmasi", "warning");
                    else if ($row['status'] == 'approve') badge("success");

                    ?>

                </td>
                <td>

                    <?php
                    if ($row['status'] == 'pending') {
                    ?>
                        <a href="<?= menu_sl("laporan/edit&id=$row[id_capaian_staff]") ?>" class="btn btn-warning text-white">Lanjut
                            <i class="fa fa-arrow-right"></i></a>
                    <?php
                    }
                    ?>
                </td>
            </tr>
    <?php
            $no++;
        }

        echo "</table>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Tutup koneksi ke database
    $pdo = null;
    ?>