<div class="col-lg-6">


    <div class="container ">
        <h2 class="mb-4">Input Capaian</h2>
        <form action="" method="post">
            <div class="form-group">
                <label for="aktifitas">Aktifitas:</label>
                <select class="form-control" id="aktifitas" name="aktifitas" required>
                    <option value="">Pilih kegiatan</option>
                    <?php
                    // Ambil daftar aktivitas dari tabel target
                    $stmt = $pdo->query("SELECT DISTINCT aktifitas,singkatan FROM target");
                    $daftarAktifitas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    // Tampilkan sebagai opsi pada dropdown
                    foreach ($daftarAktifitas as $aktivitas) {
                        echo "<option value='{$aktivitas['aktifitas']}|{$aktivitas['singkatan']}'>{$aktivitas['aktifitas']} - {$aktivitas['singkatan']}</option>";
                    }
                    ?>
                </select>

            </div>
            <div class="form-group">
                <label for="bulan">Bulan:</label>
                <select class="form-control" id="bulan" name="bulan" required>
                    <option value="">Pilih bulan</option>
                    <?php
                    $selbulan = "";
                    foreach ($bulanArray as $kodeBulan => $namaBulan) {
                        if ($kodeBulan == date("m")) $selbulan = 'selected';
                        else $selbulan = "";
                        echo "<option $selbulan value='{$kodeBulan}'>{$namaBulan}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tahun">Tahun:</label>
                <select class="form-control" id="tahun" name="tahun" required>
                    <?php
                    // Tampilkan opsi tahun pada dropdown
                    $tahunArray = array(
                        2023, 2024, 2025, 2026
                    );
                    $seltahun = "";
                    foreach ($tahunArray as $kodetahun) {
                        if ($kodetahun == date("Y")) $seltahun = 'selected';
                        else $seltahun = "";
                        echo "<option $seltahun value='{$kodetahun}'>{$kodetahun}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal">TglInput/kegiatan:</label>
                <input type="date" class="form-control" id="tanggal" value='<?= date("Y-m-d") ?>' name="tanggal"
                    required>
            </div>
            <div class="form-group">
                <label for="capaian">Capaian:</label>
                <input type="number" class="form-control" id="capaian" name="capaian" required>
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
</div>
<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data dari form
    $aktifitas = $_POST['aktifitas'];
    list($kegiatan, $singkatan) = explode("|", $aktifitas);
    $bulan = $_POST['bulan'];
    $tahun = $_POST['tahun'];
    $capaian = $_POST['capaian'];
    $tgl = $_POST['tanggal'];

    // Lakukan validasi atau manipulasi data jika diperlukan

    // Simpan data ke dalam tabel capaian
    $stmt = $pdo->prepare("INSERT INTO capaian (aktifitas,singkatan, bulan, tahun, capaian, id_user, tgl_input) 
                           VALUES (:aktifitas,:singkatan, :bulan, :tahun, :capaian, :id_user, :tgl_input)");

    // Ganti nilai placeholder dengan data yang sesuai
    $stmt->bindParam(':aktifitas', $kegiatan);
    $stmt->bindParam(':singkatan', $singkatan);
    $stmt->bindParam(':bulan', $bulan);
    $stmt->bindParam(':tahun', $tahun);
    $stmt->bindParam(':capaian', $capaian);
    $stmt->bindParam(':id_user', $sesi); // Sesuaikan dengan cara Anda menyimpan ID pengguna
    $stmt->bindParam(':tgl_input', $tgl); // Tanggal input saat ini

    // Eksekusi query
    if ($stmt->execute()) {
        echo "Data capaian berhasil disimpan.";
    } else {
        echo "Gagal menyimpan data capaian.";
    }
}
?>