<?php
$dbhost = 'localhost';
$dbname = 'abonnementen';
$dbuser = 'root';
$dbpass = '';

$conn = new mysqli($dbhost, $dbuser, $dbpass, $dbname);

if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

echo "=== Manual Insert Test ===\n";

// Check FK checks status
$result = $conn->query('SELECT @@foreign_key_checks');
$row = $result->fetch_assoc();
echo "Foreign Key Checks: " . ($row['@@foreign_key_checks'] == 1 ? 'ENABLED' : 'DISABLED') . "\n\n";

// Try with explicit user_id=6
echo "Test 1: Insert with explicit user_id = 6\n";
$sql = "INSERT INTO subscriptions (user_id, name, type, cost, frequency) VALUES (6, 'Test Sub', 'subscription', 9.99, 'monthly')";
if ($conn->query($sql)) {
    echo "✓ Success! Inserted with explicit user_id=6\n";
    $last_id = $conn->insert_id;
    echo "  Last insert ID: " . $last_id . "\n";
} else {
    echo "✗ Error: " . $conn->error . "\n";
}

// Try without user_id (should use DEFAULT 6)
echo "\nTest 2: Insert without user_id (should use DEFAULT 6)\n";
$sql = "INSERT INTO subscriptions (name, type, cost, frequency) VALUES ('Test Sub 2', 'subscription', 9.99, 'monthly')";
if ($conn->query($sql)) {
    echo "✓ Success! Inserted without explicit user_id (using DEFAULT)\n";
    $last_id = $conn->insert_id;
    echo "  Last insert ID: " . $last_id . "\n";
} else {
    echo "✗ Error: " . $conn->error . "\n";
}

$conn->close();
?>
