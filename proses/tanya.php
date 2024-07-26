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
<div class="container-fluid">
    <h2>Silakan jawab pertanyaan keamanan berikut sebelum masuk:</h2>
    <div class="row">
        <div style="display: none;">
            <form method="post">
                <div class="col-md-3">

                    <label for="jawaban">Berapa <?php echo $angkaAcak[0]; ?> <b>DIBAGI</b> (1/2)
                        <!-- <?php echo $angkaAcak[1]; ?> --> ?
                    </label>
                    <input type="text" class='form-control' id="jawaban" name="jawaban" required>
                    <input type="hidden" name="angka1" value="<?php echo $angkaAcak[0]; ?>">
                    <input type="hidden" name="angka2" value="<?php echo $angkaAcak[1]; ?>">
                    <button type="submit" class='btn btn-danger mt-2'>Login</button>
                </div>
            </form>
        </div>

        <div class="col-md-8">
            <form action="" method="post">
                <?php
                $query = $pdo->query("select * from soal_bank order by RAND() limit 1");
                $query->execute();
                $kuis = $query->fetch(PDO::FETCH_ASSOC);
                $json = json_decode($kuis['pilihan'], true);
                ?>

                <h3>1. <?= $kuis['soal'] ?></h3>
                <input type="hidden" name="id" value='<?= $kuis['id_soal'] ?>'>
                <?php
                foreach ($json as $soal) {
                ?>
                <li class="list-group-item" for="pilihan_<?= $soal['id'] ?>">
                    <input class="form-check-input me-1" type="radio" name="pilihan[]" value="<?= $soal['id'] ?>"
                        id="pilihan_<?= $soal['id'] ?>">
                    <label class="form-check-label" style="font-size: x-large;" for="pilihan_<?= $soal['id'] ?>"
                        name='pilihan_<?= $soal['id'] ?>'>
                        <?= $soal['id'] ?>. <?= $soal['teks'] ?></label>
                </li>

                <?php
                }
                ?>

                <button type="submit" class="btn btn-danger btn-lg" name='jawab'>JAWAB</button>
                <br>
                <?php
                if (isset($_POST['jawab'])) {
                    $id = $_POST['id'];
                    $jawaban  = $_POST['pilihan'][0];
                    $cek = $pdo->prepare("select * from soal_bank where id_soal=?");
                    $cek->execute([$id]);
                    $cek = $cek->fetch(PDO::FETCH_ASSOC);
                    if ($cek['jawaban'] == $jawaban) {

                        echo "<h2>Selamat! Jawaban Anda benar. Anda dapat melanjutkan ke halaman login.</h2>";
                        $session = time() . "-" . rand(111, 999);
                        $session = base64_encode($session);

                        $_SESSION['sesi'] = $session;
                        pindah("$url");
                    } else {
                        echo "jawaban salah silahkan jawab lagi";
                    }
                }
                ?>



            </form>
        </div>

        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            exit;
            // Ambil nilai jawaban dari formulir
            $jawaban = $_POST["jawaban"];

            // Ambil nilai angka-angka dari formulir
            $angka1 = $_POST["angka1"];
            $angka2 = $_POST["angka2"];

            // Hitung jawaban yang seharusnya
            $jawabanSeharusnya = $angka1 / (0.5);

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
    </div>
</div>