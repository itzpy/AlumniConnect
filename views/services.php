<?php
/**
 * Services Catalog Page
 * Browse all services: job postings, mentorship sessions, events, premium features
 * Includes search and filtering functionality
 */

// Start session and check authentication
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/login.php");
    exit();
}

// Get user details from session
$user_id = $_SESSION['user_id'];
$user_name = $_SESSION['name'] ?? 'User';
$user_type = $_SESSION['user_type'] ?? 'student';

// Include service class
require_once(dirname(__FILE__).'/../classes/service_class.php');
require_once(dirname(__FILE__).'/../classes/cart_class.php');

$service = new Service();
$cart = new Cart();

// Get filters from query parameters (treat empty strings as null)
$service_type = !empty($_GET['type']) ? $_GET['type'] : null;
$category = !empty($_GET['category']) ? $_GET['category'] : null;
$search = !empty($_GET['search']) ? $_GET['search'] : null;
$min_price = !empty($_GET['min_price']) ? floatval($_GET['min_price']) : null;
$max_price = !empty($_GET['max_price']) ? floatval($_GET['max_price']) : null;

// Get all services with filters applied
$services = $service->getAllServices($service_type, $category, $search, $min_price, $max_price);

// Get categories for filter dropdown
$categories = $service->getCategories($service_type);

// Get cart count for display
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse Services - Alumni Connect</title>
    
    <!-- Tailwind CSS -->
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
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Inter', sans-serif; }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</head>
<body class="bg-gray-50">

    <?php include 'includes/navbar.php'; ?>

    <div class="flex">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Page Header -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Browse Services</h1>
                <p class="text-gray-600">Explore job postings, mentorship sessions, events, and premium features</p>
            </div>

            <!-- AI Recommendations Section (collapsed by default if filters are active) -->
            <?php if (!$service_type && !$category && !$search && !$min_price && !$max_price): ?>
            <div class="mb-6 bg-gradient-to-r from-purple-50 to-pink-50 rounded-xl border border-purple-100 p-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-magic text-white text-sm"></i>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900">Recommended for You</h2>
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">AI Powered</span>
                    </div>
                </div>
                
                <div id="services-recommendations" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Loading skeleton -->
                    <div class="bg-white rounded-lg p-3 animate-pulse">
                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-3/4 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-lg p-3 animate-pulse hidden md:block">
                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-3/4 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-lg p-3 animate-pulse hidden lg:block">
                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-3/4 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-lg p-3 animate-pulse hidden lg:block">
                        <div class="w-full h-24 bg-gray-200 rounded-lg mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-3/4 mb-1"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Search and Filter Section -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
                <form method="GET" action="" class="space-y-4">
                    <!-- Search Bar -->
                    <div class="flex flex-col md:flex-row gap-4">
                        <div class="flex-1">
                            <input type="text" name="search" placeholder="Search services..." 
                                   value="<?php echo htmlspecialchars($search ?? ''); ?>"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary transition-colors">
                        </div>
                        <button type="submit" class="px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                            <i class="fas fa-search mr-2"></i>Search
                        </button>
                    </div>

                    <!-- Filters -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <!-- Service Type Filter -->
                        <select name="type" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary" onchange="this.form.submit()">
                            <option value="">All Types</option>
                            <option value="job_posting" <?php echo $service_type === 'job_posting' ? 'selected' : ''; ?>>Job Postings</option>
                            <option value="mentorship" <?php echo $service_type === 'mentorship' ? 'selected' : ''; ?>>Mentorship</option>
                            <option value="event" <?php echo $service_type === 'event' ? 'selected' : ''; ?>>Events</option>
                            <option value="premium_feature" <?php echo $service_type === 'premium_feature' ? 'selected' : ''; ?>>Premium Features</option>
                        </select>

                        <!-- Category Filter -->
                        <select name="category" class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php if ($categories): foreach ($categories as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" 
                                        <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                            <?php endforeach; endif; ?>
                        </select>

                        <!-- Min Price -->
                        <input type="number" name="min_price" placeholder="Min Price (GHS)" 
                               value="<?php echo $min_price ?? ''; ?>"
                               class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary" 
                               min="0" step="0.01">

                        <!-- Max Price -->
                        <input type="number" name="max_price" placeholder="Max Price (GHS)" 
                               value="<?php echo $max_price ?? ''; ?>"
                               class="px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:border-primary" 
                               min="0" step="0.01">
                    </div>

                    <!-- Reset Button -->
                    <div class="flex justify-end">
                        <a href="services.php" class="px-4 py-2 text-gray-600 hover:text-gray-800 transition-colors">
                            <i class="fas fa-redo mr-2"></i>Reset Filters
                        </a>
                    </div>
                </form>
            </div>

            <!-- Results Count -->
            <div class="mb-4">
                <p class="text-gray-600">
                    <?php echo $services ? count($services) : 0; ?> service(s) found
                </p>
            </div>

            <!-- Services Grid -->
            <div id="servicesGrid">
            <?php if ($services && count($services) > 0): ?>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($services as $item): ?>
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer">
                            <!-- Service Image -->
                            <div class="h-48 bg-gradient-to-br from-primary/20 to-primary/5 flex items-center justify-center">
                                <?php if ($item['image_url']): 
                                    // Check if it's a full URL or just a filename
                                    $image_src = (strpos($item['image_url'], 'http') === 0) 
                                        ? $item['image_url'] 
                                        : '../uploads/services/' . $item['image_url'];
                                ?>
                                    <img src="<?php echo htmlspecialchars($image_src); ?>" 
                                         alt="<?php echo htmlspecialchars($item['service_name']); ?>"
                                         class="w-full h-full object-cover">
                                <?php else: ?>
                                    <i class="fas fa-<?php 
                                        echo $item['service_type'] === 'job_posting' ? 'briefcase' : 
                                             ($item['service_type'] === 'mentorship' ? 'user-graduate' : 
                                             ($item['service_type'] === 'event' ? 'calendar' : 'star')); 
                                    ?> text-6xl text-primary/30"></i>
                                <?php endif; ?>
                            </div>

                            <div class="p-6">
                                <!-- Service Type Badge -->
                                <span class="inline-block px-3 py-1 text-xs font-medium rounded-full mb-3
                                    <?php 
                                        echo $item['service_type'] === 'job_posting' ? 'bg-blue-100 text-blue-800' : 
                                             ($item['service_type'] === 'mentorship' ? 'bg-green-100 text-green-800' : 
                                             ($item['service_type'] === 'event' ? 'bg-purple-100 text-purple-800' : 'bg-yellow-100 text-yellow-800')); 
                                    ?>">
                                    <?php echo ucwords(str_replace('_', ' ', $item['service_type'])); ?>
                                </span>

                                <!-- Service Name -->
                                <h3 class="text-lg font-bold text-gray-900 mb-2">
                                    <?php echo htmlspecialchars($item['service_name']); ?>
                                </h3>

                                <!-- Description -->
                                <p class="text-sm text-gray-600 mb-4 line-clamp-3">
                                    <?php echo htmlspecialchars(substr($item['description'], 0, 120)) . (strlen($item['description']) > 120 ? '...' : ''); ?>
                                </p>

                                <!-- Category & Location -->
                                <div class="flex flex-wrap gap-2 mb-4 text-xs text-gray-500">
                                    <?php if ($item['category']): ?>
                                        <span><i class="fas fa-tag mr-1"></i><?php echo htmlspecialchars($item['category']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['location']): ?>
                                        <span><i class="fas fa-map-marker-alt mr-1"></i><?php echo htmlspecialchars($item['location']); ?></span>
                                    <?php endif; ?>
                                    <?php if ($item['duration']): ?>
                                        <span><i class="fas fa-clock mr-1"></i><?php echo $item['duration']; ?> 
                                            <?php echo $item['service_type'] === 'mentorship' ? 'mins' : 'days'; ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Stock Info for Events -->
                                <?php if ($item['stock_quantity'] !== null): ?>
                                    <div class="mb-4">
                                        <span class="text-sm <?php echo $item['stock_quantity'] > 10 ? 'text-green-600' : 'text-orange-600'; ?>">
                                            <i class="fas fa-ticket-alt mr-1"></i>
                                            <?php echo $item['stock_quantity']; ?> tickets available
                                        </span>
                                    </div>
                                <?php endif; ?>

                                <!-- Price and Action -->
                                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                                    <div>
                                        <span class="text-2xl font-bold text-primary">GHS <?php echo number_format($item['price'], 2); ?></span>
                                    </div>
                                    <button onclick="addToCart(<?php echo $item['service_id']; ?>)" 
                                            class="px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors flex items-center gap-2">
                                        <i class="fas fa-cart-plus"></i>
                                        Add to Cart
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- No Results -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center">
                    <i class="fas fa-search text-6xl text-gray-300 mb-4"></i>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">No services found</h3>
                    <p class="text-gray-600 mb-6">Try adjusting your filters or search terms</p>
                    <a href="services.php" class="inline-block px-6 py-3 bg-primary text-white rounded-lg hover:bg-primary/90 transition-colors">
                        Reset Filters
                    </a>
                </div>
            <?php endif; ?>
            </div>
        </main>
    </div>

    <!-- Toast Container -->
    <div id="toast-container" class="fixed bottom-4 right-4 z-50 space-y-2"></div>

    <script>
        // Load recommendations on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadServicesRecommendations();
        });
        
        /**
         * Load AI recommendations for services page
         */
        function loadServicesRecommendations() {
            const container = document.getElementById('services-recommendations');
            if (!container) return;
            
            fetch('../actions/get_recommendations_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'personalized', limit: 4 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.recommendations.length > 0) {
                    container.innerHTML = data.recommendations.map(rec => createMiniRecommendationCard(rec)).join('');
                } else {
                    container.innerHTML = `
                        <div class="col-span-full text-center py-4 text-gray-500 text-sm">
                            <i class="fas fa-lightbulb mr-2"></i>Make a purchase to get personalized recommendations!
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                container.parentElement.style.display = 'none';
            });
        }
        
        /**
         * Create mini recommendation card
         */
        function createMiniRecommendationCard(service) {
            const typeIcons = {
                'mentorship': 'fa-user-graduate',
                'event': 'fa-calendar-alt',
                'job_posting': 'fa-briefcase',
                'premium': 'fa-star'
            };
            const typeColors = {
                'mentorship': 'from-green-400 to-emerald-500',
                'event': 'from-purple-400 to-violet-500',
                'job_posting': 'from-blue-400 to-indigo-500',
                'premium': 'from-amber-400 to-orange-500'
            };
            
            const icon = typeIcons[service.service_type] || 'fa-box';
            const gradient = typeColors[service.service_type] || 'from-gray-400 to-gray-500';
            
            const imageHtml = service.image_url 
                ? `<img src="${service.image_url}" alt="${service.service_name}" class="w-full h-24 object-cover rounded-lg mb-2">`
                : `<div class="w-full h-24 bg-gradient-to-br ${gradient} rounded-lg mb-2 flex items-center justify-center">
                     <i class="fas ${icon} text-2xl text-white/80"></i>
                   </div>`;
            
            return `
                <a href="single_service.php?id=${service.service_id}" 
                   class="bg-white rounded-lg p-3 hover:shadow-md transition-all duration-200 block">
                    ${imageHtml}
                    <h4 class="font-medium text-gray-900 text-sm line-clamp-1">${service.service_name}</h4>
                    <div class="flex items-center justify-between mt-1">
                        <span class="text-primary font-bold text-sm">${service.formatted_price}</span>
                        <span class="text-purple-500 text-xs"><i class="fas fa-magic"></i></span>
                    </div>
                </a>
            `;
        }
        
        /**
         * Show toast notification
         */
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg animate-fadeIn flex items-center gap-2 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>${message}`;
            document.getElementById('toast-container').appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100px)';
                toast.style.transition = 'all 0.3s ease';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        /**
         * Add service to cart via AJAX
         * @param {number} serviceId - The service ID to add
         */
        function addToCart(serviceId) {
            // Send AJAX request to add to cart
            fetch('../actions/add_to_cart_action.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'service_id=' + serviceId + '&quantity=1'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Added to cart successfully!', 'success');
                    // Update cart count in navbar after a brief delay
                    setTimeout(() => location.reload(), 1500);
                } else {
                    showToast(data.message || 'Failed to add to cart', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to add to cart. Please try again.', 'error');
            });
        }
    </script>
</body>
</html>
