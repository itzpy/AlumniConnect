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
$order = new Order();

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
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="max-w-3xl mx-auto">
                <!-- Success Message -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8 text-center mb-6">
                    <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-4xl text-green-600"></i>
                    </div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Payment Successful!</h1>
                    <p class="text-gray-600 mb-6">Thank you for your purchase. Your order has been confirmed.</p>
                    
                    <div class="bg-gray-50 rounded-lg p-4 mb-6">
                        <p class="text-sm text-gray-600 mb-1">Order Number</p>
                        <p class="text-2xl font-bold text-primary"><?php echo htmlspecialchars($order_number); ?></p>
                    </div>

                    <p class="text-sm text-gray-600">
                        A confirmation email has been sent to <strong><?php echo htmlspecialchars($order_details['billing_email']); ?></strong>
                    </p>
                </div>

                <!-- Order Details -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Order Details</h3>
                    
                    <div class="space-y-3 mb-4">
                        <?php foreach ($order_items as $item): ?>
                            <div class="flex justify-between pb-3 border-b border-gray-100 last:border-0">
                                <div>
                                    <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($item['service_name']); ?></h4>
                                    <p class="text-sm text-gray-600">Qty: <?php echo $item['quantity']; ?></p>
                                </div>
                                <p class="font-bold text-gray-900">GHS <?php echo number_format($item['total_price'], 2); ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="flex justify-between items-center pt-4 border-t border-gray-200">
                        <span class="text-lg font-semibold text-gray-900">Total Paid:</span>
                        <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($order_details['final_amount'], 2); ?></span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex gap-4">
                    <a href="orders.php" class="flex-1 px-6 py-3 bg-primary text-white text-center font-medium rounded-lg hover:bg-primary/90 transition-colors">
                        <i class="fas fa-receipt mr-2"></i>View All Orders
                    </a>
                    <a href="invoice.php?order=<?php echo $order_number; ?>" class="flex-1 px-6 py-3 border-2 border-gray-300 text-gray-700 text-center font-medium rounded-lg hover:bg-gray-50 transition-colors">
                        <i class="fas fa-file-invoice mr-2"></i>Download Invoice
                    </a>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
