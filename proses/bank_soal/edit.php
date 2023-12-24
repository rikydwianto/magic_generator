<div class="container mt-5">
    <h2 class="mb-4">Form Edit Soal</h2>


    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
            $query = "UPDATE soal_bank SET soal = ?, pilihan = ?, jawaban = ?, kategori = ?, sub_kategori = ? WHERE id_soal = ?";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(1, $soal);
            $stmt->bindParam(2, $pilihan_json);
            $stmt->bindParam(3, $jawaban);
            $stmt->bindParam(4, $kategori);
            $stmt->bindParam(5, $sub_kategori);
            $stmt->bindParam(6, $soalId);

            $stmt->execute();

            alert("berhasil disimpan");
            pindah($url . "index.php?menu=quiz&act=soal_bank");
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

    <form method="POST">
        <input type="hidden" name="soal_id" value="<?php echo $soalId; ?>">

        <div class="form-group">
            <label for="soal">Soal:</label>
            <textarea class="form-control" id="soal" name="soal" rows="3" required><?php echo $soal; ?></textarea>
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