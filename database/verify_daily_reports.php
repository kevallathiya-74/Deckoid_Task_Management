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
$db = Database::getInstance()->getConnection();

typeCheck:
$tables = ['daily_reports', 'daily_report_rows'];
foreach ($tables as $table) {
    $stmt = $db->query("SHOW TABLES LIKE '$table'");
    $result = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "$table: " . (count($result) ? 'exists' : 'missing') . PHP_EOL;
}
