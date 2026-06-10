<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=deckoid_task_management", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Add completion fields to tasks table
    try {
        $db->exec("ALTER TABLE tasks ADD COLUMN completed_by CHAR(36) NULL;");
        $db->exec("ALTER TABLE tasks ADD COLUMN completion_notes TEXT NULL;");
        $db->exec("ALTER TABLE tasks ADD CONSTRAINT fk_tasks_completed_by FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL;");
        echo "Successfully added completed_by and completion_notes to tasks table.\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate column') !== false) {
            echo "Columns already exist.\n";
        } else {
            throw $e;
        }
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
