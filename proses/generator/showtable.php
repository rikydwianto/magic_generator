<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["select_table"])) {
    $selectedTable = $_POST["select_table"];

    // Implementasi logika untuk menampilkan data dari tabel yang dipilih
    $selectQuery = "SELECT * FROM $selectedTable";
    $selectStmt = $pdo->query($selectQuery);
    $resultRows = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
    // Handle INSERT action
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["kosongkan"])) {
    $selectedTable = $_POST["kosongkan"];
    // Implement logic untuk menyisipkan data ke tabel yang dipilih
    $tableToTruncate = $selectedTable;

    // Implementasi logika untuk menjalankan TRUNCATE TABLE pada tabel yang dipilih
    $truncateQuery = "TRUNCATE TABLE $tableToTruncate";
    $pdo->exec($truncateQuery);

    pindah($url . "index.php?menu=index&act=generator");
    // Handle UPDATE action
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["backup_table"])) {
    $selectedTable = $_POST["backup_table"];
    // Implement logic untuk memperbarui data di tabel yang dipilih
    // ...
    $table = $selectedTable;
    try {
        // Buat koneksi ke database menggunakan PDO

        // Dapatkan struktur tabel
        $result = $pdo->query("SHOW CREATE TABLE $table");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        $table_structure = $row['Create Table'];

        // Dapatkan data tabel
        $result = $pdo->query("SELECT * FROM $table");
        $data = $result->fetchAll(PDO::FETCH_ASSOC);

        // Buat file SQL
        $sql_content = "-- Struktur Tabel\n\n$table_structure;\n\n-- Data Tabel\n";
        foreach ($data as $row) {
            $values = implode("', '", $row);
            $sql_content .= "INSERT INTO $table VALUES ('$values');\n";
        }

        // Simpan file SQL
        $waktu = date("Y-m-d h-i-s");
        $filename =  $waktu . ' - ' . "$table.sql";
        $file_name = __DIR__ . "/../../FILE/sql/" . $filename;
        // echo ;
        file_put_contents($file_name, $sql_content);
        echo "Backup berhasil. File SQL telah dibuat ";
        pindah($url . "download.php?filename=sql/" . $filename);
        // pindah("$url" . "index.php?menu=index&act=generator");
    } catch (PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }

    // Handle DELETE action
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["delete_table"])) {
    $selectedTable = $_POST["delete_table"];
    // Implement logic untuk menghapus data dari tabel yang dipilih
    // ...
    $deleteQuery = "DROP TABLE IF EXISTS $selectedTable";
    $pdo->exec($deleteQuery);
    pindah($url . "index.php?menu=index&act=generator");
} elseif ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["struktur"])) {
    $selectedTable = $_POST["struktur"];
    $selectQuery = "DESCRIBE $selectedTable";
    $selectStmt = $pdo->query($selectQuery);
    $resultRows = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] != "POST") {



?>

<h2>Daftar Tabel</h2>
<?php
    try {

        $no = 1;
        $sql = "SHOW TABLES";
        $stmt = $pdo->query($sql);

        // Fetch daftar tabel
        $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
    ?>
<?php if (isset($tables) && count($tables) > 0) : ?>
<table class='table table-bordered'>
    <tr>
        <th>No</th>
        <th>Nama Tabel</th>
        <th>Total Row</th>
        <th>Total Kolom</th>
        <th>Aksi</th>
    </tr>
    <?php foreach ($tables as $table) : ?>
    <?php
                $hitung = $pdo->query("SELECT COUNT(*) as total_rows FROM $table");
                $hitung = $hitung->fetch()['total_rows'];

                $hitungField = $pdo->query("SELECT COUNT(*) AS jumlah_kolom
                FROM information_schema.columns
                WHERE table_schema = '$dbName' AND table_name = '$table';
                ");
                $hitungField = $hitungField->fetch()['jumlah_kolom'];
                ?>
    <tr>
        <td><?php echo $no++; ?></td>
        <td><?php echo $table; ?></td>
        <td><?php echo $hitung; ?></td>
        <td><?php echo $hitungField; ?></td>
        <td>
            <form method="post" style='float:left' action="">
                <input type="hidden" name="select_table" value="<?php echo $table; ?>">
                <button type="submit" class='btn btn-sm m-1 btn-success '>Lihat</button>
            </form>
            <form method="post" style='float:left' action="">
                <input type="hidden" name="kosongkan" value="<?php echo $table; ?>">
                <button type="submit" class='btn btn-sm m-1 btn-danger '
                    onclick="return window.confirm('Yakin dengan tindakan ini?')">Kosongkan</button>
            </form>
            <form method="post" style='float:left' action="">
                <input type="hidden" name="backup_table" value="<?php echo $table; ?>">
                <button type="submit" class='btn btn-sm m-1 btn-warning '>Backup</button>
            </form>
            <form method="post" style='float:left' action="">
                <input type="hidden" name="struktur" value="<?php echo $table; ?>">
                <button type="submit" class='btn btn-sm m-1 btn-primary '>Struktur</button>
            </form>
            <form method="post" action="">
                <input type="hidden" name="delete_table" value="<?php echo $table; ?>">
                <button type="submit" onclick="return window.confirm('Yakin dengan tindakan ini?')"
                    class='btn btn-sm m-1 btn-dark '>Delete</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<?php else : ?>
<p>Tidak ada tabel ditemukan.</p>
<?php endif; ?>
<?php } ?>

<?php $no=1;
if (isset($selectedTable) && isset($resultRows) && count($resultRows) > 0) : ?>
<h2>Data dari Tabel <?php echo $selectedTable; ?></h2>
<table class='table table-bordered'>
    <tr>
        <th>No</th>
        <?php foreach (array_keys($resultRows[0]) as $column) : ?>
        <th><?php echo $column; ?></th>
        <?php endforeach; ?>
    </tr>
    <?php foreach ($resultRows as $row) : ?>
    <tr>
        <td><?=$no++?></td>
        <?php foreach ($row as $value) : ?>
        <td><?php echo $value; ?></td>
        <?php endforeach; ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php else : ?>
<p>Tidak ada data yang dapat ditampilkan.</p>
<?php endif; ?>