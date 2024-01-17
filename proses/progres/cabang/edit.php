<h2 class="mb-4">Form Edit Cabang</h2>
<?php
try {


    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $id_cabang = $_POST['id_cabang'];
        $kode_cabang = $_POST['kode_cabang'];
        $nama_cabang = $_POST['nama_cabang'];
        $regional = $_POST['regional'];
        $wilayah = $_POST['wilayah'];


        $query = "UPDATE cabang SET kode_cabang = ?, nama_cabang = ?, regional = ?, wilayah = ? WHERE id_cabang = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$kode_cabang, $nama_cabang, $regional, $wilayah, $id_cabang]);

        echo "Data Cabang berhasil diperbarui.";
        pindah(menu_progress("cabang/index"));
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


try {
    if (isset($_GET['id'])) {
        $id_cabang = $_GET['id'];


        $query = "SELECT * FROM cabang WHERE id_cabang = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$id_cabang]);


        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
?>
            <form action="" method="post">
                <input type="hidden" name="id_cabang" value="<?php echo $row['id_cabang']; ?>">

                <div class="form-group">
                    <label for="kode_cabang">Kode Cabang:</label>
                    <input type="text" class="form-control" id="kode_cabang" name="kode_cabang" value="<?php echo $row['kode_cabang']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="nama_cabang">Nama Cabang:</label>
                    <input type="text" class="form-control" id="nama_cabang" name="nama_cabang" value="<?php echo $row['nama_cabang']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="regional">REGIONAL:</label>
                    <select class="form-control" id="regional" name="regional">
                        <option value="">Pilih Regional</option>
                        <?php
                        $sel_reg = "";
                        for ($letter = 'A'; $letter < 'Z'; $letter++) {
                            if ($letter == $row['regional']) $sel_reg = "selected";
                            else $sel_reg = "";
                            echo '<option ' . $sel_reg . ' value="' . $letter . '">Regional ' . $letter . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="wilayah">Wilayah:</label>
                    <select class="form-control" id="wilayah" name="wilayah" required>
                        <?php

                        for ($i = 1; $i <= 5; $i++) {
                            $sel = ($i == $row['wilayah'] ? "selected" : "");
                            echo '<option ' . $sel . ' value="' . $i . '">Wilayah ' . $i . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
<?php
        } else {
            echo "Data Cabang tidak ditemukan.";
        }
    } else {
        echo "ID Cabang tidak diberikan.";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}


$pdo = null;
?>