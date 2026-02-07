<?php
require_once __DIR__ . '/../config/helpers.php';

$user_model = new \App\Models\User();
$user = $user_model->by_email('admin@example.com');

if ($user) {
    echo "✓ User found: " . $user['name'] . "<br>";
    echo "✓ Email: " . $user['email'] . "<br>";
    echo "✓ Password hash exists: " . (!empty($user['password']) ? 'Yes' : 'No') . "<br>";
    
    $verify = $user_model->verify_password('admin@example.com', 'admin123');
    echo "✓ Password verification (admin123): " . ($verify ? 'SUCCESS' : 'FAILED') . "<br>";
} else {
    echo "✗ User NOT found!";
}
?>
