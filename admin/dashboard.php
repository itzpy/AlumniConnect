<?php
/**
 * Admin Dashboard
 * Platform management and analytics
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Admin';
$user_type = $_SESSION['user_type'];

require_once(dirname(__FILE__).'/../settings/db_class.php');
require_once(dirname(__FILE__).'/../classes/order_class.php');

$db = new db_connection();
$order = new Order();

// Get platform statistics
$stats = [];

// Total users by type
$user_result = $db->db_fetch_all("SELECT user_role, COUNT(*) as count FROM users WHERE is_active = 1 GROUP BY user_role");
$user_counts = [];
if ($user_result) {
    foreach ($user_result as $row) {
        $user_counts[$row['user_role']] = $row['count'];
    }
}
$stats['total_users'] = array_sum($user_counts);
$stats['total_students'] = $user_counts['student'] ?? 0;
$stats['total_alumni'] = $user_counts['alumni'] ?? 0;

// Total orders and revenue
$order_stats = $db->db_fetch_one("SELECT COUNT(*) as count, COALESCE(SUM(final_amount), 0) as revenue FROM orders WHERE payment_status = 'paid'");
$stats['total_orders'] = $order_stats['count'] ?? 0;
$stats['total_revenue'] = $order_stats['revenue'] ?? 0;

// Pending orders
$pending = $db->db_fetch_one("SELECT COUNT(*) as count FROM orders WHERE order_status = 'pending'");
$stats['pending_orders'] = $pending['count'] ?? 0;

// Active services
$active_svc = $db->db_fetch_one("SELECT COUNT(*) as count FROM services WHERE is_active = 1");
$stats['active_services'] = $active_svc['count'] ?? 0;

// Recent orders - use the Order class method
$recent_orders = $order->getAllOrders([]);
if ($recent_orders) {
    $recent_orders = array_slice($recent_orders, 0, 5);
}

// Recent users
$recent_users = $db->db_fetch_all("SELECT user_id, first_name, last_name, email, user_role, date_created FROM users ORDER BY date_created DESC LIMIT 5");

// Monthly revenue (last 6 months)
$monthly_result = $db->db_fetch_all("SELECT DATE_FORMAT(date_created, '%Y-%m') as month, COALESCE(SUM(final_amount), 0) as revenue 
                          FROM orders WHERE payment_status = 'paid' 
                          GROUP BY DATE_FORMAT(date_created, '%Y-%m') 
                          ORDER BY month DESC LIMIT 6");
$monthly_revenue = $monthly_result ? array_reverse($monthly_result) : [];

$cart_count = 0; // Admin doesn't need cart
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">
                    <i class="fas fa-shield-alt text-primary mr-3"></i>Admin Dashboard
                </h1>
                <p class="text-gray-600">Platform management and analytics overview</p>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <!-- Total Users -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                        <span class="text-sm text-green-500 font-medium">+12%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($stats['total_users']); ?></h3>
                    <p class="text-gray-600 text-sm">Total Users</p>
                    <div class="mt-2 text-xs text-gray-500">
                        <span class="text-blue-600"><?php echo $stats['total_students']; ?> students</span> • 
                        <span class="text-purple-600"><?php echo $stats['total_alumni']; ?> alumni</span>
                    </div>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-dollar-sign text-2xl text-green-600"></i>
                        </div>
                        <span class="text-sm text-green-500 font-medium">+8%</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">GHS <?php echo number_format($stats['total_revenue'], 2); ?></h3>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                </div>

                <!-- Total Orders -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-shopping-bag text-2xl text-purple-600"></i>
                        </div>
                        <span class="text-sm text-orange-500 font-medium"><?php echo $stats['pending_orders']; ?> pending</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($stats['total_orders']); ?></h3>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                </div>

                <!-- Active Services -->
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-100 p-3 rounded-lg">
                            <i class="fas fa-boxes text-2xl text-orange-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($stats['active_services']); ?></h3>
                    <p class="text-gray-600 text-sm">Active Services</p>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                <a href="users.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-primary hover:shadow-md transition-all flex items-center space-x-3">
                    <div class="bg-blue-100 p-2 rounded-lg">
                        <i class="fas fa-user-plus text-blue-600"></i>
                    </div>
                    <span class="font-medium text-gray-700">Manage Users</span>
                </a>
                <a href="services.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-primary hover:shadow-md transition-all flex items-center space-x-3">
                    <div class="bg-green-100 p-2 rounded-lg">
                        <i class="fas fa-plus-circle text-green-600"></i>
                    </div>
                    <span class="font-medium text-gray-700">Add Service</span>
                </a>
                <a href="orders.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-primary hover:shadow-md transition-all flex items-center space-x-3">
                    <div class="bg-purple-100 p-2 rounded-lg">
                        <i class="fas fa-list-alt text-purple-600"></i>
                    </div>
                    <span class="font-medium text-gray-700">View Orders</span>
                </a>
                <a href="reports.php" class="bg-white p-4 rounded-lg border border-gray-200 hover:border-primary hover:shadow-md transition-all flex items-center space-x-3">
                    <div class="bg-orange-100 p-2 rounded-lg">
                        <i class="fas fa-chart-pie text-orange-600"></i>
                    </div>
                    <span class="font-medium text-gray-700">View Reports</span>
                </a>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Recent Orders -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">Recent Orders</h2>
                        <a href="orders.php" class="text-primary hover:text-primary-dark text-sm font-medium">View All →</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase border-b">
                                    <th class="pb-3">Order</th>
                                    <th class="pb-3">Customer</th>
                                    <th class="pb-3">Amount</th>
                                    <th class="pb-3">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if (!empty($recent_orders)): ?>
                                    <?php foreach ($recent_orders as $ord): ?>
                                    <tr class="text-sm">
                                        <td class="py-3 font-medium">#<?php echo htmlspecialchars($ord['order_number']); ?></td>
                                        <td class="py-3 text-gray-600"><?php echo htmlspecialchars($ord['first_name'] . ' ' . $ord['last_name']); ?></td>
                                        <td class="py-3">GHS <?php echo number_format($ord['final_amount'], 2); ?></td>
                                        <td class="py-3">
                                            <?php 
                                            $status_class = match($ord['order_status']) {
                                                'completed' => 'bg-green-100 text-green-800',
                                                'pending' => 'bg-yellow-100 text-yellow-800',
                                                'processing' => 'bg-blue-100 text-blue-800',
                                                'cancelled' => 'bg-red-100 text-red-800',
                                                default => 'bg-gray-100 text-gray-800'
                                            };
                                            ?>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium <?php echo $status_class; ?>">
                                                <?php echo ucfirst($ord['order_status']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">No orders yet</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Users -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-bold text-gray-900">New Users</h2>
                        <a href="users.php" class="text-primary hover:text-primary-dark text-sm font-medium">View All →</a>
                    </div>
                    <div class="space-y-4">
                        <?php if (!empty($recent_users)): ?>
                            <?php foreach ($recent_users as $user): ?>
                            <div class="flex items-center justify-between py-2">
                                <div class="flex items-center space-x-3">
                                    <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user['first_name'] . '+' . $user['last_name']); ?>&background=7A1E1E&color=fff" 
                                         alt="<?php echo htmlspecialchars($user['first_name']); ?>" class="w-10 h-10 rounded-full">
                                    <div>
                                        <p class="font-medium text-gray-900"><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></p>
                                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                                <span class="px-2 py-1 rounded-full text-xs font-medium 
                                    <?php echo $user['user_role'] === 'alumni' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <?php echo ucfirst($user['user_role']); ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="text-center text-gray-500 py-4">No users yet</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mt-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue Overview</h2>
                <canvas id="revenueChart" height="100"></canvas>
            </div>
        </main>
    </div>

    <script>
        // Revenue Chart
        const ctx = document.getElementById('revenueChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($monthly_revenue, 'month')); ?>,
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: <?php echo json_encode(array_map('floatval', array_column($monthly_revenue, 'revenue'))); ?>,
                    borderColor: '#7A1E1E',
                    backgroundColor: 'rgba(122, 30, 30, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return 'GHS ' + value.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
