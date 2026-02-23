<?php
$submenu = $_GET['submenu'] ?? '';
if ($submenu === 'del') {
    include __DIR__ . '/hapus.php';
    return;
}

$jenisAkun = $_SESSION['jenisAkun'] ?? '';
if ($jenisAkun !== 'superuser') {
    echo "<div class='alert alert-danger'>Akses ditolak.</div>";
    return;
}

$menuUrl = $url . 'index.php?menu=index&act=cabang_blockir';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $cabang = trim($_POST['cabang'] ?? '');

    if ($cabang === '') {
        alert("Nama cabang wajib diisi");
        pindah($menuUrl);
        return;
    }

    try {
        if ($action === 'create') {
            $cek = $pdo->prepare("SELECT id FROM block WHERE LOWER(cabang) = LOWER(?) LIMIT 1");
            $cek->execute([$cabang]);
            if ($cek->fetch(PDO::FETCH_ASSOC)) {
                alert("Cabang sudah ada di daftar blokir");
                pindah($menuUrl);
                return;
            }

            $stmt = $pdo->prepare("INSERT INTO block (cabang) VALUES (?)");
            $stmt->execute([$cabang]);
            alert("Cabang berhasil ditambahkan ke daftar blokir");
            pindah($menuUrl);
            return;
        }

        if ($action === 'update') {
            $id = (int)($_POST['id'] ?? 0);
            if ($id <= 0) {
                alert("ID tidak valid");
                pindah($menuUrl);
                return;
            }

            $cek = $pdo->prepare("SELECT id FROM block WHERE LOWER(cabang) = LOWER(?) AND id <> ? LIMIT 1");
            $cek->execute([$cabang, $id]);
            if ($cek->fetch(PDO::FETCH_ASSOC)) {
                alert("Nama cabang sudah digunakan pada data lain");
                pindah($menuUrl);
                return;
            }

            $stmt = $pdo->prepare("UPDATE block SET cabang = ? WHERE id = ?");
            $stmt->execute([$cabang, $id]);
            alert("Cabang berhasil diperbarui");
            pindah($menuUrl);
            return;
        }
    } catch (PDOException $e) {
        alert("Gagal menyimpan data");
        pindah($menuUrl);
        return;
    }
}

$editId = 0;
$editCabang = '';
if ($submenu === 'edit' && isset($_GET['id'])) {
    $editId = (int)$_GET['id'];
    if ($editId > 0) {
        try {
            $stmt = $pdo->prepare("SELECT id, cabang FROM block WHERE id = ?");
            $stmt->execute([$editId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $editCabang = $row['cabang'] ?? '';
            } else {
                $editId = 0;
            }
        } catch (PDOException $e) {
            $editId = 0;
        }
    }
}

$rows = [];
$error = '';
try {
    $stmt = $pdo->query("SELECT id, cabang FROM block ORDER BY cabang ASC");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = $e->getMessage();
}
?>

<div class="container-fluid px-4 py-3">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <h1 class="page-title">
                    <i class="fas fa-ban me-3"></i>CABANG BLOCKIR
                </h1>
                <p class="mb-0">Daftar cabang yang diblokir dari proses upload/analisa</p>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card modern-card">
                <div class="card-header" style="background: linear-gradient(135deg, #ff9a9e 0%, #fad0c4 100%); color: #2d3436;">
                    <h5 class="mb-0">
                        <i class="fas fa-list me-2"></i>Daftar Cabang Blockir
                    </h5>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= $menuUrl ?>" class="mb-4">
                        <input type="hidden" name="action" value="<?= $editId > 0 ? 'update' : 'create' ?>">
                        <?php if ($editId > 0): ?>
                            <input type="hidden" name="id" value="<?= (int)$editId ?>">
                        <?php endif; ?>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-8">
                                <label for="cabang" class="form-label fw-bold">
                                    <?= $editId > 0 ? 'Edit Cabang' : 'Tambah Cabang' ?>
                                </label>
                                <input type="text" class="form-control" id="cabang" name="cabang" value="<?= htmlspecialchars($editCabang, ENT_QUOTES) ?>" placeholder="Nama cabang" required>
                            </div>
                            <div class="col-md-4 d-grid">
                                <button type="submit" class="btn btn-<?= $editId > 0 ? 'warning' : 'primary' ?>">
                                    <?= $editId > 0 ? 'Simpan Perubahan' : 'Tambah' ?>
                                </button>
                                <?php if ($editId > 0): ?>
                                    <a href="<?= $menuUrl ?>" class="btn btn-light mt-2">Batal</a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>

                    <?php if ($error !== ''): ?>
                        <div class="alert alert-danger">Gagal memuat data: <?= htmlspecialchars($error, ENT_QUOTES) ?></div>
                    <?php endif; ?>

                    <div class="table-responsive">
                        <table class="table table-hover" data-datatable="true">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">No</th>
                                    <th>Cabang</th>
                                    <th width="20%">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($rows)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">Belum ada cabang yang diblokir</td>
                                    </tr>
                                <?php else: ?>
                                    <?php $no = 1; ?>
                                    <?php foreach ($rows as $row): ?>
                                        <?php
                                        $cabang = $row['cabang'] ?? '';
                                        $cabangSafe = htmlspecialchars($cabang, ENT_QUOTES);
                                        $rowId = (int)($row['id'] ?? 0);
                                        $editUrl = $menuUrl . '&submenu=edit&id=' . $rowId;
                                        $hapusUrl = $menuUrl . '&submenu=del&id=' . $rowId;
                                        ?>
                                        <tr>
                                            <td><?= $no++ ?></td>
                                            <td><strong><?= $cabangSafe ?></strong></td>
                                            <td>
                                                <a class="btn btn-sm btn-warning" href="<?= htmlspecialchars($editUrl, ENT_QUOTES) ?>">Edit</a>
                                                <button type="button" class="btn btn-sm btn-danger" onclick="confirmHapusBlock('<?= htmlspecialchars($hapusUrl, ENT_QUOTES) ?>', '<?= $cabangSafe ?>')">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function confirmHapusBlock(url, cabang) {
    var message = "Hapus blokir cabang " + cabang + "?";
    if (typeof confirmAction === 'function') {
        confirmAction(message, function() {
            window.location.href = url;
        });
        return;
    }
    if (confirm(message)) {
        window.location.href = url;
    }
}
</script>
