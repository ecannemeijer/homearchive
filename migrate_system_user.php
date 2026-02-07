<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // First, check if we can insert a user with id = 0
    // We need to temporarily disable the foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    
    // Try to create a system user with id = 0
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 0");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        // Insert system user with id = 0
        $stmt = $pdo->prepare("INSERT INTO users (id, name, email, password, is_admin) VALUES (0, 'System', 'system@local', '', 0)");
        $stmt->execute();
        echo "✓ System user created with id = 0\n";
    } else {
        echo "✓ System user with id = 0 already exists\n";
    }
    
    // Re-enable foreign key checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\nShared data system is ready!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
