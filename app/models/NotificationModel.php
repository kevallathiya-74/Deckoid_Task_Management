<?php

namespace App\Models;

use App\Core\Database;

class NotificationModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function create($data)
    {
        $id = $this->generateUuid();
        $stmt = $this->db->prepare("
            INSERT INTO notifications (id, user_id, title, message, type, link) 
            VALUES (:id, :user_id, :title, :message, :type, :link)
        ");
        $result = $stmt->execute([
            'id' => $id,
            'user_id' => $data['user_id'],
            'title' => $data['title'],
            'message' => $data['message'],
            'type' => $data['type'] ?? 'system',
            'link' => $data['link'] ?? null
        ]);
        
        return $result ? $id : false;
    }

    public function getUnreadByUser($userId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = :user_id AND is_read = 0 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function markAsRead($id)
    {
        $stmt = $this->db->prepare("UPDATE notifications SET is_read = 1 WHERE id = :id");
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
