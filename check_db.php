<?php
$pdo = new PDO('mysql:host=localhost;port=3306;dbname=abonnementen;charset=utf8mb4', 'root', '');
$result = $pdo->query('SELECT id, name, email FROM users ORDER BY id');
$users = $result->fetchAll(PDO::FETCH_ASSOC);

echo "=== Users in Database ===\n";
foreach ($users as $row) {
    echo "ID: " . $row['id'] . ", Name: " . $row['name'] . ", Email: " . $row['email'] . "\n";
}

echo "\n=== Checking subscriptions table ===\n";
$stmt = $pdo->query('SHOW COLUMNS FROM subscriptions');
$columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($columns as $col) {
    if ($col['Field'] == 'user_id') {
        echo "user_id column: " . $col['Type'] . ", Null: " . $col['Null'] . "\n";
    }
}

echo "\n=== Checking foreign keys ===\n";
$stmt = $pdo->query("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE TABLE_NAME='subscriptions' AND COLUMN_NAME='user_id'");
$fks = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($fks as $fk) {
    echo "Constraint: " . $fk['CONSTRAINT_NAME'] . "\n";
    echo "  References: " . $fk['REFERENCED_TABLE_NAME'] . "(" . $fk['REFERENCED_COLUMN_NAME'] . ")\n";
}
?>
