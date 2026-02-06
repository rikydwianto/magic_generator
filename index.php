<?php
require 'vendor/autoload.php'; // Impor library Dotenv
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";
require("vendor/PHPExcel/Classes/PHPExcel.php");
set_time_limit(3000);

// echo '<a href="https://wa.me/6281214657370?text=Hallo%20pak%2C%20saya%20..%20dari%20cabang%20...%0A%0Aterimakasih%20ya" class="btn btn-success m-3">Send Greater to Creator :)</a>';
// echo "<h1>Terima kasih semua</h1>";
// exit;
@$sesi = $_SESSION['sesi'];
?>
<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css">
    
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <!-- Quill CSS -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="<?= $url ?>assets/css/custom.css">

    <link rel="icon" type="image/png" sizes="32x32" href="./assets/img/logo.png" />
    
    <style>
        /* Index Page Specific Styles */
        .navbar-custom {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.98), rgba(227, 242, 253, 0.95));
            backdrop-filter: blur(15px);
            box-shadow: 0 4px 30px rgba(78, 115, 223, 0.15);
            border-bottom: 2px solid rgba(78, 115, 223, 0.2);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .navbar-brand img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            border: 2px solid var(--primary-color);
            padding: 3px;
            transition: transform 0.3s ease;
        }

        .navbar-brand:hover img {
            transform: rotate(360deg);
        }

        .nav-link {
            color: var(--dark-color) !important;
            font-weight: 500;
            padding: 8px 16px !important;
            margin: 0 5px;
            border-radius: 8px;
            transition: all 0.3s ease;
            position: relative;
        }

        .nav-link:hover {
            background: linear-gradient(135deg, var(--primary-color), #667eea);
            color: white !important;
            transform: translateY(-2px);
        }

        .nav-link.active {
            background: var(--primary-color);
            color: white !important;
        }

        #content {
            background: white;
            border-radius: 20px;
            padding: 40px;
            margin: 20px 0;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            animation: fadeInUp 0.6s ease;
            border: 1px solid rgba(78, 115, 223, 0.1);
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .loader-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }

        .loader {
            width: 80px;
            height: 80px;
            border: 5px solid rgba(78, 115, 223, 0.2);
            border-radius: 50%;
            border-top-color: #4e73df;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        .welcome-section {
            text-align: center;
            padding: 60px 20px;
        }

        .welcome-section h1 {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #4e73df 0%, #1cc88a 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            text-shadow: 0 2px 10px rgba(78, 115, 223, 0.1);
        }

        .welcome-section p {
            font-size: 1.2rem;
            color: var(--secondary-color);
        }

        .feature-card {
            border: none;
            border-radius: 20px;
            padding: 40px 30px;
            margin: 15px 0;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            background: white;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(78, 115, 223, 0.1);
        }

        .feature-card:hover {
            transform: translateY(-15px) scale(1.02);
            box-shadow: 0 20px 50px rgba(78, 115, 223, 0.25);
            border-color: rgba(78, 115, 223, 0.3);
        }

        .feature-card i {
            font-size: 3rem;
            margin-bottom: 20px;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            border: none;
        }

        .btn-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .stats-card {
            border-radius: 15px;
            padding: 25px;
            color: white;
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-icon {
            font-size: 3rem;
            opacity: 0.9;
        }

        .stats-content h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
            color: white;
        }

        .stats-content p {
            font-size: 0.9rem;
            opacity: 0.9;
            margin: 0;
        }

        .activity-section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(78, 115, 223, 0.1);
        }

        .activity-section h4 {
            color: var(--dark-color);
            font-weight: 600;
        }

        .table-hover tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        @media (max-width: 768px) {
            .welcome-section h1 {
                font-size: 2rem;
            }
        }
    </style>
    
    <script>
        let url = "<?= $url ?>";
        let url_api = url + "api/";
    </script>

    <title>TOOL - Modern Dashboard</title>
</head>

<body>
    <!-- Loader -->
    <div class="loader-container" id="loader">
        <div class="loader"></div>
    </div>

    <!-- Modern Navbar -->
    <nav class="navbar navbar-expand-lg navbar-custom sticky-top animate__animated animate__fadeInDown">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= $url ?>index.php">
                <img src="./assets/img/logo.png" alt="Logo">
                <span>Report Tool</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link <?= !isset($_GET['menu']) ? 'active' : '' ?>" href="<?= $url ?>index.php">
                            <i class="fas fa-home me-1"></i> HOME
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'cek_par') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=cek_par">
                            <i class="fas fa-chart-line me-1"></i> CEK PAR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'anal') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=anal">
                            <i class="fas fa-chart-bar me-1"></i> ANALISA PAR
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'center_meeting') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=center_meeting">
                            <i class="fas fa-users me-1"></i> CENTER
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'permintaandisburse') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=permintaandisburse">
                            <i class="fas fa-money-bill-wave me-1"></i> DISBURSE
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'delin_reg') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=delin_reg">
                            <i class="fas fa-building me-1"></i> REGIONAL
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= (isset($_GET['menu']) && $_GET['menu'] == 'index') ? 'active' : '' ?>" 
                           href="<?= $url ?>index.php?menu=index">
                            <i class="fas fa-desktop me-1"></i> CONTROL ROOM
                        </a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="<?= $url ?>progress.php">
                            <i class="fas fa-tasks me-1"></i> PROGRESS
                        </a>
                    </li>
                    -->
                    <?php if ($sesi != '' || $sesi != null) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $url ?>logout.php?menu=logout" onclick="return confirmLogout()">
                            <i class="fas fa-sign-out-alt me-1"></i> LOGOUT
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="row">
        <div class="container container-fluid">

            <?php

            @$menu = $_GET['menu'];
            // session_destroy();
            // if ($sesi == '' || $sesi == null) {
            if (false) {
                if ($menu == 'index') {
                    include("./proses/index.php");
                } else {
                    include("./proses/tanya.php");
                }
            } else {

                // $menu = 'mati';
                if ($menu == 'cek_par' || $menu == 'anal' || $menu == 'delin_reg' || $menu == 'proses_delin'  || $menu == 'center_meeting') {
            ?>
                    <div class="floating-box">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <!-- Kotak untuk gambar QRIS -->
                                <div class="qris-box">
                                    <!-- <a href="assets/img/qris.png" target="_blank"> -->
                                    <!-- <img src="assets/img/qris.png" alt="QRIS" class="img-fluid"> -->

                                    <!-- </a> -->
                                </div>
                                <!-- Teks atau elemen tambahan di bawah gambar -->
                                <!-- <p class="card-text mt-3">
                            <strong>Catatan:</strong> Server ini adalah server pribadi dan <strong>bukan</strong> server
                            resmi dari Komida.
                        </p> -->
                            </div>
                        </div>
                    </div>
                    <style>
                        /* Gaya untuk kotak mengambang */
                        .floating-box {
                            position: fixed;
                            /* Membuat kotak tetap di layar */
                            bottom: 20px;
                            /* Jarak dari bawah */
                            right: 20px;
                            /* Jarak dari kanan */
                            z-index: 1000;
                            /* Memastikan kotak berada di atas elemen lain */
                        }

                        /* Gaya untuk card */
                        .card {
                            /* max-width: 400px; */
                            /* Lebar maksimal card */
                            /* box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); */
                            /* Efek bayangan */
                        }

                        /* Gaya untuk kotak gambar QRIS */
                        .qris-box {
                            display: inline-block;
                            border: 2px solid #dc3545;
                            /* Warna border sesuai dengan card border-danger */
                            padding: 10px;
                            border-radius: 10px;
                            /* Membuat sudut kotak melengkung */
                            background-color: #f8f9fa;
                            /* Warna latar belakang kotak */
                        }

                        /* Gaya untuk gambar QRIS */
                        .qris-box img {
                            max-width: 100%;
                            height: auto;
                            border-radius: 5px;
                            /* Membuat sudut gambar melengkung */
                        }

                        /* Gaya untuk teks di dalam card */
                        .card-text {
                            font-size: 12px;
                            /* Ukuran font lebih kecil */
                            color: #6c757d;
                            /* Warna teks */
                        }
                    </style>

                <?php
                } else {
                    // echo '<a href="https://wa.me/6281214657370?text=Assalamualaikum%20pak%2C%20saya%20...%20dari%20cabang%20...%0AMau%20ikut%20berpartisisi%0Aterimakasih" class="btn btn-success m-3">+62 812 1465 7370</a>';
                }


                @$menu = $_GET['menu'];
                if ($menu == "cek_par") {
                    include("./proses/cek_par.php");
                } else if ($menu == 'anal') {
                    include("./proses/analisis.php");
                } else if ($menu == 'delin_reg') {
                    include("./proses/delin_reg.php");
                } else if ($menu == 'proses_delin') {
                    include("./proses/proses_delin.php");
                } else if ($menu == 'proses_delin_reg') {
                    include("./proses/proses_delin_reg.php");
                } else if ($menu == 'index') {
                    include("./proses/index.php");
                } else if ($menu == 'center_meeting') {
                    include("./proses/center_input.php");
                } else if ($menu == 'center_proses') {
                    include("./proses/center_proses.php");
                } else if ($menu == 'permintaandisburse') {
                    include("./proses/permintaandisburse.php");
                } else if ($menu == 'permintaan_disburse_print') {
                    include("./proses/permintaan_disburse_print.php");
                } else {
                ?>
                    <div class="container-fluid">
                        <div class="welcome-section animate__animated animate__fadeIn">
                            <h1 class="mb-2">REPORT TOOL</h1>
                            <p class="mb-5 text-secondary">Platform Analisis PAR & Deliquency Management System</p>
                            
                            <!-- Quick Stats Cards -->
                            <div class="row mb-5">
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="stats-card bg-primary">
                                        <div class="stats-icon">
                                            <i class="fas fa-chart-line"></i>
                                        </div>
                                        <div class="stats-content">
                                            <?php
                                            $sql_cek = "SELECT COUNT(*) as total FROM log_cek_par WHERE keterangan='proses' AND DATE(created_at) = CURDATE()";
                                            $stmt_cek = $pdo->query($sql_cek);
                                            $total_cek = $stmt_cek->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h3 class="mb-0"><?= $total_cek ?></h3>
                                            <p class="mb-0">CEK PAR Hari Ini</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="stats-card bg-success">
                                        <div class="stats-icon">
                                            <i class="fas fa-chart-bar"></i>
                                        </div>
                                        <div class="stats-content">
                                            <?php
                                            $sql_anal = "SELECT COUNT(*) as total FROM log_cek_par WHERE keterangan='proses-analisa' AND DATE(created_at) = CURDATE()";
                                            $stmt_anal = $pdo->query($sql_anal);
                                            $total_anal = $stmt_anal->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h3 class="mb-0"><?= $total_anal ?></h3>
                                            <p class="mb-0">Analisa Hari Ini</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="stats-card bg-info">
                                        <div class="stats-icon">
                                            <i class="fas fa-file-excel"></i>
                                        </div>
                                        <div class="stats-content">
                                            <?php
                                            $sql_today = "SELECT COUNT(*) as total FROM log_cek_par WHERE keterangan='selesai' AND DATE(created_at) = CURDATE()";
                                            $stmt_today = $pdo->query($sql_today);
                                            $total_today = $stmt_today->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h3 class="mb-0"><?= $total_today ?></h3>
                                            <p class="mb-0">Selesai Hari Ini</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 mb-4">
                                    <div class="stats-card bg-warning">
                                        <div class="stats-icon">
                                            <i class="fas fa-database"></i>
                                        </div>
                                        <div class="stats-content">
                                            <?php
                                            $sql_total = "SELECT COUNT(*) as total FROM log_cek_par WHERE DATE(created_at) = CURDATE()";
                                            $stmt_total = $pdo->query($sql_total);
                                            $total_records = $stmt_total->fetch(PDO::FETCH_ASSOC)['total'];
                                            ?>
                                            <h3 class="mb-0"><?= number_format($total_records) ?></h3>
                                            <p class="mb-0">Total Log Hari Ini</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Main Feature Cards -->
                            <div class="row justify-content-center mb-5">
                                <div class="col-lg-5 col-md-6">
                                    <div class="feature-card">
                                        <i class="fas fa-chart-line text-primary" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3 mb-3">CEK PAR</h4>
                                        <p class="text-muted mb-4">Upload 2 file Excel untuk membandingkan PAR (sebelum & sesudah). Sistem akan otomatis mendeteksi perubahan, kenaikan atau penurunan PAR per cabang.</p>
                                        <div class="mb-3">
                                            <span class="badge bg-primary-subtle text-primary me-2"><i class="fas fa-file-excel me-1"></i> Excel Upload</span>
                                            <span class="badge bg-primary-subtle text-primary me-2"><i class="fas fa-arrows-left-right me-1"></i> Compare Data</span>
                                            <span class="badge bg-primary-subtle text-primary"><i class="fas fa-chart-line me-1"></i> Trend Analysis</span>
                                        </div>
                                        <a href="<?= $url ?>index.php?menu=cek_par" class="btn btn-primary btn-custom w-100 mt-3">
                                            <i class="fas fa-upload me-2"></i>Mulai CEK PAR
                                        </a>
                                    </div>
                                </div>
                                <div class="col-lg-5 col-md-6">
                                    <div class="feature-card">
                                        <i class="fas fa-chart-bar text-success" style="font-size: 4rem;"></i>
                                        <h4 class="mt-3 mb-3">ANALISA PAR</h4>
                                        <p class="text-muted mb-4">Analisis mendalam dari 1 file Excel PAR. Dapatkan breakdown detail per loan, center, dan visualisasi lengkap dengan grafik interaktif.</p>
                                        <div class="mb-3">
                                            <span class="badge bg-success-subtle text-success me-2"><i class="fas fa-microscope me-1"></i> Deep Analysis</span>
                                            <span class="badge bg-success-subtle text-success me-2"><i class="fas fa-table me-1"></i> Detail Breakdown</span>
                                            <span class="badge bg-success-subtle text-success"><i class="fas fa-chart-pie me-1"></i> Visualization</span>
                                        </div>
                                        <a href="<?= $url ?>index.php?menu=anal" class="btn btn-success btn-custom w-100 mt-3">
                                            <i class="fas fa-microscope me-2"></i>Mulai Analisa
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Recent Activity -->
                            <div class="row">
                                <div class="col-12">
                                    <div class="activity-section">
                                        <h4 class="mb-4"><i class="fas fa-clock-rotate-left me-2"></i>Recent Activity</h4>
                                        <div class="table-responsive">
                                            <table class="table table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="5%">No</th>
                                                        <th width="15%">Cabang</th>
                                                        <th width="20%">Waktu Mulai</th>
                                                        <th width="15%">Jenis</th>
                                                        <th width="20%">Dibuat Pada</th>
                                                        <th width="15%">Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php
                                                    $sql_recent = "SELECT * FROM log_cek_par ORDER BY created_at DESC LIMIT 10";
                                                    $stmt_recent = $pdo->query($sql_recent);
                                                    $no = 1;
                                                    
                                                    if ($stmt_recent->rowCount() > 0) {
                                                        while ($row = $stmt_recent->fetch(PDO::FETCH_ASSOC)) {
                                                            $badge_class = 'warning';
                                                            $status_text = 'Proses';
                                                            
                                                            if ($row['keterangan'] == 'selesai') {
                                                                $badge_class = 'success';
                                                                $status_text = 'Selesai';
                                                            } elseif ($row['keterangan'] == 'proses-analisa') {
                                                                $badge_class = 'info';
                                                                $status_text = 'Analisa';
                                                            } elseif ($row['keterangan'] == 'proses') {
                                                                $badge_class = 'warning';
                                                                $status_text = 'CEK PAR';
                                                            }
                                                    ?>
                                                        <tr>
                                                            <td><?= $no++ ?></td>
                                                            <td><strong><?= $row['cabang'] ?></strong></td>
                                                            <td><?= $row['mulai'] ?></td>
                                                            <td>
                                                                <?php if ($row['keterangan'] == 'proses-analisa'): ?>
                                                                    <i class="fas fa-chart-bar text-success me-1"></i> Analisa PAR
                                                                <?php else: ?>
                                                                    <i class="fas fa-chart-line text-primary me-1"></i> CEK PAR
                                                                <?php endif; ?>
                                                            </td>
                                                            <td><?= date('d M Y H:i', strtotime($row['created_at'])) ?></td>
                                                            <td>
                                                                <span class="badge bg-<?= $badge_class ?>">
                                                                    <?= $status_text ?>
                                                                </span>
                                                            </td>
                                                        </tr>
                                                    <?php
                                                        }
                                                    } else {
                                                        echo "<tr><td colspan='6' class='text-center text-muted'>Belum ada aktivitas</td></tr>";
                                                    }
                                                    ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

            <?php
                }
            }
            ?>
        </div>
    </div>

    <!-- jQuery (HARUS PERTAMA) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Quill JS -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    
    <!-- Custom JS -->
    <script src="<?= $url ?>assets/js/script_index.js"></script>
    
    <script>
        // Hide loader when page is fully loaded
        window.addEventListener('load', function() {
            setTimeout(function() {
                const loader = document.getElementById('loader');
                if (loader) loader.style.display = 'none';
            }, 500);
        });

        // Confirm logout with SweetAlert
        function confirmLogout() {
            Swal.fire({
                title: 'Konfirmasi Logout',
                text: "Apakah Anda yakin ingin keluar?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e74a3b',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Logout!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = '<?= $url ?>logout.php?menu=logout';
                }
            });
            return false;
        }

        // Initialize DataTables with modern style - only tables with specific IDs or data-datatable attribute
        $(document).ready(function() {
            if ($.fn.DataTable) {
                // Initialize tables with data-datatable="true" or specific IDs
                $('table[data-datatable="true"], table#example, table#table, table#tabelCapaianStaff, table#table_capaian').each(function() {
                    if (!$.fn.DataTable.isDataTable(this)) {
                        try {
                            $(this).DataTable({
                                responsive: true,
                                language: {
                                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/id.json'
                                },
                                pageLength: 25,
                                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rtip',
                                columnDefs: [
                                    { targets: '_all', defaultContent: '' }
                                ]
                            });
                        } catch(e) {
                            console.warn('DataTables initialization failed for table:', this, e);
                        }
                    }
                });
            }
        });

        // Success notification
        window.showSuccess = function(message) {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: message,
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true
            });
        }

        // Error notification
        window.showError = function(message) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: message,
                confirmButtonColor: '#e74a3b'
            });
        }

        // Loading overlay
        window.showLoading = function(message = 'Sedang memproses data') {
            Swal.fire({
                title: 'Mohon tunggu...',
                html: message,
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                },
                allowOutsideClick: false,
                allowEscapeKey: false
            });
        }

        // Close loading
        window.closeLoading = function() {
            Swal.close();
        }

        // Confirm dialog
        window.confirmAction = function(message, callback) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya, Lanjutkan!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    callback();
                }
            });
        }

        // Show toast notification
        window.showToast = function(icon, title) {
            const Toast = Swal.mixin({
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
            Toast.fire({
                icon: icon,
                title: title
            });
        }

        // Replace default confirm with SweetAlert
        window.confirm = function(message) {
            Swal.fire({
                title: 'Konfirmasi',
                text: message,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#4e73df',
                cancelButtonColor: '#858796',
                confirmButtonText: 'Ya',
                cancelButtonText: 'Tidak',
                reverseButtons: true
            }).then((result) => {
                return result.isConfirmed;
            });
            return false; // Prevent default form submission
        }

        // Replace default alert with SweetAlert
        window.alert = function(message) {
            Swal.fire({
                title: 'Informasi',
                text: message,
                icon: 'info',
                confirmButtonColor: '#4e73df'
            });
        }
    </script>

</body>

</html>
<?php $pdo = null ?>
