<?php
namespace App\Controllers;

use App\Models\Task;
use App\Models\User;
use App\Middleware\AuthMiddleware;

class OverdueTaskController
{
    private $taskModel;
    private $userModel;

    public function __construct()
    {
        $this->taskModel = new Task();
        $this->userModel = new User();
    }

    public function index()
    {
        AuthMiddleware::adminOnly();

        $summary = $this->taskModel->getOverdueSummary();
        $staff = $this->userModel->getAllStaff();

        $title = 'Overdue Tasks Management';
        $active_page = 'overdue';
        $prefix = isAdminOrSubAdmin() ? 'admin' : 'staff';

        require_once ROOT_PATH . '/app/views/overdue/admin.php';
    }

    public function staffIndex()
    {
        AuthMiddleware::handle();

        $userId = $_SESSION['user_id'];
        $summary = $this->taskModel->getOverdueSummary($userId);

        $title = 'My Overdue Tasks';
        $active_page = 'overdue';
        $prefix = 'staff';

        require_once ROOT_PATH . '/app/views/overdue/staff.php';
    }
}
