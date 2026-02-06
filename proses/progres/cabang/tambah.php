<h2 class="mb-4">Form Tambah Cabang</h2>
<form action="" method="post">
    <div class="form-group">
        <label for="kode_cabang">Kode Cabang:</label>
        <input type="text" class="form-control" id="kode_cabang" name="kode_cabang" required>
    </div>

    <div class="form-group">
        <label for="nama_cabang">Nama Cabang:</label>
        <input type="text" class="form-control" id="nama_cabang" name="nama_cabang" required>
    </div>

    <div class="form-group">
        <label for="regional">REGIONAL:</label>
        <input type="text" readonly class="form-control" id="regional" value='<?= $regional ?>' name="regional" required>
        <!-- <select class="form-control" id="regional" name="regional">
            <option value="">Pilih Regional</option>
            <?php
            $sel_reg = "";
            for ($letter = 'A'; $letter < 'Z'; $letter++) {
                if ($letter == $regional) $sel_reg = "selected";
                else $sel_reg = "";
                echo '<option ' . $sel_reg . ' value="' . $letter . '">Regional ' . $letter . '</option>';
            }
            ?>
        </select> -->
    </div>

    <div class="form-group">
        <label for="wilayah">Wilayah:</label>
        <select class="form-control" id="wilayah" name="wilayah" required>
            <?php

            for ($i = 1; $i <= 5; $i++) {
                echo '<option value="' . $i . '">Wilayah ' . $i . '</option>';
            }
            ?>
        </select>
    </div>


    <button type="submit" class="btn btn-primary">Tambah Cabang</button>
</form>

<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $kode_cabang = $_POST["kode_cabang"];
    $nama_cabang = $_POST["nama_cabang"];
    $regional = $_POST["regional"];
    $wilayah = $_POST["wilayah"];



    try {


        $query = "INSERT INTO cabang (kode_cabang, nama_cabang, regional, wilayah) VALUES (?, ?, ?, ?)";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kode_cabang, $nama_cabang, $regional, $wilayah]);

        echo "Cabang berhasil ditambahkan.";
        pindah(menu_progress("cabang/index"));
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

?>