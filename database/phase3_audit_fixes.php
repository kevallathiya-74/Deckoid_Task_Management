<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=deckoid_task_management", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Starting Phase 3 Database Audit Fixes...\n";

    // Drop unused activity_logs table
    $db->exec("DROP TABLE IF EXISTS activity_logs;");
    echo "Dropped activity_logs table.\n";

    // Drop unused role column from users table
    try {
        $db->exec("ALTER TABLE users DROP COLUMN role;");
        echo "Dropped role column from users table.\n";
    } catch(PDOException $e) {
        echo "role column might not exist or already dropped: " . $e->getMessage() . "\n";
    }

    echo "Phase 3 Database Audit Fixes Complete.\n";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
