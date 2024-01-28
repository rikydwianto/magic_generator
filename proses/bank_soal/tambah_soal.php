<h2 class="mb-4">Form Tambah Soal</h2>

<form method="POST" enctype="multipart/form-data" id="myForm">
    <label for="soal">Soal:</label>
    <!-- <div class="form-group" id="editor" style="height:200px;">
    </div> -->
    <textarea class="form-control" required id="soal" name="soal" rows="3"></textarea>

    <div class="form-group">
        <div class="form-check">
            <!-- TIDAK ADA GAMBAR -->
            <input type="radio" class="form-check-input" id='tidak_ada' name="gambar_option" value="tidak_ada" checked>
            <label class="form-check-label" for="tidak_ada">Tidak Ada Gambar</label>
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <!-- CEKLIST UPLOAD GAMBAR -->
            <input type="radio" class="form-check-input" id='upload' name="gambar_option" value="upload">
            <label class="form-check-label" for="upload">Upload Gambar</label>
        </div>
        <div id="upload_gambar" style="display:none;">
            <input type="file" class="form-control form-control-file mt-2" accept="image/*" name="uploaded_image">
        </div>
    </div>
    <div class="form-group">
        <div class="form-check">
            <input type="radio" class="form-check-input" id='url' name="gambar_option" value="url">
            <label class="form-check-label" for='url'>URL Gambar</label>
        </div>
        <div id="url_gambar" style="display:none;">
            <input type="url" onchange="cekUrl()" id='url_input' pattern="^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$"
                class="form-control mt-2"
                placeholder="https://fastly.picsum.photos/id/984/500/300.jpg?hmac=a7aNUQmvchekrZHYMXkYTUTwhuY382Bm6KlnxZQW-lY"
                name="url_image">

            <div id="previewImage"></div>
        </div>
    </div>


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

    <button type="submit" name='simpan' class="btn btn-primary">Submit</button>
</form>

<?php
if (isset($_POST['simpan'])) {
    $url_gambar = '';
    if (isset($_FILES['uploaded_image']) && $_FILES['uploaded_image'] != null) {
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        $file_extension = strtolower(pathinfo($_FILES["uploaded_image"]["name"], PATHINFO_EXTENSION));

        if (!in_array($file_extension, $allowed_extensions)) {
            echo "Hanya file gambar dengan ekstensi JPG, JPEG, PNG, atau GIF yang diizinkan.";
            $url_gambar = $_POST['url_image'];
        } else {
            // Tentukan lokasi folder untuk menyimpan gambar
            $target_folder = "./assets/img/soal/";

            // Tentukan nama file yang disimpan (gunakan timestamp unik untuk menghindari duplikasi)

            $nama_file = time() . "_" . basename($_FILES["uploaded_image"]["name"]);
            $target_file = $target_folder . $nama_file;

            // Pindahkan file ke folder yang ditentukan
            if (move_uploaded_file($_FILES["uploaded_image"]["tmp_name"], $target_file)) {
                // echo "Gambar berhasil diunggah dan disimpan di " . $target_file;
            } else {
                // echo "Terjadi kesalahan saat mengunggah gambar.";
            }
            $url_gambar = $nama_file;
        }
    } else {
        $url_gambar = $_POST['url_image'];
    }
    // echo $url_gambar;
    // var_dump($_FILES);
    $soal = $_POST['soal'];

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
        $query = "INSERT INTO soal_bank (soal, pilihan, jawaban, kategori, sub_kategori,url_gambar) VALUES (?, ?, ?, ?, ?,?)";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(1, $soal);
        $stmt->bindParam(2, $pilihan_json);
        $stmt->bindParam(3, $jawaban);
        $stmt->bindParam(4, $kategori);
        $stmt->bindParam(5, $sub_kategori);
        $stmt->bindParam(6, $url_gambar);

        $stmt->execute();

        echo "Soal berhasil ditambahkan.";
?>
<script>
var tambahSoal = confirm("Ingin menambah soal lagi?");
if (tambahSoal) {
    window.location.href =
        "<?= $url . 'index.php?menu=index&act=bank_soal&submenu=tambah_soal' ?>"; // Ganti dengan halaman tambah soal
} else {
    window.location.href =
        "<?= $url . 'index.php?menu=index&act=bank_soal' ?>"; // Ganti dengan halaman soal
}
</script>
<?php

    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

?>

<script>
document.querySelectorAll('input[name="gambar_option"]').forEach(function(radio) {
    radio.addEventListener('change', function() {
        toggleOptions(this.value);
    });
});

// Fungsi untuk menampilkan/menyembunyikan elemen berdasarkan opsi yang dipilih
function toggleOptions(selectedOption) {
    var uploadGambarDiv = document.getElementById('upload_gambar');
    var urlGambarDiv = document.getElementById('url_gambar');
    var url_input = $('#url_input');


    // Menyembunyikan semua elemen terlebih dahulu
    uploadGambarDiv.style.display = 'none';
    urlGambarDiv.style.display = 'none';

    // Menampilkan elemen sesuai dengan opsi yang dipilih
    if (selectedOption === 'upload') {
        uploadGambarDiv.style.display = 'block';
    } else if (selectedOption === 'url') {
        urlGambarDiv.style.display = 'block';
    } else {
        url_input.attr('required', '')
    }
}

function cekUrl() {
    var urlInput = document.getElementById('url_input');
    var url = urlInput.value;

    // Mengecek apakah URL valid menggunakan regex
    var regex = /^(https|http?|ftp):\/\/[^\s/$.?#].[^\s]*$/;

    // Membuat elemen gambar untuk preview
    var imgElement = document.createElement('img');
    imgElement.src = url;
    imgElement.alt = 'Preview Image';
    imgElement.style.maxWidth = '100%';

    // Menampilkan gambar pada div dengan id 'previewImage'
    var previewDiv = document.getElementById('previewImage');
    previewDiv.innerHTML = '';
    previewDiv.appendChild(imgElement);
}
</script>