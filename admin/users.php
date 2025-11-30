<?php
/**
 * Admin - User Management
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

// Handle user actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        $target_user_id = intval($_POST['user_id'] ?? 0);
        $conn = $db->db_conn();
        
        if ($_POST['action'] === 'deactivate' && $target_user_id != $user_id) {
            $sql = "UPDATE users SET is_active = 0 WHERE user_id = $target_user_id";
            if (mysqli_query($conn, $sql)) {
                $message = "User deactivated successfully.";
            } else {
                $error = "Failed to deactivate user.";
            }
        } elseif ($_POST['action'] === 'activate') {
            $sql = "UPDATE users SET is_active = 1 WHERE user_id = $target_user_id";
            if (mysqli_query($conn, $sql)) {
                $message = "User activated successfully.";
            } else {
                $error = "Failed to activate user.";
            }
        } elseif ($_POST['action'] === 'change_role' && $target_user_id != $user_id) {
            $new_role = $_POST['new_role'] ?? '';
            if (in_array($new_role, ['student', 'alumni', 'admin'])) {
                $new_role = mysqli_real_escape_string($conn, $new_role);
                $sql = "UPDATE users SET user_role = '$new_role' WHERE user_id = $target_user_id";
                if (mysqli_query($conn, $sql)) {
                    $message = "User role updated successfully.";
                } else {
                    $error = "Failed to update user role.";
                }
            }
        }
    }
}

// Get filter params
$filter_role = $_GET['role'] ?? '';
$filter_status = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Build query
$where_conditions = ["1=1"];

if ($filter_role && in_array($filter_role, ['student', 'alumni', 'admin'])) {
    $where_conditions[] = "user_role = '$filter_role'";
}

if ($filter_status === 'active') {
    $where_conditions[] = "is_active = 1";
} elseif ($filter_status === 'inactive') {
    $where_conditions[] = "is_active = 0";
}

if ($search) {
    $conn = $db->db_conn();
    $search_escaped = mysqli_real_escape_string($conn, $search);
    $where_conditions[] = "(first_name LIKE '%$search_escaped%' OR last_name LIKE '%$search_escaped%' OR email LIKE '%$search_escaped%')";
}

$where_clause = implode(" AND ", $where_conditions);
$sql = "SELECT * FROM users WHERE $where_clause ORDER BY date_created DESC LIMIT 50";

$users = $db->db_fetch_all($sql);
if (!$users) $users = [];

// Get counts
$total_users = $db->db_fetch_one("SELECT COUNT(*) as count FROM users");
$active_users = $db->db_fetch_one("SELECT COUNT(*) as count FROM users WHERE is_active = 1");
$role_counts_result = $db->db_fetch_all("SELECT user_role, COUNT(*) as count FROM users WHERE is_active = 1 GROUP BY user_role");
$role_counts = ['student' => 0, 'alumni' => 0, 'admin' => 0];
if ($role_counts_result) {
    foreach ($role_counts_result as $row) {
        $role_counts[$row['user_role']] = $row['count'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin | AlumniConnect</title>
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
            <!-- Alert Messages -->
            <?php if ($message): ?>
            <div class="mb-6 p-4 bg-green-100 border border-green-200 text-green-700 rounded-lg flex items-center">
                <i class="fas fa-check-circle mr-3"></i>
                <?php echo htmlspecialchars($message); ?>
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="mb-6 p-4 bg-red-100 border border-red-200 text-red-700 rounded-lg flex items-center">
                <i class="fas fa-exclamation-circle mr-3"></i>
                <?php echo htmlspecialchars($error); ?>
            </div>
            <?php endif; ?>

            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">
                        <i class="fas fa-users-cog text-primary mr-3"></i>User Management
                    </h1>
                    <p class="text-gray-600">Manage platform users and their permissions</p>
                </div>
                <a href="../views/add_user.php" class="inline-flex items-center px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium shadow-sm">
                    <i class="fas fa-user-plus mr-2"></i>Add New User
                </a>
            </div>

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-users text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $total_users['count'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Total Users</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-blue-100 p-3 rounded-lg">
                            <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $role_counts['student'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Students</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-purple-100 p-3 rounded-lg">
                            <i class="fas fa-user-tie text-2xl text-purple-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $role_counts['alumni'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Alumni</p>
                </div>

                <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                    <div class="flex items-center justify-between mb-4">
                        <div class="bg-red-100 p-3 rounded-lg">
                            <i class="fas fa-shield-alt text-2xl text-red-600"></i>
                        </div>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-1"><?php echo $role_counts['admin'] ?? 0; ?></h3>
                    <p class="text-gray-600 text-sm">Administrators</p>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-6">
                <form method="GET" id="filterForm" class="flex flex-col md:flex-row md:items-center gap-4">
                    <div class="flex-1">
                        <div class="relative">
                            <input type="text" name="search" id="searchUsers" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search by name or email..." 
                                   class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary focus:bg-white transition-all">
                            <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400"></i>
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <select name="role" id="roleFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="">All Roles</option>
                            <option value="student" <?php echo $filter_role === 'student' ? 'selected' : ''; ?>>Students</option>
                            <option value="alumni" <?php echo $filter_role === 'alumni' ? 'selected' : ''; ?>>Alumni</option>
                            <option value="admin" <?php echo $filter_role === 'admin' ? 'selected' : ''; ?>>Admins</option>
                        </select>
                        <select name="status" id="statusFilter" class="px-4 py-2.5 bg-gray-50 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                            <option value="">All Status</option>
                            <option value="active" <?php echo $filter_status === 'active' ? 'selected' : ''; ?>>Active</option>
                            <option value="inactive" <?php echo $filter_status === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                        </select>
                        <button type="submit" class="px-4 py-2.5 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filter
                        </button>
                    </div>
                </form>
            </div>

            <!-- Users Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">User</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Role</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Joined</th>
                                <th class="text-left py-4 px-6 font-semibold text-gray-700">Status</th>
                                <th class="text-right py-4 px-6 font-semibold text-gray-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (count($users) > 0): ?>
                                <?php foreach ($users as $u): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="py-4 px-6">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white font-semibold">
                                                <?php echo strtoupper(substr($u['first_name'] ?? 'U', 0, 1)); ?>
                                            </div>
                                            <div>
                                                <p class="font-medium text-gray-900"><?php echo htmlspecialchars(($u['first_name'] ?? '') . ' ' . ($u['last_name'] ?? '')); ?></p>
                                                <p class="text-sm text-gray-500"><?php echo htmlspecialchars($u['email'] ?? ''); ?></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php 
                                        $role = $u['user_role'] ?? 'student';
                                        $role_class = match($role) {
                                            'admin' => 'bg-red-100 text-red-700',
                                            'alumni' => 'bg-purple-100 text-purple-700',
                                            default => 'bg-blue-100 text-blue-700'
                                        };
                                        $role_icon = match($role) {
                                            'admin' => 'fa-shield-alt',
                                            'alumni' => 'fa-user-tie',
                                            default => 'fa-user-graduate'
                                        };
                                        ?>
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium <?php echo $role_class; ?>">
                                            <i class="fas <?php echo $role_icon; ?> mr-1.5"></i>
                                            <?php echo ucfirst($role); ?>
                                        </span>
                                    </td>
                                    <td class="py-4 px-6">
                                        <p class="text-gray-900"><?php echo date('M d, Y', strtotime($u['date_created'])); ?></p>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php if ($u['is_active']): ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-700">
                                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full mr-1.5"></span>Active
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-700">
                                                <span class="w-1.5 h-1.5 bg-gray-400 rounded-full mr-1.5"></span>Inactive
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="py-4 px-6">
                                        <?php if ($u['user_id'] != $user_id): ?>
                                        <div class="flex items-center justify-end space-x-2">
                                            <!-- Edit Button -->
                                            <a href="../views/edit_user.php?id=<?php echo $u['user_id']; ?>" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit User">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <!-- Role Change Dropdown -->
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="change_role">
                                                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                                <select name="new_role" onchange="this.form.submit()" 
                                                        class="text-sm px-2 py-1.5 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                                                    <option value="">Change Role</option>
                                                    <option value="student">Student</option>
                                                    <option value="alumni">Alumni</option>
                                                    <option value="admin">Admin</option>
                                                </select>
                                            </form>
                                            
                                            <!-- Activate/Deactivate -->
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="user_id" value="<?php echo $u['user_id']; ?>">
                                                <?php if ($u['is_active']): ?>
                                                    <input type="hidden" name="action" value="deactivate">
                                                    <button type="submit" class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors" 
                                                            title="Deactivate" onclick="return confirm('Deactivate this user?')">
                                                        <i class="fas fa-user-slash"></i>
                                                    </button>
                                                <?php else: ?>
                                                    <input type="hidden" name="action" value="activate">
                                                    <button type="submit" class="p-2 text-green-600 hover:bg-green-50 rounded-lg transition-colors" title="Activate">
                                                        <i class="fas fa-user-check"></i>
                                                    </button>
                                                <?php endif; ?>
                                            </form>
                                        </div>
                                        <?php else: ?>
                                        <div class="flex items-center justify-end space-x-2">
                                            <a href="../views/edit_user.php?id=<?php echo $u['user_id']; ?>" 
                                               class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit Profile">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <span class="text-sm text-gray-400 italic">Current User</span>
                                        </div>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <div class="bg-gray-100 p-4 rounded-full mb-4">
                                                <i class="fas fa-users text-4xl text-gray-400"></i>
                                            </div>
                                            <h3 class="text-lg font-medium text-gray-900 mb-1">No users found</h3>
                                            <p class="text-gray-500">Try adjusting your search or filter criteria</p>
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

    <script>
        // Auto-submit form when filters change
        document.getElementById('roleFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        document.getElementById('statusFilter').addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
        
        // Debounce search input
        let searchTimeout;
        document.getElementById('searchUsers').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                document.getElementById('filterForm').submit();
            }, 500);
        });
    </script>
</body>
</html>
