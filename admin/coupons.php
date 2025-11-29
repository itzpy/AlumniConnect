<?php
/**
 * Admin - Coupon Management
 * Create, edit, and manage discount coupons
 */

session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'admin') {
    header("Location: ../login/login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Admin';

require_once(dirname(__FILE__).'/../classes/coupon_class.php');
$coupon_handler = new Coupon();
$coupons = $coupon_handler->getAllCoupons();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Coupons - Admin Panel</title>
    
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

    <?php include '../views/includes/navbar.php'; ?>

    <div class="flex">
        <?php include '../views/includes/sidebar.php'; ?>

        <main class="flex-1 p-6 lg:p-8">
            <div class="flex flex-wrap justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Coupon Management</h1>
                    <p class="text-gray-600">Create and manage discount coupons</p>
                </div>
                <button onclick="openCreateModal()" 
                        class="px-4 py-2 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors">
                    <i class="fas fa-plus mr-2"></i>Create Coupon
                </button>
            </div>

            <!-- Coupons Table -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Code</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Discount</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Min Order</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Usage</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Validity</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if ($coupons && count($coupons) > 0): ?>
                                <?php foreach ($coupons as $coupon): 
                                    $is_expired = $coupon['valid_until'] && strtotime($coupon['valid_until']) < time();
                                    $is_maxed = $coupon['usage_limit'] !== null && $coupon['usage_count'] >= $coupon['usage_limit'];
                                ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-mono font-bold text-primary"><?php echo htmlspecialchars($coupon['coupon_code']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($coupon['description'] ?? ''); ?></div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($coupon['discount_type'] === 'percentage'): ?>
                                                <span class="text-lg font-bold text-green-600"><?php echo $coupon['discount_value']; ?>%</span>
                                                <?php if ($coupon['max_discount_amount']): ?>
                                                    <div class="text-xs text-gray-500">Max: GHS <?php echo number_format($coupon['max_discount_amount'], 2); ?></div>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-lg font-bold text-green-600">GHS <?php echo number_format($coupon['discount_value'], 2); ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="text-gray-700">GHS <?php echo number_format($coupon['min_order_amount'], 2); ?></span>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="font-medium"><?php echo $coupon['usage_count']; ?></span>
                                            <?php if ($coupon['usage_limit']): ?>
                                                <span class="text-gray-500">/ <?php echo $coupon['usage_limit']; ?></span>
                                            <?php else: ?>
                                                <span class="text-gray-500">/ ∞</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if ($coupon['valid_until']): ?>
                                                <div class="<?php echo $is_expired ? 'text-red-600' : 'text-gray-700'; ?>">
                                                    <?php echo date('M d, Y', strtotime($coupon['valid_until'])); ?>
                                                </div>
                                                <?php if ($is_expired): ?>
                                                    <span class="text-xs text-red-500">Expired</span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-gray-500">No expiry</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <?php if (!$coupon['is_active']): ?>
                                                <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">Inactive</span>
                                            <?php elseif ($is_expired): ?>
                                                <span class="px-2 py-1 bg-red-100 text-red-600 rounded-full text-xs font-medium">Expired</span>
                                            <?php elseif ($is_maxed): ?>
                                                <span class="px-2 py-1 bg-orange-100 text-orange-600 rounded-full text-xs font-medium">Maxed Out</span>
                                            <?php else: ?>
                                                <span class="px-2 py-1 bg-green-100 text-green-600 rounded-full text-xs font-medium">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex gap-2">
                                                <button onclick="editCoupon(<?php echo htmlspecialchars(json_encode($coupon)); ?>)"
                                                        class="p-2 text-blue-600 hover:bg-blue-50 rounded-lg transition-colors">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <button onclick="toggleCouponStatus(<?php echo $coupon['coupon_id']; ?>)"
                                                        class="p-2 <?php echo $coupon['is_active'] ? 'text-orange-600 hover:bg-orange-50' : 'text-green-600 hover:bg-green-50'; ?> rounded-lg transition-colors"
                                                        title="<?php echo $coupon['is_active'] ? 'Deactivate' : 'Activate'; ?>">
                                                    <i class="fas fa-<?php echo $coupon['is_active'] ? 'pause' : 'play'; ?>"></i>
                                                </button>
                                                <button onclick="deleteCoupon(<?php echo $coupon['coupon_id']; ?>, '<?php echo htmlspecialchars($coupon['coupon_code']); ?>')"
                                                        class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-gray-500">
                                        <i class="fas fa-ticket-alt text-4xl mb-3 text-gray-300"></i>
                                        <p>No coupons created yet</p>
                                        <button onclick="openCreateModal()" class="mt-3 text-primary hover:underline">
                                            Create your first coupon
                                        </button>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <!-- Create/Edit Coupon Modal -->
    <div id="couponModal" class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200">
                <h3 id="modalTitle" class="text-xl font-bold text-gray-900">Create Coupon</h3>
            </div>
            
            <form id="couponForm" class="p-6 space-y-4">
                <input type="hidden" id="coupon_id" name="coupon_id">
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Coupon Code *</label>
                    <input type="text" id="coupon_code_input" name="coupon_code" required
                           placeholder="e.g., WELCOME20"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary uppercase">
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <input type="text" id="description" name="description"
                           placeholder="e.g., Welcome discount for new users"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Type *</label>
                        <select id="discount_type" name="discount_type" required onchange="toggleMaxDiscount()"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                            <option value="percentage">Percentage (%)</option>
                            <option value="fixed">Fixed Amount (GHS)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Discount Value *</label>
                        <input type="number" id="discount_value" name="discount_value" required min="0" step="0.01"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Order (GHS)</label>
                        <input type="number" id="min_order_amount" name="min_order_amount" min="0" step="0.01" value="0"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    <div id="maxDiscountDiv">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Max Discount (GHS)</label>
                        <input type="number" id="max_discount_amount" name="max_discount_amount" min="0" step="0.01"
                               placeholder="Optional"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Usage Limit</label>
                        <input type="number" id="usage_limit" name="usage_limit" min="1"
                               placeholder="Unlimited"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Per User Limit</label>
                        <input type="number" id="per_user_limit" name="per_user_limit" min="1" value="1"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Valid Until</label>
                    <input type="datetime-local" id="valid_until" name="valid_until"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                </div>
                
                <div class="flex items-center">
                    <input type="checkbox" id="is_active" name="is_active" checked
                           class="w-4 h-4 text-primary border-gray-300 rounded focus:ring-primary">
                    <label for="is_active" class="ml-2 text-sm text-gray-700">Active</label>
                </div>
            </form>
            
            <div class="p-6 border-t border-gray-200 flex justify-end gap-3">
                <button type="button" onclick="closeModal()"
                        class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                    Cancel
                </button>
                <button type="button" onclick="saveCoupon()"
                        class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                    Save Coupon
                </button>
            </div>
        </div>
    </div>

    <script>
        function openCreateModal() {
            document.getElementById('modalTitle').textContent = 'Create Coupon';
            document.getElementById('couponForm').reset();
            document.getElementById('coupon_id').value = '';
            document.getElementById('coupon_code_input').readOnly = false;
            document.getElementById('couponModal').classList.remove('hidden');
            document.getElementById('couponModal').classList.add('flex');
        }
        
        function closeModal() {
            document.getElementById('couponModal').classList.add('hidden');
            document.getElementById('couponModal').classList.remove('flex');
        }
        
        function toggleMaxDiscount() {
            const type = document.getElementById('discount_type').value;
            const maxDiv = document.getElementById('maxDiscountDiv');
            maxDiv.style.display = type === 'percentage' ? 'block' : 'none';
        }
        
        function editCoupon(coupon) {
            document.getElementById('modalTitle').textContent = 'Edit Coupon';
            document.getElementById('coupon_id').value = coupon.coupon_id;
            document.getElementById('coupon_code_input').value = coupon.coupon_code;
            document.getElementById('coupon_code_input').readOnly = true;
            document.getElementById('description').value = coupon.description || '';
            document.getElementById('discount_type').value = coupon.discount_type;
            document.getElementById('discount_value').value = coupon.discount_value;
            document.getElementById('min_order_amount').value = coupon.min_order_amount;
            document.getElementById('max_discount_amount').value = coupon.max_discount_amount || '';
            document.getElementById('usage_limit').value = coupon.usage_limit || '';
            document.getElementById('per_user_limit').value = coupon.per_user_limit;
            document.getElementById('is_active').checked = coupon.is_active == 1;
            
            if (coupon.valid_until) {
                const date = new Date(coupon.valid_until);
                document.getElementById('valid_until').value = date.toISOString().slice(0, 16);
            }
            
            toggleMaxDiscount();
            document.getElementById('couponModal').classList.remove('hidden');
            document.getElementById('couponModal').classList.add('flex');
        }
        
        function saveCoupon() {
            const form = document.getElementById('couponForm');
            const formData = new FormData(form);
            const data = Object.fromEntries(formData);
            data.is_active = document.getElementById('is_active').checked ? 1 : 0;
            
            const couponId = data.coupon_id;
            const url = couponId ? '../actions/update_coupon_action.php' : '../actions/create_coupon_action.php';
            
            fetch(url, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to save coupon');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to save coupon');
            });
        }
        
        function toggleCouponStatus(couponId) {
            fetch('../actions/toggle_coupon_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ coupon_id: couponId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to toggle status');
                }
            });
        }
        
        function deleteCoupon(couponId, code) {
            if (!confirm(`Are you sure you want to delete coupon "${code}"?`)) return;
            
            fetch('../actions/delete_coupon_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ coupon_id: couponId })
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || 'Failed to delete coupon');
                }
            });
        }
    </script>
</body>
</html>
