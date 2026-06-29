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
            $isAdmin = isAdminOrSubAdmin();
            
            $month = !empty($_GET['month']) ? (int)$_GET['month'] : (int)date('n');
            $year = !empty($_GET['year']) ? (int)$_GET['year'] : (int)date('Y');
            
            $data = $this->model->fetchReportData($userId, $isAdmin, $month, $year);
            // Include a sync timestamp so clients can initialize polling correctly
            echo json_encode([
                'status' => 'success',
                'data' => $data,
                'sync_timestamp' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function createTable()
    {
        header('Content-Type: application/json');
        try {
            $isAdmin = isAdminOrSubAdmin();
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
            $isAdmin = isAdminOrSubAdmin();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
                return;
            }
            
            $idMapping = $this->model->saveReportData($input, $userId, $isAdmin);
            
            echo json_encode([
                'status' => 'success',
                'message' => 'Publishing table saved successfully.',
                'id_mapping' => $idMapping
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function deleteTable()
    {
        header('Content-Type: application/json');
        try {
            $isAdmin = isAdminOrSubAdmin();
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

    /**
     * Real-time sync: Update a single cell's status
     * Endpoint: POST /api/publishing/cell-update
     */
    public function updateCellStatus()
    {
        header('Content-Type: application/json');
        try {
            $userId = $_SESSION['user_id'];
            $isAdmin = isAdminOrSubAdmin();
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
                return;
            }
            
            $rowId = $input['row_id'] ?? '';
            $taskIndex = isset($input['task_index']) ? (int)$input['task_index'] : null;
            $status = $input['status'] ?? null; // null, 'production', 'approval', 'publishing'
            $tableId = $input['table_id'] ?? '';
            
            if (empty($rowId) || $taskIndex === null) {
                echo json_encode(['status' => 'error', 'message' => 'Missing row_id or task_index']);
                return;
            }
            
            // Verify authorization
            if (!$isAdmin) {
                $stmt = $this->model->getDb()->prepare("
                    SELECT 1 FROM publishing_row_assignments 
                    WHERE row_id = :row_id AND user_id = :user_id
                ");
                $stmt->execute(['row_id' => $rowId, 'user_id' => $userId]);
                if (!$stmt->fetch()) {
                    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                    return;
                }
            }
            
            $this->model->updateCellStatus($rowId, $taskIndex, $status, $userId);

            // Backend debug log (to PHP error log)
            error_log('Publishing color updated: ' . json_encode([
                'row_id' => $rowId,
                'task_index' => $taskIndex,
                'status' => $status,
                'user_id' => $userId,
                'table_id' => $tableId,
                'timestamp' => date('Y-m-d H:i:s')
            ]));

            echo json_encode([
                'status' => 'success',
                'message' => 'Cell status updated',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Real-time sync: Update a row's assignments
     * Endpoint: POST /api/publishing/update-assignment
     */
    public function updateAssignment()
    {
        header('Content-Type: application/json');
        try {
            $isAdmin = isAdminOrSubAdmin();
            
            if (!$isAdmin) {
                echo json_encode(['status' => 'error', 'message' => 'Unauthorized: Only Admin can update assignments.']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input']);
                return;
            }
            
            $rowId = $input['row_id'] ?? '';
            $assignedUserIds = $input['assigned_user_ids'] ?? [];
            
            if (empty($rowId)) {
                echo json_encode(['status' => 'error', 'message' => 'Missing row_id']);
                return;
            }
            
            $this->model->updateRowAssignment($rowId, $assignedUserIds);

            echo json_encode([
                'status' => 'success',
                'message' => 'Row assignments updated',
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    /**
     * Real-time sync: Fetch only changed cells since last sync
     * Endpoint: GET /api/publishing/sync-changes?last_sync=timestamp&table_id=id
     */
    public function syncChanges()
    {
        header('Content-Type: application/json');
        try {
            $userId = $_SESSION['user_id'];
            $isAdmin = isAdminOrSubAdmin();
            
            $lastSync = $_GET['last_sync'] ?? null;
            $tableId = $_GET['table_id'] ?? null;
            
            if (!$lastSync || !$tableId) {
                echo json_encode(['status' => 'error', 'message' => 'Missing last_sync or table_id']);
                return;
            }
            
            // Verify authorization
            if (!$isAdmin) {
                // If staff, verify they have at least one row assigned in this table
                $stmt = $this->model->getDb()->prepare("
                    SELECT 1 FROM publishing_row_assignments a
                    INNER JOIN publishing_rows r ON a.row_id = r.id
                    WHERE r.table_id = :table_id AND a.user_id = :user_id
                ");
                $stmt->execute(['table_id' => $tableId, 'user_id' => $userId]);
                if (!$stmt->fetch()) {
                    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
                    return;
                }
            }
            
            $changes = $this->model->getChangedCells($tableId, $lastSync, $isAdmin ? null : $userId);

            
            // Log sync response for debugging
            error_log('Publishing sync response for table ' . $tableId . ': ' . json_encode(['count' => count($changes), 'since' => $lastSync]));

            echo json_encode([
                'status' => 'success',
                'data' => [
                    'changes' => $changes,
                    'sync_timestamp' => date('Y-m-d H:i:s')
                ]
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
