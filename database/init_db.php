<?php

/**
 * Database Initialization Script
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/app/core/Autoloader.php';
require_once ROOT_PATH . '/app/core/Env.php';
require_once ROOT_PATH . '/app/core/Config.php';

// Load helpers
require_once ROOT_PATH . '/app/helpers/env_helper.php';
require_once ROOT_PATH . '/app/helpers/config_helper.php';

// Load environment variables
loadEnv(ROOT_PATH . '/.env');

// Load configurations
loadConfig(ROOT_PATH . '/config');

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
    // Drop old tables first to avoid column mismatch
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    $db->exec("DROP TABLE IF EXISTS publishing_cells;");
    $db->exec("DROP TABLE IF EXISTS publishing_rows;");
    $db->exec("DROP TABLE IF EXISTS publishing_sections;");
    $db->exec("DROP TABLE IF EXISTS publishing_reports;");
    $db->exec("DROP TABLE IF EXISTS publishing_assignments;");
    $db->exec("DROP TABLE IF EXISTS publishing_tables;");
    $db->exec("DROP TABLE IF EXISTS daily_report_rows;");
    $db->exec("DROP TABLE IF EXISTS daily_reports;");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    
    // Read schema.sql
    $sql = file_get_contents(ROOT_PATH . '/database/schema.sql');
    
    // Execute multiple queries
    $db->exec($sql);
    
    echo "Database schema initialized successfully!\n";
    
    // Run Admin Seeder
    require_once ROOT_PATH . '/database/seeders/admin_seeder.php';
    
} catch (\Exception $e) {
    echo "Error initializing database: " . $e->getMessage() . "\n";
}
