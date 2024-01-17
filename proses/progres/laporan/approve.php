<h2>Persetujuan Laporan Staff</h2>
<hr>


<?php
try {
    // Query untuk mengambil data dari tabel capaian_staff
    $query = "SELECT * FROM capaian_staff cs join detail_capaian_staff dc on cs.id_capaian_staff=dc.id_capaian_staff where cabang_staff='$detailAkun[nama_cabang]' and status='konfirmasi' order by tahun,bulan,minggu desc";
    $stmt = $pdo->prepare($query);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Menampilkan data dalam bentuk tabel HTML
?>
<table class='table table-rensponsive' id='tabelCapaianStaff'>
    <tr>
        <th>NO</th>
        <th>NIK</th>
        <th>Nama</th>
        <th>Cabang</th>
        <th>Regional</th>
        <th>Minggu ke-</th>
        <th>Priode</th>
        <th>Keterangan</th>
        <th>created at</th>
        <th>#</th>
    </tr>
    <?php
        $no = 1;
        foreach ($result as $row) {
        ?>
    <tr>
        <td><?= $no ?></td>
        <td><?= $row['nik_staff'] ?></td>
        <td><?= $row['nama_staff'] ?></td>
        <td><?= $row['cabang_staff'] ?></td>
        <td>Reg.<?= $row['regional'] ?>/<?= $row['wilayah'] ?></td>
        <td><?= $row['minggu'] ?></td>
        <td><?= $bulanArray[$row['bulan']] ?> - <?= $row['tahun'] ?></td>
        <td><?= $row['keterangan'] ?></td>
        <td><?= $row['created_at'] ?></td>

        <td>
            <a href="<?= menu_progress("laporan/cek_laporan&id=$row[id_capaian_staff]") ?>"
                class="btn btn-success  text-white"><i class="fa fa-check"></i></a>



        </td>
    </tr>
    <?php
            $no++;
        }

        echo "</table>";
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }

    // Tutup koneksi ke database
    $pdo = null;
    ?>