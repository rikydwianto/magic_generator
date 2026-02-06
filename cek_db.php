<?php
require 'vendor/autoload.php';
require 'proses/global_fungsi.php';
include_once "./config/setting.php";
include_once "./config/koneksi.php";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Database Analyzer</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table-wrapper { margin: 20px 0; }
        .index-missing { background-color: #fff3cd; }
        .index-exists { background-color: #d1e7dd; }
        pre { background: #f8f9fa; padding: 10px; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="container-fluid py-4">
        <h1 class="mb-4">🔍 Database Analyzer & Index Checker</h1>

        <?php
        try {
            // Test koneksi
            echo "<div class='alert alert-success'>";
            echo "✅ <strong>Database Connection:</strong> OK<br>";
            echo "📊 <strong>Database:</strong> " . $pdo->query('SELECT DATABASE()')->fetchColumn();
            echo "</div>";

            // Get all tables
            $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
            
            echo "<h2>📋 Tables Summary</h2>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-bordered table-striped'>";
            echo "<thead class='table-dark'><tr><th>Table Name</th><th>Rows</th><th>Engine</th><th>Collation</th><th>Size</th></tr></thead>";
            echo "<tbody>";
            
            foreach ($tables as $table) {
                $info = $pdo->query("SHOW TABLE STATUS LIKE '$table'")->fetch(PDO::FETCH_ASSOC);
                $rows = number_format($info['Rows']);
                $size = round(($info['Data_length'] + $info['Index_length']) / 1024 / 1024, 2) . ' MB';
                echo "<tr>";
                echo "<td><strong>$table</strong></td>";
                echo "<td>$rows</td>";
                echo "<td>{$info['Engine']}</td>";
                echo "<td>{$info['Collation']}</td>";
                echo "<td>$size</td>";
                echo "</tr>";
            }
            echo "</tbody></table></div>";

            // Analyze specific important tables
            $importantTables = ['deliquency', 'log_cek_par', 'users', 'block'];
            
            foreach ($importantTables as $tableName) {
                if (!in_array($tableName, $tables)) continue;
                
                echo "<div class='table-wrapper'>";
                echo "<h3>🔎 Table: <code>$tableName</code></h3>";
                
                // Get table structure
                echo "<h5>Structure:</h5>";
                echo "<div class='table-responsive'>";
                echo "<table class='table table-sm table-bordered'>";
                echo "<thead class='table-secondary'><tr><th>Column</th><th>Type</th><th>Null</th><th>Key</th><th>Default</th><th>Extra</th></tr></thead>";
                echo "<tbody>";
                
                $columns = $pdo->query("DESCRIBE $tableName")->fetchAll(PDO::FETCH_ASSOC);
                foreach ($columns as $col) {
                    echo "<tr>";
                    echo "<td><strong>{$col['Field']}</strong></td>";
                    echo "<td>{$col['Type']}</td>";
                    echo "<td>{$col['Null']}</td>";
                    echo "<td>" . ($col['Key'] ? "<span class='badge bg-primary'>{$col['Key']}</span>" : "-") . "</td>";
                    echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
                    echo "<td>" . ($col['Extra'] ?: "-") . "</td>";
                    echo "</tr>";
                }
                echo "</tbody></table></div>";
                
                // Get indexes
                echo "<h5>Indexes:</h5>";
                $indexes = $pdo->query("SHOW INDEX FROM $tableName")->fetchAll(PDO::FETCH_ASSOC);
                
                if ($indexes) {
                    echo "<div class='table-responsive'>";
                    echo "<table class='table table-sm table-bordered'>";
                    echo "<thead class='table-info'><tr><th>Index Name</th><th>Column</th><th>Unique</th><th>Type</th><th>Cardinality</th></tr></thead>";
                    echo "<tbody>";
                    
                    foreach ($indexes as $idx) {
                        echo "<tr class='index-exists'>";
                        echo "<td><strong>{$idx['Key_name']}</strong></td>";
                        echo "<td>{$idx['Column_name']}</td>";
                        echo "<td>" . ($idx['Non_unique'] == 0 ? 'Yes' : 'No') . "</td>";
                        echo "<td>{$idx['Index_type']}</td>";
                        echo "<td>" . number_format($idx['Cardinality']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<div class='alert alert-warning'>⚠️ No indexes found</div>";
                }
                
                // Suggest indexes for deliquency table
                if ($tableName == 'deliquency') {
                    echo "<h5>💡 Index Recommendations:</h5>";
                    echo "<div class='alert alert-info'>";
                    echo "<strong>Recommended indexes untuk performa optimal:</strong>";
                    echo "<pre>";
                    echo "-- Index untuk pencarian berdasarkan tanggal dan cabang\n";
                    echo "CREATE INDEX idx_tgl_input_cabang ON deliquency(tgl_input, cabang);\n\n";
                    echo "-- Index untuk pencarian berdasarkan session\n";
                    echo "CREATE INDEX idx_session ON deliquency(session);\n\n";
                    echo "-- Index untuk pencarian berdasarkan staff\n";
                    echo "CREATE INDEX idx_staff ON deliquency(staff);\n\n";
                    echo "-- Index untuk pencarian berdasarkan no_center\n";
                    echo "CREATE INDEX idx_no_center ON deliquency(no_center);\n\n";
                    echo "-- Index untuk pencarian berdasarkan loan\n";
                    echo "CREATE INDEX idx_loan ON deliquency(loan);\n\n";
                    echo "-- Composite index untuk query yang sering digunakan\n";
                    echo "CREATE INDEX idx_cabang_tgl_session ON deliquency(cabang, tgl_input, session);";
                    echo "</pre>";
                    
                    // Check if indexes already exist
                    $existingIndexes = array_column($indexes, 'Key_name');
                    $suggestedIndexes = [
                        'idx_tgl_input_cabang' => "CREATE INDEX idx_tgl_input_cabang ON deliquency(tgl_input, cabang);",
                        'idx_session' => "CREATE INDEX idx_session ON deliquency(session);",
                        'idx_staff' => "CREATE INDEX idx_staff ON deliquency(staff);",
                        'idx_no_center' => "CREATE INDEX idx_no_center ON deliquency(no_center);",
                        'idx_loan' => "CREATE INDEX idx_loan ON deliquency(loan);",
                        'idx_cabang_tgl_session' => "CREATE INDEX idx_cabang_tgl_session ON deliquency(cabang, tgl_input, session);"
                    ];
                    
                    echo "<h6>Status Index yang Disarankan:</h6>";
                    echo "<ul class='list-group'>";
                    foreach ($suggestedIndexes as $idxName => $query) {
                        $exists = in_array($idxName, $existingIndexes);
                        $class = $exists ? 'list-group-item-success' : 'list-group-item-warning';
                        $icon = $exists ? '✅' : '❌';
                        echo "<li class='list-group-item $class'>$icon <strong>$idxName</strong> - " . ($exists ? "Sudah ada" : "Belum ada") . "</li>";
                    }
                    echo "</ul>";
                    
                    echo "<form method='post' class='mt-3'>";
                    echo "<button type='submit' name='create_indexes' class='btn btn-primary'>🚀 Create Missing Indexes</button>";
                    echo "</form>";
                    echo "</div>";
                }
                
                // Row count
                $count = $pdo->query("SELECT COUNT(*) FROM $tableName")->fetchColumn();
                echo "<p class='text-muted'>Total Records: <strong>" . number_format($count) . "</strong></p>";
                
                echo "</div><hr>";
            }
            
            // Create indexes if requested
            if (isset($_POST['create_indexes'])) {
                echo "<div class='alert alert-primary'>";
                echo "<h4>Creating Indexes...</h4>";
                
                $indexes = $pdo->query("SHOW INDEX FROM deliquency")->fetchAll(PDO::FETCH_ASSOC);
                $existingIndexes = array_column($indexes, 'Key_name');
                
                $suggestedIndexes = [
                    'idx_tgl_input_cabang' => "CREATE INDEX idx_tgl_input_cabang ON deliquency(tgl_input, cabang)",
                    'idx_session' => "CREATE INDEX idx_session ON deliquency(session)",
                    'idx_staff' => "CREATE INDEX idx_staff ON deliquency(staff)",
                    'idx_no_center' => "CREATE INDEX idx_no_center ON deliquency(no_center)",
                    'idx_loan' => "CREATE INDEX idx_loan ON deliquency(loan)",
                    'idx_cabang_tgl_session' => "CREATE INDEX idx_cabang_tgl_session ON deliquency(cabang, tgl_input, session)"
                ];
                
                foreach ($suggestedIndexes as $idxName => $query) {
                    if (!in_array($idxName, $existingIndexes)) {
                        try {
                            $pdo->exec($query);
                            echo "✅ Created: <code>$idxName</code><br>";
                        } catch (PDOException $e) {
                            echo "❌ Failed to create <code>$idxName</code>: " . $e->getMessage() . "<br>";
                        }
                    } else {
                        echo "⏭️ Skipped: <code>$idxName</code> (already exists)<br>";
                    }
                }
                
                echo "<p class='mt-3'><a href='cek_db.php' class='btn btn-success'>Refresh Page</a></p>";
                echo "</div>";
            }
            
            // Show slow queries or problematic queries
            echo "<h2>⚡ Query Analysis</h2>";
            echo "<div class='alert alert-warning'>";
            echo "<h5>Contoh Query yang Sering Digunakan:</h5>";
            echo "<pre>";
            echo "-- Query 1: Cek PAR berdasarkan cabang dan tanggal\n";
            echo "EXPLAIN SELECT * FROM deliquency WHERE tgl_input='2024-01-01' AND cabang='JAKARTA';\n\n";
            echo "-- Query 2: Cek berdasarkan staff\n";
            echo "EXPLAIN SELECT DISTINCT staff FROM deliquency WHERE tgl_input='2024-01-01' AND cabang='JAKARTA' AND session='sesi1';\n\n";
            echo "-- Query 3: Update staff berdasarkan no_center\n";
            echo "EXPLAIN UPDATE deliquency SET staff='NAMA' WHERE no_center='001' AND tgl_input='2024-01-01' AND cabang='JAKARTA';";
            echo "</pre>";
            echo "<p class='text-muted'>Gunakan EXPLAIN sebelum query untuk melihat apakah index digunakan dengan baik.</p>";
            echo "</div>";
            
        } catch (PDOException $e) {
            echo "<div class='alert alert-danger'>";
            echo "❌ <strong>Database Error:</strong> " . $e->getMessage();
            echo "</div>";
        }
        ?>
        
        <div class="mt-4">
            <a href="index.php" class="btn btn-secondary">← Back to Main</a>
        </div>
    </div>
</body>
</html>
