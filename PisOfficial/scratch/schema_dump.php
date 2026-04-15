<?php
require_once 'src/include/dbh.inc.php';
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "TABLES:\n";
foreach ($tables as $t) {
    echo "- $t\n";
    $stmtC = $pdo->query("DESCRIBE `$t` ");
    $cols = $stmtC->fetchAll(PDO::FETCH_ASSOC);
    foreach ($cols as $c) {
        echo "  - {$c['Field']} ({$c['Type']})\n";
    }
}
