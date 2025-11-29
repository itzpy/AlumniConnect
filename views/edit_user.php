<?php
session_start();
require_once '../settings/core.php';

// Authorization check - admin only
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}

require_once '../settings/db_class.php';
require_once '../classes/cart_class.php';

$db = new db_connection();
$cart = new Cart();
$cart_count = 0;

$user_type = $_SESSION['user_type'];
$current_user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Admin';

// Get user ID from URL
$edit_user_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($edit_user_id <= 0) {
    header('Location: ../admin/users.php');
    exit;
}

// Fetch user data
$user_data = $db->db_fetch_one("SELECT * FROM users WHERE user_id = $edit_user_id");

if (!$user_data) {
    $_SESSION['error'] = 'User not found.';
    header('Location: ../admin/users.php');
    exit;
}

// Check if editing self
$is_self = ($edit_user_id == $current_user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Admin | AlumniConnect</title>
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
    <?php include 'includes/navbar.php'; ?>
    
    <div class="flex min-h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <main class="flex-1 min-w-0 p-6 lg:p-8 overflow-x-auto">
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
                <div class="mb-4 sm:mb-0">
                    <nav class="flex items-center text-sm text-gray-500 mb-2">
                        <a href="../admin/users.php" class="hover:text-primary transition-colors">Users</a>
                        <i class="fas fa-chevron-right mx-2 text-xs"></i>
                        <span class="text-gray-900">Edit User</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-user-edit text-primary mr-3"></i>Edit User
                    </h1>
                </div>
                <a href="../admin/users.php" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Users
                </a>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer" class="hidden mb-6">
                <div id="alertMessage" class="p-4 rounded-lg"></div>
            </div>

            <?php if ($is_self): ?>
            <div class="mb-6 p-4 bg-yellow-100 border border-yellow-200 text-yellow-700 rounded-lg flex items-center">
                <i class="fas fa-exclamation-triangle mr-3"></i>
                You are editing your own account. Some options may be restricted.
            </div>
            <?php endif; ?>

            <!-- Edit User Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <div class="flex items-center space-x-4">
                        <div class="w-16 h-16 bg-gradient-to-br from-primary to-primary-dark rounded-full flex items-center justify-center text-white text-2xl font-bold">
                            <?php if (!empty($user_data['profile_image'])): ?>
                                <img src="../uploads/profiles/<?php echo htmlspecialchars($user_data['profile_image']); ?>" 
                                     class="w-16 h-16 rounded-full object-cover" alt="Profile">
                            <?php else: ?>
                                <?php echo strtoupper(substr($user_data['first_name'], 0, 1)); ?>
                            <?php endif; ?>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">
                                <?php echo htmlspecialchars($user_data['first_name'] . ' ' . $user_data['last_name']); ?>
                            </h2>
                            <p class="text-gray-600"><?php echo htmlspecialchars($user_data['email']); ?></p>
                        </div>
                    </div>
                </div>
                
                <form id="editUserForm" class="p-6" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo $edit_user_id; ?>">
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" required
                                       value="<?php echo htmlspecialchars($user_data['first_name']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" required
                                       value="<?php echo htmlspecialchars($user_data['last_name']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       value="<?php echo htmlspecialchars($user_data['email']); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                            </div>

                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    New Password <span class="text-gray-400">(leave blank to keep current)</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="new_password" name="new_password" minlength="6"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12"
                                           placeholder="Enter new password">
                                    <button type="button" onclick="togglePassword('new_password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="new_password-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm New Password
                                </label>
                                <div class="relative">
                                    <input type="password" id="confirm_password" name="confirm_password" minlength="6"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12"
                                           placeholder="Confirm new password">
                                    <button type="button" onclick="togglePassword('confirm_password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="confirm_password-icon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- User Role -->
                            <div>
                                <label for="user_role" class="block text-sm font-medium text-gray-700 mb-2">
                                    User Role <span class="text-red-500">*</span>
                                </label>
                                <select id="user_role" name="user_role" required
                                        <?php echo $is_self ? 'disabled' : ''; ?>
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all <?php echo $is_self ? 'bg-gray-100' : ''; ?>">
                                    <option value="student" <?php echo $user_data['user_role'] === 'student' ? 'selected' : ''; ?>>Student</option>
                                    <option value="alumni" <?php echo $user_data['user_role'] === 'alumni' ? 'selected' : ''; ?>>Alumni</option>
                                    <option value="admin" <?php echo $user_data['user_role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                                </select>
                                <?php if ($is_self): ?>
                                <input type="hidden" name="user_role" value="<?php echo $user_data['user_role']; ?>">
                                <p class="mt-1 text-sm text-yellow-600">You cannot change your own role.</p>
                                <?php endif; ?>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel" id="phone" name="phone"
                                       value="<?php echo htmlspecialchars($user_data['phone'] ?? ''); ?>"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="+233 XX XXX XXXX">
                            </div>

                            <!-- Profile Image -->
                            <div>
                                <label for="profile_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Profile Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                    <input type="file" id="profile_image" name="profile_image" accept="image/*" class="hidden">
                                    <div id="imagePreviewContainer" class="<?php echo empty($user_data['profile_image']) ? 'hidden' : ''; ?> mb-4">
                                        <img id="imagePreview" 
                                             src="<?php echo !empty($user_data['profile_image']) ? '../uploads/profiles/' . htmlspecialchars($user_data['profile_image']) : ''; ?>" 
                                             class="w-24 h-24 rounded-full mx-auto object-cover" alt="Preview">
                                    </div>
                                    <div id="uploadPlaceholder" class="<?php echo !empty($user_data['profile_image']) ? 'hidden' : ''; ?>">
                                        <i class="fas fa-user-circle text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Click to upload new image</p>
                                        <p class="text-sm text-gray-400">PNG, JPG up to 2MB</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('profile_image').click()" 
                                            class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                        <?php echo !empty($user_data['profile_image']) ? 'Change Image' : 'Choose File'; ?>
                                    </button>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" 
                                       <?php echo $user_data['is_active'] ? 'checked' : ''; ?>
                                       <?php echo $is_self ? 'disabled' : ''; ?>
                                       class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                    Account is active
                                </label>
                                <?php if ($is_self): ?>
                                <input type="hidden" name="is_active" value="1">
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Bio - Full Width -->
                    <div class="mt-6">
                        <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                            Bio
                        </label>
                        <textarea id="bio" name="bio" rows="4"
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                  placeholder="Brief bio about the user..."><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
                    </div>

                    <!-- Account Info -->
                    <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                        <h3 class="font-medium text-gray-900 mb-3">Account Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <p class="text-gray-500">User ID</p>
                                <p class="font-medium text-gray-900">#<?php echo $user_data['user_id']; ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Account Created</p>
                                <p class="font-medium text-gray-900"><?php echo date('M d, Y', strtotime($user_data['date_created'])); ?></p>
                            </div>
                            <div>
                                <p class="text-gray-500">Last Login</p>
                                <p class="font-medium text-gray-900">
                                    <?php echo $user_data['last_login'] ? date('M d, Y H:i', strtotime($user_data['last_login'])) : 'Never'; ?>
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end border-t border-gray-200 pt-6">
                        <a href="../admin/users.php" 
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-center">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium flex items-center justify-center">
                            <i class="fas fa-save mr-2"></i>Save Changes
                        </button>
                    </div>
                </form>
            </div>

            <?php if (!$is_self): ?>
            <!-- Danger Zone -->
            <div class="mt-8 bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden">
                <div class="p-6 border-b border-red-200 bg-red-50">
                    <h2 class="text-xl font-bold text-red-700">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                    </h2>
                </div>
                <div class="p-6">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                        <div class="mb-4 sm:mb-0">
                            <h3 class="font-medium text-gray-900">Delete User Account</h3>
                            <p class="text-gray-600 text-sm">This action cannot be undone. All user data will be permanently deleted.</p>
                        </div>
                        <button type="button" onclick="confirmDelete()" 
                                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-medium">
                            <i class="fas fa-trash-alt mr-2"></i>Delete User
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </main>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(fieldId) {
            const field = document.getElementById(fieldId);
            const icon = document.getElementById(fieldId + '-icon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Image preview
        document.getElementById('profile_image').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('hidden');
                    document.getElementById('uploadPlaceholder').classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        // Form submission
        document.getElementById('editUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate passwords match if changing password
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (newPassword && newPassword !== confirmPassword) {
                showAlert('error', 'Passwords do not match!');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Saving...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            if (!document.getElementById('is_active').disabled) {
                formData.set('is_active', document.getElementById('is_active').checked ? '1' : '0');
            }

            fetch('../actions/edit_user_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        window.location.href = '../admin/users.php';
                    }, 2000);
                } else {
                    showAlert('error', data.message);
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('error', 'An error occurred. Please try again.');
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });

        function confirmDelete() {
            if (confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
                fetch('../actions/delete_user_action.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'user_id=<?php echo $edit_user_id; ?>'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('User deleted successfully!');
                        window.location.href = '../admin/users.php';
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        function showAlert(type, message) {
            const alertContainer = document.getElementById('alertContainer');
            const alertMessage = document.getElementById('alertMessage');
            
            alertContainer.classList.remove('hidden');
            
            if (type === 'success') {
                alertMessage.className = 'p-4 rounded-lg bg-green-100 text-green-700 border border-green-200';
                alertMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + message;
            } else {
                alertMessage.className = 'p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
                alertMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + message;
            }
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    </script>
</body>
</html>
