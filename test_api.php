<?php
$_SERVER['REQUEST_URI'] = '/api/tasks';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['SCRIPT_NAME'] = '/index.php';
$_GET['draw'] = 1;
$_GET['start'] = 0;
$_GET['length'] = 10;
$_GET['search'] = ['value' => ''];

// Mock session
session_start();
$_SESSION['user_id'] = '1';
$_SESSION['user_role'] = 'admin';

require_once __DIR__ . '/public/index.php';
