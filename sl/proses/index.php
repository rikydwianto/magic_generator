<h1>Hallo, Apa Kabar <?= $detailAkun['nama_staff'] ?> Cabang <?= $cabang ?></h1>
<h5>Kumulatif Capaian kamu</h5>

<div class="row">
    <!-- Anggota -->
    <div class="col-md-4">
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">AK</h5>
                <p class="card-text">-12</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">AM</h5>
                <p class="card-text">10</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-primary text-white">
            <div class="card-body">
                <h5 class="card-title">NETT AGT</h5>
                <p class="card-text">Capaian: 78%</p>
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
                <p class="card-text">Capaian: 65%</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-success text-white">
            <div class="card-body">
                <h5 class="card-title">Turun Par</h5>
                <p class="card-text">Capaian: 70%</p>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-center bg-danger text-white">
            <div class="card-body">
                <h5 class="card-title">NETT PAR</h5>
                <p class="card-text">Capaian: 70%</p>
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
                <p class="card-text">Capaian: 88%</p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-center bg-dark text-white">
            <div class="card-body">
                <h5 class="card-title">Pengajuan TPK</h5>
                <p class="card-text">Capaian: 92%</p>
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
    transform: scale(0.96);
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