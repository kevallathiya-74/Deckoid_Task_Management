<?php

namespace App\Models;

use App\Core\Database;

class SopModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function listAll($filters = [], $search = '', $limit = null, $offset = null)
    {
        $sql = "
            SELECT s.*, 
                   u.full_name as staff_name, 
                   u2.full_name as creator_name
            FROM sops s
            JOIN users u ON s.staff_id = u.id
            JOIN users u2 ON s.created_by = u2.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['staff_id'])) {
            $sql .= " AND s.staff_id = :staff_id";
            $params['staff_id'] = $filters['staff_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($search)) {
            $sql .= " AND (s.description LIKE :search OR u.full_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $sql .= " ORDER BY s.created_at DESC";

        if ($limit !== null && $limit > 0) {
            $sql .= " LIMIT " . (int)$limit;
            if ($offset !== null && $offset >= 0) {
                $sql .= " OFFSET " . (int)$offset;
            }
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function countAll($filters = [], $search = '')
    {
        $sql = "
            SELECT COUNT(*) 
            FROM sops s
            JOIN users u ON s.staff_id = u.id
            WHERE 1=1
        ";
        $params = [];

        if (!empty($filters['staff_id'])) {
            $sql .= " AND s.staff_id = :staff_id";
            $params['staff_id'] = $filters['staff_id'];
        }

        if (!empty($filters['status'])) {
            $sql .= " AND s.status = :status";
            $params['status'] = $filters['status'];
        }

        if (!empty($search)) {
            $sql .= " AND (s.description LIKE :search OR u.full_name LIKE :search)";
            $params['search'] = "%$search%";
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn();
    }

    public function create($data)
    {
        $id = $this->generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO sops (id, staff_id, created_by, description, status) 
            VALUES (:id, :staff_id, :created_by, :description, :status)
        ");
        $result = $stmt->execute([
            'id' => $id,
            'staff_id' => $data['staff_id'],
            'created_by' => $data['created_by'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'active'
        ]);
        
        return $result ? $id : false;
    }

    public function update($id, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE sops 
            SET staff_id = :staff_id, 
                description = :description, 
                status = :status
            WHERE id = :id
        ");
        return $stmt->execute([
            'id' => $id,
            'staff_id' => $data['staff_id'],
            'description' => $data['description'],
            'status' => $data['status'] ?? 'active'
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM sops WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM sops WHERE id = :id");
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
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
