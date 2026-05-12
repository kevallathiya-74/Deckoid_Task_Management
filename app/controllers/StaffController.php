<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Middleware\AuthMiddleware;

class StaffController
{
    protected $userModel;
    protected $roleModel;

    public function __construct()
    {
        AuthMiddleware::adminOnly();
        $this->userModel = new User();
        $this->roleModel = new Role();
    }

    public function index()
    {
        $title = 'Staff Management';
        $active_page = 'staff';
        $roles = $this->roleModel->all();

        require_once ROOT_PATH . '/app/views/layouts/header.php';
        require_once ROOT_PATH . '/app/views/layouts/sidebar.php';
        require_once ROOT_PATH . '/app/views/staff/index.php';
        require_once ROOT_PATH . '/app/views/layouts/footer.php';
    }

    public function list()
    {
        header('Content-Type: application/json');
        
        $filters = [
            'role_id' => $_GET['role_id'] ?? null,
            'status' => $_GET['status'] ?? null
        ];

        $staff = $this->userModel->listAll($filters);
        
        echo json_encode([
            'success' => true,
            'data' => $staff
        ]);
    }

    public function create()
    {
        header('Content-Type: application/json');

        $role = $this->roleModel->findById($_POST['role_id'] ?? '');
        $data = [
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '',
            'role_id' => $_POST['role_id'] ?? '',
            'role' => $role ? $role['name'] : null,
            'status' => $_POST['status'] ?? 'active'
        ];

        // Basic Validation
        if (empty($data['full_name']) || empty($data['username']) || empty($data['email']) || empty($data['password']) || empty($data['role_id'])) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
            return;
        }

        // Check if username or email exists
        if ($this->userModel->findByUsername($data['username'])) {
            echo json_encode(['success' => false, 'message' => 'Username already exists']);
            return;
        }

        $id = $this->userModel->create($data);
        if ($id) {
            echo json_encode(['success' => true, 'message' => 'Staff member created successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to create staff member']);
        }
    }

    public function update()
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Staff ID is missing']);
            return;
        }

        $role = $this->roleModel->findById($_POST['role_id'] ?? '');
        $data = [
            'full_name' => $_POST['full_name'] ?? '',
            'username' => $_POST['username'] ?? '',
            'email' => $_POST['email'] ?? '',
            'password' => $_POST['password'] ?? '', // Optional
            'role_id' => $_POST['role_id'] ?? '',
            'role' => $role ? $role['name'] : null,
            'status' => $_POST['status'] ?? 'active'
        ];

        if (empty($data['full_name']) || empty($data['username']) || empty($data['email']) || empty($data['role_id'])) {
            echo json_encode(['success' => false, 'message' => 'All required fields must be filled']);
            return;
        }

        if ($this->userModel->update($id, $data)) {
            echo json_encode(['success' => true, 'message' => 'Staff member updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update staff member']);
        }
    }

    public function delete()
    {
        header('Content-Type: application/json');

        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'Staff ID is missing']);
            return;
        }

        if ($this->userModel->softDelete($id)) {
            echo json_encode(['success' => true, 'message' => 'Staff member deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete staff member']);
        }
    }
}
