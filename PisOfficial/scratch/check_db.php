<?php
require_once 'd:/Xampp/phpMyAdmin/htdocs/PIS/PisOfficial/src/include/config.php';
require_once 'd:/Xampp/phpMyAdmin/htdocs/PIS/PisOfficial/src/include/dbh.inc.php';

function describeTable($pdo, $tableName) {
    try {
        echo "\nTable: $tableName\n";
        $stmt = $pdo->query("DESCRIBE $tableName");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Key']} - {$row['Default']}\n";
        }
    } catch (Exception $e) {
        echo "Error describing $tableName: " . $e->getMessage() . "\n";
    }
}

describeTable($pdo, 'customers');
describeTable($pdo, 'orders');
describeTable($pdo, 'order_items');
describeTable($pdo, 'transactions');
describeTable($pdo, 'payment_tracker');
describeTable($pdo, 'notification');
describeTable($pdo, 'warehouse_logs');
describeTable($pdo, 'showroom_logs');
describeTable($pdo, 'warehouse_stocks');
describeTable($pdo, 'showroom_stocks');
