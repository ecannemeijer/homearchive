<?php
/**
 * Production Database Setup
 * Ensures system user (id=6) exists for shared data
 */

try {
    // Connect to production database
    $db_host = getenv('DB_HOST') ?: 'localhost';
    $db_port = getenv('DB_PORT') ?: 3306;
    $db_name = getenv('DB_NAME') ?: 'abonnementen';
    $db_user = getenv('DB_USER') ?: 'root';
    $db_pass = getenv('DB_PASSWORD') ?: '';
    
    $pdo = new PDO(
        "mysql:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8mb4",
        $db_user,
        $db_pass
    );
    
    echo "=== Production Database Setup ===\n";
    echo "Database: $db_name\n";
    echo "Host: $db_host:$db_port\n\n";
    
    // Disable FK checks temporarily
    $pdo->exec('SET FOREIGN_KEY_CHECKS=0');
    
    // Check if system user (id=6) exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE id = 6");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo "Creating system user (id=6)...\n";
        $stmt = $pdo->prepare("INSERT INTO users (id, name, email, password, is_admin) VALUES (6, 'System', 'system@example.com', '', 0)");
        $stmt->execute();
        echo "✓ System user created\n";
    } else {
        echo "✓ System user already exists\n";
    }
    
    // Ensure admin user exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = 'admin@example.com'");
    $stmt->execute();
    
    if ($stmt->rowCount() === 0) {
        echo "Creating admin user...\n";
        $hash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, is_admin) VALUES (?, ?, ?, 1)");
        $stmt->execute(['Administrator', 'admin@example.com', $hash]);
        echo "✓ Admin user created\n";
    } else {
        echo "✓ Admin user already exists\n";
    }
    
    // Re-enable FK checks
    $pdo->exec('SET FOREIGN_KEY_CHECKS=1');
    
    echo "\n✓ Setup Complete!\n";
    echo "\nLogin Credentials:\n";
    echo "  Email: admin@example.com\n";
    echo "  Password: admin123\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>
