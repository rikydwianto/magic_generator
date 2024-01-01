<?php
try {
    // Create a PDO pdoection

    // Select all users from the database
    $stmt = $pdo->query("SELECT * FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($users) > 0) {
?>
        <table id='example' class="table table-bordered">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Username</th>
                    <th>NIK</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Jenis Akun</th>
                    <th>Password</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($users as $user) {
                ?>
                    <tr>
                        <td><?= $no++ ?> </td>
                        <td><?= $user['username'] ?> </td>
                        <td><?= $user['nik'] ?> </td>
                        <td><?= $user['nama'] ?> </td>
                        <td><?= $user['email'] ?> </td>
                        <td><?= $user['jenis_akun'] ?> </td>
                        <td>
                            <a href="<?= $url . "index.php?menu=index&act=users&submenu=resetpass&id=$user[id]" ?>" class="btn btn-warning btn-sm text-white"><i class="fa fa-refresh"></i> Reset Pass</a>
                        </td>
                        <td>
                            <a href="<?= $url . "index.php?menu=index&act=users&submenu=edituser&id=$user[id]" ?>" class="btn btn-warning btn-sm text-white"><i class="fa fa-pencil"></i></a>
                            <a href="<?= $url . "index.php?menu=index&act=users&submenu=hapususer&id=$user[id]" ?>" class="btn btn-danger btn-sm text-white" onclick="return window.confirm('Apakah yakin untuk menghapus?')"><i class="fa fa-times"></i></a>
                        </td>
                    </tr>
                <?php
                }

                ?>
            </tbody>
        </table>
<?php
    } else {
        echo '<p>No users found.</p>';
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
