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
            SELECT t.*, p.project_name, u.full_name as assigned_to_name, r.name as role_name
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
            INSERT INTO tasks (id, project_id, assigned_to, role_id, title, description, status, due_date, priority) 
            VALUES (:id, :project_id, :assigned_to, :role_id, :title, :description, :status, :due_date, :priority)
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
            'priority' => $data['priority'] ?? 'medium'
        ]);

        return $result ? $id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE tasks SET 
                assigned_to = :assigned_to,
                role_id = :role_id,
                title = :title,
                description = :description,
                status = :status,
                due_date = :due_date,
                priority = :priority,
                progress_percentage = :progress,
                status_notes = :notes
            WHERE id = :id
        ");
        
        return $stmt->execute([
            'id' => $id,
            'assigned_to' => $data['assigned_to'],
            'role_id' => $data['role_id'],
            'title' => $data['title'],
            'description' => $data['description'] ?? null,
            'status' => $data['status'],
            'due_date' => $data['due_date'],
            'priority' => $data['priority'],
            'progress' => $data['progress_percentage'] ?? 0,
            'notes' => $data['status_notes'] ?? null
        ]);
    }

    public function softDelete($id)
    {
        $stmt = $this->db->prepare("UPDATE tasks SET deleted_at = NOW() WHERE id = :id");
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
