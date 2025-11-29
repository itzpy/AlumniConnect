<!-- Alpine.js for interactivity -->
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

<?php
// Determine if we're in admin folder or views folder
$nav_current_dir = basename(dirname($_SERVER['PHP_SELF']));
$nav_is_admin = ($nav_current_dir === 'admin');
$nav_views_path = $nav_is_admin ? '../views/' : '';
$nav_index_path = $nav_is_admin ? '../index.php' : '../index.php';
$nav_login_path = $nav_is_admin ? '../login/' : '../login/';
?>

<!-- Top Navigation Bar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm">
    <div class="px-4 lg:px-6 py-3">
        <div class="flex items-center justify-between">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="<?php echo $nav_index_path; ?>" class="flex items-center space-x-2 text-xl font-bold text-primary hover:opacity-90 transition-opacity">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                    <span class="hidden sm:inline">Alumni Connect</span>
                </a>
            </div>

            <!-- Search Bar - Desktop -->
            <div class="hidden md:flex flex-1 max-w-xl mx-6">
                <form action="<?php echo $nav_views_path; ?>alumni_search.php" method="GET" class="relative w-full">
                    <input type="text" name="q" placeholder="Search alumni, jobs, events..." 
                           class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border border-gray-200 rounded-xl focus:outline-none focus:border-primary focus:bg-white transition-all">
                    <i class="fas fa-search absolute left-3.5 top-3.5 text-gray-400"></i>
                </form>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center space-x-2 sm:space-x-4">
                <!-- Mobile Search Toggle -->
                <button class="md:hidden p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-search text-lg"></i>
                </button>

                <!-- Shopping Cart -->
                <a href="<?php echo $nav_views_path; ?>cart.php" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart text-lg"></i>
                    <?php
                    if (!isset($cart_count)) {
                        require_once(dirname(__FILE__).'/../../classes/cart_class.php');
                        $cart_obj = new Cart();
                        $cart_count = $cart_obj->getCartCount($user_id ?? $_SESSION['user_id']);
                    }
                    if ($cart_count > 0):
                    ?>
                    <span class="absolute -top-0.5 -right-0.5 bg-primary text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                
                <!-- Messages -->
                <a href="<?php echo $nav_views_path; ?>messages.php" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-envelope text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 bg-blue-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">3</span>
                </a>
                
                <!-- Notifications -->
                <a href="<?php echo $nav_views_path; ?>notifications.php" class="relative p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors">
                    <i class="fas fa-bell text-lg"></i>
                    <span class="absolute -top-0.5 -right-0.5 bg-red-500 text-white text-xs font-bold rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1">5</span>
                </a>

                <!-- Profile Dropdown -->
                <div class="relative ml-2" x-data="{ open: false }">
                    <button @click="open = !open" @click.away="open = false" 
                            class="flex items-center space-x-2 p-1.5 rounded-xl hover:bg-gray-100 transition-colors focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=7A1E1E&color=fff" 
                             alt="Profile" class="w-9 h-9 rounded-full ring-2 ring-gray-100">
                        <span class="hidden lg:block font-medium text-gray-700 max-w-[120px] truncate"><?php echo htmlspecialchars($user_name); ?></span>
                        <i class="fas fa-chevron-down text-gray-400 text-xs hidden lg:block" :class="{ 'rotate-180': open }"></i>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 mt-2 w-60 bg-white rounded-xl shadow-lg border border-gray-200 py-2 z-50">
                        
                        <!-- User Info -->
                        <div class="px-4 py-3 border-b border-gray-100">
                            <p class="font-semibold text-gray-900"><?php echo htmlspecialchars($user_name); ?></p>
                            <p class="text-sm text-gray-500 truncate"><?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?></p>
                            <span class="inline-block mt-2 px-2 py-0.5 text-xs font-medium rounded-full bg-primary/10 text-primary capitalize">
                                <?php echo htmlspecialchars($user_type ?? 'user'); ?>
                            </span>
                        </div>

                        <a href="<?php echo $nav_views_path; ?>profile.php" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-5 text-gray-400"></i>
                            <span class="ml-3">My Profile</span>
                        </a>
                        <a href="<?php echo $nav_views_path; ?>orders.php" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-receipt w-5 text-gray-400"></i>
                            <span class="ml-3">My Orders</span>
                        </a>
                        <a href="<?php echo $nav_views_path; ?>settings.php" class="flex items-center px-4 py-2.5 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-5 text-gray-400"></i>
                            <span class="ml-3">Settings</span>
                        </a>
                        
                        <div class="border-t border-gray-100 my-2"></div>
                        
                        <a href="<?php echo $nav_login_path; ?>logout.php" class="flex items-center px-4 py-2.5 text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-3 font-medium">Logout</span>
                        </a>
                    </div>
                </div>

                <!-- Mobile Menu Toggle -->
                <button class="lg:hidden p-2 text-gray-600 hover:text-primary hover:bg-gray-100 rounded-lg transition-colors" 
                        onclick="document.getElementById('mobile-sidebar').classList.toggle('hidden')">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
    </div>
</nav>
