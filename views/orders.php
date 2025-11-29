<?php
/**
 * Orders Page
 * View order history and track orders
 */

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

require_once(dirname(__FILE__).'/../classes/order_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$order = new Order();
$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);

// Get all user orders
$orders = $order->getUserOrders($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders - Alumni Connect</title>
    
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
                <h1 class="text-3xl font-bold text-gray-900 mb-2">My Orders</h1>
                <p class="text-gray-600">Track and manage your orders</p>
            </div>

            <?php if ($orders && count($orders) > 0): ?>
                <div class="space-y-6">
                    <?php foreach ($orders as $order_item): 
                        $order_items = $order->getOrderItems($order_item['order_id']);
                        
                        // Status colors
                        $status_colors = [
                            'pending' => 'bg-yellow-100 text-yellow-800',
                            'processing' => 'bg-blue-100 text-blue-800',
                            'completed' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800'
                        ];
                        
                        $payment_colors = [
                            'pending' => 'bg-gray-100 text-gray-800',
                            'paid' => 'bg-green-100 text-green-800',
                            'failed' => 'bg-red-100 text-red-800'
                        ];
                    ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <!-- Order Header -->
                            <div class="bg-gray-50 p-6 border-b border-gray-200">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div>
                                        <h3 class="text-lg font-bold text-gray-900 mb-1">
                                            Order #<?php echo htmlspecialchars($order_item['order_number']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            <i class="fas fa-calendar mr-1"></i>
                                            <?php echo date('M d, Y g:i A', strtotime($order_item['date_created'])); ?>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo $status_colors[$order_item['order_status']]; ?>">
                                            <?php echo ucfirst($order_item['order_status']); ?>
                                        </span>
                                        <span class="px-3 py-1 text-sm font-medium rounded-full <?php echo $payment_colors[$order_item['payment_status']]; ?>">
                                            <?php echo ucfirst($order_item['payment_status']); ?>
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Order Items -->
                            <div class="p-6">
                                <div class="space-y-3 mb-4">
                                    <?php foreach ($order_items as $item): ?>
                                        <div class="flex items-center gap-4 pb-3 border-b border-gray-100 last:border-0">
                                            <div class="w-12 h-12 bg-gradient-to-br from-primary/20 to-primary/5 rounded-lg flex items-center justify-center flex-shrink-0">
                                                <i class="fas fa-box text-primary/50"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="font-semibold text-gray-900">
                                                    <?php echo htmlspecialchars($item['service_name']); ?>
                                                </h4>
                                                <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?> × GHS <?php echo number_format($item['unit_price'], 2); ?></p>
                                            </div>
                                            <div class="text-right">
                                                <p class="font-bold text-gray-900">GHS <?php echo number_format($item['total_price'], 2); ?></p>
                                                <span class="text-xs px-2 py-1 rounded bg-gray-100 text-gray-700">
                                                    <?php echo ucfirst($item['fulfillment_status']); ?>
                                                </span>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <!-- Order Total -->
                                <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                                    <span class="text-lg font-semibold text-gray-900">Total Amount:</span>
                                    <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($order_item['final_amount'], 2); ?></span>
                                </div>

                                <!-- Actions -->
                                <div class="flex gap-3 mt-4">
                                    <a href="order_details.php?order=<?php echo $order_item['order_number']; ?>" 
                                       class="flex-1 px-4 py-2 text-center border-2 border-primary text-primary rounded-lg hover:bg-primary hover:text-white transition-colors">
                                        <i class="fas fa-eye mr-2"></i>View Details
                                    </a>
                                    <?php if ($order_item['payment_status'] === 'paid'): ?>
                                        <a href="invoice.php?order=<?php echo $order_item['order_number']; ?>" 
                                           class="flex-1 px-4 py-2 text-center bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                            <i class="fas fa-file-invoice mr-2"></i>Download Invoice
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <i class="fas fa-receipt text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No orders yet</h3>
                    <p class="text-gray-600 mb-6">Start shopping to see your orders here</p>
                    <a href="services.php" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="fas fa-shopping-bag mr-2"></i>Browse Services
                    </a>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
