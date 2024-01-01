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
        $sql = "DELETE FROM kuis_jawab WHERE unique_id_2 = :unique_id_2";
        $stmt = $pdo->prepare($sql);

        // Bind the parameter
        $stmt->bindParam(':unique_id_2', $unique_id_2, PDO::PARAM_STR);

        // Execute the statement
        $stmt->execute();

        // Check if the deletion was successful
        if ($stmt->rowCount() > 0) {
            echo "Record(s) deleted successfully.";
        } else {
            echo "No records deleted.";
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
        unique_id_2,
        id_kuis,
        nama,
        cabang,nik,
        MAX(CASE WHEN jenis_kuis = 'pre' THEN total_score END) AS pre_test_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 1 THEN total_score END) AS post_test_1_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 2 THEN total_score END) AS post_test_2_score,
        MAX(CASE WHEN jenis_kuis = 'post' AND post_number = 3 THEN total_score END) AS post_test_3_score
    FROM (
        SELECT 
            unique_id_2,
            id_kuis,
            nama,
            cabang,
            nik,
            jenis_kuis,
            total_score,
            ROW_NUMBER() OVER (PARTITION BY unique_id_2, jenis_kuis ORDER BY created) AS post_number
        FROM kuis_jawab
    ) AS numbered_data
    where id_kuis='$id_kuis'
    GROUP BY unique_id_2 
    order by nik, pre_test_score DESC, post_test_1_score DESC, post_test_2_score DESC, post_test_3_score DESC;;
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
            <td><?php echo $hasil['nama']; ?></td>
            <td><?php echo $hasil['pre_test_score']; ?></td>
            <td><?php echo $hasil['post_test_1_score']; ?></td>
            <td><?php echo $hasil['post_test_2_score']; ?></td>
            <td><?php echo $hasil['post_test_3_score']; ?></td>
            <td>
                <?php 
                    if($superuser==='superuser'){
                        ?>
                <a onclick="return window.confirm('Apakah yakin untuk menghapus?')"
                    href="<?= $url . "index.php?menu=index&act=quiz&sub=lihat_prepost&id_kuis=$id_kuis&id_unik=$hasil[unique_id_2]" ?>"
                    class="btn btn-danger"><i class="fa fa-times"></i></a>
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