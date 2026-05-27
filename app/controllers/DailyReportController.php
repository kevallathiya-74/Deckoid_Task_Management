<?php

namespace App\Controllers;

use App\Models\DailyReportModel;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class DailyReportController
{
    protected $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new DailyReportModel();
    }

    protected function isAdmin()
    {
        return strtolower($_SESSION['user_role'] ?? '') === 'admin';
    }

    protected function jsonResponse($status, $payload = [])
    {
        header('Content-Type: application/json');
        echo json_encode(array_merge(['status' => $status], $payload));
        exit;
    }

    public function index()
    {
        $title = 'Daily Report';
        $active_page = 'daily_report';

        $userModel = new User();
        $users = $userModel->listAll(['status' => 'active']);

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';

        if ($this->isAdmin()) {
            require_once ROOT_PATH . '/app/views/daily_report/admin_index.php';
        } else {
            require_once ROOT_PATH . '/app/views/daily_report/index.php';
        }

        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function fetchTodayReport($params = [])
    {
        $userId = $_SESSION['user_id'];
        $date = $_GET['date'] ?? date('Y-m-d');
        $data = $this->model->fetchReportByUserDate($userId, $date);
        $this->jsonResponse('success', ['data' => $data]);
    }

    public function fetchReport($params = [])
    {
        try {
            $userId = $_SESSION['user_id'];
            $targetUser = $userId;

            if ($this->isAdmin() && !empty($_GET['user_id'])) {
                $targetUser = $_GET['user_id'];
            }

            $date = $_GET['date'] ?? date('Y-m-d');
            $data = $this->model->fetchReportByUserDate($targetUser, $date);
            $this->jsonResponse('success', ['data' => $data]);
        } catch (\Exception $e) {
            $this->jsonResponse('error', ['message' => 'Unable to load report.']);
        }
    }

    public function saveReport($params = [])
    {
        try {
            $userId = $_SESSION['user_id'];
            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->jsonResponse('error', ['message' => 'Invalid request payload.']);
            }

            $reportUserId = $userId;
            if ($this->isAdmin() && !empty($input['user_id'])) {
                $reportUserId = $input['user_id'];
            }

            // Accept both 'date' and 'report_date' keys for compatibility
            $date = $input['date'] ?? $input['report_date'] ?? date('Y-m-d');
            if (!$this->isValidDate($date)) {
                $this->jsonResponse('error', ['message' => 'Invalid report date.']);
            }

            // Accept both 'rows' and 'items' keys
            $rows = $input['rows'] ?? $input['items'] ?? [];
            if (!is_array($rows)) {
                $this->jsonResponse('error', ['message' => 'Report rows must be an array.']);
            }

            $this->model->saveReport($reportUserId, $date, $rows);
            $this->jsonResponse('success', ['message' => 'Daily report saved successfully.']);
        } catch (\Exception $e) {
            // Log the detailed error for debugging (server-side)
            error_log('[daily_report][save] ' . $e->getMessage());
            $msg = $e->getMessage();
            $generic = 'Unable to save report.';
            // Expose validation messages to client
            if (strpos($msg, 'Please add at least') !== false || strpos($msg, 'numeric') !== false) {
                $this->jsonResponse('error', ['message' => $msg]);
            }

            // If app.debug is enabled, include the exception message to assist debugging
            if (config('app.debug', false)) {
                $this->jsonResponse('error', ['message' => $msg]);
            }

            $this->jsonResponse('error', ['message' => $generic]);
        }
    }

    public function updateReport($params = [])
    {
        try {
            $reportId = $params['id'] ?? null;
            if (empty($reportId)) {
                $this->jsonResponse('error', ['message' => 'Report ID is required.']);
            }

            $input = json_decode(file_get_contents('php://input'), true);
            if (!is_array($input)) {
                $this->jsonResponse('error', ['message' => 'Invalid request payload.']);
            }

            $rows = $input['rows'] ?? [];
            if (!is_array($rows)) {
                $this->jsonResponse('error', ['message' => 'Report rows must be an array.']);
            }

            $report = $this->model->fetchReportById($reportId);
            if (!$report) {
                $this->jsonResponse('error', ['message' => 'Report not found.']);
            }

            if (!$this->isAdmin() && $report['user_id'] !== $_SESSION['user_id']) {
                $this->jsonResponse('error', ['message' => 'Unauthorized to update report.']);
            }

            $this->model->updateReportById($reportId, $rows);
            $this->jsonResponse('success', ['message' => 'Daily report updated successfully.']);
        } catch (\Exception $e) {
            $this->jsonResponse('error', ['message' => 'Unable to update report.']);
        }
    }

    public function fetchReportsByUser($params = [])
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse('error', ['message' => 'Unauthorized']);
        }

        $userId = $params['id'] ?? null;
        if (empty($userId)) {
            $this->jsonResponse('error', ['message' => 'User ID is required.']);
        }

        $reports = $this->model->fetchReportsByUser($userId);
        $this->jsonResponse('success', ['data' => $reports]);
    }

    public function fetchReportByUserDate($params = [])
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse('error', ['message' => 'Unauthorized']);
        }

        $userId = $params['id'] ?? null;
        $date = $params['date'] ?? null;

        if (empty($userId) || empty($date) || !$this->isValidDate($date)) {
            $this->jsonResponse('error', ['message' => 'User ID and valid date are required.']);
        }

        $data = $this->model->fetchReportByUserDate($userId, $date);
        $this->jsonResponse('success', ['data' => $data]);
    }

    public function fetchAdminReports($params = [])
    {
        if (!$this->isAdmin()) {
            $this->jsonResponse('error', ['message' => 'Unauthorized']);
        }

        $userId = $_GET['user_id'] ?? null;
        $date = $_GET['date'] ?? null;
        if ($date !== null && !$this->isValidDate($date)) {
            $this->jsonResponse('error', ['message' => 'Invalid date filter.']);
        }

        $reports = $this->model->fetchReportsForAdmin($userId, $date);
        $this->jsonResponse('success', ['data' => $reports]);
    }

    protected function isValidDate($date)
    {
        $dt = \DateTime::createFromFormat('Y-m-d', $date);
        return $dt && $dt->format('Y-m-d') === $date;
    }
}
