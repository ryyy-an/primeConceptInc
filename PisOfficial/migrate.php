<?php
$dbHost = "localhost";
$dbPort = "3306";
$dbName = "pis-sys-db";
$dbUser = "root";
$dbPass = "";

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::MYSQL_ATTR_MULTI_STATEMENTS => true
    ]);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Read the SQL dump safely
    $sql = file_get_contents(__DIR__ . '/resources/pis-sys-db.sql');
    
    // Execute all queries at once
    $pdo->exec($sql);
    echo "SUCCESS: The database was migrated completely to the local pis-sys-db database!";
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
