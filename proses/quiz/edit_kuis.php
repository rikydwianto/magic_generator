<?php


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $id_kuis = $_POST['id_kuis'];
    $nama_kuis = $_POST['nama_kuis'];
    $nama_karyawan = $_POST['nama_karyawan'];
    $tgl_kuis = $_POST['tgl_kuis'];
    $waktu = $_POST['waktu'];
    $status = $_POST['status'];
    $acak = $_POST['acak'];
    $tampil_jawaban = $_POST['tampil_jawaban'];
    $responden = $_POST['anggota'];


    // Update data ke dalam database
    $query = "UPDATE kuis SET
                nama_kuis = :nama_kuis,
                nama_karyawan = :nama_karyawan,
                tgl_kuis = :tgl_kuis,
                waktu = :waktu,
                status = :status,
                acak = :acak,
                tampil_jawaban = :tampil_jawaban,
                anggota=:responden
              WHERE id_kuis = :id_kuis";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':nama_kuis', $nama_kuis);
    $stmt->bindParam(':nama_karyawan', $nama_karyawan);
    $stmt->bindParam(':tgl_kuis', $tgl_kuis);
    $stmt->bindParam(':waktu', $waktu);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':acak', $acak);
    $stmt->bindParam(':tampil_jawaban', $tampil_jawaban);
    $stmt->bindParam(':id_kuis', $id_kuis);
    $stmt->bindParam(':responden', $responden);

    try {
        $stmt->execute();
        pindah($url . "index.php?menu=quiz");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch();

?>
<div class="container mt-4">

    <h2>Edit Data Kuis</h2> <!-- Formulir Edit -->
    <form action="" method="post">
        <!-- ID Kuis (Dapat digunakan sebagai input tersembunyi) -->
        <input type="hidden" name="id_kuis" value="<?php echo $kuis['id_kuis']; ?>">

        <!-- Nama Kuis -->
        <div class="mb-3">
            <label for="nama_kuis" class="form-label">Nama Kuis</label>
            <input type="text" class="form-control" id="nama_kuis" name="nama_kuis" placeholder="Masukkan Nama Kuis" value="<?php echo $kuis['nama_kuis']; ?>" required>
        </div>

        <!-- Nama Karyawan -->
        <div class="mb-3">
            <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
            <input type="text" class="form-control" id="nama_karyawan" name="nama_karyawan" placeholder="Masukkan Nama Karyawan" value="<?php echo $kuis['nama_karyawan']; ?>">
        </div>

        <!-- Tanggal Kuis -->
        <div class="mb-3">
            <label for="tgl_kuis" class="form-label">Tanggal Kuis</label>
            <input type="date" class="form-control" id="tgl_kuis" name="tgl_kuis" value="<?php echo $kuis['tgl_kuis']; ?>">
        </div>

        <!-- Waktu -->
        <!-- <div class="mb-3">
        <label for="waktu" class="form-label">Waktu</label>
    </div> -->
        <input type="hidden" class="form-control" id="waktu" name="waktu" placeholder="Masukkan Waktu" value="<?php echo $kuis['waktu']; ?>">

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="aktif" <?php echo ($kuis['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                <option value="tidakaktif" <?php echo ($kuis['status'] == 'tidakaktif') ? 'selected' : ''; ?>>Nonaktif
                </option>
            </select>
        </div>

        <!-- Acak -->
        <div class="mb-3">
            <label for="acak" class="form-label">Acak</label>
            <select class="form-select" id="acak" name="acak">
                <option value="tidak" <?php echo ($kuis['acak'] == 'tidak') ? 'selected' : ''; ?>>Tidak</option>
                <option value="ya" <?php echo ($kuis['acak'] == 'ya') ? 'selected' : ''; ?>>Ya</option>
            </select>
        </div>

        <!-- Tampil Jawaban -->
        <div class="mb-3">
            <label for="tampil_jawaban" class="form-label">Tampil Jawaban</label>
            <select class="form-select" id="tampil_jawaban" name="tampil_jawaban">
                <option value="ya" <?php echo ($kuis['tampil_jawaban'] == 'ya') ? 'selected' : ''; ?>>Ya</option>
                <option value="tidak" <?php echo ($kuis['tampil_jawaban'] == 'tidak') ? 'selected' : ''; ?>>Tidak
                </option>
            </select>
        </div>

        <!-- Responden -->
        <div class="mb-3">
            <label for="anggota" class="form-label">Untuk Anggota?</label>
            <select class="form-select" id="anggota" name="anggota">
                <option value="ya" <?php echo ($kuis['anggota'] == 'ya') ? 'selected' : ''; ?>>Ya</option>
                <option value="tidak" <?php echo ($kuis['anggota'] == 'tidak') ? 'selected' : ''; ?>>Tidak
                </option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
    </form>
</div>