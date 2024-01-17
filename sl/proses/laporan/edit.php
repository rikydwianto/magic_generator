<h1>Buat Laporan</h1>
<?php
$id_capaian_staff = isset($_GET['id']) ? $_GET['id'] : die('Error: ID Capaian Staff tidak ditemukan.');

$query = "SELECT * FROM capaian_staff WHERE id_capaian_staff = :id_capaian_staff";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':id_capaian_staff', $id_capaian_staff, PDO::PARAM_INT);
$stmt->execute();
$data = $stmt->fetch(PDO::FETCH_ASSOC);

?>
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
                <div class="form-group mt-2 ">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="minggu">Minggu:</label>
                                <select class="form-control is-invalid" id="minggu" name="minggu" required>
                                    <option value="">Minggu</option>
                                    <?php
                                    for ($i = 1; $i <= 5; $i++) {
                                        $selming = $data['minggu'] == $i ? "selected" : "";
                                        echo "<option $selming value='{$i}'>{$i}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <label for="bulan">Bulan:</label>
                            <select class="form-control is-invalid" id="bulan" name="bulan" required>
                                <option value="">Pilih bulan</option>
                                <?php
                                $selbulan = "";
                                foreach ($bulanArray as $kodeBulan => $namaBulan) {
                                    if ($kodeBulan == $data['bulan']) $selbulan = 'selected';
                                    else $selbulan = "";
                                    echo "<option $selbulan value='{$kodeBulan}'>{$kodeBulan} - {$namaBulan}</option>";
                                }
                                ?>
                            </select>
                        </div>
                        <div class="col">
                            <label for="tahun">Tahun:</label>
                            <select class="form-control is-invalid" id="tahun" name="tahun" required>
                                <option value="">Pilih tahun</option>
                                <?php
                                for ($i = 2023; $i <= 2026; $i++) {
                                    $selTahun = $i == $data['tahun'] ? "selected" : "";
                                    echo "<option $selTahun value='{$i}'>{$i}</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <small>PASTIKAN MINGGU, BULAN, TAHUN SESUAI</small>

                </div>





                <button type="submit" name='tambah' class="btn btn-primary mt-3">Lanjut <i class="fa fa-arrow-right"></i></button>
            </div>
        </div>



    </form>
</div>
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
        $sqlCheck = "SELECT * FROM capaian_staff
             WHERE nik_staff = ? AND cabang_staff = ? AND bulan = ? AND minggu = ? AND tahun= ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$nik_staff, $cabang_staff, $bulan, $minggu, $tahun]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
        // echo var_dump($result);
        // Jika data sudah ada, ambil ID-nya
        if ($result['status'] == 'approve') {
            alert("Gagal disimpan, laporan tersebut sudah di approve");
        } else {
            // Query SQL untuk update kolom tertentu
            $query = "UPDATE capaian_staff 
        SET minggu = :minggu, bulan = :bulan, tahun = :tahun 
        WHERE id_capaian_staff = :id_capaian_staff";

            $id_capaian_staff = $_GET['id'];
            // Menyiapkan dan menjalankan query
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id_capaian_staff', $id_capaian_staff, PDO::PARAM_INT);
            $stmt->bindParam(':minggu', $minggu, PDO::PARAM_INT);
            $stmt->bindParam(':bulan', $bulan, PDO::PARAM_INT);
            $stmt->bindParam(':tahun', $tahun, PDO::PARAM_INT);
            $stmt->execute();

            pindah(menu_sl("laporan/capaian&id=$id_capaian_staff"));
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

?>