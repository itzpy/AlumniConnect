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
</head>
<body class="bg-gray-50 font-sans">
    <!-- Top Navigation -->
    <nav class="bg-white shadow-sm border-b border-gray-200 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <a href="../index.php" class="flex items-center space-x-2 text-xl font-bold text-primary">
                        <i class="fas fa-graduation-cap"></i>
                        <span>Alumni Connect</span>
                    </a>
                </div>
                
                <div class="flex items-center space-x-6">
                    <a href="alumni_search.php" class="text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-search text-lg"></i>
                    </a>
                    <a href="messages.php" class="relative text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-envelope text-lg"></i>
                        <span id="message-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center" style="display: none;">0</span>
                    </a>
                    <a href="notifications.php" class="relative text-gray-600 hover:text-primary transition-colors">
                        <i class="fas fa-bell text-lg"></i>
                        <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-4 h-4 flex items-center justify-center" style="display: none;">0</span>
                    </a>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 text-gray-700 hover:text-primary transition-colors">
                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=7A1E1E&color=fff" 
                                 alt="Profile" class="w-8 h-8 rounded-full">
                            <span class="font-medium"><?php echo htmlspecialchars($user_name); ?></span>
                            <i class="fas fa-chevron-down text-sm"></i>
                        </button>
                        <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-2 border border-gray-200">
                            <a href="profile.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-user mr-2"></i> My Profile
                            </a>
                            <a href="settings.php" class="block px-4 py-2 text-gray-700 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-cog mr-2"></i> Settings
                            </a>
                            <hr class="my-2">
                            <a href="../login/logout.php" class="block px-4 py-2 text-red-600 hover:bg-gray-50 transition-colors">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <div class="flex min-h-screen">
        <!-- Sidebar -->
        <aside class="w-64 bg-white border-r border-gray-200 hidden lg:block">
            <nav class="p-4 space-y-1">
                <a href="dashboard.php" class="flex items-center space-x-3 px-4 py-3 bg-primary text-white rounded-lg transition-colors">
                    <i class="fas fa-home"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
                <a href="alumni_search.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-search"></i>
                    <span class="font-medium">Find Alumni</span>
                </a>
                <a href="connections.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-users"></i>
                    <span class="font-medium">My Network</span>
                </a>
                <a href="messages.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-envelope"></i>
                    <span class="font-medium">Messages</span>
                </a>
                <a href="jobs.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-briefcase"></i>
                    <span class="font-medium">Job Board</span>
                </a>
                <a href="events.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-calendar"></i>
                    <span class="font-medium">Events</span>
                </a>
                <a href="mentorship.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-hands-helping"></i>
                    <span class="font-medium">Mentorship</span>
                </a>
                
                <hr class="my-4">
                
                <a href="profile.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-user"></i>
                    <span class="font-medium">My Profile</span>
                </a>
                <a href="settings.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-cog"></i>
                    <span class="font-medium">Settings</span>
                </a>
            </nav>
        </aside>

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
                    <!-- Admin Stats -->
                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-blue-100 p-3 rounded-lg">
                                <i class="fas fa-users text-2xl text-blue-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">+8%</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">1,248</h3>
                        <p class="text-gray-600 text-sm">Total Users</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-green-100 p-3 rounded-lg">
                                <i class="fas fa-comments text-2xl text-green-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">This week</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">156</h3>
                        <p class="text-gray-600 text-sm">Active Posts</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-red-100 p-3 rounded-lg">
                                <i class="fas fa-flag text-2xl text-red-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Pending</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">7</h3>
                        <p class="text-gray-600 text-sm">Reports</p>
                    </div>

                    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-shadow">
                        <div class="flex items-center justify-between mb-4">
                            <div class="bg-purple-100 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-2xl text-purple-600"></i>
                            </div>
                            <span class="text-sm text-gray-500">Overall</span>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-1">94%</h3>
                        <p class="text-gray-600 text-sm">Engagement Rate</p>
                    </div>
                <?php endif; ?>
            </div>

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
        document.addEventListener('DOMContentLoaded', updateCounts);

        // Update counts every 30 seconds
        setInterval(updateCounts, 30000);
    </script>
</body>
</html>
