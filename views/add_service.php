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

// Get all providers (users who can provide services)
$providers = $db->db_fetch_all("SELECT user_id, first_name, last_name, email FROM users WHERE user_role IN ('alumni', 'admin') ORDER BY first_name, last_name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Service - Admin | AlumniConnect</title>
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
                        <a href="../admin/services.php" class="hover:text-primary transition-colors">Services</a>
                        <i class="fas fa-chevron-right mx-2 text-xs"></i>
                        <span class="text-gray-900">Add New Service</span>
                    </nav>
                    <h1 class="text-3xl font-bold text-gray-900">
                        <i class="fas fa-plus-circle text-primary mr-3"></i>Add New Service
                    </h1>
                </div>
                <a href="../admin/services.php" class="inline-flex items-center px-4 py-2.5 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Services
                </a>
            </div>

            <!-- Alert Container -->
            <div id="alertContainer" class="hidden mb-6">
                <div id="alertMessage" class="p-4 rounded-lg"></div>
            </div>

            <!-- Add Service Form -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-200">
                    <h2 class="text-xl font-bold text-gray-900">Service Information</h2>
                    <p class="text-gray-600 mt-1">Fill in the details below to create a new service</p>
                </div>
                
                <form id="addServiceForm" class="p-6" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <!-- Left Column -->
                        <div class="space-y-6">
                            <!-- Service Name -->
                            <div>
                                <label for="service_name" class="block text-sm font-medium text-gray-700 mb-2">
                                    Service Name <span class="text-red-500">*</span>
                                </label>
                                <input type="text" id="service_name" name="service_name" required
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="e.g., Career Mentorship Session">
                            </div>

                            <!-- Service Type -->
                            <div>
                                <label for="service_type" class="block text-sm font-medium text-gray-700 mb-2">
                                    Service Type <span class="text-red-500">*</span>
                                </label>
                                <select id="service_type" name="service_type" required
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">Select service type</option>
                                    <option value="job_posting">Job Posting</option>
                                    <option value="mentorship">Mentorship</option>
                                    <option value="event">Event</option>
                                    <option value="premium_feature">Premium Feature</option>
                                </select>
                            </div>

                            <!-- Category -->
                            <div>
                                <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                    Category
                                </label>
                                <select id="category" name="category"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">Select category</option>
                                    <option value="technology">Technology</option>
                                    <option value="business">Business</option>
                                    <option value="engineering">Engineering</option>
                                    <option value="healthcare">Healthcare</option>
                                    <option value="education">Education</option>
                                    <option value="finance">Finance</option>
                                    <option value="marketing">Marketing</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            <!-- Price -->
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                                    Price (GHS) <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500">GHS</span>
                                    <input type="number" id="price" name="price" required min="0" step="0.01"
                                           class="w-full pl-14 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                           placeholder="0.00">
                                </div>
                            </div>

                            <!-- Duration -->
                            <div id="durationField">
                                <label for="duration" class="block text-sm font-medium text-gray-700 mb-2">
                                    Duration <span class="text-gray-400">(minutes for mentorship, days for job postings)</span>
                                </label>
                                <input type="number" id="duration" name="duration" min="1"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="e.g., 60">
                            </div>
                        </div>

                        <!-- Right Column -->
                        <div class="space-y-6">
                            <!-- Provider -->
                            <div>
                                <label for="provider_id" class="block text-sm font-medium text-gray-700 mb-2">
                                    Service Provider
                                </label>
                                <select id="provider_id" name="provider_id"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all">
                                    <option value="">No specific provider</option>
                                    <?php if ($providers): ?>
                                        <?php foreach ($providers as $provider): ?>
                                            <option value="<?php echo $provider['user_id']; ?>">
                                                <?php echo htmlspecialchars($provider['first_name'] . ' ' . $provider['last_name'] . ' (' . $provider['email'] . ')'); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>

                            <!-- Location -->
                            <div>
                                <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                                    Location
                                </label>
                                <input type="text" id="location" name="location"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="e.g., Accra, Ghana or Online">
                            </div>

                            <!-- Stock Quantity (for events) -->
                            <div id="stockField" class="hidden">
                                <label for="stock_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Stock/Tickets Available
                                </label>
                                <input type="number" id="stock_quantity" name="stock_quantity" min="0"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all"
                                       placeholder="e.g., 100">
                            </div>

                            <!-- Service Image -->
                            <div>
                                <label for="service_image" class="block text-sm font-medium text-gray-700 mb-2">
                                    Service Image
                                </label>
                                <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-primary transition-colors">
                                    <input type="file" id="service_image" name="service_image" accept="image/*" class="hidden">
                                    <div id="imagePreviewContainer" class="hidden mb-4">
                                        <img id="imagePreview" class="max-h-40 mx-auto rounded-lg" alt="Preview">
                                    </div>
                                    <div id="uploadPlaceholder">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-3"></i>
                                        <p class="text-gray-600 mb-2">Click to upload or drag and drop</p>
                                        <p class="text-sm text-gray-400">PNG, JPG, GIF up to 5MB</p>
                                    </div>
                                    <button type="button" onclick="document.getElementById('service_image').click()" 
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
                                    Service is active and visible to users
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Description - Full Width -->
                    <div class="mt-6">
                        <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                            Description <span class="text-red-500">*</span>
                        </label>
                        <textarea id="description" name="description" rows="5" required
                                  class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary/20 focus:border-primary transition-all resize-none"
                                  placeholder="Provide a detailed description of the service..."></textarea>
                        <p class="mt-2 text-sm text-gray-500">Minimum 20 characters. Be descriptive to help users understand what they're getting.</p>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-end border-t border-gray-200 pt-6">
                        <a href="../admin/services.php" 
                           class="px-6 py-3 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors font-medium text-center">
                            Cancel
                        </a>
                        <button type="submit" id="submitBtn"
                                class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium flex items-center justify-center">
                            <i class="fas fa-plus mr-2"></i>Add Service
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>

    <script>
        // Show/hide fields based on service type
        document.getElementById('service_type').addEventListener('change', function() {
            const stockField = document.getElementById('stockField');
            const durationField = document.getElementById('durationField');
            
            if (this.value === 'event') {
                stockField.classList.remove('hidden');
            } else {
                stockField.classList.add('hidden');
            }
            
            if (this.value === 'mentorship' || this.value === 'job_posting') {
                durationField.classList.remove('hidden');
            } else if (this.value === 'event' || this.value === 'premium_feature') {
                durationField.classList.add('hidden');
            }
        });

        // Image preview
        document.getElementById('service_image').addEventListener('change', function(e) {
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
        document.getElementById('addServiceForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = document.getElementById('submitBtn');
            const originalBtnText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            
            // Add is_active checkbox value
            formData.set('is_active', document.getElementById('is_active').checked ? '1' : '0');

            fetch('../actions/add_service_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const alertContainer = document.getElementById('alertContainer');
                const alertMessage = document.getElementById('alertMessage');
                
                alertContainer.classList.remove('hidden');
                
                if (data.success) {
                    alertMessage.className = 'p-4 rounded-lg bg-green-100 text-green-700 border border-green-200';
                    alertMessage.innerHTML = '<i class="fas fa-check-circle mr-2"></i>' + data.message;
                    
                    // Redirect to services list after 2 seconds
                    setTimeout(() => {
                        window.location.href = '../admin/services.php';
                    }, 2000);
                } else {
                    alertMessage.className = 'p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
                    alertMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>' + data.message;
                    
                    submitBtn.innerHTML = originalBtnText;
                    submitBtn.disabled = false;
                }
                
                // Scroll to top to show alert
                window.scrollTo({ top: 0, behavior: 'smooth' });
            })
            .catch(error => {
                console.error('Error:', error);
                const alertContainer = document.getElementById('alertContainer');
                const alertMessage = document.getElementById('alertMessage');
                
                alertContainer.classList.remove('hidden');
                alertMessage.className = 'p-4 rounded-lg bg-red-100 text-red-700 border border-red-200';
                alertMessage.innerHTML = '<i class="fas fa-exclamation-circle mr-2"></i>An error occurred. Please try again.';
                
                submitBtn.innerHTML = originalBtnText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>
</html>
