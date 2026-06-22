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

            // --- BEGIN TODOS INTEGRATION ---
            // Process persistent reminders for Todos before pulling them
            $this->processTodoReminders($userId, $isAdminOrManager);

            // Fetch overdue Todos
            $todoSql = "
                SELECT t.id, t.title, t.deadline_date, t.deadline_time, DATEDIFF(CURRENT_DATE(), t.deadline_date) as days_overdue
                FROM todo_lists t
                WHERE t.deadline_date IS NOT NULL 
                AND t.status != 'completed' 
                AND (t.deadline_date < CURRENT_DATE() OR (t.deadline_date = CURRENT_DATE() AND t.deadline_time IS NOT NULL AND t.deadline_time <= CURRENT_TIME()))
            ";
            $todoParams = [];
            if ($userId) {
                $todoSql .= " AND t.assigned_to = :uid";
                $todoParams = ['uid' => $userId];
            }
            $stmtTodo = $this->db->prepare($todoSql);
            $stmtTodo->execute($todoParams);
            $overdueTodos = $stmtTodo->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($overdueTodos as $todo) {
                $items[] = [
                    'type' => 'danger',
                    'icon' => 'fa-exclamation-triangle',
                    'title' => 'Overdue Todo',
                    'message' => $todo['title'] . ($todo['days_overdue'] > 0 ? ' (' . $todo['days_overdue'] . ' days overdue)' : ' (Overdue today)'),
                    'time' => date('M d', strtotime($todo['deadline_date'])),
                    'link' => url('/' . ($isAdminOrManager ? 'admin' : 'staff') . '/todo')
                ];
            }
            // --- END TODOS INTEGRATION ---

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

            // Fetch Due Today Todos
            $todayTodoSql = "
                SELECT title, deadline_time 
                FROM todo_lists 
                WHERE deadline_date = CURRENT_DATE() 
                AND status != 'completed'
                AND (deadline_time IS NULL OR deadline_time > CURRENT_TIME())
            ";
            if ($userId) {
                $todayTodoSql .= " AND assigned_to = :uid";
            }
            $stmtTodayTodo = $this->db->prepare($todayTodoSql);
            $stmtTodayTodo->execute($todoParams);
            $todayTodos = $stmtTodayTodo->fetchAll(\PDO::FETCH_ASSOC);

            foreach ($todayTodos as $todo) {
                $items[] = [
                    'type' => 'warning',
                    'icon' => 'fa-clock',
                    'title' => 'Todo Due Today',
                    'message' => $todo['title'],
                    'time' => $todo['deadline_time'] ? date('h:i A', strtotime($todo['deadline_time'])) : 'Today',
                    'link' => url('/' . ($isAdminOrManager ? 'admin' : 'staff') . '/todo')
                ];
            }

            if ($userId) {
                $notifModel = new \App\Models\NotificationModel();
                $dbNotifs = $notifModel->getUnreadByUser($userId);
                foreach ($dbNotifs as $n) {
                    $items[] = [
                        'type' => 'info',
                        'icon' => 'fa-info-circle',
                        'title' => $n['title'],
                        'message' => $n['message'],
                        'time' => date('h:i A', strtotime($n['created_at'])),
                        'link' => $n['link'] ?? '#'
                    ];
                }
            }

            // Sort items by time descending (this is optional but good, let's just leave the order as is or similar)

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

    private function processTodoReminders($userId, $isAdminOrManager)
    {
        $notifModel = new \App\Models\NotificationModel();
        
        $sql = "
            SELECT id, title, assigned_to, deadline_date, deadline_time, reminder_sent
            FROM todo_lists
            WHERE deadline_date IS NOT NULL
            AND status != 'completed'
            AND reminder_sent < 3
        ";
        
        // We evaluate reminders globally, so we don't necessarily filter by user here 
        // to ensure notifications are generated in the DB. Or we can just filter by user.
        if ($userId) {
            $sql .= " AND assigned_to = " . $this->db->quote($userId);
        }
        
        $stmt = $this->db->query($sql);
        $todos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $now = new \DateTime();
        
        foreach ($todos as $todo) {
            $deadline = new \DateTime($todo['deadline_date'] . ' ' . ($todo['deadline_time'] ?? '23:59:59'));
            $diffSeconds = $deadline->getTimestamp() - $now->getTimestamp();
            $state = (int)$todo['reminder_sent'];
            
            $newState = $state;
            $shouldNotify = false;
            $msg = '';
            
            // At exact deadline (or passed it but not yet sent)
            if ($diffSeconds <= 0 && $state < 3) {
                $newState = 3;
                $shouldNotify = true;
                $msg = "Your todo deadline has been reached: " . $todo['title'];
            } 
            // 1 Hour Before
            elseif ($diffSeconds > 0 && $diffSeconds <= 3600 && $state < 2 && !empty($todo['deadline_time'])) {
                $newState = 2;
                $shouldNotify = true;
                $msg = "Your todo is due in 1 hour: " . $todo['title'];
            }
            // 1 Day Before or 9 AM on day
            elseif ($diffSeconds > 3600 && $diffSeconds <= 86400 && $state < 1) {
                if (empty($todo['deadline_time'])) {
                    // Date only: 9 AM on deadline day
                    $nineAm = new \DateTime($todo['deadline_date'] . ' 09:00:00');
                    if ($now >= $nineAm) {
                        $newState = 1;
                        $shouldNotify = true;
                        $msg = "Your todo deadline is approaching today: " . $todo['title'];
                    }
                } else {
                    // Date + Time: 1 Day Before
                    $newState = 1;
                    $shouldNotify = true;
                    $msg = "Your todo is due tomorrow: " . $todo['title'];
                }
            }
            
            if ($shouldNotify) {
                $notifModel->create([
                    'user_id' => $todo['assigned_to'],
                    'title' => 'Todo Reminder',
                    'message' => $msg,
                    'type' => 'warning',
                    'link' => url('/staff/todo')
                ]);
                $upd = $this->db->prepare("UPDATE todo_lists SET reminder_sent = :rs WHERE id = :id");
                $upd->execute(['rs' => $newState, 'id' => $todo['id']]);
            }
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

            // Fetch Todo counts
            $stmtTodo = $this->db->prepare("
                SELECT 
                    SUM(CASE WHEN deadline_date = CURDATE() THEN 1 ELSE 0 END) as due_today,
                    SUM(CASE WHEN deadline_date < CURDATE() OR (deadline_date = CURDATE() AND deadline_time < CURRENT_TIME()) THEN 1 ELSE 0 END) as overdue_tasks
                FROM todo_lists 
                WHERE status != 'completed'
                AND assigned_to = :uid
            ");
            $stmtTodo->execute(['uid' => $userId]);
            $todoStats = $stmtTodo->fetch(PDO::FETCH_ASSOC);

            $taskStats['due_today'] += (int)$todoStats['due_today'];
            $taskStats['overdue_tasks'] += (int)$todoStats['overdue_tasks'];
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

            $todoStats = $this->db->query("
                SELECT 
                    SUM(CASE WHEN deadline_date = CURDATE() THEN 1 ELSE 0 END) as due_today,
                    SUM(CASE WHEN deadline_date < CURDATE() OR (deadline_date = CURDATE() AND deadline_time < CURRENT_TIME()) THEN 1 ELSE 0 END) as overdue_tasks
                FROM todo_lists 
                WHERE status != 'completed'
            ")->fetch(PDO::FETCH_ASSOC);

            $taskStats['due_today'] += (int)$todoStats['due_today'];
            $taskStats['overdue_tasks'] += (int)$todoStats['overdue_tasks'];
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
