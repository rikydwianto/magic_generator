<?php
// Fungsi untuk menghasilkan dua angka acak antara 1 dan 10
function generateRandomNumbers()
{
    $angka1 = rand(1, 10);
    $angka2 = rand(1, 10);
    return array($angka1, $angka2);
}

// Panggil fungsi untuk mendapatkan dua angka acak
$angkaAcak = generateRandomNumbers();
?>

<h2>Silakan jawab pertanyaan keamanan berikut sebelum masuk:</h2>
<form method="post">
    <div class="col-md-3">

        <label for="jawaban">Berapa <?php echo $angkaAcak[0]; ?> + <?php echo $angkaAcak[1]; ?>?</label>
        <input type="text" class='form-control' id="jawaban" name="jawaban" required>
        <input type="hidden" name="angka1" value="<?php echo $angkaAcak[0]; ?>">
        <input type="hidden" name="angka2" value="<?php echo $angkaAcak[1]; ?>">
        <button type="submit" class='btn btn-danger'>Login</button>
    </div>
</form>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nilai jawaban dari formulir
    $jawaban = $_POST["jawaban"];

    // Ambil nilai angka-angka dari formulir
    $angka1 = $_POST["angka1"];
    $angka2 = $_POST["angka2"];

    // Hitung jawaban yang seharusnya
    $jawabanSeharusnya = $angka1 + $angka2;

    // Periksa apakah jawaban benar
    if ($jawaban == $jawabanSeharusnya) {
        echo "<h2>Selamat! Jawaban Anda benar. Anda dapat melanjutkan ke halaman login.</h2>";
        $session = time() . "-" . rand(111, 999);
        $session = base64_encode($session);

        $_SESSION['sesi'] = $session;
        pindah("$url");
    } else {
        echo "<h2>Maaf, jawaban Anda salah. Silakan coba lagi.</h2>";
    }
}
?>