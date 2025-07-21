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

// Get design statistics
try {
    $stats = $design->getDesignStats($userId);
    $recentDesigns = $design->getRecentDesigns($userId, 5);
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Check for session messages
$message = '';
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']); // Clear the message after displaying
}

// Helper function to format dates
function formatDate($dateString) {
    $date = new DateTime($dateString);
    return $date->format('M j, Y');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Designer Dashboard</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        /* Popup styles */
        .sidebar-item {
            position: relative;
        }

        .popup {
            position: absolute;
            left: 100%;
            top: 0;
            width: 300px;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            z-index: 50;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.2s ease, visibility 0.2s ease;
            overflow: hidden;
            margin-left: 10px; /* Add some space between sidebar and popup */
        }

        /* Add a small triangle to connect popup with sidebar */
        .popup::before {
            content: '';
            position: absolute;
            left: -10px;
            top: 15px;
            width: 0;
            height: 0;
            border-top: 10px solid transparent;
            border-bottom: 10px solid transparent;
            border-right: 10px solid white;
        }

        .sidebar-item:hover .popup {
            opacity: 1;
            visibility: visible;
        }

        .popup-header {
            background-color: #3b82f6;
            color: white;
            padding: 0.75rem 1rem;
            font-weight: 600;
        }

        .popup-content {
            padding: 1rem;
        }

        .popup-item {
            display: flex;
            align-items: center;
            padding: 0.75rem 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.15s ease;
            border-radius: 0.25rem;
        }

        .popup-item:hover {
            background-color: #f3f4f6;
        }

        .popup-item:last-child {
            border-bottom: none;
        }

        .popup-item-icon {
            width: 2rem;
            height: 2rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 0.375rem;
            margin-right: 0.75rem;
        }

        .popup-item-details {
            flex: 1;
        }

        .popup-item-title {
            font-weight: 500;
            color: #1f2937;
            margin-bottom: 0.25rem;
        }

        .popup-item-description {
            font-size: 0.75rem;
            color: #6b7280;
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-gradient-to-b from-blue-600 to-blue-800 text-white shadow-lg">
            <!-- Logo and Title -->
            <div class="p-6 flex items-center justify-center flex-col border-b border-blue-500">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white text-blue-600 mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h1 class="text-xl font-bold">Product Designer</h1>
            </div>

            <!-- Navigation Links -->
            <div class="mt-6">
                <!-- Create New Designs with Popup -->
                <div class="sidebar-item">
                    <a href="design.php" class="block px-6 py-3 text-white hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-plus-circle w-6"></i>
                        <span>Create New Designs</span>
                    </a>
                    <div class="popup">
                        <div class="popup-header">Create New Design</div>
                        <div class="popup-content">
                            <div class="popup-item">
                                <div class="popup-item-icon bg-red-100 text-red-600">
                                    <i class="fas fa-tshirt"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">T-shirt Design</div>
                                    <div class="popup-item-description">Create custom t-shirt designs with various styles and colors</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-blue-100 text-blue-600">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Watch Design</div>
                                    <div class="popup-item-description">Design custom watch faces and straps</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-green-100 text-green-600">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Phone Case Design</div>
                                    <div class="popup-item-description">Create unique phone cases for various models</div>
                                </div>
                            </div>
                            <a href="design.php" class="mt-2 block w-full text-center bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-md transition duration-150">Start Designing</a>
                        </div>
                    </div>
                </div>

                <!-- T-shirt Designs with Popup -->
                <div class="sidebar-item">
                    <a href="#" class="block px-6 py-3 text-white hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-tshirt w-6"></i>
                        <span>T-shirt Designs</span>
                    </a>
                    <div class="popup">
                        <div class="popup-header">T-shirt Designs</div>
                        <div class="popup-content">
                            <div class="popup-item">
                                <div class="popup-item-icon bg-purple-100 text-purple-600">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">View All T-shirts</div>
                                    <div class="popup-item-description">Browse all your t-shirt designs</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-star"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Featured Designs</div>
                                    <div class="popup-item-description">See your most popular t-shirt designs</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-red-100 text-red-600">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Create New T-shirt</div>
                                    <div class="popup-item-description">Start a new t-shirt design project</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                You have <?php echo $stats['tshirt']['count']; ?> t-shirt designs
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Watch Designs with Popup -->
                <div class="sidebar-item">
                    <a href="#" class="block px-6 py-3 text-white hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-clock w-6"></i>
                        <span>Watch Designs</span>
                    </a>
                    <div class="popup">
                        <div class="popup-header">Watch Designs</div>
                        <div class="popup-content">
                            <div class="popup-item">
                                <div class="popup-item-icon bg-purple-100 text-purple-600">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">View All Watches</div>
                                    <div class="popup-item-description">Browse all your watch designs</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-blue-100 text-blue-600">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Create New Watch</div>
                                    <div class="popup-item-description">Start a new watch design project</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                You have <?php echo $stats['watch']['count']; ?> watch designs
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Phone Cover Designs with Popup -->
                <div class="sidebar-item">
                    <a href="#" class="block px-6 py-3 text-white hover:bg-blue-700 transition duration-200 flex items-center">
                        <i class="fas fa-mobile-alt w-6"></i>
                        <span>Phone Cover Designs</span>
                    </a>
                    <div class="popup">
                        <div class="popup-header">Phone Cover Designs</div>
                        <div class="popup-content">
                            <div class="popup-item">
                                <div class="popup-item-icon bg-purple-100 text-purple-600">
                                    <i class="fas fa-list"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">View All Phone Covers</div>
                                    <div class="popup-item-description">Browse all your phone case designs</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-green-100 text-green-600">
                                    <i class="fas fa-plus"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">Create New Phone Case</div>
                                    <div class="popup-item-description">Start a new phone case design project</div>
                                </div>
                            </div>
                            <div class="popup-item">
                                <div class="popup-item-icon bg-yellow-100 text-yellow-600">
                                    <i class="fas fa-mobile-alt"></i>
                                </div>
                                <div class="popup-item-details">
                                    <div class="popup-item-title">iPhone 14 Cases</div>
                                    <div class="popup-item-description">View your iPhone 14 case designs</div>
                                </div>
                            </div>
                            <div class="text-xs text-gray-500 mt-2">
                                You have <?php echo $stats['phone']['count']; ?> phone case designs
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- User Account -->
            <div class="absolute bottom-0 w-64 border-t border-blue-500">
                <div class="px-6 py-4 flex items-center">
                    <div class="h-8 w-8 rounded-full bg-white text-blue-600 flex items-center justify-center">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($_SESSION['username'] ?? 'Designer'); ?></p>
                        <a href="signout.php" class="text-xs text-blue-200 hover:text-white">Sign Out</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Header -->
            <header class="bg-white shadow-sm z-10">
                <div class="px-6 py-4 flex items-center justify-between">
                    <h2 class="text-2xl font-semibold text-gray-800">Dashboard</h2>
                    <div class="flex items-center">
                        <div class="relative">
                            <button class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i class="fas fa-bell"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Content -->
            <main class="flex-1 overflow-y-auto p-6 bg-gray-50">
                <?php if (!empty($error)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($error); ?></span>
                </div>
                <?php endif; ?>

                <?php if (!empty($message)): ?>
                <div class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline"><?php echo htmlspecialchars($message); ?></span>
                </div>
                <?php endif; ?>
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <!-- Total Designs Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                                <i class="fas fa-paint-brush"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Total Designs</h3>
                                <p class="text-2xl font-bold"><?php echo $stats['total']['count']; ?></p>
                            </div>
                        </div>
                        <div class="mt-3 text-green-600 text-xs flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span><?php echo $stats['total']['growth']; ?>% increase</span>
                        </div>
                    </div>

                    <!-- T-shirt Designs Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100 text-green-600">
                                <i class="fas fa-tshirt"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">T-shirt Designs</h3>
                                <p class="text-2xl font-bold"><?php echo $stats['tshirt']['count']; ?></p>
                            </div>
                        </div>
                        <div class="mt-3 text-green-600 text-xs flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span><?php echo $stats['tshirt']['growth']; ?>% increase</span>
                        </div>
                    </div>

                    <!-- Watch Designs Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Watch Designs</h3>
                                <p class="text-2xl font-bold"><?php echo $stats['watch']['count']; ?></p>
                            </div>
                        </div>
                        <div class="mt-3 text-green-600 text-xs flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span><?php echo $stats['watch']['growth']; ?>% increase</span>
                        </div>
                    </div>

                    <!-- Phone Cover Designs Card -->
                    <div class="bg-white rounded-lg shadow p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-orange-100 text-orange-600">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-gray-500 text-sm">Phone Covers</h3>
                                <p class="text-2xl font-bold"><?php echo $stats['phone']['count']; ?></p>
                            </div>
                        </div>
                        <div class="mt-3 text-green-600 text-xs flex items-center">
                            <i class="fas fa-arrow-up mr-1"></i>
                            <span><?php echo $stats['phone']['growth']; ?>% increase</span>
                        </div>
                    </div>
                </div>

                <!-- Recent Designs Table -->
                <div class="bg-white rounded-lg shadow">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-medium text-gray-800">Recent Designs</h2>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Design Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php if (empty($recentDesigns)): ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                        No designs found. <a href="design.php" class="text-blue-600 hover:underline">Create your first design</a>.
                                    </td>
                                </tr>
                                <?php else: ?>
                                    <?php foreach ($recentDesigns as $design): ?>
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="h-10 w-10 rounded-md bg-gray-200 flex items-center justify-center">
                                                    <?php if ($design['type'] === 'tshirt'): ?>
                                                        <i class="fas fa-tshirt text-gray-500"></i>
                                                    <?php elseif ($design['type'] === 'phone'): ?>
                                                        <i class="fas fa-mobile-alt text-gray-500"></i>
                                                    <?php elseif ($design['type'] === 'watch'): ?>
                                                        <i class="fas fa-clock text-gray-500"></i>
                                                    <?php else: ?>
                                                        <i class="fas fa-paint-brush text-gray-500"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="ml-4">
                                                    <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($design['name']); ?></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900">
                                                <?php
                                                $typeLabels = [
                                                    'tshirt' => 'T-shirt',
                                                    'phone' => 'Phone Cover',
                                                    'watch' => 'Watch'
                                                ];
                                                echo $typeLabels[$design['type']] ?? ucfirst($design['type']);
                                                ?>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo formatDate($design['created_at']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <?php if ($design['status'] === 'Published'): ?>
                                                <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Published</span>
                                            <?php elseif ($design['status'] === 'In Cart'): ?>
                                                <span class="px-2 py-1 text-xs rounded-full bg-blue-100 text-blue-800">In Cart</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Draft</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <a href="edit-design.php?id=<?php echo $design['id']; ?>" class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</a>
                                            <a href="delete-design.php?id=<?php echo $design['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Are you sure you want to delete this design?');">Delete</a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-200">
                        <a href="#" class="text-blue-600 hover:text-blue-900 text-sm font-medium">View all designs →</a>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Make popup items clickable
            const popupItems = document.querySelectorAll('.popup-item');
            popupItems.forEach(item => {
                item.addEventListener('click', function() {
                    // Get the parent sidebar item's link
                    const sidebarLink = this.closest('.sidebar-item').querySelector('a').getAttribute('href');

                    // If the link is not just a '#', navigate to it
                    if (sidebarLink && sidebarLink !== '#') {
                        window.location.href = sidebarLink;
                    }
                });

                // Add cursor pointer to show it's clickable
                item.style.cursor = 'pointer';
            });

            // Add delay before hiding popups to make them more user-friendly
            const sidebarItems = document.querySelectorAll('.sidebar-item');
            sidebarItems.forEach(item => {
                let timeout;

                item.addEventListener('mouseenter', function() {
                    clearTimeout(timeout);
                });

                item.addEventListener('mouseleave', function() {
                    timeout = setTimeout(() => {
                        const popup = this.querySelector('.popup');
                        if (popup) {
                            popup.style.opacity = '0';
                            popup.style.visibility = 'hidden';
                        }
                    }, 300); // 300ms delay before hiding
                });
            });
        });
    </script>
</body>
</html>
