<h2>Users Control Regional <?= $regional ?></h2>
<a href="<?= menu_progress("users/tambahuser") ?> " class="btn btn-danger "><i class="fa fa-plus"></i> User</a>

<hr><?php
    try {



        $stmt = $pdo->query("SELECT users.*,cabang.nama_cabang FROM users left join cabang on cabang.id_cabang=users.id_cabang where cabang.regional='$regional'");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($users) > 0) {
    ?>
        <table id='example' class="table table-bordered">
            <thead>
                <tr>
                    <th>NO</th>
                    <th>Username</th>
                    <th>NIK</th>
                    <th>Reg.</th>
                    <th>Cabang</th>
                    <th>Jabatan</th>
                    <th>Nama</th>
                    <th>Email</th>
                    <th>Action</th>
                    <th>Act</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 1;
                foreach ($users as $user) {

                    if ($user['jenis_akun'] != 'superuser') {
                ?>
                        <tr>
                            <td><?= $no++ ?> </td>
                            <td><?= $user['username'] ?> </td>
                            <td><?= $user['nik'] ?> </td>
                            <td>Reg. <?= $user['regional'] ?> </td>
                            <td><?= $user['nama_cabang'] ?> </td>
                            <td><?= $user['jabatan'] ?> </td>
                            <td><?= $user['nama'] ?> </td>
                            <td><?= $user['email'] ?> </td>
                            <td>
                                <a href="<?= menu_progress("users/resetpass&id=$user[id]") ?>" class="btn btn-sm btn-warning btn-sm "><i class="fa fa-refresh"></i> Reset
                                    Pass</a>
                            </td>
                            <td>
                                <a href="<?= menu_progress("users/edituser&id=$user[id]") ?>" class="btn btn-warning btn-sm text-white"><i class="fa fa-pencil"></i></a>
                                <?php if ($user['jenis_akun'] != 'superuser') {
                                ?>
                                    <a href="<?= menu_progress("users/hapususer&id=$user[id]") ?>" class="btn btn-danger btn-sm text-white" onclick="return window.confirm('Apakah yakin untuk menghapus?')"><i class="fa fa-times"></i></a>
                                <?php
                                } ?>
                            </td>
                        </tr>
                <?php
                    }
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
