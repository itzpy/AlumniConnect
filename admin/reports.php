<?php
/**
 * Admin Reports Page
 * Platform analytics and reports
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
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$db = new db_connection();
$cart = new Cart();
$cart_count = 0;

// Revenue by month (last 12 months)
$monthly_revenue = $db->db_fetch_all("
    SELECT 
        DATE_FORMAT(date_created, '%Y-%m') as month,
        DATE_FORMAT(MIN(date_created), '%b %Y') as month_name,
        COUNT(*) as order_count,
        COALESCE(SUM(final_amount), 0) as revenue
    FROM orders 
    WHERE payment_status = 'paid' 
    AND date_created >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(date_created, '%Y-%m')
    ORDER BY month ASC
");

// Revenue by service type
$revenue_by_type = $db->db_fetch_all("
    SELECT 
        s.service_type,
        COUNT(oi.order_item_id) as orders,
        COALESCE(SUM(oi.total_price), 0) as revenue
    FROM order_items oi
    JOIN services s ON oi.service_id = s.service_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.payment_status = 'paid'
    GROUP BY s.service_type
    ORDER BY revenue DESC
");

// Top services
$top_services = $db->db_fetch_all("
    SELECT 
        s.service_name,
        s.service_type,
        COUNT(oi.order_item_id) as order_count,
        COALESCE(SUM(oi.total_price), 0) as revenue
    FROM order_items oi
    JOIN services s ON oi.service_id = s.service_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.payment_status = 'paid'
    GROUP BY s.service_id
    ORDER BY revenue DESC
    LIMIT 10
");

// User registration by month
$user_registrations = $db->db_fetch_all("
    SELECT 
        DATE_FORMAT(date_created, '%Y-%m') as month,
        DATE_FORMAT(MIN(date_created), '%b %Y') as month_name,
        COUNT(*) as count,
        SUM(CASE WHEN user_role = 'student' THEN 1 ELSE 0 END) as students,
        SUM(CASE WHEN user_role = 'alumni' THEN 1 ELSE 0 END) as alumni
    FROM users 
    WHERE date_created >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
    GROUP BY DATE_FORMAT(date_created, '%Y-%m')
    ORDER BY month ASC
");

// Summary stats
$summary = $db->db_fetch_one("
    SELECT 
        (SELECT COUNT(*) FROM users WHERE is_active = 1) as total_users,
        (SELECT COUNT(*) FROM orders WHERE payment_status = 'paid') as total_orders,
        (SELECT COALESCE(SUM(final_amount), 0) FROM orders WHERE payment_status = 'paid') as total_revenue,
        (SELECT COUNT(*) FROM services WHERE is_active = 1) as total_services
");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports & Analytics - Admin | AlumniConnect</title>
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
    <style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-gray-50">
    <?php include '../views/includes/navbar.php'; ?>

    <div class="flex min-h-screen">
        <?php include '../views/includes/sidebar.php'; ?>

        <main class="flex-1 min-w-0 p-6 lg:p-8 overflow-x-auto">
            <!-- Page Header -->
            <div class="flex items-center justify-between mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-chart-pie text-primary mr-3"></i>Reports & Analytics
                    </h1>
                    <p class="text-gray-600">Platform performance and business insights</p>
                </div>
                <div class="flex space-x-3">
                    <button onclick="exportReport('pdf')" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-file-pdf mr-2"></i>Export PDF
                    </button>
                    <button onclick="exportReport('csv')" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <i class="fas fa-file-csv mr-2"></i>Export CSV
                    </button>
                </div>
            </div>

            <!-- Summary Stats -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($summary['total_users'] ?? 0); ?></h3>
                    <p class="text-gray-600 text-sm">Total Users</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-shopping-bag text-2xl text-green-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($summary['total_orders'] ?? 0); ?></h3>
                    <p class="text-gray-600 text-sm">Total Orders</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-dollar-sign text-2xl text-purple-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1">GHS <?php echo number_format($summary['total_revenue'] ?? 0, 2); ?></h3>
                    <p class="text-gray-600 text-sm">Total Revenue</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-orange-100 p-3 rounded-lg">
                            <i class="fas fa-boxes text-2xl text-orange-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo number_format($summary['total_services'] ?? 0); ?></h3>
                    <p class="text-gray-600 text-sm">Active Services</p>
                </div>
            </div>

            <!-- Charts Row -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Monthly Revenue</h2>
                    <canvas id="revenueChart" height="200"></canvas>
                </div>

                <!-- User Registration Chart -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">User Registrations</h2>
                    <canvas id="userChart" height="200"></canvas>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
                <!-- Revenue by Service Type -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Revenue by Service Type</h2>
                    <canvas id="serviceTypeChart" height="200"></canvas>
                </div>

                <!-- Top Services -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h2 class="text-lg font-bold text-gray-900 mb-4">Top Performing Services</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-xs text-gray-500 uppercase border-b">
                                    <th class="pb-3">Service</th>
                                    <th class="pb-3">Type</th>
                                    <th class="pb-3">Orders</th>
                                    <th class="pb-3">Revenue</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <?php if ($top_services): ?>
                                    <?php foreach ($top_services as $svc): ?>
                                    <tr class="text-sm">
                                        <td class="py-3 font-medium"><?php echo htmlspecialchars($svc['service_name']); ?></td>
                                        <td class="py-3">
                                            <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-full text-xs">
                                                <?php echo ucfirst($svc['service_type']); ?>
                                            </span>
                                        </td>
                                        <td class="py-3"><?php echo $svc['order_count']; ?></td>
                                        <td class="py-3 font-semibold text-green-600">GHS <?php echo number_format($svc['revenue'], 2); ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="py-4 text-center text-gray-500">No data available</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthly_revenue ? array_column($monthly_revenue, 'month_name') : []); ?>,
                datasets: [{
                    label: 'Revenue (GHS)',
                    data: <?php echo json_encode($monthly_revenue ? array_map('floatval', array_column($monthly_revenue, 'revenue')) : []); ?>,
                    borderColor: '#7A1E1E',
                    backgroundColor: 'rgba(122, 30, 30, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { callback: value => 'GHS ' + value.toLocaleString() }
                    }
                }
            }
        });

        // User Registration Chart
        const userCtx = document.getElementById('userChart').getContext('2d');
        new Chart(userCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($user_registrations ? array_column($user_registrations, 'month_name') : []); ?>,
                datasets: [
                    {
                        label: 'Students',
                        data: <?php echo json_encode($user_registrations ? array_map('intval', array_column($user_registrations, 'students')) : []); ?>,
                        backgroundColor: '#3B82F6'
                    },
                    {
                        label: 'Alumni',
                        data: <?php echo json_encode($user_registrations ? array_map('intval', array_column($user_registrations, 'alumni')) : []); ?>,
                        backgroundColor: '#8B5CF6'
                    }
                ]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        // Service Type Pie Chart
        const typeCtx = document.getElementById('serviceTypeChart').getContext('2d');
        new Chart(typeCtx, {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode($revenue_by_type ? array_map(function($t) { return ucfirst($t['service_type']); }, $revenue_by_type) : []); ?>,
                datasets: [{
                    data: <?php echo json_encode($revenue_by_type ? array_map('floatval', array_column($revenue_by_type, 'revenue')) : []); ?>,
                    backgroundColor: ['#7A1E1E', '#3B82F6', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'right' }
                }
            }
        });

        function exportReport(format) {
            alert('Export to ' + format.toUpperCase() + ' functionality coming soon!');
        }
    </script>
</body>
</html>
