<?php
$sqlCheck = "SELECT * FROM capaian_staff WHERE id_capaian_staff = ?";
@$id_capaian  = $_GET['id'];
$stmtCheck = $pdo->prepare($sqlCheck);
$stmtCheck->execute([$id_capaian]);
$result = $stmtCheck->fetch(PDO::FETCH_ASSOC);
if (!$result) {
    pindah(menu_sl("index"));
}
$sqlCheck1 = "SELECT * FROM detail_capaian_staff
WHERE id_capaian_staff = ?";
$stmtCheck1 = $pdo->prepare($sqlCheck1);
$stmtCheck1->execute([$id_capaian]);
$hasil = $stmtCheck1->fetch(PDO::FETCH_ASSOC);
@$json = json_decode($hasil['json_pinjaman'], true);
@$total_pinj =  hitungTotalPinjaman($hasil['json_pinjaman']);
$status = $result['status'];
$disabled = "";
if ($status == 'approve' || $status == 'konfirmasi') $disabled = "disabled";
// echo $status;
error_reporting(0);
?>
<h1>Form Detail Capaian Staff</h1>
<h3>minggu ke <?= $result['minggu'] ?>/<?= $bulanArray[$result['bulan']] ?>
    <?= $result['tahun'] ?>[<?= $result['status'] ?>]</h3>
<div class="col-lg-12 col-sm-12">
    <form action="" method="post" id='form_capaian'>
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

        Total Pencairan Pembiayaan Lain
        <div class="row g-3  mb-3">
            <?php
            foreach ($pinjamanArray as $kode => $kategori) {
            ?>
            <div class="col">
                <label for="pinjaman<?= $kode ?>"> <?= $kode ?></label>
                <input type="text" <?= $disabled ?> value="<?= $json[$kode] ? $json[$kode] : 0 ?>"
                    oninput="hitungTotalPinjaman()" class="form-control" id="pinjaman<?= $kode ?>"
                    name="pinjaman[<?= $kode ?>]" pattern="^(100|[0-9][0-9]?)$" min="0">
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



        <div class="form-group mb-3">
            <label for="keterangan">Keterangan: <br> <small>Jelaskan kenapa tidak bisa mencapai target atau ada
                    kenaikan</small></label>
            <textarea class="form-control" <?= $disabled ?> name="keterangan"><?= $hasil['keterangan'] ?></textarea>
        </div>
        <?php
        if ($disabled != "") {
        ?>
        <div class="form-group mb-3 ">
            TTD
            <img alt="" src="data:<?= $result['ttd'] ?>" />
        </div>
        <?php
        }
        ?>
        <a href='<?= menu_sl("laporan/index") ?>' class="btn btn-danger"><i class="fa fa-arrow-left"></i> KEMBALI</a>
        <button type="submit" name='simpan' <?= $disabled ?> class="btn btn-primary">SIMPAN</button>
        <?php
        if ($hasil && $status == 'pending') {
        ?>
        <!-- <button type="submit" value='simpan' name='konfirmasi'
            onclick="return window.confirm('Apakah yakin akan menyetujui laporan ini?')"
            class="btn btn-success">KONFIRMASI</button> -->
        <!-- Button trigger modal -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal">
            KONFIRMASI
        </button>
        <?php
        }
        ?>

    </form>

    <!-- Modal -->
    <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Satu langkah lagi, Silahkan berikan Tandatangan
                        anda disini!</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="signature"></div>


                    <div>Dengan penuh tanggung jawab, saya menandatangani ini sebagai tanda bahwa saya telah mengisi
                        data dengan teliti dan benar.</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button onclick="resetSignature()" class='btn btn-danger'>Reset TTD</button>
                    <button type="submit" value='simpan' id='submitBtn' name='konfirmasi'
                        class="btn btn-success">KONFIRMASI</button>
                </div>
            </div>
        </div>
    </div>



</div>

<?php
// echo var_dump($_POST);
function simpan($pdo, $id_capaian)
{
    $id_capaian_staff = $_GET['id'];
    $anggota_masuk = removeNonNumeric($_POST['anggota_masuk']);
    $anggota_keluar = removeNonNumeric($_POST['anggota_keluar']);
    $nett_anggota = $anggota_masuk - $anggota_keluar;
    $naik_par = removeNonNumeric($_POST['naik_par']);
    $turun_par = removeNonNumeric($_POST['turun_par']);
    $nett_par = $naik_par - $turun_par;
    $pemb_lain = removeNonNumeric($_POST['pemb_lain']);
    $keterangan = $_POST['keterangan'];
    $agt_cuti = $_POST['agt_cuti'];
    $agt_tpk = $_POST['agt_tpk'];
    $pinjaman_ = json_encode($_POST['pinjaman']);
    try {


        $sqlCheck = "SELECT id_detail_capaian FROM detail_capaian_staff
            WHERE id_capaian_staff = ?";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([$id_capaian_staff]);
        $result = $stmtCheck->fetch(PDO::FETCH_ASSOC);


        if ($result) {
            $sqlUpdate = "UPDATE detail_capaian_staff
                          SET anggota_masuk = ?, anggota_keluar = ?, nett_anggota = ?, naik_par = ?, turun_par = ?, nett_par = ?, pemb_lain = ?, keterangan = ?,agt_tpk=?,agt_cuti=?,json_pinjaman=?
                          WHERE id_capaian_staff = ?";
            $stmtUpdate = $pdo->prepare($sqlUpdate);
            $stmtUpdate->execute([$anggota_masuk, $anggota_keluar, $nett_anggota, $naik_par, $turun_par, $nett_par, $pemb_lain, $keterangan,  $agt_tpk, $agt_cuti, $pinjaman_, $id_capaian_staff]);
        } else {

            $sqlInsert = "INSERT INTO detail_capaian_staff (id_capaian_staff, anggota_masuk, anggota_keluar, nett_anggota, naik_par, turun_par, nett_par, pemb_lain, keterangan,agt_tpk,agt_cuti,json_pinjaman)
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?,?,?,?)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $stmtInsert->execute([$id_capaian_staff, $anggota_masuk, $anggota_keluar, $nett_anggota, $naik_par, $turun_par, $nett_par, $pemb_lain, $keterangan, $agt_tpk, $agt_cuti, $pinjaman_]);
        }
    } catch (PDOException $e) {
        echo $e->getMessage();
    }
}

if (isset($_POST['simpan'])) {

    simpan($pdo, $id_capaian);
    pindah(menu_sl("laporan/capaian&id=$id_capaian"));
}

if (isset($_POST['konfirmasi'])) {

    simpan($pdo, $id_capaian);

    try {

        $ttd = $_POST['imageData'];
        $id = $id_capaian;





        if ($id) {
            $query = "UPDATE capaian_staff SET status = 'konfirmasi',ttd=:ttd WHERE id_capaian_staff = :id AND status = 'pending'";
            $stmt = $pdo->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':ttd', $ttd);
            $stmt->execute();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    alert("Laporan berhasil disimpan, Terima kasih");
    pindah(menu_sl("laporan/index"));
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

#signature {
    /* height: 400px; */
    border: 1px solid #ccc;
    background-color: #fafafa;
}
</style>

<script>
function hitungTotalPinjaman() {
    // Ambil nilai input untuk masing-masing pinjaman
    var totalPinjaman = 0;

    <?php
        foreach ($pinjamanArray as $kode => $kategori) {
            echo 'var pinjaman' . $kode . ' = parseFloat(document.getElementById("pinjaman' . $kode . '").value) || 0;';
            echo 'totalPinjaman += pinjaman' . $kode . ';';
        }
        ?>

    // Set nilai totalPinjaman pada input readonly
    document.getElementById("totalPinjaman").value = totalPinjaman;
    document.getElementById("total_pemb").textContent = totalPinjaman;
}
document.getElementById('submitBtn').addEventListener('click', function() {
    // Aksi submit form
    var $signaturePad = $("#signature");
    var imageData = $signaturePad.jSignature("getData", "image");
    var konfirmasiInput = document.createElement("input");
    konfirmasiInput.type = "hidden";
    konfirmasiInput.name = "konfirmasi";
    konfirmasiInput.value = "ya";

    var imageDataTextarea = document.createElement("textarea");
    imageDataTextarea.name = "imageData"; // Sesuaikan dengan nama yang sesuai
    imageDataTextarea.style.display = "none"; // Sembunyikan textarea

    // Set nilai textarea dengan data gambar base64
    imageDataTextarea.value = imageData; // Anda perlu memiliki imageData yang telah didefinisikan sebelumnya
    document.getElementById("form_capaian").appendChild(imageDataTextarea);

    // Tambahkan elemen input hidden ke dalam form
    document.getElementById('form_capaian').appendChild(konfirmasiInput);

    document.getElementById('form_capaian').submit();
});
</script>