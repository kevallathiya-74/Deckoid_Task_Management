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
            // Staff sees only tables where they have assigned rows
            $stmt = $this->db->prepare("
                SELECT DISTINCT t.* FROM publishing_tables t
                INNER JOIN publishing_rows r ON t.id = r.table_id
                INNER JOIN publishing_row_assignments a ON r.id = a.row_id
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

        if ($isAdmin) {
            // Fetch all rows for these tables
            $stmt = $this->db->prepare("
                SELECT * FROM publishing_rows 
                WHERE table_id IN ($placeholders) 
                ORDER BY row_order ASC
            ");
            $stmt->execute($tableIds);
        } else {
            // Staff only sees their assigned rows
            $stmt = $this->db->prepare("
                SELECT r.* FROM publishing_rows r
                INNER JOIN publishing_row_assignments a ON r.id = a.row_id
                WHERE r.table_id IN ($placeholders) AND a.user_id = ?
                ORDER BY r.row_order ASC
            ");
            $rowParams = array_merge($tableIds, [$userId]);
            $stmt->execute($rowParams);
        }
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            $rowIds = [];
            $assignments = [];
        } else {
            $rowIds = array_column($rows, 'id');
            $rowPlaceholders = implode(',', array_fill(0, count($rowIds), '?'));

            // Fetch assignments mapped by row
            $stmt = $this->db->prepare("
                SELECT a.row_id, a.user_id, u.full_name, u.username 
                FROM publishing_row_assignments a
                INNER JOIN users u ON a.user_id = u.id
                WHERE a.row_id IN ($rowPlaceholders)
            ");
            $stmt->execute($rowIds);
            $assignmentsData = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Group assignments by row_id
            $assignments = [];
            foreach ($rowIds as $rid) {
                $assignments[$rid] = [];
            }
            foreach ($assignmentsData as $assign) {
                $assignments[$assign['row_id']][] = [
                    'user_id' => $assign['user_id'],
                    'full_name' => $assign['full_name'],
                    'username' => $assign['username']
                ];
            }
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

            // Check for previous week to clone structure
            $prevTableId = null;
            if ($weekNumber > 1) {
                $stmtPrev = $this->db->prepare("
                    SELECT id FROM publishing_tables 
                    WHERE category = :category 
                      AND week_number = :prev_week 
                      AND MONTH(created_at) = :month 
                      AND YEAR(created_at) = :year
                ");
                $stmtPrev->execute([
                    'category' => $category,
                    'prev_week' => $weekNumber - 1,
                    'month' => $month,
                    'year' => $year
                ]);
                $prevTableId = $stmtPrev->fetchColumn();
            }

            if ($prevTableId) {
                // Clone rows from previous table
                $stmtGetRows = $this->db->prepare("SELECT * FROM publishing_rows WHERE table_id = :table_id ORDER BY row_order ASC");
                $stmtGetRows->execute(['table_id' => $prevTableId]);
                $prevRows = $stmtGetRows->fetchAll(\PDO::FETCH_ASSOC);

                if (count($prevRows) > 0) {
                    $stmtInsertRow = $this->db->prepare("
                        INSERT INTO publishing_rows (id, table_id, company_name, task_box_1, task_box_2, task_box_3, task_box_4, task_box_5, task_box_6, task_box_7, task_status_1, task_status_2, task_status_3, task_status_4, task_status_5, task_status_6, task_status_7, row_order)
                        VALUES (:id, :table_id, :company_name, '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, :row_order)
                    ");
                    $stmtGetAssigns = $this->db->prepare("SELECT user_id FROM publishing_row_assignments WHERE row_id = :row_id");
                    $stmtInsertAssign = $this->db->prepare("INSERT INTO publishing_row_assignments (id, row_id, user_id) VALUES (:id, :row_id, :user_id)");

                    foreach ($prevRows as $oldRow) {
                        $newRowId = $this->generateUuid();
                        
                        // Insert cloned row with empty tasks
                        $stmtInsertRow->execute([
                            'id' => $newRowId,
                            'table_id' => $tableId,
                            'company_name' => $oldRow['company_name'],
                            'row_order' => $oldRow['row_order']
                        ]);

                        // Clone assignments
                        $stmtGetAssigns->execute(['row_id' => $oldRow['id']]);
                        $assigns = $stmtGetAssigns->fetchAll(\PDO::FETCH_ASSOC);
                        foreach ($assigns as $assign) {
                            $stmtInsertAssign->execute([
                                'id' => $this->generateUuid(),
                                'row_id' => $newRowId,
                                'user_id' => $assign['user_id']
                            ]);
                        }
                    }
                } else {
                    $prevTableId = null; // Fallback to default creation if previous week was empty
                }
            }
            
            // Fallback: Add 5 default rows if no previous week found or it was empty
            if (!$prevTableId) {
                $stmtRow = $this->db->prepare("
                    INSERT INTO publishing_rows (id, table_id, company_name, task_box_1, task_box_2, task_box_3, task_box_4, task_box_5, task_box_6, task_box_7, task_status_1, task_status_2, task_status_3, task_status_4, task_status_5, task_status_6, task_status_7, row_order)
                    VALUES (:id, :table_id, '', '', '', '', '', '', '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, :row_order)
                ");
                for ($i = 0; $i < 5; $i++) {
                    $stmtRow->execute([
                        'id' => $this->generateUuid(),
                        'table_id' => $tableId,
                        'row_order' => $i
                    ]);
                }
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
        $idMapping = [];
        $this->db->beginTransaction();
        try {
            // Loop through all tables in data
            if (isset($data['tables']) && is_array($data['tables'])) {
                foreach ($data['tables'] as $table) {
                    $tableId = $table['id'];

                    if ($isAdmin) {
                        // Admin updates/saves table level fields if any
                    }

                    // Save Rows
                    if (isset($data['rows']) && is_array($data['rows'])) {
                        foreach ($data['rows'] as $row) {
                            if ($row['table_id'] !== $tableId) {
                                continue;
                            }

                            $isTemp = isset($row['id']) && strpos($row['id'], 'temp-') === 0;

                            // If staff, verify assignment to row
                            if (!$isAdmin && !$isTemp) {
                                $stmtVerify = $this->db->prepare("
                                    SELECT 1 FROM publishing_row_assignments 
                                    WHERE row_id = :row_id AND user_id = :user_id
                                ");
                                $stmtVerify->execute([
                                    'row_id' => $row['id'],
                                    'user_id' => $userId
                                ]);
                                if (!$stmtVerify->fetch()) {
                                    throw new \Exception("Unauthorized: You are not assigned to this row.");
                                }
                            }

                            if ($isTemp) {
                                if (!$isAdmin) {
                                    throw new \Exception("Unauthorized: Staff cannot add new rows.");
                                }
                                $rowId = $this->generateUuid();
                                $idMapping[$row['id']] = $rowId;
                                
                                $stmtInsRow = $this->db->prepare("
                                    INSERT INTO publishing_rows (id, table_id, company_name, task_box_1, task_box_2, task_box_3, task_box_4, task_box_5, task_box_6, task_box_7, task_status_1, task_status_2, task_status_3, task_status_4, task_status_5, task_status_6, task_status_7, row_order)
                                    VALUES (:id, :table_id, :company_name, :task_box_1, :task_box_2, :task_box_3, :task_box_4, :task_box_5, :task_box_6, :task_box_7, :task_status_1, :task_status_2, :task_status_3, :task_status_4, :task_status_5, :task_status_6, :task_status_7, :row_order)
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
                                    'task_status_1' => $row['task_status_1'] ?? null,
                                    'task_status_2' => $row['task_status_2'] ?? null,
                                    'task_status_3' => $row['task_status_3'] ?? null,
                                    'task_status_4' => $row['task_status_4'] ?? null,
                                    'task_status_5' => $row['task_status_5'] ?? null,
                                    'task_status_6' => $row['task_status_6'] ?? null,
                                    'task_status_7' => $row['task_status_7'] ?? null,
                                    'row_order' => $row['row_order'] ?? 0
                                ]);
                                
                                // Insert assignments for new row
                                if (isset($data['assignments'][$row['id']]) && is_array($data['assignments'][$row['id']])) {
                                    $stmtInsAssign = $this->db->prepare("
                                        INSERT INTO publishing_row_assignments (id, row_id, user_id) 
                                        VALUES (:id, :row_id, :user_id)
                                    ");
                                    $addedUsers = [];
                                    foreach ($data['assignments'][$row['id']] as $user) {
                                        $uid = is_array($user) ? ($user['user_id'] ?? null) : $user;
                                        if (!empty($uid) && !in_array($uid, $addedUsers)) {
                                            $addedUsers[] = $uid;
                                            $stmtInsAssign->execute([
                                                'id' => $this->generateUuid(),
                                                'row_id' => $rowId,
                                                'user_id' => $uid
                                            ]);
                                        }
                                    }
                                }

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
                                            task_status_1 = :task_status_1,
                                            task_status_2 = :task_status_2,
                                            task_status_3 = :task_status_3,
                                            task_status_4 = :task_status_4,
                                            task_status_5 = :task_status_5,
                                            task_status_6 = :task_status_6,
                                            task_status_7 = :task_status_7,
                                            row_order = :row_order,
                                            updated_at = NOW()
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
                                        'task_status_1' => $row['task_status_1'] ?? null,
                                        'task_status_2' => $row['task_status_2'] ?? null,
                                        'task_status_3' => $row['task_status_3'] ?? null,
                                        'task_status_4' => $row['task_status_4'] ?? null,
                                        'task_status_5' => $row['task_status_5'] ?? null,
                                        'task_status_6' => $row['task_status_6'] ?? null,
                                        'task_status_7' => $row['task_status_7'] ?? null,
                                        'row_order' => $row['row_order'] ?? 0
                                    ]);

                                    // Admin saves row assignments
                                    $stmtDelAssign = $this->db->prepare("DELETE FROM publishing_row_assignments WHERE row_id = :row_id");
                                    $stmtDelAssign->execute(['row_id' => $row['id']]);
                                    
                                    if (isset($data['assignments'][$row['id']]) && is_array($data['assignments'][$row['id']])) {
                                        $stmtInsAssign = $this->db->prepare("
                                            INSERT INTO publishing_row_assignments (id, row_id, user_id) 
                                            VALUES (:id, :row_id, :user_id)
                                        ");
                                        $addedUsers = [];
                                        foreach ($data['assignments'][$row['id']] as $user) {
                                            $uid = is_array($user) ? ($user['user_id'] ?? null) : $user;
                                            if (!empty($uid) && !in_array($uid, $addedUsers)) {
                                                $addedUsers[] = $uid;
                                                $stmtInsAssign->execute([
                                                    'id' => $this->generateUuid(),
                                                    'row_id' => $row['id'],
                                                    'user_id' => $uid
                                                ]);
                                            }
                                        }
                                    }
                                } else {
                                    // Staff can only update task boxes and task statuses
                                    $stmtUpd = $this->db->prepare("
                                        UPDATE publishing_rows 
                                        SET task_box_1 = :task_box_1,
                                            task_box_2 = :task_box_2,
                                            task_box_3 = :task_box_3,
                                            task_box_4 = :task_box_4,
                                            task_box_5 = :task_box_5,
                                            task_box_6 = :task_box_6,
                                            task_box_7 = :task_box_7,
                                            task_status_1 = :task_status_1,
                                            task_status_2 = :task_status_2,
                                            task_status_3 = :task_status_3,
                                            task_status_4 = :task_status_4,
                                            task_status_5 = :task_status_5,
                                            task_status_6 = :task_status_6,
                                            task_status_7 = :task_status_7,
                                            updated_at = NOW()
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
                                        'task_box_7' => $row['task_box_7'] ?? '',
                                        'task_status_1' => $row['task_status_1'] ?? null,
                                        'task_status_2' => $row['task_status_2'] ?? null,
                                        'task_status_3' => $row['task_status_3'] ?? null,
                                        'task_status_4' => $row['task_status_4'] ?? null,
                                        'task_status_5' => $row['task_status_5'] ?? null,
                                        'task_status_6' => $row['task_status_6'] ?? null,
                                        'task_status_7' => $row['task_status_7'] ?? null
                                    ]);
                                }
                            }
                        }
                    }
                }
            }

            $this->db->commit();
            return $idMapping;
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

    /**
     * Update a single cell's status
     */
    public function updateCellStatus($rowId, $taskIndex, $status, $userId)
    {
        $statusField = "task_status_{$taskIndex}";
        
        $stmt = $this->db->prepare("
            UPDATE publishing_rows 
            SET {$statusField} = :status,
                updated_at = NOW()
            WHERE id = :row_id
        ");
        
        return $stmt->execute([
            'row_id' => $rowId,
            'status' => $status
        ]);
    }

    /**
     * Get cells changed since last sync timestamp
     */
    public function getChangedCells($tableId, $lastSync, $userId = null)
    {
        if ($userId) {
            $stmt = $this->db->prepare("
                SELECT 
                    r.id as row_id,
                    r.table_id,
                    r.task_box_1, r.task_status_1,
                    r.task_box_2, r.task_status_2,
                    r.task_box_3, r.task_status_3,
                    r.task_box_4, r.task_status_4,
                    r.task_box_5, r.task_status_5,
                    r.task_box_6, r.task_status_6,
                    r.task_box_7, r.task_status_7,
                    r.company_name,
                    r.updated_at
                FROM publishing_rows r
                INNER JOIN publishing_row_assignments a ON r.id = a.row_id
                WHERE r.table_id = :table_id 
                  AND r.updated_at > :last_sync
                  AND a.user_id = :user_id
                ORDER BY r.updated_at DESC
            ");
            
            $stmt->execute([
                'table_id' => $tableId,
                'last_sync' => $lastSync,
                'user_id' => $userId
            ]);
        } else {
            $stmt = $this->db->prepare("
                SELECT 
                    id as row_id,
                    table_id,
                    task_box_1, task_status_1,
                    task_box_2, task_status_2,
                    task_box_3, task_status_3,
                    task_box_4, task_status_4,
                    task_box_5, task_status_5,
                    task_box_6, task_status_6,
                    task_box_7, task_status_7,
                    company_name,
                    updated_at
                FROM publishing_rows
                WHERE table_id = :table_id 
                  AND updated_at > :last_sync
                ORDER BY updated_at DESC
            ");
            
            $stmt->execute([
                'table_id' => $tableId,
                'last_sync' => $lastSync
            ]);
        }
        
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!empty($rows)) {
            $rowIds = array_column($rows, 'row_id');
            $rowPlaceholders = implode(',', array_fill(0, count($rowIds), '?'));
            
            $stmtAssign = $this->db->prepare("
                SELECT a.row_id, a.user_id, u.full_name, u.username 
                FROM publishing_row_assignments a
                INNER JOIN users u ON a.user_id = u.id
                WHERE a.row_id IN ($rowPlaceholders)
            ");
            $stmtAssign->execute($rowIds);
            $assignData = $stmtAssign->fetchAll(PDO::FETCH_ASSOC);
            
            $assignmentsMap = [];
            foreach ($rowIds as $rid) {
                $assignmentsMap[$rid] = [];
            }
            foreach ($assignData as $a) {
                $assignmentsMap[$a['row_id']][] = [
                    'user_id' => $a['user_id'],
                    'full_name' => $a['full_name'],
                    'username' => $a['username']
                ];
            }

            foreach ($rows as &$r) {
                $r['assignments'] = $assignmentsMap[$r['row_id']] ?? [];
                // map row_id back to id for consistency with frontend
                $r['id'] = $r['row_id']; 
            }
        }
        
        return $rows;
    }

    /**
     * Update a row's assignments silently
     */
    public function updateRowAssignment($rowId, $userIds)
    {
        $this->db->beginTransaction();
        try {
            // Delete existing assignments for this row
            $stmtDel = $this->db->prepare("DELETE FROM publishing_row_assignments WHERE row_id = :row_id");
            $stmtDel->execute(['row_id' => $rowId]);
            
            // Insert new assignments
            if (!empty($userIds) && is_array($userIds)) {
                $stmtIns = $this->db->prepare("
                    INSERT INTO publishing_row_assignments (id, row_id, user_id) 
                    VALUES (:id, :row_id, :user_id)
                ");
                
                $addedUsers = [];
                foreach ($userIds as $uid) {
                    if (!empty($uid) && !in_array($uid, $addedUsers)) {
                        $addedUsers[] = $uid;
                        $stmtIns->execute([
                            'id' => $this->generateUuid(),
                            'row_id' => $rowId,
                            'user_id' => $uid
                        ]);
                    }
                }
            }
            
            // Touch the row's updated_at to ensure sync picks it up
            $stmtUpd = $this->db->prepare("UPDATE publishing_rows SET updated_at = NOW() WHERE id = :row_id");
            $stmtUpd->execute(['row_id' => $rowId]);
            
            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Get database connection for authorization checks
     */
    public function getDb()
    {
        return $this->db;
    }
}
