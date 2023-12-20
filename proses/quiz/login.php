<h2>Silakan Masukan kode akses</h2>
<form method="post">
    <div class="col-md-3">

        <label for="jawaban">kode akses</label>
        <input type="text" class='form-control' id="jawaban" name="nik" placeholder="NIK" required>
        <input type="text" class='form-control' id="jawaban" name="jawaban" required>
        <button type="submit" class='btn btn-danger'>Login</button>
    </div>
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai jawaban dari formulir
    $jawaban = $_POST["jawaban"];


    // Hitung jawaban yang seharusnya
    $jawabanSeharusnya = "1sampai9";
    $id = $_POST["nik"];

    // Periksa apakah jawaban benar
    if ($jawaban == $jawabanSeharusnya) {
        echo "<h2>Selamat! Jawaban Anda benar. Anda dapat melanjutkan ke halaman login.</h2>";
        // $session = time() . "-" . rand(111, 999);
        // $session = base64_encode($session);

        $_SESSION['idLogin'] = $id;
        header("location:$url" . "index.php?menu=quiz");
    } else {
        echo "<h2>Maaf, jawaban Anda salah. Silakan coba lagi.</h2>";
    }
}
?>