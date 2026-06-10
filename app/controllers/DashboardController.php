<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Core\Database;
use App\Middleware\AuthMiddleware;
use PDO;

class DashboardController
{
    protected $db;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $role = $_SESSION['user_role'] ?? 'staff';
        $prefix = ($role === 'admin') ? 'admin' : 'staff';
        $currentUri = $_SERVER['REQUEST_URI'];

        // If accessing root or generic dashboard, redirect to role-prefixed dashboard
        if ($currentUri == url('/') || $currentUri == url('/dashboard')) {
            header('Location: ' . url("/$prefix/dashboard"));
            exit;
        }

        $title = 'Dashboard';
        $active_page = 'dashboard';

        // Fetch Stats
        $stats = $this->getStats();
        
        // Fetch Recent Activities
        $recent_tasks = $this->getRecentTasks();

        $projectModel = new Project();
        $userModel = new User();
        $projects = $projectModel->listAll();
        $staff = $userModel->listAll();

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/dashboard/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function getChartData()
    {
        header('Content-Type: application/json');
        
        // Project status distribution (removed since status was dropped)
        $projects = [];

        $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
        $userId = $_SESSION['user_id'];

        if (!$isAdminOrManager) {
            // Task priority distribution - ONLY ACTIVE TASKS FOR THIS USER
            $stmt = $this->db->prepare("
                SELECT priority, COUNT(*) as count 
                FROM tasks 
                WHERE status != 'completed' 
                AND deleted_at IS NULL 
                AND (assigned_to = :uid OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
                GROUP BY priority
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Growth Analysis (Last 7 Days)
            $stmt = $this->db->prepare("
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM tasks 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                AND deleted_at IS NULL 
                AND (assigned_to = :uid OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
            $growth_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Task priority distribution - ONLY ACTIVE TASKS
            $stmt = $this->db->query("SELECT priority, COUNT(*) as count FROM tasks WHERE status != 'completed' AND deleted_at IS NULL GROUP BY priority");
            $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Growth Analysis (Last 7 Days)
            $stmt = $this->db->query("
                SELECT DATE(created_at) as date, COUNT(*) as count 
                FROM tasks 
                WHERE created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY) 
                AND deleted_at IS NULL 
                GROUP BY DATE(created_at)
                ORDER BY date ASC
            ");
            $growth_data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
        
        // Map to ensure all 7 days are present
        $growth = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $count = 0;
            foreach ($growth_data as $g) {
                if ($g['date'] === $date) {
                    $count = $g['count'];
                    break;
                }
            }
            $growth[] = [
                'date' => $date,
                'count' => (int)$count
            ];
        }

        echo json_encode([
            'success' => true,
            'projects' => $projects,
            'tasks' => $tasks,
            'growth' => $growth
        ]);
    }

    public function getPriorityTasks()
    {
        header('Content-Type: application/json');
        $priority = $_GET['priority'] ?? '';
        
        if (!$priority) {
            echo json_encode(['success' => false, 'message' => 'Priority required']);
            return;
        }

        $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
        $userId = $_SESSION['user_id'];

        if (!$isAdminOrManager) {
            $stmt = $this->db->prepare("
                SELECT t.*, p.project_name, u.full_name as staff_name 
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN users u ON t.assigned_to = u.id
                WHERE t.priority = :priority 
                AND t.status != 'completed' 
                AND t.deleted_at IS NULL
                AND (t.assigned_to = :uid OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
                ORDER BY t.created_at DESC
            ");
            $stmt->execute(['priority' => $priority, 'uid' => $userId, 'uid2' => $userId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT t.*, p.project_name, u.full_name as staff_name 
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN users u ON t.assigned_to = u.id
                WHERE t.priority = :priority 
                AND t.status != 'completed' 
                AND t.deleted_at IS NULL
                ORDER BY t.created_at DESC
            ");
            $stmt->execute(['priority' => $priority]);
        }
        $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $tasks
        ]);
    }

    public function getAlerts()
    {
        header('Content-Type: application/json');
        $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
        $userId = $_SESSION['user_id'];

        if (!$isAdminOrManager) {
            $stmt = $this->db->prepare("
                SELECT a.*, t.title as task_title, u.full_name as staff_name 
                FROM task_alerts a
                JOIN tasks t ON a.task_id = t.id
                JOIN users u ON t.assigned_to = u.id
                WHERE a.is_read = 0
                AND (t.assigned_to = :uid OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
                ORDER BY a.created_at DESC
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
        } else {
            $stmt = $this->db->query("
                SELECT a.*, t.title as task_title, u.full_name as staff_name 
                FROM task_alerts a
                JOIN tasks t ON a.task_id = t.id
                JOIN users u ON t.assigned_to = u.id
                WHERE a.is_read = 0
                ORDER BY a.created_at DESC
            ");
        }
        $alerts = $stmt->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode([
            'success' => true,
            'data' => $alerts
        ]);
    }

    public function markAlertRead()
    {
        header('Content-Type: application/json');
        $id = $_POST['id'] ?? '';
        
        if (!$id) {
            echo json_encode(['success' => false]);
            return;
        }

        $stmt = $this->db->prepare("UPDATE task_alerts SET is_read = 1 WHERE id = :id");
        $stmt->execute(['id' => $id]);

        echo json_encode(['success' => true]);
    }

    public function getNotifications()
    {
        header('Content-Type: application/json');
        try {
            $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
            $userId = $isAdminOrManager ? null : $_SESSION['user_id'];
            
            $taskModel = new \App\Models\Task();
            $overdue = $taskModel->getOverdueTasks($userId);
            
            $items = [];
            foreach ($overdue as $task) {
                $items[] = [
                    'type' => 'danger',
                    'icon' => 'fa-exclamation-triangle',
                    'title' => 'Overdue Task',
                    'message' => $task['title'] . ' (' . $task['days_overdue'] . ' days overdue)',
                    'time' => date('M d', strtotime($task['due_date'])),
                    'link' => url('/' . ($isAdminOrManager ? 'admin' : 'staff') . '/overdue')
                ];
            }

            $todaySql = "SELECT title, due_time FROM tasks WHERE due_date = CURRENT_DATE() AND status != 'completed' AND deleted_at IS NULL";
            $params = [];
            if ($userId) {
                $todaySql .= " AND (assigned_to = :uid OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))";
                $params = ['uid' => $userId, 'uid2' => $userId];
            }
            $stmt = $this->db->prepare($todaySql);
            $stmt->execute($params);
            $todayTasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($todayTasks as $task) {
                $items[] = [
                    'type' => 'warning',
                    'icon' => 'fa-clock',
                    'title' => 'Due Today',
                    'message' => $task['title'],
                    'time' => $task['due_time'] ? date('h:i A', strtotime($task['due_time'])) : 'Today',
                    'link' => url('/' . ($isAdminOrManager ? 'admin' : 'staff') . '/tasks')
                ];
            }

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'count' => count($items),
                    'items' => array_slice($items, 0, 10)
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    private function getStats()
    {
        $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
        $userId = $_SESSION['user_id'];

        if (!$isAdminOrManager) {
            // Projects where user is assigned to a task in that project
            $stmt = $this->db->prepare("
                SELECT COUNT(DISTINCT p.id) as total
                FROM projects p
                JOIN tasks t ON t.project_id = p.id
                WHERE p.deleted_at IS NULL AND t.deleted_at IS NULL
                AND (t.assigned_to = :uid OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
            $projectStats = $stmt->fetch(PDO::FETCH_ASSOC);

            // Fetch Due Today and Overdue Tasks counts
            $stmt = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN DATE(due_date) = CURDATE() THEN 1 ELSE 0 END) as due_today,
                    SUM(CASE WHEN DATE(due_date) < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks
                FROM tasks 
                WHERE deleted_at IS NULL AND status != 'completed'
                AND (assigned_to = :uid OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
            $taskStats = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            // Combine projects counts
            $projectStats = $this->db->query("
                SELECT COUNT(*) as total
                FROM projects 
                WHERE deleted_at IS NULL
            ")->fetch(PDO::FETCH_ASSOC);

            // Fetch Due Today and Overdue Tasks counts
            $taskStats = $this->db->query("
                SELECT 
                    SUM(CASE WHEN DATE(due_date) = CURDATE() THEN 1 ELSE 0 END) as due_today,
                    SUM(CASE WHEN DATE(due_date) < CURDATE() THEN 1 ELSE 0 END) as overdue_tasks
                FROM tasks 
                WHERE deleted_at IS NULL AND status != 'completed'
            ")->fetch(PDO::FETCH_ASSOC);
        }

        return [
            'total_projects' => (int)$projectStats['total'],
            'due_today' => (int)$taskStats['due_today'],
            'overdue_tasks' => (int)$taskStats['overdue_tasks']
        ];
    }

    private function getRecentTasks()
    {
        $isAdminOrManager = ($_SESSION['user_role'] === 'admin' || $_SESSION['user_role'] === 'manager');
        $userId = $_SESSION['user_id'];

        if (!$isAdminOrManager) {
            $stmt = $this->db->prepare("
                SELECT t.*, p.project_name, u.full_name as assigned_to_name 
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN users u ON t.assigned_to = u.id
                WHERE t.deleted_at IS NULL
                AND (t.assigned_to = :uid OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :uid2))
                ORDER BY t.created_at DESC
                LIMIT 5
            ");
            $stmt->execute(['uid' => $userId, 'uid2' => $userId]);
        } else {
            $stmt = $this->db->prepare("
                SELECT t.*, p.project_name, u.full_name as assigned_to_name 
                FROM tasks t
                LEFT JOIN projects p ON t.project_id = p.id
                JOIN users u ON t.assigned_to = u.id
                WHERE t.deleted_at IS NULL
                ORDER BY t.created_at DESC
                LIMIT 5
            ");
            $stmt->execute();
        }
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
