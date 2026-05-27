<?php

/**
 * Database Initialization Script
 * Safely creates all necessary database tables
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
    
    echo "[INFO] Starting database initialization...\n";
    
    // Create database if it doesn't exist
    $dbName = config('database.connections.mysql.database', 'deckoid_task_management');
    $createDbSql = "CREATE DATABASE IF NOT EXISTS " . $dbName . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;";
    $db->exec($createDbSql);
    echo "[SUCCESS] Database '{$dbName}' ready\n";
    
    // Use the database
    $db->exec("USE " . $dbName . ";");
    
    // Disable foreign key checks temporarily
    $db->exec("SET FOREIGN_KEY_CHECKS = 0;");
    echo "[INFO] Foreign key checks disabled\n";
    
    // Read and execute schema
    $schemaFile = ROOT_PATH . '/database/schema.sql';
    if (!file_exists($schemaFile)) {
        throw new Exception("Schema file not found: {$schemaFile}");
    }
    
    $sql = file_get_contents($schemaFile);
    
    // Split and execute queries individually to handle multiple statements
    $queries = array_filter(array_map('trim', preg_split('/;(?=\s*$)/', $sql, -1, PREG_SPLIT_NO_EMPTY)), function($q) {
        return !empty($q) && !preg_match('/^--/', $q);
    });
    
    $tableCount = 0;
    foreach ($queries as $query) {
        if (!empty(trim($query))) {
            try {
                $db->exec($query);
                if (preg_match('/CREATE TABLE/i', $query)) {
                    $tableCount++;
                }
            } catch (\Exception $e) {
                // Log but continue - some tables may already exist
                echo "[WARN] Query execution: " . substr($e->getMessage(), 0, 100) . "...\n";
            }
        }
    }
    
    // Re-enable foreign key checks
    $db->exec("SET FOREIGN_KEY_CHECKS = 1;");
    echo "[SUCCESS] Foreign key checks re-enabled\n";
    
    echo "[SUCCESS] Database schema initialized! ({$tableCount} tables created/verified)\n";
    
    // Run Admin Seeder if exists
    $seederFile = ROOT_PATH . '/database/seeders/admin_seeder.php';
    if (file_exists($seederFile)) {
        echo "[INFO] Running admin seeder...\n";
        require_once $seederFile;
        echo "[SUCCESS] Admin seeder completed\n";
    }
    
} catch (\Exception $e) {
    echo "[ERROR] Database initialization failed: " . $e->getMessage() . "\n";
    exit(1);
}
