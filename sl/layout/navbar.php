<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <a class="navbar-brand" href="<?= $url ?>">Logo</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="<?= $url ?>">Dashboard</a>
                </li>

                <!-- Add more menu items as needed -->
            </ul>
        </div>
    </div>
</nav>



<nav class="navbar navbar-expand-md navbar-dark bg-dark fixed-top">
    <div class="container-fluid">
        <button class="navbar-toggler text-white p-3" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar"
            aria-controls="sidebar">
            <i class="fa fa-bars fa-lg"></i>
        </button>
        <a class="navbar-brand" href="<?= $url ?>">REGIONAL</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto ">
                <li class="nav-item text-white">
                    <a class="nav-link" style="color: white;" href="<?= $url . "logout.php" ?>">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>