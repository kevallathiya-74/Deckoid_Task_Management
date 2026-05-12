<?php

/**
 * Database Initialization Script
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
}

require_once ROOT_PATH . '/app/helpers/env_helper.php';
loadEnv(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/app/core/Autoloader.php';

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();
    
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
