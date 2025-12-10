<form action="" method="post">
    <label for="sqlQuery">Masukkan Query MySQL:</label>
    <textarea id="sqlQuery" name="sqlQuery" class='form-control' rows="5" cols="40"
        required><?= @$_POST['sqlQuery'] ?></textarea><br>
    <button type="submit" name='run'>Jalankan Query</button>
</form>

<?php
try {
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['run'])) {
        $sqlQuery = $_POST["sqlQuery"];

        // Validate and Sanitize Input
        $sqlQuery = validateInput($sqlQuery);

        // Run Query
        $result = $pdo->query($sqlQuery);

        if ($result === FALSE) {
            throw new Exception("Error during query execution: " . $pdo->error);
        } else {
            // Display Results in Table
?>
<h2>Hasil Query</h2>
<table class='table ' border='1'>
    <tr>
        <?php
                    $columnCount = $result->columnCount();

                    // Display Column Names as Table Headers
                    for ($i = 0; $i < $columnCount; $i++) {
                        $metaData = $result->getColumnMeta($i);
                        $columnName = $metaData['name'];
                        echo "<th>{$columnName}</th>";
                    }

                    echo "</tr>";

                    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        foreach ($row as $value) {
                            echo "<td>{$value}</td>";
                        }
                        echo "</tr>";
                    }

                    ?>
</table>
<?php
        }
    }
} catch (Exception $e) {
    // Tangkap dan tampilkan pesan kesalahan
    echo "Error: " . $e->getMessage();
}