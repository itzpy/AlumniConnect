<!-- Top Navigation Bar -->
<nav class="bg-white border-b border-gray-200 sticky top-0 z-50">
    <div class="px-6 py-4">
        <div class="flex items-center justify-between">
            <!-- Logo and Brand -->
            <div class="flex items-center space-x-4">
                <a href="dashboard.php" class="text-2xl font-bold text-primary">Alumni Connect</a>
            </div>

            <!-- Search Bar -->
            <div class="hidden md:flex flex-1 max-w-2xl mx-8">
                <div class="relative w-full">
                    <input type="text" placeholder="Search alumni, posts, jobs..." 
                           class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:border-primary">
                    <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                </div>
            </div>

            <!-- Right Icons -->
            <div class="flex items-center space-x-6">
                <!-- Shopping Cart -->
                <a href="cart.php" class="relative text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-shopping-cart text-xl"></i>
                    <?php
                    if (!isset($cart_count)) {
                        require_once(dirname(__FILE__).'/../../classes/cart_class.php');
                        $cart_obj = new Cart();
                        $cart_count = $cart_obj->getCartCount($user_id ?? $_SESSION['user_id']);
                    }
                    if ($cart_count > 0):
                    ?>
                    <span class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center"><?php echo $cart_count; ?></span>
                    <?php endif; ?>
                </a>
                
                <button class="relative text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-envelope text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-primary text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">3</span>
                </button>
                
                <button class="relative text-gray-600 hover:text-primary transition-colors">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center">5</span>
                </button>

                <!-- Profile Dropdown -->
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                        <img src="https://ui-avatars.com/api/?name=<?php echo urlencode($user_name); ?>&background=7A1E1E&color=fff" 
                             alt="Profile" class="w-10 h-10 rounded-full">
                        <i class="fas fa-chevron-down text-gray-600 text-sm"></i>
                    </button>

                    <div x-show="open" @click.away="open = false" 
                         class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-lg border border-gray-200 py-2 hidden"
                         x-transition>
                        <a href="profile.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-user w-5"></i>
                            <span class="ml-3">My Profile</span>
                        </a>
                        <a href="settings.php" class="flex items-center px-4 py-3 text-gray-700 hover:bg-gray-50 transition-colors">
                            <i class="fas fa-cog w-5"></i>
                            <span class="ml-3">Settings</span>
                        </a>
                        <div class="border-t border-gray-200 my-2"></div>
                        <a href="../login/logout.php" class="flex items-center px-4 py-3 text-red-600 hover:bg-red-50 transition-colors">
                            <i class="fas fa-sign-out-alt w-5"></i>
                            <span class="ml-3">Logout</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
