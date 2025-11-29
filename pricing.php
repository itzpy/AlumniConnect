<?php
/**
 * Pricing / Subscription Plans Page
 * Shows available subscription tiers and features
 * Allows users to select and purchase plans
 */

session_start();
$isLoggedIn = isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
$user_id = $_SESSION['user_id'] ?? null;
$user_type = $_SESSION['user_type'] ?? 'student';
$current_plan = 'free'; // Default

// Get current subscription if logged in
if ($isLoggedIn && $user_id) {
    require_once(dirname(__FILE__).'/classes/subscription_class.php');
    $subscription = new Subscription();
    $user_subscription = $subscription->getUserSubscription($user_id);
    if ($user_subscription) {
        $current_plan = $user_subscription['plan_type'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pricing & Plans - Alumni Connect</title>
    
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
        .plan-card:hover { transform: translateY(-8px); }
        .popular-badge { 
            position: absolute; 
            top: -12px; 
            left: 50%; 
            transform: translateX(-50%); 
        }
    </style>
</head>
<body class="bg-gray-50">

    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <a href="index.php" class="flex items-center space-x-2 text-xl font-bold text-primary">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Alumni Connect</span>
                </a>
                <ul class="hidden md:flex items-center space-x-8">
                    <?php if ($isLoggedIn): ?>
                        <li><a href="views/dashboard.php" class="text-gray-700 hover:text-primary transition-colors">Dashboard</a></li>
                        <li><a href="views/services.php" class="text-gray-700 hover:text-primary transition-colors">Services</a></li>
                        <li><a href="pricing.php" class="text-primary font-semibold">Pricing</a></li>
                        <li><a href="login/logout.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php#features" class="text-gray-700 hover:text-primary transition-colors">Features</a></li>
                        <li><a href="pricing.php" class="text-primary font-semibold">Pricing</a></li>
                        <li><a href="index.php#about" class="text-gray-700 hover:text-primary transition-colors">About</a></li>
                        <li><a href="login/login.php" class="border-2 border-primary text-primary px-4 py-2 rounded-lg hover:bg-primary hover:text-white transition-colors">Login</a></li>
                        <li><a href="login/register.php" class="bg-primary text-white px-4 py-2 rounded-lg hover:bg-primary-dark transition-colors">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-primary-dark to-primary text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h1 class="text-4xl md:text-5xl font-bold mb-4">Simple, Transparent Pricing</h1>
            <p class="text-xl text-gray-100 max-w-2xl mx-auto">Choose the plan that fits your needs. Upgrade anytime to unlock premium features.</p>
        </div>
    </section>

    <!-- Pricing Cards -->
    <section class="py-16 -mt-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                
                <!-- Free Plan -->
                <div class="bg-white rounded-2xl shadow-lg p-8 plan-card transition-all duration-300 border-2 border-gray-100 <?php echo $current_plan === 'free' ? 'ring-2 ring-primary' : ''; ?>">
                    <?php if ($current_plan === 'free'): ?>
                        <div class="popular-badge">
                            <span class="bg-gray-600 text-white text-xs font-bold px-3 py-1 rounded-full">CURRENT PLAN</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-user text-2xl text-gray-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Free</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">GHS 0</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <p class="text-gray-600">Perfect for getting started</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Basic alumni directory access
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            View job postings
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Browse events
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            5 messages per month
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-times text-gray-300 mr-3"></i>
                            <span class="line-through">Mentorship access</span>
                        </li>
                        <li class="flex items-center text-gray-400">
                            <i class="fas fa-times text-gray-300 mr-3"></i>
                            <span class="line-through">Priority support</span>
                        </li>
                    </ul>
                    
                    <?php if (!$isLoggedIn): ?>
                        <a href="login/register.php" class="block w-full py-3 px-6 text-center border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Get Started Free
                        </a>
                    <?php elseif ($current_plan === 'free'): ?>
                        <button disabled class="w-full py-3 px-6 bg-gray-100 text-gray-500 font-semibold rounded-lg cursor-not-allowed">
                            Current Plan
                        </button>
                    <?php else: ?>
                        <button onclick="changePlan('free')" class="w-full py-3 px-6 border-2 border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition-colors">
                            Downgrade to Free
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Professional Plan (Most Popular) -->
                <div class="bg-white rounded-2xl shadow-xl p-8 plan-card transition-all duration-300 border-2 border-primary relative <?php echo $current_plan === 'professional' ? 'ring-4 ring-primary/30' : ''; ?>">
                    <div class="popular-badge">
                        <span class="bg-primary text-white text-xs font-bold px-4 py-1.5 rounded-full shadow-lg">
                            <?php echo $current_plan === 'professional' ? 'CURRENT PLAN' : 'MOST POPULAR'; ?>
                        </span>
                    </div>
                    
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-star text-2xl text-primary"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Professional</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">GHS 49</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <p class="text-gray-600">Best for active networking</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <strong>Everything in Free, plus:</strong>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Unlimited messaging
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Book mentorship sessions
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
                            Profile badge & visibility boost
                        </li>
                    </ul>
                    
                    <?php if (!$isLoggedIn): ?>
                        <a href="login/register.php?plan=professional" class="block w-full py-3 px-6 text-center bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                            Get Started
                        </a>
                    <?php elseif ($current_plan === 'professional'): ?>
                        <button disabled class="w-full py-3 px-6 bg-primary/20 text-primary font-semibold rounded-lg cursor-not-allowed">
                            Current Plan
                        </button>
                    <?php else: ?>
                        <button onclick="selectPlan('professional', 49)" class="w-full py-3 px-6 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                            <?php echo $current_plan === 'premium' ? 'Switch Plan' : 'Upgrade Now'; ?>
                        </button>
                    <?php endif; ?>
                </div>

                <!-- Premium Plan -->
                <div class="bg-white rounded-2xl shadow-lg p-8 plan-card transition-all duration-300 border-2 border-gray-100 <?php echo $current_plan === 'premium' ? 'ring-2 ring-amber-500' : ''; ?>">
                    <?php if ($current_plan === 'premium'): ?>
                        <div class="popular-badge">
                            <span class="bg-amber-500 text-white text-xs font-bold px-3 py-1 rounded-full">CURRENT PLAN</span>
                        </div>
                    <?php endif; ?>
                    
                    <div class="text-center mb-8">
                        <div class="w-16 h-16 bg-amber-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-crown text-2xl text-amber-500"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Premium</h3>
                        <div class="mb-4">
                            <span class="text-4xl font-bold text-gray-900">GHS 99</span>
                            <span class="text-gray-500">/month</span>
                        </div>
                        <p class="text-gray-600">For power networkers</p>
                    </div>
                    
                    <ul class="space-y-4 mb-8">
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            <strong>Everything in Professional, plus:</strong>
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Unlimited mentorship bookings
                        </li>
                        <li class="flex items-center text-gray-700">
                            <i class="fas fa-check text-green-500 mr-3"></i>
                            Post unlimited job listings
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
                            Priority support & dedicated manager
                        </li>
                    </ul>
                    
                    <?php if (!$isLoggedIn): ?>
                        <a href="login/register.php?plan=premium" class="block w-full py-3 px-6 text-center bg-amber-500 text-white font-semibold rounded-lg hover:bg-amber-600 transition-colors">
                            Get Started
                        </a>
                    <?php elseif ($current_plan === 'premium'): ?>
                        <button disabled class="w-full py-3 px-6 bg-amber-100 text-amber-700 font-semibold rounded-lg cursor-not-allowed">
                            Current Plan
                        </button>
                    <?php else: ?>
                        <button onclick="selectPlan('premium', 99)" class="w-full py-3 px-6 bg-amber-500 text-white font-semibold rounded-lg hover:bg-amber-600 transition-colors">
                            Upgrade to Premium
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Comparison Table -->
    <section class="py-16 bg-white">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Compare All Features</h2>
            
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b-2 border-gray-200">
                            <th class="text-left py-4 px-4 font-semibold text-gray-900">Feature</th>
                            <th class="text-center py-4 px-4 font-semibold text-gray-900">Free</th>
                            <th class="text-center py-4 px-4 font-semibold text-primary">Professional</th>
                            <th class="text-center py-4 px-4 font-semibold text-amber-600">Premium</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Alumni Directory Access</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Monthly Messages</td>
                            <td class="py-4 px-4 text-center text-gray-600">5</td>
                            <td class="py-4 px-4 text-center text-primary font-semibold">Unlimited</td>
                            <td class="py-4 px-4 text-center text-amber-600 font-semibold">Unlimited</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">View Job Postings</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Early Job Access</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Post Job Listings</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center text-gray-600">3/month</td>
                            <td class="py-4 px-4 text-center text-amber-600 font-semibold">Unlimited</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Mentorship Sessions</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center text-gray-600">5/month</td>
                            <td class="py-4 px-4 text-center text-amber-600 font-semibold">Unlimited</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Event Discounts</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center text-primary font-semibold">10%</td>
                            <td class="py-4 px-4 text-center text-amber-600 font-semibold">25%</td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">Service Discounts</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center text-amber-600 font-semibold">25%</td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Profile Badge</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-star text-primary"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-crown text-amber-500"></i></td>
                        </tr>
                        <tr class="bg-gray-50">
                            <td class="py-4 px-4 text-gray-700">VIP Event Access</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                        <tr>
                            <td class="py-4 px-4 text-gray-700">Priority Support</td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-times text-gray-300"></i></td>
                            <td class="py-4 px-4 text-center"><i class="fas fa-check text-green-500"></i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-center text-gray-900 mb-12">Frequently Asked Questions</h2>
            
            <div class="space-y-4">
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center" onclick="toggleFaq(this)">
                        <span class="font-semibold text-gray-900">Can I cancel my subscription anytime?</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                    </button>
                    <div class="px-6 pb-4 hidden">
                        <p class="text-gray-600">Yes! You can cancel your subscription at any time. Your premium features will remain active until the end of your current billing period.</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center" onclick="toggleFaq(this)">
                        <span class="font-semibold text-gray-900">What payment methods do you accept?</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                    </button>
                    <div class="px-6 pb-4 hidden">
                        <p class="text-gray-600">We accept all major credit/debit cards and mobile money (MTN, Vodafone, AirtelTigo) through our secure Paystack payment gateway.</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center" onclick="toggleFaq(this)">
                        <span class="font-semibold text-gray-900">Can I switch plans?</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                    </button>
                    <div class="px-6 pb-4 hidden">
                        <p class="text-gray-600">Absolutely! You can upgrade or downgrade your plan at any time. When upgrading, you'll be charged the prorated difference. When downgrading, the change takes effect at the next billing cycle.</p>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <button class="w-full px-6 py-4 text-left flex justify-between items-center" onclick="toggleFaq(this)">
                        <span class="font-semibold text-gray-900">Is there a student discount?</span>
                        <i class="fas fa-chevron-down text-gray-500 transition-transform"></i>
                    </button>
                    <div class="px-6 pb-4 hidden">
                        <p class="text-gray-600">Yes! Current students get 50% off all paid plans. Simply verify your student status during registration to receive your discount automatically.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-gradient-to-r from-primary-dark to-primary text-white py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Ready to Unlock Your Full Potential?</h2>
            <p class="text-xl mb-8 text-gray-100">Join thousands of alumni already benefiting from premium features</p>
            <?php if (!$isLoggedIn): ?>
                <a href="login/register.php" class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                    Start Your Free Trial
                </a>
            <?php else: ?>
                <a href="views/dashboard.php" class="inline-block bg-white text-primary px-8 py-4 rounded-lg font-semibold text-lg hover:bg-gray-100 transition-colors">
                    Go to Dashboard
                </a>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center space-x-2 text-xl font-bold mb-4 md:mb-0">
                    <i class="fas fa-graduation-cap"></i>
                    <span>Alumni Connect</span>
                </div>
                <div class="flex space-x-6 text-gray-400">
                    <a href="#" class="hover:text-white transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-white transition-colors">Terms of Service</a>
                    <a href="#" class="hover:text-white transition-colors">Contact</a>
                </div>
            </div>
            <div class="mt-6 pt-6 border-t border-gray-800 text-center text-gray-400">
                <p>&copy; 2025 Alumni Connect. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script>
        function toggleFaq(button) {
            const content = button.nextElementSibling;
            const icon = button.querySelector('i');
            
            content.classList.toggle('hidden');
            icon.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
        }
        
        function selectPlan(plan, price) {
            <?php if (!$isLoggedIn): ?>
                window.location.href = 'login/register.php?plan=' + plan;
            <?php else: ?>
                // Redirect to subscription checkout
                if (confirm(`Upgrade to ${plan.charAt(0).toUpperCase() + plan.slice(1)} plan for GHS ${price}/month?`)) {
                    window.location.href = 'views/subscribe.php?plan=' + plan;
                }
            <?php endif; ?>
        }
        
        function changePlan(plan) {
            if (confirm('Are you sure you want to change your plan?')) {
                window.location.href = 'views/subscribe.php?plan=' + plan;
            }
        }
    </script>
</body>
</html>
