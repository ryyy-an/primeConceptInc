<?php
// Only output if session user_id exists
if (isset($_SESSION['user_id'])) {
    echo '<script>';
    echo 'const SESSION_USER_ID = ' . json_encode((int)$_SESSION['user_id']) . ';';

    // Point all POS cart endpoints globally
    echo 'const CART_ENDPOINT = "../include/global.ctrl.php";';

    echo '</script>';
}
