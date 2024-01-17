<?php
@$menu  = $_GET['act'];

?>
<nav id="sidebar" class="col-md-2 col-lg-2 d-md-block sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column ">
            <li class="nav-item">
                <a class="nav-link <?= ($menu == '') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index' ?>">
                    <i class="fas fa-2x fa-home"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($menu === 'target') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=target' ?>">
                    <i class="fas fa-2x fa-file-excel"></i> Target
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($menu === 'capaian') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=capaian' ?>">
                    <i class="fas fa-2x fa-hourglass-start"></i> Capaian
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= ($menu === 'quiz') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=quiz' ?>">
                    <i class="fas fa-2x fa-question"></i> KUIS
                </a>
            </li>


            <li class="nav-item">
                <a class="nav-link <?= ($menu === 'bank_soal') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=bank_soal' ?>">
                    <i class="fas fa-2x fa-book"></i> Bank Soal
                </a>
            </li>
            <?php
            if ($_SESSION['jenisAkun'] === 'superuser') {
            ?>
                <li class="nav-item">
                    <a class="nav-link <?= ($menu === 'users') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=users' ?>">
                        <i class="fas fa-2x fa-users"></i> Users
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link <?= ($menu === 'generator') ? 'active' : '' ?>" href="<?= $url . 'index.php?menu=index&act=generator' ?>">
                        <i class="fas fa-2x fa-database"></i> Database
                    </a>
                </li>
            <?php
            }
            ?>
            <li class="nav-item mb-4">
                <a class="nav-link " href="<?= $url . 'logout.php?menu=logout' ?>">
                    <i class="fas fa-2x fa-arrow-left"></i> Logout
                </a>
            </li>
            <!-- Add more sidebar items as needed -->
        </ul>
    </div>
</nav>

<style>
    .sidebar {
        background-color: #2c3e50;
        /* Sidebar background color */
        color: #ecf0f1;
        /* Text color */
    }

    .nav-link {
        color: #ecf0f1 !important;
        /* Text color for links */
        transition: background-color 0.3s;
    }

    .nav-link:hover {
        background-color: #34495e;
        /* Hover background color for links */
    }

    .nav-link.active {
        background-color: #2980b9;
        /* Active background color for the selected link */
        color: #ecf0f1 !important;
        /* Text color for the selected link */
    }
</style>