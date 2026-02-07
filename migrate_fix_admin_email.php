<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // Update admin@localhost to admin@example.com
    $pdo->exec("UPDATE users SET email = 'admin@example.com' WHERE email = 'admin@localhost'");
    
    echo "Admin email updated to admin@example.com\n";
    echo "You can now login with: admin@example.com / admin123\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
