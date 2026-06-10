<?php
require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/core/Database.php';

session_start();
$_SESSION['user_role'] = 'admin';
$_SESSION['user_id'] = '1';

// Mock get
$_GET['draw'] = 1;
$_GET['start'] = 0;
$_GET['length'] = 10;
$_GET['search'] = ['value' => ''];

require_once __DIR__ . '/app/models/Task.php';
require_once __DIR__ . '/app/models/Project.php';
require_once __DIR__ . '/app/models/User.php';
require_once __DIR__ . '/app/models/Role.php';
require_once __DIR__ . '/app/middleware/AuthMiddleware.php';
require_once __DIR__ . '/app/controllers/TaskController.php';

$controller = new \App\Controllers\TaskController();
$controller->list();
