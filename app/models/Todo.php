<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class Todo
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listAll($filters = [])
    {
        $sql = "
            SELECT t.*, u.full_name as assigned_to_name, b.full_name as assigned_by_name
            FROM todo_lists t
            LEFT JOIN users u ON t.assigned_to = u.id
            LEFT JOIN users b ON t.assigned_by = b.id
            WHERE 1=1
        ";
        
        $params = [];

        if (!empty($filters['assigned_to'])) {
            $sql .= " AND t.assigned_to = :assigned_to";
            $params['assigned_to'] = $filters['assigned_to'];
        }
        if (!empty($filters['created_by'])) {
            $sql .= " AND t.created_by = :created_by";
            $params['created_by'] = $filters['created_by'];
        }
        if (!empty($filters['status'])) {
            $sql .= " AND t.status = :status";
            $params['status'] = $filters['status'];
        }

        $sql .= " ORDER BY t.is_pinned DESC, t.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM todo_lists WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        $id = $this->generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO todo_lists (id, title, assigned_to, assigned_by, status, priority, notes, is_pinned, todo_type, deadline_date, deadline_time, reminder_sent, created_by) 
            VALUES (:id, :title, :assigned_to, :assigned_by, :status, :priority, :notes, :is_pinned, :todo_type, :deadline_date, :deadline_time, 0, :created_by)
        ");
        
        $success = $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'assigned_to' => $data['assigned_to'],
            'assigned_by' => $_SESSION['user_id'],
            'status' => $data['status'] ?? 'pending',
            'priority' => $data['priority'] ?? 'medium',
            'notes' => $data['notes'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
            'todo_type' => $data['todo_type'] ?? 'Normal Task',
            'deadline_date' => $data['deadline_date'] ?? null,
            'deadline_time' => $data['deadline_time'] ?? null,
            'created_by' => $data['created_by'] ?? $_SESSION['user_id']
        ]);

        return $success ? $id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE todo_lists SET 
                title = :title, 
                assigned_to = :assigned_to, 
                status = :status, 
                priority = :priority, 
                notes = :notes,
                is_pinned = :is_pinned,
                todo_type = :todo_type,
                deadline_date = :deadline_date,
                deadline_time = :deadline_time
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'title' => $data['title'],
            'assigned_to' => $data['assigned_to'],
            'status' => $data['status'],
            'priority' => $data['priority'],
            'notes' => $data['notes'] ?? null,
            'is_pinned' => $data['is_pinned'] ?? false,
            'todo_type' => $data['todo_type'] ?? 'Normal Task',
            'deadline_date' => $data['deadline_date'] ?? null,
            'deadline_time' => $data['deadline_time'] ?? null
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM todo_lists WHERE id = :id");
        return $stmt->execute(['id' => $id]);
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
}
