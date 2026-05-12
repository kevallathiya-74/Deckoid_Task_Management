<?php

namespace App\Controllers;

use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Models\Role;
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
        $title = 'Task Management';
        $active_page = 'tasks';
        
        $projects = $this->projectModel->listAll();
        $staff = $this->userModel->listAll();
        $roles = $this->roleModel->all();
        $project_id = $_GET['project_id'] ?? null;

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/tasks/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        try {
            $filters = [
                'project_id' => $_GET['project_id'] ?? null,
                'assigned_to' => $_GET['assigned_to'] ?? null,
                'status' => $_GET['status'] ?? null,
                'role_id' => $_GET['role_id'] ?? null
            ];

            // If user is not admin, they can only see tasks assigned to them (unless they are a manager of that dept - but keeping it simple for now)
            if ($_SESSION['user_role'] !== 'admin' && empty($filters['assigned_to'])) {
                // $filters['assigned_to'] = $_SESSION['user_id']; 
                // Let's allow staff to see all tasks for now as per PRD "Transparency"
            }

            $tasks = $this->taskModel->listAll($filters);
            
            echo json_encode([
                'success' => true,
                'data' => $tasks
            ]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database Error: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        header('Content-Type: application/json');

        try {
            $data = [
                'project_id' => $_POST['project_id'] ?? '',
                'assigned_to' => $_POST['assigned_to'] ?? '',
                'role_id' => $_POST['role_id'] ?? '',
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : date('Y-m-d', strtotime('+1 day')),
                'priority' => $_POST['priority'] ?? 'medium',
                'status' => $_POST['status'] ?? 'pending'
            ];

            if (empty($data['project_id']) || empty($data['assigned_to']) || empty($data['title'])) {
                echo json_encode(['success' => false, 'message' => 'Project, Assignee, and Title are required']);
                return;
            }

            $id = $this->taskModel->create($data);
            if ($id) {
                echo json_encode(['success' => true, 'message' => 'Task created successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to create task']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['success' => false, 'message' => 'Task ID is missing']);
                return;
            }

            $data = [
                'assigned_to' => $_POST['assigned_to'] ?? '',
                'role_id' => $_POST['role_id'] ?? '',
                'title' => $_POST['title'] ?? '',
                'description' => $_POST['description'] ?? '',
                'status' => $_POST['status'] ?? 'pending',
                'due_date' => !empty($_POST['due_date']) ? $_POST['due_date'] : date('Y-m-d'),
                'priority' => $_POST['priority'] ?? 'medium',
                'progress_percentage' => $_POST['progress_percentage'] ?? 0,
                'status_notes' => $_POST['status_notes'] ?? ''
            ];

            if ($this->taskModel->update($id, $data)) {
                echo json_encode(['success' => true, 'message' => 'Task updated successfully']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update task']);
            }
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function delete()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Task ID is missing']);
            return;
        }

        if ($this->taskModel->softDelete($id)) {
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete task']);
        }
    }
}
