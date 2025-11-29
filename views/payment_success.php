<?php
/**
 * Payment Success Page
 * Confirmation page after successful payment
 */

session_start();
if (!isset($_SESSION['user_id']) || !isset($_GET['order'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';
$order_number = $_GET['order'];

require_once(dirname(__FILE__).'/../classes/order_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$order = new Order();
$cart = new Cart();
$cart_count = $cart->getCartCount($_SESSION['user_id']);

$order_details = $order->getOrderByNumber($order_number);
if (!$order_details || $order_details['user_id'] != $_SESSION['user_id']) {
    header("Location: orders.php");
    exit();
}

$order_items = $order->getOrderItems($order_details['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Successful - Alumni Connect</title>
    
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
        @keyframes checkmark {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.2); }
            100% { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes confetti {
            0% { transform: translateY(0) rotate(0deg); opacity: 1; }
            100% { transform: translateY(-100vh) rotate(720deg); opacity: 0; }
        }
        .animate-checkmark { animation: checkmark 0.6s ease-out 0.2s both; }
        .animate-fadeUp { animation: fadeUp 0.5s ease-out both; }
        .animate-fadeUp-delay-1 { animation-delay: 0.2s; }
        .animate-fadeUp-delay-2 { animation-delay: 0.4s; }
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            animation: confetti 3s ease-out forwards;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Confetti Animation -->
    <div id="confetti-container"></div>

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-3xl mx-auto">
                <!-- Success Message -->
                <div class="bg-white rounded-2xl shadow-lg border border-gray-200 p-8 text-center mb-6 overflow-hidden relative">
                    <!-- Success Icon -->
                    <div class="w-24 h-24 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center mx-auto mb-6 shadow-lg animate-checkmark">
                        <i class="fas fa-check text-5xl text-white"></i>
                    </div>
                    
                    <h1 class="text-3xl font-bold text-gray-900 mb-2 animate-fadeUp">Payment Successful!</h1>
                    <p class="text-gray-600 mb-6 animate-fadeUp animate-fadeUp-delay-1">Thank you for your purchase. Your order has been confirmed.</p>
                    
                    <!-- Order Number Card -->
                    <div class="bg-gradient-to-r from-primary/10 to-primary/5 rounded-xl p-6 mb-6 animate-fadeUp animate-fadeUp-delay-2">
                        <p class="text-sm text-gray-600 mb-2">Order Number</p>
                        <p class="text-3xl font-bold text-primary tracking-wider"><?php echo htmlspecialchars($order_number); ?></p>
                    </div>

                    <div class="flex items-center justify-center gap-2 text-sm text-gray-500 animate-fadeUp animate-fadeUp-delay-2">
                        <i class="fas fa-envelope text-green-500"></i>
                        <span>Confirmation email sent to <strong class="text-gray-700"><?php echo htmlspecialchars($order_details['billing_email']); ?></strong></span>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6 animate-fadeUp" style="animation-delay: 0.5s;">
                    <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-receipt text-primary mr-3"></i>Order Summary
                    </h3>
                    
                    <div class="space-y-4 mb-4">
                        <?php foreach ($order_items as $index => $item): ?>
                            <div class="flex justify-between items-center pb-4 border-b border-gray-100 last:border-0" style="animation: fadeUp 0.4s ease-out <?php echo 0.6 + ($index * 0.1); ?>s both;">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                        <?php 
                                            $service_type = $item['service_type'] ?? 'other';
                                            $icon_class = match($service_type) {
                                                'event' => 'calendar-alt text-purple-500',
                                                'mentorship' => 'user-graduate text-green-500',
                                                'job_posting' => 'briefcase text-blue-500',
                                                default => 'star text-yellow-500'
                                            };
                                        ?>
                                        <i class="fas fa-<?php echo $icon_class; ?> text-lg"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['service_name']); ?></h4>
                                        <p class="text-sm text-gray-500">Qty: <?php echo $item['quantity']; ?></p>
                                    </div>
                                </div>
                                <p class="font-bold text-gray-900">GHS <?php echo number_format($item['total_price'], 2); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t-2 border-gray-200">
                        <span class="text-lg font-bold text-gray-900">Total Paid</span>
                        <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($order_details['final_amount'], 2); ?></span>
                    </div>
                </div>

                <!-- What's Next Section -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 mb-6 border border-blue-100" style="animation: fadeUp 0.4s ease-out 0.8s both;">
                    <h3 class="text-lg font-bold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>What's Next?
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-envelope-open text-blue-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">Check Your Email</p>
                                <p class="text-xs text-gray-600">Order details have been sent</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-calendar-check text-green-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">Mark Your Calendar</p>
                                <p class="text-xs text-gray-600">For events or sessions</p>
                            </div>
                        </div>
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-bell text-purple-600 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900 text-sm">Stay Updated</p>
                                <p class="text-xs text-gray-600">We'll notify you of updates</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-col sm:flex-row gap-4" style="animation: fadeUp 0.4s ease-out 1s both;">
                    <a href="orders.php" class="flex-1 px-6 py-4 bg-primary text-white text-center font-bold rounded-xl hover:bg-red-900 hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-receipt"></i>View All Orders
                    </a>
                    <a href="invoice.php?order=<?php echo $order_number; ?>" class="flex-1 px-6 py-4 border-2 border-gray-300 text-gray-700 text-center font-bold rounded-xl hover:bg-gray-50 hover:border-gray-400 transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-download"></i>Download Invoice
                    </a>
                    <a href="dashboard.php" class="flex-1 px-6 py-4 border-2 border-primary text-primary text-center font-bold rounded-xl hover:bg-primary hover:text-white transition-all duration-200 flex items-center justify-center gap-2">
                        <i class="fas fa-home"></i>Go to Dashboard
                    </a>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Confetti animation
        function createConfetti() {
            const container = document.getElementById('confetti-container');
            const colors = ['#7A1E1E', '#22c55e', '#3b82f6', '#eab308', '#a855f7'];
            
            for (let i = 0; i < 50; i++) {
                setTimeout(() => {
                    const confetti = document.createElement('div');
                    confetti.className = 'confetti';
                    confetti.style.left = Math.random() * 100 + 'vw';
                    confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                    confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '0';
                    confetti.style.animationDuration = (2 + Math.random() * 2) + 's';
                    container.appendChild(confetti);
                    
                    setTimeout(() => confetti.remove(), 5000);
                }, i * 50);
            }
        }
        
        // Run confetti on page load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
