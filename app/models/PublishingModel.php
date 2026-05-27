<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class PublishingModel
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function generateUuid()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    public function fetchReportData($userId, $isAdmin, $month, $year)
    {
        // Fetch users to populate dropdown
        $userModel = new User();
        $users = $userModel->listAll(['status' => 'active']);

        $params = [
            'month' => $month,
            'year' => $year
        ];

        if ($isAdmin) {
            // Admin sees all tables for the month/year
            $stmt = $this->db->prepare("
                SELECT * FROM publishing_tables 
                WHERE MONTH(created_at) = :month AND YEAR(created_at) = :year
                ORDER BY category ASC, week_number ASC
            ");
            $stmt->execute($params);
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            // Staff sees only tables assigned to them
            $stmt = $this->db->prepare("
                SELECT t.* FROM publishing_tables t
                INNER JOIN publishing_assignments a ON t.id = a.table_id
                WHERE a.user_id = :user_id
                  AND MONTH(t.created_at) = :month AND YEAR(t.created_at) = :year
                ORDER BY t.category ASC, t.week_number ASC
            ");
            $params['user_id'] = $userId;
            $stmt->execute($params);
            $tables = $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        if (empty($tables)) {
            return [
                'tables' => [],
                'rows' => [],
                'assignments' => [],
                'users' => $users
            ];
        }

        $tableIds = array_column($tables, 'id');
        $placeholders = implode(',', array_fill(0, count($tableIds), '?'));

        // Fetch rows
        $stmt = $this->db->prepare("
            SELECT * FROM publishing_rows 
            WHERE table_id IN ($placeholders) 
            ORDER BY row_order ASC
        ");
        $stmt->execute($tableIds);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Fetch assignments
        $stmt = $this->db->prepare("
            SELECT a.table_id, a.user_id, u.full_name, u.username 
            FROM publishing_assignments a
            INNER JOIN users u ON a.user_id = u.id
            WHERE a.table_id IN ($placeholders)
        ");
        $stmt->execute($tableIds);
        $assignmentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Group assignments by table_id
        $assignments = [];
        foreach ($tableIds as $tid) {
            $assignments[$tid] = [];
        }
        foreach ($assignmentsData as $assign) {
            $assignments[$assign['table_id']][] = [
                'user_id' => $assign['user_id'],
                'full_name' => $assign['full_name'],
                'username' => $assign['username']
            ];
        }

        return [
            'tables' => $tables,
            'rows' => $rows,
            'assignments' => $assignments,
            'users' => $users
        ];
    }

    public function createTable($category, $weekNumber, $month, $year, $userId, $isAdmin)
    {
        if (!$isAdmin) {
            throw new \Exception("Only admins can create tables.");
        }

        // Prevent invalid week creation (check if week already exists for this category/month/year)
        $stmt = $this->db->prepare("
            SELECT id FROM publishing_tables 
            WHERE category = :category 
              AND week_number = :week_number 
              AND MONTH(created_at) = :month 
              AND YEAR(created_at) = :year
        ");
        $stmt->execute([
            'category' => $category,
            'week_number' => $weekNumber,
            'month' => $month,
            'year' => $year
        ]);
        if ($stmt->fetch()) {
            throw new \Exception("Week $weekNumber already exists for this category.");
        }

        $this->db->beginTransaction();
        try {
            $tableId = $this->generateUuid();
            // Set created_at to YYYY-MM-DD to respect month/year filtering
            $createdAt = sprintf('%04d-%02d-01 00:00:00', $year, $month);

            $stmt = $this->db->prepare("
                INSERT INTO publishing_tables (id, category, week_number, created_by, created_at, updated_at)
                VALUES (:id, :category, :week_number, :created_by, :created_at, NOW())
            ");
            $stmt->execute([
                'id' => $tableId,
                'category' => $category,
                'week_number' => $weekNumber,
                'created_by' => $userId,
                'created_at' => $createdAt
            ]);

            // Add 5 default rows
            $stmtRow = $this->db->prepare("
                INSERT INTO publishing_rows (id, table_id, company_name, task_box_1, task_box_2, task_box_3, task_box_4, task_box_5, task_box_6, task_box_7, row_order)
                VALUES (:id, :table_id, '', '', '', '', '', '', '', '', :row_order)
            ");
            for ($i = 0; $i < 5; $i++) {
                $stmtRow->execute([
                    'id' => $this->generateUuid(),
                    'table_id' => $tableId,
                    'row_order' => $i
                ]);
            }

            $this->db->commit();
            return $tableId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function saveReportData($data, $userId, $isAdmin)
    {
        $this->db->beginTransaction();
        try {
            // Loop through all tables in data
            if (isset($data['tables']) && is_array($data['tables'])) {
                foreach ($data['tables'] as $table) {
                    $tableId = $table['id'];

                    if ($isAdmin) {
                        // Admin updates/saves table level fields if any, and assignments
                        // Update assignments
                        $stmtDel = $this->db->prepare("DELETE FROM publishing_assignments WHERE table_id = :table_id");
                        $stmtDel->execute(['table_id' => $tableId]);

                        if (isset($data['assignments'][$tableId]) && is_array($data['assignments'][$tableId])) {
                            $stmtIns = $this->db->prepare("
                                INSERT INTO publishing_assignments (id, table_id, user_id) 
                                VALUES (:id, :table_id, :user_id)
                            ");
                            // Avoid duplicate user IDs in assignments
                            $addedUsers = [];
                            foreach ($data['assignments'][$tableId] as $user) {
                                $uid = $user['user_id'] ?? $user; // handle user object or user_id string
                                if (!empty($uid) && !in_array($uid, $addedUsers)) {
                                    $addedUsers[] = $uid;
                                    $stmtIns->execute([
                                        'id' => $this->generateUuid(),
                                        'table_id' => $tableId,
                                        'user_id' => $uid
                                    ]);
                                }
                            }
                        }
                    }

                    // Save Rows
                    if (isset($data['rows']) && is_array($data['rows'])) {
                        foreach ($data['rows'] as $row) {
                            if ($row['table_id'] !== $tableId) {
                                continue;
                            }

                            // If staff, verify assignment
                            if (!$isAdmin) {
                                $stmtVerify = $this->db->prepare("
                                    SELECT 1 FROM publishing_assignments 
                                    WHERE table_id = :table_id AND user_id = :user_id
                                ");
                                $stmtVerify->execute([
                                    'table_id' => $tableId,
                                    'user_id' => $userId
                                ]);
                                if (!$stmtVerify->fetch()) {
                                    throw new \Exception("Unauthorized: You are not assigned to this table.");
                                }
                            }

                            // Prepare SQL depending on role and existence of row
                            $isTemp = (strpos($row['id'], 'temp-') === 0);

                            if ($isTemp) {
                                if (!$isAdmin) {
                                    throw new \Exception("Unauthorized: Staff cannot add new rows.");
                                }
                                $rowId = $this->generateUuid();
                                $stmtInsRow = $this->db->prepare("
                                    INSERT INTO publishing_rows (id, table_id, company_name, task_box_1, task_box_2, task_box_3, task_box_4, task_box_5, task_box_6, task_box_7, row_order)
                                    VALUES (:id, :table_id, :company_name, :task_box_1, :task_box_2, :task_box_3, :task_box_4, :task_box_5, :task_box_6, :task_box_7, :row_order)
                                ");
                                $stmtInsRow->execute([
                                    'id' => $rowId,
                                    'table_id' => $tableId,
                                    'company_name' => $row['company_name'] ?? '',
                                    'task_box_1' => $row['task_box_1'] ?? '',
                                    'task_box_2' => $row['task_box_2'] ?? '',
                                    'task_box_3' => $row['task_box_3'] ?? '',
                                    'task_box_4' => $row['task_box_4'] ?? '',
                                    'task_box_5' => $row['task_box_5'] ?? '',
                                    'task_box_6' => $row['task_box_6'] ?? '',
                                    'task_box_7' => $row['task_box_7'] ?? '',
                                    'row_order' => $row['row_order'] ?? 0
                                ]);
                            } else {
                                if ($isAdmin) {
                                    // Admin can update everything including company name
                                    $stmtUpd = $this->db->prepare("
                                        UPDATE publishing_rows 
                                        SET company_name = :company_name,
                                            task_box_1 = :task_box_1,
                                            task_box_2 = :task_box_2,
                                            task_box_3 = :task_box_3,
                                            task_box_4 = :task_box_4,
                                            task_box_5 = :task_box_5,
                                            task_box_6 = :task_box_6,
                                            task_box_7 = :task_box_7,
                                            row_order = :row_order
                                        WHERE id = :id
                                    ");
                                    $stmtUpd->execute([
                                        'id' => $row['id'],
                                        'company_name' => $row['company_name'] ?? '',
                                        'task_box_1' => $row['task_box_1'] ?? '',
                                        'task_box_2' => $row['task_box_2'] ?? '',
                                        'task_box_3' => $row['task_box_3'] ?? '',
                                        'task_box_4' => $row['task_box_4'] ?? '',
                                        'task_box_5' => $row['task_box_5'] ?? '',
                                        'task_box_6' => $row['task_box_6'] ?? '',
                                        'task_box_7' => $row['task_box_7'] ?? '',
                                        'row_order' => $row['row_order'] ?? 0
                                    ]);
                                } else {
                                    // Staff can only update task boxes
                                    $stmtUpd = $this->db->prepare("
                                        UPDATE publishing_rows 
                                        SET task_box_1 = :task_box_1,
                                            task_box_2 = :task_box_2,
                                            task_box_3 = :task_box_3,
                                            task_box_4 = :task_box_4,
                                            task_box_5 = :task_box_5,
                                            task_box_6 = :task_box_6,
                                            task_box_7 = :task_box_7
                                        WHERE id = :id
                                    ");
                                    $stmtUpd->execute([
                                        'id' => $row['id'],
                                        'task_box_1' => $row['task_box_1'] ?? '',
                                        'task_box_2' => $row['task_box_2'] ?? '',
                                        'task_box_3' => $row['task_box_3'] ?? '',
                                        'task_box_4' => $row['task_box_4'] ?? '',
                                        'task_box_5' => $row['task_box_5'] ?? '',
                                        'task_box_6' => $row['task_box_6'] ?? '',
                                        'task_box_7' => $row['task_box_7'] ?? ''
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function deleteTable($tableId, $isAdmin)
    {
        if (!$isAdmin) {
            throw new \Exception("Only admins can delete tables.");
        }

        $stmt = $this->db->prepare("DELETE FROM publishing_tables WHERE id = :id");
        return $stmt->execute(['id' => $tableId]);
    }
}
