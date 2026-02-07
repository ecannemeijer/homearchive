<?php
$dbhost = 'localhost';
$dbname = 'abonnementen';
$dbuser = 'root';
$dbpass = '';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "=== Subscriptions Table Structure ===\n";
$result = $conn->query('DESCRIBE subscriptions');
while ($row = $result->fetch_assoc()) {
    echo $row['Field'] . ' | ' . $row['Type'] . ' | ' . $row['Null'] . ' | ' . $row['Key'] . ' | ' . $row['Default'] . "\n";
}

echo "\n=== Foreign Keys ===\n";
$result = $conn->query("SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME 
FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
WHERE TABLE_NAME='subscriptions' AND CONSTRAINT_NAME LIKE 'subscriptions_ibfk%'");

while ($row = $result->fetch_assoc()) {
    echo "Constraint: " . $row['CONSTRAINT_NAME'] . "\n";
    echo "  Column: " . $row['TABLE_NAME'] . '.' . $row['COLUMN_NAME'] . "\n";
    echo "  References: " . $row['REFERENCED_TABLE_NAME'] . '.' . $row['REFERENCED_COLUMN_NAME'] . "\n";
}

echo "\n=== User ID 6 check ===\n";
$result = $conn->query('SELECT id, name, email FROM users WHERE id = 6');
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo "✓ User ID 6 exists: " . $row['name'] . ' (' . $row['email'] . ')' . "\n";
} else {
    echo "✗ User ID 6 does NOT exist!\n";
}

$conn->close();
?>
