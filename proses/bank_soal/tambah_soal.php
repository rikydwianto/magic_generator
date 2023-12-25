<h2 class="mb-4">Form Tambah Soal</h2>

<form action="" method="POST" id="myForm">
    <label for="soal">Soal:</label>
    <!-- <div class="form-group" id="editor" style="height:200px;">
    </div> -->
    <textarea class="form-control" id="soal" name="soal" rows="3"></textarea>

    <div class="form-group">
        <label for="pilihan">Pilihan:</label>
        <div class="form-group" id="pilihan-container">
            <!-- Contoh satu baris input -->
            <div class="input-group mb-2">
                <div class="input-group-prepend">
                    <span class="input-group-text">A</span>
                </div>
                <input type="text" class="form-control" name="pilihan[]" placeholder="Teks Pilihan" required>
            </div>
        </div>
    </div>
    <button type="button" class="btn btn-success tambah-pilihan">Tambah Pilihan</button>



    <div class="form-group">
        <label for="jawaban">Jawaban:</label>
        <input type="text" class="form-control" id="jawaban" maxlength="1" name="jawaban" required>
    </div>

    <div class="form-group">
        <label for="kategori">Kategori:</label>
        <input type="text" class="form-control" id="kategori" name="kategori" required>
    </div>

    <div class="form-group">
        <label for="sub_kategori">Sub Kategori:</label>
        <input type="text" class="form-control" id="sub_kategori" name="sub_kategori" required>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $soal = $_POST['soal'];
    $regex = '/<([a-zA-Z]+)[^>]*>/';

    // Temukan tag pertama
    $htmlString = $soal;
    if (preg_match($regex, $htmlString, $matches)) {
        $tagName = $matches[1];
        $styledHtml = preg_replace($regex, '<' . $tagName . ' style="font-size: 1.5rem;">', $htmlString, 1);
        $soal = $styledHtml;
    } else {
        $soal = $soal;
    }
    $soal = $soal;

    $pilihan = $_POST['pilihan'];
    $jawaban = strtoupper($_POST['jawaban']);
    $kategori = $_POST['kategori'];
    $sub_kategori = $_POST['sub_kategori'];

    // Mengonversi pilihan ke format JSON
    $pilihan_array = array();
    foreach ($pilihan as $key => $value) {
        $id = chr(65 + $key); // Mengubah angka menjadi huruf (A, B, C, ...)
        $pilihan_array[] = array('id' => $id, 'teks' => $value);
    }
    $pilihan_json = json_encode($pilihan_array);

    try {
        $query = "INSERT INTO soal_bank (soal, pilihan, jawaban, kategori, sub_kategori) VALUES (?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $soal);
        $stmt->bindParam(2, $pilihan_json);
        $stmt->bindParam(3, $jawaban);
        $stmt->bindParam(4, $kategori);
        $stmt->bindParam(5, $sub_kategori);

        $stmt->execute();

        echo "Soal berhasil ditambahkan.";

        // Tampilkan konfirmasi menggunakan JavaScript
?>
        <script>
            var tambahSoal = confirm("Ingin menambah soal lagi?");
            if (tambahSoal) {
                window.location.href =
                    "<?= $url ?>index.php?menu=quiz&act=soal_bank&submenu=tambah_soal"; // Ganti dengan halaman tambah soal
            } else {
                window.location.href = '<?= $url ?>index.php?menu=quiz&act=soal_bank'; // Ganti dengan halaman soal
            }
        </script>
<?php
        // pindah($url . "index.php?menu=quiz&act=soal_bank");
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

?>