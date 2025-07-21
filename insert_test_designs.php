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

// Sample designs
$sampleDesigns = [
    [
        'type' => 'tshirt',
        'name' => 'Summer Vibes',
        'design_path' => 'uploads/designs/tshirt1.jpg',
        'color' => 'Blue'
    ],
    [
        'type' => 'tshirt',
        'name' => 'Neon Dreams',
        'design_path' => 'uploads/designs/tshirt2.jpg',
        'color' => 'Green'
    ],
    [
        'type' => 'phone',
        'name' => 'Geometric Art',
        'design_path' => 'uploads/designs/phone1.jpg',
        'color' => 'Black'
    ],
    [
        'type' => 'watch',
        'name' => 'Elegant Face',
        'design_path' => 'uploads/designs/watch1.jpg',
        'color' => 'Silver'
    ],
    [
        'type' => 'phone',
        'name' => 'Abstract Pattern',
        'design_path' => 'uploads/designs/phone2.jpg',
        'color' => 'Red'
    ]
];

// Create uploads directory if it doesn't exist
if (!file_exists('uploads/designs')) {
    mkdir('uploads/designs', 0777, true);
}

// Create empty image files for testing
foreach ($sampleDesigns as $design_data) {
    $path = $design_data['design_path'];
    if (!file_exists($path)) {
        file_put_contents($path, ''); // Create empty file
    }
}

// Insert designs
$inserted = 0;
$errors = [];

foreach ($sampleDesigns as $design_data) {
    try {
        $design->saveDesign(
            $userId,
            $design_data['type'],
            $design_data['name'],
            $design_data['design_path'],
            $design_data['color']
        );
        $inserted++;
    } catch (Exception $e) {
        $errors[] = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Test Designs</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 p-6">
    <div class="max-w-2xl mx-auto bg-white rounded-lg shadow p-6">
        <h1 class="text-2xl font-bold mb-4">Test Data Insertion</h1>
        
        <?php if ($inserted > 0): ?>
            <div class="bg-green-50 text-green-700 p-4 rounded-lg mb-4">
                Successfully inserted <?php echo $inserted; ?> sample designs.
            </div>
        <?php endif; ?>
        
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 text-red-700 p-4 rounded-lg mb-4">
                <p class="font-bold">Errors:</p>
                <ul class="list-disc pl-5">
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>
        
        <a href="dashboard.php" class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Return to Dashboard
        </a>
    </div>
</body>
</html>
