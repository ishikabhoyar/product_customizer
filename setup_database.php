<?php
// Load database configuration
$dbConfig = require_once 'config/database.php';

try {
    // Extract endpoint ID from the host (first part of the domain name)
    $hostParts = explode('.', $dbConfig['host']);
    $endpointId = $hostParts[0];
    
    // Create DSN with endpoint parameter
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};sslmode=require;options=endpoint=$endpointId";
    $dbConn = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "<p>Database connection successful!</p>";
    
    // Read schema file
    $schemaFile = file_get_contents('database/schema.sql');
    
    // Split into individual statements
    $statements = explode(';', $schemaFile);
    
    // Execute each statement
    foreach ($statements as $statement) {
        $statement = trim($statement);
        if (!empty($statement)) {
            try {
                $dbConn->exec($statement);
                echo "<p>Executed: " . htmlspecialchars(substr($statement, 0, 50)) . "...</p>";
            } catch (PDOException $e) {
                echo "<p>Error executing statement: " . htmlspecialchars($e->getMessage()) . "</p>";
                echo "<pre>" . htmlspecialchars($statement) . "</pre>";
            }
        }
    }
    
    echo "<p>Database setup complete!</p>";
    
} catch (PDOException $e) {
    echo "<p>Database connection failed: " . htmlspecialchars($e->getMessage()) . "</p>";
}
?>
