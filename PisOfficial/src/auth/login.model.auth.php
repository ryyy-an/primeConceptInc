<?php

// This model handle every sql Queries

declare(strict_types=1);

function get_user(object $pdo, string $username)
{
    $sql = "SELECT * FROM users WHERE username = :username;";
    $pst = $pdo->prepare($sql);
    $pst->bindParam(":username", $username);
    $pst->execute();

    $result = $pst->fetch(PDO::FETCH_ASSOC);
    return $result;
}