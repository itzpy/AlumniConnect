<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$order_number = $_GET['order'] ?? null;

if (!$order_number) {
    header("Location: orders.php");
    exit();
}

require_once(dirname(__FILE__).'/../classes/order_class.php');
$order_obj = new Order();
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
    <title>Invoice - <?php echo $order_number; ?></title>
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
    <style>
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-gray-50">
    <div class="max-w-4xl mx-auto p-8">
        <div class="bg-white rounded-xl shadow-lg border border-gray-200 p-8">
            <!-- Header -->
            <div class="flex justify-between items-start mb-8 pb-8 border-b-2 border-primary">
                <div>
                    <h1 class="text-3xl font-bold text-primary mb-2">INVOICE</h1>
                    <p class="text-gray-600">Order #<?php echo htmlspecialchars($order_number); ?></p>
                    <p class="text-sm text-gray-500">Date: <?php echo date('M d, Y', strtotime($order['date_created'] ?? 'now')); ?></p>
                </div>
                <div class="text-right">
                    <h2 class="text-2xl font-bold text-primary">Alumni Connect</h2>
                    <p class="text-sm text-gray-600">Building Bridges, Creating Futures</p>
                    <p class="text-sm text-gray-500 mt-2">www.alumniconnect.com</p>
                </div>
            </div>

            <!-- Billing Info -->
            <div class="mb-8">
                <h3 class="font-bold text-gray-900 mb-2">Bill To:</h3>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['billing_name']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['billing_email']); ?></p>
                <p class="text-gray-600"><?php echo htmlspecialchars($order['billing_phone']); ?></p>
            </div>

            <!-- Order Items -->
            <table class="w-full mb-8">
                <thead>
                    <tr class="border-b-2 border-gray-300">
                        <th class="text-left py-3 font-bold text-gray-900">Item</th>
                        <th class="text-center py-3 font-bold text-gray-900">Qty</th>
                        <th class="text-right py-3 font-bold text-gray-900">Price</th>
                        <th class="text-right py-3 font-bold text-gray-900">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr class="border-b border-gray-200">
                            <td class="py-3"><?php echo htmlspecialchars($item['service_name']); ?></td>
                            <td class="text-center py-3"><?php echo $item['quantity']; ?></td>
                            <td class="text-right py-3">GHS <?php echo number_format($item['unit_price'] ?? 0, 2); ?></td>
                            <td class="text-right py-3">GHS <?php echo number_format($item['total_price'] ?? 0, 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div class="flex justify-end mb-8">
                <div class="w-64 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">GHS <?php echo number_format($order['total_amount'] ?? 0, 2); ?></span>
                    </div>
                    <?php if (($order['discount_amount'] ?? 0) > 0): ?>
                    <div class="flex justify-between text-green-600">
                        <span>Discount:</span>
                        <span class="font-medium">-GHS <?php echo number_format($order['discount_amount'], 2); ?></span>
                    </div>
                    <?php endif; ?>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">GHS <?php echo number_format($order['tax_amount'] ?? 0, 2); ?></span>
                    </div>
                    <div class="flex justify-between text-xl font-bold border-t-2 border-gray-300 pt-2">
                        <span>Total:</span>
                        <span>GHS <?php echo number_format($order['final_amount'] ?? 0, 2); ?></span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-green-600">Payment Status:</span>
                        <span class="font-bold text-green-600"><?php echo strtoupper($order['payment_status'] ?? 'PENDING'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="border-t border-gray-200 pt-6 text-center text-sm text-gray-500">
                <p>Thank you for your business!</p>
                <p class="mt-2">For any questions, please contact us at support@alumniconnect.com</p>
            </div>

            <!-- Action Buttons -->
            <div class="no-print flex gap-4 mt-8">
                <button onclick="window.print()" class="flex-1 px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Invoice
                </button>
                <a href="orders.php" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 text-center rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>
    </div>
</body>
</html>
