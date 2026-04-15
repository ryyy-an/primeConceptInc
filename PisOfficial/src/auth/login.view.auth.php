<?php

declare(strict_types=1);

function get_error_messages()
{
    if (isset($_SESSION['login_errors'])) {
        $errors = $_SESSION['login_errors'];
        $firstError = reset($errors); // gets the first value in the array

        unset($_SESSION['login_errors']);
        return htmlspecialchars($firstError);
    }

    return "";
}
