<?php
$id_kuis = $_GET['id_kuis'];
$query = "select * from kuis where id_kuis='$id_kuis'";
$stmt = $pdo->query($query);
$kuis = $stmt->fetch();
if (isset($_GET['id_unik'])) {
    $id_unik = $_GET['id_unik'];
    $unique_id_2 = $id_unik;
    try {

        // Assuming $unique_id_2 is the value to be deleted

        // Using prepared statement to avoid SQL injection
        $selectQuery = "SELECT id_jawab, id_kuis FROM kuis_jawab WHERE unique_id_2 = :unique_id_2";
        $selectStmt = $pdo->prepare($selectQuery);
        $selectStmt->bindParam(':unique_id_2', $unique_id_2, PDO::PARAM_STR);
        $selectStmt->execute();

        // Fetch hasil query
        $result = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($result as $row) {
            $id_jawab = $row['id_jawab'];
            $id_kuis = $row['id_kuis'];

            $qdel_kuis_jawab = "DELETE FROM kuis_jawab WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis";
            $stmt_kuis_jawab = $pdo->prepare($qdel_kuis_jawab);
            $stmt_kuis_jawab->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
            $stmt_kuis_jawab->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
            $stmt_kuis_jawab->execute();

            // Hapus dari tabel soal_jawab
            $qdel_soal_jawab = "DELETE FROM soal_jawab WHERE id_jawab = :id_jawab AND id_kuis = :id_kuis";
            $stmt_soal_jawab = $pdo->prepare($qdel_soal_jawab);
            $stmt_soal_jawab->bindParam(':id_jawab', $id_jawab, PDO::PARAM_INT);
            $stmt_soal_jawab->bindParam(':id_kuis', $id_kuis, PDO::PARAM_INT);
            $stmt_soal_jawab->execute();
        }


        pindah($url . "index.php?menu=index&act=quiz&sub=lihat_prepost&id_kuis=$id_kuis");
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>
<h2>HASIL PRE POST - TEST</h2>
<h3>Judul : <?= $kuis['nama_kuis'] ?></h3>
<?php

$query = "
SELECT
    id_kuis,
    unique_id_2,
    nama,
    cabang,
    nik,
    MAX(CASE WHEN jenis_kuis = 'pre' THEN total_score END) AS pre_test_score,
    MAX(CASE WHEN jenis_kuis = 'pre' THEN id_jawab END) AS id_jawab_pre_test,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 1 THEN total_score END) AS post_test_1_score,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 1 THEN id_jawab END) AS id_jawab_post_test_1,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 2 THEN total_score END) AS post_test_2_score,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 2 THEN id_jawab END) AS id_jawab_post_test_2,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 3 THEN total_score END) AS post_test_3_score,
    MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 3 THEN id_jawab END) AS id_jawab_post_test_3
FROM (
    SELECT
        id_kuis,
        unique_id_2,
        nama,
        cabang,
        nik,
        jenis_kuis,
        total_score,
        id_jawab,
        ROW_NUMBER() OVER (PARTITION BY unique_id_2, jenis_kuis ORDER BY created) AS post_number
    FROM kuis_jawab
) AS numbered_data
WHERE id_kuis = '$id_kuis'
GROUP BY id_kuis, unique_id_2, nik
HAVING pre_test_score IS NOT NULL
ORDER BY pre_test_score DESC, post_test_1_score DESC, post_test_2_score DESC, post_test_3_score DESC;
";

$stmt = $pdo->query($query);
$hasilQuery = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<table class='table table-bordered'>
    <thead>
        <tr>
            <th>Cabang</th>
            <th>NIK</th>
            <th>Nama</th>
            <th>Pre-Score</th>
            <th>Post-1 Score</th>
            <th>Post-2 Score</th>
            <th>Post-3 Score</th>
            <th>Act</th>
        </tr>
    </thead>
    <tbody>

        <?php foreach ($hasilQuery as $hasil) : ?>
            <tr>
                <td><?php echo $hasil['cabang']; ?></td>
                <td><?php echo $hasil['nik']; ?></td>
                <td><?php echo proper($hasil['nama'])  ?></td>
                <td><a href="javascript:void(0)" onclick="openNewTab('<?= $hasil['id_jawab_pre_test'] ?>','<?= $id_kuis ?>')"><?php echo $hasil['pre_test_score']; ?></a>
                </td>
                <td><a href="javascript:void(0)" onclick="openNewTab('<?= $hasil['id_jawab_post_test_1'] ?>','<?= $id_kuis ?>')"><?php echo $hasil['post_test_1_score']; ?></a>
                </td>
                <td><a href="javascript:void(0)" onclick="openNewTab('<?= $hasil['id_jawab_post_test_2'] ?>','<?= $id_kuis ?>')"><?php echo $hasil['post_test_2_score']; ?></a>
                </td>
                <td><a href="javascript:void(0)" onclick="openNewTab('<?= $hasil['id_jawab_post_test_3'] ?>','<?= $id_kuis ?>')"><?php echo $hasil['post_test_3_score']; ?></a>
                </td>
                <td>
                    <?php
                    if ($superuser === 'superuser') {
                    ?>
                        <a onclick="return window.confirm('Apakah yakin untuk menghapus?')" href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_prepost&id_kuis=$id_kuis&id_unik=$hasil[unique_id_2]" ?>" class="btn btn-danger"><i class="fa fa-times"></i></a>
                    <?php
                    }
                    ?>

                </td>
            </tr>
        <?php endforeach; ?>

    </tbody>
</table>
<?php
include "./proses/quiz/analisa_quiz.php";
?>