<!-- Sidebar Navigation -->
<?php 
// Ensure $user_type is defined with fallback
if (!isset($user_type)) {
    $user_type = $_SESSION['user_type'] ?? 'student';
}
// Ensure $cart_count is defined
if (!isset($cart_count)) {
    $cart_count = 0;
}

// Get current page for active state
$current_page = basename($_SERVER['PHP_SELF']);

// Determine if we're in admin folder or views folder
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$is_admin_folder = ($current_dir === 'admin');

// Set base paths based on current location
$views_path = $is_admin_folder ? '../views/' : '';
$admin_path = $is_admin_folder ? '' : '../admin/';
$login_path = $is_admin_folder ? '../login/' : '../login/';
$index_path = $is_admin_folder ? '../index.php' : '../index.php';

// Dashboard path depends on user type
if ($user_type === 'admin') {
    $dashboard_path = $is_admin_folder ? 'dashboard.php' : '../admin/dashboard.php';
} else {
    $dashboard_path = $is_admin_folder ? '../views/dashboard.php' : 'dashboard.php';
}
?>

<!-- Desktop Sidebar -->
<aside class="w-64 min-w-[256px] bg-white border-r border-gray-200 min-h-screen sticky top-[61px] hidden lg:block overflow-y-auto flex-shrink-0">
    <nav class="p-4">
        <ul class="space-y-1">
            <!-- User Type Badge -->
            <li class="mb-4 px-4">
                <?php if ($user_type === 'admin'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                        <i class="fas fa-shield-alt mr-1"></i> Administrator
                    </span>
                <?php elseif ($user_type === 'alumni'): ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                        <i class="fas fa-user-tie mr-1"></i> Alumni
                    </span>
                <?php else: ?>
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                        <i class="fas fa-user-graduate mr-1"></i> Student
                    </span>
                <?php endif; ?>
            </li>

            <!-- Main Navigation -->
            <li class="mb-2">
                <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Main Menu</span>
            </li>
            <li>
                <a href="<?php echo $dashboard_path; ?>" class="<?php echo $current_page == 'dashboard.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                    <i class="fas fa-home w-5 text-center"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            
            <?php if ($user_type === 'admin'): ?>
                <!-- ADMIN NAVIGATION -->
                <li class="pt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Administration</span>
                </li>
                <li>
                    <a href="<?php echo $admin_path; ?>users.php" class="<?php echo $current_page == 'users.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-users-cog w-5 text-center <?php echo $current_page == 'users.php' ? '' : 'text-blue-500'; ?>"></i>
                        <span class="font-medium">Manage Users</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $admin_path; ?>services.php" class="<?php echo $current_page == 'services.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-boxes w-5 text-center <?php echo $current_page == 'services.php' ? '' : 'text-green-500'; ?>"></i>
                        <span class="font-medium">Manage Services</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $admin_path; ?>orders.php" class="<?php echo $current_page == 'orders.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-shopping-bag w-5 text-center <?php echo $current_page == 'orders.php' ? '' : 'text-orange-500'; ?>"></i>
                        <span class="font-medium">All Orders</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $admin_path; ?>coupons.php" class="<?php echo $current_page == 'coupons.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-ticket-alt w-5 text-center <?php echo $current_page == 'coupons.php' ? '' : 'text-yellow-500'; ?>"></i>
                        <span class="font-medium">Coupons</span>
                    </a>
                </li>
                <li>
                    <a href="<?php echo $admin_path; ?>reports.php" class="<?php echo $current_page == 'reports.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-chart-bar w-5 text-center <?php echo $current_page == 'reports.php' ? '' : 'text-purple-500'; ?>"></i>
                        <span class="font-medium">Reports & Analytics</span>
                    </a>
                </li>

            <?php elseif ($user_type === 'alumni'): ?>
                <!-- ALUMNI NAVIGATION -->
                <li>
                    <a href="alumni_search.php" class="<?php echo $current_page == 'alumni_search.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-search w-5 text-center"></i>
                        <span class="font-medium">Find Alumni</span>
                    </a>
                </li>
                <li>
                    <a href="connections.php" class="<?php echo $current_page == 'connections.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-user-friends w-5 text-center"></i>
                        <span class="font-medium">My Network</span>
                    </a>
                </li>
                <li>
                    <a href="messages.php" class="<?php echo $current_page == 'messages.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-envelope w-5 text-center"></i>
                        <span class="font-medium">Messages</span>
                    </a>
                </li>

                <!-- Alumni Services (Give Back) -->
                <li class="pt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Give Back</span>
                </li>
                <li>
                    <a href="mentor_dashboard.php" class="<?php echo $current_page == 'mentor_dashboard.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-chalkboard-teacher w-5 text-center <?php echo $current_page == 'mentor_dashboard.php' ? '' : 'text-green-500'; ?>"></i>
                        <span class="font-medium">Mentor Dashboard</span>
                    </a>
                </li>
                <li>
                    <a href="jobs.php" class="<?php echo $current_page == 'jobs.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-briefcase w-5 text-center <?php echo $current_page == 'jobs.php' ? '' : 'text-blue-500'; ?>"></i>
                        <span class="font-medium">Post Jobs</span>
                    </a>
                </li>

                <!-- Explore -->
                <li class="pt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Explore</span>
                </li>
                <li>
                    <a href="events.php" class="<?php echo $current_page == 'events.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-calendar-alt w-5 text-center <?php echo $current_page == 'events.php' ? '' : 'text-purple-500'; ?>"></i>
                        <span class="font-medium">Events</span>
                    </a>
                </li>

            <?php else: ?>
                <!-- STUDENT NAVIGATION -->
                <li>
                    <a href="alumni_search.php" class="<?php echo $current_page == 'alumni_search.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-search w-5 text-center"></i>
                        <span class="font-medium">Find Alumni</span>
                    </a>
                </li>
                <li>
                    <a href="connections.php" class="<?php echo $current_page == 'connections.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-user-friends w-5 text-center"></i>
                        <span class="font-medium">My Network</span>
                    </a>
                </li>
                <li>
                    <a href="messages.php" class="<?php echo $current_page == 'messages.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-envelope w-5 text-center"></i>
                        <span class="font-medium">Messages</span>
                    </a>
                </li>

                <!-- Career Services -->
                <li class="pt-4 mb-2">
                    <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Career Services</span>
                </li>
                <li>
                    <a href="mentorship.php" class="<?php echo $current_page == 'mentorship.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-user-graduate w-5 text-center <?php echo $current_page == 'mentorship.php' ? '' : 'text-green-500'; ?>"></i>
                        <span class="font-medium">Find a Mentor</span>
                    </a>
                </li>
                <li>
                    <a href="jobs.php" class="<?php echo $current_page == 'jobs.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-briefcase w-5 text-center <?php echo $current_page == 'jobs.php' ? '' : 'text-blue-500'; ?>"></i>
                        <span class="font-medium">Job Opportunities</span>
                    </a>
                </li>
                <li>
                    <a href="events.php" class="<?php echo $current_page == 'events.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-calendar-alt w-5 text-center <?php echo $current_page == 'events.php' ? '' : 'text-purple-500'; ?>"></i>
                        <span class="font-medium">Events & Tickets</span>
                    </a>
                </li>
                <li>
                    <a href="services.php" class="<?php echo $current_page == 'services.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                        <i class="fas fa-th-large w-5 text-center"></i>
                        <span class="font-medium">All Services</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Shopping Section (Students & Alumni only) -->
            <?php if ($user_type !== 'admin'): ?>
            <li class="pt-4 mb-2">
                <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Shopping</span>
            </li>
            <li>
                <a href="cart.php" class="<?php echo $current_page == 'cart.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center justify-between px-4 py-2.5 rounded-lg transition-all duration-200">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-shopping-cart w-5 text-center"></i>
                        <span class="font-medium">Cart</span>
                    </div>
                    <?php if (isset($cart_count) && $cart_count > 0): ?>
                    <span class="bg-primary text-white text-xs font-bold rounded-full min-w-[20px] h-5 flex items-center justify-center px-1.5 <?php echo $current_page == 'cart.php' ? 'bg-white text-primary' : ''; ?>">
                        <?php echo $cart_count; ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?php echo $current_page == 'orders.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                    <i class="fas fa-receipt w-5 text-center"></i>
                    <span class="font-medium">My Orders</span>
                </a>
            </li>
            <?php endif; ?>

            <!-- Account Section -->
            <li class="pt-4 mb-2">
                <span class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider">Account</span>
            </li>
            <?php if ($user_type !== 'admin'): ?>
            <li>
                <a href="../pricing.php" class="<?php echo $current_page == 'pricing.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                    <i class="fas fa-crown w-5 text-center <?php echo $current_page == 'pricing.php' ? '' : 'text-amber-500'; ?>"></i>
                    <span class="font-medium">Upgrade Plan</span>
                </a>
            </li>
            <?php endif; ?>
            <li>
                <a href="<?php echo $views_path; ?>profile.php" class="<?php echo $current_page == 'profile.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                    <i class="fas fa-user w-5 text-center"></i>
                    <span class="font-medium">My Profile</span>
                </a>
            </li>
            <li>
                <a href="<?php echo $views_path; ?>settings.php" class="<?php echo $current_page == 'settings.php' ? 'bg-primary text-white shadow-md' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-2.5 rounded-lg transition-all duration-200">
                    <i class="fas fa-cog w-5 text-center"></i>
                    <span class="font-medium">Settings</span>
                </a>
            </li>
        </ul>

        <div class="border-t border-gray-200 mt-6 pt-4">
            <a href="<?php echo $login_path; ?>logout.php" class="flex items-center space-x-3 px-4 py-2.5 text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span class="font-medium">Logout</span>
            </a>
        </div>
    </nav>
</aside>

<!-- Mobile Sidebar (Hidden by default) -->
<div id="mobile-sidebar" class="hidden fixed inset-0 z-50 lg:hidden">
    <!-- Overlay -->
    <div class="fixed inset-0 bg-black/50" onclick="document.getElementById('mobile-sidebar').classList.add('hidden')"></div>
    
    <!-- Sidebar Panel -->
    <aside class="fixed left-0 top-0 h-full w-72 bg-white shadow-xl overflow-y-auto">
        <div class="p-4 border-b border-gray-200 flex items-center justify-between">
            <a href="<?php echo $index_path; ?>" class="flex items-center space-x-2 text-xl font-bold text-primary">
                <i class="fas fa-graduation-cap"></i>
                <span>Alumni Connect</span>
            </a>
            <button onclick="document.getElementById('mobile-sidebar').classList.add('hidden')" class="p-2 text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <nav class="p-4">
            <ul class="space-y-1">
                <!-- User Type Badge -->
                <li class="mb-4">
                    <?php if ($user_type === 'admin'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-100 text-red-800">
                            <i class="fas fa-shield-alt mr-1"></i> Administrator
                        </span>
                    <?php elseif ($user_type === 'alumni'): ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 text-purple-800">
                            <i class="fas fa-user-tie mr-1"></i> Alumni
                        </span>
                    <?php else: ?>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-800">
                            <i class="fas fa-user-graduate mr-1"></i> Student
                        </span>
                    <?php endif; ?>
                </li>
                
                <li><a href="<?php echo $dashboard_path; ?>" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-home w-5"></i><span>Dashboard</span></a></li>
                
                <?php if ($user_type === 'admin'): ?>
                    <li class="pt-4"><span class="px-4 text-xs font-semibold text-gray-400 uppercase">Administration</span></li>
                    <li><a href="<?php echo $admin_path; ?>users.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-users-cog w-5 text-blue-500"></i><span>Manage Users</span></a></li>
                    <li><a href="<?php echo $admin_path; ?>services.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-boxes w-5 text-green-500"></i><span>Manage Services</span></a></li>
                    <li><a href="<?php echo $admin_path; ?>orders.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-shopping-bag w-5 text-orange-500"></i><span>All Orders</span></a></li>
                    <li><a href="<?php echo $admin_path; ?>coupons.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-ticket-alt w-5 text-yellow-500"></i><span>Coupons</span></a></li>
                    <li><a href="<?php echo $admin_path; ?>reports.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-chart-bar w-5 text-purple-500"></i><span>Reports</span></a></li>
                <?php elseif ($user_type === 'alumni'): ?>
                    <li><a href="alumni_search.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-search w-5"></i><span>Find Alumni</span></a></li>
                    <li><a href="connections.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-user-friends w-5"></i><span>My Network</span></a></li>
                    <li><a href="messages.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-envelope w-5"></i><span>Messages</span></a></li>
                    <li class="pt-4"><span class="px-4 text-xs font-semibold text-gray-400 uppercase">Give Back</span></li>
                    <li><a href="mentor_dashboard.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-chalkboard-teacher w-5 text-green-500"></i><span>Mentor Dashboard</span></a></li>
                    <li><a href="jobs.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-briefcase w-5 text-blue-500"></i><span>Post Jobs</span></a></li>
                    <li><a href="events.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-calendar-alt w-5 text-purple-500"></i><span>Events</span></a></li>
                <?php else: ?>
                    <li><a href="alumni_search.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-search w-5"></i><span>Find Alumni</span></a></li>
                    <li><a href="connections.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-user-friends w-5"></i><span>My Network</span></a></li>
                    <li><a href="messages.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-envelope w-5"></i><span>Messages</span></a></li>
                    <li class="pt-4"><span class="px-4 text-xs font-semibold text-gray-400 uppercase">Career Services</span></li>
                    <li><a href="mentorship.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-user-graduate w-5 text-green-500"></i><span>Find a Mentor</span></a></li>
                    <li><a href="jobs.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-briefcase w-5 text-blue-500"></i><span>Job Opportunities</span></a></li>
                    <li><a href="events.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-calendar-alt w-5 text-purple-500"></i><span>Events & Tickets</span></a></li>
                    <li><a href="services.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-th-large w-5"></i><span>All Services</span></a></li>
                <?php endif; ?>
                
                <?php if ($user_type !== 'admin'): ?>
                <li class="pt-4"><span class="px-4 text-xs font-semibold text-gray-400 uppercase">Shopping</span></li>
                <li><a href="cart.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-shopping-cart w-5"></i><span>Cart</span></a></li>
                <li><a href="orders.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-receipt w-5"></i><span>My Orders</span></a></li>
                <?php endif; ?>
                
                <li class="pt-4"><span class="px-4 text-xs font-semibold text-gray-400 uppercase">Account</span></li>
                <?php if ($user_type !== 'admin'): ?>
                <li><a href="../pricing.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-crown w-5 text-amber-500"></i><span>Upgrade Plan</span></a></li>
                <?php endif; ?>
                <li><a href="profile.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-user w-5"></i><span>My Profile</span></a></li>
                <li><a href="settings.php" class="flex items-center space-x-3 px-4 py-3 text-gray-700 hover:bg-gray-100 rounded-lg"><i class="fas fa-cog w-5"></i><span>Settings</span></a></li>
                
                <li class="pt-4 border-t border-gray-200 mt-4">
                    <a href="<?php echo $login_path; ?>logout.php" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg"><i class="fas fa-sign-out-alt w-5"></i><span>Logout</span></a>
                </li>
            </ul>
        </nav>
    </aside>
</div>
