<?php

namespace App\Core;

class ErrorHandler
{
    private static $errors = [];
    private static $logPath = ROOT_PATH . '/storage/logs';

    public static function initialize()
    {
        // Ensure logs directory exists
        if (!is_dir(self::$logPath)) {
            mkdir(self::$logPath, 0755, true);
        }

        // Set custom error handler
        set_error_handler([self::class, 'handleError']);
        
        // Set custom exception handler
        set_exception_handler([self::class, 'handleException']);
        
        // Set shutdown handler for fatal errors
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    public static function handleError($errno, $errstr, $errfile, $errline)
    {
        $errorType = [
            E_ERROR => 'Fatal Error',
            E_WARNING => 'Warning',
            E_PARSE => 'Parse Error',
            E_NOTICE => 'Notice',
            E_CORE_ERROR => 'Core Error',
            E_CORE_WARNING => 'Core Warning',
            E_COMPILE_ERROR => 'Compile Error',
            E_COMPILE_WARNING => 'Compile Warning',
            E_USER_ERROR => 'User Error',
            E_USER_WARNING => 'User Warning',
            E_USER_NOTICE => 'User Notice',
            E_STRICT => 'Strict',
            E_DEPRECATED => 'Deprecated',
            E_USER_DEPRECATED => 'User Deprecated'
        ];

        $type = isset($errorType[$errno]) ? $errorType[$errno] : 'Unknown';
        
        $error = [
            'type' => $type,
            'message' => $errstr,
            'file' => $errfile,
            'line' => $errline,
            'timestamp' => date('Y-m-d H:i:s'),
            'severity' => $errno
        ];

        self::log($error);
        
        // Don't suppress errors but log them
        return false;
    }

    public static function handleException(\Throwable $exception)
    {
        $error = [
            'type' => 'Exception',
            'class' => get_class($exception),
            'message' => $exception->getMessage(),
            'file' => $exception->getFile(),
            'line' => $exception->getLine(),
            'trace' => $exception->getTrace(),
            'timestamp' => date('Y-m-d H:i:s')
        ];

        self::log($error);

        // Return appropriate response
        if (php_sapi_name() === 'cli') {
            echo "Exception: " . $exception->getMessage() . "\n";
            echo "File: " . $exception->getFile() . ":" . $exception->getLine() . "\n";
        } else {
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode([
                'status' => 'error',
                'message' => config('app.debug', false) ? $exception->getMessage() : 'An error occurred',
                'code' => $exception->getCode()
            ]);
        }
        exit;
    }

    public static function handleShutdown()
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
            self::handleError($error['type'], $error['message'], $error['file'], $error['line']);
        }
    }

    public static function log($error)
    {
        $logFile = self::$logPath . '/error-' . date('Y-m-d') . '.log';
        $logMessage = json_encode($error) . "\n";
        
        file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }

    public static function apiError($message, $statusCode = 400, $data = [])
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        echo json_encode(array_merge([
            'status' => 'error',
            'message' => $message
        ], $data));
        
        exit;
    }

    public static function apiSuccess($message, $data = [], $statusCode = 200)
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        
        echo json_encode([
            'status' => 'success',
            'message' => $message,
            'data' => $data
        ]);
        
        exit;
    }
}
