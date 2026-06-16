<?php

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Check if user is authenticated
     */
    public static function handle()
    {
        if (!isset($_SESSION['user_id'])) {
            self::redirect();
        }

        // Verify CSRF token for POST requests
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
            if (empty($token) || !isset($_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $token)) {
                if (self::isApiRequest()) {
                    header('Content-Type: application/json');
                    http_response_code(403);
                    echo json_encode(['success' => false, 'error' => 'CSRF token mismatch. Please refresh the page.']);
                    exit;
                }
                die('CSRF token mismatch. Please go back and refresh the page.');
            }
        }

        // Verify session in database if token exists
        if (isset($_SESSION['session_token'])) {
            $sessionModel = new \App\Models\Session();
            if (!$sessionModel->isValid($_SESSION['session_token'])) {
                self::logout();
            }
        }
    }

    /**
     * Check if user is Admin
     */
    public static function adminOnly()
    {
        self::handle();
        
        if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
            if (self::isApiRequest()) {
                header('Content-Type: application/json');
                http_response_code(403);
                echo json_encode(['success' => false, 'error' => 'Forbidden: Admin access only']);
                exit;
            }
            header('Location: ' . url('/dashboard?error=forbidden'));
            exit;
        }
    }

    private static function redirect()
    {
        if (self::isApiRequest()) {
            header('Content-Type: application/json');
            http_response_code(401);
            echo json_encode(['success' => false, 'error' => 'Unauthorized access. Session expired.']);
            exit;
        }
        header('Location: ' . url('/login'));
        exit;
    }

    private static function logout()
    {
        session_unset();
        session_destroy();
        self::redirect();
    }

    private static function isApiRequest()
    {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') !== false;
    }
}
