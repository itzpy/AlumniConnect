<?php
session_start();
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: ../login/login.php");
    exit();
}

$user_name = $_SESSION['name'] ?? 'User';
$user_email = $_SESSION['email'] ?? '';
$user_type = $_SESSION['user_type'] ?? 'alumni';
$user_id = $_SESSION['user_id'] ?? 0;

// Get cart count for navbar
require_once(dirname(__FILE__).'/../classes/cart_class.php');
$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Alumni Connect</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out; }
        .animate-fadeIn-delay-1 { animation: fadeIn 0.4s ease-out 0.1s both; }
        .animate-fadeIn-delay-2 { animation: fadeIn 0.4s ease-out 0.2s both; }
    </style>
</head>
<body class="bg-gray-50 font-sans">
    <?php include 'includes/navbar.php'; ?>

    <div class="flex min-h-screen">
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <main class="flex-1 p-6 lg:p-8">
            <!-- Welcome Section -->
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Welcome back, <?php echo htmlspecialchars($user_name); ?>!</h1>
                <?php if ($user_type === 'student'): ?>
                    <p class="text-gray-600">Connect with alumni, explore opportunities, and grow your network</p>
                <?php elseif ($user_type === 'alumni'): ?>
                    <p class="text-gray-600">Here's your impact on the alumni network today</p>
                <?php else: ?>
                    <p class="text-gray-600">Platform overview and management dashboard</p>
                <?php endif; ?>
            </div>

            <!-- Stats Cards - Role Specific -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <?php if ($user_type === 'student'): ?>
                    <!-- Student Stats -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-user-graduate text-2xl text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Active</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">42</h3>
                        <p class="text-gray-600 text-sm">Alumni Connections</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-briefcase text-2xl text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">New</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">28</h3>
                        <p class="text-gray-600 text-sm">Job Opportunities</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-hands-helping text-2xl text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Pending</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">5</h3>
                        <p class="text-gray-600 text-sm">Mentorship Requests</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-orange-100 p-3 rounded-lg">
                                <i class="fas fa-calendar text-2xl text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Upcoming</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">3</h3>
                        <p class="text-gray-600 text-sm">Upcoming Events</p>
                    </div>

                <?php elseif ($user_type === 'alumni'): ?>
                    <!-- Alumni Stats -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-users text-2xl text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">+12%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">248</h3>
                        <p class="text-gray-600 text-sm">Connections</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-briefcase text-2xl text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Active</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">15</h3>
                        <p class="text-gray-600 text-sm">Jobs Posted</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-user-graduate text-2xl text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Active</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">8</h3>
                        <p class="text-gray-600 text-sm">Students Mentored</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-orange-100 p-3 rounded-lg">
                                <i class="fas fa-calendar text-2xl text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">This month</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">4</h3>
                        <p class="text-gray-600 text-sm">Events</p>
                    </div>

                <?php else: ?>
                    <!-- Admin Stats with link to admin dashboard -->
                    <a href="../admin/dashboard.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-users text-2xl text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">+8%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">1,248</h3>
                        <p class="text-gray-600 text-sm">Total Users</p>
                    </a>

                    <a href="../admin/services.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-box text-2xl text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Active</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">156</h3>
                        <p class="text-gray-600 text-sm">Platform Services</p>
                    </a>

                    <a href="../admin/orders.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-orange-100 p-3 rounded-lg">
                                <i class="fas fa-shopping-cart text-2xl text-orange-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Pending</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">24</h3>
                        <p class="text-gray-600 text-sm">Orders</p>
                    </a>

                    <a href="../admin/users.php" class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md hover:border-primary transition-all">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Overall</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">94%</h3>
                        <p class="text-gray-600 text-sm">Engagement Rate</p>
                    </a>
                <?php endif; ?>
            </div>

            <!-- Quick Actions - Service Cards -->
            <div class="mb-8 animate-fadeIn">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Events Card -->
                    <a href="events.php" class="group bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl p-6 text-white hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-2xl"></i>
                            </div>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Events & Tickets</h3>
                        <p class="text-white/80 text-sm">Browse upcoming events and purchase tickets</p>
                    </a>

                    <!-- Mentorship Card -->
                    <a href="mentorship.php" class="group bg-gradient-to-br from-green-500 to-green-700 rounded-xl p-6 text-white hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-user-graduate text-2xl"></i>
                            </div>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Find a Mentor</h3>
                        <p class="text-white/80 text-sm">Book 1-on-1 sessions with alumni mentors</p>
                    </a>

                    <!-- Jobs Card -->
                    <a href="jobs.php" class="group bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl p-6 text-white hover:shadow-lg hover:scale-[1.02] transition-all duration-300">
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-briefcase text-2xl"></i>
                            </div>
                            <i class="fas fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                        </div>
                        <h3 class="text-lg font-bold mb-1">Job Board</h3>
                        <p class="text-white/80 text-sm"><?php echo $user_type === 'alumni' ? 'Post jobs and find talent' : 'Discover career opportunities'; ?></p>
                    </a>
                </div>
            </div>

            <!-- AI-Powered Recommendations Section -->
            <?php if ($user_type !== 'admin'): ?>
            <div class="mb-8 animate-fadeIn-delay-1">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-gradient-to-br from-purple-500 to-pink-500 rounded-lg flex items-center justify-center">
                            <i class="fas fa-magic text-white text-sm"></i>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Recommended for You</h2>
                        <span class="px-2 py-0.5 bg-purple-100 text-purple-700 text-xs font-medium rounded-full">AI Powered</span>
                    </div>
                    <a href="services.php" class="text-primary hover:text-primary-dark text-sm font-medium">
                        View All <i class="fas fa-arrow-right ml-1"></i>
                    </a>
                </div>
                
                <div id="recommendations-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Loading skeleton -->
                    <div class="bg-white rounded-xl border border-gray-200 p-4 animate-pulse">
                        <div class="w-full h-32 bg-gray-200 rounded-lg mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4 animate-pulse hidden md:block">
                        <div class="w-full h-32 bg-gray-200 rounded-lg mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4 animate-pulse hidden lg:block">
                        <div class="w-full h-32 bg-gray-200 rounded-lg mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-200 p-4 animate-pulse hidden lg:block">
                        <div class="w-full h-32 bg-gray-200 rounded-lg mb-3"></div>
                        <div class="h-4 bg-gray-200 rounded w-3/4 mb-2"></div>
                        <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Community Feed -->
                <div class="lg:col-span-2 space-y-6">
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-900 mb-4">Community Feed</h2>
                        
                        <!-- Create Post -->
                        <div class="mb-6 pb-6 border-b border-gray-200">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=7A1E1E&color=fff" 
                                     alt="Profile" class="w-10 h-10 rounded-full">
                                <input type="text" placeholder="Share an update..." 
                                       class="flex-1 px-4 py-3 bg-gray-50 rounded-lg border border-gray-200 focus:outline-none focus:border-primary transition-colors cursor-pointer"
                                       onclick="alert('Post creation modal would open here')">
                            </div>
                        </div>

                        <!-- Post Item -->
                        <div class="space-y-6">
                            <div class="pb-6 border-b border-gray-200">
                                <div class="flex items-start space-x-3 mb-4">
                                    <img src="https://ui-avatars.com/api/?name=Sarah+Johnson&background=2563eb&color=fff" 
                                         alt="Sarah Johnson" class="w-12 h-12 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">Sarah Johnson</h4>
                                                <p class="text-sm text-gray-500">Software Engineer at Google • Class of 2018</p>
                                            </div>
                                            <span class="text-sm text-gray-400">2h ago</span>
                                        </div>
                                        <p class="mt-3 text-gray-700 leading-relaxed">
                                            Excited to announce that I'll be speaking at our upcoming alumni tech conference! 
                                            Looking forward to connecting with fellow graduates. 🎉
                                        </p>
                                        <div class="flex items-center space-x-6 mt-4 text-sm">
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-thumbs-up"></i>
                                                <span>24 Likes</span>
                                            </button>
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-comment"></i>
                                                <span>5 Comments</span>
                                            </button>
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-share-square"></i>
                                                <span>Share</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pb-6 border-b border-gray-200">
                                <div class="flex items-start space-x-3 mb-4">
                                    <img src="https://ui-avatars.com/api/?name=Michael+Chen&background=059669&color=fff" 
                                         alt="Michael Chen" class="w-12 h-12 rounded-full">
                                    <div class="flex-1">
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <h4 class="font-semibold text-gray-900">Michael Chen</h4>
                                                <p class="text-sm text-gray-500">Product Manager at Microsoft • Class of 2016</p>
                                            </div>
                                            <span class="text-sm text-gray-400">5h ago</span>
                                        </div>
                                        <p class="mt-3 text-gray-700 leading-relaxed">
                                            We're hiring! Looking for talented product designers to join our team. 
                                            DM me if you're interested or know someone who might be a great fit.
                                        </p>
                                        <div class="flex items-center space-x-6 mt-4 text-sm">
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-thumbs-up"></i>
                                                <span>42 Likes</span>
                                            </button>
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-comment"></i>
                                                <span>8 Comments</span>
                                            </button>
                                            <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                <i class="far fa-share-square"></i>
                                                <span>Share</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Connection Requests -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Connection Requests</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Emma+Davis&background=dc2626&color=fff" 
                                     alt="Emma Davis" class="w-10 h-10 rounded-full">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">Emma Davis</h4>
                                    <p class="text-xs text-gray-500 truncate">Marketing Manager</p>
                                </div>
                            </div>
                            <div class="flex space-x-2">
                                <button class="flex-1 px-3 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition-colors">
                                    Accept
                                </button>
                                <button class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                    Decline
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Upcoming Events</h3>
                        <div class="space-y-4">
                            <div class="flex space-x-3">
                                <div class="bg-primary text-white p-2 rounded-lg text-center flex-shrink-0" style="width: 50px; height: 50px;">
                                    <div class="text-xs font-medium">DEC</div>
                                    <div class="text-lg font-bold">15</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm">Alumni Networking Night</h4>
                                    <p class="text-xs text-gray-500">6:00 PM - Virtual Event</p>
                                </div>
                            </div>

                            <div class="flex space-x-3">
                                <div class="bg-primary text-white p-2 rounded-lg text-center flex-shrink-0" style="width: 50px; height: 50px;">
                                    <div class="text-xs font-medium">DEC</div>
                                    <div class="text-lg font-bold">20</div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm">Career Fair 2025</h4>
                                    <p class="text-xs text-gray-500">10:00 AM - Campus Center</p>
                                </div>
                            </div>
                        </div>
                        <a href="events.php" class="block mt-4 text-center text-sm text-primary hover:text-primary-dark font-medium">
                            View All Events →
                        </a>
                    </div>

                    <!-- Suggested Connections -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">People You May Know</h3>
                        <div class="space-y-4">
                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Alex+Turner&background=8b5cf6&color=fff" 
                                     alt="Alex Turner" class="w-10 h-10 rounded-full">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">Alex Turner</h4>
                                    <p class="text-xs text-gray-500 truncate">15 mutual connections</p>
                                </div>
                                <button class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex-shrink-0">
                                    Connect
                                </button>
                            </div>

                            <div class="flex items-center space-x-3">
                                <img src="https://ui-avatars.com/api/?name=Lisa+Wong&background=f59e0b&color=fff" 
                                     alt="Lisa Wong" class="w-10 h-10 rounded-full">
                                <div class="flex-1 min-w-0">
                                    <h4 class="font-semibold text-gray-900 text-sm truncate">Lisa Wong</h4>
                                    <p class="text-xs text-gray-500 truncate">8 mutual connections</p>
                                </div>
                                <button class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex-shrink-0">
                                    Connect
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Function to update notification and message counts
        function updateCounts() {
            // Fetch notification count
            fetch('../actions/get_notification_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count > 0) {
                        const badge = document.getElementById('notification-badge');
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else {
                        document.getElementById('notification-badge').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.log('Error fetching notification count:', error);
                });

            // Fetch message count
            fetch('../actions/get_message_count.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.count > 0) {
                        const badge = document.getElementById('message-badge');
                        badge.textContent = data.count;
                        badge.style.display = 'flex';
                    } else {
                        document.getElementById('message-badge').style.display = 'none';
                    }
                })
                .catch(error => {
                    console.log('Error fetching message count:', error);
                });
        }

        // Update counts on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateCounts();
            loadRecommendations();
        });

        // Update counts every 30 seconds
        setInterval(updateCounts, 30000);
        
        /**
         * Load AI-powered recommendations
         */
        function loadRecommendations() {
            const container = document.getElementById('recommendations-container');
            if (!container) return;
            
            fetch('../actions/get_recommendations_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ type: 'personalized', limit: 4 })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.recommendations.length > 0) {
                    container.innerHTML = data.recommendations.map(rec => createRecommendationCard(rec)).join('');
                } else {
                    // Show fallback message
                    container.innerHTML = `
                        <div class="col-span-full text-center py-8 text-gray-500">
                            <i class="fas fa-lightbulb text-4xl mb-3 text-gray-300"></i>
                            <p>Browse our services to get personalized recommendations!</p>
                            <a href="services.php" class="inline-block mt-3 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors">
                                Explore Services
                            </a>
                        </div>
                    `;
                }
            })
            .catch(error => {
                console.error('Error loading recommendations:', error);
                container.innerHTML = '<div class="col-span-full text-center py-8 text-gray-400">Unable to load recommendations</div>';
            });
        }
        
        /**
         * Create a recommendation card HTML
         */
        function createRecommendationCard(service) {
            const typeIcons = {
                'mentorship': 'fa-user-graduate',
                'event': 'fa-calendar-alt',
                'job_posting': 'fa-briefcase',
                'premium': 'fa-star'
            };
            const typeColors = {
                'mentorship': 'from-green-500 to-emerald-600',
                'event': 'from-purple-500 to-violet-600',
                'job_posting': 'from-blue-500 to-indigo-600',
                'premium': 'from-amber-500 to-orange-600'
            };
            
            const icon = typeIcons[service.service_type] || 'fa-box';
            const gradient = typeColors[service.service_type] || 'from-gray-500 to-gray-600';
            
            const imageHtml = service.image_url 
                ? `<img src="${service.image_url}" alt="${service.service_name}" class="w-full h-32 object-cover rounded-lg mb-3">`
                : `<div class="w-full h-32 bg-gradient-to-br ${gradient} rounded-lg mb-3 flex items-center justify-center">
                     <i class="fas ${icon} text-4xl text-white/80"></i>
                   </div>`;
            
            return `
                <a href="single_service.php?id=${service.service_id}" 
                   class="bg-white rounded-xl border border-gray-200 p-4 hover:shadow-lg hover:border-primary/30 transition-all duration-300 group">
                    ${imageHtml}
                    <div class="flex items-start justify-between mb-2">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 text-xs font-medium rounded-full">${service.type_label}</span>
                        <span class="text-primary font-bold text-sm">${service.formatted_price}</span>
                    </div>
                    <h3 class="font-semibold text-gray-900 mb-1 line-clamp-2 group-hover:text-primary transition-colors">${service.service_name}</h3>
                    <p class="text-xs text-purple-600 flex items-center gap-1">
                        <i class="fas fa-magic"></i>
                        ${service.recommendation_reason}
                    </p>
                </a>
            `;
        }
    </script>
</body>
</html>
