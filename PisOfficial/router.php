<?php
// router.php - Simplifies routing for PHP's built-in development server (php -S)

// Get the requested URI without query strings
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Decode the path
$path = urldecode($uri);

// If the requested path exists, let the built-in server serve it
if (file_exists(__DIR__ . $path)) {
    return false; // serve the requested resource or directory as-is
}

// If the resource wasn't found, load our custom 404 page
require __DIR__ . '/public/404.php';
