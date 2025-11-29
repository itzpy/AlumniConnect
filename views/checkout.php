<?php
/**
 * Checkout Page
 * Final step before payment - review order and enter billing info
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_email = $_SESSION['email'];
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/cart_class.php');
require_once(dirname(__FILE__).'/../classes/order_class.php');
require_once(dirname(__FILE__).'/../classes/coupon_class.php');

$cart = new Cart();
$order = new Order();
$coupon_handler = new Coupon();

// Get cart summary
$cart_summary = $cart->getCartSummary($user_id);

// Check for applied coupon
$applied_coupon = $_SESSION['applied_coupon'] ?? null;
$discount_amount = 0;
if ($applied_coupon) {
    // Re-validate coupon in case something changed
    $validation = $coupon_handler->validateCoupon($applied_coupon['coupon_code'], $user_id, $cart_summary['subtotal']);
    if ($validation['valid']) {
        $discount_amount = $validation['coupon']['discount_amount'];
        $_SESSION['applied_coupon'] = $validation['coupon'];
    } else {
        unset($_SESSION['applied_coupon']);
        $applied_coupon = null;
    }
}
$cart_items = $cart_summary['items'];
$subtotal = $cart_summary['subtotal'];
$tax_amount = $cart_summary['tax_amount'];
$pre_discount_total = $cart_summary['total'];

// Apply discount if coupon is active
$total = $pre_discount_total - $discount_amount;
if ($total < 0) $total = 0;

// Validate cart
$validation = $cart->validateCart($user_id);
if (!$validation['valid']) {
    $_SESSION['checkout_error'] = implode('<br>', $validation['errors']);
    header("Location: cart.php");
    exit();
}

// Redirect if cart is empty
if (empty($cart_items)) {
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Alumni Connect</title>
    
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
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Checkout</h1>
                <p class="text-gray-600">Review your order and complete payment</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Billing Information -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Billing Details Form -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Billing Information</h3>
                        
                        <form id="checkoutForm" class="space-y-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                                    <input type="text" name="billing_name" id="billing_name" 
                                           value="<?php echo htmlspecialchars($user_name); ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Email *</label>
                                    <input type="email" name="billing_email" id="billing_email" 
                                           value="<?php echo htmlspecialchars($user_email); ?>" required
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                                <input type="tel" name="billing_phone" id="billing_phone" 
                                       placeholder="+233 XX XXX XXXX" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                                <textarea name="notes" id="notes" rows="3" 
                                          placeholder="Any special requirements or instructions..."
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary"></textarea>
                            </div>
                        </form>
                    </div>

                    <!-- Order Items -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Order Items</h3>
                        
                        <div class="space-y-4">
                            <?php foreach ($cart_items as $item): ?>
                                <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="w-16 h-16 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <i class="fas fa-<?php 
                                            echo $item['service_type'] === 'job_posting' ? 'briefcase' : 
                                                 ($item['service_type'] === 'mentorship' ? 'user-graduate' : 
                                                 ($item['service_type'] === 'event' ? 'calendar' : 'star')); 
                                        ?> text-xl text-primary/50"></i>
                                    </div>
                                    <div class="flex-1">
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['service_name']); ?></h4>
                                        <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">GHS <?php echo number_format($item['subtotal'], 2); ?></p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary & Payment -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sticky top-24">
                        <h3 class="text-xl font-bold text-gray-900 mb-4">Order Summary</h3>

                        <!-- Coupon Code Input -->
                        <div class="mb-4 pb-4 border-b border-gray-200">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Have a coupon?</label>
                            <div class="flex gap-2">
                                <input type="text" id="coupon_code" 
                                       placeholder="Enter code" 
                                       value="<?php echo $applied_coupon ? htmlspecialchars($applied_coupon['coupon_code']) : ''; ?>"
                                       class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary text-sm <?php echo $applied_coupon ? 'bg-green-50 border-green-400' : ''; ?>"
                                       <?php echo $applied_coupon ? 'readonly' : ''; ?>>
                                <?php if ($applied_coupon): ?>
                                    <button type="button" onclick="removeCoupon()" 
                                            class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                        <i class="fas fa-times"></i>
                                    </button>
                                <?php else: ?>
                                    <button type="button" onclick="applyCoupon()" id="applyCouponBtn"
                                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors text-sm font-medium">
                                        Apply
                                    </button>
                                <?php endif; ?>
                            </div>
                            <div id="couponMessage" class="mt-2 text-sm <?php echo $applied_coupon ? 'text-green-600' : 'hidden'; ?>">
                                <?php if ($applied_coupon): ?>
                                    <i class="fas fa-check-circle mr-1"></i><?php echo htmlspecialchars($applied_coupon['description'] ?? 'Coupon applied!'); ?>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="space-y-3 mb-4 pb-4 border-b border-gray-200">
                            <div class="flex justify-between text-gray-600">
                                <span>Subtotal</span>
                                <span id="displaySubtotal">GHS <?php echo number_format($subtotal, 2); ?></span>
                            </div>
                            <?php if ($discount_amount > 0): ?>
                            <div class="flex justify-between text-green-600" id="discountRow">
                                <span>Discount</span>
                                <span id="displayDiscount">- GHS <?php echo number_format($discount_amount, 2); ?></span>
                            </div>
                            <?php else: ?>
                            <div class="flex justify-between text-green-600 hidden" id="discountRow">
                                <span>Discount</span>
                                <span id="displayDiscount">- GHS 0.00</span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between text-gray-600">
                                <span>Tax</span>
                                <span id="displayTax">GHS <?php echo number_format($tax_amount, 2); ?></span>
                            </div>
                        </div>

                        <div class="flex justify-between items-center mb-6">
                            <span class="text-lg font-bold text-gray-900">Total</span>
                            <span class="text-2xl font-bold text-primary" id="displayTotal">GHS <?php echo number_format($total, 2); ?></span>
                        </div>

                        <!-- Payment Method Info -->
                        <div class="mb-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex items-center gap-3 mb-2">
                                <i class="fas fa-lock text-blue-600"></i>
                                <span class="font-semibold text-blue-900">Secure Payment</span>
                            </div>
                            <p class="text-sm text-blue-800">
                                Powered by Paystack. We accept credit/debit cards and mobile money.
                            </p>
                        </div>

                        <button onclick="processPayment(event)" id="paymentBtn"
                                class="w-full px-6 py-3 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors mb-3">
                            <i class="fas fa-credit-card mr-2"></i>Pay GHS <?php echo number_format($total, 2); ?>
                        </button>

                        <a href="cart.php" 
                           class="block w-full px-6 py-3 border-2 border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Back to Cart
                        </a>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Store current totals
        let currentTotal = <?php echo $total; ?>;
        let currentDiscount = <?php echo $discount_amount; ?>;
        
        /**
         * Apply coupon code
         */
        function applyCoupon() {
            const couponCode = document.getElementById('coupon_code').value.trim();
            if (!couponCode) {
                showCouponMessage('Please enter a coupon code', false);
                return;
            }
            
            const btn = document.getElementById('applyCouponBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';
            
            fetch('../actions/apply_coupon_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ coupon_code: couponCode })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showCouponMessage(data.message, true);
                    updateTotalsDisplay(data.totals);
                    currentDiscount = data.totals.discount;
                    currentTotal = data.totals.total;
                    
                    // Update coupon input
                    document.getElementById('coupon_code').classList.add('bg-green-50', 'border-green-400');
                    document.getElementById('coupon_code').readOnly = true;
                    
                    // Change button to remove
                    btn.outerHTML = `<button type="button" onclick="removeCoupon()" 
                                     class="px-4 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors text-sm font-medium">
                                     <i class="fas fa-times"></i></button>`;
                    
                    // Reload page to update session properly
                    setTimeout(() => location.reload(), 500);
                } else {
                    showCouponMessage(data.message, false);
                    btn.disabled = false;
                    btn.innerHTML = 'Apply';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showCouponMessage('Failed to apply coupon', false);
                btn.disabled = false;
                btn.innerHTML = 'Apply';
            });
        }
        
        /**
         * Remove applied coupon
         */
        function removeCoupon() {
            fetch('../actions/remove_coupon_action.php', {
                method: 'POST'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload to update session and UI
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
        
        /**
         * Show coupon message
         */
        function showCouponMessage(message, isSuccess) {
            const msgDiv = document.getElementById('couponMessage');
            msgDiv.innerHTML = `<i class="fas fa-${isSuccess ? 'check-circle' : 'exclamation-circle'} mr-1"></i>${message}`;
            msgDiv.className = `mt-2 text-sm ${isSuccess ? 'text-green-600' : 'text-red-600'}`;
            msgDiv.classList.remove('hidden');
        }
        
        /**
         * Update totals display
         */
        function updateTotalsDisplay(totals) {
            document.getElementById('displaySubtotal').textContent = 'GHS ' + totals.subtotal.toFixed(2);
            
            const discountRow = document.getElementById('discountRow');
            const discountDisplay = document.getElementById('displayDiscount');
            if (totals.discount > 0) {
                discountRow.classList.remove('hidden');
                discountDisplay.textContent = '- GHS ' + totals.discount.toFixed(2);
            } else {
                discountRow.classList.add('hidden');
            }
            
            document.getElementById('displayTax').textContent = 'GHS ' + totals.tax.toFixed(2);
            document.getElementById('displayTotal').textContent = 'GHS ' + totals.total.toFixed(2);
            
            // Update payment button
            updatePaymentButton(totals.total);
        }
        
        /**
         * Update payment button amount
         */
        function updatePaymentButton(amount) {
            const btn = document.getElementById('paymentBtn');
            btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay GHS ' + amount.toFixed(2);
        }
        
        /**
         * Process payment using Paystack redirect flow
         */
        function processPayment(event) {
            if (event) event.preventDefault();
            
            // Validate form
            const form = document.getElementById('checkoutForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            // Get form data
            const email = document.getElementById('billing_email').value;
            const total = currentTotal;

            console.log('Initiating payment:', { email, total, discount: currentDiscount });
            
            // Show loading state
            const btn = document.getElementById('paymentBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';

            // Initialize Paystack transaction
            fetch('../actions/paystack_init_transaction.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    amount: total,
                    email: email,
                    discount: currentDiscount
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
                        // Store billing info in session storage for later use
                        sessionStorage.setItem('billing_name', document.getElementById('billing_name').value);
                        sessionStorage.setItem('billing_email', email);
                        sessionStorage.setItem('billing_phone', document.getElementById('billing_phone').value);
                        sessionStorage.setItem('billing_notes', document.getElementById('notes').value);
                        sessionStorage.setItem('discount_amount', currentDiscount);
                        
                        console.log('Redirecting to:', data.authorization_url);
                        // Redirect to Paystack payment page
                        window.location.href = data.authorization_url;
                    } else {
                        console.error('Error from server:', data);
                        alert('Error: ' + (data.message || 'Failed to initialize payment'));
                        btn.disabled = false;
                        btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay GHS ' + currentTotal.toFixed(2);
                    }
                } catch (e) {
                    console.error('JSON parse error:', e);
                    console.error('Response was:', text);
                    alert('Server error. Please check console and try again.');
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay GHS ' + currentTotal.toFixed(2);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                alert('Failed to process payment. Please try again.');
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-credit-card mr-2"></i>Pay GHS ' + currentTotal.toFixed(2);
            });
        }
        
        // Allow pressing Enter in coupon field to apply
        document.getElementById('coupon_code').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                if (!this.readOnly) {
                    applyCoupon();
                }
            }
        });
    </script>
</body>
</html>
