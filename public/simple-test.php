<?php
// Very simple test - no dependencies
echo "✓ PHP works<br>";
echo "✓ Current file: " . __FILE__ . "<br>";
echo "✓ Server: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "✓ REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD'] . "<br>";

// Test database connection
try {
    $conn = new mysqli('localhost', 'root', '', 'abonnementen');
    if ($conn->connect_error) {
        echo "✗ Database error: " . $conn->connect_error;
    } else {
        echo "✓ Database connection works<br>";
        
        $result = $conn->query('SELECT email, password FROM users WHERE email = "admin@example.com"');
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            echo "✓ Admin user found<br>";
            echo "✓ Email: " . $row['email'] . "<br>";
            
            // Test password
            $hash = $row['password'];
            if (password_verify('admin123', $hash)) {
                echo "✓ Password admin123 is CORRECT<br>";
            } else {
                echo "✗ Password admin123 is WRONG<br>";
                echo "✓ Hash stored: " . substr($hash, 0, 20) . "...<br>";
            }
        } else {
            echo "✗ Admin user NOT found<br>";
        }
        
        $conn->close();
    }
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage();
}
?>
