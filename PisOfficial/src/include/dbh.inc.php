<?php

$dbHost = "localhost";
$dbName = "pis-sys-db";
$dbUser = "root";
$dbPass = "";
$dbPort = "3306";

// DSN (Data Source Name)
$dsn = "mysql:host=" . $dbHost . ";port=" . $dbPort . ";dbname=" . $dbName;

try {
    // PDO (PHP Data Object)
    $pdo = new PDO($dsn, $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection Failed:" . $e->getMessage());
}
