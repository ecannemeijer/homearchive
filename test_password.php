<?php
require_once __DIR__ . '/config/helpers.php';

try {
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
    
    // Get the test user
    $stmt = $pdo->query("SELECT id, name, email, password FROM users WHERE email = 'test@example.com'");
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "User not found!\n";
        exit;
    }
    
    echo "=== Password Verification Test ===\n";
    echo "User Found: {$user['email']}\n";
    echo "Password Hash: " . substr($user['password'], 0, 20) . "...\n";
    echo "\n";
    
    // Test password verification
    $test_password = 'password';
    $is_valid = password_verify($test_password, $user['password']);
    
    echo "Testing password: '{$test_password}'\n";
    echo "Verification Result: " . ($is_valid ? 'PASS ✓' : 'FAIL ✗') . "\n";
    
    // Also test with the User model
    echo "\n=== Testing via User Model ===\n";
    $user_model = new App\Models\User();
    $verify = $user_model->verify_password('test@example.com', 'password');
    echo "Model Verification Result: " . ($verify ? 'PASS ✓' : 'FAIL ✗') . "\n";
    
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
?>
