<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch()
?>
<h2>Duplikat Kuis dan Soal </h2>
<form action="" method="post">

    <div class="mb-3">
        <label for="nama_kuis_baru" class="form-label">Nama Kuis sebelumnya</label>
        <input type="text" value='<?= $kuis['nama_kuis'] ?>' disabled class="form-control" id="nama_kuis_lama"
            name="nama_kuis_lama" placeholder="Masukkan Nama Kuis Lama" required>
    </div>
    <div class="mb-3">
        <label for="nama_kuis_baru" class="form-label">Nama Kuis Baru</label>
        <input type="text" value='<?= $kuis['nama_kuis'] ?>' class="form-control" id="nama_kuis_baru"
            name="nama_kuis_baru" placeholder="Masukkan Nama Kuis Baru" required>
    </div>
    <button type="submit" class="btn btn-primary">Salin Kuis dan Soal-soal</button>

</form>

<?php
// Include koneksi ke database (gantilah dengan koneksi sesuai kebutuhan Anda)

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari formulir
    $id_kuis_asal = $id_kuis;
    $nama_kuis_baru = $_POST['nama_kuis_baru'];

    // Salin data kuis
    $query_copy_kuis = "INSERT INTO kuis (nama_kuis, nama_karyawan, tgl_kuis, waktu, status, acak, tampil_jawaban)
                        SELECT :nama_kuis_baru, nama_karyawan, tgl_kuis, waktu, status, acak, tampil_jawaban
                        FROM kuis WHERE id_kuis = :id_kuis_asal";

    $stmt_copy_kuis = $pdo->prepare($query_copy_kuis);
    $stmt_copy_kuis->bindParam(':nama_kuis_baru', $nama_kuis_baru);
    $stmt_copy_kuis->bindParam(':id_kuis_asal', $id_kuis_asal);

    try {
        $stmt_copy_kuis->execute();
        $id_kuis_baru = $pdo->lastInsertId(); // Dapatkan ID kuis baru

        // Salin data soal
        $query_copy_soal = "INSERT INTO soal (id_kuis, soal, pilihan, jawaban,id_bank_soal,url_gambar)
                            SELECT :id_kuis_baru, soal, pilihan, jawaban,id_soal,url_gambar
                            FROM soal WHERE id_kuis = :id_kuis_asal";

        $stmt_copy_soal = $pdo->prepare($query_copy_soal);
        $stmt_copy_soal->bindParam(':id_kuis_baru', $id_kuis_baru);
        $stmt_copy_soal->bindParam(':id_kuis_asal', $id_kuis_asal);

        $stmt_copy_soal->execute();

        echo "Kuis dan soal-soalnya berhasil disalin.";
        pindah($url . "index.php?menu=index&act=quiz");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}