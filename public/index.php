<?php

/**
 * Task Management System Entry Point
 */

// Define root path
define('ROOT_PATH', dirname(__DIR__));

// Load environment variables
require_once ROOT_PATH . '/app/helpers/env_helper.php';
require_once ROOT_PATH . '/app/helpers/url_helper.php';
loadEnv(ROOT_PATH . '/.env');

// Set error reporting
if (env('APP_DEBUG', true)) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Load Autoloader
require_once ROOT_PATH . '/app/core/Autoloader.php';

// Start session
session_start();

// Load Router
use App\Core\Router;

$router = new Router();

// Load routes
require_once ROOT_PATH . '/routes/web.php';
require_once ROOT_PATH . '/routes/api.php';

// Dispatch
$router->dispatch($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD']);
