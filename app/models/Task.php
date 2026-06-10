<?php

namespace App\Models;

use App\Core\Database;

class Task
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listAll($filters = [], $search = '', $limit = null, $offset = null)
    {
        $sql = "
            SELECT 
                t.id, t.project_id, t.assigned_to, t.title, t.description, 
                t.status, t.is_completed, t.is_incomplete, t.due_date, t.due_time, t.priority, 
                t.completed_at, t.admin_alert_sent, t.created_at, t.updated_at,
                t.is_recurring, t.recurring_type, t.recurring_parent_id, 
                t.next_repeat_date, t.repeat_status,
                p.project_name, p.client_name
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            WHERE t.deleted_at IS NULL
        ";
        
        $params = [];
        if (!empty($filters['project_id'])) {
            $sql .= " AND t.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND (t.assigned_to = :assigned_to OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :assigned_to2))";
            $params['assigned_to'] = $filters['assigned_to'];
            $params['assigned_to2'] = $filters['assigned_to'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }


        if (!empty($search)) {
            $sql .= " AND (t.title LIKE :search OR p.project_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $sql .= " GROUP BY t.id";
        $sql .= " ORDER BY t.due_date ASC";

        if ($limit !== null && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null && $offset >= 0) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $tasks = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        if (empty($tasks)) {
            return [];
        }

        // Map relationships in PHP to avoid N+1
        $taskIds = array_column($tasks, 'id');
        $placeholders = implode(',', array_fill(0, count($taskIds), '?'));

        // Fetch roles associated with the task via its assigned users
        $stmtRoles = $this->db->prepare("
            SELECT ta.task_id, r.id as role_id, r.name as role_name
            FROM task_assignments ta
            JOIN users u ON ta.user_id = u.id
            JOIN roles r ON u.role_id = r.id
            WHERE ta.task_id IN ($placeholders)
        ");
        $stmtRoles->execute($taskIds);
        $rolesData = $stmtRoles->fetchAll(\PDO::FETCH_ASSOC);

        $taskRoles = [];
        foreach ($rolesData as $rd) {
            $taskRoles[$rd['task_id']][] = $rd;
        }

        // Fetch assignments
        $stmtAssign = $this->db->prepare("
            SELECT ta.task_id, u.id as user_id, u.full_name
            FROM task_assignments ta
            JOIN users u ON ta.user_id = u.id
            WHERE ta.task_id IN ($placeholders)
        ");
        $stmtAssign->execute($taskIds);
        $assignData = $stmtAssign->fetchAll(\PDO::FETCH_ASSOC);

        $taskAssigns = [];
        foreach ($assignData as $ad) {
            $taskAssigns[$ad['task_id']][] = $ad;
        }

        // Map back to tasks
        foreach ($tasks as &$task) {
            $tid = $task['id'];
            
            $task['role_name'] = isset($taskRoles[$tid]) ? implode(', ', array_column($taskRoles[$tid], 'role_name')) : '';
            $task['role_ids_csv'] = isset($taskRoles[$tid]) ? implode(',', array_column($taskRoles[$tid], 'role_id')) : '';
            
            $task['assigned_to_names'] = isset($taskAssigns[$tid]) ? implode(', ', array_column($taskAssigns[$tid], 'full_name')) : '';
            $task['assigned_to_ids'] = isset($taskAssigns[$tid]) ? implode(',', array_column($taskAssigns[$tid], 'user_id')) : '';
        }

        return $tasks;
    }

    public function create($data)
    {
        $id = $this->generateUuid();
        $inTransaction = $this->db->inTransaction();
        if (!$inTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare("
                INSERT INTO `tasks` (`id`, `project_id`, `assigned_to`, `title`, `description`, `status`, `due_date`, `due_time`, `priority`, `is_completed`, `is_incomplete`, `admin_alert_sent`, `is_recurring`, `recurring_type`, `recurring_parent_id`, `next_repeat_date`, `repeat_status`) 
                VALUES (:id, :project_id, :assigned_to, :title, :description, :status, :due_date, :due_time, :priority, :is_completed, :is_incomplete, :admin_alert_sent, :is_recurring, :recurring_type, :recurring_parent_id, :next_repeat_date, :repeat_status)
            ");
            
            $result = $stmt->execute([
                'id' => $id,
                'project_id' => $data['project_id'],
                'assigned_to' => $data['assigned_to'] ?: (!empty($data['assigned_users']) ? $data['assigned_users'][0] : ''),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'due_date' => $data['due_date'],
                'due_time' => $data['due_time'] ?? null,
                'priority' => $data['priority'] ?? 'medium',
                'is_completed' => $data['is_completed'] ?? 0,
                'is_incomplete' => $data['is_incomplete'] ?? 0,
                'admin_alert_sent' => $data['admin_alert_sent'] ?? 0,
                'is_recurring' => $data['is_recurring'] ?? 0,
                'recurring_type' => $data['recurring_type'] ?? null,
                'recurring_parent_id' => $data['recurring_parent_id'] ?? null,
                'next_repeat_date' => $data['next_repeat_date'] ?? null,
                'repeat_status' => $data['repeat_status'] ?? 'active'
            ]);
    
            if ($result) {
                $assignedUsers = $data['assigned_users'] ?? [];
                if (empty($assignedUsers) && !empty($data['assigned_to'])) {
                    $assignedUsers = [$data['assigned_to']];
                }
                
                foreach ($assignedUsers as $userId) {
                    $stmtIns = $this->db->prepare("INSERT INTO task_assignments (id, task_id, user_id) VALUES (:id, :task_id, :user_id)");
                    $stmtIns->execute([
                        'id' => $this->generateUuid(),
                        'task_id' => $id,
                        'user_id' => $userId
                    ]);
                }
            }

            if (!$inTransaction) {
                $this->db->commit();
            }
            return $result ? $id : false;
        } catch (\Exception $e) {
            if (!$inTransaction) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function update($id, $data)
    {
        $inTransaction = $this->db->inTransaction();
        if (!$inTransaction) {
            $this->db->beginTransaction();
        }

        try {
            $stmt = $this->db->prepare("
                UPDATE `tasks` SET 
                    `assigned_to` = :assigned_to,
                    `title` = :title,
                    `description` = :description,
                    `status` = :status,
                    `due_date` = :due_date,
                    `due_time` = :due_time,
                    `priority` = :priority,
                    `is_completed` = :is_completed,
                    `is_incomplete` = :is_incomplete,
                    `completed_at` = :completed_at,
                    `admin_alert_sent` = :admin_alert_sent,
                    `project_id` = :project_id,
                    `is_recurring` = :is_recurring,
                    `recurring_type` = :recurring_type,
                    `recurring_parent_id` = :recurring_parent_id,
                    `next_repeat_date` = :next_repeat_date,
                    `repeat_status` = :repeat_status
                WHERE `id` = :id
            ");
            
            $result = $stmt->execute([
                'id' => $id,
                'assigned_to' => $data['assigned_to'] ?: (!empty($data['assigned_users']) ? $data['assigned_users'][0] : ''),
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'status' => $data['status'],
                'due_date' => $data['due_date'],
                'due_time' => $data['due_time'] ?? null,
                'priority' => $data['priority'],
                'is_completed' => $data['is_completed'] ?? 0,
                'is_incomplete' => $data['is_incomplete'] ?? 0,
                'completed_at' => $data['completed_at'] ?? null,
                'admin_alert_sent' => $data['admin_alert_sent'] ?? 0,
                'project_id' => $data['project_id'],
                'is_recurring' => $data['is_recurring'] ?? 0,
                'recurring_type' => $data['recurring_type'] ?? null,
                'recurring_parent_id' => $data['recurring_parent_id'] ?? null,
                'next_repeat_date' => $data['next_repeat_date'] ?? null,
                'repeat_status' => $data['repeat_status'] ?? 'active'
            ]);
    
            if ($result) {

                // Sync assignments
                $stmtDel = $this->db->prepare("DELETE FROM task_assignments WHERE task_id = :task_id");
                $stmtDel->execute(['task_id' => $id]);
                
                $assignedUsers = $data['assigned_users'] ?? [];
                if (empty($assignedUsers) && !empty($data['assigned_to'])) {
                    $assignedUsers = [$data['assigned_to']];
                }
                
                foreach ($assignedUsers as $userId) {
                    $stmtIns = $this->db->prepare("INSERT INTO task_assignments (id, task_id, user_id) VALUES (:id, :task_id, :user_id)");
                    $stmtIns->execute([
                        'id' => $this->generateUuid(),
                        'task_id' => $id,
                        'user_id' => $userId
                    ]);
                }
            }
            
            if (!$inTransaction) {
                $this->db->commit();
            }
            return $result;
        } catch (\Exception $e) {
            if (!$inTransaction) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }

    public function softDelete($id)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET deleted_at = NOW() WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE id = :id AND deleted_at IS NULL");
        $stmt->execute(['id' => $id]);
        $task = $stmt->fetch(\PDO::FETCH_ASSOC);
        
        if ($task) {
            $stmt = $this->db->prepare("SELECT user_id FROM task_assignments WHERE task_id = :id");
            $stmt->execute(['id' => $id]);
            $task['assigned_users'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);

            $stmt = $this->db->prepare("
                SELECT DISTINCT u.role_id 
                FROM task_assignments ta
                JOIN users u ON ta.user_id = u.id
                WHERE ta.task_id = :id
            ");
            $stmt->execute(['id' => $id]);
            $task['role_ids'] = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        }
        
        return $task;
    }

    public function countAll($filters = [], $search = '')
    {
        $sql = "
            SELECT COUNT(DISTINCT t.id)
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            WHERE t.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['project_id'])) {
            $sql .= " AND t.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND (t.assigned_to = :assigned_to OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :assigned_to2))";
            $params['assigned_to'] = $filters['assigned_to'];
            $params['assigned_to2'] = $filters['assigned_to'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }
        if (!empty($search)) {
            $sql .= " AND (t.title LIKE :search OR p.project_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function countByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE (assigned_to = :user_id1 OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2)) AND deleted_at IS NULL");
        $stmt->execute(['user_id1' => $userId, 'user_id2' => $userId]);
        return $stmt->fetchColumn();
    }

    public function countByStatusAndUser($status, $userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE status = :status AND (assigned_to = :user_id1 OR id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2)) AND deleted_at IS NULL");
        $stmt->execute(['status' => $status, 'user_id1' => $userId, 'user_id2' => $userId]);
        return $stmt->fetchColumn();
    }

    public function listRecentByUser($userId, $limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, p.project_name 
            FROM tasks t 
            LEFT JOIN projects p ON t.project_id = p.id 
            WHERE (t.assigned_to = :user_id1 OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2)) AND t.deleted_at IS NULL 
            ORDER BY t.updated_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id1', $userId);
        $stmt->bindValue(':user_id2', $userId);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    private function generateUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function listByPriority($priority, $userId = null)
    {
        $sql = "
            SELECT t.*, p.project_name, r.name as role_name,
                   GROUP_CONCAT(u.full_name SEPARATOR ', ') as assigned_to_names,
                   GROUP_CONCAT(u.id SEPARATOR ', ') as assigned_to_ids
            FROM tasks t 
            LEFT JOIN projects p ON t.project_id = p.id 
            LEFT JOIN task_assignments ta ON t.id = ta.task_id
            LEFT JOIN users u ON ta.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            WHERE t.priority = :priority AND t.deleted_at IS NULL
        ";
        
        $params = ['priority' => $priority];
        if ($userId) {
            $sql .= " AND (t.assigned_to = :user_id OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id))";
            $params['user_id'] = $userId;
        }
        
        $sql .= " GROUP BY t.id";
        $sql .= " ORDER BY t.due_date ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function updateRecurringStatus($id, $isRecurring, $type = null, $nextDate = null, $status = 'active')
    {
        $stmt = $this->db->prepare("
            UPDATE tasks SET 
                is_recurring = :is_recurring,
                recurring_type = :recurring_type,
                next_repeat_date = :next_repeat_date,
                repeat_status = :repeat_status
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'is_recurring' => $isRecurring,
            'recurring_type' => $type,
            'next_repeat_date' => $nextDate,
            'repeat_status' => $status
        ]);
    }

    public function logRecurringGeneration($taskId, $type, $generatedTaskId, $generatedDate, $createdBy)
    {
        $id = $this->generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO task_recurring_logs (id, task_id, recurring_type, generated_task_id, generated_date, created_by)
            VALUES (:id, :task_id, :type, :generated_task_id, :generated_date, :created_by)
        ");
        return $stmt->execute([
            'id' => $id,
            'task_id' => $taskId,
            'type' => $type,
            'generated_task_id' => $generatedTaskId,
            'generated_date' => $generatedDate,
            'created_by' => $createdBy
        ]);
    }

    public function listRecurringLogs($taskId)
    {
        $stmt = $this->db->prepare("
            SELECT l.*, u.full_name as creator_name, t.title as generated_task_title
            FROM task_recurring_logs l
            JOIN users u ON l.created_by = u.id
            JOIN tasks t ON l.generated_task_id = t.id
            WHERE l.task_id = :task_id
            ORDER BY l.created_at DESC
        ");
        $stmt->execute(['task_id' => $taskId]);
        return $stmt->fetchAll();
    }

    public function getRecurringTasks()
    {
        $stmt = $this->db->prepare("
            SELECT t.*, GROUP_CONCAT(ta.user_id) as assigned_to_ids
            FROM tasks t 
            LEFT JOIN task_assignments ta ON t.id = ta.task_id
            WHERE t.is_recurring = 1 
            AND t.repeat_status = 'active' 
            AND t.deleted_at IS NULL 
            AND (t.next_repeat_date IS NULL OR t.next_repeat_date <= CURDATE())
            GROUP BY t.id
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getOverdueTasks($userId = null, $search = '', $limit = null, $offset = null)
    {
        $sql = "
            SELECT t.*, p.project_name, u.full_name as assigned_to_name,
                   DATEDIFF(CURRENT_DATE(), t.due_date) as days_overdue
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.due_date < CURRENT_DATE()
              AND t.status != 'completed'
              AND t.deleted_at IS NULL
        ";
        $params = [];

        if ($userId) {
            $sql .= " AND (t.assigned_to = :user_id OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2))";
            $params['user_id']  = $userId;
            $params['user_id2'] = $userId;
        }
        if (!empty($search)) {
            $sql .= " AND (t.title LIKE :search OR p.project_name LIKE :search OR u.full_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $sql .= " ORDER BY days_overdue DESC";

        if ($limit !== null && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null && $offset >= 0) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countOverdueTasks($userId = null, $search = '')
    {
        $sql = "
            SELECT COUNT(*)
            FROM tasks t
            LEFT JOIN projects p ON t.project_id = p.id
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.due_date < CURRENT_DATE()
              AND t.status != 'completed'
              AND t.deleted_at IS NULL
        ";
        $params = [];

        if ($userId) {
            $sql .= " AND (t.assigned_to = :user_id OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2))";
            $params['user_id']  = $userId;
            $params['user_id2'] = $userId;
        }
        if (!empty($search)) {
            $sql .= " AND (t.title LIKE :search OR p.project_name LIKE :search OR u.full_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function getOverdueSummary($userId = null)
    {
        $sql = "
            SELECT
                COUNT(*) as total_overdue,
                COUNT(DISTINCT t.assigned_to) as staff_with_overdue,
                COALESCE(MAX(DATEDIFF(CURRENT_DATE(), t.due_date)), 0) as max_overdue_days
            FROM tasks t
            WHERE t.due_date < CURRENT_DATE()
              AND t.status != 'completed'
              AND t.deleted_at IS NULL
        ";
        $params = [];
        if ($userId) {
            $sql .= " AND (t.assigned_to = :user_id OR t.id IN (SELECT task_id FROM task_assignments WHERE user_id = :user_id2))";
            $params['user_id'] = $userId;
            $params['user_id2'] = $userId;
        }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
