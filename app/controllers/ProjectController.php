<?php

namespace App\Controllers;

use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class ProjectController
{
    protected $projectModel;
    protected $roleModel;
    protected $userModel;

    public function __construct()
    {
        AuthMiddleware::handle();
        $this->projectModel = new Project();
        $this->roleModel = new Role();
        $this->userModel = new User();
    }

    public function index()
    {
        try {
            $title = 'Project Management';
            $active_page = 'projects';
            $roles = $this->roleModel->all();
            $staff = $this->userModel->listAll();

            require_once ROOT_PATH . '/app/views/layouts/header.php';
            require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
            require_once ROOT_PATH . '/app/views/projects/index.php';
            require_once ROOT_PATH . '/app/views/layouts/footer.php';
        } catch (\Exception $e) {
            http_response_code(500);
            die("Error loading projects page: " . $e->getMessage());
        }
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        try {
            $filters = [
                'role_id' => $_GET['role_id'] ?? null,
                'status' => $_GET['status'] ?? null
            ];
            
            $start = isset($_GET['start']) ? (int)$_GET['start'] : 0;
            $length = isset($_GET['length']) ? (int)$_GET['length'] : null;
            $search = $_GET['search']['value'] ?? '';

            $projects = $this->projectModel->listAll($filters, $search, $length, $start);
            $recordsFiltered = $this->projectModel->countAll($filters, $search);
            $recordsTotal = $this->projectModel->countAll($filters, '');
            
            echo json_encode([
                'status' => 'success',
                'draw' => isset($_GET['draw']) ? (int)$_GET['draw'] : 1,
                'recordsTotal' => $recordsTotal,
                'recordsFiltered' => $recordsFiltered,
                'data' => $projects
            ]);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
        }
    }

    public function create()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $data = [
                'project_name' => trim($_POST['project_name'] ?? ''),
                'client_name' => trim($_POST['client_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'role_ids' => $_POST['role_ids'] ?? [],
                'assigned_users' => $_POST['assigned_users'] ?? []
            ];

            // Robust Validation
            if (empty($data['project_name']) || empty($data['role_ids'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'Project name and department are required']);
                return;
            }


            foreach ($data['role_ids'] as $roleId) {
                if (!$this->roleModel->findById($roleId)) {
                    echo json_encode(['status' => 'validation_error', 'message' => 'Invalid department selected']);
                    return;
                }
            }

            // Check duplicate name
            if ($this->projectModel->findByName($data['project_name'])) {
                echo json_encode(['status' => 'validation_error', 'message' => 'A project with this name already exists']);
                return;
            }

            $id = $this->projectModel->create($data);
            if ($id) {
                echo json_encode(['status' => 'success', 'message' => 'Project created successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to create project']);
            }
        } catch (\Exception $e) {
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }

    public function update()
    {
        AuthMiddleware::adminOnly();
        header('Content-Type: application/json');

        try {
            $id = $_POST['id'] ?? '';
            if (empty($id)) {
                echo json_encode(['status' => 'error', 'message' => 'Project ID is missing']);
                return;
            }

            $data = [
                'project_name' => trim($_POST['project_name'] ?? ''),
                'client_name' => trim($_POST['client_name'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'role_ids' => $_POST['role_ids'] ?? [],
                'assigned_users' => $_POST['assigned_users'] ?? []
            ];

            if (empty($data['project_name']) || empty($data['role_ids'])) {
                echo json_encode(['status' => 'error', 'message' => 'Project name and department are required']);
                return;
            }


            foreach ($data['role_ids'] as $roleId) {
                if (!$this->roleModel->findById($roleId)) {
                    echo json_encode(['status' => 'error', 'message' => 'Invalid department selected']);
                    return;
                }
            }

            if ($this->projectModel->update($id, $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Project updated successfully']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to update project']);
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
            echo json_encode(['status' => 'error', 'message' => 'Project ID is missing']);
            return;
        }

        if ($this->projectModel->softDelete($id)) {
            echo json_encode(['status' => 'success', 'message' => 'Project deleted successfully']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete project']);
        }
    }
}
