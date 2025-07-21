<?php
$dbConfig = require_once 'config/database.php';

try {
    // Extract endpoint ID from the host (first part of the domain name)
    $hostParts = explode('.', $dbConfig['host']);
    $endpointId = $hostParts[0];
    
    // Create DSN with endpoint parameter
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};sslmode=require;options=endpoint=$endpointId";
    $dbConn = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Database connection successful!<br>";
    
    // Test query to check if the users table exists and its structure
    $stmt = $dbConn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "<h3>Tables in database:</h3>";
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . htmlspecialchars($table) . "</li>";
    }
    echo "</ul>";
    
    // Try to query the users table
    try {
        $stmt = $dbConn->query("SELECT * FROM users LIMIT 5");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<h3>Sample users (up to 5):</h3>";
        if (count($users) > 0) {
            echo "<table border='1'>";
            echo "<tr>";
            foreach (array_keys($users[0]) as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr>";
            
            foreach ($users as $user) {
                echo "<tr>";
                foreach ($user as $key => $value) {
                    // Don't display actual passwords
                    if ($key === 'password') {
                        echo "<td>[HIDDEN]</td>";
                    } else {
                        echo "<td>" . htmlspecialchars($value) . "</td>";
                    }
                }
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "No users found in the database.";
        }
    } catch (PDOException $e) {
        echo "Error querying users table: " . $e->getMessage();
    }
    
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
}
?>
