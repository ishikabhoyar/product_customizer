<?php
session_start();
require_once 'includes/Database.php';
require_once 'includes/Cart.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$cart = new Cart();
$cartItems = $cart->getCartItems($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 py-8">
        <h1 class="text-2xl font-bold mb-8">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="text-center py-8">
                <p class="text-gray-500">Your cart is empty</p>
                <a href="design.php" class="mt-4 inline-block bg-blue-600 text-white px-6 py-2 rounded-lg">
                    Start Designing
                </a>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-sm">
                <?php foreach ($cartItems as $item): ?>
                    <div class="border-b p-4 flex items-center">
                        <img src="<?php echo htmlspecialchars($item['design_path']); ?>" 
                             alt="Design Preview" 
                             class="w-20 h-20 object-cover rounded">
                        <div class="ml-4 flex-1">
                            <h3 class="font-medium"><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p class="text-gray-500">Color: <?php echo htmlspecialchars($item['color']); ?></p>
                        </div>
                        <div class="flex items-center">
                            <input type="number" 
                                   value="<?php echo $item['quantity']; ?>" 
                                   min="1" 
                                   class="w-16 px-2 py-1 border rounded">
                            <button class="ml-4 text-red-600 hover:text-red-800">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
                
                <div class="p-4 flex justify-end">
                    <a href="checkout.php" 
                       class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>