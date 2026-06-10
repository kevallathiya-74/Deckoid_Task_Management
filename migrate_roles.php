<?php
require_once __DIR__ . '/app/core/Env.php';
require_once __DIR__ . '/app/helpers/env_helper.php';
loadEnv(__DIR__ . '/.env');

$host = env('DB_HOST', '127.0.0.1');
$port = env('DB_PORT', '3306');
$db   = env('DB_DATABASE', 'deckoid_task_management');
$user = env('DB_USERNAME', 'root');
$pass = env('DB_PASSWORD', '');
$charset = env('DB_CHARSET', 'utf8mb4');

$dsn = "mysql:host=$host;port=$port;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    
    // The required roles
    $requiredRoles = [
        'Admin' => 'admin',
        'AI Products' => 'ai-products',
        'AI Video Making' => 'ai-video-making',
        'Client Management' => 'client-management',
        'Facebook Ads' => 'facebook-ads',
        'Google Ads' => 'google-ads',
        'Graphics Design' => 'graphics-design',
        'Marketing Manager' => 'marketing-manager',
        'Search Engine Optimization' => 'search-engine-optimization',
        'Social Media Management' => 'social-media-management',
        'Website Design & Development' => 'website-design-development'
    ];

    // Helper to generate UUID v4
    function generateUuid() {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    // Insert or update based on name
    $insertStmt = $pdo->prepare("
        INSERT INTO roles (id, name, slug) VALUES (?, ?, ?)
        ON DUPLICATE KEY UPDATE slug = VALUES(slug)
    ");
    
    // We also need to get the id of the role after insert/update to keep it
    $getIdStmt = $pdo->prepare("SELECT id FROM roles WHERE name = ?");
    
    $keepIds = [];
    
    foreach ($requiredRoles as $name => $slug) {
        $newId = generateUuid();
        try {
            $insertStmt->execute([$newId, $name, $slug]);
        } catch (\PDOException $e) {
            // Ignore if slug conflict happens differently, but ON DUPLICATE KEY should handle it
        }
        $getIdStmt->execute([$name]);
        $existing = $getIdStmt->fetch();
        if ($existing) {
            $keepIds[] = $existing['id'];
        }
        echo "Processed $name ($slug)\n";
    }
    
    // Now find any old role that is NOT in keepIds, and reassign its users to 'Admin' (or simply not delete roles that are in use to avoid FK errors)
    // Actually, let's just delete roles that are NOT in keepIds AND have 0 users.
    $placeholders = implode(',', array_fill(0, count($keepIds), '?'));
    $deleteStmt = $pdo->prepare("
        DELETE FROM roles 
        WHERE id NOT IN ($placeholders) 
        AND id NOT IN (SELECT DISTINCT role_id FROM users)
    ");
    $deleteStmt->execute($keepIds);
    echo "Deleted " . $deleteStmt->rowCount() . " unused roles.\n";
    
    echo "Database Role Normalization Complete.\n";
    
} catch (\PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
