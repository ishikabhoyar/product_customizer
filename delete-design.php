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

try {
    $deleted = $design->deleteDesign($designId, $userId);

    if ($deleted) {
        $_SESSION['message'] = "Design deleted successfully!";
    } else {
        $_SESSION['message'] = "Design not found or you don't have permission to delete it.";
    }
} catch (Exception $e) {
    $_SESSION['message'] = "Error deleting design: " . $e->getMessage();
}

// Redirect back to dashboard
header("Location: dashboard.php");
exit();
?>
