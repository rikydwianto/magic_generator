<?php
@$menu  = $_GET['menu'];
$jabatanMenu = array(
    'Staff Lapang' => array(
        'Dashboard' => array('link' => 'index', 'icon' => 'fa-home'),
        'Riwayat' => array('link' => 'riwayat', 'icon' => 'fa-history'),
    ),
    'MIS' => array(
        'Dashboard' => array('link' => 'index', 'icon' => 'fa-home'),
        'Submit Laporan' => array('link' => 'laporan/submit', 'icon' => 'fa-paper-plane'),
        'Laporan' => array('link' => 'laporan/index', 'icon' => 'fa-chart-bar'),
        'Staff' => array('link' => 'staff/index', 'icon' => 'fa-users'),
    ),
    'Manager' => array(
        'Dashboard' => array('link' => 'index', 'icon' => 'fa-home'),
        'Approve Laporan' => array('link' => 'laporan/approve', 'icon' => 'fa-check'),
        'Submit Laporan' => array('link' => 'laporan/submit', 'icon' => 'fa-paper-plane'),
        'Laporan' => array('link' => 'laporan/index', 'icon' => 'fa-chart-bar'),
        'Laporan per Staff' => array('link' => 'laporan/staff', 'icon' => 'fa-list'),
        'Staff' => array('link' => 'staff/index', 'icon' => 'fa-users'),
    ),
    'Regional' => array(
        'Dashboard' => array('link' => 'index', 'icon' => 'fa-home'),
        'Cabang' => array('link' => 'cabang/index', 'icon' => 'fa-store'),
        'Cek Laporan' => array('link' => 'laporan_regional/cek_laporan', 'icon' => 'fa-solid fa-calendar-days'),
        'Semua Laporan' => array('link' => 'laporan_regional/laporan_mingguan', 'icon' => 'fa-solid fa-calendar-days'),
        'Rekap Per Regional' => array('link' => 'laporan_regional/regional', 'icon' => 'fa-file-excel'),
        'Laporan Per Cabang ' => array('link' => 'laporan_regional/cabang', 'icon' => 'fa-file-excel'),
        'Laporan Per Staff ' => array('link' => 'laporan_regional/staff', 'icon' => 'fa-file-excel'),
        'User Login' => array('link' => 'users/index', 'icon' => 'fa-users'),
    ),
);

$defaultMenu = 'index';


$jabatanPengguna = $jabatan;

?>
<nav id="sidebar" class="col-md-2 col-lg-2 d-md-block sidebar">
    <div class="sidebar-sticky">
        <ul class="nav flex-column ">


            <?php

            foreach ($jabatanMenu as $jabatan => $menuItems) {
                echo '<li class="nav-item">';

                if ($jabatan == $jabatanPengguna) {
                    foreach ($menuItems as $menuLabel => $menuItem) {
                        $isActive = ($menu == $menuItem['link']) ? 'active' : '';
                        echo '<a class="nav-link ' . $isActive . '" href="' . $url . 'progress.php?menu=' . $menuItem['link'] . '">';
                        echo '<i class="fas fa-2x ' . $menuItem['icon'] . '"></i> ' . $menuLabel;
                        echo '</a>';
                    }
                }
                echo '</li>';
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