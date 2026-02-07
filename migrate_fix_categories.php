<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    
    // Update any remaining categories to use system user
    $pdo->exec('UPDATE categories SET user_id = 0 WHERE user_id IS NULL OR user_id NOT IN (SELECT id FROM users)');
    
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    
    echo "✓ Categories updated to use system user (id = 0)\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
