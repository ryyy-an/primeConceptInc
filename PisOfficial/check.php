<?php
$dbHost = "localhost";
$dbPort = "3306";
$dbName = "pis-sys-db";
$dbUser = "root";
$dbPass = "";

$dsn = "mysql:host=$dbHost;port=$dbPort;dbname=$dbName";
try {
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Test the database by listing tables!
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h1>Database Connection Test</h1>";
    echo "Host: " . $dbHost . "<br>";
    echo "Database: " . $dbName . "<br><br>";
    
    if (count($tables) > 0) {
        echo "<h2 style='color:green;'>SUCCESS: Found " . count($tables) . " tables online!</h2>";
        echo "<ul>";
        foreach ($tables as $table) {
            echo "<li>" . htmlspecialchars($table) . "</li>";
        }
        echo "</ul>";
        echo "<p>Your data is officially live on the mainline Railway server!</p>";
    } else {
        echo "<h2 style='color:red;'>ERROR: Database is completely empty.</h2>";
    }

} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage();
}
