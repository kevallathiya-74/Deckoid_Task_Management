<?php

namespace App\Controllers;

use App\Models\Todo;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class TodoController
{
    protected $todoModel;
    protected $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->todoModel = new Todo();
        $this->userModel = new User();
    }

    public function index()
    {
        $title = 'Todo List';
        $active_page = 'todo';
        
        $staff = $this->userModel->listAll();

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/todo/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        try {
            $filters = [];
            if (!isAdminOrSubAdmin()) {
                $filters['assigned_to'] = $_SESSION['user_id'];
            } else {
                if (!empty($_GET['assigned_to'])) {
                    $filters['assigned_to'] = $_GET['assigned_to'];
                }
            }
            
            if (!empty($_GET['status'])) {
                $filters['status'] = $_GET['status'];
            }

            $todos = $this->todoModel->listAll($filters);
            echo json_encode(['status' => 'success', 'data' => $todos], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function listAdmin()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');
        try {
            $filters = [];
            if (empty($_GET['staff_id'])) {
                // Return empty if no staff selected
                echo json_encode(['status' => 'success', 'data' => []]);
                return;
            }
            
            // Show ONLY selected staff member's todos (assigned to them or personal)
            $filters['assigned_to'] = $_GET['staff_id'];
            $todos = $this->todoModel->listAll($filters);
            echo json_encode(['status' => 'success', 'data' => $todos], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function listStaff()
    {
        header('Content-Type: application/json');
        try {
            $filters = ['assigned_to' => $_SESSION['user_id']];
            $todos = $this->todoModel->listAll($filters);
            echo json_encode(['status' => 'success', 'data' => $todos], JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function create()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'assigned_to' => $_POST['assigned_to'] ?? '',
                'status' => $_POST['status'] ?? 'pending',
                'priority' => $_POST['priority'] ?? 'medium',
                'notes' => trim($_POST['notes'] ?? ''),
                'is_pinned' => isset($_POST['is_pinned']) ? (bool)$_POST['is_pinned'] : false,
                'todo_type' => isset($_POST['is_pinned']) && $_POST['is_pinned'] ? 'Pinned Task' : 'Normal Task',
                'deadline_date' => !empty($_POST['deadline_date']) ? $_POST['deadline_date'] : null,
                'deadline_time' => !empty($_POST['deadline_time']) ? $_POST['deadline_time'] : null,
                'created_by' => $_SESSION['user_id']
            ];

            if (empty($data['title']) || empty($data['assigned_to'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Title and Assignee are required']);
                return;
            }

            if (!empty($data['deadline_time']) && empty($data['deadline_date'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Please select a deadline date first.']);
                return;
            }

            $id = $this->todoModel->create($data);
            if ($id) {
                echo json_encode(['status' => 'success', 'message' => 'Todo created successfully', 'id' => $id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create todo']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createStaff()
    {
        header('Content-Type: application/json');
        try {
            $data = [
                'title' => trim($_POST['title'] ?? ''),
                'assigned_to' => $_SESSION['user_id'], // Assigned to self
                'status' => 'pending',
                'priority' => 'medium',
                'notes' => '',
                'todo_type' => $_POST['todo_type'] ?? 'Normal Task',
                'is_pinned' => (isset($_POST['todo_type']) && $_POST['todo_type'] === 'Pinned Task') ? true : false,
                'deadline_date' => !empty($_POST['deadline_date']) ? $_POST['deadline_date'] : null,
                'deadline_time' => !empty($_POST['deadline_time']) ? $_POST['deadline_time'] : null,
                'created_by' => $_SESSION['user_id']
            ];

            if (empty($data['title'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Title is required']);
                return;
            }

            if (!empty($data['deadline_time']) && empty($data['deadline_date'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Please select a deadline date first.']);
                return;
            }

            $id = $this->todoModel->create($data);
            if ($id) {
                echo json_encode(['status' => 'success', 'message' => 'Todo created successfully', 'id' => $id]);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create todo']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');

        try {
            // Check for PUT or POST data
            $input = $_POST;
            if (empty($input)) {
                $rawInput = file_get_contents("php://input");
                parse_str($rawInput, $input);
            }
            
            $id = $input['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Todo ID is missing']);
                return;
            }

            $todo = $this->todoModel->getById($id);
            if (!$todo) {
                echo json_encode(['status' => 'error', 'message' => 'Todo not found']);
                return;
            }

            // Authorization
            if (!isAdminOrSubAdmin() && $todo['assigned_to'] !== $_SESSION['user_id'] && $todo['created_by'] !== $_SESSION['user_id']) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                return;
            }

            $data = [
                'title' => $input['title'] ?? $todo['title'],
                'assigned_to' => $input['assigned_to'] ?? $todo['assigned_to'],
                'status' => $input['status'] ?? $todo['status'],
                'priority' => $input['priority'] ?? $todo['priority'],
                'notes' => $input['notes'] ?? $todo['notes'],
                'is_pinned' => isset($input['is_pinned']) ? (bool)$input['is_pinned'] : (bool)$todo['is_pinned'],
                'todo_type' => $input['todo_type'] ?? $todo['todo_type'],
                'deadline_date' => isset($input['deadline_date']) ? (!empty($input['deadline_date']) ? $input['deadline_date'] : null) : $todo['deadline_date'],
                'deadline_time' => isset($input['deadline_time']) ? (!empty($input['deadline_time']) ? $input['deadline_time'] : null) : $todo['deadline_time']
            ];

            if (!empty($data['deadline_time']) && empty($data['deadline_date'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Please select a deadline date first.']);
                return;
            }

            // Staff can only fully update if they created it. 
            // If they are just assigned, they can only update status. 
            if (!isAdminOrSubAdmin()) {
                if ($todo['created_by'] !== $_SESSION['user_id']) {
                    // Just assigned, update status only
                    $data = [
                        'title' => $todo['title'],
                        'assigned_to' => $todo['assigned_to'],
                        'status' => $input['status'] ?? $todo['status'],
                        'priority' => $todo['priority'],
                        'notes' => $input['notes'] ?? $todo['notes'],
                        'is_pinned' => $todo['is_pinned'],
                        'todo_type' => $todo['todo_type'],
                        'deadline_date' => $todo['deadline_date'],
                        'deadline_time' => $todo['deadline_time']
                    ];
                } else {
                    // Ensure assigned_to remains self if created by self
                    $data['assigned_to'] = $_SESSION['user_id'];
                    $data['is_pinned'] = ($data['todo_type'] === 'Pinned Task') ? true : false;
                }
            } else {
                $data['todo_type'] = $data['is_pinned'] ? 'Pinned Task' : 'Normal Task';
            }

            if ($this->todoModel->update($id, $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Todo updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update todo']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');

        try {
            $input = $_POST;
            if (empty($input)) {
                $rawInput = file_get_contents("php://input");
                parse_str($rawInput, $input);
            }

            $id = $input['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Todo ID is missing']);
                return;
            }

            $todo = $this->todoModel->getById($id);
            if (!$todo) {
                echo json_encode(['status' => 'error', 'message' => 'Todo not found']);
                return;
            }

            // Authorization: Admin or creator
            if (!isAdminOrSubAdmin() && $todo['created_by'] !== $_SESSION['user_id']) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                return;
            }

            if ($this->todoModel->delete($id)) {
                echo json_encode(['status' => 'success', 'message' => 'Todo deleted successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to delete todo']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
    
    public function resetPinned()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $db = \App\Core\Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE todo_lists SET status = 'pending' WHERE is_pinned = 1");
            
            if ($stmt->execute()) {
                echo json_encode(['status' => 'success', 'message' => 'Pinned tasks reset successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to reset pinned tasks']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function getOverdue()
    {
        header('Content-Type: application/json');
        try {
            $isAdminOrManager = (isAdminOrSubAdmin() || hasRole(['manager']));
            $userId = $isAdminOrManager ? null : $_SESSION['user_id'];
            
            $db = \App\Core\Database::getInstance()->getConnection();
            $sql = "
                SELECT t.id, t.title, t.deadline_date, t.deadline_time, DATEDIFF(CURRENT_DATE(), t.deadline_date) as days_overdue
                FROM todo_lists t
                WHERE t.deadline_date IS NOT NULL 
                AND t.status != 'completed' 
                AND (t.deadline_date < CURRENT_DATE() OR (t.deadline_date = CURRENT_DATE() AND t.deadline_time IS NOT NULL AND t.deadline_time <= CURRENT_TIME()))
            ";
            
            $params = [];
            if ($userId) {
                $sql .= " AND t.assigned_to = :uid";
                $params['uid'] = $userId;
            }
            
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $todos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            echo json_encode([
                'status' => 'success',
                'data' => $todos
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
