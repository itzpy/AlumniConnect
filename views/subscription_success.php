<?php
/**
 * Subscription Success Page
 * Shows confirmation after successful subscription upgrade
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$plan = $_GET['plan'] ?? 'Professional';

require_once(dirname(__FILE__).'/../classes/subscription_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$subscription = new Subscription();
$cart = new Cart();

$current_subscription = $subscription->getUserSubscription($user_id);
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscription Activated - Alumni Connect</title>
    
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
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        
        @keyframes confetti {
            0% { transform: translateY(-100vh) rotate(0deg); }
            100% { transform: translateY(100vh) rotate(720deg); }
        }
        
        .confetti {
            position: fixed;
            width: 10px;
            height: 10px;
            animation: confetti 3s ease-in-out forwards;
        }
        
        @keyframes pulse-ring {
            0% { transform: scale(0.8); opacity: 1; }
            100% { transform: scale(1.5); opacity: 0; }
        }
        
        .pulse-ring {
            animation: pulse-ring 1.5s infinite;
        }
        
        @keyframes check {
            0% { stroke-dashoffset: 80; }
            100% { stroke-dashoffset: 0; }
        }
        
        .check-animation {
            stroke-dasharray: 80;
            stroke-dashoffset: 80;
            animation: check 0.6s ease-out forwards;
            animation-delay: 0.3s;
        }
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-2xl mx-auto">
                <!-- Success Card -->
                <div class="bg-white rounded-2xl shadow-xl p-8 text-center relative overflow-hidden">
                    
                    <!-- Decorative Background -->
                    <div class="absolute top-0 left-0 right-0 h-32 bg-gradient-to-r from-<?php echo strtolower($plan) === 'premium' ? 'amber-400 to-amber-500' : 'primary to-primary-dark'; ?>"></div>
                    
                    <!-- Success Icon -->
                    <div class="relative z-10 mb-6">
                        <div class="w-24 h-24 bg-white rounded-full mx-auto flex items-center justify-center shadow-lg relative">
                            <div class="absolute inset-0 rounded-full bg-green-100 pulse-ring"></div>
                            <svg class="w-12 h-12 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path class="check-animation" stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    
                    <!-- Badge -->
                    <div class="mb-6">
                        <?php if (strtolower($plan) === 'premium'): ?>
                            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-amber-400 to-amber-500 text-white px-6 py-2 rounded-full text-lg font-bold shadow-lg">
                                <i class="fas fa-crown"></i> Premium Member
                            </span>
                        <?php else: ?>
                            <span class="inline-flex items-center gap-2 bg-gradient-to-r from-primary to-primary-dark text-white px-6 py-2 rounded-full text-lg font-bold shadow-lg">
                                <i class="fas fa-star"></i> <?php echo htmlspecialchars($plan); ?> Member
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Success Message -->
                    <h1 class="text-3xl font-bold text-gray-900 mb-3">
                        Welcome to <?php echo htmlspecialchars($plan); ?>!
                    </h1>
                    <p class="text-gray-600 text-lg mb-8">
                        Your subscription has been activated successfully. Enjoy all your new premium features!
                    </p>
                    
                    <!-- Subscription Details -->
                    <?php if ($current_subscription): ?>
                    <div class="bg-gray-50 rounded-xl p-6 mb-8 text-left">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-receipt text-primary"></i> Subscription Details
                        </h3>
                        <div class="space-y-3">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Plan</span>
                                <span class="font-semibold text-gray-900"><?php echo ucfirst($current_subscription['plan_type']); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status</span>
                                <span class="inline-flex items-center gap-1 text-green-600">
                                    <i class="fas fa-check-circle"></i> Active
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Activated</span>
                                <span class="font-medium text-gray-900"><?php echo date('F j, Y', strtotime($current_subscription['created_at'])); ?></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Renews</span>
                                <span class="font-medium text-gray-900"><?php echo date('F j, Y', strtotime($current_subscription['expires_at'])); ?></span>
                            </div>
                            <div class="flex justify-between pt-3 border-t border-gray-200">
                                <span class="text-gray-600">Amount</span>
                                <span class="font-bold text-primary text-lg">GHS <?php echo number_format($current_subscription['amount'], 2); ?>/mo</span>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    
                    <!-- What's Included -->
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 mb-8 text-left">
                        <h3 class="font-semibold text-gray-900 mb-4 flex items-center gap-2">
                            <i class="fas fa-gift text-green-600"></i> Your New Benefits
                        </h3>
                        <ul class="space-y-3">
                            <li class="flex items-start gap-3">
                                <i class="fas fa-check text-green-500 mt-1"></i>
                                <span class="text-gray-700">Unlimited messaging with alumni and students</span>
                            </li>
                            <?php if (strtolower($plan) === 'premium'): ?>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">Unlimited mentorship sessions</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">25% discount on all services</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">VIP access to exclusive events</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">Priority support from our team</span>
                                </li>
                            <?php else: ?>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">5 mentorship sessions per month</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">10% discount on events</span>
                                </li>
                                <li class="flex items-start gap-3">
                                    <i class="fas fa-check text-green-500 mt-1"></i>
                                    <span class="text-gray-700">Early access to job postings</span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-4 justify-center">
                        <a href="dashboard.php" class="px-8 py-3 bg-primary hover:bg-primary-dark text-white font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-home"></i> Go to Dashboard
                        </a>
                        <a href="services.php" class="px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-800 font-semibold rounded-lg transition-colors inline-flex items-center justify-center gap-2">
                            <i class="fas fa-briefcase"></i> Browse Services
                        </a>
                    </div>
                </div>
                
                <!-- Help Card -->
                <div class="bg-white rounded-xl shadow-sm p-6 mt-6 text-center">
                    <p class="text-gray-600 mb-2">Need help with your subscription?</p>
                    <a href="mailto:support@alumniconnect.com" class="text-primary hover:underline font-medium">
                        <i class="fas fa-envelope mr-1"></i> Contact Support
                    </a>
                </div>
            </div>
        </main>
    </div>
    
    <!-- Confetti Animation -->
    <script>
        function createConfetti() {
            const colors = ['#7A1E1E', '#FFD700', '#22C55E', '#3B82F6', '#F59E0B'];
            for (let i = 0; i < 50; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = Math.random() * 100 + 'vw';
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDelay = Math.random() * 2 + 's';
                confetti.style.animationDuration = (Math.random() * 2 + 2) + 's';
                document.body.appendChild(confetti);
                
                setTimeout(() => confetti.remove(), 5000);
            }
        }
        
        // Trigger confetti on load
        window.addEventListener('load', createConfetti);
    </script>
</body>
</html>
