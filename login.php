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
    $connection_successful = true;
} catch (PDOException $e) {
    $error = "Database connection failed: " . $e->getMessage();
}

// Check for success message from registration
if (isset($_SESSION['message'])) {
    $success_message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!$connection_successful) {
        $error = "Cannot login at this time due to database connection issues. Please try again later.";
    } else {
        try {
            // Prepare query to find user by email - use lowercase 'users' as per schema
            $query = "SELECT id, name, email, password FROM users WHERE email = :email";
            $stmt = $dbConn->prepare($query);
            $stmt->bindParam(':email', $email);
            $stmt->execute();

            // Check if user exists
            if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {
                // Verify password
                if (password_verify($password, $user['password'])) {
                    // Password is correct, set session variables
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['name'];
                    $_SESSION['email'] = $user['email'];

                    // If remember me is checked, set a cookie (optional)
                    if (isset($_POST['remember']) && $_POST['remember'] == 'on') {
                        setcookie('user_email', $email, time() + (86400 * 30), "/"); // 30 days
                    }

                    // Redirect to dashboard
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Invalid email or password";
                }
            } else {
                $error = "Invalid email or password";
            }
        } catch (PDOException $e) {
            $error = "Login failed: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Login</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gradient-to-br from-indigo-50 to-blue-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Logo and Title -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-blue-600 text-white mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-gray-900">Product Designer</h1>
            <p class="text-gray-600 mt-2">Sign in to your account</p>
        </div>

        <!-- Login Form -->
        <div class="bg-white rounded-xl shadow-lg p-8">
            <?php if (!empty($error)): ?>
                <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if (isset($success_message)): ?>
                <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-6">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your email"
                        value="<?php echo isset($_COOKIE['user_email']) ? htmlspecialchars($_COOKIE['user_email']) : ''; ?>">
                </div>

                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                        placeholder="Enter your password">
                </div>

                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center">
                        <input id="remember" name="remember" type="checkbox"
                            class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                        <label for="remember" class="ml-2 block text-sm text-gray-700">
                            Remember me
                        </label>
                    </div>
                    <a href="#" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Forgot password?
                    </a>
                </div>

                <button type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 px-4 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-150">
                    Sign In
                </button>
            </form>

            <div class="mt-6 text-center text-sm">
                <p class="text-gray-600">
                    Don't have an account?
                    <a href="signup.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Create one now
                    </a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
