<?php

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // Create a shared user if it doesn't exist
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 0");
    $stmt->execute();
    if ($stmt->rowCount() === 0) {
        // We need to insert a user with id = 0
        // First, let's see what the MIN id is
        $result = $pdo->query("SELECT MIN(id) as min_id FROM users");
        $row = $result->fetch(PDO::FETCH_ASSOC);
        
        // We'll use NULL for user_id in categories instead, or insert a shared user
        // Let's check if we can alter the foreign key or just set categories.user_id to NULL
        try {
            // Try to set categories user_id to NULL (if column allows NULL)
            $pdo->exec('UPDATE categories SET user_id = NULL');
            echo "Updated categories to user_id = NULL\n";
        } catch (Exception $e) {
            echo "Could not update categories: " . $e->getMessage() . "\n";
        }
    }
    
    // Update all subscriptions to have user_id = 0
    $pdo->exec('UPDATE subscriptions SET user_id = 0');
    echo "Updated subscriptions to user_id = 0\n";
    
    // Update all passwords to have user_id = 0
    $pdo->exec('UPDATE passwords SET user_id = 0');
    echo "Updated passwords to user_id = 0\n";
    
    // Update all documents to have user_id = 0
    $pdo->exec('UPDATE documents SET user_id = 0');
    echo "Updated documents to user_id = 0\n";
    
    echo "\nAll data is now shared across all users!\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
