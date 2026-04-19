<?php
// Only output if session user_id exists
if (isset($_SESSION['user_id'])) {
    $sessionData = [
        'userId' => (int)$_SESSION['user_id'],
        'cartEndpoint' => '../include/global.ctrl.php'
    ];
    echo '<div id="session-data" class="hidden" data-session="' . htmlspecialchars(json_encode($sessionData), ENT_QUOTES, 'UTF-8') . '"></div>';
}
?>
