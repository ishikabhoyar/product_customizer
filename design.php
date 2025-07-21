<?php
session_start();

// Check if user is logged in, redirect if not
// if (!isset($_SESSION['user_id'])) {
//     header("Location: login.php");
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Design</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-5xl">
        <!-- Header -->
        <div class="text-center mb-10">
            <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-slate-700 text-white mb-4 shadow-md">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
            </div>
            <h1 class="text-3xl font-bold text-slate-800">Create New Design</h1>
            <p class="text-slate-500 mt-2">Select a product to design</p>
        </div>

        <!-- Design Type Tiles -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- T-shirt Design Tile -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-slate-100">
                <div class="h-40 bg-gradient-to-r from-indigo-500 to-indigo-600 flex items-center justify-center relative">
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.2),rgba(255,255,255,0))]"></div>
                    <i class="fas fa-tshirt text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">T-shirt Design</h3>
                    <p class="text-slate-500 mb-4">Create custom graphics for t-shirts with our easy-to-use design tools</p>
                    <a href="tshirt-design.php" class="block w-full bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 shadow-sm">
                        Start Designing
                    </a>
                </div>
            </div>

            <!-- Watch Design Tile -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-slate-100">
                <div class="h-40 bg-gradient-to-r from-emerald-500 to-emerald-600 flex items-center justify-center relative">
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.2),rgba(255,255,255,0))]"></div>
                    <i class="fas fa-clock text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Watch Design</h3>
                    <p class="text-slate-500 mb-4">Create elegant custom watch faces with precision and detail</p>
                    <a href="watch-design.php" class="block w-full bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 shadow-sm">
                        Start Designing
                    </a>
                </div>
            </div>

            <!-- Phone Cover Design Tile -->
            <div class="bg-white rounded-xl shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 border border-slate-100">
                <div class="h-40 bg-gradient-to-r from-amber-500 to-amber-600 flex items-center justify-center relative">
                    <div class="absolute inset-0 opacity-20 bg-[radial-gradient(circle_at_center,rgba(255,255,255,0.2),rgba(255,255,255,0))]"></div>
                    <i class="fas fa-mobile-alt text-white text-6xl"></i>
                </div>
                <div class="p-6">
                    <h3 class="text-xl font-bold text-slate-800 mb-2">Phone Cover Design</h3>
                    <p class="text-slate-500 mb-4">Design stylish and protective cases for various phone models</p>
                    <a href="phone-design.php" class="block w-full bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white font-medium py-3 px-4 rounded-lg text-center transition duration-150 shadow-sm">
                        Start Designing
                    </a>
                </div>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-8 text-center">
            <a href="dashboard.php" class="inline-flex items-center text-slate-600 hover:text-slate-800 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>
</body>
</html>