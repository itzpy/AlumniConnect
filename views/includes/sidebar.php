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
?>
<aside class="w-64 bg-white border-r border-gray-200 min-h-screen sticky top-16 hidden lg:block">
    <nav class="p-4">
        <ul class="space-y-2">
            <li>
                <a href="dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-home w-5"></i>
                    <span class="font-medium">Dashboard</span>
                </a>
            </li>
            
            <?php if ($user_type === 'student'): ?>
                <!-- Student-specific menu -->
                <li>
                    <a href="alumni_search.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'alumni_search.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-search w-5"></i>
                        <span class="font-medium">Find Alumni</span>
                    </a>
                </li>
                <li>
                    <a href="jobs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'jobs.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-briefcase w-5"></i>
                        <span class="font-medium">Job Opportunities</span>
                    </a>
                </li>
                <li>
                    <a href="mentorship.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'mentorship.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-hands-helping w-5"></i>
                        <span class="font-medium">Find Mentors</span>
                    </a>
                </li>
                
            <?php elseif ($user_type === 'alumni'): ?>
                <!-- Alumni-specific menu -->
                <li>
                    <a href="alumni_search.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'alumni_search.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-search w-5"></i>
                        <span class="font-medium">Alumni Directory</span>
                    </a>
                </li>
                <li>
                    <a href="post_job.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'post_job.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-plus-circle w-5"></i>
                        <span class="font-medium">Post a Job</span>
                    </a>
                </li>
                <li>
                    <a href="mentorship.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'mentorship.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-user-graduate w-5"></i>
                        <span class="font-medium">My Mentees</span>
                    </a>
                </li>
                
            <?php else: ?>
                <!-- Admin-specific menu -->
                <li>
                    <a href="admin_users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_users.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-users-cog w-5"></i>
                        <span class="font-medium">User Management</span>
                    </a>
                </li>
                <li>
                    <a href="admin_analytics.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_analytics.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-chart-bar w-5"></i>
                        <span class="font-medium">Analytics</span>
                    </a>
                </li>
                <li>
                    <a href="admin_reports.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin_reports.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                        <i class="fas fa-flag w-5"></i>
                        <span class="font-medium">Reports</span>
                    </a>
                </li>
            <?php endif; ?>
            
            <!-- Common menu items for all users -->
            <li>
                <a href="messages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'messages.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-envelope w-5"></i>
                    <span class="font-medium">Messages</span>
                </a>
            </li>
            <li>
                <a href="connections.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'connections.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-user-friends w-5"></i>
                    <span class="font-medium"><?php echo $user_type === 'admin' ? 'All Connections' : 'My Network'; ?></span>
                </a>
            </li>
            <li>
                <a href="events.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'events.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-calendar w-5"></i>
                    <span class="font-medium">Events</span>
                </a>
            </li>
            
            <!-- E-Commerce Section -->
            <li class="pt-4">
                <div class="px-4 mb-2">
                    <span class="text-xs font-semibold text-gray-400 uppercase">Services & Payments</span>
                </div>
            </li>
            <li>
                <a href="services.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'services.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-shopping-bag w-5"></i>
                    <span class="font-medium">Browse Services</span>
                </a>
            </li>
            <li>
                <a href="cart.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'cart.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors relative">
                    <i class="fas fa-shopping-cart w-5"></i>
                    <span class="font-medium">Shopping Cart</span>
                    <?php
                    if (isset($cart_count) && $cart_count > 0):
                    ?>
                    <span class="absolute right-4 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">
                        <?php echo $cart_count; ?>
                    </span>
                    <?php endif; ?>
                </a>
            </li>
            <li>
                <a href="orders.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'orders.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-receipt w-5"></i>
                    <span class="font-medium">My Orders</span>
                </a>
            </li>
            <li>
                <a href="profile.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-user w-5"></i>
                    <span class="font-medium">My Profile</span>
                </a>
            </li>
            <li>
                <a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'bg-primary/10 text-primary' : 'text-gray-700 hover:bg-gray-100'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition-colors">
                    <i class="fas fa-cog w-5"></i>
                    <span class="font-medium">Settings</span>
                </a>
            </li>
        </ul>

        <div class="border-t border-gray-200 mt-6 pt-6">
            <a href="../login/logout.php" class="flex items-center space-x-3 px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                <i class="fas fa-sign-out-alt w-5"></i>
                <span class="font-medium">Logout</span>
            </a>
        </div>
    </nav>
</aside>
