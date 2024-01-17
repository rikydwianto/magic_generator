<h3>Daftar Cabang Regional <?= $regional ?></h3>
<a href="<?= $url . "progress.php?menu=cabang/tambah" ?>" class="btn btn-danger">
    <fa class="fa-plus"> Tambah</fa>
</a>
<hr>
<table class="table" id='table'>
    <thead>
        <tr>
            <th scope="col">NO</th>
            <th scope="col">Kode Cabang</th>
            <th scope="col">Nama Cabang</th>
            <th scope="col">Regional</th>
            <th scope="col">Wilayah</th>
            <th scope="col">ACT</th>
        </tr>
    </thead>
    <tbody>
        <?php
        try {
            $query = "SELECT * FROM cabang where regional='$regional'";
            $result = $pdo->query($query);

            $no = 1;
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        ?>

                <tr>
                    <th scope="row"><?= $no++ ?></th>
                    <td><?= $row['kode_cabang'] ?></td>
                    <td><?= $row['nama_cabang'] ?></td>
                    <td>Regional <?= $row['regional'] ?></td>
                    <td><?= $row['wilayah'] ?></td>
                    <td>
                        <a href="<?= menu_progress("cabang/edit&id=$row[id_cabang]") ?>" class="btn btn-warning">
                            <fa class="fa fa-gear text-white"></fa>
                        </a>
                        <a href="<?= menu_progress("cabang/hapus&id=$row[id_cabang]") ?>" onclick="return window.confirm('Yakin untuk menghapus ini?')" class="btn btn-danger">
                            <fa class="fa fa-times"></fa>
                        </a>
                    </td>
                </tr>
        <?php
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }


        $pdo = null;
        ?>
    </tbody>
</table>