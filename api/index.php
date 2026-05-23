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

// Force Laravel to write bootstrap cache files to /tmp (Vercel has read-only filesystem except /tmp)
$cachePath = '/tmp/storage/bootstrap/cache';
if (!is_dir($cachePath)) {
    mkdir($cachePath, 0755, true);
}

$_ENV['APP_SERVICES_CACHE'] = $cachePath . '/services.php';
$_ENV['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';
$_ENV['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
$_ENV['APP_ROUTES_CACHE'] = $cachePath . '/routes-v7.php';
$_ENV['APP_EVENTS_CACHE'] = $cachePath . '/events.php';
putenv('APP_SERVICES_CACHE=' . $_ENV['APP_SERVICES_CACHE']);
putenv('APP_PACKAGES_CACHE=' . $_ENV['APP_PACKAGES_CACHE']);
putenv('APP_CONFIG_CACHE=' . $_ENV['APP_CONFIG_CACHE']);
putenv('APP_ROUTES_CACHE=' . $_ENV['APP_ROUTES_CACHE']);
putenv('APP_EVENTS_CACHE=' . $_ENV['APP_EVENTS_CACHE']);

// Handle the Request
$response = $app->handleRequest(Request::capture());
$response->send();
