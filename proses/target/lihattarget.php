<?php
$daftarTahun = array(2024, 2025, 2026);

$stmt = $pdo->query("SELECT *,sum(target) as total_target FROM target where id_user='$sesi' group by aktifitas");
$daftarTarget = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (isset($_GET['tahun'])) {
    $tahun = $_GET['tahun'];
} else $tahun = date("Y");
?>
<div class="col-lg-2">

    <form action="" method="get">
        <input type="hidden" name="menu" value='index'>
        <input type="hidden" name="act" value='target'>
        <select class="form-control" id="tahun" name="tahun" required>
            <!-- <option value="">Pilih Tahun</option> -->
            <?php

            foreach ($daftarTahun as $tahun1) {
                if ($tahun1 == $tahun) {
                    echo "<option selected value='$tahun1'>$tahun1</option>";
                } else {
                    echo "<option value='$tahun1'>$tahun1</option>";
                }
            }
            ?>
        </select>
        <button type="submit" class='btn btn-primary'>Pilih</button>
    </form>
</div>
<h3>Rekapan Target <?= $tahun ?></h3>
<table class='table table-bordered'>
    <thead>
        <tr class='bg-dark text-white'>
            <th>NO</th>
            <th>Aktifitas</th>
            <?php

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                if ($bulan == date("m")) {
                    $bg = "bg-danger text-white";
                } else {
                    $bg = "";
                }
                echo "<th class=' text-center $bg'>" . $bulan . "</th>";
            }
            ?>
            <th class='text-center'>Kumulatif</th>
            <th>Act</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->query("SELECT * FROM kegiatan ");
        $daftarTarget = $stmt->fetchAll(PDO::FETCH_ASSOC);
        ?>
        <?php $no = 1;
        foreach ($daftarTarget as $target) : ?>
        <tr>
            <td><?php echo $no++; ?></td>
            <td><?php echo $target['kegiatan']; ?></td>
            <?php
                $total_target = 0;

                for ($bulan = 1; $bulan <= 12; $bulan++) {
                    $stmt = $pdo->query("SELECT sum(target) as total_target,id FROM target where id_user='$sesi' and aktifitas='$target[kegiatan]' and bulan='$bulan'and tahun='$tahun'");
                    $tg = $stmt->fetch(PDO::FETCH_ASSOC);
                    $hitung = $tg['total_target'];
                    $id = $tg['id'];

                    if ($bulan == date("m")) {
                        $bg = "bg-danger text-white";
                    } else {
                        $bg = "";
                    }
                    if ($hitung > 0) {


                ?>
            <td class='text-center <?= $bg ?>'>
                <a href='<?= $url . "index.php?menu=index&act=target&submenu=edit_target&id=$id" ?>'
                    class='text-dark'><?= $hitung ?></a>
            </td>
            <?php
                    } else {
                    ?>
            <td class='text-center'>
                <a href='<?= $url . "index.php?menu=index&act=target&submenu=tambahtarget&bulan=$bulan&tahun=$tahun&akt=$target[kegiatan]" ?>'
                    class=''><i class="fa fa-plus"></i></a>
            </td>
            <?php

                    }
                    $total_target += $hitung;
                }
                ?>
            <td class='text-center'><span><?php echo $total_target; ?></span></td>
            <td>
                <a href="<?= $url . "index.php?menu=index&act=target&submenu=detail_target&aktifitas=" . urlencode($target['kegiatan']) . "&singkatan=" . urlencode($target['singkatan']) . "&tahun=" . $tahun ?>"
                    class="btn btn-success">
                    Detail Target
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>