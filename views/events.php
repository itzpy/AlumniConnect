<?php
/**
 * Events & Tickets Page
 * E-commerce style - Add tickets to cart and checkout
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/service_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$service = new Service();
$cart = new Cart();

// Get only events
$events = $service->getAllServices('event', null, null, null, null);
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Events & Tickets - Alumni Connect</title>
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
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <div class="flex items-center justify-between flex-wrap gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">
                            <i class="fas fa-calendar-alt text-primary mr-3"></i>Events & Tickets
                        </h1>
                        <p class="text-gray-600">Purchase tickets for upcoming alumni events, career fairs, and networking sessions</p>
                    </div>
                    <a href="cart.php" class="relative px-6 py-3 bg-primary text-white rounded-lg hover:bg-red-900 transition-colors">
                        <i class="fas fa-shopping-cart mr-2"></i>View Cart
                        <?php if ($cart_count > 0): ?>
                            <span class="absolute -top-2 -right-2 bg-red-500 text-white text-xs rounded-full w-6 h-6 flex items-center justify-center">
                                <?php echo $cart_count; ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>

            <!-- Events Grid -->
            <?php if ($events && count($events) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($events as $event): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 animate-fadeIn">
                            <!-- Event Image -->
                            <div class="h-48 bg-gradient-to-br from-purple-600 to-purple-800 flex items-center justify-center relative">
                                <?php if ($event['image_url']): 
                                    $event_img_src = (strpos($event['image_url'], 'http') === 0) 
                                        ? $event['image_url'] 
                                        : '../uploads/services/' . $event['image_url'];
                                ?>
                                    <img src="<?php echo htmlspecialchars($event_img_src); ?>" 
                                         alt="<?php echo htmlspecialchars($event['service_name']); ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-calendar-day text-6xl text-white/50"></i>
                                <?php endif; ?>
                                
                                <!-- Date Badge -->
                                <div class="absolute top-4 left-4 bg-white rounded-lg p-2 text-center shadow-lg">
                                    <div class="text-xs font-bold text-primary uppercase">DEC</div>
                                    <div class="text-2xl font-bold text-gray-900">15</div>
                                </div>
                                
                                <!-- Stock Badge -->
                                <?php if ($event['stock_quantity'] !== null): ?>
                                    <div class="absolute top-4 right-4 px-3 py-1 rounded-full text-xs font-bold
                                        <?php echo $event['stock_quantity'] > 20 ? 'bg-green-500 text-white' : 
                                                    ($event['stock_quantity'] > 0 ? 'bg-orange-500 text-white' : 'bg-red-500 text-white'); ?>">
                                        <?php if ($event['stock_quantity'] > 0): ?>
                                            <?php echo $event['stock_quantity']; ?> left
                                        <?php else: ?>
                                            Sold Out
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="p-6">
                                <!-- Event Type Badge -->
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full bg-purple-100 text-purple-800 mb-3">
                                    <i class="fas fa-ticket-alt mr-1"></i> Event Ticket
                                </span>

                                <!-- Event Name -->
                                <h3 class="text-xl font-bold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($event['service_name']); ?>
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-4">
                                    <?php echo htmlspecialchars(substr($event['description'], 0, 100)) . '...'; ?>
                                </p>

                                <!-- Event Details -->
                                <div class="space-y-2 mb-4 text-sm text-gray-500">
                                    <?php if ($event['location']): ?>
                                        <div class="flex items-center">
                                            <i class="fas fa-map-marker-alt w-5 text-primary"></i>
                                            <span><?php echo htmlspecialchars($event['location']); ?></span>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex items-center">
                                        <i class="fas fa-clock w-5 text-primary"></i>
                                        <span>6:00 PM - 10:00 PM</span>
                                    </div>
                                </div>

                                <!-- Price and Action -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div>
                                        <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($event['price'], 2); ?></span>
                                        <span class="text-sm text-gray-500">/ticket</span>
                                    </div>
                                    
                                    <?php if ($event['stock_quantity'] === null || $event['stock_quantity'] > 0): ?>
                                        <button onclick="addToCart(<?php echo $event['service_id']; ?>)" 
                                                class="px-5 py-2.5 bg-primary text-white rounded-lg hover:bg-red-900 hover:shadow-md active:scale-95 transition-all duration-200 font-medium">
                                            <i class="fas fa-cart-plus mr-2"></i>Buy Ticket
                                        </button>
                                    <?php else: ?>
                                        <button disabled class="px-5 py-2.5 bg-gray-300 text-gray-500 rounded-lg cursor-not-allowed font-medium">
                                            Sold Out
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Events -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <div class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-purple-100 to-purple-50 rounded-full flex items-center justify-center">
                        <i class="fas fa-calendar-times text-4xl text-purple-400"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No Upcoming Events</h3>
                    <p class="text-gray-600 mb-6">Check back later for new events and networking opportunities</p>
                </div>
            <?php endif; ?>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50"></div>

    <script>
        function addToCart(serviceId) {
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'service_id=' + serviceId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Ticket added to cart!', 'success');
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to add to cart', 'error');
            });
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg animate-fadeIn mb-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }
    </script>
</body>
</html>
