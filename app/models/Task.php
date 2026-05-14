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

    public function listAll($filters = [])
    {
        $sql = "
            SELECT 
                t.id, t.project_id, t.assigned_to, t.role_id, t.title, t.description, 
                t.status, t.is_completed, t.is_incomplete, t.status_notes, 
                t.progress_percentage, t.due_date, t.due_time, t.priority, 
                t.completed_at, t.admin_alert_sent, t.created_at, t.updated_at,
                t.is_recurring, t.recurring_type, t.recurring_parent_id, 
                t.next_repeat_date, t.repeat_status,
                p.project_name, p.client_name,
                u.full_name as assigned_to_name, 
                r.name as role_name
            FROM tasks t
            JOIN projects p ON t.project_id = p.id
            JOIN users u ON t.assigned_to = u.id
            JOIN roles r ON t.role_id = r.id
            WHERE t.deleted_at IS NULL
        ";
        
        $params = [];
        if (!empty($filters['project_id'])) {
            $sql .= " AND t.project_id = :project_id";
            $params['project_id'] = $filters['project_id'];
        }
        
        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = :assigned_to";
            $params['assigned_to'] = $filters['assigned_to'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($filters['role_id'])) {
            $sql .= " AND t.role_id = :role_id";
            $params['role_id'] = $filters['role_id'];
        }

        $sql .= " ORDER BY t.due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $id = $this->generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO `tasks` (`id`, `project_id`, `assigned_to`, `role_id`, `title`, `description`, `status`, `due_date`, `due_time`, `priority`, `progress_percentage`, `status_notes`, `is_completed`, `is_incomplete`, `admin_alert_sent`, `is_recurring`, `recurring_type`, `recurring_parent_id`, `next_repeat_date`, `repeat_status`) 
            VALUES (:id, :project_id, :assigned_to, :role_id, :title, :description, :status, :due_date, :due_time, :priority, :progress_percentage, :status_notes, :is_completed, :is_incomplete, :admin_alert_sent, :is_recurring, :recurring_type, :recurring_parent_id, :next_repeat_date, :repeat_status)
        ");
        
        $result = $stmt->execute([
            'id' => $id,
            'project_id' => $data['project_id'],
            'assigned_to' => $data['assigned_to'],
            'role_id' => $data['role_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'] ?? 'pending',
            'due_date' => $data['due_date'],
            'due_time' => $data['due_time'] ?? null,
            'priority' => $data['priority'] ?? 'medium',
            'progress_percentage' => $data['progress_percentage'] ?? 0,
            'status_notes' => $data['status_notes'] ?? null,
            'is_completed' => $data['is_completed'] ?? 0,
            'is_incomplete' => $data['is_incomplete'] ?? 0,
            'admin_alert_sent' => $data['admin_alert_sent'] ?? 0,
            'is_recurring' => $data['is_recurring'] ?? 0,
            'recurring_type' => $data['recurring_type'] ?? null,
            'recurring_parent_id' => $data['recurring_parent_id'] ?? null,
            'next_repeat_date' => $data['next_repeat_date'] ?? null,
            'repeat_status' => $data['repeat_status'] ?? 'active'
        ]);

        return $result ? $id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE `tasks` SET 
                `assigned_to` = :assigned_to,
                `role_id` = :role_id,
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
                `progress_percentage` = :progress,
                `status_notes` = :notes,
                `project_id` = :project_id,
                `is_recurring` = :is_recurring,
                `recurring_type` = :recurring_type,
                `recurring_parent_id` = :recurring_parent_id,
                `next_repeat_date` = :next_repeat_date,
                `repeat_status` = :repeat_status
            WHERE `id` = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'assigned_to' => $data['assigned_to'],
            'role_id' => $data['role_id'],
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
            'progress' => $data['progress_percentage'] ?? 0,
            'notes' => $data['status_notes'] ?? null,
            'project_id' => $data['project_id'],
            'is_recurring' => $data['is_recurring'] ?? 0,
            'recurring_type' => $data['recurring_type'] ?? null,
            'recurring_parent_id' => $data['recurring_parent_id'] ?? null,
            'next_repeat_date' => $data['next_repeat_date'] ?? null,
            'repeat_status' => $data['repeat_status'] ?? 'active'
        ]);
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
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function countAll()
    {
        $stmt = $this->db->query("SELECT COUNT(*) FROM tasks WHERE deleted_at IS NULL");
        return $stmt->fetchColumn();
    }

    public function countByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE assigned_to = :user_id AND deleted_at IS NULL");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchColumn();
    }

    public function countByStatusAndUser($status, $userId)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tasks WHERE status = :status AND assigned_to = :user_id AND deleted_at IS NULL");
        $stmt->execute(['status' => $status, 'user_id' => $userId]);
        return $stmt->fetchColumn();
    }

    public function listRecentByUser($userId, $limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT t.*, p.project_name 
            FROM tasks t 
            JOIN projects p ON t.project_id = p.id 
            WHERE t.assigned_to = :user_id AND t.deleted_at IS NULL 
            ORDER BY t.updated_at DESC 
            LIMIT :limit
        ");
        $stmt->bindValue(':user_id', $userId);
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
            SELECT t.*, u.full_name as staff_name, p.project_name, r.name as role_name
            FROM tasks t 
            JOIN users u ON t.assigned_to = u.id 
            JOIN projects p ON t.project_id = p.id 
            JOIN roles r ON t.role_id = r.id
            WHERE t.priority = :priority AND t.deleted_at IS NULL
        ";
        
        $params = ['priority' => $priority];
        if ($userId) {
            $sql .= " AND t.assigned_to = :user_id";
            $params['user_id'] = $userId;
        }
        
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
            SELECT * FROM tasks 
            WHERE is_recurring = 1 
            AND repeat_status = 'active' 
            AND deleted_at IS NULL 
            AND (next_repeat_date IS NULL OR next_repeat_date <= CURDATE())
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
