<?php

namespace App\Models;

use App\Core\Database;
use PDO;

class DailyReportModel
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

    public function fetchReportByUserDate($userId, $date)
    {
        $stmt = $this->db->prepare("SELECT * FROM daily_reports WHERE user_id = :user_id AND report_date = :report_date LIMIT 1");
        $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
        $report = $stmt->fetch(PDO::FETCH_ASSOC);

        $rows = [];
        if ($report) {
            $stmtRows = $this->db->prepare("SELECT * FROM daily_report_rows WHERE report_id = :report_id ORDER BY row_order ASC");
            $stmtRows->execute(['report_id' => $report['id']]);
            $rows = $stmtRows->fetchAll(PDO::FETCH_ASSOC);
        }

        return ['report' => $report, 'rows' => $rows];
    }

    public function fetchReportsByUser($userId)
    {
        $stmt = $this->db->prepare("SELECT * FROM daily_reports WHERE user_id = :user_id ORDER BY report_date DESC, updated_at DESC");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function fetchReportById($reportId)
    {
        $stmt = $this->db->prepare("SELECT * FROM daily_reports WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $reportId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveReport($userId, $date, $rows)
    {
        $cleanRows = $this->cleanRows($rows);
        if (empty($cleanRows)) {
            throw new \Exception('Please add at least one task before saving.');
        }

        $totalTasks = 0;
        $totalValue = 0;

        foreach ($cleanRows as $row) {
            $totalTasks++;
            if ($row['number_value'] !== null) {
                $totalValue += $row['number_value'];
            }
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare("SELECT id FROM daily_reports WHERE user_id = :user_id AND report_date = :report_date LIMIT 1");
            $stmt->execute(['user_id' => $userId, 'report_date' => $date]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $reportId = $existing['id'];
                $stmtUpd = $this->db->prepare("UPDATE daily_reports SET total_tasks = :total_tasks, total_value = :total_value, updated_at = NOW() WHERE id = :id");
                $stmtUpd->execute([
                    'total_tasks' => $totalTasks,
                    'total_value' => $totalValue,
                    'id' => $reportId
                ]);
                $stmtDel = $this->db->prepare("DELETE FROM daily_report_rows WHERE report_id = :report_id");
                $stmtDel->execute(['report_id' => $reportId]);
            } else {
                $reportId = $this->generateUuid();
                $stmtIns = $this->db->prepare("INSERT INTO daily_reports (id, user_id, report_date, total_tasks, total_value, created_at, updated_at) VALUES (:id, :user_id, :report_date, :total_tasks, :total_value, NOW(), NOW())");
                $stmtIns->execute([
                    'id' => $reportId,
                    'user_id' => $userId,
                    'report_date' => $date,
                    'total_tasks' => $totalTasks,
                    'total_value' => $totalValue
                ]);
            }

            $stmtRow = $this->db->prepare("INSERT INTO daily_report_rows (id, report_id, task_text, number_value, row_order, created_at) VALUES (:id, :report_id, :task_text, :number_value, :row_order, NOW())");
            foreach ($cleanRows as $row) {
                $stmtRow->execute([
                    'id' => $this->generateUuid(),
                    'report_id' => $reportId,
                    'task_text' => $row['task_text'],
                    'number_value' => $row['number_value'],
                    'row_order' => $row['row_order']
                ]);
            }

            $this->db->commit();
            return $reportId;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    public function updateReportById($reportId, $rows)
    {
        $report = $this->fetchReportById($reportId);
        if (!$report) {
            throw new \Exception('Report not found.');
        }

        $cleanRows = $this->cleanRows($rows);
        if (empty($cleanRows)) {
            throw new \Exception('Please add at least one task before updating.');
        }

        $totalTasks = count($cleanRows);
        $totalValue = 0;
        foreach ($cleanRows as $row) {
            if ($row['number_value'] !== null) {
                $totalValue += $row['number_value'];
            }
        }

        $this->db->beginTransaction();
        try {
            $stmtUpd = $this->db->prepare("UPDATE daily_reports SET total_tasks = :total_tasks, total_value = :total_value, updated_at = NOW() WHERE id = :id");
            $stmtUpd->execute([
                'total_tasks' => $totalTasks,
                'total_value' => $totalValue,
                'id' => $reportId
            ]);

            $stmtDel = $this->db->prepare("DELETE FROM daily_report_rows WHERE report_id = :report_id");
            $stmtDel->execute(['report_id' => $reportId]);

            $stmtRow = $this->db->prepare("INSERT INTO daily_report_rows (id, report_id, task_text, number_value, row_order, created_at) VALUES (:id, :report_id, :task_text, :number_value, :row_order, NOW())");
            foreach ($cleanRows as $row) {
                $stmtRow->execute([
                    'id' => $this->generateUuid(),
                    'report_id' => $reportId,
                    'task_text' => $row['task_text'],
                    'number_value' => $row['number_value'],
                    'row_order' => $row['row_order']
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    protected function cleanRows($rows)
    {
        $cleanRows = [];

        foreach ($rows as $idx => $row) {
            $taskText = trim($row['task_text'] ?? '');
            if ($taskText === '') {
                continue;
            }

            $taskText = mb_substr($taskText, 0, 1000);
            $numberValue = null;

            if (isset($row['number_value']) && $row['number_value'] !== '') {
                if (!is_numeric($row['number_value'])) {
                    throw new \Exception('All number values must be numeric.');
                }
                $numberValue = (float) $row['number_value'];
            }

            $cleanRows[] = [
                'task_text' => $taskText,
                'number_value' => $numberValue,
                'row_order' => isset($row['row_order']) ? (int) $row['row_order'] : $idx
            ];
        }

        return $cleanRows;
    }

    public function fetchReportsForAdmin($userId = null, $date = null)
    {
        $params = [];
        $sql = "SELECT r.*, u.full_name FROM daily_reports r JOIN users u ON r.user_id = u.id";
        $conditions = [];

        if (!empty($userId)) {
            $conditions[] = "r.user_id = :user_id";
            $params['user_id'] = $userId;
        }

        if (!empty($date)) {
            $conditions[] = "r.report_date = :report_date";
            $params['report_date'] = $date;
        }

        if (!empty($conditions)) {
            $sql .= ' WHERE ' . implode(' AND ', $conditions);
        }

        $sql .= ' ORDER BY r.report_date DESC, r.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
