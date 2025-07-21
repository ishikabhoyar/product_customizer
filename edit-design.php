<?php
session_start();

// Check if user is logged in, redirect if not
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include necessary files
require_once 'includes/Database.php';
require_once 'includes/Design.php';

// Initialize Design class
$design = new Design();

// Get user ID from session
$userId = $_SESSION['user_id'];

// Check if design ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$designId = $_GET['id'];
$error = '';
$success = '';

// Get the design details
try {
    $currentDesign = $design->getDesignById($designId, $userId);

    if (!$currentDesign) {
        $_SESSION['message'] = "Design not found or you don't have permission to edit it.";
        header("Location: dashboard.php");
        exit();
    }
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $type = $_POST['type'];
    $color = $_POST['color'];

    try {
        $data = [
            'name' => $name,
            'type' => $type,
            'color' => $color
        ];

        $updated = $design->updateDesign($designId, $userId, $data);

        if ($updated) {
            $success = "Design updated successfully!";
            // Refresh the design data
            $currentDesign = $design->getDesignById($designId, $userId);
        } else {
            $error = "No changes were made to the design.";
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Design</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Top Header -->
        <header class="bg-white shadow-sm z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-800">Edit Design</h1>
                <a href="dashboard.php" class="text-blue-600 hover:text-blue-800">
                    <i class="fas fa-arrow-left mr-1"></i> Back to Dashboard
                </a>
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-1 py-10">
            <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <?php if (!empty($error)): ?>
                    <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success)): ?>
                    <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-6">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <div class="bg-white shadow rounded-lg overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-800">Design Details</h2>
                    </div>

                    <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?id=' . $designId); ?>" class="p-6">
                        <div class="grid grid-cols-1 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Design Name</label>
                                <input type="text" id="name" name="name" required
                                    class="w-full px-4 py-2 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    value="<?php echo htmlspecialchars($currentDesign['name']); ?>">
                            </div>

                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">Design Type</label>
                                <select id="type" name="type" required
                                    class="w-full px-4 py-2 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="tshirt" <?php echo $currentDesign['type'] === 'tshirt' ? 'selected' : ''; ?>>T-shirt</option>
                                    <option value="phone" <?php echo $currentDesign['type'] === 'phone' ? 'selected' : ''; ?>>Phone Cover</option>
                                    <option value="watch" <?php echo $currentDesign['type'] === 'watch' ? 'selected' : ''; ?>>Watch</option>
                                </select>
                            </div>

                            <div>
                                <label for="color" class="block text-sm font-medium text-gray-700 mb-1">Color</label>
                                <input type="text" id="color" name="color"
                                    class="w-full px-4 py-2 rounded-md border border-gray-300 focus:ring-blue-500 focus:border-blue-500"
                                    value="<?php echo htmlspecialchars($currentDesign['color'] ?? ''); ?>">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Design Preview</label>
                                <div class="mt-1 border border-gray-300 rounded-md p-4 bg-gray-50 flex items-center justify-center">
                                    <?php if (!empty($currentDesign['design_path'])): ?>
                                        <img src="<?php echo htmlspecialchars($currentDesign['design_path']); ?>" alt="Design Preview" class="max-h-64">
                                    <?php else: ?>
                                        <div class="text-gray-400">No preview available</div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            <a href="dashboard.php" class="text-gray-600 hover:text-gray-800 mr-4">Cancel</a>
                            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                                Save Changes
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
