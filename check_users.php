<?php
require_once __DIR__ . '/config/helpers.php';

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    $stmt = $pdo->query('SELECT id, name, email FROM users');
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "=== Users in Database ===\n";
    if (empty($users)) {
        echo "NO USERS FOUND!\n";
    } else {
        foreach ($users as $user) {
            echo "ID: {$user['id']}, Name: {$user['name']}, Email: {$user['email']}\n";
        }
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
