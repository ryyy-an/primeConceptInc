<?php

declare(strict_types=1);

function is_input_empty(string $username, string $password)
{
    if (empty($username) || empty($password)) {
        return true;
    } else {
        return false;
    }
}

function is_username_wrong(bool | array $result)
{
    if (!$result) {
        return true;
    } else {
        return false;
    }
}

function is_password_wrong(string $password, string $dbpassword, object $pdo, string $username)
{
    // 1. First, check if it's already a secure hash
    if (password_verify($password, $dbpassword)) {
        return false; // Password is correct
    }

    // 2. FALLBACK: Check if it's still plain text (like 'adminpass')
    if ($password === $dbpassword) {
        // Automatically upgrade this user to a secure hash now
        $newHash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "UPDATE users SET password_hash = :newHash WHERE username = :username";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['newHash' => $newHash, 'username' => $username]);

        return false; // Password is correct (and now secured!)
    }

    return true; // Neither matched
}

function check_for_account_type(PDO $pdo, string $account_type)
{
    // Update the status to online (1) for the logged-in user
    if (isset($_SESSION['user_id'])) {
        $stmt = $pdo->prepare("UPDATE users SET is_online = 1 WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
    }

    // Set splash screen flag
    $_SESSION['login_success_splash'] = true;

    switch ($account_type) {
        case 'admin':
            header("Location: ../-admin/dashboard-page.php");
            exit;
        case 'showroom':
            header("Location: ../-showroom/home-page.php");
            exit;
        case 'warehouse':
            header("Location: ../-warehouse/dashboard-page.php");
            exit;
        default:
            header("Location: ../../public/index.php");
            exit;
    }
}
