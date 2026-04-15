<?php
/**
 * Redirect root requests to the public entry point.
 * This prevents the "403 Forbidden" error when visiting the project root.
 */
header("Location: public/index.php");
exit;
