<a href="<?= menu_progress("staff/tambah") ?>" class="btn btn-sm btn-danger">
    <i class="fa fa-plus"> Tambah</i>
</a>

<hr>
<?php
try {

    // Query untuk mendapatkan semua data staff
    $query = "SELECT * FROM staff where cabang='$detailAkun[nama_cabang]'";
    $stmt = $pdo->query($query);
    $staffList = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
} ?>
<div class="">
    <h2>Daftar Staff</h2>
    <table class="table">
        <thead>
            <tr>
                <th scope="col">No</th>
                <th scope="col">NIK Staff</th>
                <th scope="col">Nama Staff</th>
                <th scope="col">Cabang</th>
                <th scope="col">Status</th>
                <th scope="col">ACT</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $no = 1;
            foreach ($staffList as $staff) : ?>

            <tr>
                <th scope="row"><?php echo $no++; ?></th>
                <td><?php echo $staff['nik_staff']; ?></td>
                <td><?php echo $staff['nama_staff']; ?></td>
                <td><?php echo $staff['cabang']; ?></td>
                <td><?php echo $staff['status']; ?></td>
                <td>
                    <a href="<?= menu_progress("staff/reset_pass&id_staff=$staff[id_staff]") ?>"
                        class="btn btn-success text-white ">
                        <i class="fa fa-refresh"></i> Password
                    </a>
                    <a href="<?= menu_progress("staff/edit&id_staff=$staff[id_staff]") ?>"
                        class="btn btn-warning text-white ">
                        <i class="fa fa-gear"></i>
                    </a>
                    <a href="<?= menu_progress("staff/hapus&id_staff=$staff[id_staff]") ?>"
                        onclick="return window.confirm('yakin untuk menghapus ini?')" class="btn btn-danger ">
                        <i class="fa fa-times"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>