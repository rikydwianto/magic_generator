<?php
$namaCabang = $_GET['nama_cabang'];
$id_cabang = $sesi;
?>
<style>
table {
    border-collapse: collapse;
}
</style>
<div class="container-fluid">

    <?php
    $cek  = "select count(*) as hitung from center where nama_cabang='$namaCabang'";
    $hitung = $pdo->query($cek);
    $hitung = $hitung->fetch()['hitung'];
    if ($hitung < 1) {
        pindah("index.php?menu=center_meeting");
    }
    ?>
    <div class="row">
        jika ingin diprint, klik kanan -> print
        <!-- <a href="javacript:printCtr()" onclick="printCtr('printArea')" class="btn"><i class="fa fa-print"></i></a> -->
        <hr>
        <div class="col-12">
            <table border="1" id='printArea' class='table table-bordered'>
                <tr>
                    <th class="text-center p-1">
                        <small style="font-weight: lighter;text-align: left;">&nbsp;</small> <br>
                        <img src="./assets/img/logo.png" style="width:50px;" alt="Logo Comdev" class=""> <br>
                        Community Development
                    </th>
                    <th style="text-align: center;" colspan="30"> <br />
                        JADWAL CENTER MEETING <br /> CABANG <?= strtoupper($namaCabang) ?> <br /><br />
                    </th>
                </tr>
                <?php
                $qhari = "SELECT distinct hari from center where id_cabang='$id_cabang' and nama_cabang='$namaCabang' order by FIELD(hari,'senin','selasa','rabu','kamis','jumat') asc";
                $hari  = $pdo->query($qhari);
                foreach ($hari->fetchAll() as $hari) {
                ?>
                <tr>
                    <td rowspan="2" style="padding: 5px;font-weight: bold;"><?= strtoupper($hari['hari']) ?></td>
                    <?php
                        $qkar = "SELECT distinct c.staff from center c  where c.id_cabang='$id_cabang' order by c.staff asc ";
                        $kar =  $pdo->query($qkar);
                        foreach ($kar->fetchAll() as $kar) {
                            $pecah_nama = explode(" ", strtoupper($kar['staff']));
                            $nama_staff = $pecah_nama[0];
                            if (strlen($nama_staff) < 3) {
                                $nama_staff = $pecah_nama[0] . " " . $pecah_nama[1];
                            } else {
                                if (!empty($pecah_nama[1])) {

                                    $nama_staff = $nama_staff . " " . $pecah_nama[1][0];
                                }
                            }
                        ?>
                    <th colspan="1" style="font-size: 12px;min-width: 60px;"> &nbsp;&nbsp;<?= $nama_staff ?>&nbsp;&nbsp;
                    </th>
                    <?php

                            $center_hari =  "SELECT count(hari) as hitung_hari from center where id_cabang='$id_cabang' and hari='$hari[hari]'";
                            $center_hari = $pdo->query($center_hari);
                        }
                        ?>
                    <td rowspan="2">


                    </td>
                </tr>
                <tr>

                    <?php
                        $qkar = "SELECT distinct c.staff,c.id_karyawan from center c where c.id_cabang='$id_cabang' order by c.staff asc ";
                        $qkar  = $pdo->query($qkar);
                        foreach ($qkar->fetchAll() as $kar) {
                            $qcenter = $pdo->query("SELECT no_center,status_center,member_center from center where id_cabang='$id_cabang' and hari='$hari[hari]' and staff='$kar[staff]' order by jam_center asc");
                        ?>
                    <td style="vertical-align: top;text-align:center">
                        <?php
                                foreach ($qcenter->fetchAll() as $center) {
                                    $status = $center['status_center'];
                                    $warna = 'black';
                                    echo "<b style='color:$warna;float:left'>" . sprintf("%03d", $center['no_center']) . "</b> | <b style='float:right'>$center[member_center]</b>" . "<br/>";
                                }
                                ?>
                    </td>
                    <?php
                        }
                        ?>

                </tr>
                <tr>
                    <th>TOTAL</th>
                    <?php $qkar = $pdo->query("SELECT distinct c.staff,c.id_karyawan from center c  where c.id_cabang='$id_cabang' order by c.staff asc ");
                        foreach ($qkar->fetchAll() as $kar) {
                            $qcenter = $pdo->query("SELECT count(no_center) as hitung_center from center where id_cabang='$id_cabang' and hari='$hari[hari]' and id_karyawan='$kar[id_karyawan]' order by jam_center asc");
                            $hitung = $qcenter->fetch();
                        ?>
                    <td style="vertical-align: top;text-align:center;font-weight: bold;background-color: #dcdedc;">
                        <?= $hitung['hitung_center'] ?>
                    </td>
                    <?php
                        }
                        ?>
                    <th><?= $center_hari->fetch()['hitung_hari'] ?></th>
                </tr>

                <?php
                }
                $hitung_semua = 0;
                ?>
                <tr>
                    <th colspan="1">TOTAL SEMUA STAFF
                        <hr>MEMBER
                    </th>
                    <?php
                    $qkar = $pdo->query("SELECT distinct c.staff,c.id_karyawan from center c  where c.id_cabang='$id_cabang' order by c.staff asc ");
                    foreach ($qkar->fetchAll() as $kar) {
                        $qcenter = $pdo->query("SELECT count(no_center) as hitung_center,sum(member_center) as member from center where id_cabang='$id_cabang'  and staff='$kar[staff]' order by jam_center asc");

                        $semua = $qcenter->fetch();
                    ?>
                    <th style="vertical-align: middle;text-align:center;font-weight: bold;background-color: dcdedc;">
                        <?= $total = $semua['hitung_center'] ?>
                        <hr />
                        <?= $total_member = $semua['member'] ?><br />
                        <?php $hitung_semua += $total ?>
                    </th>
                    <?php

                        $total_member += $total_member;
                    }
                    ?>
                    <td rowspan="0" style="padding: 10px;font-weight: bold;">
                        <?= $hitung_semua ?>
                        <hr>
                        <?php
                        $total_member = $pdo->query("SELECT sum(member_center) as member from center where id_cabang='$id_cabang' group by id_cabang  ");
                        $total_member = $total_member->fetch();
                        echo $total_member['member'];
                        ?>
                    </td>
                </tr>

            </table>
        </div>
    </div>
</div>
<?php
$query = "delete from center where nama_cabang='$namaCabang'";
$pdo->query($query);
?>
<style>
@media print {

    /* Sembunyikan semua elemen kecuali elemen dengan id printArea */
    body * {
        visibility: hidden;
    }

    #printArea,
    #printArea * {
        visibility: visible;
    }

    #printArea {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
    }

    img {
        max-width: 100%;
        height: auto;
    }
}
</style>
<script>
function printCtr(elementId) {
    // Menyimpan elemen yang akan dicetak
    var printElement = document.getElementById(elementId);
    var originalContents = document.body.innerHTML;

    // Menyembunyikan semua elemen lain kecuali elemen yang akan dicetak
    document.body.innerHTML = printElement.outerHTML;

    // Memanggil metode print
    window.print();

    // Mengembalikan konten asli halaman setelah mencetak
    document.body.innerHTML = originalContents;
    location.reload(); // Reload halaman untuk mengembalikan e

}
</script>