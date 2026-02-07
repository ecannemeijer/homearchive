<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // Update system user email
    $pdo->exec("UPDATE users SET email = 'system@example.com' WHERE email = 'system@local'");
    echo "✓ System user email updated to system@example.com\n";
    
    // Verify admin user exists
    $stmt = $pdo->prepare("SELECT id, name, email FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($admin) {
        echo "✓ Admin user found: " . $admin['email'] . " (ID: " . $admin['id'] . ")\n";
    } else {
        echo "✗ Admin user NOT found with email admin@example.com\n";
        echo "\nAttempting to create admin user...\n";
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute(['Administrator', 'admin@example.com', $hash]);
        echo "✓ Admin user created\n";
    }
    
    echo "\n✓ LOGIN CREDENTIALS:\n";
    echo "   Email: admin@example.com\n";
    echo "   Password: admin123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
