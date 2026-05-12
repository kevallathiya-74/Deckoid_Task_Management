<?php

/**
 * Admin Seeder
 * Run this script to create the initial admin account.
 */

if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(dirname(__DIR__)));
}

require_once ROOT_PATH . '/app/helpers/env_helper.php';
loadEnv(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/app/core/Autoloader.php';

use App\Core\Database;

$db = Database::getInstance()->getConnection();

$adminId = 'e3b0c442-98fc-1c14-9afb-f4c8996fb924'; // Static UUID for admin
$roleId = '6f9e836b-67a4-4770-96f1-67e39a5f4581'; // Admin role ID from schema.sql
$fullName = 'Super Admin';
$username = 'admin';
$email = 'admin@gmail.com';
$password = 'admin123';
$passwordHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    $stmt = $db->prepare("
        INSERT INTO users (id, role_id, role, full_name, username, email, password_hash, status) 
        VALUES (:id, :role_id, :role, :full_name, :username, :email, :password_hash, 'active')
        ON DUPLICATE KEY UPDATE password_hash = VALUES(password_hash), role = VALUES(role)
    ");
    
    $stmt->execute([
        'id' => $adminId,
        'role_id' => $roleId,
        'role' => 'Admin',
        'full_name' => $fullName,
        'username' => $username,
        'email' => $email,
        'password_hash' => $passwordHash
    ]);

    echo "Admin user created successfully!\n";
    echo "Username: $username\n";
    echo "Password: $password\n";

} catch (\Exception $e) {
    echo "Error creating admin user: " . $e->getMessage() . "\n";
}
