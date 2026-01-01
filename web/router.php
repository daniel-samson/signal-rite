<?php

/**
 * Router script for PHP built-in server.
 *
 * Serves static files directly and routes dynamic requests to app_dev.php
 *
 * Usage: php -S 0.0.0.0:8000 -t web web/router.php
 */

$path = $_SERVER['REQUEST_URI'];

// Remove query string
$path = parse_url($path, PHP_URL_PATH);

// Get the file path
$filePath = __DIR__ . $path;

// Check if this is a static file that exists
if ($path !== '/' && file_exists($filePath) && is_file($filePath)) {
    // Get the file extension
    $extension = pathinfo($filePath, PATHINFO_EXTENSION);

    // Set appropriate content type for common static files
    $mimeTypes = [
        'css'  => 'text/css',
        'js'   => 'application/javascript',
        'json' => 'application/json',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'svg'  => 'image/svg+xml',
        'ico'  => 'image/x-icon',
        'woff' => 'font/woff',
        'woff2'=> 'font/woff2',
        'ttf'  => 'font/ttf',
        'eot'  => 'application/vnd.ms-fontobject',
        'txt'  => 'text/plain',
        'xml'  => 'application/xml',
        'pdf'  => 'application/pdf',
        'html' => 'text/html',
        'htm'  => 'text/html',
    ];

    if (isset($mimeTypes[$extension])) {
        header('Content-Type: ' . $mimeTypes[$extension]);
    }

    // Return false to let PHP serve the file directly
    return false;
}

// Route to app_dev.php for dynamic requests
$_SERVER['SCRIPT_FILENAME'] = __DIR__ . '/app_dev.php';
$_SERVER['SCRIPT_NAME'] = '/app_dev.php';

require __DIR__ . '/app_dev.php';
