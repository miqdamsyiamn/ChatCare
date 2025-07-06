<?php

// Load environment variables (if not already loaded)
// This is a basic example, in a real app you might use a library like phpdotenv
$dbHost = getenv('DB_HOST') ?: 'localhost';
$dbUser = getenv('DB_USERNAME') ?: 'root';
$dbPass = getenv('DB_PASSWORD') ?: '';
$dbName = getenv('DB_DATABASE') ?: 'chatcare_db';

$sqlFile = __DIR__ . '/../chatcare_db.sql';

if (!file_exists($sqlFile)) {
    die("Error: SQL file not found at $sqlFile\n");
}

try {
    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4", $dbUser, $dbPass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to database successfully.\n";

    // Read the SQL file content
    $sql = file_get_contents($sqlFile);

    // Execute the SQL queries
    $pdo->exec($sql);

    echo "Database migration (chatcare_db.sql) completed successfully.\n";

} catch (PDOException $e) {
    die("Database error: " . $e->getMessage() . "\n");
} catch (Exception $e) {
    die("General error: " . $e->getMessage() . "\n");
}

?>