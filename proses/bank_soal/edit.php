<div class="container mt-5">
    <h2 class="mb-4">Form Edit Soal</h2>


    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['kirim'])) {
        $url_gambar = '';
        $folder = './assets/img/soal/';
        if (isset($_FILES['uploaded_image']) && $_FILES['uploaded_image'] != null) {
            $allowed_extensions = array("jpg", "jpeg", "png", "gif");
            $file_extension = strtolower(pathinfo($_FILES["uploaded_image"]["name"], PATHINFO_EXTENSION));

            if (!in_array($file_extension, $allowed_extensions)) {
                // echo "Hanya file gambar dengan ekstensi JPG, JPEG, PNG, atau GIF yang diizinkan.";
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

                $nama_asli = $_POST['nama_file_asli'];
                if ($nama_asli != "") {
                    $target = $folder . $nama_asli;
                    // echo $target;
                    // error_reporting(0);
                    unlink($target);
                }
                $url_gambar = $nama_file;
            }
        } else {

            $nama_asli = $_POST['nama_file_asli'];
            if ($nama_asli != "") {
                $target = $folder . $nama_asli;
                // echo $target;
                // error_reporting(0);
                unlink($target);
            }
            $url_gambar = $_POST['url_image'];
        }

        if ($_POST['gambar_option'] == 'tidak_ada') {
            $url_gambar = '';
            //proses hapus gambar
            $nama_asli = $_POST['nama_file_asli'];
            if ($nama_asli != "") {
                $target = $folder . $nama_asli;
                // echo $target;
                // error_reporting(0);
                unlink($target);
            }
        }
        // echo 'isi url baru : ' . $url_gambar;
        $soalId = $_POST['soal_id'];
        $soal = $_POST['soal'];
        $pilihan = $_POST['pilihan'];
        $jawaban = $_POST['jawaban'];
        $kategori = $_POST['kategori'];
        $sub_kategori = $_POST['sub_kategori'];

        // Validasi pilihan tidak boleh kosong

        // Mengonversi pilihan ke format JSON
        $pilihan_array = array();
        foreach ($pilihan as $key => $value) {
            $id = chr(65 + $key); // Mengubah angka menjadi huruf (A, B, C, ...)
            $pilihan_array[] = array('id' => $id, 'teks' => $value);
        }
        $pilihan_json = json_encode($pilihan_array);

        try {
            $query = "UPDATE soal_bank SET soal = ?, pilihan = ?, jawaban = ?, kategori = ?, sub_kategori = ?,url_gambar=? WHERE id_soal = ?";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $soal);
            $stmt->bindParam(2, $pilihan_json);
            $stmt->bindParam(3, $jawaban);
            $stmt->bindParam(4, $kategori);
            $stmt->bindParam(5, $sub_kategori);
            $stmt->bindParam(6, $url_gambar);
            $stmt->bindParam(7, $soalId);

            $stmt->execute();

            alert("berhasil disimpan");
            pindah($url . "index.php?menu=index&act=bank_soal");
        } catch (PDOException $e) {
            // Display detailed error information
            echo "Error: " . $e->getMessage();

            // Log the error to a file (you can customize the file path)
            error_log("Error: " . $e->getMessage(), 3, "error_log.txt");
        }
    }

    // $pdo = null;




    if (isset($_GET['id_soal'])) {
        $soalId = $_GET['id_soal'];

        try {
            $query = "SELECT * FROM soal_bank WHERE id_soal = ?";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $soalId);
            $stmt->execute();

            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result) {
                $soal = $result['soal'];
                $pilihan = json_decode($result['pilihan'], true);
                $jawaban = $result['jawaban'];
                $kategori = $result['kategori'];
                $sub_kategori = $result['sub_kategori'];
                $url_gambar = $result['url_gambar'];
            } else {
                echo "Soal tidak ditemukan.";
                exit();
            }
        } catch (PDOException $e) {
            die("Error: " . $e->getMessage());
        }
    } else {
        echo "Invalid Request.";
    }
    ?>

    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="soal_id" value="<?php echo $soalId; ?>">

        <div class="form-group">
            <label for="soal">Soal:</label>
            <textarea class="form-control" id="soal" name="soal" rows="3" required><?php echo $soal; ?></textarea>
        </div>
        <div class="form-group">
            <div class="form-check">
                <!-- TIDAK ADA GAMBAR -->
                <input type="radio" class="form-check-input" id='tidak_ada' name="gambar_option" value="tidak_ada" <?= $url_gambar == null ? 'checked' : '' ?>>
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
                <input type="radio" class="form-check-input" id='url' name="gambar_option" <?= $url_gambar != null ? 'checked' : '' ?> value="url">
                <label class="form-check-label" for='url'>URL Gambar</label>
            </div>
            <div id="url_gambar" style="display:<?= $url_gambar != null ? 'block' : 'none' ?>;">
                <?php
                $gambar = cekGambarSoal($url_api, $soalId, 'soal_bank');
                ?>
                <input type="url" onchange="cekUrl()" id='url_input' value="<?= $gambar['url_gambar'] ?>" pattern="^(https?|ftp):\/\/[^\s/$.?#].[^\s]*$" class="form-control mt-2" placeholder="https://fastly.picsum.photos/id/984/500/300.jpg?hmac=a7aNUQmvchekrZHYMXkYTUTwhuY382Bm6KlnxZQW-lY" name="url_image">

                nama file asli
                <input type="text" readonly class='form-control' value="<?= $url_gambar ?>" name="nama_file_asli">

            </div>
            <div id="previewImage">
                <?php
                if ($url_gambar != null) {

                ?>
                    <img src="<?= $gambar['url_gambar'] ?>" class="img img-fluid" alt="">
                <?php
                }
                ?>
            </div>
        </div>


        <div class="form-group">
            <label for="pilihan">Pilihan:</label>
            <div id="pilihan-container">
                <?php foreach ($pilihan as $key => $choice) : ?>
                    <div class="input-group mb-2">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><?php echo $choice['id']; ?></span>
                        </div>
                        <input type="text" class="form-control pilihan-input" name="pilihan[]" value="<?php echo $choice['teks']; ?>" required>
                        <button class="btn btn-danger hapus-pilihan" type="button">-</button>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <button type="button" class="btn btn-success tambah-pilihan">Tambah Pilihan</button>

        <div class="form-group">
            <label for="jawaban">Jawaban (Huruf):</label>
            <input type="text" class="form-control" id="jawaban" name="jawaban" value="<?php echo $jawaban; ?>" required>
        </div>

        <div class="form-group">
            <label for="kategori">Kategori:</label>
            <input type="text" class="form-control" id="kategori" name="kategori" value="<?php echo $kategori; ?>" required>
        </div>

        <div class="form-group">
            <label for="sub_kategori">Sub Kategori:</label>
            <input type="text" class="form-control" id="sub_kategori" name="sub_kategori" value="<?php echo $sub_kategori; ?>" required>
        </div>

        <button type="submit" name='kirim' class="btn btn-primary">Simpan Perubahan!</button>
    </form>

</div>


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
            $("#previewImage").show();
        } else if (selectedOption === 'url') {
            $("#previewImage").show();
            urlGambarDiv.style.display = 'block';
        } else {
            $("#previewImage").html('jika terdapat gambar, akan terhapus ketika memilih ini!');

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