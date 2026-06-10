<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/app/core/Database.php';
require_once __DIR__ . '/app/models/Task.php';

$taskModel = new \App\Models\Task();

try {
    $filters = [
        'project_id' => null,
        'assigned_to' => null,
        'status' => null
    ];
    $search = '';
    $length = 10;
    $start = 0;

    $tasks = $taskModel->listAll($filters, $search, $length, $start);
    $recordsFiltered = $taskModel->countAll($filters, $search);
    $recordsTotal = $taskModel->countAll($filters, '');

    echo "SUCCESS\n";
    echo "recordsFiltered: $recordsFiltered\n";
    echo "recordsTotal: $recordsTotal\n";
    // echo json_encode($tasks);
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
