<?php
session_start();
$dbConfig = require_once 'config/database.php';

try {
    // Extract endpoint ID from the host (first part of the domain name)
    $hostParts = explode('.', $dbConfig['host']);
    $endpointId = $hostParts[0];
    
    // Create DSN with endpoint parameter
    $dsn = "pgsql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};sslmode=require;options=endpoint=$endpointId";
    $dbConn = new PDO($dsn, $dbConfig['user'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "Database connection successful!<br>";
    $connection_successful = true;
} catch (PDOException $e) {
    echo "Database connection failed: " . $e->getMessage();
    $error = "Database connection failed: " . $e->getMessage();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    if (!$connection_successful) {
        echo "Cannot login at this time due to database connection issues. Please try again later.";
    } else {
        try {
            // Debug: Show the query we're about to execute
            echo "<p>Attempting to find user with email: " . htmlspecialchars($email) . "</p>";
            
            // First check if the users table exists with the correct case
            $stmt = $dbConn->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public' AND table_name = 'users'");
            $usersTable = $stmt->fetchColumn();
            
            echo "<p>Found table: " . ($usersTable ? htmlspecialchars($usersTable) : "users table not found") . "</p>";
            
            // Prepare query to find user by email - use lowercase 'users' as per schema
            $query = "SELECT id, name, email, password FROM users WHERE email = :email";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            
            // Check if user exists
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<p>User found in database!</p>";
                
                // Verify password
                if (password_verify($password, $user['password'])) {
                    echo "<p>Password verified successfully!</p>";
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['name'];
                    $_SESSION['email'] = $user['email'];
                    
                    echo "<p>Session variables set. You would normally be redirected to dashboard.php now.</p>";
                } else {
                    echo "<p>Password verification failed.</p>";
                }
            } else {
                echo "<p>No user found with that email address.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Login failed: " . $e->getMessage() . "</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Debug Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen p-4">
    <div class="w-full max-w-md mx-auto mt-10">
        <h1 class="text-2xl font-bold mb-4">Debug Login Form</h1>
        
        <div class="bg-white rounded-xl shadow-lg p-8">
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your email">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your password">
                </div>

                <button type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    Debug Login
                </button>
            </form>
        </div>
    </div>
</body>
</html>
