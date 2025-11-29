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
        @keyframes slideIn {
            from { opacity: 0; transform: translateX(100px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .animate-slideIn { animation: slideIn 0.3s ease-out; }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .animate-spin { animation: spin 1s linear infinite; }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }
        .animate-pulse { animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite; }
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <!-- Breadcrumb -->
            <nav class="mb-6 text-sm">
                <ol class="flex items-center space-x-2 text-gray-500">
                    <li><a href="dashboard.php" class="hover:text-primary">Dashboard</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-900 font-medium">Shopping Cart</li>
                </ol>
            </nav>

            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2 flex items-center">
                    <i class="fas fa-shopping-cart text-primary mr-3"></i>
                    Shopping Cart
                </h1>
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
                                        <?php if ($item['image_url']): 
                                            $cart_image_src = (strpos($item['image_url'], 'http') === 0) 
                                                ? $item['image_url'] 
                                                : '../uploads/services/' . $item['image_url'];
                                        ?>
                                            <img src="<?php echo htmlspecialchars($cart_image_src); ?>" 
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
                
                <!-- AI Recommendations: Frequently Bought Together -->
                <div class="mt-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100 p-6">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-magic text-white text-sm"></i>
                        </div>
                        <h3 class="font-bold text-gray-900">You Might Also Like</h3>
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">AI</span>
                    </div>
                    <div id="cart-recommendations" class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <!-- Loading -->
                        <div class="bg-white rounded-lg p-3 animate-pulse">
                            <div class="w-full h-16 bg-gray-200 rounded mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                        </div>
                        <div class="bg-white rounded-lg p-3 animate-pulse hidden md:block">
                            <div class="w-full h-16 bg-gray-200 rounded mb-2"></div>
                            <div class="h-3 bg-gray-200 rounded w-3/4"></div>
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

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        // Load recommendations on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadCartRecommendations();
        });
        
        /**
         * Load AI recommendations for cart
         */
        function loadCartRecommendations() {
            const container = document.getElementById('cart-recommendations');
            if (!container) return;
            
            // Get current cart item IDs
            const cartIds = <?php echo json_encode(array_column($cart_items, 'service_id')); ?>;
            
            fetch('../actions/get_recommendations_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    type: 'frequently_bought', 
                    cart_ids: cartIds,
                    limit: 4 
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.recommendations.length > 0) {
                    container.innerHTML = data.recommendations.map(rec => createCartRecCard(rec)).join('');
                } else {
                    // Fallback to personalized if no frequently bought together
                    fetch('../actions/get_recommendations_action.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ 
                            type: 'personalized', 
                            exclude_ids: cartIds,
                            limit: 4 
                        })
                    })
                    .then(response => response.json())
                    .then(fallbackData => {
                        if (fallbackData.success && fallbackData.recommendations.length > 0) {
                            container.innerHTML = fallbackData.recommendations.map(rec => createCartRecCard(rec)).join('');
                        } else {
                            container.parentElement.style.display = 'none';
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.parentElement.style.display = 'none';
            });
        }
        
        /**
         * Create cart recommendation card
         */
        function createCartRecCard(service) {
            const typeIcons = {
                'mentorship': 'fa-user-graduate',
                'event': 'fa-calendar-alt',
                'job_posting': 'fa-briefcase',
                'premium': 'fa-star'
            };
            const typeColors = {
                'mentorship': 'from-green-400 to-emerald-500',
                'event': 'from-purple-400 to-violet-500',
                'job_posting': 'from-blue-400 to-indigo-500',
                'premium': 'from-amber-400 to-orange-500'
            };
            
            const icon = typeIcons[service.service_type] || 'fa-box';
            const gradient = typeColors[service.service_type] || 'from-gray-400 to-gray-500';
            
            return `
                <div class="bg-white rounded-lg p-3 hover:shadow-md transition-all">
                    <div class="w-full h-16 bg-gradient-to-br ${gradient} rounded-lg mb-2 flex items-center justify-center">
                        <i class="fas ${icon} text-xl text-white/80"></i>
                    </div>
                    <h4 class="font-medium text-gray-900 text-xs line-clamp-2 mb-1">${service.service_name}</h4>
                    <div class="flex items-center justify-between">
                        <span class="text-primary font-bold text-xs">${service.formatted_price}</span>
                        <button onclick="quickAddToCart(${service.service_id})" 
                                class="w-6 h-6 bg-primary text-white rounded-full flex items-center justify-center hover:bg-primary/90 transition-colors text-xs">
                            <i class="fas fa-plus"></i>
                        </button>
                    </div>
                </div>
            `;
        }
        
        /**
         * Quick add to cart from recommendations
         */
        function quickAddToCart(serviceId) {
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'service_id=' + serviceId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Added to cart!', 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to add', 'error');
                }
            })
            .catch(() => showToast('Failed to add to cart', 'error'));
        }
        
        /**
         * Show toast notification
         */
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg animate-slideIn flex items-center gap-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        /**
         * Proceed to payment (skip checkout page, go straight to Paystack)
         */
        function proceedToPayment() {
            const btn = document.getElementById('checkoutBtn');
            const originalHTML = btn.innerHTML;
            
            const email = <?php echo json_encode($user_email); ?>;
            const total = <?php echo $total; ?>;
            
            if (!email) {
                showToast('Please update your profile with a valid email address', 'error');
                return;
            }
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Redirecting to payment...';
            btn.classList.add('animate-pulse');
            
            showToast('Initializing secure payment...', 'info');
            
            fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ amount: total, email: email })
            })
            .then(response => response.text())
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if (data.status === 'success') {
                        showToast('Redirecting to Paystack...', 'success');
                        setTimeout(() => window.location.href = data.authorization_url, 500);
                    } else {
                        showToast(data.message || 'Failed to initialize payment', 'error');
                        btn.disabled = false;
                        btn.innerHTML = originalHTML;
                        btn.classList.remove('animate-pulse');
                    }
                } catch (e) {
                    showToast('Server error. Please try again.', 'error');
                    btn.disabled = false;
                    btn.innerHTML = originalHTML;
                    btn.classList.remove('animate-pulse');
                }
            })
            .catch(error => {
                showToast('Failed to process payment. Please try again.', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                btn.classList.remove('animate-pulse');
            });
        }
        
        /**
         * Update cart item quantity
         */
        function updateQuantity(cartId, newQuantity) {
            if (newQuantity < 1) {
                removeFromCart(cartId);
                return;
            }
            
            showToast('Updating quantity...', 'info');
            
            fetch('../actions/update_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId + '&quantity=' + newQuantity
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Cart updated!', 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to update quantity', 'error');
                }
            })
            .catch(error => {
                showToast('Failed to update cart', 'error');
            });
        }

        /**
         * Remove item from cart
         */
        function removeFromCart(cartId) {
            // Create confirmation modal
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black/50 z-50 flex items-center justify-center animate-fadeIn';
            modal.innerHTML = `
                <div class="bg-white rounded-xl shadow-2xl p-6 max-w-sm mx-4 animate-fadeIn">
                    <div class="text-center mb-4">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-trash text-2xl text-red-500"></i>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900">Remove Item?</h3>
                        <p class="text-gray-600 text-sm mt-2">Are you sure you want to remove this item from your cart?</p>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="this.closest('.fixed').remove()" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium transition-colors">Cancel</button>
                        <button onclick="confirmRemove(${cartId}, this)" class="flex-1 px-4 py-2.5 bg-red-500 text-white rounded-lg hover:bg-red-600 font-medium transition-colors">Remove</button>
                    </div>
                </div>
            `;
            document.body.appendChild(modal);
        }

        function confirmRemove(cartId, btn) {
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            btn.disabled = true;
            
            fetch('../actions/remove_from_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'cart_id=' + cartId
            })
            .then(response => response.json())
            .then(data => {
                btn.closest('.fixed').remove();
                if (data.success) {
                    showToast('Item removed from cart', 'success');
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(data.message || 'Failed to remove item', 'error');
                }
            })
            .catch(error => {
                btn.closest('.fixed').remove();
                showToast('Failed to remove item', 'error');
            });
        }
    </script>
</body>
</html>
