<?php
/**
 * Subscription Checkout Page
 * Allows users to subscribe to paid plans
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

// Get requested plan
$requested_plan = $_GET['plan'] ?? 'professional';
if (!in_array($requested_plan, ['professional', 'premium'])) {
    $requested_plan = 'professional';
}

// Get current subscription
require_once(dirname(__FILE__).'/../classes/subscription_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');
$subscription = new Subscription();
$cart = new Cart();

$current_subscription = $subscription->getUserSubscription($user_id);
$current_plan = $current_subscription ? $current_subscription['plan_type'] : 'free';
$plan_details = $subscription->getPlanDetails($requested_plan);

// Cart count for navbar
$cart_count = $cart->getCartCount($user_id);

// Plan prices
$plan_prices = [
    'professional' => 49,
    'premium' => 99
];

$price = $plan_prices[$requested_plan];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe to <?php echo ucfirst($requested_plan); ?> - Alumni Connect</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                        'primary-dark': '#5a1616',
                    }
                }
            }
        }
    </script>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://js.paystack.co/v1/inline.js"></script>
    
    <style>
        body { font-family: 'Inter', sans-serif; }
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
                    <li><a href="../pricing.php" class="hover:text-primary">Pricing</a></li>
                    <li><i class="fas fa-chevron-right text-xs"></i></li>
                    <li class="text-gray-900 font-medium">Subscribe</li>
                </ol>
            </nav>

            <div class="max-w-2xl mx-auto">
                <!-- Header -->
                <div class="text-center mb-8">
                    <div class="w-20 h-20 bg-<?php echo $requested_plan === 'premium' ? 'amber' : 'primary'; ?>-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-<?php echo $requested_plan === 'premium' ? 'crown text-amber-500' : 'star text-primary'; ?> text-3xl"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        Upgrade to <?php echo ucfirst($requested_plan); ?>
                    </h1>
                    <p class="text-gray-600">Unlock premium features and supercharge your networking</p>
                </div>

                <!-- Plan Summary Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <div class="flex items-center justify-between mb-6 pb-6 border-b border-gray-200">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900"><?php echo ucfirst($requested_plan); ?> Plan</h2>
                            <p class="text-gray-500">Monthly subscription</p>
                        </div>
                        <div class="text-right">
                            <span class="text-3xl font-bold text-gray-900">GHS <?php echo $price; ?></span>
                            <span class="text-gray-500">/month</span>
                        </div>
                    </div>

                    <!-- Features included -->
                    <div class="mb-6">
                        <h3 class="font-semibold text-gray-900 mb-3">What's included:</h3>
                        <ul class="space-y-2">
                            <?php if ($requested_plan === 'professional'): ?>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Unlimited messaging
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    5 mentorship sessions per month
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Early access to job postings
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    10% discount on events
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <i class="fas fa-star text-primary mr-1"></i> Profile badge
                                </li>
                            <?php else: ?>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Unlimited messaging
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Unlimited mentorship sessions
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Unlimited job postings
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    25% discount on all services
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    VIP event access
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    Priority support
                                </li>
                                <li class="flex items-center text-gray-700">
                                    <i class="fas fa-check text-green-500 mr-3"></i>
                                    <i class="fas fa-crown text-amber-500 mr-1"></i> Premium badge
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>

                    <!-- Billing info -->
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Plan price</span>
                            <span class="text-gray-900">GHS <?php echo number_format($price, 2); ?></span>
                        </div>
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-gray-600">Billing cycle</span>
                            <span class="text-gray-900">Monthly</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-200">
                            <span class="font-semibold text-gray-900">Total today</span>
                            <span class="text-xl font-bold text-primary">GHS <?php echo number_format($price, 2); ?></span>
                        </div>
                    </div>

                    <!-- Payment Button -->
                    <button onclick="initiatePayment()" id="payBtn"
                            class="w-full py-4 bg-<?php echo $requested_plan === 'premium' ? 'amber-500 hover:bg-amber-600' : 'primary hover:bg-primary-dark'; ?> text-white font-bold text-lg rounded-lg transition-colors">
                        <i class="fas fa-lock mr-2"></i>Subscribe Now - GHS <?php echo $price; ?>/month
                    </button>

                    <p class="text-center text-sm text-gray-500 mt-4">
                        <i class="fas fa-shield-alt mr-1"></i>
                        Secure payment powered by Paystack. Cancel anytime.
                    </p>
                </div>

                <!-- Switch plans -->
                <div class="text-center">
                    <p class="text-gray-600 mb-2">Looking for a different plan?</p>
                    <div class="flex justify-center gap-4">
                        <?php if ($requested_plan !== 'professional'): ?>
                            <a href="subscribe.php?plan=professional" class="text-primary hover:underline">
                                Professional (GHS 49/mo)
                            </a>
                        <?php endif; ?>
                        <?php if ($requested_plan !== 'premium'): ?>
                            <a href="subscribe.php?plan=premium" class="text-amber-600 hover:underline">
                                Premium (GHS 99/mo)
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg flex items-center gap-2 ${type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        function initiatePayment() {
            const btn = document.getElementById('payBtn');
            const originalHTML = btn.innerHTML;
            
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Initializing payment...';
            
            // Initialize Paystack payment
            fetch('../actions/subscription_payment_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    plan: '<?php echo $requested_plan; ?>',
                    email: '<?php echo $user_email; ?>',
                    amount: <?php echo $price; ?>
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.authorization_url) {
                    showToast('Redirecting to payment...', 'info');
                    setTimeout(() => {
                        window.location.href = data.authorization_url;
                    }, 1000);
                } else {
                    throw new Error(data.message || 'Failed to initialize payment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast(error.message || 'Payment initialization failed', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            });
        }
    </script>
</body>
</html>
