<h1>Hallo, Apa Kabar <?= $detailAkun['nama_staff'] ?> Cabang <?= $cabang ?></h1>
<h5>Kumulatif Capaian kamu</h5>
<?php
// Nik yang akan digunakan sebagai parameter dalam query
$nik = $detailAkun['nik_staff']; // Ganti sesuai dengan nik yang diinginkan
// Query SQL dengan parameter nik
$query = "
    SELECT
        cs.nik_staff,
        cs.nama_staff,
        SUM(ds.anggota_masuk) AS total_anggota_masuk,
        SUM(ds.anggota_keluar) AS total_anggota_keluar,
        SUM(ds.nett_anggota) AS total_nett_anggota,
        SUM(ds.naik_par) AS total_naik_par,
        SUM(ds.turun_par) AS total_turun_par,
        SUM(ds.nett_par) AS total_nett_par,
        SUM(ds.agt_tpk) AS total_agt_tpk,
        SUM(ds.pemb_lain) AS total_pemb_lain
    FROM
        detail_capaian_staff ds
    JOIN
        capaian_staff cs ON ds.id_capaian_staff = cs.id_capaian_staff
    WHERE
        cs.nik_staff = :nik and status='approve'
    GROUP BY
        cs.nik_staff
";

// Mempersiapkan dan mengeksekusi query dengan parameter
try {
    $stmt = $pdo->prepare($query);
    $stmt->bindParam(':nik', $nik, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    // Menampilkan hasil query

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
<div class="row">
    <!-- Anggota -->
    <div class="col-md-4">
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">AK</h5>
                <p class="card-text"><?= $result['total_anggota_keluar'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">AM</h5>
                <p class="card-text"><?= $result['total_anggota_masuk'] ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">NETT AGT</h5>
                <p class="card-text"><?= $result['total_nett_anggota'] ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Naik Par, Turun Par, Nett Par -->
    <div class="col-md-4">
        <div class="card text-center bg-warning text-dark">
            <div class="card-body">
                <h5 class="card-title">Naik Par</h5>
                <p class="card-text"><?= angka($result['total_naik_par']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Turun Par</h5>
                <p class="card-text"><?= angka($result['total_turun_par']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">NETT PAR</h5>
                <p class="card-text"><?= angka($result['total_nett_par']) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Pembiayaan Lain dan Pengajuan TPK -->
    <div class="col-md-6">
        <div class="card text-center bg-secondary text-white">
            <div class="card-body">
                <h5 class="card-title">Pembiayaan Lain</h5>
                <p class="card-text"><?= angka($result['total_pemb_lain']) ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-center bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title">Pengajuan TPK</h5>
                <p class="card-text"><?= angka($result['total_agt_tpk']) ?></p>
            </div>
        </div>
    </div>
</div>
<style>
body {
    background-color: #f8f9fa;
}

.card {
    background-color: #ffffff;
    border: 1px solid #e1e5eb;
    border-radius: -10px;
    transition: transform 0.2s;
}

.card:hover {
    transform: scale(0.99);
}

.card-title {
    color: #fff;
    font-size: 24px;
    font-weight: bold;
}

.card-text {
    color: #fff;
    font-size: 18px;
}
</style>