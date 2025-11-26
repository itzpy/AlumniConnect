<?php
/**
 * Shopping Cart Page
 * View and manage items in cart before checkout
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_email = $_SESSION['email'] ?? '';
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/cart_class.php');
$cart = new Cart();

// Get cart summary
$cart_summary = $cart->getCartSummary($user_id);
$cart_items = $cart_summary['items'];
$subtotal = $cart_summary['subtotal'];
$tax_amount = $cart_summary['tax_amount'];
$total = $cart_summary['total'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart - Alumni Connect</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Shopping Cart</h1>
                <p class="text-gray-600">Review your items before checkout</p>
            </div>

            <div id="cartContainer">
            <?php if (!empty($cart_items)): ?>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Cart Items -->
                    <div class="lg:col-span-2 space-y-4">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                                <div class="flex gap-4">
                                    <!-- Image -->
                                    <div class="w-24 h-24 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <?php if ($item['image_url']): ?>
                                            <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($item['service_name']); ?>"
                                                 class="w-full h-full object-cover rounded-lg">
                                        <?php else: ?>
                                            <i class="fas fa-box text-2xl text-primary/30"></i>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Details -->
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start mb-2">
                                            <div>
                                                <h3 class="font-bold text-gray-900 mb-1">
                                                    <?php echo htmlspecialchars($item['service_name']); ?>
                                                </h3>
                                                <span class="inline-block px-2 py-1 text-xs font-medium rounded
                                                    <?php 
                                                        echo $item['service_type'] === 'job_posting' ? 'bg-blue-100 text-blue-800' : 
                                                             ($item['service_type'] === 'mentorship' ? 'bg-green-100 text-green-800' : 
                                                             ($item['service_type'] === 'event' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')); 
                                                    ?>">
                                                    <?php echo ucwords(str_replace('_', ' ', $item['service_type'])); ?>
                                                </span>
                                            </div>
                                            <button onclick="removeFromCart(<?php echo $item['cart_id']; ?>)" 
                                                    class="text-red-500 hover:text-red-700 transition-colors">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>

                                        <p class="text-sm text-gray-600 mb-3">
                                            <?php echo htmlspecialchars(substr($item['description'], 0, 100)) . '...'; ?>
                                        </p>

                                        <!-- Quantity and Price -->
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-3">
                                                <label class="text-sm text-gray-600">Qty:</label>
                                                <div class="flex items-center border border-gray-300 rounded-lg">
                                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] - 1; ?>)" 
                                                            class="px-3 py-1 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-minus text-sm"></i>
                                                    </button>
                                                    <span class="px-4 py-1 border-x border-gray-300">
                                                        <?php echo $item['quantity']; ?>
                                                    </span>
                                                    <button onclick="updateQuantity(<?php echo $item['cart_id']; ?>, <?php echo $item['quantity'] + 1; ?>)" 
                                                            class="px-3 py-1 hover:bg-gray-100 transition-colors">
                                                        <i class="fas fa-plus text-sm"></i>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-sm text-gray-500">GHS <?php echo number_format($item['price'], 2); ?> each</p>
                                                <p class="text-lg font-bold text-primary">
                                                    GHS <?php echo number_format($item['subtotal'], 2); ?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Date/Time if applicable -->
                                        <?php if ($item['selected_date'] || $item['selected_time']): ?>
                                            <div class="mt-3 pt-3 border-t border-gray-200">
                                                <p class="text-sm text-gray-600">
                                                    <i class="fas fa-calendar mr-2"></i>
                                                    <?php echo $item['selected_date'] ? date('M d, Y', strtotime($item['selected_date'])) : ''; ?>
                                                    <?php echo $item['selected_time'] ? 'at ' . date('g:i A', strtotime($item['selected_time'])) : ''; ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Order Summary -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                            <h3 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h3>

                            <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                                <div class="flex justify-between text-gray-600">
                                    <span>Subtotal (<?php echo count($cart_items); ?> items)</span>
                                    <span>GHS <?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <div class="flex justify-between text-gray-600">
                                    <span>Tax</span>
                                    <span>GHS <?php echo number_format($tax_amount, 2); ?></span>
                                </div>
                            </div>

                            <div class="flex justify-between items-center mb-6">
                                <span class="text-lg font-bold text-gray-900">Total</span>
                                <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($total, 2); ?></span>
                            </div>

                            <button onclick="proceedToPayment()" id="checkoutBtn"
                                    class="w-full px-6 py-4 bg-primary text-white font-bold text-lg rounded-lg hover:bg-red-900 hover:shadow-lg active:scale-[0.98] transition-all duration-200 mb-3">
                                <i class="fas fa-lock mr-2"></i>Proceed to Checkout
                            </button>

                            <a href="services.php" 
                               class="block w-full px-6 py-3 border-2 border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 hover:border-gray-400 active:scale-[0.98] transition-all duration-200">
                                <i class="fas fa-shopping-bag mr-2"></i>Continue Shopping
                            </a>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <!-- Empty Cart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center animate-fadeIn">
                    <div class="w-32 h-32 bg-gradient-to-br from-primary/10 to-primary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i class="fas fa-shopping-cart text-5xl text-primary/40"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-3">Your cart is empty</h3>
                    <p class="text-gray-600 mb-8 max-w-md mx-auto">Looks like you haven't added any services yet. Browse our catalog and find what you need!</p>
                    <a href="services.php" 
                       class="inline-block px-8 py-4 bg-primary text-white rounded-lg hover:bg-red-900 hover:shadow-lg active:scale-95 transition-all duration-200 font-semibold">
                        <i class="fas fa-shopping-bag mr-2"></i>Browse Services
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        /**
         * Proceed to payment (skip checkout page, go straight to Paystack)
         */
        function proceedToPayment() {
            const btn = document.getElementById('checkoutBtn');
            const originalHTML = btn.innerHTML;
            
            // Get email from user
            const email = <?php echo json_encode($user_email); ?>;
            const total = <?php echo $total; ?>;
            
            if (!email) {
                alert('Please update your profile with a valid email address');
                return;
            }
            
            // Show loading
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirecting to payment...';
            
            console.log('Initiating payment:', { email, total });
            
            // Initialize Paystack transaction
            fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: total,
                    email: email
                })
            })
            .then(response => {
                console.log('Response status:', response.status);
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                try {
                    const data = JSON.parse(text);
                    console.log('Parsed data:', data);
                    
                    if (data.status === 'success') {
                        console.log('Redirecting to:', data.authorization_url);
                        // Redirect to Paystack payment page
                        window.location.href = data.authorization_url;
                    } else {
                        console.error('Error from server:', data);
                        alert('Error: ' + (data.message || 'Failed to initialize payment'));
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', text);
                    alert('Server error. Please check console and try again.');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Failed to process payment. Please try again.');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        }
        
        /**
         * Update cart item quantity
         */
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                if (!confirm('Remove this item from cart?')) return;
            }
            
            fetch('../actions/update_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId + '&quantity=' + newQuantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to update quantity'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update cart');
            });
        }

        /**
         * Remove item from cart
         */
        function removeFromCart(cartId) {
            if (!confirm('Remove this item from your cart?')) return;
            
            fetch('../actions/remove_from_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert('Error: ' + (data.message || 'Failed to remove item'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to remove item');
            });
        }
    </script>
</body>
</html>
