<?php

namespace App\Controllers;

use App\Models\SopModel;
use App\Models\User;
use App\Models\NotificationModel;
use App\Middleware\AuthMiddleware;

class SopController
{
    protected $sopModel;
    protected $userModel;
    protected $notificationModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->sopModel = new SopModel();
        $this->userModel = new User();
        $this->notificationModel = new NotificationModel();
    }

    public function adminIndex()
    {
        AuthMiddleware::adminOnly();
        
        try {
            $title = 'SOP Management';
            $active_page = 'sop';
            
            $staff = $this->userModel->listAll();

            require_once ROOT_PATH . '/app/views/layouts/header.php';
            require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
            require_once ROOT_PATH . '/app/views/sop/admin.php';
            require_once ROOT_PATH . '/app/views/layouts/footer.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            die("Error loading admin SOP page: " . $e->getMessage());
        }
    }

    public function staffIndex()
    {
        try {
            $title = 'My SOPs';
            $active_page = 'sop';
            
            require_once ROOT_PATH . '/app/views/layouts/header.php';
            require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
            require_once ROOT_PATH . '/app/views/sop/staff.php';
            require_once ROOT_PATH . '/app/views/layouts/footer.php';
        } catch (\Throwable $e) {
            http_response_code(500);
            die("Error loading staff SOP page: " . $e->getMessage());
        }
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        try {
            $isAdmin = ($_SESSION['user_role'] === 'admin');
            $filters = [
                'staff_id' => $isAdmin ? ($_GET['staff_id'] ?? null) : $_SESSION['user_id'],
                'status' => $_GET['status'] ?? null
            ];

            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : null;
            $search = $_GET['search']['value'] ?? '';

            $sops = $this->sopModel->listAll($filters, $search, $length, $start);
            $recordsFiltered = $this->sopModel->countAll($filters, $search);
            $recordsTotal = $this->sopModel->countAll($filters, '');
            
            echo json_encode([
                'status' => 'success',
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $sops
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => 'Database Error: ' . $e->getMessage()]);
        }
    }

    public function save()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            $staffId = $_POST['staff_id'] ?? '';
            $description = trim($_POST['description'] ?? '');
            
            if (empty($staffId) || empty($description)) {
                echo json_encode(['status' => 'error', 'message' => 'Staff and Description are required']);
                return;
            }

            if (!$this->userModel->findById($staffId)) {
                echo json_encode(['status' => 'error', 'message' => 'Invalid staff selected']);
                return;
            }

            $data = [
                'staff_id' => $staffId,
                'description' => $description,
                'status' => 'active'
            ];

            if (!empty($id)) {
                $sop = $this->sopModel->getById($id);
                if (!$sop) {
                    echo json_encode(['status' => 'error', 'message' => 'SOP not found']);
                    return;
                }
                
                if ($this->sopModel->update($id, $data)) {
                    // Maybe notify on update too?
                    $this->notificationModel->create([
                        'user_id' => $staffId,
                        'title' => 'SOP Updated',
                        'message' => 'An SOP assigned to you has been updated.',
                        'type' => 'sop_updated',
                        'link' => url('/staff/sop')
                    ]);
                    
                    echo json_encode(['status' => 'success', 'message' => 'SOP updated successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to update SOP']);
                }
            } else {
                $data['created_by'] = $_SESSION['user_id'];
                $newId = $this->sopModel->create($data);
                if ($newId) {
                    $this->notificationModel->create([
                        'user_id' => $staffId,
                        'title' => 'New SOP Assigned',
                        'message' => 'A new SOP has been assigned to you.',
                        'type' => 'sop_assigned',
                        'link' => url('/staff/sop')
                    ]);
                    
                    echo json_encode(['status' => 'success', 'message' => 'SOP created successfully']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'Failed to create SOP']);
                }
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function delete()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['status' => 'error', 'message' => 'SOP ID is missing']);
            return;
        }

        if ($this->sopModel->delete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'SOP deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete SOP']);
        }
    }
}
