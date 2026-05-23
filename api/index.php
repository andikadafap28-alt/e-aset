<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Tweak for Vercel Serverless Read-Only Filesystem
// Create required directories in /tmp
if (!is_dir('/tmp/storage/framework/views')) {
    mkdir('/tmp/storage/framework/views', 0755, true);
}
if (!is_dir('/tmp/storage/framework/cache/data')) {
    mkdir('/tmp/storage/framework/cache/data', 0755, true);
}
if (!is_dir('/tmp/storage/framework/sessions')) {
    mkdir('/tmp/storage/framework/sessions', 0755, true);
}
if (!is_dir('/tmp/storage/logs')) {
    mkdir('/tmp/storage/logs', 0755, true);
}

// Register the Composer autoloader
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// Force Laravel to use /tmp for its storage directory
$app->useStoragePath('/tmp/storage');

// Handle the Request
$app->handleRequest(Request::capture());
