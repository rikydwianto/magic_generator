<div class="container mt-4">
    <h2>Tambah Data Kuis</h2>

    <!-- Formulir Tambah -->
    <form action="" method="post">
        <!-- Nama Kuis -->
        <div class="mb-3">
            <label for="nama_kuis" class="form-label">Nama Kuis</label>
            <input type="text" class="form-control" id="nama_kuis" name="nama_kuis" placeholder="Masukkan Nama Kuis"
                required>
        </div>

        <!-- Nama Karyawan -->
        <div class="mb-3">
            <label for="nama_karyawan" class="form-label">Nama Karyawan</label>
            <input type="text" required class="form-control" id="nama_karyawan" name="nama_karyawan"
                placeholder="Masukkan Nama Karyawan">
        </div>

        <!-- Tanggal Kuis -->
        <div class="mb-3">
            <label for="tgl_kuis" class="form-label">Tanggal Kuis</label>
            <input type="date" value="<?= date("Y-m-d") ?>" class="form-control" id="tgl_kuis" name="tgl_kuis">
        </div>

        <!-- Waktu -->
        <div class="mb-3">
            <label for="waktu" class="form-label">Waktu</label>
            <input type="text" class="form-control" id="waktu" name="waktu" placeholder="Masukkan Waktu">
        </div>

        <!-- Status -->
        <div class="mb-3">
            <label for="status" class="form-label">Status</label>
            <select class="form-select" id="status" name="status">
                <option value="tidakaktif">Nonaktif</option>
                <option value="aktif">Aktif</option>
            </select>
        </div>

        <!-- Acak -->
        <div class="mb-3">
            <label for="acak" class="form-label">Acak</label>
            <select class="form-select" id="acak" name="acak">
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
            </select>
        </div>

        <!-- Tampil Jawaban -->
        <div class="mb-3">
            <label for="tampil_jawaban" class="form-label">Tampil Jawaban</label>
            <select class="form-select" id="tampil_jawaban" name="tampil_jawaban">
                <option value="ya">Ya</option>
                <option value="tidak">Tidak</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="responden" class="form-label">Untuk Anggota ?</label>
            <select class="form-select" id="responden" name="responden">
                <option value="ya">Ya</option>
                <option value="bukan">Tidak</option>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Tambah Data</button>
    </form>
    <!-- Akhir Formulir Tambah -->

</div>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $nama_kuis = $_POST['nama_kuis'];
    $nama_karyawan = $_POST['nama_karyawan'];
    $tgl_kuis = $_POST['tgl_kuis'];
    $waktu = $_POST['waktu'];
    $status = $_POST['status'];
    $acak = $_POST['acak'];
    $tampil_jawaban = $_POST['tampil_jawaban'];
    $responden = $_POST['responden'];

    // Insert data ke dalam database
    $query = "INSERT INTO kuis (nama_kuis, nama_karyawan, tgl_kuis, waktu, status, acak, tampil_jawaban,anggota)
              VALUES (:nama_kuis, :nama_karyawan, :tgl_kuis, :waktu, :status, :acak, :tampil_jawaban,:responden)";

    $stmt = $pdo->prepare($query);

    $stmt->bindParam(':nama_kuis', $nama_kuis);
    $stmt->bindParam(':nama_karyawan', $nama_karyawan);
    $stmt->bindParam(':tgl_kuis', $tgl_kuis);
    $stmt->bindParam(':waktu', $waktu);
    $stmt->bindParam(':status', $status);
    $stmt->bindParam(':acak', $acak);
    $stmt->bindParam(':tampil_jawaban', $tampil_jawaban);
    $stmt->bindParam(':responden', $responden);

    try {
        $stmt->execute();
        alert("berhasil dibuat anda akan diarahkan ke halaman tambah soal");
        pindah($url . "index.php?menu=quiz");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}