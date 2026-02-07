<?php
require 'config/config.php';

try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ':' . DB_PORT . ';dbname=' . DB_NAME, DB_USER, DB_PASSWORD);
    
    echo "=== Subscriptions Table Structure ===\n";
    $result = $pdo->query('SHOW CREATE TABLE subscriptions');
    echo $result->fetchColumn(1);
    echo "\n\n";
    
    echo "=== Foreign Key Constraints ===\n";
    $result = $pdo->query("SELECT * FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='subscriptions' AND CONSTRAINT_NAME LIKE 'subscriptions_ibfk%'");
    foreach ($result as $row) {
        print_r($row);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
