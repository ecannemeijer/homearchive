<?php
require_once __DIR__ . '/config/helpers.php';

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // Check if is_admin column exists
    $stmt = $pdo->query("SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME='users' AND COLUMN_NAME='is_admin'");
    if ($stmt->rowCount() > 0) {
        echo "is_admin column already exists\n";
    } else {
        $pdo->exec('ALTER TABLE users ADD COLUMN is_admin TINYINT DEFAULT 0 AFTER password');
        echo "is_admin column added\n";
    }

    // Update test user to be admin
    $pdo->exec("UPDATE users SET is_admin = 1 WHERE email = 'test@example.com'");
    echo "Test user updated to admin\n";

    // Create admin user if doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@localhost'");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        $admin_password = password_hash('admin123', PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)")
            ->execute(['Administrator', 'admin@localhost', $admin_password]);
        echo "Admin user created: admin@localhost / admin123\n";
    }

} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
