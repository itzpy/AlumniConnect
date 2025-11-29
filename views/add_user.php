<?php
session_start();
require_once '../settings/core.php';

// Authorization check - admin only
if (!isset($_SESSION['logged_in']) || $_SESSION['user_type'] !== 'admin') {
    header('Location: ../login/login.php');
    exit;
}

$user_type = $_SESSION['user_type'];
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'Admin';

require_once '../settings/db_class.php';
require_once '../classes/cart_class.php';

$db = new db_connection();
$cart = new Cart();
$cart_count = 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New User - Admin | AlumniConnect</title>
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
                        <span class="text-gray-900">Add New User</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-user-plus text-primary mr-3"></i>Add New User
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

            <!-- Add User Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">User Information</h2>
                    <p class="text-gray-600 mt-1">Fill in the details below to create a new user account</p>
                </div>
                
                <form id="addUserForm" class="p-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- First Name -->
                            <div>
                                <label for="first_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    First Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="first_name" name="first_name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="Enter first name">
                            </div>

                            <!-- Last Name -->
                            <div>
                                <label for="last_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Last Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="last_name" name="last_name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="Enter last name">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Email Address <span class="text-red-500">*</span>
                                </label>
                                <input type="email" id="email" name="email" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="user@example.com">
                            </div>

                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="password" name="password" required minlength="6"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12"
                                           placeholder="Minimum 6 characters">
                                    <button type="button" onclick="togglePassword('password')" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                        <i class="fas fa-eye" id="password-icon"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                    Confirm Password <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all pr-12"
                                           placeholder="Confirm password">
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
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="student">Student</option>
                                    <option value="alumni">Alumni</option>
                                    <option value="admin">Administrator</option>
                                </select>
                            </div>

                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Phone Number
                                </label>
                                <input type="tel" id="phone" name="phone"
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
                                    <div id="imagePreviewContainer" class="hidden mb-4">
                                        <img id="imagePreview" class="w-24 h-24 rounded-full mx-auto object-cover" alt="Preview">
                                    </div>
                                    <div id="uploadPlaceholder">
                                        <i class="fas fa-user-circle text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Click to upload profile image</p>
                                        <p class="text-sm text-gray-400">PNG, JPG up to 2MB</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('profile_image').click()" 
                                            class="mt-4 px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                                        Choose File
                                    </button>
                                </div>
                            </div>

                            <!-- Active Status -->
                            <div class="flex items-center">
                                <input type="checkbox" id="is_active" name="is_active" checked
                                       class="w-5 h-5 text-primary border-gray-300 rounded focus:ring-primary">
                                <label for="is_active" class="ml-3 text-sm font-medium text-gray-700">
                                    Account is active
                                </label>
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
                                  placeholder="Brief bio about the user..."></textarea>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end border-t border-gray-200 pt-6">
                        <a href="../admin/users.php" 
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-center">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium flex items-center justify-center">
                            <i class="fas fa-user-plus mr-2"></i>Create User
                        </button>
                    </div>
                </form>
            </div>
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
        document.getElementById('addUserForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate passwords match
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (password !== confirmPassword) {
                showAlert('error', 'Passwords do not match!');
                return;
            }
            
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Creating...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            formData.set('is_active', document.getElementById('is_active').checked ? '1' : '0');

            fetch('../actions/add_user_action.php', {
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
