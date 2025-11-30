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
require_once '../classes/service_class.php';
require_once '../classes/cart_class.php';

$db = new db_connection();
$service = new Service();
$cart = new Cart();
$cart_count = 0;

// Check if we should show inactive services (default: hide them)
$show_inactive = isset($_GET['show_inactive']) && $_GET['show_inactive'] == '1';

// Get all services with provider info
$inactive_filter = $show_inactive ? "" : "WHERE s.is_active = 1";
$services = $db->db_fetch_all("SELECT s.*, u.first_name, u.last_name, u.email,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.service_id = s.service_id) as total_orders
                   FROM services s 
                   LEFT JOIN users u ON s.provider_id = u.user_id 
                   $inactive_filter
                   ORDER BY s.date_created DESC");

// Get service stats
$stats = $db->db_fetch_one("SELECT 
    COUNT(*) as total_services,
    SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_services,
    SUM(CASE WHEN is_active = 0 THEN 1 ELSE 0 END) as inactive_services
    FROM services");

if (!$stats) {
    $stats = ['total_services' => 0, 'active_services' => 0, 'inactive_services' => 0];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Service Management - Admin | AlumniConnect</title>
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
                        <i class="fas fa-boxes text-primary mr-3"></i>Service Management
                    </h1>
                    <p class="text-gray-600">Manage all platform services and offerings</p>
                </div>
                <a href="../views/add_service.php" class="inline-flex items-center px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                    <i class="fas fa-plus mr-2"></i>Add New Service
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-box text-2xl text-blue-600"></i>
                        </div>
                        <span class="text-sm text-gray-500">Total</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['total_services'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Total Services</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-green-100 p-3 rounded-lg">
                            <i class="fas fa-check-circle text-2xl text-green-600"></i>
                        </div>
                        <span class="text-sm text-green-600 font-medium">Live</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['active_services'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Active Services</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-ban text-2xl text-red-600"></i>
                        </div>
                        <span class="text-sm text-red-600 font-medium">Disabled</span>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $stats['inactive_services'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Inactive Services</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <div class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" id="searchServices" placeholder="Search services..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:bg-white transition-all">
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-3 items-center">
                        <!-- Toggle deleted services -->
                        <a href="?show_inactive=<?php echo $show_inactive ? '0' : '1'; ?>" 
                           class="px-4 py-2.5 rounded-lg border transition-all <?php echo $show_inactive ? 'bg-red-50 border-red-300 text-red-700' : 'bg-gray-50 border-gray-200 text-gray-600 hover:bg-gray-100'; ?>">
                            <i class="fas fa-<?php echo $show_inactive ? 'eye-slash' : 'eye'; ?> mr-2"></i>
                            <?php echo $show_inactive ? 'Hide Deleted' : 'Show Deleted'; ?>
                        </a>
                        <select id="categoryFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="">All Categories</option>
                            <option value="mentorship">Mentorship</option>
                            <option value="workshop">Workshop</option>
                            <option value="consultation">Consultation</option>
                            <option value="event">Event</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Services Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Service</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Provider</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Category</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Price</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Orders</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Status</th>
                                <th class="text-right py-4 px-6 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($services && count($services) > 0): ?>
                                <?php foreach ($services as $svc): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-3">
                                            <?php if (!empty($svc['image_url'])): 
                                                $admin_img_src = (strpos($svc['image_url'], 'http') === 0) 
                                                    ? $svc['image_url'] 
                                                    : '../uploads/services/' . $svc['image_url'];
                                            ?>
                                                <img src="<?php echo htmlspecialchars($admin_img_src); ?>" class="w-12 h-12 rounded-lg object-cover" alt="Service">
                                            <?php else: ?>
                                                <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                                    <i class="fas fa-box text-gray-400"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <h4 class="font-semibold text-gray-900"><?php echo htmlspecialchars($svc['service_name'] ?? 'Unnamed'); ?></h4>
                                                <span class="text-sm text-gray-500">ID: #<?php echo $svc['service_id']; ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div>
                                            <p class="font-medium text-gray-900"><?php echo htmlspecialchars(($svc['first_name'] ?? '') . ' ' . ($svc['last_name'] ?? '')); ?></p>
                                            <p class="text-sm text-gray-500"><?php echo htmlspecialchars($svc['email'] ?? ''); ?></p>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                            <?php echo ucfirst($svc['service_type'] ?? 'General'); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="font-bold text-gray-900">GHS <?php echo number_format($svc['price'] ?? 0, 2); ?></span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-primary/10 text-primary">
                                            <?php echo $svc['total_orders'] ?? 0; ?> orders
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php 
                                        $is_active = $svc['is_active'] ?? 1;
                                        if ($is_active): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-700">
                                                <span class="w-1.5 h-1.5 bg-red-500 rounded-full mr-1.5"></span>Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <div class="flex items-center justify-end space-x-2">
                                            <button onclick="viewService(<?php echo $svc['service_id']; ?>)" 
                                                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="View">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="editService(<?php echo $svc['service_id']; ?>)" 
                                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button onclick="deleteService(<?php echo $svc['service_id']; ?>)" 
                                                    class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Delete">
                                                <i class="fas fa-trash"></i>
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
                                                <i class="fas fa-box-open text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No services found</h3>
                                            <p class="text-gray-500 mb-4">Get started by adding your first service</p>
                                            <a href="../views/add_service.php" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                                <i class="fas fa-plus mr-2"></i>Add Service
                                            </a>
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

    <!-- View Service Modal -->
    <div id="viewServiceModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="fixed inset-0 bg-black/50 transition-opacity" onclick="closeModal('viewServiceModal')"></div>
            <div class="relative bg-white rounded-xl shadow-xl max-w-2xl w-full mx-4 overflow-hidden">
                <div class="flex items-center justify-between p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-900">Service Details</h3>
                    <button onclick="closeModal('viewServiceModal')" class="p-2 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-times text-gray-500"></i>
                    </button>
                </div>
                <div id="serviceDetails" class="p-6">
                    <!-- Content loaded via AJAX -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // Search functionality
        document.getElementById('searchServices').addEventListener('input', filterTable);
        document.getElementById('categoryFilter').addEventListener('change', filterTable);

        function filterTable() {
            const searchTerm = document.getElementById('searchServices').value.toLowerCase();
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const rowText = row.textContent.toLowerCase();
                let showRow = true;
                
                // Search filter
                if (searchTerm && !rowText.includes(searchTerm)) showRow = false;
                
                // Category filter - check the category cell specifically
                if (category) {
                    const categoryCell = row.querySelector('td:nth-child(3)');
                    if (categoryCell && !categoryCell.textContent.toLowerCase().includes(category)) {
                        showRow = false;
                    }
                }
                
                row.style.display = showRow ? '' : 'none';
            });
        }

        function viewService(serviceId) {
            fetch(`../actions/get_service_details.php?id=${serviceId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('serviceDetails').innerHTML = `
                        <div class="flex flex-col md:flex-row gap-6">
                            <div class="md:w-1/3">
                                <img src="../uploads/services/${data.image || 'default.jpg'}" class="w-full rounded-lg object-cover" alt="Service">
                            </div>
                            <div class="md:w-2/3 text-left">
                                <h4 class="text-xl font-bold text-gray-900 mb-2">${data.title || data.service_name}</h4>
                                <p class="text-gray-600 mb-4">${data.description || 'No description available'}</p>
                                <div class="space-y-2">
                                    <p><span class="font-medium text-gray-700">Price:</span> <span class="text-primary font-bold">GHS ${data.price}</span></p>
                                    <p><span class="font-medium text-gray-700">Provider:</span> ${data.provider_name || 'N/A'}</p>
                                    <p><span class="font-medium text-gray-700">Status:</span> ${data.is_active ? '<span class="text-green-600">Active</span>' : '<span class="text-red-600">Inactive</span>'}</p>
                                </div>
                            </div>
                        </div>
                    `;
                    document.getElementById('viewServiceModal').classList.remove('hidden');
                })
                .catch(() => {
                    alert('Error loading service details');
                });
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.add('hidden');
        }

        function editService(serviceId) {
            window.location.href = `../views/edit_service.php?id=${serviceId}`;
        }

        function deleteService(serviceId) {
            if (confirm('Are you sure you want to delete this service? This action cannot be undone.')) {
                fetch('../actions/delete_service_action.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify({service_id: serviceId})
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        alert('Service deleted successfully!');
                        location.reload();
                    } else {
                        alert(data.message || 'Error deleting service');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to delete service. Check console for details.');
                });
            }
        }
    </script>
</body>
</html>
