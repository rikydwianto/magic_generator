<?php
$sqlCheck = "SELECT * FROM capaian_staff WHERE id_capaian_staff = ?";
@$id_capaian  = $_GET['id'];
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([$id_capaian]);
$result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    pindah(menu_progress("index"));
}
$sqlCheck1 = "SELECT * FROM detail_capaian_staff
WHERE id_capaian_staff = ?";
$stmtCheck1 = $pdo->prepare($sqlCheck1);
$stmtCheck1->execute([$id_capaian]);
$hasil = $stmtCheck1->fetch(PDO::FETCH_ASSOC);
$json = json_decode($hasil['json_pinjaman'], true);
$total_pinj =  hitungTotalPinjaman($hasil['json_pinjaman']);
$status = $result['status'];
$disabled = "";
if ($status == 'approve' || $status == 'konfirmasi') $disabled = "disabled";
// echo $status;
error_reporting(0);
?>
<h1>Laporan An <?= $result['nama_staff'] ?> - <?= $result['cabang_staff'] ?></h1>
<h3>minggu ke <?= $result['minggu'] ?>/<?= $bulanArray[$result['bulan']] ?>
    <?= $result['tahun'] ?>[<?= $result['status'] ?>]</h3>
<div class="col-lg-12 col-sm-12">
    <form action="" method="post">
        <div class="row g-3 text-center mb-3">
            <div class="col">
                <div class="form-group">
                    <label for="anggota_masuk">Anggota Masuk:</label>
                    <input type="text" <?= $disabled ?> oninput="formatAngka(this)"
                        value="<?= $hasil['anggota_masuk'] ?>" class="form-control" name="anggota_masuk">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="anggota_keluar">Anggota Keluar:</label>
                    <input type="text" <?= $disabled ?> oninput="formatAngka(this)"
                        value="<?= $hasil['anggota_keluar'] ?>" class="form-control" name="anggota_keluar">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="nett_anggota">Nett Anggota:</label>
                    <div id="hasil_agt" class="mt-1 <?= $hasil['nett_anggota'] < 0 ? "text-danger" : "text-success" ?>">
                        <?= formatNumber($hasil['nett_anggota']) ?></div>
                    <input type="hidden" <?= $disabled ?> oninput="formatAngka(this)" readonly
                        value="<?= $hasil['nett_anggota'] ?>" class="form-control" name="nett_anggota">
                </div>
            </div>
        </div>



        <div class="row g-3 text-center  mb-3">
            <div class="col">
                <div class="form-group">
                    <label for="naik_par">Outstanding Par Naik:</label>
                    <input type="text" <?= $disabled ?> oninput="formatAngka(this)"
                        value="<?= formatNumber($hasil['naik_par']) ?>" class="form-control" name="naik_par">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="turun_par">Outstanding Par Turun:</label>
                    <input type="text" <?= $disabled ?> placeholder="3000000"
                        value="<?= formatNumber($hasil['turun_par']) ?>" oninput="formatAngka(this)"
                        class="form-control" name="turun_par">
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="nett_par">Nett Outstanding Par</label>
                    <div id="hasil" class="mt-1 <?= $hasil['nett_pat'] > 0 ? "text-danger" : "text-success" ?>">
                        <?= formatNumber($hasil['nett_par']) ?></div>
                    <input type="hidden" <?= $disabled ?> onload="formatAngka(this)" readonly
                        value="<?= formatNumber($hasil['nett_par']) ?>" class="form-control" name="nett_par">
                </div>
            </div>
        </div>

        <div class="row g-3  mb-3">
            <?php
            foreach ($pinjamanArray as $kode => $kategori) {
            ?>
            <div class="col">
                <label for="pinjaman<?= $kode ?>"> <?= $kode ?></label>
                <input type="number" <?= $disabled ?> value="<?= $json[$kode] ?>" oninput="hitungTotalPinjaman()"
                    class="form-control" id="pinjaman<?= $kode ?>" name="pinjaman[<?= $kode ?>]" min="0">
            </div>
            <?php

            }

            ?>
            <input type="hidden" readonly id='totalPinjaman' value="<?= $total_pinj ?>" class="form-control"
                name="pemb_lain">
            <div class="col text-center">
                <label for="total_pemb"> Total Pemb:</label>
                <div id="total_pemb"><?= $total_pinj ?></div>
            </div>

        </div>

        <div class="row g-3  mb-3">
            <div class="col">

                <div class="form-group">
                    <label for="agt_cuti">Anggota Cuti <br> <small>Total Anggota cuti yang
                            dimiliki</small></label>
                    <input type="number" <?= $disabled ?> value="<?= $hasil['agt_cuti'] ?>" class="form-control"
                        id="agt_cuti" name="agt_cuti">

                </div>
            </div>
            <div class="col">

                <div class="form-group">
                    <label for="agt_tpk">Pengajuan Anggota TPK <br> <small>Total Anggota yang diajukan TPK minggu
                            ini</small></label>
                    <input type="number" <?= $disabled ?> value="<?= $hasil['agt_tpk'] ?>" class="form-control"
                        id="agt_tpk" name="agt_tpk">

                </div>
            </div>
        </div>



        <div class="form-group mb-3 ">
            <label for="keterangan">Keterangan: <br> <small>Jelaskan kenapa tidak bisa mencapai target atau ada
                    kenaikan</small></label>
            <textarea class="form-control" <?= $disabled ?> name="keterangan"><?= $hasil['keterangan'] ?></textarea>
        </div>
        <div class="form-group mb-3 ">
            TTD
            <img alt="" src="data:<?= $result['ttd'] ?>" />
        </div>
        <?php
        if ($hasil && $status == 'konfirmasi') {
        ?>
        <button type="submit" value='simpan' name='approve'
            onclick="return window.confirm('Apakah yakin akan menyetujui laporan ini?')" class="btn btn-success"><i
                class="fa fa-check"></i> APPROVE</button>
        <button type="submit" value='pending' name='reject'
            onclick="return window.confirm('Apakah yakin akan Reject laporan ini?')" class="btn btn-danger"><i
                class="fa fa-times"></i> REJECT</button>
        <?php
        }
        ?>
        <a href='<?= menu_progress("laporan/approve") ?>' class="btn btn-danger"><i class="fa fa-arrow-left"></i>
            KEMBALI</a>
    </form>
</div>

<?php




if (isset($_POST['approve'])) {


    try {
        $id = $id_capaian;
        if ($id) {
            $query = "UPDATE capaian_staff SET status = 'approve' WHERE id_capaian_staff = :id AND status = 'konfirmasi'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    // alert("Laporan berhasil disimpan, Terima kasih");
    pindah(menu_progress("laporan/approve"));
}


if (isset($_POST['reject'])) {


    try {
        $id = $id_capaian;
        if ($id) {
            $query = "UPDATE capaian_staff SET status = 'pending' WHERE id_capaian_staff = :id AND status = 'konfirmasi'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    // alert("Laporan berhasil disimpan, Terima kasih");
    pindah(menu_progress("laporan/approve"));
}

?>
<style>
/* CSS untuk input */
input {
    font-weight: bold;
    vertical-align: auto;
    /* Membuat huruf tebal */
    font-size: 18px;
    /* Menentukan ukuran huruf */
    text-align: center;
    /* Menengahkan teks */
}

#hasil,
#hasil_agt,
#total_pemb {
    text-align: center;
    font-weight: bold;
    font-size: larger;
}
</style>