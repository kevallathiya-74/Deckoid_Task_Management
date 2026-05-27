<?php

namespace App\Controllers;

use App\Models\PublishingModel;
use App\Middleware\AuthMiddleware;

class PublishingController
{
    protected $model;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->model = new PublishingModel();
    }

    public function index()
    {
        $title = 'Publishing Report';
        $active_page = 'publishing';
        
        $extra_css = '<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />';
        $extra_js = '<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>';
        
        $userModel = new \App\Models\User();
        $users = $userModel->listAll(['status' => 'active']);
        
        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/publishing/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function fetchReport()
    {
        header('Content-Type: application/json');
        try {
            $userId = $_SESSION['user_id'];
            $isAdmin = (strtolower($_SESSION['user_role']) === 'admin');
            
            $month = !empty($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
            $year = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
            
            $data = $this->model->fetchReportData($userId, $isAdmin, $month, $year);
            
            echo json_encode([
                'status' => 'success',
                'data' => $data
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createTable()
    {
        header('Content-Type: application/json');
        try {
            $isAdmin = (strtolower($_SESSION['user_role']) === 'admin');
            if (!$isAdmin) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Only Admin can create tables.']);
                return;
            }
            
            $category = $_POST['category'] ?? '';
            $weekNumber = isset($_POST['week_number']) ? (int)$_POST['week_number'] : 1;
            $month = isset($_POST['month']) ? (int)$_POST['month'] : (int)date('n');
            $year = isset($_POST['year']) ? (int)$_POST['year'] : (int)date('Y');
            
            if (empty($category) || empty($weekNumber)) {
                echo json_encode(['status' => 'error', 'message' => 'Category and Week Number are required']);
                return;
            }
            
            $userId = $_SESSION['user_id'];
            $tableId = $this->model->createTable($category, $weekNumber, $month, $year, $userId, $isAdmin);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Publishing table created successfully',
                'table_id' => $tableId
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function saveReport()
    {
        header('Content-Type: application/json');
        try {
            $userId = $_SESSION['user_id'];
            $isAdmin = (strtolower($_SESSION['user_role']) === 'admin');
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
                return;
            }
            
            $this->model->saveReportData($input, $userId, $isAdmin);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Publishing table saved successfully.'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteTable()
    {
        header('Content-Type: application/json');
        try {
            $isAdmin = (strtolower($_SESSION['user_role']) === 'admin');
            if (!$isAdmin) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Only Admin can delete tables.']);
                return;
            }
            
            $tableId = $_POST['id'] ?? '';
            
            if (empty($tableId)) {
                echo json_encode(['status' => 'error', 'message' => 'Table ID is missing']);
                return;
            }
            
            $this->model->deleteTable($tableId, $isAdmin);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Publishing table deleted successfully.'
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
