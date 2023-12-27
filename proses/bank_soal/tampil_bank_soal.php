<?php
try {
    $query = "SELECT * FROM soal_bank";
    $stmt = $pdo->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>


<div class="container mt-5">
    <h2 class="mb-4">Soal Bank </h2>
    <div class="col-md-4 float-right">
        <div class="mb-3">
            <label for="kategoriFilter" class="form-label">Filter Kategori:</label>
            <select id="kategoriFilter" class="form-select">
                <option value="">Semua</option>
                <?php
                $query = "SELECT DISTINCT kategori FROM soal_bank";
                $stmt = $pdo->query($query);

                // Menampilkan opsi dropdown kategori
                while ($row = $stmt->fetch()) {
                    echo '<option value="' . $row['kategori'] . '">' . $row['kategori'] . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="subkategoriFilter" class="form-label">Filter Sub Kategori:</label>
            <select id="subkategoriFilter" class="form-select">
                <option value="">Semua</option>
                <?php
                $query = "SELECT DISTINCT sub_kategori,kategori FROM soal_bank";
                $stmt = $pdo->query($query);

                // Menampilkan semua opsi subkategori
                while ($row = $stmt->fetch()) {
                    echo '<option  value="' . $row['sub_kategori'] . '">' . $row['sub_kategori'] . '</option>';
                }
                ?>
            </select>
        </div>
    </div>
    <table id="soalTable" class="table table-striped table-bordered" style="width:100%">
        <thead>
            <tr>
                <th>NO</th>
                <th>Soal</th>
                <th>Pilihan</th>
                <th>Jawaban</th>
                <th>Kategori</th>
                <th>Sub Kategori</th>
                <th>act</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($result as $row) : ?>
            <tr>
                <td><?php echo $no ?></td>
                <td><?php echo $row['soal']; ?></td>
                <td>
                    <?php

                        $choices = json_decode($row['pilihan'], true);

                        $output = '';
                        foreach ($choices as $choice) {
                            if ($choice['id'] === $row['jawaban']) {
                        ?>
                    <b><?= strtoupper($choice['id']) ?>. <?= $choice['teks'] ?><br /></b>
                    <?php
                            } else {
                            ?>
                    <?= strtoupper($choice['id']) ?>. <?= $choice['teks'] ?><br />
                    <?php
                            }
                            ?>

                    <?php }

                        ?>
                </td>
                <td><?php echo strtoupper($row['jawaban']); ?></td>
                <td><?php echo $row['kategori']; ?></td>
                <td><?php echo $row['sub_kategori']; ?></td>
                <td>
                    <a href="<?= $url . "index.php?menu=index&act=bank_soal&submenu=del&id_soal=" . $row['id_soal'] ?>"
                        class="btn btn-danger btn-sm"
                        onclick="return window.confirm('Apakah yakin akan menghapus soal ini?')"><i
                            class="fa fa-times"></i></a>

                    <a href="<?= $url . "index.php?menu=index&act=bank_soal&submenu=edit&id_soal=" . $row['id_soal'] ?>"
                        class="btn text-white btn-warning btn-sm"><i class="fa fa-pencil"></i></a>
                </td>
            </tr>
            <?php
                $no++;
            endforeach; ?>
            <!-- Tambahkan baris sesuai dengan data yang Anda miliki -->
        </tbody>
    </table>
</div>