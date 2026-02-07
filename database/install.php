<?php
/**
 * Database installatie script
 * Run: php database/install.php
 */

require_once __DIR__ . '/../config/helpers.php';

echo "=== Database Installatie ===\n\n";

try {
    $config = config('db');
    
    if (!$config) {
        die("❌ Fout: Database configuratie niet gevonden. Zorg ervoor dat .env bestand correct is ingesteld.\n");
    }
    
    // Prepare credentials
    $host = !empty($config['host']) ? $config['host'] : 'localhost';
    $port = !empty($config['port']) ? $config['port'] : 3306;
    $database = !empty($config['database']) ? $config['database'] : 'abonnementen';
    $username = !empty($config['username']) ? $config['username'] : 'root';
    $password = $config['password'] ?? '';
    
    // Connect to MySQL (without selecting database)
    $pdo = new PDO(
        'mysql:host=' . $host . ';port=' . $port,
        $username,
        $password
    );
    
    // Drop existing database if exists
    $pdo->exec('DROP DATABASE IF EXISTS ' . $database);
    echo "✓ Database verwijderd (indien aanwezig)\n";
    
    // Create database
    $pdo->exec('CREATE DATABASE ' . $database . ' 
        CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
    echo "✓ Database aangemaakt: " . $database . "\n";
    
    // Connect to new database
    $pdo = new PDO(
        'mysql:host=' . $host . 
        ';port=' . $port . 
        ';dbname=' . $database .
        ';charset=utf8mb4',
        $username,
        $password
    );
    
    // Read and execute schema
    $schema = file_get_contents(__DIR__ . '/schema.sql');
    $queries = array_filter(array_map('trim', explode(';', $schema)));
    
    foreach ($queries as $query) {
        if (!empty($query)) {
            $pdo->exec($query);
        }
    }
    
    echo "✓ Database schema geïmporteerd\n";
    
    // Create test user (optional)
    $test_email = 'test@example.com';
    $test_password = password_hash('password', PASSWORD_DEFAULT);
    
    $stmt = $pdo->prepare(
        'INSERT INTO users (name, email, password) VALUES (?, ?, ?)'
    );
    $stmt->execute(['Test User', $test_email, $test_password]);
    
    echo "✓ Test gebruiker aangemaakt\n";
    echo "   Email: " . $test_email . "\n";
    echo "   Wachtwoord: password\n\n";
    
    echo "✅ Database installatie voltooid!\n";
    echo "\nU kunt nu inloggen met de test gegevens.\n";
    
} catch (Exception $e) {
    echo "❌ Fout: " . $e->getMessage() . "\n";
    exit(1);
}
