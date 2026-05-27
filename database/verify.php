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

echo "=== Publishing Tables Verification ===\n";
$tables = $db->query("SHOW TABLES LIKE 'publishing_%'")->fetchAll(PDO::FETCH_COLUMN);
echo "Tables found: " . implode(', ', $tables) . "\n\n";

echo "publishing_tables columns:\n";
foreach ($db->query("DESCRIBE publishing_tables")->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

echo "\npublishing_rows columns:\n";
foreach ($db->query("DESCRIBE publishing_rows")->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

echo "\npublishing_assignments columns:\n";
foreach ($db->query("DESCRIBE publishing_assignments")->fetchAll(PDO::FETCH_ASSOC) as $col) {
    echo "  - {$col['Field']} ({$col['Type']})\n";
}

echo "\n=== Users in DB ===\n";
$users = $db->query("SELECT id, full_name, role FROM users WHERE deleted_at IS NULL LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
foreach ($users as $u) {
    echo "  - [{$u['role']}] {$u['full_name']} ({$u['id']})\n";
}

echo "\n=== ALL OK ===\n";
