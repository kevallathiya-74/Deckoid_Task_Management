<?php
try {
    $db = new PDO("mysql:host=127.0.0.1;dbname=deckoid_task_management", "root", "");
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // projects table was already altered in a previous run

    // Drop fields from tasks table
    $db->exec("ALTER TABLE tasks DROP FOREIGN KEY tasks_ibfk_3;");
    $db->exec("ALTER TABLE tasks DROP COLUMN role_id;");
    $db->exec("ALTER TABLE tasks DROP COLUMN progress_percentage;");
    $db->exec("ALTER TABLE tasks DROP COLUMN status_notes;");
    echo "Successfully dropped role_id, progress_percentage, status_notes from tasks table.\n";

    // Drop task_departments table
    $db->exec("DROP TABLE IF EXISTS task_departments;");
    echo "Successfully dropped task_departments table.\n";

    // Add completion fields to tasks table
    $db->exec("ALTER TABLE tasks ADD COLUMN completed_by CHAR(36) NULL;");
    $db->exec("ALTER TABLE tasks ADD COLUMN completion_notes TEXT NULL;");
    $db->exec("ALTER TABLE tasks ADD CONSTRAINT fk_tasks_completed_by FOREIGN KEY (completed_by) REFERENCES users(id) ON DELETE SET NULL;");
    echo "Successfully added completed_by and completion_notes to tasks table.\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
