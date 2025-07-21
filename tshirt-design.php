<?php
session_start();
require_once 'includes/Database.php';
require_once 'includes/Design.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$design = new Design();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        if (isset($_POST['save_design'])) {
            $designPath = ''; // Initialize variable
            
            // Handle custom design upload
            if (isset($_FILES['custom_design']) && $_FILES['custom_design']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/designs/';
                $fileName = uniqid() . '_' . basename($_FILES['custom_design']['name']);
                $targetPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($_FILES['custom_design']['tmp_name'], $targetPath)) {
                    $designPath = $targetPath;
                }
            }
            
            // Save design to database
            $designId = $design->saveDesign(
                $_SESSION['user_id'],
                'tshirt',
                'Custom T-shirt Design',
                $designPath,
                $_POST['color']
            );
            
            $design_saved = true;
        } elseif (isset($_POST['add_to_cart'])) {
            $cart = new Cart();
            $cart->addItem($_SESSION['user_id'], $designId);
            header("Location: cart.php");
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Default values
$selected_color = isset($_POST['color']) ? $_POST['color'] : 'white';
$selected_design = isset($_POST['design']) ? $_POST['design'] : '';
$custom_design = isset($_FILES['custom_design']) ? $_FILES['custom_design'] : null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>T-shirt Design Studio</title>
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <!-- Add FontAwesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tshirt-container {
            position: relative;
            width: 300px;
            height: 300px;
            margin: 0 auto;
        }
        
        .tshirt-color-layer {
            position: absolute;
            width: 100%;
            height: 100%;
            background-color: <?php echo $selected_color; ?>;
            mask-image: url('tshirt-mask.png');
            -webkit-mask-image: url('tshirt-mask.png');
            mask-repeat: no-repeat;
            -webkit-mask-repeat: no-repeat;
            mask-size: contain;
            -webkit-mask-size: contain;
            mask-position: center;
            -webkit-mask-position: center;
        }
        
        .tshirt-details {
            position: absolute;
            width: 100%;
            height: 100%;
            background-image: url('tshirt-details.png');
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            pointer-events: none;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen p-4">
    <!-- Header -->
    <header class="max-w-7xl mx-auto mb-8">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-slate-700 text-white shadow-md mr-3">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </div>
                <h1 class="text-2xl font-bold text-slate-800">T-shirt Design Studio</h1>
            </div>
            <a href="design.php" class="inline-flex items-center text-slate-600 hover:text-slate-800 font-medium transition-colors">
                <i class="fas fa-arrow-left mr-2"></i> Back to Design Selection
            </a>
        </div>
    </header>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto">
        <!-- Success Message -->
        <?php if(isset($design_saved) && $design_saved): ?>
            <div class="bg-green-50 text-green-800 p-4 rounded-lg mb-6 flex items-center">
                <i class="fas fa-check-circle mr-3 text-green-500"></i>
                <span>Your design has been saved successfully!</span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- T-shirt Preview Section -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
                    <h2 class="text-xl font-bold text-slate-800 mb-4">T-shirt Preview</h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Front View -->
                        <div class="relative bg-gray-50 rounded-lg p-4 text-center">
                            <h3 class="text-sm font-medium text-slate-500 mb-2">Front View</h3>
                            <div class="tshirt-container">
                                <!-- Color Layer -->
                                <div class="tshirt-color-layer" id="front-tshirt"></div>
                                <!-- T-shirt Details (shadows, stitching, etc.) -->
                                <div class="tshirt-details"></div>
                                
                                <!-- Selected Design Overlay -->
                                <?php if(!empty($selected_design)): ?>
                                    <div class="absolute inset-0 flex items-center justify-center p-8">
                                        <img src="designs/<?php echo htmlspecialchars($selected_design); ?>" 
                                            alt="Design" class="max-w-full max-h-full object-contain">
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Back View -->
                        <div class="relative bg-gray-50 rounded-lg p-4 text-center">
                            <h3 class="text-sm font-medium text-slate-500 mb-2">Back View</h3>
                            <div class="tshirt-container">
                                <!-- Color Layer -->
                                <div class="tshirt-color-layer" id="back-tshirt"></div>
                                <!-- T-shirt Details (shadows, stitching, etc.) -->
                                <div class="tshirt-details"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Design Tools Section -->
            <div class="md:col-span-1">
                <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" enctype="multipart/form-data" id="design-form">
                    <!-- Color Selection -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-3">Select Color</h2>
                        
                        <div class="flex flex-wrap gap-3">
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="white" class="sr-only color-option" <?php if($selected_color == 'white') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-white border-2 <?php echo $selected_color == 'white' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="black" class="sr-only color-option" <?php if($selected_color == 'black') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-black border-2 <?php echo $selected_color == 'black' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="#3B82F6" class="sr-only color-option" <?php if($selected_color == '#3B82F6') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-blue-500 border-2 <?php echo $selected_color == '#3B82F6' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="#EF4444" class="sr-only color-option" <?php if($selected_color == '#EF4444') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-red-500 border-2 <?php echo $selected_color == '#EF4444' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="#10B981" class="sr-only color-option" <?php if($selected_color == '#10B981') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-green-500 border-2 <?php echo $selected_color == '#10B981' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="#F59E0B" class="sr-only color-option" <?php if($selected_color == '#F59E0B') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-yellow-500 border-2 <?php echo $selected_color == '#F59E0B' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="color" value="#8B5CF6" class="sr-only color-option" <?php if($selected_color == '#8B5CF6') echo 'checked'; ?>>
                                <span class="block w-8 h-8 rounded-full bg-purple-500 border-2 <?php echo $selected_color == '#8B5CF6' ? 'border-indigo-500' : 'border-gray-300'; ?>"></span>
                            </label>
                        </div>
                    </div>
                    
                    <!-- Design Selection -->
                    <div class="bg-white rounded-xl shadow-md overflow-hidden p-6 mb-6">
                        <h2 class="text-lg font-bold text-slate-800 mb-3">Choose Design</h2>
                        
                        <div class="grid grid-cols-3 gap-3 mb-4">
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design1.png" class="sr-only design-option" <?php if($selected_design == 'design1.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design1.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-mountain text-gray-700"></i>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design2.png" class="sr-only design-option" <?php if($selected_design == 'design2.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design2.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-star text-gray-700"></i>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design3.png" class="sr-only design-option" <?php if($selected_design == 'design3.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design3.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-heart text-gray-700"></i>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design4.png" class="sr-only design-option" <?php if($selected_design == 'design4.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design4.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-bolt text-gray-700"></i>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design5.png" class="sr-only design-option" <?php if($selected_design == 'design5.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design5.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-peace text-gray-700"></i>
                                </div>
                            </label>
                            
                            <label class="cursor-pointer">
                                <input type="radio" name="design" value="design6.png" class="sr-only design-option" <?php if($selected_design == 'design6.png') echo 'checked'; ?>>
                                <div class="border-2 <?php echo $selected_design == 'design6.png' ? 'border-indigo-500' : 'border-gray-200'; ?> rounded-md p-2 h-16 flex items-center justify-center bg-gray-50 hover:bg-gray-100">
                                    <i class="fas fa-music text-gray-700"></i>
                                </div>
                            </label>
                        </div>
                        
                        <!-- Upload Custom Design -->
                        <div class="mt-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload Your Own Design</label>
                            <div class="flex items-center">
                                <label class="w-full flex items-center justify-center px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100">
                                    <input type="file" name="custom_design" class="sr-only" accept="image/*">
                                    <div class="text-center">
                                        <i class="fas fa-upload mb-1 text-gray-500"></i>
                                        <p class="text-sm text-gray-500">Click to upload</p>
                                    </div>
                                </label>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">PNG, JPG up to 2MB</p>
                        </div>
                    </div>
                    
                    <!-- Save Buttons -->
                    <div class="flex space-x-3">
                        <button type="submit" name="save_design" class="flex-1 bg-gradient-to-r from-indigo-500 to-indigo-600 hover:from-indigo-600 hover:to-indigo-700 text-white font-medium py-3 px-6 rounded-lg transition duration-150 shadow-sm flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i> Save Design
                        </button>
                        
                        <button type="submit" name="add_to_cart" class="flex-1 bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white font-medium py-3 px-6 rounded-lg transition duration-150 shadow-sm flex items-center justify-center">
                            <i class="fas fa-shopping-cart mr-2"></i> Add to Cart
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // JavaScript for instant color/design changes without page reload
        document.addEventListener('DOMContentLoaded', function() {
            // Set initial colors
            document.getElementById('front-tshirt').style.backgroundColor = '<?php echo $selected_color; ?>';
            document.getElementById('back-tshirt').style.backgroundColor = '<?php echo $selected_color; ?>';
            
            // Color selection
            const colorOptions = document.querySelectorAll('.color-option');
            colorOptions.forEach(option => {
                option.addEventListener('change', function() {
                    // Update T-shirt color
                    const selectedColor = this.value;
                    document.getElementById('front-tshirt').style.backgroundColor = selectedColor;
                    document.getElementById('back-tshirt').style.backgroundColor = selectedColor;
                    
                    // Update borders on color options
                    colorOptions.forEach(opt => {
                        const span = opt.nextElementSibling;
                        if (opt === this) {
                            span.classList.remove('border-gray-300');
                            span.classList.add('border-indigo-500');
                        } else {
                            span.classList.remove('border-indigo-500');
                            span.classList.add('border-gray-300');
                        }
                    });
                    
                    // Submit form to save the selected color
                    document.getElementById('design-form').submit();
                });
            });
            
            // Design selection
            const designOptions = document.querySelectorAll('.design-option');
            designOptions.forEach(option => {
                option.addEventListener('change', function() {
                    // Update borders on design options
                    designOptions.forEach(opt => {
                        const div = opt.nextElementSibling;
                        if (opt === this) {
                            div.classList.remove('border-gray-200');
                            div.classList.add('border-indigo-500');
                        } else {
                            div.classList.remove('border-indigo-500');
                            div.classList.add('border-gray-200');
                        }
                    });
                    
                    // Submit form to save the selected design
                    document.getElementById('design-form').submit();
                });
            });
        });
    </script>
</body>
</html>
