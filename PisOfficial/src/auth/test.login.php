<?php
require_once '../include/dbh.inc.php';

$test_username = 'admin01';
$typed_password = 'adminpass'; // The one you are typing in the site

$sql = "SELECT * FROM users WHERE username = :username;";
$stmt = $pdo->prepare($sql);
$stmt->execute([':username' => $test_username]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<h2>Debug Report</h2>";
if ($user) {
    $db_hash = $user['password_hash'];
    echo "<b>Hash in DB:</b> <br><code>" . $db_hash . "</code><br>";
    echo "<b>Length in DB:</b> " . strlen($db_hash) . " characters<br><br>";

    if (strlen($db_hash) < 60) {
        echo "<span style='color:red;'>⚠️ ERROR: Your database column is too short! 
              The hash is being cut off. Change 'password_hash' column to VARCHAR(255).</span><br>";
    }

    if (password_verify($typed_password, $db_hash)) {
        echo "<span style='color:green;'>✅ SUCCESS: The password matches the hash!</span>";
    } else {
        echo "<span style='color:red;'>❌ FAIL: The password does NOT match the hash.</span>";
    }
} else {
    echo "User not found in database.";
}