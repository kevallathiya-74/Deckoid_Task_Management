<?php
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/app/core/Autoloader.php';
require_once ROOT_PATH . '/app/core/Env.php';
require_once ROOT_PATH . '/app/core/Config.php';
require_once ROOT_PATH . '/app/helpers/env_helper.php';
require_once ROOT_PATH . '/app/helpers/config_helper.php';

loadEnv(ROOT_PATH . '/.env');
loadConfig(ROOT_PATH . '/config');

use App\Core\Database;
use App\Models\DailyReportModel;

try {
    $db = Database::getInstance()->getConnection();
    $userId = $db->query("SELECT id FROM users WHERE status = 'active' LIMIT 1")->fetchColumn();
    if (!$userId) {
        throw new \Exception('No active user found in users table.');
    }

    $model = new DailyReportModel();
    $date = date('Y-m-d');
    $rows = [
        [
            'task_text' => 'Terminal save test task',
            'number_value' => '7.5',
            'row_order' => 0,
        ],
    ];

    $reportId = $model->saveReport($userId, $date, $rows);
    echo "Saved report with ID: $reportId\n";

    $data = $model->fetchReportByUserDate($userId, $date);
    echo "Loaded report for user $userId on $date:\n";
    print_r($data);
} catch (\Exception $e) {
    echo 'Test failed: ' . $e->getMessage() . "\n";
    exit(1);
}
