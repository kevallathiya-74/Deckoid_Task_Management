-- Migration: Create daily reports tables for staff reports
-- This migration defines the report header table and the row table.

CREATE TABLE IF NOT EXISTS daily_reports (
    id CHAR(36) PRIMARY KEY,
    user_id CHAR(36) NOT NULL,
    report_date DATE NOT NULL,
    total_tasks INT DEFAULT 0,
    total_value DECIMAL(12,4) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY unique_user_date (user_id, report_date),
    INDEX idx_daily_reports_user_id (user_id),
    INDEX idx_daily_reports_report_date (report_date),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS daily_report_rows (
    id CHAR(36) PRIMARY KEY,
    report_id CHAR(36) NOT NULL,
    task_text TEXT NULL,
    number_value DECIMAL(12,4) NULL,
    row_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (report_id) REFERENCES daily_reports(id) ON DELETE CASCADE,
    INDEX idx_daily_report_rows_report_id (report_id)
) ENGINE=InnoDB;
