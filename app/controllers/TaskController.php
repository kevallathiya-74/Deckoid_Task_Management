<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
use App\Models\TaskAlert;
use App\Middleware\AuthMiddleware;

class TaskController
{
    protected $taskModel;
    protected $projectModel;
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->taskModel = new Task();
        $this->projectModel = new Project();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    public function index()
    {
        try {
            $title = 'Task Management';
            $active_page = 'tasks';
            
            $projects = $this->projectModel->listAll();
            $staff = $this->userModel->listAll();
            $roles = $this->roleModel->all();

            require_once ROOT_PATH . '/app/views/layouts/header.php';
            require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
            require_once ROOT_PATH . '/app/views/tasks/index.php';
            require_once ROOT_PATH . '/app/views/layouts/footer.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            die("Error loading tasks page: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
        }
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        try {
            $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
            $filters = [
                'project_id' => $_GET['project_id'] ?? null,
                'assigned_to' => $isAdminOrManager ? ($_GET['assigned_to'] ?? null) : $_SESSION['user_id'],
                'status' => $_GET['status'] ?? null
            ];

            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : null;
            $search = $_GET['search']['value'] ?? '';

            $tasks = $this->taskModel->listAll($filters, $search, $length, $start);
            $recordsFiltered = $this->taskModel->countAll($filters, $search);
            $recordsTotal = $this->taskModel->countAll($filters, '');
            
            echo json_encode([
                'status' => 'success',
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        $tasksData = $_POST['tasks'] ?? [];

        if (empty($tasksData) || !is_array($tasksData)) {
            echo json_encode(['status' => 'error', 'message' => 'No task data provided']);
            return;
        }

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $db->beginTransaction();

            $createdCount = 0;
            foreach ($tasksData as $index => $rawTask) {
                $data = [
                    'project_id' => $rawTask['project_id'] ?? '',
                    'assigned_to' => $rawTask['assigned_to'] ?? '',
                    'assigned_users' => $rawTask['assigned_users'] ?? [],
                    'title' => trim($rawTask['title'] ?? ''),
                    'description' => trim($rawTask['description'] ?? ''),
                    'due_date' => (!empty($rawTask['due_date'])) ? $rawTask['due_date'] . ' ' . ($rawTask['due_time'] ?? '09:00') : date('Y-m-d H:i:s'),
                    'due_time' => $rawTask['due_time'] ?? '09:00',
                    'priority' => $rawTask['priority'] ?? 'medium',
                    'status' => $rawTask['status'] ?? 'pending'
                ];

                if (isset($data['project_id']) && $data['project_id'] === '') {
                    $data['project_id'] = null;
                }

                if ((empty($data['assigned_to']) && empty($data['assigned_users'])) || empty($data['title'])) {
                    echo json_encode(['status' => 'validation_error', 'message' => "Task #" . ($index + 1) . ": Assignee and Title are required"]);
                    $db->rollBack();
                    return;
                }

                // Verify existence if project_id is provided
                if (!empty($data['project_id'])) {
                    if (!$this->projectModel->findById($data['project_id'])) {
                        echo json_encode(['status' => 'validation_error', 'message' => "Task #" . ($index + 1) . ": Invalid project selected"]);
                        $db->rollBack();
                        return;
                    }
                }
                
                $assignedUsers = $data['assigned_users'];
                if (empty($assignedUsers) && !empty($data['assigned_to'])) {
                    $assignedUsers = [$data['assigned_to']];
                }
                
                foreach ($assignedUsers as $userId) {
                    if (!$this->userModel->findById($userId)) {
                        echo json_encode(['status' => 'validation_error', 'message' => "Task #" . ($index + 1) . ": Invalid assignee selected"]);
                        $db->rollBack();
                        return;
                    }
                }

                if (!$this->taskModel->create($data)) {
                    throw new \Exception("Task #" . ($index + 1) . ": Failed to create task record");
                }
                $createdCount++;
            }

            $db->commit();
            echo json_encode([
                'status' => 'success', 
                'message' => $createdCount . ' task(s) created successfully'
            ]);
        } catch (\Exception $e) {
            $db = \App\Core\Database::getInstance()->getConnection();
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Task ID is missing']);
                return;
            }

            $task = $this->taskModel->getById($id);
            if (!$task) {
                echo json_encode(['status' => 'error', 'message' => 'Task not found']);
                return;
            }

            // Authorization check: Admin or Assignee
            $isAssigned = false;
            if ($task['assigned_to'] === $_SESSION['user_id'] || (isset($task['assigned_users']) && in_array($_SESSION['user_id'], $task['assigned_users']))) {
                $isAssigned = true;
            }

            if ($_SESSION['user_role'] !== 'admin' && !$isAssigned) {
                echo json_encode(['status' => 'error', 'message' => 'You are not authorized to update this task']);
                return;
            }

            $projectId = $_POST['project_id'] ?? $task['project_id'];
            if ($projectId === '') {
                $projectId = null;
            }

            $data = [
                'project_id' => $projectId,
                'assigned_to' => $_POST['assigned_to'] ?? $task['assigned_to'],
                'assigned_users' => $_POST['assigned_users'] ?? $task['assigned_users'] ?? [],
                'title' => trim($_POST['title'] ?? $task['title']),
                'description' => trim($_POST['description'] ?? $task['description']),
                'status' => $_POST['status'] ?? $task['status'],
                'due_date' => (!empty($_POST['due_date']) && !empty($_POST['due_time'])) ? $_POST['due_date'] . ' ' . $_POST['due_time'] : $task['due_date'],
                'due_time' => $_POST['due_time'] ?? $task['due_time'],
                'priority' => $_POST['priority'] ?? $task['priority'],
                'is_completed' => $task['is_completed'],
                'is_incomplete' => $task['is_incomplete'],
                'completed_at' => $task['completed_at'],
                'admin_alert_sent' => $task['admin_alert_sent'],
                'is_recurring' => $_POST['is_recurring'] ?? $task['is_recurring'],
                'recurring_type' => $_POST['recurring_type'] ?? $task['recurring_type'],
                'recurring_parent_id' => $task['recurring_parent_id'],
                'next_repeat_date' => $_POST['next_repeat_date'] ?? $task['next_repeat_date'],
                'repeat_status' => $_POST['repeat_status'] ?? $task['repeat_status']
            ];

            // If not admin, restrict certain fields
            if ($_SESSION['user_role'] !== 'admin') {
                $data['project_id'] = $task['project_id'];
                $data['assigned_to'] = $task['assigned_to'];
                $data['title'] = $task['title'];
            }

            if ($this->taskModel->update($id, $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Task updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update task']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'Task ID is missing']);
            return;
        }

        if ($this->taskModel->softDelete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Task deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete task']);
        }
    }

    public function completeStaff()
    {
        header('Content-Type: application/json');

        try {
            $id = $_POST['task_id'] ?? '';
            $notes = $_POST['completion_notes'] ?? '';

            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Task ID is missing']);
                return;
            }

            $task = $this->taskModel->getById($id);
            if (!$task) {
                echo json_encode(['status' => 'error', 'message' => 'Task not found']);
                return;
            }

            $isAssigned = false;
            if ($task['assigned_to'] === $_SESSION['user_id'] || (isset($task['assigned_users']) && in_array($_SESSION['user_id'], $task['assigned_users']))) {
                $isAssigned = true;
            }

            if ($_SESSION['user_role'] !== 'admin' && !$isAssigned) {
                echo json_encode(['status' => 'error', 'message' => 'You are not authorized to update this task']);
                return;
            }

            $data = $task;
            $data['status'] = 'completed';
            $data['is_completed'] = 1;
            $data['completed_at'] = date('Y-m-d H:i:s');
            $data['completed_by'] = $_SESSION['user_id'];
            $data['completion_notes'] = $notes;

            if ($this->taskModel->update($id, $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Task completed successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to complete task']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOverdue()
    {
        header('Content-Type: application/json');
        try {
            $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
            $userId = $isAdminOrManager ? null : $_SESSION['user_id'];
            
            if ($isAdminOrManager && !empty($_GET['user_id'])) {
                $userId = $_GET['user_id'];
            }

            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : null;
            $search = $_GET['search']['value'] ?? '';
            $taskType = $_GET['task_type'] ?? 'All';

            $tasks = $this->taskModel->getOverdueTasks($userId, $search, $length, $start, $taskType);
            $recordsFiltered = $this->taskModel->countOverdueTasks($userId, $search, $taskType);
            $recordsTotal = $this->taskModel->countOverdueTasks($userId, '', $taskType);

            echo json_encode([
                'status' => 'success',
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $tasks
            ], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function updateStatus()
    {
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            $type = $_POST['type'] ?? ''; // 'complete' or 'incomplete'

            if (empty($id) || empty($type)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing data']);
                return;
            }

            $task = $this->taskModel->getById($id);
            if (!$task) {
                echo json_encode(['status' => 'error', 'message' => 'Task not found']);
                return;
            }

            // Authorization check
            $isAssigned = false;
            if ($task['assigned_to'] === $_SESSION['user_id'] || (isset($task['assigned_users']) && in_array($_SESSION['user_id'], $task['assigned_users']))) {
                $isAssigned = true;
            }

            if ($_SESSION['user_role'] !== 'admin' && !$isAssigned) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                return;
            }

            $data = $task; // Start with current data

            if ($type === 'complete') {
                $data['is_completed'] = 1;
                $data['is_incomplete'] = 0;
                $data['status'] = 'completed';
                $data['completed_at'] = date('Y-m-d H:i:s');
            } elseif ($type === 'incomplete') {
                $data['is_incomplete'] = 1;
                $data['is_completed'] = 0;
                $data['status'] = 'in_progress';
                
                if (!$task['admin_alert_sent']) {
                    $alertModel = new TaskAlert();
                    $alertModel->create([
                        'task_id' => $id,
                        'user_id' => $task['assigned_to'],
                        'message' => "Task marked incomplete"
                    ]);
                    $data['admin_alert_sent'] = 1;
                }
            }

            if ($this->taskModel->update($id, $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Task status updated']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Update failed']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    public function enableRecurring()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            $type = $_POST['type'] ?? ''; // 'daily', 'weekly' or 'monthly'

            if (empty($id) || !in_array($type, ['daily', 'weekly', 'monthly'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid data provided']);
                return;
            }

            $task = $this->taskModel->getById($id);
            if (!$task) {
                echo json_encode(['success' => false, 'message' => 'Task not found']);
                return;
            }

            // Calculate next repeat date
            $currentDueDate = !empty($task['due_date']) ? $task['due_date'] : date('Y-m-d');
            $nextDate = $this->calculateNextDate($currentDueDate, $type);

            if ($this->taskModel->updateRecurringStatus($id, 1, $type, $nextDate, 'active')) {
                // Generate first repeat task immediately
                $parentTask = $this->taskModel->getById($id);
                $newId = $this->generateNextTask($parentTask);
                
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'Recurring enabled. New task created for ' . $nextDate
                ]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to enable recurring']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function disableRecurring()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Task ID is missing']);
                return;
            }

            if ($this->taskModel->updateRecurringStatus($id, 0, null, null, 'completed')) {
                echo json_encode(['status' => 'success', 'message' => 'Recurring disabled successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to disable recurring']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function recurringLogs()
    {
        header('Content-Type: application/json');
        try {
            $id = $_GET['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Task ID is missing']);
                return;
            }

            $logs = $this->taskModel->listRecurringLogs($id);
            echo json_encode(['status' => 'success', 'data' => $logs]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function processRecurring()
    {
        // This could be called by a cron job or manually
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $tasks = $this->taskModel->getRecurringTasks();
            $generatedCount = 0;

            foreach ($tasks as $task) {
                $newId = $this->generateNextTask($task);
                if ($newId) {
                    $generatedCount++;
                }
            }

            echo json_encode([
                'success' => true, 
                'message' => $generatedCount . ' recurring task(s) processed'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function generateNextTask($parentTask)
    {
        $db = \App\Core\Database::getInstance()->getConnection();
        
        try {
            $db->beginTransaction();

            // 1. Prepare new task data (cloning parent)
            $assignedUsers = !empty($parentTask['assigned_to_ids']) ? explode(',', $parentTask['assigned_to_ids']) : [];
            if (empty($assignedUsers) && !empty($parentTask['assigned_to'])) {
                $assignedUsers = [$parentTask['assigned_to']];
            }

            // 1. Prepare new task data (cloning parent)
            $newData = [
                'project_id' => $parentTask['project_id'],
                'assigned_to' => $parentTask['assigned_to'],
                'assigned_users' => $assignedUsers,
                'title' => $parentTask['title'],
                'description' => $parentTask['description'],
                'due_date' => $parentTask['next_repeat_date'],
                'due_time' => $parentTask['due_time'],
                'priority' => $parentTask['priority'],
                'status' => 'pending',
                'is_completed' => 0,
                'is_incomplete' => 0,
                'admin_alert_sent' => 0,
                'is_recurring' => 0, // Generated task is not recurring by default unless it's the new parent
                'recurring_type' => null,
                'recurring_parent_id' => $parentTask['id'],
                'next_repeat_date' => null,
                'repeat_status' => 'active'
            ];

            // 2. Create the new task
            $newTaskId = $this->taskModel->create($newData);
            if (!$newTaskId) {
                throw new \Exception("Failed to create generated task");
            }

            // 3. Log the generation
            $this->taskModel->logRecurringGeneration(
                $parentTask['id'], 
                $parentTask['recurring_type'], 
                $newTaskId, 
                $parentTask['next_repeat_date'], 
                $_SESSION['user_id']
            );

            // 4. Update the parent task's next_repeat_date
            $nextDate = $this->calculateNextDate($parentTask['next_repeat_date'], $parentTask['recurring_type']);
            $this->taskModel->updateRecurringStatus(
                $parentTask['id'], 
                1, 
                $parentTask['recurring_type'], 
                $nextDate, 
                'active'
            );

            $db->commit();
            return $newTaskId;

        } catch (\Exception $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            return false;
        }
    }

    private function calculateNextDate($currentDate, $type)
    {
        $date = new \DateTime($currentDate);
        if ($type === 'daily') {
            $date->modify('+1 day');
        } elseif ($type === 'weekly') {
            $date->modify('+7 days');
        } elseif ($type === 'monthly') {
            $date->modify('+1 month');
        }
        return $date->format('Y-m-d');
    }
}
