<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

$order_number = $_GET['order'] ?? null;

if (!$order_number) {
    header("Location: orders.php");
    exit();
}

require_once(dirname(__FILE__).'/../classes/order_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$order_obj = new Order();
$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);

$order = $order_obj->getOrderByNumber($order_number);

if (!$order || $order['user_id'] != $user_id) {
    header("Location: orders.php");
    exit();
}

$order_items = $order_obj->getOrderItems($order['order_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
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
                <a href="orders.php" class="text-primary hover:underline mb-4 inline-block">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Order Details</h1>
                <p class="text-gray-600">Order #<?php echo htmlspecialchars($order_number); ?></p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Order Items -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Order Items</h2>
                        <div class="space-y-4">
                            <?php foreach ($order_items as $item): ?>
                                <div class="flex gap-4 pb-4 border-b border-gray-200 last:border-0">
                                    <div class="flex-1">
                                        <h3 class="font-bold text-gray-900">
                                            <?php echo htmlspecialchars($item['service_name']); ?>
                                        </h3>
                                        <p class="text-sm text-gray-600">
                                            Quantity: <?php echo $item['quantity']; ?>
                                        </p>
                                        <p class="text-sm text-gray-600">
                                            Price: GHS <?php echo number_format($item['unit_price'] ?? $item['price'] ?? 0, 2); ?>
                                        </p>
                                    </div>
                                    <div class="text-right">
                                        <p class="font-bold text-gray-900">
                                            GHS <?php echo number_format($item['total_price'] ?? (($item['unit_price'] ?? $item['price'] ?? 0) * $item['quantity']), 2); ?>
                                        </p>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 space-y-4">
                        <h2 class="text-xl font-bold text-gray-900">Order Summary</h2>
                        
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Status:</span>
                                <span class="font-medium px-2 py-1 rounded text-xs
                                    <?php 
                                        echo $order['order_status'] === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                             ($order['order_status'] === 'processing' ? 'bg-blue-100 text-blue-800' : 
                                             ($order['order_status'] === 'completed' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800')); 
                                    ?>">
                                    <?php echo ucfirst($order['order_status']); ?>
                                </span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600">Payment:</span>
                                <span class="font-medium px-2 py-1 rounded text-xs
                                    <?php echo $order['payment_status'] === 'paid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <?php echo ucfirst($order['payment_status']); ?>
                                </span>
                            </div>
                            <div class="flex justify-between pt-2 border-t">
                                <span class="text-gray-600">Date:</span>
                                <span class="font-medium"><?php echo date('M d, Y', strtotime($order['date_created'] ?? $order['order_date'] ?? 'now')); ?></span>
                            </div>
                        </div>

                        <div class="border-t border-gray-200 pt-4 space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Subtotal:</span>
                                <span>GHS <?php echo number_format($order['total_amount'] ?? 0, 2); ?></span>
                            </div>
                            <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                            <div class="flex justify-between text-sm text-green-600">
                                <span>Discount:</span>
                                <span>-GHS <?php echo number_format($order['discount_amount'], 2); ?></span>
                            </div>
                            <?php endif; ?>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Tax:</span>
                                <span>GHS <?php echo number_format($order['tax_amount'] ?? 0, 2); ?></span>
                            </div>
                            <div class="flex justify-between text-lg font-bold border-t pt-2">
                                <span>Total:</span>
                                <span class="text-primary">GHS <?php echo number_format($order['final_amount'] ?? 0, 2); ?></span>
                            </div>
                        </div>

                        <?php if ($order['payment_status'] === 'paid'): ?>
                            <a href="invoice.php?order=<?php echo $order_number; ?>" 
                               class="block w-full text-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                                <i class="fas fa-download mr-2"></i>Download Invoice
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
