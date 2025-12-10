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
                <th>Gambar</th>
                <th>Pilihan</th>
                <!-- <th>Jawaban</th> -->
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
                        if ($row['url_gambar'] != "") {
                            $gambar = cekGambarSoal($url_api, $row['id_soal'], 'soal_bank');
                        ?>
                    <img src="<?php echo $gambar['url_gambar']; ?>" alt="<?php echo $row['url_gambar']; ?>"
                        style="width: 150px;" class="img ">
                    <a href="javascript:klikGambar('<?php echo $row['id_soal']; ?>','soal_bank')"
                        class="btn btn-link view-image" data-id="<?php echo $row['id_soal']; ?>"> lihat Gambar</a>
                    <?php
                        }

                        ?>

                </td>
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
                    Jawaban : <?php echo strtoupper($row['jawaban']); ?>
                </td>
                <!-- <td><?php echo strtoupper($row['jawaban']); ?></td> -->
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
<!-- Modal -->
<div class="modal fade" id="gambarModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="staticBackdropLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Gambar Soal : </h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="loader"></div>
                <div class="isi"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <!-- <button type="button" class="btn btn-primary">Understood</button> -->
            </div>
        </div>
    </div>
</div>

<script>

</script>