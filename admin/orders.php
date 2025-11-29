<?php
session_start();
require_once '../settings/core.php';

// Admin authorization check
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Admin';

// Include database and classes
require_once '../settings/db_class.php';
require_once '../classes/order_class.php';
require_once '../classes/cart_class.php';

$db = new db_connection();
$orderClass = new Order();
$cart = new Cart();
$cart_count = 0;

// Get order statistics
$stats = $db->db_fetch_one("SELECT 
    COUNT(*) as total_orders,
    SUM(CASE WHEN order_status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
    SUM(CASE WHEN order_status = 'completed' THEN 1 ELSE 0 END) as completed_orders,
    SUM(CASE WHEN order_status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_orders,
    COALESCE(SUM(CASE WHEN payment_status = 'paid' THEN final_amount ELSE 0 END), 0) as total_revenue
    FROM orders");

if (!$stats) {
    $stats = ['total_orders' => 0, 'pending_orders' => 0, 'completed_orders' => 0, 'cancelled_orders' => 0, 'total_revenue' => 0];
}

// Get all orders with customer info
$orders = $db->db_fetch_all("SELECT o.*, u.first_name, u.last_name, u.email 
                             FROM orders o 
                             LEFT JOIN users u ON o.user_id = u.user_id 
                             ORDER BY o.date_created DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Management - Admin | AlumniConnect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#7A1E1E',
                        'primary-dark': '#5a1616',
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
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
    <?php include '../views/includes/navbar.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include '../views/includes/sidebar.php'; ?>

        <main class="flex-1 min-w-0 p-6 lg:p-8 overflow-x-auto">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-shopping-bag text-primary mr-3"></i>Order Management
                    </h1>
                    <p class="text-gray-600">Track and manage all customer orders</p>
                </div>
                <div class="flex gap-3">
                    <button onclick="exportOrders()" class="inline-flex items-center px-4 py-2.5 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors font-medium">
                        <i class="fas fa-download mr-2"></i>Export
                    </button>
                    <a href="reports.php" class="inline-flex items-center px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                        <i class="fas fa-chart-bar mr-2"></i>View Reports
                    </a>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-shopping-cart text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['total_orders'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-amber-100 p-3 rounded-lg">
                            <i class="fas fa-clock text-2xl text-amber-600"></i>
                        </div>
                        <span class="text-sm text-amber-600 font-medium">Pending</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['pending_orders'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Pending Orders</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <span class="text-sm text-green-600 font-medium">Done</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['completed_orders'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Completed</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-times-circle text-2xl text-red-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['cancelled_orders'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Cancelled</p>
                </div>

                <div class="bg-gradient-to-br from-primary to-primary-dark p-6 rounded-xl shadow-sm hover:shadow-md transition-shadow text-white">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-white/20 p-3 rounded-lg">
                            <i class="fas fa-money-bill-wave text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold mb-1">GHS <?php echo number_format($stats['total_revenue'] ?? 0, 2); ?></h3>
                    <p class="text-white/80 text-sm">Total Revenue</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchOrders" placeholder="Search by order ID, customer name..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:bg-white transition-all">
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select id="statusFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="">All Status</option>
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <select id="paymentFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="">All Payments</option>
                            <option value="paid">Paid</option>
                            <option value="pending">Payment Pending</option>
                            <option value="failed">Failed</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Order ID</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Customer</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Date</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Amount</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Payment</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Status</th>
                                <th class="text-right py-4 px-6 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($orders && count($orders) > 0): ?>
                                <?php foreach ($orders as $order): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <span class="font-mono font-bold text-primary">#<?php echo str_pad($order['order_id'], 6, '0', STR_PAD_LEFT); ?></span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($order['first_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars(($order['first_name'] ?? '') . ' ' . ($order['last_name'] ?? '')); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($order['email'] ?? ''); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-gray-900"><?php echo date('M d, Y', strtotime($order['date_created'])); ?></p>
                                        <p class="text-sm text-gray-500"><?php echo date('h:i A', strtotime($order['date_created'])); ?></p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-gray-900">GHS <?php echo number_format($order['final_amount'] ?? $order['total_amount'] ?? 0, 2); ?></span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php 
                                        $payment_status = strtolower($order['payment_status'] ?? 'pending');
                                        $payment_class = match($payment_status) {
                                            'paid' => 'bg-green-100 text-green-700',
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'failed' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $payment_class; ?>">
                                            <?php echo ucfirst($payment_status); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php 
                                        $order_status = strtolower($order['order_status'] ?? 'pending');
                                        $status_class = match($order_status) {
                                            'completed' => 'bg-green-100 text-green-700',
                                            'processing' => 'bg-blue-100 text-blue-700',
                                            'pending' => 'bg-amber-100 text-amber-700',
                                            'cancelled' => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700'
                                        };
                                        $status_dot = match($order_status) {
                                            'completed' => 'bg-green-500',
                                            'processing' => 'bg-blue-500',
                                            'pending' => 'bg-amber-500',
                                            'cancelled' => 'bg-red-500',
                                            default => 'bg-gray-500'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                            <span class="w-1.5 h-1.5 <?php echo $status_dot; ?> rounded-full mr-1.5"></span>
                                            <?php echo ucfirst($order_status); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button onclick="viewOrder(<?php echo $order['order_id']; ?>)" 
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="updateStatus(<?php echo $order['order_id']; ?>)" 
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Update Status">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="printInvoice(<?php echo $order['order_id']; ?>)" 
                                                    class="p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition-colors" title="Print Invoice">
                                                <i class="fas fa-print"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gray-100 p-4 rounded-full mb-4">
                                                <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No orders yet</h3>
                                            <p class="text-gray-500">Orders will appear here when customers make purchases</p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- View Order Modal -->
    <div id="viewOrderModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal('viewOrderModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Order Details</h3>
                    <button onclick="closeModal('viewOrderModal')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div id="orderDetails" class="p-6">
                    <!-- Content loaded via AJAX -->
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button onclick="closeModal('viewOrderModal')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Close
                    </button>
                    <button onclick="printCurrentOrder()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                        <i class="fas fa-print mr-2"></i>Print Invoice
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Status Modal -->
    <div id="updateStatusModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal('updateStatusModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full mx-4 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Update Order Status</h3>
                    <button onclick="closeModal('updateStatusModal')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div class="p-6">
                    <input type="hidden" id="updateOrderId">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">New Status</label>
                        <select id="newStatus" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="pending">Pending</option>
                            <option value="processing">Processing</option>
                            <option value="completed">Completed</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                        <textarea id="statusNotes" rows="3" class="w-full px-4 py-2.5 border border-gray-200 rounded-lg focus:outline-none focus:border-primary" placeholder="Add any notes about this status change..."></textarea>
                    </div>
                    <div class="flex items-center mb-4">
                        <input type="checkbox" id="notifyCustomer" class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                        <label for="notifyCustomer" class="ml-2 text-sm text-gray-700">Notify customer via email</label>
                    </div>
                </div>
                <div class="flex justify-end gap-3 p-6 border-t border-gray-200">
                    <button onclick="closeModal('updateStatusModal')" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Cancel
                    </button>
                    <button onclick="saveStatusUpdate()" class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                        Update Status
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentOrderId = null;

        // Search functionality
        document.getElementById('searchOrders').addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        });

        // Filters
        document.getElementById('statusFilter').addEventListener('change', filterTable);
        document.getElementById('paymentFilter').addEventListener('change', filterTable);

        function filterTable() {
            const status = document.getElementById('statusFilter').value.toLowerCase();
            const payment = document.getElementById('paymentFilter').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                let showRow = true;
                
                if (status && !rowText.includes(status)) showRow = false;
                if (payment && !rowText.includes(payment)) showRow = false;
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function viewOrder(orderId) {
            currentOrderId = orderId;
            fetch(`../actions/get_order_details.php?id=${orderId}`)
                .then(response => response.json())
                .then(data => {
                    let itemsHtml = '';
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            itemsHtml += `
                                <tr class="border-b border-gray-100">
                                    <td class="py-2">${item.service_name || item.title || 'Service'}</td>
                                    <td class="py-2 text-center">${item.quantity}</td>
                                    <td class="py-2 text-right">GHS ${parseFloat(item.price).toFixed(2)}</td>
                                    <td class="py-2 text-right">GHS ${parseFloat(item.total_price).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }
                    
                    document.getElementById('orderDetails').innerHTML = `
                        <div class="grid grid-cols-2 gap-6 mb-6 text-left">
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Order Information</h4>
                                <p class="mb-1"><span class="font-medium">Order ID:</span> #${String(data.order_id).padStart(6, '0')}</p>
                                <p class="mb-1"><span class="font-medium">Date:</span> ${new Date(data.date_created).toLocaleDateString()}</p>
                                <p><span class="font-medium">Status:</span> <span class="capitalize">${data.order_status}</span></p>
                            </div>
                            <div>
                                <h4 class="text-sm font-medium text-gray-500 mb-2">Customer Information</h4>
                                <p class="mb-1"><span class="font-medium">Name:</span> ${data.customer_name || 'N/A'}</p>
                                <p><span class="font-medium">Email:</span> ${data.customer_email || 'N/A'}</p>
                            </div>
                        </div>
                        <div class="text-left">
                            <h4 class="text-sm font-medium text-gray-500 mb-2">Order Items</h4>
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="text-left py-2 px-2 text-sm font-medium text-gray-700">Item</th>
                                        <th class="text-center py-2 px-2 text-sm font-medium text-gray-700">Qty</th>
                                        <th class="text-right py-2 px-2 text-sm font-medium text-gray-700">Price</th>
                                        <th class="text-right py-2 px-2 text-sm font-medium text-gray-700">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${itemsHtml || '<tr><td colspan="4" class="py-4 text-center text-gray-500">No items</td></tr>'}
                                </tbody>
                                <tfoot class="border-t-2 border-gray-200">
                                    <tr>
                                        <td colspan="3" class="py-3 text-right font-bold">Total:</td>
                                        <td class="py-3 text-right font-bold text-primary">GHS ${parseFloat(data.final_amount || data.total_amount || 0).toFixed(2)}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    `;
                    document.getElementById('viewOrderModal').classList.remove('hidden');
                })
                .catch(() => {
                    document.getElementById('orderDetails').innerHTML = `
                        <div class="text-center py-8">
                            <i class="fas fa-exclamation-circle text-4xl text-gray-400 mb-4"></i>
                            <p class="text-gray-500">Unable to load order details</p>
                        </div>
                    `;
                    document.getElementById('viewOrderModal').classList.remove('hidden');
                });
        }

        function updateStatus(orderId) {
            document.getElementById('updateOrderId').value = orderId;
            document.getElementById('updateStatusModal').classList.remove('hidden');
        }

        function saveStatusUpdate() {
            const orderId = document.getElementById('updateOrderId').value;
            const newStatus = document.getElementById('newStatus').value;
            const notes = document.getElementById('statusNotes').value;
            const notifyCustomer = document.getElementById('notifyCustomer').checked;

            fetch('../actions/update_order_status.php', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({
                    order_id: orderId,
                    status: newStatus,
                    notes: notes,
                    notify: notifyCustomer
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                } else {
                    alert(data.message || 'Error updating order status');
                }
            });
        }

        function printInvoice(orderId) {
            window.open(`../views/invoice.php?order_id=${orderId}`, '_blank');
        }

        function printCurrentOrder() {
            if (currentOrderId) {
                printInvoice(currentOrderId);
            }
        }

        function exportOrders() {
            window.location.href = '../actions/export_orders.php';
        }
    </script>
</body>
</html>
