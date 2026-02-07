<?php

// Include Composer autoload (optional)
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

// Load .env file manually
$env_file = __DIR__ . '/../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue; // Skip comments
        if (strpos($line, '=') === false) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
    }
}

// Database configuration
return [
    'db' => [
        'host' => $_ENV['DB_HOST'] ?? 'localhost',
        'port' => $_ENV['DB_PORT'] ?? 3306,
        'database' => $_ENV['DB_NAME'] ?? 'abonnementen',
        'username' => $_ENV['DB_USER'] ?? 'root',
        'password' => $_ENV['DB_PASSWORD'] ?? '',
        'charset' => 'utf8mb4'
    ],
    'app' => [
        'name' => $_ENV['APP_NAME'] ?? 'Abonnementen Manager',
        'debug' => $_ENV['APP_DEBUG'] ?? false,
        'url' => $_ENV['APP_URL'] ?? 'http://localhost'
    ],
    'session' => [
        'name' => $_ENV['SESSION_NAME'] ?? 'subscription_app'
    ],
    'upload' => [
        'dir' => $_ENV['UPLOAD_DIR'] ?? 'uploads',
        'max_size' => $_ENV['MAX_UPLOAD_SIZE'] ?? 5242880, // 5MB
        'allowed_types' => ['pdf', 'jpg', 'jpeg', 'png', 'doc', 'docx', 'xls', 'xlsx', 'txt']
    ],
    'encryption' => [
        'key' => $_ENV['ENCRYPTION_KEY'] ?? 'default-key-change-this-32-chars!'
    ]
];
