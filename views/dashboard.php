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
require_once(dirname(__FILE__).'/../classes/connection_class.php');
require_once(dirname(__FILE__).'/../classes/post_class.php');
require_once(dirname(__FILE__).'/../settings/db_class.php');

$cart = new Cart();
$cart_count = $cart->getCartCount($user_id);

// Get posts for feed
$post = new Post();
$posts = $post->get_all_posts(10, 0);
if ($posts === false) {
    $posts = [];
}

// Get pending connection requests
$connection = new Connection();
$pending_requests = $connection->getPendingRequests($user_id);

// Get suggested connections (users not connected with)
$db = new db_connection();
$suggested_users = $db->db_fetch_all("
    SELECT u.user_id, u.first_name, u.last_name, u.profile_image, u.user_role
    FROM users u
    WHERE u.user_id != $user_id 
    AND u.is_active = 1
    AND u.user_id NOT IN (
        SELECT CASE WHEN requester_id = $user_id THEN receiver_id ELSE requester_id END
        FROM connections 
        WHERE requester_id = $user_id OR receiver_id = $user_id
    )
    ORDER BY RAND()
    LIMIT 5
");

// Get upcoming events from services
$upcoming_events = $db->db_fetch_all("
    SELECT service_id, service_name, description, price, date_created
    FROM services 
    WHERE service_type = 'event' AND is_active = 1
    ORDER BY date_created DESC
    LIMIT 3
");
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
                                       onclick="openPostModal()" readonly id="post-trigger">
                            </div>
                            <div class="flex items-center space-x-4 mt-3 ml-13 pl-10">
                                <button onclick="openPostModal('general')" class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors text-sm">
                                    <i class="far fa-edit"></i>
                                    <span>Post</span>
                                </button>
                                <button onclick="openPostModal('job')" class="flex items-center space-x-2 text-gray-500 hover:text-blue-600 transition-colors text-sm">
                                    <i class="fas fa-briefcase"></i>
                                    <span>Job</span>
                                </button>
                                <button onclick="openPostModal('event')" class="flex items-center space-x-2 text-gray-500 hover:text-green-600 transition-colors text-sm">
                                    <i class="far fa-calendar-alt"></i>
                                    <span>Event</span>
                                </button>
                            </div>
                        </div>

                        <!-- Posts Container -->
                        <div class="space-y-6" id="posts-container">
                            <?php if (empty($posts)): ?>
                                <div class="text-center py-8 text-gray-500">
                                    <i class="far fa-newspaper text-4xl mb-3"></i>
                                    <p>No posts yet. Be the first to share something!</p>
                                </div>
                            <?php else: ?>
                                <?php foreach ($posts as $p): 
                                    $author_name = htmlspecialchars($p['first_name'] . ' ' . $p['last_name']);
                                    $author_role = ucfirst($p['user_role'] ?? 'Member');
                                    $post_date = new DateTime($p['date_created']);
                                    $now = new DateTime();
                                    $diff = $post_date->diff($now);
                                    
                                    // Format time ago
                                    if ($diff->d > 0) {
                                        $time_ago = $diff->d . 'd ago';
                                    } elseif ($diff->h > 0) {
                                        $time_ago = $diff->h . 'h ago';
                                    } elseif ($diff->i > 0) {
                                        $time_ago = $diff->i . 'm ago';
                                    } else {
                                        $time_ago = 'Just now';
                                    }
                                    
                                    // Check if user has liked this post
                                    $has_liked = $post->has_liked($p['post_id'], $user_id);
                                    
                                    // Random color for avatar
                                    $colors = ['7A1E1E', '2563eb', '059669', '7c3aed', 'dc2626', 'ea580c'];
                                    $color = $colors[array_rand($colors)];
                                ?>
                                <div class="pb-6 border-b border-gray-200 post-item" data-post-id="<?php echo $p['post_id']; ?>">
                                    <div class="flex items-start space-x-3 mb-4">
                                        <?php if (!empty($p['profile_image'])): ?>
                                            <img src="../uploads/profiles/<?php echo htmlspecialchars($p['profile_image']); ?>" 
                                                 alt="<?php echo $author_name; ?>" class="w-12 h-12 rounded-full object-cover">
                                        <?php else: ?>
                                            <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($author_name); ?>&background=<?php echo $color; ?>&color=fff" 
                                                 alt="<?php echo $author_name; ?>" class="w-12 h-12 rounded-full">
                                        <?php endif; ?>
                                        <div class="flex-1">
                                            <div class="flex items-center justify-between">
                                                <div>
                                                    <h4 class="font-semibold text-gray-900"><?php echo $author_name; ?></h4>
                                                    <p class="text-sm text-gray-500"><?php echo $author_role; ?></p>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <?php if ($p['post_type'] !== 'general'): ?>
                                                        <span class="px-2 py-1 text-xs rounded-full 
                                                            <?php echo $p['post_type'] === 'job' ? 'bg-blue-100 text-blue-700' : 
                                                                ($p['post_type'] === 'event' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600'); ?>">
                                                            <?php echo ucfirst($p['post_type']); ?>
                                                        </span>
                                                    <?php endif; ?>
                                                    <span class="text-sm text-gray-400"><?php echo $time_ago; ?></span>
                                                </div>
                                            </div>
                                            <?php if (!empty($p['post_title'])): ?>
                                                <h5 class="mt-2 font-semibold text-gray-900"><?php echo htmlspecialchars($p['post_title']); ?></h5>
                                            <?php endif; ?>
                                            <p class="mt-2 text-gray-700 leading-relaxed"><?php echo nl2br(htmlspecialchars($p['post_content'])); ?></p>
                                            
                                            <?php if (!empty($p['image_url'])): ?>
                                                <div class="mt-3">
                                                    <img src="../uploads/posts/<?php echo htmlspecialchars($p['image_url']); ?>" 
                                                         alt="Post image" class="rounded-lg max-h-96 object-cover w-full">
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="flex items-center space-x-6 mt-4 text-sm">
                                                <button onclick="toggleLike(<?php echo $p['post_id']; ?>, this)" 
                                                        class="flex items-center space-x-2 transition-colors like-btn <?php echo $has_liked ? 'text-primary' : 'text-gray-500 hover:text-primary'; ?>">
                                                    <i class="<?php echo $has_liked ? 'fas' : 'far'; ?> fa-thumbs-up"></i>
                                                    <span class="like-count"><?php echo $p['likes_count'] ?? 0; ?> Likes</span>
                                                </button>
                                                <button onclick="toggleComments(<?php echo $p['post_id']; ?>)" 
                                                        class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                    <i class="far fa-comment"></i>
                                                    <span><?php echo $p['comments_count'] ?? 0; ?> Comments</span>
                                                </button>
                                                <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                                    <i class="far fa-share-square"></i>
                                                    <span>Share</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <?php if (count($posts) >= 10): ?>
                            <div class="mt-6 text-center">
                                <button onclick="loadMorePosts()" id="load-more-btn" 
                                        class="px-6 py-2 text-primary border border-primary rounded-lg hover:bg-primary hover:text-white transition-colors">
                                    Load More Posts
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <div class="space-y-6">
                    <!-- Connection Requests -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Connection Requests</h3>
                        <?php if (empty($pending_requests)): ?>
                            <p class="text-sm text-gray-500 text-center py-4">No pending requests</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach (array_slice($pending_requests, 0, 3) as $request): 
                                    $req_name = htmlspecialchars($request['first_name'] . ' ' . $request['last_name']);
                                    $req_initials = strtoupper(substr($request['first_name'], 0, 1) . substr($request['last_name'], 0, 1));
                                ?>
                                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <?php if (!empty($request['profile_image'])): ?>
                                            <img src="../uploads/profiles/<?php echo htmlspecialchars($request['profile_image']); ?>" 
                                                 alt="<?php echo $req_name; ?>" class="w-10 h-10 rounded-full object-cover">
                                        <?php else: ?>
                                            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white text-sm font-bold">
                                                <?php echo $req_initials; ?>
                                            </div>
                                        <?php endif; ?>
                                        <div class="flex-1 min-w-0">
                                            <h4 class="font-semibold text-gray-900 text-sm truncate"><?php echo $req_name; ?></h4>
                                            <p class="text-xs text-gray-500 truncate"><?php echo ucfirst($request['user_role'] ?? 'Member'); ?></p>
                                        </div>
                                    </div>
                                    <div class="flex space-x-2">
                                        <button onclick="handleConnectionRequest(<?php echo $request['connection_id']; ?>, 'accept')" 
                                                class="flex-1 px-3 py-2 bg-primary text-white text-sm rounded-lg hover:bg-primary-dark transition-colors">
                                            Accept
                                        </button>
                                        <button onclick="handleConnectionRequest(<?php echo $request['connection_id']; ?>, 'reject')" 
                                                class="flex-1 px-3 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors">
                                            Decline
                                        </button>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($pending_requests) > 3): ?>
                                <a href="connections.php" class="block mt-4 text-center text-sm text-primary hover:text-primary-dark font-medium">
                                    View All (<?php echo count($pending_requests); ?>) →
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Upcoming Events -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">Upcoming Events</h3>
                        <?php if (empty($upcoming_events)): ?>
                            <p class="text-sm text-gray-500 text-center py-4">No upcoming events</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($upcoming_events as $event): 
                                    $event_date = new DateTime($event['date_created']);
                                ?>
                                <div class="flex space-x-3">
                                    <div class="bg-primary text-white p-2 rounded-lg text-center flex-shrink-0" style="width: 50px; height: 50px;">
                                        <div class="text-xs font-medium"><?php echo $event_date->format('M'); ?></div>
                                        <div class="text-lg font-bold"><?php echo $event_date->format('d'); ?></div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm"><?php echo htmlspecialchars($event['service_name']); ?></h4>
                                        <p class="text-xs text-gray-500">GHS <?php echo number_format($event['price'], 2); ?></p>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="services.php?type=event" class="block mt-4 text-center text-sm text-primary hover:text-primary-dark font-medium">
                            View All Events →
                        </a>
                    </div>

                    <!-- Suggested Connections -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                        <h3 class="text-lg font-bold text-gray-900 mb-4">People You May Know</h3>
                        <?php if (empty($suggested_users)): ?>
                            <p class="text-sm text-gray-500 text-center py-4">No suggestions available</p>
                        <?php else: ?>
                            <div class="space-y-4">
                                <?php foreach ($suggested_users as $sug_user): 
                                    $sug_name = htmlspecialchars($sug_user['first_name'] . ' ' . $sug_user['last_name']);
                                    $sug_initials = strtoupper(substr($sug_user['first_name'], 0, 1) . substr($sug_user['last_name'], 0, 1));
                                ?>
                                <div class="flex items-center space-x-3">
                                    <?php if (!empty($sug_user['profile_image'])): ?>
                                        <img src="../uploads/profiles/<?php echo htmlspecialchars($sug_user['profile_image']); ?>" 
                                             alt="<?php echo $sug_name; ?>" class="w-10 h-10 rounded-full object-cover">
                                    <?php else: ?>
                                        <div class="w-10 h-10 rounded-full bg-purple-500 flex items-center justify-center text-white text-sm font-bold">
                                            <?php echo $sug_initials; ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-semibold text-gray-900 text-sm truncate"><?php echo $sug_name; ?></h4>
                                        <p class="text-xs text-gray-500 truncate"><?php echo ucfirst($sug_user['user_role'] ?? 'Member'); ?></p>
                                    </div>
                                    <button onclick="quickConnect(<?php echo $sug_user['user_id']; ?>)" 
                                            class="px-3 py-1 text-xs bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors flex-shrink-0">
                                        Connect
                                    </button>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                        <a href="alumni_search.php" class="block mt-4 text-center text-sm text-primary hover:text-primary-dark font-medium">
                            Find More People →
                        </a>
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

        /**
         * Handle connection request (accept/reject)
         */
        function handleConnectionRequest(connectionId, action) {
            fetch('../actions/handle_connection_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    connection_id: connectionId,
                    action: action
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Action failed', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Action failed', 'error');
            });
        }

        /**
         * Quick connect with a user
         */
        function quickConnect(userId) {
            fetch('../actions/send_connection_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    receiver_id: userId,
                    message: ''
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Connection request sent!', 'success');
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showToast(data.message || 'Failed to send request', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to send request', 'error');
            });
        }

        /**
         * Show toast notification
         */
        function showToast(message, type = 'success') {
            const container = document.getElementById('toast-container') || createToastContainer();
            const toast = document.createElement('div');
            toast.className = `px-6 py-3 rounded-lg text-white font-medium shadow-lg ${type === 'success' ? 'bg-green-500' : 'bg-red-500'}`;
            toast.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} mr-2"></i>${message}`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.3s';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        function createToastContainer() {
            const container = document.createElement('div');
            container.id = 'toast-container';
            container.className = 'fixed bottom-4 right-4 z-50 space-y-2';
            document.body.appendChild(container);
            return container;
        }

        // ========== POST FUNCTIONS ==========
        let currentPostPage = 1;
        
        /**
         * Open post creation modal
         */
        function openPostModal(postType = 'general') {
            document.getElementById('post-type').value = postType;
            updatePostTypeLabel(postType);
            document.getElementById('post-modal').classList.remove('hidden');
            document.getElementById('post-content').focus();
        }
        
        /**
         * Close post modal
         */
        function closePostModal() {
            document.getElementById('post-modal').classList.add('hidden');
            document.getElementById('post-form').reset();
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('image-preview').querySelector('img').src = '';
        }
        
        /**
         * Update post type label in modal
         */
        function updatePostTypeLabel(type) {
            const labels = {
                'general': { text: 'Share a Post', icon: 'fa-edit', color: 'text-gray-600' },
                'job': { text: 'Share a Job Opportunity', icon: 'fa-briefcase', color: 'text-blue-600' },
                'event': { text: 'Share an Event', icon: 'fa-calendar-alt', color: 'text-green-600' }
            };
            const label = labels[type] || labels['general'];
            document.getElementById('post-type-label').innerHTML = `<i class="fas ${label.icon} mr-2 ${label.color}"></i>${label.text}`;
        }
        
        /**
         * Preview selected image
         */
        function previewImage(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('image-preview').classList.remove('hidden');
                    document.getElementById('image-preview').querySelector('img').src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }
        
        /**
         * Remove image preview
         */
        function removeImagePreview() {
            document.getElementById('post-image').value = '';
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('image-preview').querySelector('img').src = '';
        }
        
        /**
         * Submit new post
         */
        function submitPost() {
            const form = document.getElementById('post-form');
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submit-post-btn');
            
            // Validate content
            const content = formData.get('content');
            if (!content || content.trim().length === 0) {
                showToast('Please write something to post', 'error');
                return;
            }
            
            // Disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Posting...';
            
            fetch('../actions/create_post_action.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast('Post created successfully!', 'success');
                    closePostModal();
                    
                    // Add new post to feed
                    const container = document.getElementById('posts-container');
                    const emptyMessage = container.querySelector('.text-center.py-8');
                    if (emptyMessage) {
                        emptyMessage.remove();
                    }
                    container.insertAdjacentHTML('afterbegin', data.post_html);
                } else {
                    showToast(data.message || 'Failed to create post', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to create post', 'error');
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-paper-plane mr-2"></i>Post';
            });
        }
        
        /**
         * Toggle like on a post
         */
        function toggleLike(postId, button) {
            fetch('../actions/like_post_action.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ post_id: postId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const icon = button.querySelector('i');
                    const countSpan = button.querySelector('.like-count');
                    
                    if (data.liked) {
                        button.classList.remove('text-gray-500');
                        button.classList.add('text-primary');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                    } else {
                        button.classList.remove('text-primary');
                        button.classList.add('text-gray-500');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                    }
                    countSpan.textContent = data.likes_count + ' Likes';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to like post', 'error');
            });
        }
        
        /**
         * Toggle comments section (placeholder)
         */
        function toggleComments(postId) {
            showToast('Comments feature coming soon!', 'success');
        }
        
        /**
         * Load more posts
         */
        function loadMorePosts() {
            currentPostPage++;
            const btn = document.getElementById('load-more-btn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Loading...';
            
            fetch(`../actions/get_posts_action.php?page=${currentPostPage}&limit=10`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.posts.length > 0) {
                    const container = document.getElementById('posts-container');
                    data.posts.forEach(post => {
                        container.insertAdjacentHTML('beforeend', createPostHTML(post));
                    });
                    
                    if (data.posts.length < 10) {
                        btn.remove();
                    } else {
                        btn.disabled = false;
                        btn.innerHTML = 'Load More Posts';
                    }
                } else {
                    btn.remove();
                    if (data.posts.length === 0) {
                        showToast('No more posts to load', 'success');
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Failed to load posts', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Load More Posts';
            });
        }
        
        /**
         * Create post HTML from data
         */
        function createPostHTML(post) {
            const colors = ['7A1E1E', '2563eb', '059669', '7c3aed', 'dc2626', 'ea580c'];
            const color = colors[Math.floor(Math.random() * colors.length)];
            
            const avatar = post.profile_image 
                ? `<img src="../uploads/profiles/${post.profile_image}" alt="${post.first_name}" class="w-12 h-12 rounded-full object-cover">`
                : `<img src="https://ui-avatars.com/api/?name=${encodeURIComponent(post.first_name + ' ' + post.last_name)}&background=${color}&color=fff" alt="${post.first_name}" class="w-12 h-12 rounded-full">`;
            
            const typeLabel = post.post_type !== 'general' 
                ? `<span class="px-2 py-1 text-xs rounded-full ${post.post_type === 'job' ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700'}">${post.post_type.charAt(0).toUpperCase() + post.post_type.slice(1)}</span>` 
                : '';
            
            const title = post.post_title ? `<h5 class="mt-2 font-semibold text-gray-900">${escapeHtml(post.post_title)}</h5>` : '';
            const image = post.image_url ? `<div class="mt-3"><img src="../uploads/posts/${post.image_url}" alt="Post image" class="rounded-lg max-h-96 object-cover w-full"></div>` : '';
            
            return `
                <div class="pb-6 border-b border-gray-200 post-item" data-post-id="${post.post_id}">
                    <div class="flex items-start space-x-3 mb-4">
                        ${avatar}
                        <div class="flex-1">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h4 class="font-semibold text-gray-900">${escapeHtml(post.first_name + ' ' + post.last_name)}</h4>
                                    <p class="text-sm text-gray-500">${post.user_role ? post.user_role.charAt(0).toUpperCase() + post.user_role.slice(1) : 'Member'}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    ${typeLabel}
                                    <span class="text-sm text-gray-400">${post.time_ago}</span>
                                </div>
                            </div>
                            ${title}
                            <p class="mt-2 text-gray-700 leading-relaxed">${escapeHtml(post.post_content).replace(/\n/g, '<br>')}</p>
                            ${image}
                            <div class="flex items-center space-x-6 mt-4 text-sm">
                                <button onclick="toggleLike(${post.post_id}, this)" 
                                        class="flex items-center space-x-2 transition-colors like-btn ${post.user_liked ? 'text-primary' : 'text-gray-500 hover:text-primary'}">
                                    <i class="${post.user_liked ? 'fas' : 'far'} fa-thumbs-up"></i>
                                    <span class="like-count">${post.likes_count || 0} Likes</span>
                                </button>
                                <button onclick="toggleComments(${post.post_id})" 
                                        class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                    <i class="far fa-comment"></i>
                                    <span>${post.comments_count || 0} Comments</span>
                                </button>
                                <button class="flex items-center space-x-2 text-gray-500 hover:text-primary transition-colors">
                                    <i class="far fa-share-square"></i>
                                    <span>Share</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        }
        
        /**
         * Escape HTML to prevent XSS
         */
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
        
        // Close modal on outside click
        document.getElementById('post-modal')?.addEventListener('click', function(e) {
            if (e.target === this) {
                closePostModal();
            }
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePostModal();
            }
        });
    </script>
    
    <!-- Post Creation Modal -->
    <div id="post-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-lg w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 id="post-type-label" class="text-lg font-bold text-gray-900">
                        <i class="fas fa-edit mr-2 text-gray-600"></i>Share a Post
                    </h3>
                    <button onclick="closePostModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                
                <form id="post-form" onsubmit="event.preventDefault(); submitPost();">
                    <input type="hidden" name="post_type" id="post-type" value="general">
                    
                    <!-- Title (optional) -->
                    <div class="mb-4">
                        <input type="text" name="title" placeholder="Title (optional)" 
                               class="w-full px-4 py-2 border border-gray-200 rounded-lg focus:outline-none focus:border-primary">
                    </div>
                    
                    <!-- Content -->
                    <div class="mb-4">
                        <textarea name="content" id="post-content" rows="5" 
                                  placeholder="What would you like to share?" 
                                  class="w-full px-4 py-3 border border-gray-200 rounded-lg focus:outline-none focus:border-primary resize-none"
                                  required></textarea>
                    </div>
                    
                    <!-- Image Preview -->
                    <div id="image-preview" class="mb-4 hidden relative">
                        <img src="" alt="Preview" class="w-full rounded-lg max-h-48 object-cover">
                        <button type="button" onclick="removeImagePreview()" 
                                class="absolute top-2 right-2 bg-red-500 text-white rounded-full w-8 h-8 flex items-center justify-center hover:bg-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-4">
                            <label class="cursor-pointer text-gray-500 hover:text-primary transition-colors">
                                <i class="fas fa-image text-lg"></i>
                                <input type="file" name="image" id="post-image" accept="image/*" class="hidden" onchange="previewImage(this)">
                            </label>
                        </div>
                        <button type="submit" id="submit-post-btn" 
                                class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-primary-dark transition-colors font-medium">
                            <i class="fas fa-paper-plane mr-2"></i>Post
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
