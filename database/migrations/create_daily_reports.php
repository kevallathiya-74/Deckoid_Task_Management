<?php
/**
 * Migration: create_daily_reports.php
 * Run: php database/migrations/create_daily_reports.php
 */

// Determine project root reliably (this file is in database/migrations)
if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__, 2));
}

require_once ROOT_PATH . '/app/core/Autoloader.php';
require_once ROOT_PATH . '/app/core/Env.php';
require_once ROOT_PATH . '/app/core/Config.php';
require_once ROOT_PATH . '/app/helpers/env_helper.php';
require_once ROOT_PATH . '/app/helpers/config_helper.php';

loadEnv(ROOT_PATH . '/.env');
loadConfig(ROOT_PATH . '/config');

use App\Core\Database;

try {
    $db = Database::getInstance()->getConnection();

    // NOTE: MySQL performs implicit commits for DDL (CREATE TABLE),
    // so wrapping DDL in transactions can cause "no active transaction" errors.
    // We'll execute DDL statements directly without an explicit transaction.

    $sql1 = <<<SQL
CREATE TABLE IF NOT EXISTS daily_reports (
  id CHAR(36) PRIMARY KEY,
  user_id CHAR(36) NOT NULL,
  report_date DATE NOT NULL,
  total_tasks INT DEFAULT 0,
  total_value DECIMAL(12,4) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  UNIQUE KEY unique_user_date (user_id, report_date),
  INDEX idx_report_date (report_date),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    $sql2 = <<<SQL
CREATE TABLE IF NOT EXISTS daily_report_rows (
  id CHAR(36) PRIMARY KEY,
  report_id CHAR(36) NOT NULL,
  task_text TEXT NULL,
  number_value DECIMAL(12,4) NULL,
  row_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (report_id) REFERENCES daily_reports(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL;

    try {
        $db->exec($sql1);
        $db->exec($sql2);
        echo "Migration completed: daily_reports and daily_report_rows are present.\n";
    } catch (\PDOException $inner) {
        // If table already exists or another DDL issue occurs, show details
        echo "DDL execution error: " . $inner->getMessage() . "\n";
        throw $inner;
    }
} catch (\Exception $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
